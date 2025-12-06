<?php
include '../base.php';

auth();

if (is_get()) {

    $stm = $_db->prepare('SELECT * FROM user WHERE id = ?');
    $stm->execute([$_user->id]);
    $u = $stm->fetch();

    if (!$u) {
        redirect('/');
    }
    extract((array)$u);
    $_SESSION['profile'] = $u->photo;

    $address = trim((string)$u->address);

    if ($address !== '') {
        $parts = array_map('trim', explode('|', $address));
        list($street_number, $postcode, $city, $state) = array_pad($parts, 4, '');
    } else {
        $street_number = $postcode = $city = $state = '';
    }
}
if (is_post()) {

    $email = req('email');
    $name = req('name');
    $profile = $_SESSION['profile'];
    $f = get_file('profile');

    if ($email == '') {
        $_err['email'] = "Required";
    } else if (!is_email($email)) {
        $_err['email'] = "Invalid email";
    } else if (strlen($email) > 100) {
        $_err['email'] = "Maximun 100 charater";
    } else {
        $stm = $_db->prepare('SELECT COUNT(*) FROM user WHERE email= ? AND id != ?');
        $stm->execute([$email, $_user->id]);

        if ($stm->fetchColumn() > 0) {
            $_err['email'] = 'Duplicated';
        }
    }

    if ($name == '') {
        $_err['name'] = "Required";
    } else if (strlen($name) > 100) {
        $_err['name'] = "Maximun 100 charater";
    }

    if ($f) {
        if ($f->size > 1 * 1024 * 1024) {
            $_err['profile'] = "Maximun Size is 1MB";
        } else if (!str_starts_with($f->type, 'image/')) {
            $_err['profile'] = 'Must be image';
        }
    }

    if ($_user?->role == "Member") {
        // Address Validation
        $street_number = req('street_number');
        $city = req('city');
        $state = req('state');
        $postcode = req('postcode');

        if ($street_number == '') {
            $_err['street_number'] = 'Street number is required';
        }
        if ($city == '') {
            $_err['city'] = 'City is required';
        }
        if ($state == '') {
            $_err['state'] = 'State is required';
        }
        if ($postcode == '' || !is_numeric($postcode)) {
            $_err['postcode'] = 'Valid postcode is required.Must be 5 digits.';
        }
        $address = "$street_number|$postcode| $city| $state "; //combine 4in1
    }


    if (!$_err) {

        if ($f) {
            unlink("../photo/$profile");
            $profile = save_photo($f, '../photo');
        }
        $stm = $_db->prepare('
            UPDATE user
            SET email = ?,name = ?,photo = ?
            WHERE id = ?
        ');
        $stm->execute([$email, $name, $profile, $_user->id]);

        $stm = $_db->prepare('
            UPDATE user
            SET address = ?
            WHERE id = ?
        ');
        $stm->execute([$address, $_user->id]);

        $_user->email = $email;
        $_user->name = $name;
        $_user->photo = $profile;
        $_user->address = $address;

        temp('info', 'Update Sucessful');
        redirect('/');
    }
}
$_title = 'Profile';
include '../head.php'
?>

<form method="post" enctype="multipart/form-data">
    <?php if ($_user?->role == "Member"): ?>
        <div class="address-container">
            <div class="address_details">
                <h3>Address Information</h3>

                <label for="street_number">Street Number :</label>
                <?= html_textarea('street_number', 'rows="4"', 'cols="55"') ?>
                <?= err("street_number") ?>

                <label for="city">City:</label>
                <?= html_text('city', 'maxlength = "50"') ?>
                <?= err("city") ?>

                <label for="state">State:</label>
                <?= html_text('state', 'maxlength = "50"') ?>
                <?= err("state") ?>

                <label for="postcode">Postcode:</label>
                <?= html_text('postcode', 'maxlength="10"') ?>
                <?= err("postcode") ?>

            </div>
        </div>
    <?php endif ?>
    <div class="address-container">
        <div class="mb-3 mt-3">
            <label for="email" class="form-label">Email:</label><br>
            <?= html_text('email', 'maxlength="100"') ?>
            <?= err('email') ?><br><br>
        </div>
        <div class="mb-3 mt-3">

            <label for="name" class="form-label">Name:</label><br>
            <?= html_text('name', 'maxlength="100"') ?>
            <?= err('name') ?><br><br>
        </div>

        <div class="mb-3 mt-3">
            <label for="profile" class="form-label">Photo:</label><br>
            <label class="upload" tabindex="0">
                <?= html_file('profile', 'image/*', 'hidden') ?>
                <img src="/photo/<?= $_user->photo ?>"><br><br>
            </label>
            <?= err('profile') ?>
        </div>

        <section>
            <button class="btn btn-success">Submit ✔</button>
            <button type="reset" class="btn btn-danger">Reset ⟳</button>
        </section>
</form>
</div>
</div>
<?php
include '../foot.php';
