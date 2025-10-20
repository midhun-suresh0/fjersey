<?php
session_start();
require_once '../config/database.php';
require_once '../classes/User.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

try {
    // Get user input
    $address_data = array(
        'phone' => $_POST['phone'] ?? '',
        'address' => $_POST['address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'state' => $_POST['state'] ?? '',
        'pincode' => $_POST['pincode'] ?? ''
    );
    
    // Validate required fields
    foreach ($address_data as $key => $value) {
        if (empty($value)) {
            echo json_encode(['success' => false, 'message' => ucfirst($key) . ' is required']);
            exit;
        }
    }
    
    // Update address
    $user = new User();
    if ($user->updateAddress($address_data)) {
        echo json_encode(['success' => true, 'message' => 'Address updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update address']);
    }
} catch (Exception $e) {
    error_log('Address update error: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred while updating the address']);
}
?>