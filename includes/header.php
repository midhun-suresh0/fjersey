<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Cart.php';

// Initialize cart
$cart = new Cart();
$cart_count = $cart->getItemCount();

// Initialize user
$user = new User();
$isLoggedIn = $user->isLoggedIn();
$isAdmin = $user->isAdmin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>
    <!-- Header Top -->
    <div class="header-top">
        <div class="container">
            <div class="header-top-left">
                <span><i class="fas fa-phone"></i> +1 234 567 890</span>
                <span><i class="fas fa-envelope"></i> info@fjersey.com</span>
            </div>
            <div class="header-top-right">
                <?php if ($isLoggedIn): ?>
                    <a href="<?php echo SITE_URL; ?>public/account.php"><i class="fas fa-user"></i> My Account</a>
                    <a href="<?php echo SITE_URL; ?>public/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>public/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="<?php echo SITE_URL; ?>public/register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <a href="<?php echo SITE_URL; ?>" class="logo">F<span>Jersey</span></a>
            
            <form action="<?php echo SITE_URL; ?>public/shop.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search for jerseys...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
            
            <div class="header-cart">
                <a href="<?php echo SITE_URL; ?>public/cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart"></i>
                    <?php if ($cart_count > 0): ?>
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
            </div>
        </div>
    </header>
    
    <!-- Navigation -->
    <nav>
        <div class="container">
            <ul class="nav-menu">
                <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                <li><a href="<?php echo SITE_URL; ?>public/shop.php">Shop</a></li>
                <li><a href="<?php echo SITE_URL; ?>public/about.php">About Us</a></li>
                <li><a href="<?php echo SITE_URL; ?>public/contact.php">Contact</a></li>
                <?php if ($isAdmin): ?>
                    <li><a href="<?php echo SITE_URL; ?>admin/dashboard.php">Admin Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>