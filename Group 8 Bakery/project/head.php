<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="icon" href="/image/icon.svg">
    <link rel="stylesheet" href="/css/bakery.css">
    <!-- Table CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.5/css/dataTables.dataTables.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.5/js/dataTables.js"></script>
    <script src="/js/app.js"></script>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


</head>

<body>
    <header>
        <div id="info"><?= temp('info') ?></div>

        <nav class="navbar navbar-expand-sm bg-danger navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="/">
                    <img src="/image/icon.svg" alt="logo" class="rounded-pill">
                </a>
                <ul class="navbar-nav">
                    <li class="nav-item d-flex align-items-center">
                        <div class="form-check form-switch d-flex align-items-center">
                            <input class="form-check-input" type="checkbox" id="themeToggle" name="darkmode" value="yes">
                            <label class="form-check-label text-light" for="mySwitch">Dark Mode</label>
                        </div>
                    </li>
                    <?php if (!$_user || $_user?->role == "Member"): ?>

                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link" href="/order/shopping_cart.php">
                                <img src="/image/shopping_cart.png" alt="shopping cart" style="width: 35px; height:30px">
                                <?php
                                $cart = get_cart();
                                $count = array_sum($cart);
                                if ($count) echo "($count)";
                                ?>
                            </a>
                        </li>
                    <?php endif ?>


                    <?php if ($_user?->role == "Admin"): ?>
                        <?= set_cart() ?>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link" href="/product/product_maintenance.php">Product</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link" href="/member/userList.php">User List</a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link" href="/order/order_list.php">Order List</a>
                        </li>
                    <?php endif ?>

                    <?php if (!$_user): ?>
                        <li class="nav-item dropdown d-flex align-items-center">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Account</a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/user/login.php">Login</a></li>
                                <li><a class="dropdown-item" href="/user/register.php">Register</a></li>
                                <li><a class="dropdown-item" href="/user/reset.php">Forget Password</a></li>
                            </ul>
                        </li>
                    <?php endif ?>
                    <?php if ($_user?->role == "Member"): ?>
                        <li class="nav-item d-flex align-items-center">
                            <a class="nav-link" href="/order/history.php">Order History</a>
                        </li>
                    <?php endif ?>

                    <?php if ($_user): ?>
                        <li class="nav-item dropdown d-flex align-items-center">
                            <a class="nav-link dropdown " href="#" role="button" data-bs-toggle="dropdown"><img class="rounded-pill"
                                    width="40px" height="40px" src="/photo/<?= $_user->photo ?>"></a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/user/profile.php">Profile</a></li>
                                <li><a class="dropdown-item" href="/user/password.php">Password</a></li>
                                <li><a class="dropdown-item" href="/user/logout.php">Logout</a></li>
                            </ul>
                        </li>
                    <?php endif ?>

                </ul>
            </div>
        </nav>
    </header>
    <script>
        let savedTheme = localStorage.getItem("theme");

        if (!savedTheme) savedTheme = "light";

        applyTheme(savedTheme);

        document.getElementById("themeToggle").checked = (savedTheme === "dark");

        document.getElementById("themeToggle").addEventListener("click", function() {
            let newTheme = this.checked ? "dark" : "light";
            applyTheme(newTheme);

            localStorage.setItem("theme", newTheme);
        });

        function applyTheme(theme) {
            const html = document.documentElement;

            html.classList.remove("light", "dark");
            html.classList.add(theme);
            html.setAttribute("data-bs-theme", theme);
        }
    </script>


    <main>
        <h1 class="p-5 text-center"><?= $_title ?? 'Untitled' ?></h1>