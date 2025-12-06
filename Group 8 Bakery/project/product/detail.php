<?php
include '../base.php';

if (is_post()) {
    $id = req('id');
    $units = req('unit');
    $stock = req('stock');
    update_cart($id, $units, $stock);
    redirect();
}

$id = req('id');
$stm = $_db->prepare("SELECT * FROM product WHERE id = ?");
$stm->execute([$id]);
$product = $stm->fetch();
if (!$product) redirect('index.php');

$_title = 'Detail';
include '../head.php';
?>
<div class="productDetailContainer">
    <p>
        <img src="/product_img/<?= $product->photo ?>" class="productDetailIMG">
    </p>
    <div class="productDetail">
        <p class="productName"><?= $product->name ?></p>
        <p class="productName">RM<?= $product->price ?></p><br>
        <div class="productDescription">
            <p>
                <?= $product->description ?>
            </p>
        </div>
    </div>
</div>
<?php
$cart = get_cart();
$id = $product->id;
$stock = $product->stock;
$unit = $cart[$product->id] ?? 0;
?>
<form method="post" class="productDetailQuantity">
    <?= html_hidden('id') ?>
    Stock: <?= html_hidden('stock') ?>
    <?= html_select('unit', $_units, '') ?>
    <?= $unit ? '✅' : '' ?>
    <?= $product->stock ?> Piece Available
</form>
<script>
    $('select').on('change', e => e.target.form.submit());
</script>
<?php
include '../foot.php';
