<?php
include '../base.php';

auth();
if (is_post()) {

    $password       = req('password');
    $new_password   = req('new_password');
    $confirm        = req('confirm');

    if ($password == '') {
        $_err['password'] = 'Require';
    } else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Between 5 - 100 charaters';
    } else {
        $stm = $_db->prepare('
                SELECT COUNT(*)FROM user
                WHERE password = SHA1(?) AND id = ?
                ');
        $stm->execute([$password, $_user->id]);

        if ($stm->fetchColumn() == 0) {
            $_err['password'] = 'Not Matched';
        }
    }
    if ($new_password == '') {
        $_err['new_password'] = 'Require';
    } else if (strlen($new_password) < 5 || strlen($new_password) > 100) {
        $_err['new_password'] = 'Between 5 - 100 charaters';
    }
    if ($confirm == '') {
        $_err['confirm'] = 'Require';
    } else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5 - 100 charaters';
    } else if ($new_password != $confirm) {
        $_err['confirm'] = 'Not Matched';
    }

    if (!$_err) {
        $stm = $_db->prepare('
                UPDATE user
                SET password = SHA1(?)
                WHERE id = ?
                ');
        $stm->execute([$new_password, $_user->id]);

        temp('Info', 'Update Sucessful');
        redirect('/');
    }
}

$_title = 'Reset Password';
include '../head.php';
?>
<div class="container mt-3">
    <form method="post" class="register" enctype="multipart/form-data">
        <div class="input-group">
            <span class="input-group-text">Password</span>
            <?= html_password('password', 'maxlength = "100"') ?>
            <?= err('password') ?>
        </div>
        <div class="input-group">
            <span class="input-group-text">New Password</span>
            <?= html_password('new_password', 'maxlength = "100"') ?>
            <?= err('new_password') ?>
        </div>

        <div class="input-group">
            <span class="input-group-text">Confirm</span>
            <?= html_password('confirm', 'maxlength = "100"') ?>
            <?= err('confirm') ?>
        </div>
<br>
        <section>
            <button class="btn btn-success">Submit ✔</button>
            <button type="reset" class="btn btn-danger">Reset ⟳</button>
        </section>
    </form>
</div>
<?php
include '../foot.php';
