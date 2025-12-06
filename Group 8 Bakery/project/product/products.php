<?php
require '../base.php';

$category = req('category');

if ($category === 'cake') {
    $query = "SELECT * FROM product WHERE name LIKE '%cake%' AND active = 'Yes'";
    $products = $_db->query($query)->fetchAll();
} elseif ($category === 'bread') {
    $query = "SELECT * FROM product WHERE name NOT LIKE '%cake%' AND name NOT LIKE'%Tart%' AND active = 'Yes'";
    $products = $_db->query($query)->fetchAll();
} elseif ($category === 'pastries') {
    $query = "SELECT * FROM product WHERE name LIKE '%Tart%' AND active = 'Yes'";
    $products = $_db->query($query)->fetchAll();
} else {
    $query = "SELECT * FROM product WHERE active = 'Yes'";
    $products = $_db->query($query)->fetchAll();
}


$_title = ucfirst($category) ?: 'All Products';
include '../head.php';
?>

<form method="get">
    Category : <?= html_select('category', $categories, 'All Product') ?>
</form>

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
                style="width:100%">
                <div class="card-body">
                    <h4 class="card-title"><?= $product->name ?></h4>
                    <p class="card-text">RM <?= number_format($product->price, 2) ?></p>
                    <p class="card-text"><?= $product->stock ?> pieces available</p>
                    <a href="detail.php?id=<?= $product->id ?>" class="btn btn-primary">See Profile</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<script>
    $('select').on('change', e => e.target.form.submit());
</script>
<?php include '../foot.php'; ?>