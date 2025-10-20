<?php
require_once '../includes/config.php';
require_once '../classes/User.php';

// Initialize user object
$user = new User();

// Call logout method
$user->logout();

// Redirect to home page
header("Location: " . SITE_URL . "public/index.php");
exit;
?>