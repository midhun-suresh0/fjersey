<?php
$page_title = "Shop";
require_once '../includes/header.php';
require_once '../classes/Product.php';

// Initialize product class
$product = new Product();

// Handle search and filters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';
$team = isset($_GET['team']) ? $_GET['team'] : '';

// Get products based on filters
if (!empty($search)) {
    $products = $product->search($search);
} elseif (!empty($category)) {
    $products = $product->filterByCategory($category);
} elseif (!empty($team)) {
    $products = $product->filterByTeam($team);
} else {
    $products = $product->getAll();
}
?>

<style>
    .products {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
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
    
    .product-img {
        position: relative;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        overflow: hidden;
        background: #f8f9fa;
    }
    
    .product-img img {
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
    
    .product-info {
        padding: 15px;
    }
    
    .product-title {
        margin: 0 0 5px;
        font-size: 1.1em;
        font-weight: 600;
    }
    
    .product-team {
        color: #666;
        margin: 0 0 10px;
        font-size: 0.9em;
    }
    
    .product-price {
        font-weight: 600;
        color: #2c3e50;
        margin: 0 0 15px;
    }
    
    .product-actions {
        display: flex;
        gap: 10px;
    }
    
    .product-actions .btn {
        flex: 1;
        padding: 8px 15px;
        font-size: 0.9em;
    }
</style>

<!-- Shop Banner -->
<section class="section" style="padding-top: 30px; padding-bottom: 30px; background-color: #f8f9fa;">
    <div class="container">
        <h1 style="margin-bottom: 0;">Shop Football Jerseys</h1>
    </div>
</section>

<!-- Shop Content -->
<section class="section">
    <div class="container">
        <div style="display: grid; grid-template-columns: 1fr 3fr; gap: 30px;">
            <!-- Sidebar Filters -->
            <div class="sidebar">
                <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px;">
                    <h3>Categories</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?category=Premier League">Premier League</a></li>
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?category=La Liga">La Liga</a></li>
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?category=Serie A">Serie A</a></li>
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?category=Bundesliga">Bundesliga</a></li>
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?category=Ligue 1">Ligue 1</a></li>
                    </ul>
                </div>
                
                <div style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <h3>Popular Teams</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?team=Manchester United">Manchester United</a></li>
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?team=Barcelona">Barcelona</a></li>
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?team=Real Madrid">Real Madrid</a></li>
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?team=Liverpool">Liverpool</a></li>
                        <li style="margin-bottom: 10px;"><a href="<?php echo SITE_URL; ?>public/shop.php?team=Bayern Munich">Bayern Munich</a></li>
                    </ul>
                </div>
            </div>
            
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
            
            <!-- Products Grid -->
            <div>
                <?php if (!empty($search)): ?>
                    <p>Search results for: <strong><?php echo htmlspecialchars($search); ?></strong></p>
                <?php elseif (!empty($category)): ?>
                    <p>Category: <strong><?php echo htmlspecialchars($category); ?></strong></p>
                <?php elseif (!empty($team)): ?>
                    <p>Team: <strong><?php echo htmlspecialchars($team); ?></strong></p>
                <?php endif; ?>
                
                <?php if (empty($products)): ?>
                    <p>No products found.</p>
                <?php else: ?>
                    <div class="products">
                        <?php foreach ($products as $item): ?>
                            <div class="product-card">
                                <?php if (!empty($item['image']) && file_exists('../uploads/' . $item['image'])): ?>
                                    <div class="product-img">
                                        <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image']; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </div>
                                <?php else: ?>
                                    <div class="product-img no-image"></div>
                                <?php endif; ?>
                                <div class="product-info">
                                    <h3 class="product-title"><?php echo htmlspecialchars($item['name']); ?></h3>
                                    <p class="product-team"><?php echo htmlspecialchars($item['team']); ?></p>
                                    <p class="product-price">$<?php echo number_format($item['price'], 2); ?></p>
                                    <div class="product-actions">
                                        <a href="<?php echo SITE_URL; ?>public/product_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-sm">View Details</a>
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>