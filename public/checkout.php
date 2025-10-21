<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/razorpay_config.php';
require_once '../classes/User.php';
require_once '../classes/Cart.php';
require_once '../classes/Product.php';
require_once '../vendor/autoload.php';

use Razorpay\Api\Api;

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize classes
$user = new User();
$cart = new Cart();

// Get user details
$user_details = $user->getUserData();

// Calculate cart total
$cart_total = 0;
$cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

foreach ($cart_items as $item) {
    $product = new Product();
    $product_details = $product->getById($item['product_id']);
    $cart_total += $product_details['price'] * $item['quantity'];
}

// Add shipping fee
$total_amount = $cart_total + 10.00;

// Initialize Razorpay API
$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

// Create Razorpay order
$order_data = [
    'receipt' => 'order_' . time(),
    'amount' => $total_amount * 100, // Convert to smallest currency unit (paise)
    'currency' => RAZORPAY_CURRENCY,
    'payment_capture' => 1
];

$razorpay_order = $api->order->create($order_data);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FJersey</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Checkout</h1>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="checkout-container">
            <div class="shipping-details">
                <h2>Shipping Details</h2>
                <form id="addressForm" method="post">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_details['name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_details['email']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user_details['phone'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" required><?php echo htmlspecialchars($user_details['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($user_details['city'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="state">State:</label>
                        <input type="text" id="state" name="state" value="<?php echo htmlspecialchars($user_details['state'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="pincode">Pincode:</label>
                        <input type="text" id="pincode" name="pincode" value="<?php echo htmlspecialchars($user_details['pincode'] ?? ''); ?>" required>
                    </div>
                    
                </form>
            </div>
            
            <div class="order-summary">
                <h2>Order Summary</h2>
                <div class="cart-items">
                    <?php foreach ($cart_items as $item): 
                        $product = new Product();
                        $product_details = $product->getById($item['product_id']);
                    ?>
                        <div class="cart-item">
                            <img src="<?php echo SITE_URL; ?>uploads/<?php echo htmlspecialchars($product_details['image']); ?>" alt="<?php echo htmlspecialchars($product_details['name']); ?>">
                            <div class="item-details">
                                <h3><?php echo htmlspecialchars($product_details['name']); ?></h3>
                                <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                                <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                                <p>Price: ₹<?php echo number_format($product_details['price'] * $item['quantity'], 2); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="order-total">
                    <p>Subtotal: ₹<?php echo number_format($cart_total, 2); ?></p>
                    <p>Shipping: ₹10.00</p>
                    <h3>Total: ₹<?php echo number_format($total_amount, 2); ?></h3>
                </div>
                
                <button id="placeOrder" class="btn btn-primary">Place Order</button>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
    document.getElementById('addressForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('update_address.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Address saved successfully!');
            } else {
                alert('Error saving address: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the address');
        });
    });
    
    document.getElementById('placeOrder').addEventListener('click', function() {
        // Get the form data
        const formData = new FormData(document.getElementById('addressForm'));
        
        // Validate form
        if (!document.getElementById('addressForm').checkValidity()) {
            alert('Please fill in all required fields');
            return;
        }
        
        // Create Razorpay options
        var options = {
            "key": "<?php echo RAZORPAY_KEY_ID; ?>",
            "amount": "<?php echo $total_amount * 100; ?>",
            "currency": "<?php echo RAZORPAY_CURRENCY; ?>",
            "name": "FJersey",
            "description": "Jersey Purchase",
            "image": "images/logo.png",
            "order_id": "<?php echo $razorpay_order['id']; ?>",
            "handler": function (response) {
                // Show loading message
                document.body.style.cursor = 'wait';
                
                // Send payment details to server
                // Prepare POST parameters including address form data to help server validation
                const params = new URLSearchParams();
                params.append('razorpay_payment_id', response.razorpay_payment_id);
                params.append('razorpay_order_id', response.razorpay_order_id);
                params.append('razorpay_signature', response.razorpay_signature);
                // append address form fields so server can validate/store them
                for (const [key, value] of formData.entries()) {
                    params.append(key, value);
                }

                fetch('process_payment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'Accept': 'application/json, text/plain, */*'
                    },
                    body: params
                })
                .then(async response => {
                    const text = await response.text();
                    let data = null;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        // response is not JSON
                    }
                    if (!response.ok) {
                        console.error('Server returned error', response.status, text);
                        const message = data && data.message ? data.message : text || 'Server error';
                        alert('Server error (' + response.status + '): ' + message);
                        throw new Error('Server response not ok: ' + response.status);
                    }
                    return data || {};
                })
                .then(data => {
                    if (data.success) {
                        // Redirect to order confirmation page
                        window.location.href = data.data.redirect_url;
                    } else {
                        const msg = data && data.message ? data.message : 'Payment failed';
                        alert('Payment failed: ' + msg);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your payment. Please try again.');
                })
                .finally(() => {
                    document.body.style.cursor = 'default';
                });
            },
            "prefill": {
                "name": "<?php echo htmlspecialchars($user_details['name']); ?>",
                "email": "<?php echo htmlspecialchars($user_details['email']); ?>",
                "contact": "<?php echo htmlspecialchars($user_details['phone'] ?? ''); ?>"
            },
            "theme": {
                "color": "#3399cc"
            }
        };
        
        var rzp1 = new Razorpay(options);
        rzp1.open();
    });
    </script>
</body>
</html>