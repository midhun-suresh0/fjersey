<?php
$page_title = "Shopping Cart";
require_once '../includes/header.php';
require_once '../classes/Product.php';

// Initialize product class to get product details
$productObj = new Product();
?>

<!-- Cart Section -->
<section class="section">
    <div class="container">
        <h1>Shopping Cart</h1>
        
        <?php if ($cart->isEmpty()): ?>
            <div class="empty-cart">
                <p>Your cart is empty.</p>
                <a href="<?php echo SITE_URL; ?>public/shop.php" class="btn">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-container">
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Size</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $items = $cart->getItems();
                            foreach ($items as $item): 
                                $product = $productObj->getById($item['product_id']);
                                if (!$product) continue;

                                // Defensive defaults
                                $prod_image = isset($product['image']) ? $product['image'] : '';
                                $prod_name = isset($product['name']) ? $product['name'] : 'Product';
                                $prod_team = isset($product['team']) ? $product['team'] : '';
                                $prod_price = isset($product['price']) ? (float)$product['price'] : 0.0;
                                $prod_stock = isset($product['stock']) ? (int)$product['stock'] : 0;
                                $item_size = isset($item['size']) ? $item['size'] : '';
                                $item_qty = isset($item['quantity']) ? (int)$item['quantity'] : 1;
                                $item_id = isset($item['id']) ? $item['id'] : 0;
                            ?>
                                <tr>
                                    <td class="cart-product">
                                        <img src="<?php echo SITE_URL; ?>uploads/<?php echo htmlspecialchars($prod_image); ?>" alt="<?php echo htmlspecialchars($prod_name); ?>">
                                        <div>
                                            <h4><?php echo htmlspecialchars($prod_name); ?></h4>
                                            <p><?php echo htmlspecialchars($prod_team); ?></p>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($item_size); ?></td>
                                    <td>$<?php echo number_format($prod_price, 2); ?></td>
                                    <td>
                                        <form action="<?php echo SITE_URL; ?>public/cart_actions.php" method="POST" class="quantity-form">
                                            <input type="hidden" name="action" value="update">
                                            <input type="hidden" name="cart_id" value="<?php echo $item_id; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item_qty; ?>" min="1" max="<?php echo $prod_stock; ?>" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td>$<?php echo number_format($prod_price * $item_qty, 2); ?></td>
                                    <td>
                                        <form action="<?php echo SITE_URL; ?>public/cart_actions.php" method="POST">
                                            <input type="hidden" name="action" value="remove">
                                            <input type="hidden" name="cart_id" value="<?php echo $item_id; ?>">
                                            <button type="submit" class="btn-remove">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-item">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($cart->getTotal(), 2); ?></span>
                    </div>
                    <div class="summary-item">
                        <span>Shipping</span>
                        <span>$<?php echo number_format(10.00, 2); ?></span>
                    </div>
                    <div class="summary-item total">
                        <span>Total</span>
                        <span>$<?php echo number_format($cart->getTotal() + 10.00, 2); ?></span>
                    </div>
                    
                    <div class="cart-actions">
                        <a href="<?php echo SITE_URL; ?>public/shop.php" class="btn btn-secondary">Continue Shopping</a>
                        <a href="<?php echo SITE_URL; ?>public/checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>