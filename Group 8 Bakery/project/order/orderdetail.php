<?php
include '../base.php';
auth();
$id = req('id');
setOrderID($id);

if (is_post()) {
    // Used for admin to update the order status
    $status = req('orderStatus');
    $stm = $_db->prepare("UPDATE payment SET status = ? WHERE order_id = ?");
    $stm->execute([$status, $id]);

    temp('info', 'Update Status Successful');
}

$stm = $_db->prepare('
SELECT * FROM `order`
WHERE id = ?
');

$stm->execute([$id]);
$o = $stm->fetch();
if (!$o) redirect('history.php');

// Display all the information about the order
$stm = $_db->prepare('
SELECT i.*,p.name,p.photo,y.datetime,y.status
FROM item AS i LEFT JOIN payment AS y
ON i.order_id = y.order_id
JOIN product AS p
ON i.product_id = p.id
WHERE i.order_id = ?
');
$stm->execute([$id]);
$arr = $stm->fetchAll();

// For display current order status
$stm = $_db->prepare('SELECT status FROM payment WHERE order_id = ?');
$stm->execute([$id]);
$status = $stm->fetch();

// For display address of user
$stm = $_db->prepare('SELECT u.address FROM `order` AS o JOIN user AS u ON o.user_id = u.id WHERE o.id = ?');
$stm->execute([$id]);
$ad = $stm->fetch();


$_title = "Order Detail";
include '../head.php';
?>
<form class="OrderDetail">
    <label>Order Id:</label>
    <b><?= $o->id ?></b>
    <br>

    <label>Date Time:</label>
    <?= $o->datetime ?>
    <br>

    <label>Count:</label>
    <?= $o->count ?>
    <br>

    <label>Total:</label>
    RM <?= $o->total ?>
    <br>
</form>
<div class="detail-table-container">

    <table class="detail">
        <!-- Check User Role if it is admin can update order status -->
        <?php foreach ($status as $s): ?>
            <?php if ($_user?->role == "Admin" && $s != "CANCEL" && $s != "DELIVERED"): ?>
                <th>Status</th>
                <th>
                    <form method="post">
                        <?= html_select('orderStatus', $_orderStatus, $s) ?>
                    </form>
                <?php endif ?>
                </th>
                <?php if ($_user?->role == "Admin" && ($s == "CANCEL" || $s == "DELIVERED")): ?>
                    <th>Status</th>
                    <td><?= $s ?></td>
                <?php endif ?>
            <?php endforeach ?>
    </table>
    <table class="detail">
        <tr>
            <?php if ($_user?->role == "Member"): ?>
                <th>Status</th>
                <?php foreach ($status as $s): ?>
                    <td><?= $s ?></td>
                <?php endforeach ?>
            <?php endif ?>
        </tr>

        <tr>
            <th colspan="2">Address (Street Number | Postcode | City | State)</th>
            <td colspan="3"><?= $ad->address ?></td>
        </tr>

        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Price (RM)</th>
            <th>Unit</th>
            <th>Subtotal (RM)</th>
        </tr>

        <?php foreach ($arr as $h): ?>
            <tr>
                <td><?= $h->product_id ?></td>
                <td><?= $h->name ?></td>
                <td><?= $h->price ?></td>
                <td><?= $h->unit ?></td>
                <td><?= $h->subtotal ?></td>
            </tr>
        <?php endforeach ?>
    </table>
    <?php if ($_user?->role == "Member" && $h->status != "PENDING"): ?>
        <button class="reorder" data-get="reorder.php">Reorder</button>
    <?php endif ?>
    <?php if ($h->status != "CANCEL" && $h->status != "READY TO SHIP" && $h->status != "DELIVERED"): ?>
        <button class="cancel" data-get="cancelOrder.php">Cancel Order</button>
    <?php endif ?>
    <?php if ($_user?->role == "Member" && $h->status == "PENDING"): ?>
        <button class="pending" data-get="paymentCard.php">Make Payment</button>
    <?php endif ?>

</div>
<script>
    $('select').on('change', e => e.target.form.submit());
</script>
<?php
include '../foot.php';
