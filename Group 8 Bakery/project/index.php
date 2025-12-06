<?php
require 'base.php';

$_title = 'Welcome';
include 'head.php';
?>
<?php if (!$_user || $_user->role == "Member") : ?>
    <div class="container-fluid mt-3">
        <div class="row category-cards">
            <div class="col-md category-cards">
                <a href="../product/products.php" class="card">
                    <img src="/image/all.jpg" alt="All Products">
                    <span>All Products</span>
                </a>
            </div>
            <div class="col-md category-cards">
                <a href="../product/products.php?category=cake" class="card">
                    <img src="/image/cake.jpg" alt="Delicious Cakes">
                    <span>Cakes</span>
                </a>
            </div>
            <div class="col-md category-cards">
                <a href="../product/products.php?category=bread" class="card">
                    <img src="/image/breadbackground.jpg" alt="Fresh Bread">
                    <span>Bread</span>
                </a>
            </div>
            <div class="col-md category-cards">
                <a href="../product/products.php?category=pastries" class="card">
                    <img src="/image/tartt.png" alt="Pastries">
                    <span>Pastries</span>
                </a>
            </div>
        </div>
    </div>
<?php else: ?>
    <h2>This is Admin Page</h2>
<?php endif ?>
<?php
include 'foot.php';
