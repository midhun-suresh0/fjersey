<?php
require_once '../includes/config.php';
require_once '../classes/Cart.php';
require_once '../classes/Product.php';

// Initialize cart
$cart = new Cart();
$product = new Product();

// Check if action is set
if (!isset($_POST['action'])) {
    header("Location: " . SITE_URL . "public/shop.php");
    exit;
}

$action = $_POST['action'];

// Handle different cart actions
switch ($action) {
    case 'add':
        // Check required parameters
        if (!isset($_POST['product_id']) || !isset($_POST['quantity']) || !isset($_POST['size'])) {
            $_SESSION['error'] = "Missing required parameters";
            header("Location: " . SITE_URL . "public/shop.php");
            exit;
        }
        
        $product_id = (int)$_POST['product_id'];
        $quantity = (int)$_POST['quantity'];
        $size = $_POST['size'];
        
        // Validate product exists and has enough stock
        $product_data = $product->getById($product_id);
        if (!$product_data) {
            $_SESSION['error'] = "Product not found";
            header("Location: " . SITE_URL . "public/shop.php");
            exit;
        }
        
        if ($product_data['stock'] < $quantity) {
            $_SESSION['error'] = "Not enough stock available";
            header("Location: " . SITE_URL . "public/product_detail.php?id=" . $product_id);
            exit;
        }
        
        // Add to cart
        $cart->addItem($product_id, $quantity, $size);
        $_SESSION['success'] = "Product added to cart";
        
        // Redirect back to product page or referrer
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : SITE_URL . "public/shop.php";
        header("Location: " . $redirect);
        break;
        
    case 'update':
        // Check required parameters
        if (!isset($_POST['cart_id']) || !isset($_POST['quantity'])) {
            $_SESSION['error'] = "Missing required parameters";
            header("Location: " . SITE_URL . "public/cart.php");
            exit;
        }
        
        $cart_id = (int)$_POST['cart_id'];
        $quantity = (int)$_POST['quantity'];
        
        // Update cart item
        $cart->updateItem($cart_id, $quantity);
        $_SESSION['success'] = "Cart updated";
        header("Location: " . SITE_URL . "public/cart.php");
        break;
        
    case 'remove':
        // Check required parameters
        if (!isset($_POST['cart_id'])) {
            $_SESSION['error'] = "Missing required parameters";
            header("Location: " . SITE_URL . "public/cart.php");
            exit;
        }
        
        $cart_id = (int)$_POST['cart_id'];
        
        // Remove item from cart
        $cart->removeItem($cart_id);
        $_SESSION['success'] = "Item removed from cart";
        header("Location: " . SITE_URL . "public/cart.php");
        break;
        
    case 'clear':
        // Clear entire cart
        $cart->clear();
        $_SESSION['success'] = "Cart cleared";
        header("Location: " . SITE_URL . "public/cart.php");
        break;
        
    default:
        header("Location: " . SITE_URL . "public/shop.php");
        break;
}
?>