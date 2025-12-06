<?php
require '../base.php';

if (is_post()) {
    $email = req('email');
    $password = req('password');

    if ($email == '') {
        $_err['email'] = 'Required';
    }
    if (!is_email($email)) {
        $_err['email'] = 'Invalid Email';
    }

    if ($password == '') {
        $_err['password'] = 'Required';
    }

    if (!$_err) {
        $stm = $_db->prepare('SELECT * FROM user WHERE email = ? AND password = SHA1(?)');
        $stm->execute([$email, $password]);
        $u = $stm->fetch();

        if ($u && $u->active == "Yes") {
            temp('info', 'Login Successful');
            login($u);
        } else {
            $_err['password'] = "Not Matched";
        }
    }
}

$_title = 'Login';
include '../head.php';
?>
<div class="container mt-3">
    <form method="post" class="login">
        <div class="form-floating mb-3 mt-3">
            <?= html_text('email', 'maxlength:"100"','placeholder="Enter email"') ?>
            <label for="email">Email</label>
            <?= err('email') ?>
        </div>
        <div class="form-floating mb-3 mt-3">
            <?= html_password('password', 'maxlength="100"','placeholder="Enter password"') ?>
            <label for="password">Password</label>
            <?= err('password') ?>
        </div>
        <section>
            <button class="btn btn-primary">Login</button>
            <button class="btn btn-secondary" type="reset">Reset</button>
        </section>
        <p>Forget Password ? <a href="/user/reset.php">Click Me</a> !!!</p>
    </form>
</div>


<?php
include '../foot.php';
