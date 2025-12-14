<?php
include '../base.php';
//-----------------------------------------------------------------------------
auth("Admin");

// Searching Function
$search = req('search');
$search_sql = $search ? "WHERE o.id LIKE ? OR o.user_id LIKE ? OR p.status LIKE ? OR u.name LIKE ?" : "";
$params = $search ? ["%$search%", "%$search%", "%$search%", "%$search%"] : [];

// (1) Sorting
$fields = [
    'o.id' => 'ID',
    'o.datetime' => 'Date Time',
    'o.count' => "Quantity",
    'o.total' => 'Total Amount(RM)',
    'u.name' => 'Name',
    'p.status' => 'Order Status'
];



$sort = req('sort');
key_exists($sort, $fields) || $sort = 'o.id';

$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// (2) Paging
$page = req('page', 1);

// For sorting purpose
$sql = "SELECT o.*,p.status,u.name FROM `order` o JOIN payment p ON o.id = p.order_id JOIN user AS u ON o.user_id = u.id $search_sql ORDER BY $sort $dir";
require_once '../lib/SimplePager.php';
$p = new SimplePager($sql, $params, 10, $page);
$arr = $p->result;

// ----------------------------------------------------------------------------
$_title = 'Order List';
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
                <th>Name</th>
                <th>Order Status</th>
                <th>More</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arr as $s): ?>
                <tr>
                    <td><?= $s->id ?></td>
                    <td><?= $s->datetime ?></td>
                    <td><?= $s->count ?></td>
                    <td><?= $s->total ?></td>
                    <td><?= $s->name ?></td>
                    <td><?= $s->status ?></td>
                    <td> <button class="btn btn-info" data-get="orderdetail.php?id=<?= $s->id ?>">Detail</button></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function() {
        $('#myTable').DataTable();
    });
</script>

<?php
include '../foot.php';
