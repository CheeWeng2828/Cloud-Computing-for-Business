<?php
include '../base.php';

auth("Member");

$sql = $_db->prepare("SELECT o.*, p.status
        FROM `order` o 
        JOIN payment p ON o.id = p.order_id
        WHERE o.user_id = ?");
$sql->execute([$_user->id]);
$arr = $sql->fetchAll();


$_title = 'Order History';
include '../head.php';
?>

<div class="container mt-3">
    <table id="myTable" class="display">
        <thead class="bg-danger text-white">
            <tr>
                <th>ID</th>
                <th>Date Time</th>
                <th>Quantity</th>
                <th>Total Amount(RM)</th>
                <th>Order Status</th>
                <th>More</th>
            </tr>
        </thead>
        <?php foreach ($arr as $h): ?>
            <tr>
                <td><?= $h->id ?></td>
                <td><?= $h->datetime ?></td>
                <td><?= $h->count ?></td>
                <td><?= $h->total ?></td>
                <td><?= $h->status ?></td>
                <td> <button class="btn btn-primary" data-get="orderdetail.php?id=<?= $h->id ?>">Detail</button></td>
            </tr>
        <?php endforeach ?>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>
<?php
include '../foot.php';
