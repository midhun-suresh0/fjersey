<?php
$page_title = "Admin Login";
require_once '../includes/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Admin.php';

// Initialize user and admin objects
$user = new User();
$admin = new Admin();

// Check if user is already logged in as admin
if ($user->isLoggedIn() && $user->isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        // Use Admin::login to authenticate admin users only
        $login_result = $admin->login($email, $password);

        if ($login_result) {
            // Redirect to admin dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid admin credentials";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-form">
            <h1>Admin Login</h1>
            <div class="logo">
                <h2><?php echo SITE_NAME; ?></h2>
                <p>Admin Panel</p>
            </div>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            
            <div class="login-footer">
                <a href="<?php echo SITE_URL; ?>public/index.php">Return to Store</a>
            </div>
        </div>
    </div>
</body>
</html>