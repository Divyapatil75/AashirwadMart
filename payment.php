<?php
require_once __DIR__.'/includes/functions.php';
require_customer();
$uid=(int)current_user()['id'];
$id=(int)($_GET['order']??0);
$s=$conn->prepare('SELECT * FROM orders WHERE id=? AND user_id=? AND payment_method="upi"');
$s->bind_param('ii',$id,$uid); $s->execute();
$order=$s->get_result()->fetch_assoc();
if(!$order){flash('Order not found.','error');redirect('my_orders.php');}
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $ref=trim($_POST['reference_number']??'');
    if(strlen($ref)<4||strlen($ref)>120){
        $error='Enter the UPI transaction reference number.';
    } elseif($order['payment_status']==='verified'){
        $error='Payment has already been verified.';
    } else {
        $file=null;
        if(!empty($_FILES['proof']['name']) && $_FILES['proof']['error']!==UPLOAD_ERR_NO_FILE){
            if($_FILES['proof']['error']!==UPLOAD_ERR_OK||$_FILES['proof']['size']>3*1024*1024){
                $error='Proof image must be under 3 MB.';
            } else {
                $mime=(new finfo(FILEINFO_MIME_TYPE))->file($_FILES['proof']['tmp_name']);
                $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;
                if(!$ext){
                    $error='Upload a JPG, PNG, or WEBP image.';
                } else {
                    $dir=__DIR__.'/uploads/payment-proofs';
                    if(!is_dir($dir)) mkdir($dir,0750,true);
                    $file=bin2hex(random_bytes(16)).'.'.$ext;
                    if(!move_uploaded_file($_FILES['proof']['tmp_name'],$dir.'/'.$file)){
                        $file=null; $error='Could not save the uploaded proof.';
                    }
                }
            }
        }
        if(!$error){
            $conn->begin_transaction();
            try {
                $s=$conn->prepare('INSERT INTO payment_submissions(order_id,user_id,reference_number,proof_file,status) VALUES(?,?,?,?,"pending")');
                $s->bind_param('iiss',$id,$uid,$ref,$file); $s->execute();
                $s=$conn->prepare('UPDATE orders SET payment_reference=?,payment_proof=?,payment_status="pending",status="awaiting_payment_review" WHERE id=? AND user_id=?');
                $s->bind_param('ssii',$ref,$file,$id,$uid); $s->execute();
                $conn->commit();
                flash('Payment details submitted. Your order is not confirmed until the admin verifies the transaction.');
                redirect('my_orders.php');
            } catch(Throwable $e) {
                $conn->rollback(); $error='Could not submit payment details.';
            }
        }
    }
}
render_header('UPI payment');
?>
<div class="payment-layout"><section class="form-card"><span class="eyebrow">ORDER <?=e($order['order_number'])?></span><h1>Pay with UPI</h1><p>Scan the shop's genuine QR code using your UPI app. Pay exactly <b><?=money($order['total'])?></b>. Then enter the transaction reference below.</p><div class="qr-frame"><?php if(is_file(__DIR__.'/assets/images/'.UPI_QR_IMAGE)):?><img src="<?=e(image_path(UPI_QR_IMAGE))?>" alt="Aashirwad Mart UPI QR code"><?php else:?><div class="qr-placeholder">QR image not added yet</div><p class="muted tiny">Copy your genuine QR image to <code>assets/images/<?=e(UPI_QR_IMAGE)?></code>.</p><?php endif;?></div><div class="alert warning">Submitting a reference does not confirm payment. Admin verification is required.</div><?php if($error):?><div class="alert error"><?=e($error)?></div><?php endif;?><form method="post" enctype="multipart/form-data"><?=csrf_field()?><label>UPI transaction reference / UTR<input name="reference_number" required maxlength="120" placeholder="Enter reference from your UPI app"></label><label>Payment screenshot (optional, JPG/PNG/WEBP, max 3 MB)<input type="file" name="proof" accept="image/jpeg,image/png,image/webp"></label><button class="button full">Submit payment for review</button></form></section><aside class="summary-card"><h2>Payment summary</h2><div><span>Order total</span><b><?=money($order['total'])?></b></div><div><span>Payment status</span><b>Pending verification</b></div><a class="button secondary full" href="<?=e(url('my_orders.php'))?>">View my orders</a></aside></div><?php render_footer();?>
