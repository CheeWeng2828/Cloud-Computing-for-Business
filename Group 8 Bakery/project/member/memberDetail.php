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

<table class="detail">
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
            <form method="post">
                <td><?= html_select('role', $_userRole, $d->role) ?></td>
            </form>
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

<script>
    $('select').on('change', e => e.target.form.submit());
</script>
<?php
include '../foot.php';
