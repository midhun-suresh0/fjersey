<div class="admin-header">
    <div class="page-title">
        <h2><?php echo isset($page_title) ? $page_title : 'Admin Panel'; ?></h2>
    </div>
    
    <div class="user-dropdown">
        <div class="user-info">
            <span class="user-name"><?php echo $user->getUserData()['name']; ?></span>
            <i class="fas fa-chevron-down"></i>
        </div>
        <div class="dropdown-menu">
            <a href="<?php echo SITE_URL; ?>admin/profile.php">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="<?php echo SITE_URL; ?>public/logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>