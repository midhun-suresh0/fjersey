<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../classes/Database.php';

$db = Database::getInstance()->getConnection();
$email = 'admin@fjersey.com';

$stmt = $db->prepare('SELECT id, email, password, role FROM users WHERE email = ?');
if ($stmt === false) {
    echo json_encode(['error' => 'prepare_failed', 'db_error' => $db->error]);
    exit;
}
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['found' => false]);
    exit;
}
$row = $result->fetch_assoc();
$row['password_preview'] = substr($row['password'], 0, 10) . '...';
echo json_encode(['found' => true, 'user' => $row]);

?>