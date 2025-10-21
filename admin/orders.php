<?php
$page_title = "Manage Orders";
require_once '../includes/config.php';
require_once '../classes/User.php';
require_once '../classes/Order.php';

// Initialize user and check if admin
$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Initialize order class
$order = new Order();

// Handle status update
if (isset($_POST['update_status']) && !empty($_POST['order_id']) && !empty($_POST['status'])) {
    $order_id = (int)$_POST['order_id'];
    $status = $_POST['status'];
    
    $update_result = $order->updateStatus($order_id, $status);
    
    if ($update_result) {
        $_SESSION['success'] = "Order status updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update order status";
    }
    
    // Redirect to remove the form submission
    header("Location: " . SITE_URL . "admin/orders.php");
    exit;
}

// Get all orders with user information
$orders = $order->getAllWithUserInfo();
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
            
            <!-- Orders Content -->
            <div class="orders-management">
                <div class="page-header">
                    <h1>Manage Orders</h1>
                </div>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment Method</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($orders)): ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center;">No orders found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($orders as $item): ?>
                                        <tr>
                                            <td>#<?php echo $item['id']; ?></td>
                                            <td><?php echo htmlspecialchars($item['user_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($item['order_date'])); ?></td>
                                            <td>₹<?php echo number_format($item['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($item['status']); ?>">
                                                    <?php echo $item['status']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo $item['payment_method']; ?></td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?php echo SITE_URL; ?>admin/order_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-sm" title="Update Status" onclick="showStatusModal(<?php echo $item['id']; ?>, '<?php echo $item['status']; ?>')">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Order Status</h2>
            <form action="" method="POST">
                <input type="hidden" id="order_id" name="order_id" value="">
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Delivered">Delivered</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="submit" name="update_status" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        // Modal functionality
        const modal = document.getElementById("statusModal");
        const closeBtn = document.getElementsByClassName("close")[0];
        
        function showStatusModal(orderId, currentStatus) {
            document.getElementById("order_id").value = orderId;
            document.getElementById("status").value = currentStatus;
            modal.style.display = "block";
        }
        
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
        
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>