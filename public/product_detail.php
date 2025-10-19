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

<style>
    .product-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin-top: 20px;
    }
    
    .product-detail-img {
        position: relative;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .product-detail-img img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .product-detail-info {
        padding: 20px;
    }
    
    .product-detail-info h1 {
        margin: 0 0 10px;
        font-size: 2em;
        color: #2c3e50;
    }
    
    .product-team {
        color: #666;
        margin: 0 0 20px;
        font-size: 1.1em;
    }
    
    .product-price {
        font-size: 1.5em;
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 20px;
    }
    
    .product-description {
        margin: 20px 0;
        line-height: 1.6;
        color: #444;
    }
    
    .product-form {
        margin-top: 30px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .form-group select,
    .form-group input {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 1em;
    }
    
    .stock-info {
        display: block;
        margin-top: 5px;
        color: #666;
        font-size: 0.9em;
    }
    
    .btn-primary {
        width: 100%;
        padding: 12px;
        font-size: 1.1em;
    }
    
    /* Related Products */
    .section-title {
        margin-bottom: 30px;
        text-align: center;
    }
    
    .section-title h2 {
        font-size: 1.8em;
        color: #2c3e50;
    }
    
    .products {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .product-card {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .product-card:hover {
        transform: translateY(-5px);
    }
    
    .product-card .product-img {
        position: relative;
        padding-top: 100%;
        overflow: hidden;
        background: #f8f9fa;
    }
    
    .product-card .product-img img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-img img {
        transform: scale(1.05);
    }
    
    .product-card .product-info {
        padding: 15px;
    }
    
    .product-card .product-title {
        margin: 0 0 5px;
        font-size: 1.1em;
        font-weight: 600;
    }
    
    .product-card .product-team {
        color: #666;
        margin: 0 0 10px;
        font-size: 0.9em;
    }
    
    .product-card .product-price {
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 15px;
        font-size: 1.1em;
    }
    
    .product-card .product-actions {
        display: flex;
        gap: 10px;
    }
    
    .product-card .product-actions .btn {
        width: 100%;
        padding: 8px 15px;
        font-size: 0.9em;
    }
</style>

<!-- Product Detail Section -->
<section class="section">
    <div class="container">
        <div class="product-detail">
            <div class="product-detail-grid">
                <!-- Product Image -->
                <div class="product-detail-img">
                    <?php
                    $image_path = !empty($product_data['image']) ? SITE_URL . 'uploads/' . $product_data['image'] : SITE_URL . 'assets/images/placeholder.jpg';
                    ?>
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product_data['name']); ?>">
                </div>
                
                <!-- Product Info -->
                <div class="product-detail-info">
                    <h1><?php echo htmlspecialchars($product_data['name']); ?></h1>
                    <p class="product-team"><?php echo htmlspecialchars($product_data['team']); ?> | <?php echo htmlspecialchars($product_data['category']); ?></p>
                    <div class="product-price">$<?php echo number_format($product_data['price'], 2); ?></div>
                    
                    <div class="product-description">
                        <p><?php echo nl2br(htmlspecialchars($product_data['description'])); ?></p>
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
                        <?php
                        $image_path = !empty($item['image']) ? SITE_URL . 'uploads/' . $item['image'] : SITE_URL . 'assets/images/placeholder.jpg';
                        ?>
                        <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="product-team"><?php echo htmlspecialchars($item['team']); ?></p>
                        <p class="product-price">$<?php echo number_format($item['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="<?php echo SITE_URL; ?>public/product_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>


<style>
    /* Add this to existing styles */
    .product-img.no-image {
        background: linear-gradient(45deg, #f1f1f1 25%, #e9e9e9 25%, #e9e9e9 50%, #f1f1f1 50%, #f1f1f1 75%, #e9e9e9 75%, #e9e9e9 100%);
        background-size: 20px 20px;
        min-height: 400px;
        position: relative;
    }
    
    .product-img.no-image::after {
        content: 'No Image';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #666;
        font-size: 1.2em;
    }
</style>

<!-- Product Detail -->
<section class="section">
    <div class="container">
        <div class="product-detail" style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <!-- Product Image -->
            <?php if (!empty($product['image']) && file_exists('../uploads/' . $product['image'])): ?>
                <div class="product-img">
                    <img src="<?php echo SITE_URL; ?>uploads/<?php echo $product['image']; ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>"
                         style="width: 100%; height: auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                </div>
            <?php else: ?>
                <div class="product-img no-image"></div>
            <?php endif; ?>

            <!-- Product Info -->
            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p class="team" style="color: #666; font-size: 1.1em; margin: 10px 0;">
                    <?php echo htmlspecialchars($product['team']); ?>
                </p>
                <p class="price" style="font-size: 1.5em; font-weight: 600; color: #2c3e50; margin: 20px 0;">
                    $<?php echo number_format($product['price'], 2); ?>
                </p>
                <div class="description" style="margin: 20px 0;">
                    <h3>Description</h3>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>
                
                <form action="<?php echo SITE_URL; ?>public/cart_actions.php" method="POST" style="margin-top: 30px;">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    
                    <div style="margin-bottom: 20px;">
                        <label for="size" style="display: block; margin-bottom: 10px;">Size:</label>
                        <select name="size" id="size" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                            <option value="S">Small</option>
                            <option value="M" selected>Medium</option>
                            <option value="L">Large</option>
                            <option value="XL">Extra Large</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom: 20px;">
                        <label for="quantity" style="display: block; margin-bottom: 10px;">Quantity:</label>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" required
                               style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Related Products -->
        <?php if (!empty($related_products)): ?>
            <div style="margin-top: 50px;">
                <h2>Related Products</h2>
                <div class="products" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
                    <?php foreach ($related_products as $item): ?>
                        <div class="product-card">
                            <?php if (!empty($item['image']) && file_exists('../uploads/' . $item['image'])): ?>
                                <div class="product-img">
                                    <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image']; ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         style="width: 100%; height: auto; border-radius: 8px 8px 0 0;">
                                </div>
                            <?php else: ?>
                                <div class="product-img no-image" style="min-height: 200px;"></div>
                            <?php endif; ?>
                            <div style="padding: 15px;">
                                <h3 style="margin: 0 0 5px; font-size: 1.1em;">
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </h3>
                                <p style="color: #666; margin: 0 0 10px;">
                                    <?php echo htmlspecialchars($item['team']); ?>
                                </p>
                                <p style="font-weight: 600; color: #2c3e50; margin: 0 0 15px;">
                                    $<?php echo number_format($item['price'], 2); ?>
                                </p>
                                <a href="<?php echo SITE_URL; ?>public/product_detail.php?id=<?php echo $item['id']; ?>" 
                                   class="btn btn-sm btn-primary" style="width: 100%; text-align: center;">
                                    View Details
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>