<?php
include '../base.php';

auth('Admin');

$result = $_db->query('SELECT * FROM user');
$arr = $result->fetchAll();



$_title = 'User List';
include '../head.php';
?>
<p>
    Create New Admin Account ? <a href="addAdmin.php">Click Me</a>
</p>



<div class="container mt-3">
    <table id="myTable" class="display">
        <thead class="bg-danger text-white">
            <tr>
                <th>Id</th>
                <th>Email</th>
                <th>Name</th>
                <th>Role</th>
                <th>Active</th>
                <th>Photo</th>
                <th>More</th>
            </tr>
        <tbody>
            <?php foreach ($arr as $s): ?>
                <tr>
                    <td><?= $s->id ?></td>
                    <td><?= $s->email ?></td>
                    <td><?= $s->name ?></td>
                    <td><?= $s->role ?></td>
                    <td><?= $s->active ?></td>
                    <td><img src="/photo/<?= $s->photo ?>" style="height:50px;weight:50px"></td>
                    <td>
                        <button data-get="memberDetail.php?id=<?= $s->id ?>" class="btn btn-info">Detail</button>
                        <?php if ($s->active == "Yes"): ?>
                            <button data-post="active.php?id=<?= $s->id ?>" data-confirm="Diactive Account?" class="btn btn-danger">Diactive</button>
                        <?php endif ?>
                        <?php if ($s->active == "No"): ?>
                            <button data-post="active.php?id=<?= $s->id ?>" data-confirm="Reactive Account?" class="btn btn-success">Reactive</button>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>

    </table>

    <script>
        $(document).ready(function() {
            $('#myTable').DataTable();
        });
    </script>
    <?php
    include '../foot.php';
