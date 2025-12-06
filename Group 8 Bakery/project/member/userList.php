<?php
include '../base.php';

auth('Admin');

$search = req('search');
$role = req('role');

if ($role) {
    $result = $_db->prepare('SELECT * FROM user WHERE name LIKE ? AND role = ?');
    $result->execute(["%$search%", $role]);
    $arr = $result->fetchAll();
}

else{
    $result = $_db->prepare('SELECT * FROM user WHERE name LIKE ?');
    $result->execute(["%$search%"]);
    $arr = $result->fetchAll();
}



$_title = 'User List';
include '../head.php';
?>
<p>
    Create New Admin Account ? <a href="addAdmin.php">Click Me</a>
</p>

<form method="get">
    <?= html_search('search', 'placeholder = "Name..."') ?>
    <?= html_select('role', $_userRole, '') ?>
    <button type="submit">Search</button>
</form>
<p><?= count($arr) ?> record(s)</p>

<table class="memberList">
    <tr>
        <th>Id</th>
        <th>Email</th>
        <th>Name</th>
        <th>Role</th>
        <th>Active</th>
        <th>Photo</th>
        <th>More</th>
    </tr>

    <?php foreach ($arr as $s): ?>
        <tr>
            <td><?= $s->id ?></td>
            <td><?= $s->email ?></td>
            <td><?= $s->name ?></td>
            <td><?= $s->role ?></td>
            <td><?= $s->active ?></td>
            <td><img src="/photo/<?= $s->photo ?>"></td>
            <td>
                <button data-get="memberDetail.php?id=<?= $s->id ?>">Detail</button>
                <?php if ($s->active == "Yes"): ?>
                    <button data-post="active.php?id=<?= $s->id ?>" data-confirm="Diactive Account?">Diactive</button>
                <?php endif ?>
                <?php if ($s->active == "No"): ?>
                    <button data-post="active.php?id=<?= $s->id ?>" data-confirm="Reactive Account?">Reactive</button>
                <?php endif ?>
            </td>
        </tr>
    <?php endforeach ?>
</table>

<?php
include '../foot.php';
