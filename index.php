<?php
require_once __DIR__.'/includes/functions.php';
$q=trim($_GET['q']??''); $cat=(int)($_GET['category']??0);
$categories=$conn->query("SELECT * FROM categories WHERE is_active=1 ORDER BY id")->fetch_all(MYSQLI_ASSOC);
$sql="SELECT p.*,c.name category_name FROM products p JOIN categories c ON c.id=p.category_id WHERE p.is_active=1";
$params=[];$types='';
if($q!==''){ $sql.=" AND (p.name LIKE ? OR p.description LIKE ?)";$like='%'.$q.'%';$params=[$like,$like];$types='ss';}
if($cat>0){$sql.=" AND p.category_id=?";$params[]=$cat;$types.='i';}
$sql.=" ORDER BY p.created_at DESC,p.id DESC";
$stmt=$conn->prepare($sql);if($params)$stmt->bind_param($types,...$params);$stmt->execute();$products=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
render_header('Home');
?>
<section class="hero-slider" aria-label="Store highlights">
 <div class="slide active"><div class="hero-copy"><span class="eyebrow">YOUR NEIGHBOURHOOD MART</span><h1>Everyday essentials,<br><em>delivered with care.</em></h1><p>Shop groceries, stationery, electronics, makeup and phone accessories in one place.</p><a class="button" href="#products">Explore products</a></div><div class="hero-art"><span>🛍️</span><small>One cart. All your essentials.</small></div></div>
 <div class="slide"><div class="hero-copy"><span class="eyebrow">SMART SHOPPING</span><h1>Little things.<br><em>Big convenience.</em></h1><p>Find useful picks for your home, desk and everyday routine.</p><a class="button" href="#categories">Browse categories</a></div><div class="hero-art art-two"><span>📦</span><small>Simple, local shopping.</small></div></div>
 <div class="slide"><div class="hero-copy"><span class="eyebrow">LOCAL DELIVERY</span><h1>Your essentials,<br><em>just a few clicks away.</em></h1><p>Delivery is currently available for approved PIN code 431005.</p><a class="button" href="#products">Shop now</a></div><div class="hero-art art-three"><span>🚚</span><small>Check your PIN at checkout.</small></div></div>
 <div class="slider-dots"><button class="dot active" aria-label="Slide 1"></button><button class="dot" aria-label="Slide 2"></button><button class="dot" aria-label="Slide 3"></button></div>
</section>
<section id="categories" class="section"><div class="section-heading"><div><span class="eyebrow">SHOP BY CATEGORY</span><h2>Find your everyday favourites</h2></div></div><div class="category-grid">
<?php foreach($categories as $c): ?><a class="category-card" href="<?=e(url('index.php?category='.$c['id'].'#products'))?>"><span class="category-emoji"><?=['Groceries'=>'🥑','Stationery'=>'✏️','Electronics'=>'🔌','Makeup'=>'💄','Phone Accessories'=>'📱'][$c['name']]??'🛍️'?></span><b><?=e($c['name'])?></b><small>Explore collection →</small></a><?php endforeach; ?>
</div></section>
<section id="products" class="section"><div class="section-heading"><div><span class="eyebrow">CURATED FOR YOU</span><h2><?=e($q!==''?'Search results':($cat?'Category products':'Popular products'))?></h2></div><span class="muted"><?=count($products)?> products</span></div>
<?php if(!$products): ?><div class="empty-state"><h3>No products found</h3><p>Try another search or browse all products.</p><a class="button secondary" href="<?=e(url())?>">View all products</a></div><?php else: ?><div class="product-grid"><?php foreach($products as $p): ?><article class="product-card"><a class="product-image" href="<?=e(url('product.php?id='.$p['id']))?>"><img src="<?=e(image_path($p['image']))?>" alt="<?=e($p['name'])?>"><span><?=e($p['category_name'])?></span></a><div class="product-info"><a class="product-name" href="<?=e(url('product.php?id='.$p['id']))?>"><?=e($p['name'])?></a><p><?=e(mb_strimwidth($p['description']??'',0,76,'…'))?></p><div class="product-bottom"><b><?=money($p['price'])?></b><span><?=((int)$p['stock']>0)?'In stock':'Out of stock'?></span></div><div class="product-actions"><form method="post" action="<?=e(url('cart.php'))?>"><?=csrf_field()?><input type="hidden" name="action" value="add"><input type="hidden" name="product_id" value="<?=$p['id']?>"><button class="button small" <?=((int)$p['stock']<1?'disabled':'')?>>Add to cart</button></form><form method="post" action="<?=e(url('wishlist.php'))?>"><?=csrf_field()?><input type="hidden" name="product_id" value="<?=$p['id']?>"><button class="icon-button" title="Add to wishlist">♡</button></form></div></div></article><?php endforeach; ?></div><?php endif; ?></section>
<section class="delivery-strip"><span>📍</span><div><b>Local delivery, made simple</b><p>We currently deliver to approved PIN code 431005. Check availability before ordering.</p></div><a href="#products">Start shopping →</a></section>
<?php render_footer(); ?>
