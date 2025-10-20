<?php
require_once '../includes/config.php';
require_once '../classes/User.php';
require_once '../classes/Order.php';

$user = new User();

// Redirect to login if not logged in
if (!$user->isLoggedIn()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Get user data and orders
$user_data = $user->getUserData();
$orderObj = new Order();
$orders = $orderObj->getByUserId($user_data['id']);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account - FJersey</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <section class="section">
        <div class="container">
            <h1>My Account</h1>

            <div class="account-grid">
                <div class="account-card">
                    <h2>Profile</h2>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($user_data['name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($user_data['phone']); ?></p>
                    <p><strong>Address:</strong> <?php echo nl2br(htmlspecialchars($user_data['address'])); ?></p>
                    <p><a href="<?php echo SITE_URL; ?>public/update_address.php" class="btn">Update Address</a></p>
                </div>

                <div class="account-card">
                    <h2>My Orders</h2>
                    <?php if (!empty($orders)): ?>
                        <ul class="orders-list">
                            <?php foreach ($orders as $o): ?>
                                <li>
                                    <a href="<?php echo SITE_URL; ?>public/order_confirmation.php?order_id=<?php echo (int)$o['id']; ?>">Order #<?php echo (int)$o['id']; ?></a>
                                    &mdash; <?php echo htmlspecialchars($o['status']); ?> &mdash; ₹<?php echo number_format($o['total_amount'], 2); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>You have no orders yet.</p>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
</body>
</html>
