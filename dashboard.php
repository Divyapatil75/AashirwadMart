<?php
require_once __DIR__ . '/../includes/functions.php';
require_customer();

$userId = (int) current_user()['id'];
$userQuery = $conn->prepare('SELECT name, email, phone, created_at FROM users WHERE id = ? LIMIT 1');
$userQuery->bind_param('i', $userId);
$userQuery->execute();
$userDetails = $userQuery->get_result()->fetch_assoc();
if (!$userDetails) {
    unset($_SESSION['user']);
    flash('Please log in again to open your account.', 'error');
    redirect('login.php');
}

$countQuery = $conn->prepare('SELECT COUNT(*) AS total FROM orders WHERE user_id = ?');
$countQuery->bind_param('i', $userId);
$countQuery->execute();
$orderCount = (int) $countQuery->get_result()->fetch_assoc()['total'];

$countQuery = $conn->prepare('SELECT COUNT(*) AS total FROM wishlist_items WHERE user_id = ?');
$countQuery->bind_param('i', $userId);
$countQuery->execute();
$wishlistCount = (int) $countQuery->get_result()->fetch_assoc()['total'];

$countQuery = $conn->prepare('SELECT COALESCE(SUM(quantity), 0) AS total FROM cart_items WHERE user_id = ?');
$countQuery->bind_param('i', $userId);
$countQuery->execute();
$cartCount = (int) $countQuery->get_result()->fetch_assoc()['total'];

$recentQuery = $conn->prepare('SELECT order_number, total, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 3');
$recentQuery->bind_param('i', $userId);
$recentQuery->execute();
$recentOrders = $recentQuery->get_result()->fetch_all(MYSQLI_ASSOC);

render_header('Customer dashboard');
?>
<div class="page-heading">
    <span class="eyebrow">YOUR AASHIRWAD MART ACCOUNT</span>
    <h1>Hello, <?= e($userDetails['name']) ?>!</h1>
    <p class="muted">Manage your profile, shopping cart, wishlist and orders from one place.</p>
</div>

<div class="stats-grid customer-stats">
    <a class="stat-card" href="<?= e(url('my_orders.php')) ?>"><span class="stat-icon">📦</span><span class="stat-label">My orders</span><strong><?= $orderCount ?></strong><small>View order history →</small></a>
    <a class="stat-card" href="<?= e(url('cart.php')) ?>"><span class="stat-icon">🛒</span><span class="stat-label">Cart items</span><strong><?= $cartCount ?></strong><small>Continue shopping →</small></a>
    <a class="stat-card" href="<?= e(url('wishlist.php')) ?>"><span class="stat-icon">♡</span><span class="stat-label">Wishlist</span><strong><?= $wishlistCount ?></strong><small>View saved products →</small></a>
</div>

<section class="section customer-account-grid">
    <div class="admin-panel">
        <span class="eyebrow">ACCOUNT INFORMATION</span>
        <h2>My profile</h2>
        <div class="profile-details">
            <p><span>Full name</span><strong><?= e($userDetails['name']) ?></strong></p>
            <p><span>Email address</span><strong><?= e($userDetails['email']) ?></strong></p>
            <p><span>Phone number</span><strong><?= e($userDetails['phone']) ?></strong></p>
            <p><span>Member since</span><strong><?= e(date('d M Y', strtotime($userDetails['created_at']))) ?></strong></p>
        </div>
    </div>
    <div class="admin-panel customer-shortcuts">
        <span class="eyebrow">QUICK LINKS</span>
        <h2>What would you like to do?</h2>
        <a href="<?= e(url()) ?>">🛍️ &nbsp; Browse products</a>
        <a href="<?= e(url('my_orders.php')) ?>">📦 &nbsp; Track my orders</a>
        <a href="<?= e(url('wishlist.php')) ?>">♡ &nbsp; Open my wishlist</a>
        <a href="<?= e(url('cart.php')) ?>">🛒 &nbsp; View my cart</a>
        <a class="customer-logout" href="<?= e(url('logout.php')) ?>">Log out of my account</a>
    </div>
</section>

<section class="section">
    <div class="section-heading"><div><span class="eyebrow">RECENT ACTIVITY</span><h2>Recent orders</h2></div><a href="<?= e(url('my_orders.php')) ?>">View all orders →</a></div>
    <?php if (!$recentOrders): ?>
        <div class="empty-state"><div class="empty-icon">📦</div><h2>Your first order starts here</h2><p>You haven't placed an order yet. Browse the store to find your everyday essentials.</p><a class="button" href="<?= e(url()) ?>">Start shopping</a></div>
    <?php else: ?>
        <div class="orders-list">
        <?php foreach ($recentOrders as $order): ?>
            <article class="order-card"><div class="order-head"><div><small>ORDER NUMBER</small><b><?= e($order['order_number']) ?></b><small><?= e(date('d M Y, h:i A', strtotime($order['created_at']))) ?></small></div><span class="status-pill"><?= e(str_replace('_', ' ', $order['status'])) ?></span></div><div class="order-meta"><strong><?= money($order['total']) ?></strong><a href="<?= e(url('my_orders.php')) ?>">View details →</a></div></article>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
<?php render_footer(); ?>
