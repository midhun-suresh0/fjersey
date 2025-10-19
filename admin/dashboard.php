<?php
$page_title = "Admin Dashboard";
require_once '../includes/config.php';
require_once '../classes/User.php';
require_once '../classes/Admin.php';

// Initialize user and check if admin
$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Initialize admin class
$admin = new Admin();

// Get dashboard statistics
$stats = $admin->getDashboardStats();
$monthly_sales = $admin->getMonthlySales();
$top_products = $admin->getTopSellingProducts(5);
$recent_orders = $admin->getRecentOrders(5);
$low_stock = $admin->getLowStockProducts(5);
$new_users = $admin->getNewUsers(5);
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
            
            <!-- Dashboard Content -->
            <div class="dashboard">
                <h1>Dashboard</h1>
                
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Users</h3>
                            <p><?php echo $stats['total_users']; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Products</h3>
                            <p><?php echo $stats['total_products']; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Orders</h3>
                            <p><?php echo $stats['total_orders']; ?></p>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="stat-info">
                            <h3>Total Sales</h3>
                            <p>$<?php echo number_format($stats['total_sales'], 2); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Charts and Tables Row -->
                <div class="dashboard-row">
                    <!-- Sales Chart -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Monthly Sales</h2>
                        </div>
                        <div class="card-body">
                            <canvas id="salesChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Top Products -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Top Selling Products</h2>
                        </div>
                        <div class="card-body">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Team</th>
                                        <th>Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_products as $product): ?>
                                        <tr>
                                            <td><?php echo $product['name']; ?></td>
                                            <td><?php echo $product['team']; ?></td>
                                            <td><?php echo $product['total_sold']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders and Low Stock Row -->
                <div class="dashboard-row">
                    <!-- Recent Orders -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Recent Orders</h2>
                            <a href="<?php echo SITE_URL; ?>admin/orders.php" class="btn btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo $order['customer_name']; ?></td>
                                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo strtolower($order['status']); ?>">
                                                    <?php echo $order['status']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Low Stock Products -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h2>Low Stock Products</h2>
                            <a href="<?php echo SITE_URL; ?>admin/products.php" class="btn btn-sm">View All</a>
                        </div>
                        <div class="card-body">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Team</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($low_stock as $product): ?>
                                        <tr>
                                            <td><?php echo $product['name']; ?></td>
                                            <td><?php echo $product['team']; ?></td>
                                            <td>
                                                <span class="stock-badge <?php echo $product['stock'] <= 5 ? 'stock-low' : 'stock-medium'; ?>">
                                                    <?php echo $product['stock']; ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Chart
        const salesData = <?php echo json_encode(array_values($monthly_sales)); ?>;
        const salesLabels = <?php echo json_encode(array_keys($monthly_sales)); ?>;
        
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesLabels,
                datasets: [{
                    label: 'Monthly Sales ($)',
                    data: salesData,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>