<?php
require '../base.php';

auth("Admin");

if (is_post()) {
    $id         = req('id');
    $name       = req('name');
    $price     = req('price');
    $stock = req('stock');
    $description = req('description');
    $f = get_file('photo');

    if ($id == "") {
        $_err['id'] = 'Required';
    } else if (!preg_match('/^[A-Z]\d{3}$/', $id)) {
        $_err['id'] = 'Invalid Format';
    } else if (!is_unique($id, 'product', 'id')) {
        $_err['id'] = 'Duplicated';
    }


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
    }

    if (!$f) {
        $_err['photo'] = 'Required';
    } else if (!str_starts_with($f->type, 'image/')) {
        $_err['photo'] = 'Must be image';
    } else if ($f->size > 1 * 1024 * 1024) {
        $_err['photo'] = 'Maximum 1MB';
    }


    if (!$_err) {
        $photo = save_photo($f, '../product_img');
        $stm = $_db->prepare('
    INSERT INTO product
     ( id , name , price , photo,stock,description,active)
     VALUES(?,?,?,?,?,?,"Yes")');

        $stm->execute([$id, $name, $price, $photo, $stock,$description]);


        temp('info', 'Insert Sucessful');
        redirect('product_maintenance.php');
    }
}


$_title = "Insert Product";
include '../head.php';
?>

<form method="post" class="register" enctype="multipart/form-data">
    <label for="id">Id</label>
    <?= html_text('id', 'maxlength="4" data-upper') ?><br>
    <?= err('id') ?>


    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?><br>
    <?= err('name') ?>

    <label for="price">Price</label>
    <?= html_number('price', '1', '100', '0.5') ?><br>
    <?= err('price') ?>

    <label for="stock">Stock</label>
    <?= html_number('stock', '1', '100', '1') ?><br>
    <?= err('stock') ?>

    <label for="description">Description</label>
    <?= html_textarea('description', 'rows="4"', 'cols="55"') ?>
    <?= err("description") ?>

    <label for="photo">Photo</label>
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/image/placeholder.jpg">
    </label><br>
    <?= err('photo') ?>

    <section>
        <button class="submitReg">Submit ✔</button>
        <button type="reset" class="resetReg">Reset ⟳</button>
    </section>
</form>

<?php
include '../foot.php';
