<?php
// Quick test script to validate admin login using classes/Admin.php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Admin.php';

// Use test credentials
$email = 'admin@fjersey.com';
$password = 'admin123';

$admin = new Admin();
$ok = $admin->login($email, $password);

echo json_encode([
    'email' => $email,
    'success' => $ok,
    'session_user_id' => isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
    'session_role' => isset($_SESSION['role']) ? $_SESSION['role'] : null,
    'db_error' => (isset($admin) && method_exists($admin, 'getDbError')) ? $admin->getDbError() : null
]);

?>