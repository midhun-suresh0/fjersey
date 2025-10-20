<?php
// Reset admin password to a known value (only run locally)
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Database.php';

$db = Database::getInstance()->getConnection();
$email = 'admin@fjersey.com';
$newPassword = 'admin123';
$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $db->prepare('UPDATE users SET password = ? WHERE email = ?');
if ($stmt === false) {
    echo json_encode(['success' => false, 'error' => 'prepare_failed', 'db_error' => $db->error]);
    exit;
}
$stmt->bind_param('ss', $newHash, $email);
$ok = $stmt->execute();

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Admin password updated to "admin123" (hashed)']);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}

?>