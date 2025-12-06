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

$_title = 'Order | Shopping Cart';
include '../head.php';
?>

<table class="cart">
    <tr>
        <th>Id</th>
        <th>Name</th>
        <th>Price (RM)</th>
        <th>Unit</th>
        <th>Subtotal (RM)</th>
    </tr>

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
            <td><?= $p->id ?></td>
            <td><?= $p->name ?></td>
            <td class="right"><?= $p->price ?></td>
            <td>
                <form method="post">
                    <?= html_hidden('id') ?>
                    <!-- For Update Stock Quantity -->
                    <?= html_hidden('stock') ?>
                    <?= html_select('unit', $_units, '') ?>
                </form>
            </td>
            <td class="right">
                <?= sprintf('%.2f', $subtotal) ?>
                <img src="/product_img/<?= $p->photo ?>" class="popup">
            </td>
        </tr>
    <?php endforeach ?>

    <tr>
        <th colspan="3"></th>
        <th class="right"><?= $count ?></th>
        <th class="right"><?= sprintf('%.2f', $total) ?></th>
    </tr>
</table>

<p>
    <?php if ($cart): ?>
        <?php if ($_user?->role == 'Member'): ?>
            <?php if ($_user?->address): ?>
                <button data-post="checkout.php">Checkout</button>
            <?php endif ?>
            <?php if (!$_user?->address): ?>
                Please update your shipping <a href="/user/profile.php">Address</a> to checkout
            <?php endif ?>
        <?php elseif (!$_user): ?>
            Please <a href="/user/login.php">login</a> as member to checkout
        <?php endif ?>
    <?php endif ?>
</p>

<script>
    $('select').on('change', e => e.target.form.submit());
</script>

<?php
include '../foot.php';
