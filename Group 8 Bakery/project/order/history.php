<?php
include '../base.php';

auth("Member");
/// Sorting Field
$fields = [
    'o.id' => 'ID',
    'o.datetime' => 'Date Time',
    'o.count' => "Quantity",
    'o.total' => 'Total Amount(RM)',
    'p.status' => 'Order Status'
];

// Sorting Field
$sort = req('sort');
key_exists($sort, $fields) || $sort = 'o.id';


// Sorting Direction
$dir = req('dir');
in_array($dir, ['asc', 'desc']) || $dir = 'asc';

// (2) Paging
$page = req('page', 1);

$search      = trim(req('search'));
$params      = [$_user->id];
$search_sql = "";

if ($search !== '') {
    // wrap the ORs in parens to keep your logic clear
    $search_sql = " AND (o.id LIKE ? OR p.status LIKE ?) ";
    $params[]   = "%{$search}%";
    $params[]   = "%{$search}%";
}

$count_sql = "SELECT COUNT(*) FROM `order` o JOIN payment p ON o.id = p.order_id WHERE o.user_id = ? {$search_sql}";
$stm = $_db->prepare($count_sql);
$stm->execute($params);
$total = (int)$stm->fetchColumn();


$sql = "SELECT o.*, p.status
        FROM `order` o 
        JOIN payment p ON o.id = p.order_id
        WHERE o.user_id = ? 
        {$search_sql}
        ORDER BY {$sort} {$dir}";

require_once '../lib/SimplePager.php';
$p = new SimplePager($sql, $params, 10, $page);
$arr = $p->result;

$p->item_count = $total;
$p->page_count = (int)ceil($total / 10);

$_title = 'Order History';
include '../head.php';
?>
<form method="get">
    <?= html_search('search', 'placeholder = "Search..."') ?>
    <button type="submit">Search</button>
</form>

<p>
    <?= count($arr) ?> of <?= $total ?> record(s) |
    Page <?= $p->page ?> of <?= $p->page_count ?>
</p>

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
                <td> <button data-get="orderdetail.php?id=<?= $h->id ?>">Detail</button></td>
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
