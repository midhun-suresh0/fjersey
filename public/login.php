<?php
$page_title = "Login";
require_once '../includes/header.php';

// Check if user is already logged in
if ($user->isLoggedIn()) {
    header("Location: " . SITE_URL . "public/index.php");
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password";
    } else {
        $login_result = $user->login($email, $password);
        
        if ($login_result) {
            // Redirect based on user role
            if ($user->isAdmin()) {
                header("Location: " . SITE_URL . "admin/dashboard.php");
            } else {
                // Redirect to previous page or home
                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : SITE_URL . "public/index.php";
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
            }
            exit;
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!-- Login Section -->
<section class="section">
    <div class="container">
        <div class="auth-form">
            <h1>Login to Your Account</h1>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo SITE_URL; ?>public/login.php" method="POST">
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
            
            <div class="auth-links">
                <p>Don't have an account? <a href="<?php echo SITE_URL; ?>public/register.php">Register</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once '../includes/footer.php'; ?>