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

        <!-- Sidebar -->
        <div class="d-flex">
            <div id="sidebar" class="bg-danger text-white p-3">
                <!-- Logo -->
                <div class="text-center mb-4">
                    <a href="/">
                        <img src="/image/icon.svg" class="rounded-pill" width="100%">
                    </a>
                </div>

                <!-- Dark Mode -->
                <div class="theme-toggle mb-3">
                    <input type="checkbox" id="themeToggle">
                    <label for="themeToggle" class="toggle-btn">
                        <span class="toggle-icon"></span>
                    </label>
                </div>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link text-white" href="/">🏠 Home</a>
                    </li>
                    <?php if (!$_user || $_user?->role == "Member"): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/order/shopping_cart.php">
                                🛒 Cart
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
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/product/product_maintenance.php">📦 Product</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/member/userList.php">👥 User List</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/order/order_list.php">📄 Order List</a>
                        </li>
                    <?php endif ?>

                    <?php if (!$_user): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">Account</a>
                            <ul class="dropdown-menu">
                                <li class="nav-item">
                                    <a class="dropdown-item" href="/user/login.php">Login</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="/user/register.php">Register</a>
                                </li>
                                <li class="nav-item">
                                    <a class="dropdown-item" href="/user/reset.php">Forget Password</a>
                                </li>
                            </ul>
                        </li>
                    <?php endif ?>

                    <?php if ($_user?->role == "Member"): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/order/history.php">📜 Order History</a>
                        </li>
                    <?php endif ?>

                    <?php if ($_user): ?>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/user/profile.php">👤 Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/user/password.php">🔑 Password</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-white" href="/user/logout.php">🚪 Logout</a>
                        </li>
                    <?php endif ?>

                </ul>
            </div>
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


    <main id="content" class="p-4 flex-fill">
        <h1 class="p-5 text-center"><?= $_title ?? 'Untitled' ?></h1>