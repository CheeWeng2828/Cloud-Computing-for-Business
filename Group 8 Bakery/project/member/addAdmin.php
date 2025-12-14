<?php
include '../base.php';

if (is_post()) {
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');
    $name     = req('name');
    $f = get_file('photo');

    // Validate: email
    if (!$email) {
        $_err['email'] = 'Required';
    } else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    } else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    } else if (!is_unique($email, 'user', 'email')) {
        $_err['email'] = 'Duplicated';
    }

    // Validate: password
    if (!$password) {
        $_err['password'] = 'Required';
    } else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Between 5-100 characters';
    }

    // Validate: confirm
    if (!$confirm) {
        $_err['confirm'] = 'Required';
    } else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5-100 characters';
    } else if ($confirm != $password) {
        $_err['confirm'] = 'Not matched';
    }

    // Validate: name
    if (!$name) {
        $_err['name'] = 'Required';
    } else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    // Validate: photo (file)
    if (!$f) {
        $_err['photo'] = 'Required';
    } else if (!str_starts_with($f->type, 'image/')) {
        $_err['photo'] = 'Must be image';
    } else if ($f->size > 1 * 1024 * 1024) {
        $_err['photo'] = 'Maximum 1MB';
    }

    // DB operation
    if (!$_err) {

        //Save photo
        $photo = save_photo($f, '../photo');

        // Insert data Same like Register Member But Different Role
        $stm = $_db->prepare('
            INSERT INTO user (email, password, name, photo, role,active)
            VALUES (?,SHA1(?),?,?,"Admin","Yes")
        ');
        $stm->execute([$email, $password, $name, $photo]);

        temp('info', 'Record inserted');
        redirect('userList.php');
    }
}


$_title = 'New Admin';

include '../head.php';
?>
<div class="container mt-3">
    <form method="post" class="register" enctype="multipart/form-data">
        <div class="input-group">
            <span class="input-group-text">Email</span>
            <?= html_text('email', 'maxlength="100"') ?><br>
            <?= err('email') ?>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-text">Name</span>
            <?= html_text('name', 'maxlength="100"') ?><br>
            <?= err('name') ?>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-text">Password</span>
            <?= html_password('password', 'maxlength="100"') ?><br>
            <?= err('password') ?>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-text">Confirm</span>
            <?= html_password('confirm', 'maxlength="100"') ?><br>
            <?= err('confirm') ?>
        </div>
        <br>
        <div class="input-group">
            <span class="input-group-text">Upload Photo</span>
            <label class="upload" tabindex="0">
                <?= html_file("photo", "image/*", "hidden") ?>
                <img src="../image/placeholder.jpg">
            </label><br>
            <?= err("photo") ?>

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
