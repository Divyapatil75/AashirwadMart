<?php
require_once __DIR__.'/includes/functions.php';
$message='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf(); $name=trim($_POST['name']??''); $email=trim($_POST['email']??''); $body=trim($_POST['message']??''); $u=current_user();
 if(mb_strlen($name)<2 || !filter_var($email,FILTER_VALIDATE_EMAIL) || mb_strlen($body)<5){$message='Please enter your name, a valid email, and a message.';}
 else { $uid=$u?(int)$u['id']:null; $s=$conn->prepare('INSERT INTO contact_messages(user_id,name,email,message) VALUES(?,?,?,?)'); $s->bind_param('isss',$uid,$name,$email,$body); $s->execute(); flash('Your message has been sent. Thank you for contacting Aashirwad Mart.'); redirect('contact.php'); }
}
render_header('Contact us'); ?>
<div class="page-heading"><span class="eyebrow">WE ARE HERE TO HELP</span><h1>Contact Aashirwad Mart</h1><p class="muted">Send us a question about products, orders, delivery or payments.</p></div>
<div class="checkout-layout"><form method="post" class="form-card"><?=csrf_field()?><?php if($message):?><div class="alert error"><?=e($message)?></div><?php endif;?><label>Your name<input name="name" required value="<?=e(current_user()['name']??'')?>"></label><label>Email address<input type="email" name="email" required value="<?=e(current_user()['email']??'')?>"></label><label>Message<textarea name="message" rows="5" required></textarea></label><button class="button">Send message</button></form><aside class="summary-card"><h2>Store details</h2><p><b>Address</b><br><?=e(BUSINESS_ADDRESS)?></p><p><b>Phone</b><br><?=e(BUSINESS_PHONE)?></p><p><b>Email</b><br><?=e(BUSINESS_EMAIL)?></p><p><b>Delivery</b><br>Approved PIN code: 431005</p></aside></div>
<?php render_footer(); ?>
