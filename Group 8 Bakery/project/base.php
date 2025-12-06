<?php

date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();
//===================================================================================
// PHP function
//===================================================================================
function is_get()
{
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

function is_post()
{
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function post($key, $value = null)
{
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

function get($key, $value = null)
{
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

function req($key, $value = null)
{
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map("trim", $value) : trim($value);
}

function redirect($url = null)
{
    $url ??= $_SERVER['REQUEST_URI'];
    header("Location:$url");
    exit();
}

function temp($key, $value = null)
{
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    } else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}

function base($path = '')
{
    return "http://$_SERVER[SERVER_NAME]:$_SERVER[SERVER_PORT]/$path";
}

//===================================================================================
// Photo function
//===================================================================================
function save_photo($f, $folder, $weigth = 200, $height = 200)
{

    $photo = uniqid() . '.jpg';
    require_once 'lib/SimpleImage.php';

    $img = new SimpleImage();
    $img->fromFile($f->tmp_name)
        ->thumbnail($weigth, $height)
        ->toFile("$folder/$photo", 'image/jpeg');

    return $photo;
}

function get_file($key)
{
    $f = $_FILES[$key] ?? null;

    if ($f && $f['error'] == 0) {
        return (object)$f;
    }

    return null;
}

function html_file($key, $accept = '', $attr = '')
{
    echo "<input type='file' id='$key' name='$key' accept='$accept' $attr>";
}

//===================================================================================
// HTML Reusable Code
//===================================================================================

function encode($value)
{
    return htmlentities($value);
}

function html_textarea($key, $attr = '', $attr2 = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<textarea id='$key' name='$key' $attr $attr2>$value</textarea>";
}

function html_text($key, $attr = '', $attr2 = '',$attr3 = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='text' id='$key' class='form-control' name='$key' value='$value' $attr $attr2 $attr3>";
}

function html_password($key, $attr = ' ',$attr2 = '',$attr3 = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='password' id='$key' class='form-control' name='$key'value='$value' $attr $attr2 $attr3>";
}

function html_search($key, $attr = ' ')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type ='search' id='$key' name='$key' value='$value' $attr>";
}

function html_number($key, $min = '', $max = '', $step = '', $attr = '', $attr2 = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type='number' name='$key' id='$key' value='$value' min='$min' max='$max' step='$step' $attr $attr2>";
}

function table_headers($fields, $sort, $dir, $href = '')
{
    foreach ($fields as $k => $v) {
        $d = 'asc'; // Default direction
        $c = '';    // Default class

        if ($k == $sort) {
            $d = $dir == 'asc' ? 'desc' : 'asc';
            $c = $dir;
        }

        echo "<th><a href='?sort=$k&dir=$d&$href' class='$c'>$v</a></th>";
    }
}

function html_select($key, $items, $default = '- Select One -', $attr = '')
{
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name='$key' $attr>";
    if ($default !== null) {
        echo "<option value=''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $state = $id == $value ? 'selected' : '';
        echo "<option value='$id' $state>$text</option>";
    }
    echo '</select>';
}

function html_hidden($key, $attr = '')
{
    $value ??= encode($GLOBALS[$key] ?? '');
    echo "<input type='hidden' id='$key' name='$key' value='$value' $attr>";
}

// Generate SINGLE <input type='checkbox'>
function html_checkbox($key, $label = '', $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    $status = $value == 1 ? 'checked' : '';
    echo "<label><input type='checkbox' id='$key' name='$key' value='1' $status $attr>$label</label>";
}

//===================================================================================
// Check Format
//===================================================================================

function is_email($value)
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

//===================================================================================
// Error Handling
//===================================================================================
$_err = [];

function err($key)
{
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class ='err'>$_err[$key]</span><br>";
    } else {
        echo '<span> </span>';
    }
}

//===================================================================================
//      Security
//===================================================================================

$_user = $_SESSION['user'] ?? null;

// Login user
function login($user, $url = '/')
{
    $_SESSION['user'] = $user;
    redirect($url);
}

// Logout user
function logout($url = '/')
{
    unset($_SESSION['user']);
    redirect($url);
}

// Authorization
function auth(...$roles)
{
    global $_user;
    if ($_user) {
        if ($roles) {
            if (in_array($_user->role, $roles)) {
                return;
            }
        } else {
            return;
        }
    }

    redirect('../index.php');
}

//===================================================================================
//          Session For Order Id
//===================================================================================

function getOrderId()
{
    return $_SESSION['order_id'] ?? null;
}

function setOrderID($order_id = [])
{
    $_SESSION['order_id'] = $order_id;
}

function deleteOrderID(){
    unset($_SESSION['order_id']);
}

//===================================================================================
//          DATABASE
//===================================================================================
$_db = new PDO('mysql:dbname=assign', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

function is_unique($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

function is_exist($value, $table, $field)
{
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*)FROM $table WHERE $field=?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}

//===================================================================================
//          Shopping Cart
//===================================================================================

function get_cart()
{
    return $_SESSION['cart'] ?? [];
}

function set_cart($cart = [])
{
    $_SESSION['cart'] = $cart;
}

function update_cart($id, $unit, $quantity)
{
    $cart = get_cart();

    if ($unit >= 1 && $unit <= 10 && is_exist($id, 'product', 'id') && $unit <= $quantity) {
        $cart[$id] = $unit;
    } else {
        unset($cart[$id]);
    }

    set_cart($cart);
}

//===================================================================================
//          Email
//===================================================================================
function get_mail()
{
    require_once 'lib/PHPMailer.php';
    require_once 'lib/SMTP.php';

    $m = new PHPMailer(true);
    $m->isSMTP();
    $m->SMTPAuth = true;
    $m->Host = 'smtp.gmail.com';
    $m->Port = 587;
    $m->Username = 'sem3sbakery2025@gmail.com';
    $m->Password = 'kgki ehuc jrgb riiv';
    $m->CharSet = 'utf-8';
    $m->setFrom($m->Username, '🍞 Admin');

    return $m;
}

//===================================================================================
//          Global variable
//===================================================================================

$_units = array_combine(range(1, 10), range(1, 10));

$_orderStatus = array(
    'READY TO SHIP' => 'READY TO SHIP',
    'DELIVERED' => 'DELIVERED',
);

$_userRole = array(
    'Member' => 'Member',
    'Admin' => 'Admin',
);

$categories = array(
    'cake' => 'cake',
    'bread' => 'bread',
    'pastries' => 'pastries'
);