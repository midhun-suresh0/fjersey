<?php
require_once __DIR__ . '/Database.php';

/**
 * User Class
 * Handles user registration, authentication, and profile management
 */
class User {
    private $db;
    private $id;
    private $username;
    private $email;
    private $password;
    private $role;
    private $created_at;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Register new user
     * @param string $username
     * @param string $email
     * @param string $password
     * @param string $role
     * @return bool
     */
    public function register($username, $email, $password, $role = 'customer') {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare statement
        $stmt = $this->db->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);
        
        // Execute query
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Login user
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password) {
        // Prepare statement
        $stmt = $this->db->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        
        // Execute query
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $row['password'])) {
                // Password is correct, set user properties
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                $this->role = $row['role'];
                
                // Create session
                $_SESSION['user_id'] = $this->id;
                $_SESSION['username'] = $this->username;
                $_SESSION['email'] = $this->email;
                $_SESSION['role'] = $this->role;
                
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user is admin
     * @return bool
     */
    public function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
    }
    
    /**
     * Get user by ID
     * @param int $id
     * @return array|bool
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT id, username, email, role, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }
    
    /**
     * Update user profile
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateProfile($id, $data) {
        $username = $data['username'];
        $email = $data['email'];
        
        $stmt = $this->db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Change password
     * @param int $id
     * @param string $current_password
     * @param string $new_password
     * @return bool
     */
    public function changePassword($id, $current_password, $new_password) {
        // Get current password
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            // Verify current password
            if (password_verify($current_password, $row['password'])) {
                // Hash new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                // Update password
                $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $id);
                
                if ($stmt->execute()) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Get current user's data
     * @return array|bool
     */
    public function getUserData() {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        // Get additional user data from database
        $stmt = $this->db->prepare("SELECT phone, address, city, state, pincode FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $additional_data = $result->fetch_assoc();
        
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'role' => $_SESSION['role'],
            'phone' => $additional_data['phone'] ?? '',
            'address' => $additional_data['address'] ?? '',
            'city' => $additional_data['city'] ?? '',
            'state' => $additional_data['state'] ?? '',
            'pincode' => $additional_data['pincode'] ?? ''
        ];
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Unset all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
    }
    
    /**
     * Get all users (for admin)
     * @return array
     */
    public function getAllUsers() {
        $result = $this->db->query("SELECT id, username, email, role, created_at FROM users ORDER BY id DESC");
        $users = array();
        
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    /**
     * Update user address
     * @param array $data
     * @return bool
     */
    public function updateAddress($data) {
        if (!$this->isLoggedIn()) {
            return false;
        }
        
        $stmt = $this->db->prepare("UPDATE users SET phone = ?, address = ?, city = ?, state = ?, pincode = ? WHERE id = ?");
        $stmt->bind_param("sssssi", 
            $data['phone'],
            $data['address'],
            $data['city'],
            $data['state'],
            $data['pincode'],
            $_SESSION['user_id']
        );
        
        return $stmt->execute();
    }
}
?>