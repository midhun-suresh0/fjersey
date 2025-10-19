<?php
$page_title = "Manage Products";
require_once '../includes/config.php';
require_once '../classes/User.php';
require_once '../classes/Product.php';

// Initialize user and check if admin
$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Initialize product class
$product = new Product();

// Handle product deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    $delete_result = $product->delete($product_id);
    
    if ($delete_result) {
        $_SESSION['success'] = "Product deleted successfully";
    } else {
        $_SESSION['error'] = "Failed to delete product";
    }
    
    // Redirect to remove the query string
    header("Location: " . SITE_URL . "admin/products.php");
    exit;
}

// Get all products
$products = $product->getAll();
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
            
            <!-- Products Content -->
            <div class="products-management">
                <div class="page-header">
                    <h1>Manage Products</h1>
                    <a href="<?php echo SITE_URL; ?>admin/product_form.php" class="btn">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
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
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Team</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Featured</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <tr>
                                        <td colspan="9" style="text-align: center;">No products found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($products as $item): ?>
                                        <tr>
                                            <td><?php echo $item['id']; ?></td>
                                            <td>
                                                <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                            </td>
                                            <td><?php echo $item['name']; ?></td>
                                            <td><?php echo $item['team']; ?></td>
                                            <td><?php echo $item['category']; ?></td>
                                            <td>$<?php echo number_format($item['price'], 2); ?></td>
                                            <td>
                                                <span class="stock-badge <?php echo $item['stock'] <= 5 ? 'stock-low' : ($item['stock'] <= 10 ? 'stock-medium' : ''); ?>">
                                                    <?php echo $item['stock']; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php echo $item['featured'] ? '<i class="fas fa-check" style="color: green;"></i>' : '<i class="fas fa-times" style="color: red;"></i>'; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?php echo SITE_URL; ?>admin/product_form.php?id=<?php echo $item['id']; ?>" class="btn btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?php echo SITE_URL; ?>admin/products.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this product?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
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
</body>
</html>