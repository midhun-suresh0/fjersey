<?php
$page_title = "Order Confirmation";
require_once '../includes/header.php';
require_once '../classes/Order.php';

// Check if user is logged in
if (!$user->isLoggedIn()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Initialize order object
$orderObj = new Order();

// Get order details
$order = $orderObj->getById($order_id);

// Check if order exists and belongs to current user
if (!$order || $order['user_id'] != $user->getId()) {
    header("Location: " . SITE_URL . "public/index.php");
    exit;
}

// Get order items
$order_items = $orderObj->getOrderItems($order_id);
?>

<!-- Order Confirmation Section -->
<section class="section">
    <div class="container">
        <div class="order-confirmation">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="confirmation-header">
                <h1>Order Confirmation</h1>
                <p>Thank you for your order! Your order has been received and is being processed.</p>
            </div>
            
            <div class="order-details">
                <div class="order-info">
                    <h2>Order Information</h2>
                    <p><strong>Order Number:</strong> #<?php echo $order_id; ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo ucfirst($order['payment_method']); ?></p>
                    <p><strong>Payment Status:</strong> <?php echo ucfirst($order['status']); ?></p>
                    <?php if ($order['payment_id']): ?>
                        <p><strong>Payment ID:</strong> <?php echo $order['payment_id']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="shipping-info">
                    <h2>Shipping Information</h2>
                    <p><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></p>
                </div>
            </div>
            
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="order-items">
                    <?php foreach ($order_items as $item): ?>
                        <div class="order-item">
                            <div class="item-info">
                                <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                <div>
                                    <h4><?php echo $item['name']; ?></h4>
                                    <p>Size: <?php echo $item['size']; ?> | Qty: <?php echo $item['quantity']; ?></p>
                                </div>
                            </div>
                            <div class="item-price">
                                $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-totals">
                    <div class="total-row">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($order['total_amount'] - 10.00, 2); ?></span>
                    </div>
                    <div class="total-row">
                        <span>Shipping</span>
                        <span>$<?php echo number_format(10.00, 2); ?></span>
                    </div>
                    <div class="total-row grand-total">
                        <span>Total</span>
                        <span>$<?php echo number_format($order['total_amount'], 2); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="order-actions">
                <a href="<?php echo SITE_URL; ?>public/shop.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
ALTER TABLE orders
ADD COLUMN payment_id VARCHAR(255) NULL AFTER payment_method,
ADD COLUMN razorpay_order_id VARCHAR(255) NULL AFTER payment_id;