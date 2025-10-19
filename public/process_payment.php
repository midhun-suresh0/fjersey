<?php
require_once '../includes/header.php';
require_once '../includes/razorpay_config.php';
require_once '../vendor/autoload.php';
require_once '../classes/Cart.php';
require_once '../classes/Order.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

// Check if user is logged in
if (!$user->isLoggedIn()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Initialize cart and order objects
$cart = new Cart();
$orderObj = new Order();

try {
    // Initialize Razorpay API
    $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
    
    // Get payment data from POST
    $razorpay_order_id = $_POST['razorpay_order_id'];
    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $razorpay_signature = $_POST['razorpay_signature'];
    $shipping_address = $_POST['shipping_address'];
    
    // Verify payment signature
    $attributes = array(
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature' => $razorpay_signature
    );
    
    $api->utility->verifyPaymentSignature($attributes);
    
    // Get cart items and calculate total
    $cartItems = $cart->getItems();
    $total = 0;
    foreach ($cartItems as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    $total += 10.00; // Add shipping cost
    
    // Verify amount matches session amount
    if (!isset($_SESSION['checkout_amount']) || $_SESSION['checkout_amount'] != $total) {
        throw new Exception('Amount mismatch');
    }
    
    // Create order in database
    $order_id = $orderObj->create(
        $user->getId(),
        $cartItems,
        $total,
        $shipping_address,
        'razorpay',
        $razorpay_payment_id,
        $razorpay_order_id
    );
    
    if ($order_id) {
        // Clear cart
        $cart->clear();
        
        // Clear checkout session data
        unset($_SESSION['razorpay_order_id']);
        unset($_SESSION['checkout_amount']);
        
        // Set success message
        $_SESSION['success'] = "Payment successful! Your order has been placed.";
        
        // Redirect to order confirmation
        header("Location: " . SITE_URL . "public/order_confirmation.php?id=" . $order_id);
        exit;
    } else {
        throw new Exception('Failed to create order');
    }
    
} catch (SignatureVerificationError $e) {
    // Handle signature verification failure
    $_SESSION['error'] = "Payment verification failed. Please contact support if your payment was deducted.";
    error_log("Razorpay Signature Verification Error: " . $e->getMessage());
    header("Location: " . SITE_URL . "public/checkout.php");
    exit;
    
} catch (Exception $e) {
    // Handle other errors
    $_SESSION['error'] = "An error occurred while processing your payment. Please try again.";
    error_log("Razorpay Payment Error: " . $e->getMessage());
    header("Location: " . SITE_URL . "public/checkout.php");
    exit;
}
?>