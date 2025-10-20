<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/razorpay_config.php';
require_once '../classes/User.php';
require_once '../classes/Order.php';
require_once '../classes/Product.php';
require_once '../vendor/autoload.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

header('Content-Type: application/json');

function sendJsonResponse($success, $message, $data = null) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    sendJsonResponse(false, 'User not logged in');
}

// Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    sendJsonResponse(false, 'Cart is empty');
}

try {
    // Get the payment details from POST
    $razorpay_payment_id = $_POST['razorpay_payment_id'];
    $razorpay_order_id = $_POST['razorpay_order_id'];
    $razorpay_signature = $_POST['razorpay_signature'];
    
    // Initialize Razorpay API
    $api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);
    
    // Verify payment signature
    $attributes = array(
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature' => $razorpay_signature
    );
    
    $api->utility->verifyPaymentSignature($attributes);
    
    // Get user details
    $user = new User();
    $user_details = $user->getUserData();
    
    // Prepare order data
    $order_data = array(
        'shipping_name' => $user_details['name'],
        'shipping_email' => $user_details['email'],
        'shipping_phone' => $user_details['phone'],
        'shipping_address' => $user_details['address'],
        'shipping_city' => $user_details['city'],
        'shipping_state' => $user_details['state'],
        'shipping_pincode' => $user_details['pincode']
    );
    
    // Create new order
    $order = new Order();
    $cart_items = $_SESSION['cart'];
    
    // Get product details for each cart item
    foreach ($cart_items as &$item) {
        $product = new Product();
        $product_details = $product->getById($item['product_id']);
        $item['name'] = $product_details['name'];
        $item['price'] = $product_details['price'];
    }
    
    $order_id = $order->create($_SESSION['user_id'], $order_data, $cart_items);
    
    if (!$order_id) {
        throw new Exception('Failed to create order');
    }
    
    // Update payment details
    $payment_details = array(
        'payment_id' => $razorpay_payment_id,
        'order_id' => $razorpay_order_id
    );
    
    if (!$order->updatePaymentDetails($order_id, $payment_details)) {
        throw new Exception('Failed to update payment details');
    }
    
    // Clear the cart
    unset($_SESSION['cart']);
    
    // Send success response with order ID
    sendJsonResponse(true, 'Payment successful', array(
        'order_id' => $order_id,
        'redirect_url' => 'order_confirmation.php?order_id=' . $order_id
    ));
    
} catch (SignatureVerificationError $e) {
    error_log('Razorpay Signature Verification Error: ' . $e->getMessage());
    sendJsonResponse(false, 'Payment verification failed');
} catch (Exception $e) {
    error_log('Payment Processing Error: ' . $e->getMessage());
    sendJsonResponse(false, 'An error occurred while processing your payment: ' . $e->getMessage());
}
?>