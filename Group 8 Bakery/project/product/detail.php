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
$cart = get_cart();
$id = $product->id;
$stock = $product->stock;
$unit = $cart[$product->id] ?? 0;


$_title = 'Detail';
include '../head.php';
?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 border p-3 main-section bg-white">
            <div class="row hedding m-0 pl-3 pt-0 pb-3">
                Product Detail
            </div>
            <div class="row m-0">
                <div class="col-lg-4 left-side-product-box pb-3">
                    <img src="/product_img/<?= $product->photo ?>" class="border p-3">
                </div>
                <div class="col-lg-8">
                    <div class="right-side-pro-detail border p-3 m-0">
                        <div class="row">
                            <div class="col-lg-12">
                                <p class="m-0 p-0"><?= $product->name ?></p>
                            </div>
                            <div class="col-lg-12">
                                <p class="m-0 p-0 price-pro">RM<?= $product->price ?></p>
                                <hr class="p-0 m-0">
                            </div>
                            <div class="col-lg-12 pt-2">
                                <h5>Product Detail</h5>
                                <span><?= $product->description ?></span>
                                <hr class="m-0 pt-2 mt-2">
                            </div>
                            <div class="col-lg-12 pt-2">
                                <form method="post" class="productDetailQuantity">
                                    <?= html_hidden('id') ?>
                                    Stock: <?= html_hidden('stock') ?>
                                    <?= $product->stock ?> Piece Available
                                    <hr class="m-0 pt-2 mt-2">
                            </div>
                            <div class="col-lg-12">
                                <h6 class="text-center">Quantity :</h6>
                                <?= html_select('unit', $_units, '0','class="form-select text-center"') ?>
                            </div>
                            <div class="col-lg-12 mt-3">
                                <div class="row">
                                    <div class="col-lg-6 pb-2">
                                        <button class="btn btn-danger w-100">Add To Cart</button>
                                    </div>
                                    <div class="col-lg-6 pb-2">
                                        <button data-get="/" class="btn btn-light w-100">Back</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include '../foot.php';
