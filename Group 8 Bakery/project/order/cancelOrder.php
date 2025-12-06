<?php
include '../base.php';
auth();

// Read The Order Id Pass From Order Detail Page
$id = getOrderId();

// Update the payment status
$stm = $_db->prepare("UPDATE payment SET status ='CANCEL' WHERE order_id = ?");
$stm->execute([$id]);

// To get the unit purchase by customer to add back the stock quantity
$unit = $_db->prepare('SELECT * FROM item WHERE order_id = ?');
$unit->execute([$id]);
$i = $unit->fetchAll();

// Update the stock quantity
$inv = $_db->prepare('UPDATE product SET stock = stock + ? WHERE id = ?');
foreach ($i as $item) {
    $inv->execute([$item->unit, $item->product_id]);
}

// Clear the Session which hold the order id
deleteOrderID();
temp('info', 'Cancel Successful');
redirect('/');

include '../head.php';
$_title = "Cancel Order";
?>

<?php
include '../foot.php';
