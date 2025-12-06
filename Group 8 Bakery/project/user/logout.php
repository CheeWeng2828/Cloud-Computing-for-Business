<?php
require '../base.php';
include '../head.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_logout'])) {
    temp('info', 'Logout successful');
    set_cart();
    logout();
    exit();
}


$_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php'; 
?>

<script>
    if (confirm('Are you sure you want to logout?')) {
      
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'logout.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'confirm_logout';
        input.value = 'yes';
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    } else {
       
        window.location.href = '<?php echo $_SESSION['previous_page'] ?>';
    }
</script>

<?php
include '../foot.php';