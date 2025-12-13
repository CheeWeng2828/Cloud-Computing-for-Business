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
<style>
    /* Layout helpers */
    .profile-card,
    .address-card {
        border-radius: 12px;
    }

    /* Upload / current photo */
    .upload .current-photo {
        width: 100%;
        padding: 6px 0;
    }

    .upload img {
        border: 2px solid rgba(0, 0, 0, 0.06);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    /* Make sure textareas and inputs are full width inside the card */
    .profile-card .form-control,
    .address-card .form-control {
        width: 100%;
    }

    /* Responsive tweak: small screens stack with spacing */
    @media (max-width: 767.98px) {

        .profile-card,
        .address-card {
            margin-bottom: 12px;
        }
    }
</style>
<form method="post" enctype="multipart/form-data">
    <div class="container my-4">
        <div class="row g-4">
            <!-- LEFT: Profile column -->
            <div class="col-12 col-md-4">
                <div class="card profile-card shadow-sm p-3">
                    <h5 class="mb-3">Profile Details</h5>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <?= html_text('email', 'class="form-control" maxlength="100"') ?>
                        <?= err('email') ?>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Name:</label>
                        <?= html_text('name', 'class="form-control" maxlength="100"') ?>
                        <?= err('name') ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Photo</label>
                        <label class="upload d-inline-block" tabindex="0" style="cursor:pointer;">
                            <?= html_file('profile', 'image/*', 'hidden') ?>
                            <div class="current-photo d-flex justify-content-center align-items-center">
                                <img src="/photo/<?= $_user->photo ?>" alt="photo" class="rounded-circle" style="width:96px;height:96px;object-fit:cover;">
                            </div>
                        </label>
                        <?= err('profile') ?>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Address column (shows only for Member) -->
            <div class="col-12 col-md-8">
                <div class="card address-card shadow-sm p-3">

                    <?php if ($_user?->role == "Member"): ?>
                        <div class="address_details">
                            <h5 class="mb-3">Address Information</h5>

                            <div class="mb-3">
                                <label for="street_number" class="form-label">Street Number :</label>
                                <?= html_textarea('street_number', 'class="form-control" rows="2"') ?>
                                <?= err("street_number") ?>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">City:</label>
                                    <?= html_text('city', 'class="form-control" maxlength="50"') ?>
                                    <?= err("city") ?>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="state" class="form-label">State:</label>
                                    <?= html_text('state', 'class="form-control" maxlength="50"') ?>
                                    <?= err("state") ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="postcode" class="form-label">Postcode:</label>
                                <?= html_text('postcode', 'class="form-control" maxlength="10"') ?>
                                <?= err("postcode") ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Non-members see no address fields. You can add other details here if needed -->
                        <p class="text-muted">Address fields are available for Members only.</p>
                    <?php endif ?>
                </div>
            </div>

        </div> <!-- /.row -->

        <!-- Buttons -->
        <div class="row mt-4">
            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-success">Submit ✔</button>
                <button type="reset" class="btn btn-danger">Reset ⟳</button>
            </div>
        </div>
    </div>
</form>
</div>
<?php
include '../foot.php';
