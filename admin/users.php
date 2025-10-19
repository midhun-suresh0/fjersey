<?php
$page_title = "Manage Users";
require_once '../includes/config.php';
require_once '../classes/User.php';

// Initialize user and check if admin
$user = new User();
if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header("Location: " . SITE_URL . "public/login.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete']) && !empty($_GET['delete'])) {
    $user_id = (int)$_GET['delete'];
    
    // Don't allow deleting yourself
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot delete your own account";
    } else {
        $delete_result = $user->delete($user_id);
        
        if ($delete_result) {
            $_SESSION['success'] = "User deleted successfully";
        } else {
            $_SESSION['error'] = "Failed to delete user";
        }
    }
    
    // Redirect to remove the query string
    header("Location: " . SITE_URL . "admin/users.php");
    exit;
}

// Handle admin status toggle
if (isset($_GET['toggle_admin']) && !empty($_GET['toggle_admin'])) {
    $user_id = (int)$_GET['toggle_admin'];
    
    // Don't allow changing your own admin status
    if ($user_id == $_SESSION['user_id']) {
        $_SESSION['error'] = "You cannot change your own admin status";
    } else {
        $toggle_result = $user->toggleAdminStatus($user_id);
        
        if ($toggle_result) {
            $_SESSION['success'] = "User admin status updated successfully";
        } else {
            $_SESSION['error'] = "Failed to update user admin status";
        }
    }
    
    // Redirect to remove the query string
    header("Location: " . SITE_URL . "admin/users.php");
    exit;
}

// Get all users
$users = $user->getAll();
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
            
            <!-- Users Content -->
            <div class="users-management">
                <div class="page-header">
                    <h1>Manage Users</h1>
                </div>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="card">
                    <div class="card-body">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Registration Date</th>
                                    <th>Admin</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center;">No users found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $item): ?>
                                        <tr>
                                            <td><?php echo $item['id']; ?></td>
                                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                                            <td><?php echo htmlspecialchars($item['email']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($item['created_at'])); ?></td>
                                            <td>
                                                <?php if ($item['is_admin']): ?>
                                                    <span class="badge badge-admin">Admin</span>
                                                <?php else: ?>
                                                    <span class="badge badge-user">User</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <a href="<?php echo SITE_URL; ?>admin/user_detail.php?id=<?php echo $item['id']; ?>" class="btn btn-sm" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    
                                                    <?php if ($item['id'] != $_SESSION['user_id']): ?>
                                                        <a href="<?php echo SITE_URL; ?>admin/users.php?toggle_admin=<?php echo $item['id']; ?>" class="btn btn-sm <?php echo $item['is_admin'] ? 'btn-warning' : 'btn-success'; ?>" title="<?php echo $item['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>" onclick="return confirm('Are you sure you want to <?php echo $item['is_admin'] ? 'remove admin privileges from' : 'make admin'; ?> this user?');">
                                                            <i class="fas <?php echo $item['is_admin'] ? 'fa-user-minus' : 'fa-user-plus'; ?>"></i>
                                                        </a>
                                                        
                                                        <a href="<?php echo SITE_URL; ?>admin/users.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>