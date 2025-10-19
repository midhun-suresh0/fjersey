<?php
$page_title = "My Profile";
require_once '../includes/config.php';
require_once '../classes/User.php';

// Initialize user and check if admin
$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Get user data
$user_data = $user->getUserData();

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update profile
    if (isset($_POST['update_profile'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format";
        } else {
            $update_data = [
                'id' => $user_data['id'],
                'name' => $name,
                'email' => $email,
                'phone' => $phone
            ];
            
            $result = $user->update($update_data);
            if ($result) {
                $success_message = "Profile updated successfully";
                // Refresh user data
                $user_data = $user->getUserData();
            } else {
                $error_message = "Failed to update profile";
            }
        }
    }
    
    // Update password
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
            
            <!-- Profile Content -->
            <div class="profile-page">
                <div class="page-header">
                    <h1>My Profile</h1>
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
                
                <div class="profile-grid">
                    <!-- Profile Information -->
                    <div class="card">
                        <div class="card-header">
                            <h2>Profile Information</h2>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Role</label>
                                    <div class="static-value">
                                        <span class="badge badge-admin">Administrator</span>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Registration Date</label>
                                    <div class="static-value">
                                        <?php echo date('F d, Y', strtotime($user_data['created_at'])); ?>
                                    </div>
                                </div>
                                
                                <div class="form-actions">
                                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
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
    
    <script src="<?php echo SITE_URL; ?>admin/js/admin.js"></script>
</body>
</html>