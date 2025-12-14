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

        $stm->execute([$id, $name, $price, $photo, $stock, $description]);


        temp('info', 'Insert Sucessful');
        redirect('product_maintenance.php');
    }
}


$_title = "Insert Product";
include '../head.php';
?>

<div class="container mt-3">
    <form method="post" class="register" enctype="multipart/form-data">
        <div class="input-group">
            <span class="input-group-text">Id</span>
            <?= html_text('id', 'maxlength="4" data-upper') ?><br>
            <?= err('id') ?>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-text">Name</span>
            <?= html_text('name', 'maxlength="100"') ?><br>
            <?= err('name') ?>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-text">Price</span>
            <?= html_number('price', '1', '100', '0.5') ?><br>
            <?= err('price') ?>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-text">Stock</span>
            <?= html_number('stock', '1', '100', '1') ?><br>
            <?= err('stock') ?>
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
                <img src="/image/placeholder.jpg">
            </label><br>
            <?= err('photo') ?>
        </div>
        <br>
        <section>
            <button class="btn btn-success">Submit</button>
            <button type="reset" class="btn btn-danger">Reset</button>
        </section>
    </form>
</div>


<?php
include '../foot.php';
