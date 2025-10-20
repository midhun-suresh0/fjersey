<?php
require_once '../includes/header.php';
require_once '../classes/Product.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: " . SITE_URL . "public/shop.php");
    exit;
}

$product_id = (int)$_GET['id'];
$product = new Product();
$product_data = $product->getById($product_id);

// If product not found, redirect to shop
if (!$product_data) {
    header("Location: " . SITE_URL . "public/shop.php");
    exit;
}

$page_title = $product_data['name'];
?>

<!-- Product Detail Section -->
<section class="section">
    <div class="container">
        <div class="product-detail">
            <div class="product-detail-grid">
                <!-- Product Image -->
                <div class="product-detail-img">
                    <img src="<?php echo SITE_URL; ?>uploads/<?php echo $product_data['image']; ?>" alt="<?php echo $product_data['name']; ?>">
                </div>
                
                <!-- Product Info -->
                <div class="product-detail-info">
                    <h1><?php echo $product_data['name']; ?></h1>
                    <p class="product-team"><?php echo $product_data['team']; ?> | <?php echo $product_data['category']; ?></p>
                    <div class="product-price">$<?php echo number_format($product_data['price'], 2); ?></div>
                    
                    <div class="product-description">
                        <p><?php echo $product_data['description']; ?></p>
                    </div>
                    
                    <form action="<?php echo SITE_URL; ?>public/cart_actions.php" method="POST" class="product-form">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="product_id" value="<?php echo $product_data['id']; ?>">
                        
                        <div class="form-group">
                            <label for="size">Size:</label>
                            <select name="size" id="size" required>
                                <option value="S">Small</option>
                                <option value="M" selected>Medium</option>
                                <option value="L">Large</option>
                                <option value="XL">X-Large</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="quantity">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?php echo $product_data['stock']; ?>" required>
                            <span class="stock-info"><?php echo $product_data['stock']; ?> in stock</span>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Add to Cart</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
<section class="section" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="section-title">
            <h2>You May Also Like</h2>
        </div>
        
        <?php
        // Get products from the same team or category
        $related = $product->filterByCategory($product_data['category'], 4, $product_data['id']);
        
        if (count($related) > 0):
        ?>
        <div class="products">
            <?php foreach ($related as $item): ?>
                <div class="product-card">
                    <div class="product-img">
                        <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo $item['name']; ?></h3>
                        <p class="product-team"><?php echo $item['team']; ?></p>
                        <p class="product-price">$<?php echo number_format($item['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="<?php echo SITE_URL; ?>public/product_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-sm">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>
