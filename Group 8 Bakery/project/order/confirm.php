<?php
include '../base.php';

auth("Member");

// Get Order Id from Session
$id = getOrderId();
$tokenID = req('tokenID');

// Delete previous Payment Token
$_db->query('DELETE FROM payment_token WHERE expire < NOW()');

if (!is_exist($tokenID,'payment_token','id')) {
    temp('info', 'Invalid token. Try again');
    redirect('/');
}

// Direct Delete the token to prevent customer reclick link at Email
$deleteToken = $_db->prepare('DELETE FROM payment_token WHERE id = ?');
$deleteToken->execute([$tokenID]);


// Update the payment status when transaction is complete
$stm = $_db->prepare("UPDATE payment SET status = 'PAID' WHERE order_id = ?");
$stm->execute([$id]);

temp('info', 'Place Order Successful');

$_title = "Confirmation";
include '../head.php';
?>

<!-- Clear order id at Session -->
<?= deleteOrderID();?>
<p>Your Payment Is Make Successful</p>
<p>Click Here For More Information<button data-get="orderdetail.php?id=<?= $id ?>">Detail</button></p>

<?php
include '../foot.php';
