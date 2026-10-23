<?php
require_once __DIR__ . '/../db.php';
function e(?string $v): string { return htmlspecialchars($v ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $path=''): string { return rtrim(BASE_URL, '/') . ($path !== '' ? '/' . ltrim($path, '/') : '/'); }
function redirect(string $path): never { header('Location: ' . url($path)); exit; }
function csrf_token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.e(csrf_token()).'">'; }
function verify_csrf(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419); exit('Session security token expired. Please go back and try again.');
    }
}
function flash(string $message, string $type='success'): void { $_SESSION['flash'] = ['message'=>$message,'type'=>$type]; }
function show_flash(): void {
    if (!empty($_SESSION['flash'])) { $f=$_SESSION['flash']; echo '<div class="alert '.e($f['type']).'">'.e($f['message']).'</div>'; unset($_SESSION['flash']); }
}
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_customer(): void { if (empty($_SESSION['user'])) { flash('Please log in to continue.', 'error'); redirect('login.php'); } }
function require_admin(): void { if (empty($_SESSION['admin'])) { flash('Please log in as admin.', 'error'); redirect('admin/login.php'); } }
function money(float|int|string $amount): string { return CURRENCY . number_format((float)$amount, 2); }
function image_path(?string $image): string {
    $name = basename((string)$image);
    if ($name === '' || !is_file(__DIR__.'/../assets/images/'.$name)) return url('assets/images/product-placeholder.svg');
    return url('assets/images/'.$name);
}
function get_cart_items(mysqli $conn, int $userId): array {
    $q=$conn->prepare('SELECT c.product_id,c.quantity,p.name,p.price,p.stock,p.image,p.is_active FROM cart_items c JOIN products p ON p.id=c.product_id WHERE c.user_id=? AND p.is_active=1');
    $q->bind_param('i',$userId); $q->execute(); return $q->get_result()->fetch_all(MYSQLI_ASSOC);
}
function cart_subtotal(array $items): float { $sum=0; foreach($items as $i) $sum+=(float)$i['price']*(int)$i['quantity']; return $sum; }
function delivery_charge(float $subtotal): float { return $subtotal >= 499 ? 0 : 30; }
function valid_delivery_pin(mysqli $conn, string $pin): bool {
    if (!preg_match('/^[0-9]{6}$/',$pin)) return false;
    $q=$conn->prepare('SELECT id FROM delivery_pincodes WHERE pincode=? AND is_active=1 LIMIT 1');
    $q->bind_param('s',$pin); $q->execute(); return (bool)$q->get_result()->fetch_assoc();
}
function render_header(string $title='', bool $adminArea=false): void {
    $u=current_user(); $count=0;
    if ($u) { global $conn; $q=$conn->prepare('SELECT COALESCE(SUM(quantity),0) n FROM cart_items WHERE user_id=?'); $q->bind_param('i',$u['id']); $q->execute(); $count=(int)$q->get_result()->fetch_assoc()['n']; }
    ?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=e($title ? $title.' | '.SITE_NAME : SITE_NAME)?></title><link rel="stylesheet" href="<?=e(url('assets/css/style.css'))?>"></head><body>
    <div class="topline">Home delivery available in approved PIN codes · Call <?=e(BUSINESS_PHONE)?></div>
    <header class="site-header"><a class="brand" href="<?=e(url())?>"><span class="brand-mark">A</span><span><b><?=e(SITE_NAME)?></b><small><?=e(SITE_TAGLINE)?></small></span></a>
    <form class="search" action="<?=e(url('index.php'))?>" method="get"><input name="q" placeholder="Search products..." value="<?=e($_GET['q']??'')?>"><button aria-label="Search">⌕</button></form>
    <nav><?php if($adminArea): ?><a href="<?=e(url())?>">View storefront</a><?php else: ?><a href="<?=e(url())?>">Home</a><a href="<?=e(url('index.php#categories'))?>">Categories</a><a href="<?=e(url('contact.php'))?>">Contact</a><a href="<?=e(url('wishlist.php'))?>">Wishlist</a><a href="<?=e(url('cart.php'))?>">Cart (<?=$count?>)</a>
    <?php if($u): ?><a class="nav-user-pill" href="<?=e(url('customer/dashboard.php'))?>" aria-label="Open customer dashboard"><?=e($u['name'])?></a><?php else: ?><a href="<?=e(url('login-options.php'))?>">Login</a><a class="nav-cta" href="<?=e(url('register.php'))?>">Create account</a><?php endif; ?><?php endif; ?>
    </nav></header><main class="container"><?php show_flash(); ?>
    <?php
}
function render_footer(): void { ?>
    </main><footer class="footer"><div><b><?=e(SITE_NAME)?></b><p>Owner contact information</p><p>Email: <?=e(BUSINESS_EMAIL)?></p><p>Phone: <?=e(BUSINESS_PHONE)?></p><p>Location: <?=e(BUSINESS_ADDRESS)?></p></div></footer>
    <button id="chat-toggle" aria-label="Open help chat">Chat with us</button><section id="chat-box" class="chat-box hidden"><div class="chat-head">Aashirwad Mart Help <button id="chat-close">×</button></div><div id="chat-log" class="chat-log"><p class="bot-msg">Hi! Ask me about delivery, orders, payments, or categories.</p></div><form id="chat-form"><input id="chat-input" placeholder="Type your question..." required><button>Send</button></form></section>
    <script src="<?=e(url('assets/js/app.js'))?>" defer></script></body></html><?php }
