<?php

require '../base.php';

$_title = 'Payment Detail';
include '../head.php';
auth("Member");


if (is_post()) {

    $userInfo = $_db->prepare('SELECT * FROM user_payment WHERE user_id = ?');
    $userInfo->execute([$_user->id]);
    $u = $userInfo->fetchAll();

    //for cardpayment validation
    $cardholder_name = req('cardholder_name');
    $card_no = req('card_no');
    $expiry_month = req('expiry_month');
    $expiry_year = req('expiry_year');
    $cvv = req('cvv');

    if ($cardholder_name == '') {
        $_err['cardholder_name'] = 'Cardholder name is required.';
    }

    if (strlen($card_no) != 16 || !is_numeric($card_no)) {
        $_err['card_no'] = 'Invalid card number. Must be 16 digits.';
    }
    if ($expiry_month < 1 || $expiry_month > 12) {
        $_err['expiry_month'] = 'Invalid expiry month. Please enter a value between 1 and 12.';
    }
    if ($expiry_year < 25 || $expiry_year > 50) {
        $_err['expiry_month'] = 'Invalid expiry year. Please enter a value between 25 and 50.';
    }

    if (empty($cvv) || strlen($cvv) != 3 || !is_numeric($cvv)) {
        $_err['cvv'] = 'Invalid CVV. Must be 3 digits.';
    }


    if (!$_err) {

        if ($u) {
            $stm = $_db->prepare('
        UPDATE user_payment SET cardholder_name=?,card_no=?, expiry_month=?, expiry_year=?, cvv=? 
        WHERE user_id = ?
    ');
            $stm->execute([$cardholder_name, $card_no, $expiry_month, $expiry_year, $cvv, $_user->id]);
        } else if (!$u) {
            $stm = $_db->prepare('
            INSERT INTO user_payment (cardholder_name,card_no, expiry_month, expiry_year, cvv,user_id)
            VALUES (?,?,?,?,?,?)
        ');
            $stm->execute([$cardholder_name, $card_no, $expiry_month, $expiry_year, $cvv, $_user->id]);
        }

        temp('info', 'Payment save successfully!');
        redirect("payment.php");
    }
}
?>

<div class="payment-container">
    <div class="payment-details">
        <form method="post">
            <h3>Payment</h3>
            <label for="cardholder_name">Cardholder Name:</label>
            <?= html_text('cardholder_name', 'maxlength="100"', 'placeholder="Name"') ?><br><br>
            <?php err('cardholder_name') ?>


            <label for="card_no">Card Number:</label>
            <?= html_text('card_no', 'maxlength="16"', 'placeholder=" 0000 0000 0000 0000"') ?><br><br>
            <?php err('card_no') ?>

            <label for="expiry_date">Expiry Month/Year:</label><br>
            <div class="expiry-date-container">
                <?= html_number('expiry_month', '1', '12', '1', 'maxlength = "2"', 'placeholder = "MM"') ?>
                <span>/</span>
                <?= html_number('expiry_year', '25', '50', '1', 'maxlength = "2"', 'placeholder = "YY"') ?>
            </div>


            <label for="cvv">CVV:</label><br>
            <?= html_text('cvv', 'maxlength = "3"', 'placeholder="3 digit only"') ?><br><br>
            <?php err('cvv') ?><br><br>


            <button type="submit">Confirm</button>

    </div>
    </form>

    <?php
    include '../foot.php';
