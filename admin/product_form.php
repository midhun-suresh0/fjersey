<?php
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

// Check if we're editing an existing product
$editing = false;
$product_data = [
    'id' => '',
    'name' => '',
    'description' => '',
    'price' => '',
    'stock' => '',
    'category' => '',
    'team' => '',
    'league' => '',
    'season' => '',
    'image' => '',
    'featured' => 0
];

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = (int)$_GET['id'];
    $existing_product = $product->getById($product_id);
    
    if ($existing_product) {
        $editing = true;
        $product_data = $existing_product;
        $page_title = "Edit Product";
    } else {
        $_SESSION['error'] = "Product not found";
        header("Location: " . SITE_URL . "admin/products.php");
        exit;
    }
} else {
    $page_title = "Add New Product";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $product_data = [
        'name' => $_POST['name'],
        'description' => $_POST['description'],
        'price' => $_POST['price'],
        'stock' => $_POST['stock'],
        'category' => $_POST['category'],
        'team' => $_POST['team'],
        'league' => $_POST['league'],
        'season' => $_POST['season'],
        'featured' => isset($_POST['featured']) ? 1 : 0
    ];
    
    // Handle image upload
    $image_uploaded = false;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload_dir = '../uploads/';
        
        // Create directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $file_name;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            // Try to upload file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $product_data['image'] = $file_name;
                $image_uploaded = true;
            } else {
                $_SESSION['error'] = "Sorry, there was an error uploading your file.";
            }
        } else {
            $_SESSION['error'] = "File is not an image.";
        }
    }
    
    // If editing and no new image was uploaded, keep the existing image
    if ($editing && !$image_uploaded) {
        $product_data['image'] = $existing_product['image'];
    }
    
    // Add or update product
    if ($editing) {
        $product_data['id'] = $product_id;
        // If a new image was uploaded, pass it; otherwise let update() keep existing image
        $image_param = $image_uploaded ? $product_data['image'] : null;
        $result = $product->update($product_id, $product_data, $image_param);
        $message = "Product updated successfully";
    } else {
        // New product must have an image
        if (!$image_uploaded) {
            $_SESSION['error'] = "Please upload an image for the product.";
        } else {
            $result = $product->create($product_data, $product_data['image']);
            $message = "Product added successfully";
        }
    }
    
    // Check result and redirect
    if (isset($result) && $result) {
        $_SESSION['success'] = $message;
        header("Location: " . SITE_URL . "admin/products.php");
        exit;
    } elseif (!isset($_SESSION['error'])) {
        $_SESSION['error'] = "Failed to save product";
    }
}
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
            
            <!-- Product Form Content -->
            <div class="product-form">
                <div class="page-header">
                    <h1><?php echo $page_title; ?></h1>
                    <a href="<?php echo SITE_URL; ?>admin/products.php" class="btn">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Product Name *</label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product_data['name']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="price">Price ($) *</label>
                                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?php echo htmlspecialchars($product_data['price']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="stock">Stock *</label>
                                    <input type="number" id="stock" name="stock" min="0" value="<?php echo htmlspecialchars($product_data['stock']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="category">Category *</label>
                                    <select id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Home Kit" <?php echo $product_data['category'] === 'Home Kit' ? 'selected' : ''; ?>>Home Kit</option>
                                        <option value="Away Kit" <?php echo $product_data['category'] === 'Away Kit' ? 'selected' : ''; ?>>Away Kit</option>
                                        <option value="Third Kit" <?php echo $product_data['category'] === 'Third Kit' ? 'selected' : ''; ?>>Third Kit</option>
                                        <option value="Goalkeeper" <?php echo $product_data['category'] === 'Goalkeeper' ? 'selected' : ''; ?>>Goalkeeper</option>
                                        <option value="Training" <?php echo $product_data['category'] === 'Training' ? 'selected' : ''; ?>>Training</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="team">Team *</label>
                                    <input type="text" id="team" name="team" value="<?php echo htmlspecialchars($product_data['team']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="league">League *</label>
                                    <select id="league" name="league" required>
                                        <option value="">Select League</option>
                                        <option value="Premier League" <?php echo $product_data['league'] === 'Premier League' ? 'selected' : ''; ?>>Premier League</option>
                                        <option value="La Liga" <?php echo $product_data['league'] === 'La Liga' ? 'selected' : ''; ?>>La Liga</option>
                                        <option value="Serie A" <?php echo $product_data['league'] === 'Serie A' ? 'selected' : ''; ?>>Serie A</option>
                                        <option value="Bundesliga" <?php echo $product_data['league'] === 'Bundesliga' ? 'selected' : ''; ?>>Bundesliga</option>
                                        <option value="Ligue 1" <?php echo $product_data['league'] === 'Ligue 1' ? 'selected' : ''; ?>>Ligue 1</option>
                                        <option value="International" <?php echo $product_data['league'] === 'International' ? 'selected' : ''; ?>>International</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="season">Season *</label>
                                    <input type="text" id="season" name="season" value="<?php echo htmlspecialchars($product_data['season']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="image">Product Image <?php echo $editing ? '(Leave empty to keep current image)' : '*'; ?></label>
                                    <input type="file" id="image" name="image" accept="image/*" <?php echo !$editing ? 'required' : ''; ?>>
                                    
                                    <?php if ($editing && !empty($product_data['image'])): ?>
                                        <div class="current-image">
                                            <p>Current image:</p>
                                            <img src="<?php echo SITE_URL; ?>uploads/<?php echo $product_data['image']; ?>" alt="Current product image" style="max-width: 100px;">
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description *</label>
                                <textarea id="description" name="description" rows="5" required><?php echo htmlspecialchars($product_data['description']); ?></textarea>
                            </div>
                            
                            <div class="form-group checkbox-group">
                                <input type="checkbox" id="featured" name="featured" value="1" <?php echo $product_data['featured'] ? 'checked' : ''; ?>>
                                <label for="featured">Featured Product</label>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo $editing ? 'Update Product' : 'Add Product'; ?>
                                </button>
                                <a href="<?php echo SITE_URL; ?>admin/products.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Show filename when file is selected
        document.getElementById('image').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name || 'No file chosen';
            const fileLabel = this.nextElementSibling;
            if (!fileLabel || fileLabel.className !== 'file-name') {
                const span = document.createElement('span');
                span.className = 'file-name';
                span.textContent = fileName;
                this.parentNode.appendChild(span);
            } else {
                fileLabel.textContent = fileName;
            }
        });
    </script>
</body>
</html>