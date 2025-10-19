<?php
$page_title = "Home";
require_once '../includes/header.php';
require_once '../classes/Product.php';

// Get featured products
$product = new Product();
$featured_products = $product->getFeatured(8);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Welcome to Football Jersey Store</h1>
            <p>Find authentic jerseys from your favorite teams around the world</p>
            <a href="<?php echo SITE_URL; ?>public/shop.php" class="btn">Shop Now</a>
        </div>
    </div>
</section>

<style>
    /* Add this to existing styles */
    .product-img.no-image {
        background: linear-gradient(45deg, #f1f1f1 25%, #e9e9e9 25%, #e9e9e9 50%, #f1f1f1 50%, #f1f1f1 75%, #e9e9e9 75%, #e9e9e9 100%);
        background-size: 20px 20px;
    }
    
    .product-img.no-image::after {
        content: 'No Image';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #666;
        font-size: 0.9em;
    }
</style>

<!-- Featured Products -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Featured Products</h2>
        <div class="products">
            <?php foreach ($featured_products as $item): ?>
                <div class="product-card">
                    <?php if (!empty($item['image']) && file_exists('../uploads/' . $item['image'])): ?>
                        <div class="product-img">
                            <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="product-img no-image"></div>
                    <?php endif; ?>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="product-team"><?php echo htmlspecialchars($item['team']); ?></p>
                        <p class="product-price">$<?php echo number_format($item['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="<?php echo SITE_URL; ?>public/product_detail.php?id=<?php echo $item['id']; ?>" 
                               class="btn btn-sm">View Details</a>
                            <form action="<?php echo SITE_URL; ?>public/cart_actions.php" method="POST" style="flex: 1;">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="size" value="M">
                                <button type="submit" class="btn btn-sm btn-primary" style="width: 100%;">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Latest Products -->
<section class="section" style="background-color: #f8f9fa;">
    <div class="container">
        <h2 class="section-title">Latest Products</h2>
        <div class="products">
            <?php foreach ($latest_products as $item): ?>
                <div class="product-card">
                    <?php if (!empty($item['image']) && file_exists('../uploads/' . $item['image'])): ?>
                        <div class="product-img">
                            <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image']; ?>" 
                                 alt="<?php echo htmlspecialchars($item['name']); ?>">
                        </div>
                    <?php else: ?>
                        <div class="product-img no-image"></div>
                    <?php endif; ?>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="product-team"><?php echo htmlspecialchars($item['team']); ?></p>
                        <p class="product-price">$<?php echo number_format($item['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="<?php echo SITE_URL; ?>public/product_detail.php?id=<?php echo $item['id']; ?>" 
                               class="btn btn-sm">View Details</a>
                            <form action="<?php echo SITE_URL; ?>public/cart_actions.php" method="POST" style="flex: 1;">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="size" value="M">
                                <button type="submit" class="btn btn-sm btn-primary" style="width: 100%;">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section" style="background-color: #f0f0f0;">
    <div class="container">
        <div class="section-title">
            <h2>Shop by League</h2>
        </div>
        
        <div class="categories" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; text-align: center;">
            <div class="category-card" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>Premier League</h3>
                <a href="<?php echo SITE_URL; ?>public/shop.php?category=Premier League" class="btn btn-sm">Shop Now</a>
            </div>
            <div class="category-card" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>La Liga</h3>
                <a href="<?php echo SITE_URL; ?>public/shop.php?category=La Liga" class="btn btn-sm">Shop Now</a>
            </div>
            <div class="category-card" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>Serie A</h3>
                <a href="<?php echo SITE_URL; ?>public/shop.php?category=Serie A" class="btn btn-sm">Shop Now</a>
            </div>
            <div class="category-card" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>Bundesliga</h3>
                <a href="<?php echo SITE_URL; ?>public/shop.php?category=Bundesliga" class="btn btn-sm">Shop Now</a>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>