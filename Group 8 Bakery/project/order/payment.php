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

// Check the Payment Status of the order
$payment_status = $_db->prepare('SELECT * FROM payment WHERE order_id = ?');
$payment_status->execute([$id]);
$status = $payment_status->fetchAll();

// Direct the page to index when the order status is paid
foreach ($status as $ps) {
    if ($ps->status == "PAID") {
        temp('info', 'Order Has Paid !!!');
        redirect('/');
    }
}

// For Insert payment token purpose
$stm2 = $_db->prepare('SELECT * FROM payment WHERE order_id = ?');
$stm2->execute([$id]);
$payment = $stm2->fetchAll();

// Generate Random Payment Token Id
$tokenID = sha1(uniqid() . rand());
foreach ($payment as $pay) {
    $stm = $_db->prepare('
            DELETE FROM payment_token WHERE payment_id=?;

            INSERT INTO payment_token(id,expire,payment_id)
            VALUES (?,ADDTIME(NOW(),"00:05"),?);
        ');

    $stm->execute([$pay->id, $tokenID, $pay->id]);
}

// To display order detail make by customer at Email
$stm2 = $_db->prepare('SELECT p.name,i.unit,i.price,i.subtotal 
                       FROM product AS p JOIN item AS i 
                       ON p.id = i.product_id 
                       WHERE i.order_id = ?
                       ');

$stm2->execute([$id]);
$n = $stm2->fetchAll();


$itemsStr = "";
foreach ($n as $d) {
    // Combine all the order detail within this array
    $itemsStr .= $d->name . "<br>" . "Quantity: " . $d->unit . "<br>Price:  " . $d->price . "<br>";
}

$m = get_mail();

$url = base("order/confirm.php?tokenID=$tokenID");
$m->addAddress($_user->email, $_user->name);
$m->addEmbeddedImage("../photo/$_user->photo", 'photo');
$m->isHTML(true);
$m->Subject = 'Payment Confirmation';
$m->Body = "
            <img src='cid:photo'
            style='width: 50px; height: 50px;
            border: 1px solid #333'>
            <p>Dear $_user->name,<p>
            <h1>Order Detail</h1>
            <p>
            Order ID:   $p->id <br>
            Item:
            </p>
            <p>
            $itemsStr
            </p>
            <p>
            Total Price:    RM$p->total
            <p>
                Please click <a href='$url'>here</a>
                for more detail.
            </p>
            <p>From, 🍞 Admin</p>
        ";
$m->send();

$_title = 'Payment';
include '../head.php';
?>

<p>Please Check Your Mail Box To Confirm The Payment</p>

<?php
include '../foot.php';
