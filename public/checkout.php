<?php
$page_title = "Checkout";
require_once '../includes/header.php';
require_once '../includes/razorpay_config.php';
require_once '../vendor/autoload.php';
require_once '../classes/Cart.php';
require_once '../classes/Order.php';

use Razorpay\Api\Api;

// Check if user is logged in
if (!$user->isLoggedIn()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Get user data
$userData = $user->getUserData();

// Initialize cart
$cart = new Cart();
$cartItems = $cart->getItems();

// Check if cart is empty
if (empty($cartItems)) {
    header("Location: " . SITE_URL . "public/cart.php");
    exit;
}

// Calculate total amount
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
$total += 10.00; // Add shipping cost

// Initialize Razorpay API
$api = new Api(RAZORPAY_KEY_ID, RAZORPAY_KEY_SECRET);

// Create Razorpay Order
$orderData = [
    'receipt'         => 'rcpt_' . time(),
    'amount'          => $total * 100, // Amount in smallest currency unit (cents)
    'currency'        => RAZORPAY_CURRENCY,
    'payment_capture' => 1 // Auto capture payment
];

$razorpayOrder = $api->order->create($orderData);
$razorpayOrderId = $razorpayOrder['id'];

// Store order data in session for verification
$_SESSION['razorpay_order_id'] = $razorpayOrderId;
$_SESSION['checkout_amount'] = $total;
?>

<!-- Checkout Section -->
<section class="section">
    <div class="container">
        <div class="checkout-container">
            <h1>Checkout</h1>
            
            <!-- Customer Information -->
            <div class="customer-info">
                <h2>Customer Information</h2>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($userData['name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
            </div>

            <!-- Shipping Form -->
            <form id="checkout-form" method="POST" action="process_payment.php">
                <input type="hidden" name="razorpay_order_id" value="<?php echo $razorpayOrderId; ?>">
                
                <div class="shipping-info">
                    <h2>Shipping Information</h2>
                    <div class="form-group">
                        <label for="shipping_address">Shipping Address</label>
                        <textarea name="shipping_address" id="shipping_address" rows="4" required></textarea>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div class="cart-items">
                        <?php foreach ($cartItems as $item): ?>
                            <div class="cart-item">
                                <div class="item-info">
                                    <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
                                    <div>
                                        <h4><?php echo $item['name']; ?></h4>
                                        <p>Size: <?php echo $item['size']; ?> | Qty: <?php echo $item['quantity']; ?></p>
                                    </div>
                                </div>
                                <div class="item-price">
                                    $<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-totals">
                        <div class="total-row">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($total - 10.00, 2); ?></span>
                        </div>
                        <div class="total-row">
                            <span>Shipping</span>
                            <span>$10.00</span>
                        </div>
                        <div class="total-row grand-total">
                            <span>Total</span>
                            <span>$<?php echo number_format($total, 2); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="payment-section">
                    <h2>Payment Method</h2>
                    <button type="button" id="razorpay-button" class="btn btn-primary">Pay with Razorpay</button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Razorpay Integration -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
var options = {
    "key": "<?php echo RAZORPAY_KEY_ID; ?>",
    "amount": "<?php echo $total * 100; ?>",
    "currency": "<?php echo RAZORPAY_CURRENCY; ?>",
    "name": "F Jersey",
    "description": "Jersey Order Payment",
    "image": "<?php echo SITE_URL; ?>assets/images/logo.png",
    "order_id": "<?php echo $razorpayOrderId; ?>",
    "handler": function (response) {
        // Add payment details to form
        var form = document.getElementById('checkout-form');
        
        var paymentIdInput = document.createElement('input');
        paymentIdInput.type = 'hidden';
        paymentIdInput.name = 'razorpay_payment_id';
        paymentIdInput.value = response.razorpay_payment_id;
        form.appendChild(paymentIdInput);
        
        var signatureInput = document.createElement('input');
        signatureInput.type = 'hidden';
        signatureInput.name = 'razorpay_signature';
        signatureInput.value = response.razorpay_signature;
        form.appendChild(signatureInput);
        
        // Submit the form
        form.submit();
    },
    "prefill": {
        "name": "<?php echo htmlspecialchars($userData['name']); ?>",
        "email": "<?php echo htmlspecialchars($userData['email']); ?>"
    },
    "theme": {
        "color": "#3399cc"
    }
};

document.getElementById('razorpay-button').onclick = function(e) {
    // Validate shipping address
    var shippingAddress = document.getElementById('shipping_address').value;
    if (!shippingAddress.trim()) {
        alert('Please enter your shipping address');
        return false;
    }
    
    var rzp1 = new Razorpay(options);
    rzp1.open();
    e.preventDefault();
}
</script>

<?php require_once '../includes/footer.php'; ?>