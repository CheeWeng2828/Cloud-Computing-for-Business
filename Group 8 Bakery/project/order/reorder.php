<?php
include '../base.php';
auth("Member");
$id = getOrderId();

/// Fetch the item unit purchase by customer previously
$item = $_db->prepare('SELECT * FROM item WHERE order_id = ?');
$item->execute([$id]);
$p = $item->fetchAll();

/// Here Start To Check The product Stock
foreach ($p as $items) {
    $product = $_db->prepare('SELECT stock FROM product WHERE id = ?');
    $product->execute([$items->product_id]);
    $stockqty = $product->fetchColumn();
    $orderqty = $items->unit;

    if ($stockqty < $orderqty) {
        temp('info', 'Low Stock,Please Modify the Order');
        redirect('history.php');
    }
}

/// Starting copy the data to the table
$_db->beginTransaction();

$stm = $_db->prepare('SELECT * FROM `order`WHERE id = ?');
$stm->execute([$id]);
$arr = $stm->fetch();

$ins = $_db->prepare('INSERT INTO `order`(datetime,count,total,user_id)VALUES(NOW(),?,?,?)');
$ins->execute([$arr->count, $arr->total, $arr->user_id]);
$orderId = $_db->lastInsertId();

// Order id session
setOrderID($orderId);

$stm1 = $_db->prepare('SELECT * FROM item WHERE order_id = ?');
$stm1->execute([$id]);
$items = $stm1->fetchAll();

$itm = $_db->prepare('INSERT INTO item(order_id,product_id,price,unit,subtotal)VALUES(?,?,?,?,?)');
foreach ($items as $item) {
    $itm->execute([$orderId, $item->product_id, $item->price, $item->unit, $item->subtotal]);
}

$stm = $_db->prepare("INSERT INTO payment (datetime,status,order_id) VALUES (NOW(),'PENDING',?)");
$stm->execute([$orderId]);

$stm = $_db->prepare('UPDATE `product` SET `stock` = `stock` - ? WHERE `id` = ?');
foreach ($items as $item) {
    $stm->execute([$item->unit, $item->product_id]);
}

$_db->commit();

temp('info', 'Make Order Successful');
redirect("paymentCard.php");

$_title = "Reorder";
include '../head.php';
?>

<?php
include '../foot.php';
