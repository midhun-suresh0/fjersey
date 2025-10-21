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

// Check if user ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: " . SITE_URL . "admin/users.php");
    exit;
}

$user_id = (int)$_GET['id'];

// Get user details
$user_details = $user->getUserById($user_id);
if (!$user_details) {
    $_SESSION['error'] = "User not found";
    header("Location: " . SITE_URL . "admin/users.php");
    exit;
}

// Get user orders
$order = new Order();
$user_orders = $order->getOrdersByUserId($user_id);

$page_title = "User Details: " . $user_details['name'];
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
            
            <!-- User Detail Content -->
            <div class="user-detail">
                <div class="page-header">
                    <h1><?php echo $page_title; ?></h1>
                    <a href="<?php echo SITE_URL; ?>admin/users.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Back to Users
                    </a>
                </div>
                
                <div class="user-info-grid">
                    <!-- User Information -->
                    <div class="card">
                        <div class="card-header">
                            <h2>User Information</h2>
                        </div>
                        <div class="card-body">
                            <div class="info-group">
                                <div class="info-label">ID:</div>
                                <div class="info-value"><?php echo $user_details['id']; ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Name:</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_details['name']); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Email:</div>
                                <div class="info-value"><?php echo htmlspecialchars($user_details['email']); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Phone:</div>
                                <div class="info-value"><?php echo !empty($user_details['phone']) ? htmlspecialchars($user_details['phone']) : 'N/A'; ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Registration Date:</div>
                                <div class="info-value"><?php echo date('F d, Y H:i', strtotime($user_details['created_at'])); ?></div>
                            </div>
                            <div class="info-group">
                                <div class="info-label">Role:</div>
                                <div class="info-value">
                                    <?php if ($user_details['is_admin']): ?>
                                        <span class="badge badge-admin">Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-user">User</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Actions</h2>
                        </div>
                        <div class="card-body">
                            <?php if ($user_details['id'] != $_SESSION['user_id']): ?>
                                <a href="<?php echo SITE_URL; ?>admin/users.php?toggle_admin=<?php echo $user_details['id']; ?>" class="btn <?php echo $user_details['is_admin'] ? 'btn-warning' : 'btn-success'; ?>" onclick="return confirm('Are you sure you want to <?php echo $user_details['is_admin'] ? 'remove admin privileges from' : 'make admin'; ?> this user?');">
                                    <i class="fas <?php echo $user_details['is_admin'] ? 'fa-user-minus' : 'fa-user-plus'; ?>"></i>
                                    <?php echo $user_details['is_admin'] ? 'Remove Admin Privileges' : 'Make Admin'; ?>
                                </a>
                                
                                <a href="<?php echo SITE_URL; ?>admin/users.php?delete=<?php echo $user_details['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                    <i class="fas fa-trash"></i> Delete User
                                </a>
                            <?php else: ?>
                                <p class="text-muted">You cannot modify your own account from here.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- User Orders -->
                <div class="card">
                    <div class="card-header">
                        <h2>Order History</h2>
                    </div>
                    <div class="card-body">
                        <?php if (empty($user_orders)): ?>
                            <p>This user has not placed any orders yet.</p>
                        <?php else: ?>
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($user_orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                    <?php echo $order['status']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo SITE_URL; ?>admin/order_detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm" title="View Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>