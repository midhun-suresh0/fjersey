<?php
require_once '../includes/config.php';
require_once '../classes/User.php';
require_once '../classes/Order.php';

$user = new User();

// Check if user is logged in
if (!$user->isLoggedIn()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Check if order ID is provided
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if (!$order_id) {
    $_SESSION['error'] = "Invalid order ID provided.";
    header("Location: " . SITE_URL . "public/index.php");
    exit;
}

$orderObj = new Order();
$order = $orderObj->getById($order_id);

// If order not found or doesn't belong to current user
// Determine current user ID safely without assuming a specific method exists
$currentUserId = null;
if (method_exists($user, 'getId')) {
    $currentUserId = $user->getId();
} elseif (method_exists($user, 'getUserId')) {
    $currentUserId = $user->getUserId();
} elseif (method_exists($user, 'getUser')) {
    $u = $user->getUser();
    if (is_array($u) && isset($u['id'])) {
        $currentUserId = $u['id'];
    } elseif (is_object($u) && isset($u->id)) {
        $currentUserId = $u->id;
    }
} elseif (isset($_SESSION['user_id'])) {
    $currentUserId = (int) $_SESSION['user_id'];
}

if (!$order || $currentUserId === null || $order['user_id'] != $currentUserId) {
    $_SESSION['error'] = "Order not found or access denied.";
    header("Location: " . SITE_URL . "public/index.php");
    exit;
}

// Get order items
$items = $orderObj->getOrderItems($order_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - FJersey</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <!-- Order Confirmation Section -->
    <section class="section">
        <div class="container">
            <div class="order-confirmation">
                <div class="confirmation-header">
                    <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745;"></i>
                    <h1>Thank You for Your Order!</h1>
                    <p>Your order has been placed successfully.</p>
                </div>
                
                <div class="order-details">
                    <h2>Order Details</h2>
                    <div class="detail-row">
                        <span>Order Number:</span>
                        <span>#<?php echo htmlspecialchars($order['id']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Order Date:</span>
                        <span><?php echo date('F j, Y', strtotime($order['created_at'])); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Payment Status:</span>
                        <span><?php echo ucfirst($order['status']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Payment ID:</span>
                        <span><?php echo htmlspecialchars($order['payment_id']); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Shipping Name:</span>
                        <span><?php echo htmlspecialchars(isset($order['shipping_name']) ? $order['shipping_name'] : (isset($order['shipping_address']) ? '' : '')); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Shipping Phone:</span>
                        <span><?php echo htmlspecialchars(isset($order['shipping_phone']) ? $order['shipping_phone'] : ''); ?></span>
                    </div>
                    <div class="detail-row">
                        <span>Shipping Address:</span>
                        <span>
                            <?php 
                            $sa = isset($order['shipping_address']) ? $order['shipping_address'] : '';
                            $sc = isset($order['shipping_city']) ? $order['shipping_city'] : '';
                            $sst = isset($order['shipping_state']) ? $order['shipping_state'] : '';
                            $sp = isset($order['shipping_pincode']) ? $order['shipping_pincode'] : '';
                            $parts = array_filter([$sa, $sc, $sst]);
                            echo htmlspecialchars(implode(', ', $parts) . ($sp ? ' - ' . $sp : ''));
                            ?>
                        </span>
                    </div>
                    <div class="detail-row total">
                        <span>Order Total:</span>
                        <span>₹<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
                
                <div class="order-items">
                    <h2>Order Items</h2>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <div class="order-item">
                                <div class="item-details">
                                    <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                                    <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                    <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                    <p>Price: ₹<?php echo number_format($item['price'], 2); ?></p>
                                </div>
                                <div class="item-total">
                                    ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No items found in this order.</p>
                    <?php endif; ?>
                </div>
                
                <div class="confirmation-actions">
                    <a href="<?php echo SITE_URL; ?>public/shop.php" class="btn btn-primary">Continue Shopping</a>
                    <?php if ($order['status'] === 'paid'): ?>
                        <button onclick="window.print()" class="btn btn-secondary">Print Order</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
