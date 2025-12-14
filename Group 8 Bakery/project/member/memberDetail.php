<?php
include '../base.php';

auth();

$id = req('id');

if (is_post()) {
    $role = req('role');
    $stm = $_db->prepare('UPDATE user SET role = ? WHERE id = ?');
    $stm->execute([$role, $id]);
    temp('info', 'Update Role Successful');
}


$profile = $_db->prepare('SELECT * FROM user WHERE id = ?');
$profile->execute([$id]);
$detail = $profile->fetchAll();


$_title = 'User Detail';
include '../head.php'
?>

<table class="table table-striped">
    <?php foreach ($detail as $d): ?>

        <tr>
            <th>Name :</th>
            <td><?= $d->name ?></td>
        </tr>

        <tr>
            <th>Email : </th>
            <td><?= $d->email ?></td>
        </tr>

        <tr>
            <th>Role : </th>
            <td><?= $d->role ?></td>
        </tr>
        <?php if ($d->role == 'Member'): ?>
            <tr>
                <th>Address : </th>
                <td><?= $d->address ?></td>
            </tr>
        <?php endif ?>
        <tr>
            <th>Photo</th>
            <td><img src="/photo/<?= $d->photo ?>"></td>
        </tr>
    <?php endforeach ?>
</table>

<button data-get="userList.php" class="btn btn-light">Back</button>

<script>
    $('select').on('change', e => e.target.form.submit());
</script>
<?php
include '../foot.php';
