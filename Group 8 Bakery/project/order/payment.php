<?php
include '../base.php';
auth("Member");

// Get order Id from Session
$id = getOrderId();

// Check and verify the order is exist or not
$stm = $_db->prepare('SELECT * FROM `order` WHERE id = ?');
$stm->execute([$id]);
$p = $stm->fetch();

$userInfo = $_db->prepare('SELECT * FROM user_payment WHERE user_id = ?');
$userInfo->execute([$_user->id]);
$u = $userInfo->fetch();

if(!$u) {
    temp('info', 'Please Insert Your Payment Method');
    redirect("addresswitpay.php");
}


if (!$p) {
    redirect('/');
    temp('info', 'Order Not Exist !!!');
}

$stm = $_db->prepare("UPDATE payment SET status = 'PAID' WHERE order_id = ?");
$stm->execute([$id]);

temp('info', 'Make Payment Successful');

$_title = 'Payment';
include '../head.php';
?>


<?php
include '../foot.php';
