<?php
require_once '../includes/config.php';
require_once '../classes/User.php';
require_once '../classes/Order.php';

// Initialize user and check if admin
$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Check if order ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: " . SITE_URL . "admin/orders.php");
    exit;
}

$order_id = (int)$_GET['id'];

// Initialize order class
$order = new Order();

// Get order details
$order_details = $order->getById($order_id);
if (!$order_details) {
    $_SESSION['error'] = "Order not found";
    header("Location: " . SITE_URL . "admin/orders.php");
    exit;
}

// Get order items
$order_items = $order->getOrderItems($order_id);

// Get customer information
$customer = new User();
$customer_info = $customer->getUserById($order_details['user_id']);

$page_title = "Order #" . $order_id . " Details";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Football Jersey Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="admin-content">
            <!-- Admin Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Order Detail Content -->
            <div class="order-detail">
                <div class="page-header">
                    <h1><?php echo $page_title; ?></h1>
                    <a href="<?php echo SITE_URL; ?>admin/orders.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Back to Orders
                    </a>
                </div>
                
                <div class="order-info-grid">
                    <!-- Order Summary -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Order Summary</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-group">
                                <div class="info-label">Order ID:</div>
                                <div class="info-value">#<?php echo $order_details['id']; ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Date:</div>
                                <div class="info-value"><?php echo date('F d, Y H:i', strtotime($order_details['order_date'])); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Status:</div>
                                <div class="info-value">
                                    <span class="status-badge status-<?php echo strtolower($order_details['status']); ?>">
                                        <?php echo $order_details['status']; ?>
                                    </span>
                                </div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Payment Method:</div>
                                <div class="info-value"><?php echo $order_details['payment_method']; ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Total Amount:</div>
                                <div class="info-value">$<?php echo number_format($order_details['total_amount'], 2); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer Information -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Customer Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-group">
                                <div class="info-label">Name:</div>
                                <div class="info-value"><?php echo htmlspecialchars($customer_info['name']); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Email:</div>
                                <div class="info-value"><?php echo htmlspecialchars($customer_info['email']); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Phone:</div>
                                <div class="info-value"><?php echo !empty($customer_info['phone']) ? htmlspecialchars($customer_info['phone']) : 'N/A'; ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Shipping Address -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Shipping Address</h2>
                        </div>
                        <div class="card-body">
                            <p><?php echo nl2br(htmlspecialchars($order_details['shipping_address'])); ?></p>
                        </div>
                    </div>
                    
                    <!-- Update Status -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Update Status</h2>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo SITE_URL; ?>admin/orders.php" method="POST">
                                <input type="hidden" name="order_id" value="<?php echo $order_details['id']; ?>">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select id="status" name="status" required>
                                        <option value="Pending" <?php echo $order_details['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Processing" <?php echo $order_details['status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                        <option value="Shipped" <?php echo $order_details['status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                        <option value="Delivered" <?php echo $order_details['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                        <option value="Cancelled" <?php echo $order_details['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="card">
                    <div class="card-header">
                        <h2>Order Items</h2>
                    </div>
                    <div class="card-body">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td class="product-cell">
                                            <div class="product-info">
                                                <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image']; ?>" alt="<?php echo isset($item['product_name']) ? $item['product_name'] : ''; ?>">
                                                <div>
                                                    <h4><?php echo htmlspecialchars(isset($item['product_name']) ? $item['product_name'] : ($item['name'] ?? '')); ?></h4>
                                                    <p><?php echo htmlspecialchars(isset($item['product_team']) ? $item['product_team'] : ($item['team'] ?? '')); ?> - <?php echo htmlspecialchars(isset($item['product_category']) ? $item['product_category'] : ($item['category'] ?? '')); ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $item['size']; ?></td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                    <td>$<?php echo number_format($order_details['total_amount'] - 10, 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Shipping:</strong></td>
                                    <td>$10.00</td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Total:</strong></td>
                                    <td>$<?php echo number_format($order_details['total_amount'], 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>