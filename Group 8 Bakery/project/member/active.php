<?php
include '../base.php';

auth("Admin");
if (is_post()) {

    $id = req('id');

    $query = $_db->prepare('SELECT active FROM user WHERE id = ?');
    $query->execute([$id]);
    $status = $query->fetch();
    
    if ($status->active == "Yes") {
        $stm = $_db->prepare('UPDATE user SET active = "No" WHERE id = ? ');
        $stm->execute([$id]);
    }

    else if ($status->active == "No") {
        $stm = $_db->prepare('UPDATE user SET active = "Yes" WHERE id = ? ');
        $stm->execute([$id]);
    }


    temp('info', 'Update Successful');
    redirect('userList.php');
}

$_title = "Account Status Update";
include '../head.php'
?>

<?php
include '../foot.php';