<?php
$page_title = "Settings";
require_once '../includes/config.php';
require_once '../classes/User.php';

// Initialize user and check if admin
$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update site settings
    if (isset($_POST['update_settings'])) {
        // In a real application, you would save these to a settings table in the database
        // For this demo, we'll just show a success message
        $success_message = "Settings updated successfully";
    }
    
    // Update admin password
    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        // Check if current password is correct
        if (!$user->verifyPassword($current_password)) {
            $error_message = "Current password is incorrect";
        } 
        // Check if new passwords match
        elseif ($new_password !== $confirm_password) {
            $error_message = "New passwords do not match";
        } 
        // Check password length
        elseif (strlen($new_password) < 6) {
            $error_message = "Password must be at least 6 characters long";
        } 
        // Update password
        else {
            $result = $user->updatePassword($new_password);
            if ($result) {
                $success_message = "Password updated successfully";
            } else {
                $error_message = "Failed to update password";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Football Jersey Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>admin/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Admin Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="admin-content">
            <!-- Admin Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Settings Content -->
            <div class="settings-page">
                <div class="page-header">
                    <h1>Settings</h1>
                </div>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-error">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>
                
                <div class="settings-grid">
                    <!-- Site Settings -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Site Settings</h2>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="site_name">Site Name</label>
                                    <input type="text" id="site_name" name="site_name" value="Football Jersey Store" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_email">Contact Email</label>
                                    <input type="email" id="site_email" name="site_email" value="contact@fjersey.com" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="site_phone">Contact Phone</label>
                                    <input type="text" id="site_phone" name="site_phone" value="+1 (555) 123-4567">
                                </div>
                                
                                <div class="form-group">
                                    <label for="shipping_fee">Shipping Fee ($)</label>
                                    <input type="number" id="shipping_fee" name="shipping_fee" value="10.00" step="0.01" min="0" required>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="update_settings" class="btn btn-primary">Save Settings</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Change Password -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Change Password</h2>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="update_password" class="btn btn-primary">Update Password</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>