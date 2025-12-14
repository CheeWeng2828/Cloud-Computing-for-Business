<?php
require '../base.php';

auth("Admin");

$name = req('name');

$stm = $_db->prepare('SELECT *
                      FROM product
                      WHERE name LIKE ?');
$stm->execute(["%$name%"]);
$arr = $stm->fetchAll();

$_title = "Product List";
include '../head.php';
?>

<p>
    Any New Product? <a href="insert_product.php">Click Me</a>
</p>

<div class="container mt-3">
    <table id="myTable" class="display">
        <thead class="bg-danger text-white">
            <tr>
                <th>Id</th>
                <th>Name</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Active</th>
                <th>Photo</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arr as $s): ?>
                <tr>
                    <td><?= $s->id ?></td>
                    <td><?= $s->name ?></td>
                    <td><?= $s->price ?></td>
                    <td><?= $s->stock ?></td>
                    <td><?= $s->active ?></td>
                    <td><img src="/product_img/<?= $s->photo ?>" style="height:50px;width:50px"></td>
                    <td>
                        <button data-get="update.php?id=<?= $s->id ?>" class="btn btn-info">Update</button>
                        <?php if ($s->active == "Yes"): ?>
                            <button data-post="delete.php?id=<?= $s->id ?>" data-confirm="Diactive Product?" class="btn btn-danger">Diactive</button>
                        <?php endif ?>
                        <?php if ($s->active == "No"): ?>
                            <button data-post="delete.php?id=<?= $s->id ?>" data-confirm="Reactive Product?" class="btn btn-success">Reactive</button>
                        <?php endif ?>
                    </td>
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
?>