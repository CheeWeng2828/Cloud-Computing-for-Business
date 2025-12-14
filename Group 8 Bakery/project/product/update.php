<?php
require '../base.php';

auth("Admin");

if (is_get()) {
    $id = req('id');

    $stm = $_db->prepare('SELECT * FROM product WHERE id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();

    extract((array)$s);
    $_SESSION['photo'] = $s->photo;



    $id = $s->id;
    $name = $s->name;
    $price = $s->price;
    $photo = $s->photo;
    $stock = $s->stock;
    $description = $s->description;
}

if (is_post()) {
    $id         = req('id');
    $name       = req('name');
    $price     = req('price');
    $stock   = req('stock');
    $description = req('description');
    $f = get_file('photo');
    $photo = $_SESSION['photo'];

    if (!$price) {
        $_err['price'] = "Required";
    }
    if (!$stock) {
        $_err['stock'] = "Required";
    }

    if (!$description) {
        $_err['description'] = "Required";
    }

    if (!$name) {
        $_err['name'] = "Required";
    } else if (strlen($name) > 100) {
        $_err['name'] = "Maximun 100 charater";
    } else {
        $stm = $_db->prepare('SELECT COUNT(*) FROM product WHERE name=? AND id != ?');
        $stm->execute([$name, $id]);

        if ($stm->fetchColumn() > 0) {
            $_err['name'] = 'Duplicated';
        }
    }

    if ($f) {
        if ($f->size > 1 * 1024 * 1024) {
            $_err['photo'] = "Maximun Size is 1MB";
        } else if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        }
    }

    if (!$_err) {
        if ($f) {
            unlink("../product_img/$photo");
            $photo = save_photo($f, '../product_img');
        }
        $stm = $_db->prepare('UPDATE product 
                              SET price = ?,name = ?,photo = ?,stock = ?,description = ?
                              WHERE id = ?
                            ');
        $stm->execute([$price, $name, $photo, $stock, $description, $id]);

        temp('info', 'Update Sucessful');
        redirect('product_maintenance.php');
    }
}

$_title = "Product Update";
include '../head.php';
?>
<div class="container mt-3">
    <form method="post" class="updateProduct" enctype="multipart/form-data">

        <div class="input-group">
            <span class="input-group-text">Id</span>
            <?= html_text('id', 'maxlength="100"', 'disabled', 'value="<?= $id ?>"', "class='form-control-plaintext'") ?>
            <?= err('id') ?><br>
        </div>
        <br>

        <div class="input-group">
            <span class="input-group-text">Name</span>
            <?= html_text('name', 'maxlength="100"') ?>
            <?= err('name') ?><br>
        </div>
        <br>

        <div class="input-group">
            <span class="input-group-text">Price(RM)</span>
            <?= html_number('price', '1', '100', '0.01') ?>
            <?= err('price') ?><br>
        </div>
        <br>

        <div class="input-group">
            <span class="input-group-text">Stock</span>
            <?= html_number('stock', '0', '100', '1', 'value = $stock') ?>
            <?= err('stock') ?><br>
        </div>
        <br>

        <div class="input-group">
            <span class="input-group-text">Description</span>
            <?= html_textarea('description', 'rows="4"', 'cols="55"') ?>
            <?= err("description") ?>
        </div>
        <br>

        <div class="input-group">
            <span class="input-group-text">Photo</span>
            <label class="upload" tabindex="0">
                <?= html_file('photo', 'image/*', 'hidden') ?>
                <img src="/product_img/<?= $photo ?>">
            </label>
            <?= err('photo') ?>
        </div>
        <br>

        <section>
            <button class="updateSub">Submit ✔</button>
            <button type="reset" class="updateReset">Reset ⟳</button>
        </section>
    </form>
</div>
<?php
include '../foot.php';
