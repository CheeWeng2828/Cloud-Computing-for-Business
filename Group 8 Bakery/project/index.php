<?php
require 'base.php';

$query = "SELECT * FROM product WHERE active = 'Yes'";
$products = $_db->query($query)->fetchAll();

$_title = 'Menu';
include 'head.php';
?>
<?php if (!$_user || $_user->role == "Member") : ?>
    <div class="product-grid">
        <?php foreach ($products as $product): ?>
            <?php
            $cart = get_cart();
            $id = $product->id;
            $stock = $product->stock;
            $unit = $cart[$product->id] ?? 0;
            ?>
            <div class="row">
                <div class="col-md card" style="width:400px">
                    <img class="card-img-top" src="/product_img/<?= $product->photo ?>" alt="<?= $product->name ?>" data-get="detail.php?id=<?= $product->id ?>"
                        style="width:100%;height:100%">
                    <div class="card-body">
                        <h4 class="card-title"><?= $product->name ?></h4>
                        <p class="card-text">RM <?= number_format($product->price, 2) ?></p>
                        <p class="card-text"><?= $product->stock ?> pieces available</p>
                        <a href="product/detail.php?id=<?= $product->id ?>" class="btn btn-danger">Detail</a>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>

<?php else: ?>
    <h2>This is Admin Page</h2>
<?php endif ?>

<?php
include 'foot.php';
