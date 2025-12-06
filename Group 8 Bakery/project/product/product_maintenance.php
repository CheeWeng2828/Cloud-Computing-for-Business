<?php
require '../base.php';
$name = req('name');

auth("Admin");

$stm = $_db->prepare('SELECT *
                      FROM product
                      WHERE name LIKE ?');
$stm->execute(["%$name%"]);
$arr = $stm->fetchAll();

$_title = "Product List";
include '../head.php';
?>

<form>
    <p><?= count($arr) ?> record(s)</p>
    <?= html_search('name','placeholder="Name..."') ?>
    <button>Search</button>
    <p>Any New Product? <button data-get="insert_product.php">Insert Product</button> </p>
</form>



<table class="orderList">
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Active</th>
        <th colspan="2">Action</th>
    </tr>

    <?php foreach ($arr as $s) : ?>
        <tr>
            <td><?= $s->id ?></td>
            <td><?= $s->name ?></td>
            <td><?= $s->price ?></td>
            <td><?= $s->stock ?></td>
            <td><?=$s ->active ?></td>
            <td>
                <button data-get="update.php?id=<?= $s->id ?>">Update</button>
            </td>
            <td>
            <?php if($s->active == "Yes"): ?>
                <button data-post="delete.php?id=<?= $s->id ?>" data-confirm="Diactive Product?" class="productDelete">Diactive</button>
                <?php endif?>
                <?php if($s->active == "No"): ?>
                <button data-post="delete.php?id=<?= $s->id ?>" data-confirm="Reactive Product?">Reactive</button>
                    <?php endif?>
                    <img src="/product_img/<?= $s->photo ?>" class="popup">
            </td>
        <?php endforeach ?>
        </tr>
</table>

<?php
include '../foot.php';
