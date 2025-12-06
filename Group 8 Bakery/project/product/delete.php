<?php
include '../base.php';

if (is_post()) {

    $id = req('id');

    $query = $_db->prepare('SELECT active FROM product WHERE id = ?');
    $query->execute([$id]);
    $status = $query->fetch();

    if($status->active == "Yes"){
        $stm = $_db->prepare('UPDATE product SET active = "No" WHERE id = ? ');
        $stm->execute([$id]);
    }

    else if($status->active == "No"){
        $stm = $_db->prepare('UPDATE product SET active = "Yes" WHERE id = ? ');
        $stm->execute([$id]);
    }
    

    temp('info', 'Update Successful');
    redirect('product_maintenance.php');
}
