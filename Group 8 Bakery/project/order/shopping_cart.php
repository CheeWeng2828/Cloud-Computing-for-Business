<?php
include '../base.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $id   = req('id');
    $unit = req('unit');
    $stock = req('stock');
    update_cart($id, $unit, $stock);
    redirect();
}

// ----------------------------------------------------------------------------

$_title = '🛒 Your Shopping Cart';
include '../head.php';
?>
<div class="container my-4">

    <div class="row g-4">

        <!-- Cart Items -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-0">

                    <table class="table align-middle mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th>Item</th>
                                <th>Price (RM)</th>
                                <th>Qty</th>
                                <th>Subtotal (RM)</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $count = 0;
                            $total = 0;

                            $stm = $_db->prepare('SELECT * FROM product WHERE id = ?');
                            $cart = get_cart();

                            foreach ($cart as $id => $unit):
                                $stm->execute([$id]);
                                $p = $stm->fetch();

                                $stock = $p->stock;
                                $subtotal = $p->price * $unit;
                                $count += $unit;
                                $total += $subtotal;
                            ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="/product_img/<?= $p->photo ?>"
                                                 class="rounded-circle"
                                                 style="width:70px;height:70px;object-fit:cover">
                                            <strong><?= $p->name ?></strong>
                                        </div>
                                    </td>

                                    <td><?= number_format($p->price, 2) ?></td>

                                    <td>
                                        <form method="post" class="d-inline">
                                            <?= html_hidden('id', $id) ?>
                                            <?= html_hidden('stock', $stock) ?>
                                            <?= html_select('unit', $_units,"Remove From Cart",'class="form-select form-select-sm" style="width:80px"') ?>
                                        </form>
                                    </td>

                                    <td class="fw-bold">
                                        <?= number_format($subtotal, 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card shadow-sm sticky-top" style="top: 90px;">
                <div class="card-body">

                    <h5 class="fw-bold mb-3">Order Summary</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Items</span>
                        <span><?= $count ?></span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Total Price</span>
                        <span class="fw-bold text-danger">
                            RM <?= number_format($total, 2) ?>
                        </span>
                    </div>

                    <hr>

                    <?php if ($cart): ?>
                        <?php if ($_user?->role == 'Member'): ?>
                            <?php if ($_user?->address): ?>
                                <button class="btn btn-warning w-100 fw-bold"
                                        data-post="checkout.php">
                                    Checkout 🍔
                                </button>
                            <?php else: ?>
                                <div class="alert alert-warning small mb-0">
                                    Please update your
                                    <a href="/user/profile.php" class="alert-link">
                                        shipping address
                                    </a>
                                </div>
                            <?php endif ?>
                        <?php else: ?>
                            <div class="alert alert-info small mb-0">
                                Please <a href="/user/login.php" class="alert-link">login</a>
                                as a member to checkout
                            </div>
                        <?php endif ?>
                    <?php else: ?>
                        <div class="alert alert-secondary small mb-0">
                            Your cart is empty.
                        </div>
                    <?php endif ?>

                </div>
            </div>
        </div>

    </div>
</div>


<script>
    $('select').on('change', e => e.target.form.submit());
</script>

<?php
include '../foot.php';
