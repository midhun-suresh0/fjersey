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

<!-- Featured Products -->
<section class="section">
    <div class="container">
        <div class="section-title">
            <h2>Featured Jerseys</h2>
        </div>
        
        <div class="products">
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-img">
                        <img src="<?php echo SITE_URL; ?>uploads/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo $product['name']; ?></h3>
                        <p class="product-team"><?php echo $product['team']; ?></p>
                        <p class="product-price">$<?php echo number_format($product['price'], 2); ?></p>
                        <div class="product-actions">
                            <a href="<?php echo SITE_URL; ?>public/product_detail.php?id=<?php echo $product['id']; ?>" class="btn btn-sm">View Details</a>
                            <form action="<?php echo SITE_URL; ?>public/cart_actions.php" method="POST">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="size" value="M">
                                <button type="submit" class="btn btn-sm">Add to Cart</button>
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