<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Product.php';

/**
 * Cart Class
 * Handles shopping cart operations
 */
class Cart {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        
        // Initialize cart session if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = array();
        }
    }
    
    /**
     * Add item to cart
     * @param int $product_id
     * @param int $quantity
     * @param string $size
     * @return bool
     */
    public function add($product_id, $quantity = 1, $size = 'M') {
        // Check if product exists
        $product = new Product();
        $product_info = $product->getById($product_id);
        
        if (!$product_info) {
            return false;
        }
        
        // Check if product is already in cart
        $item_key = $this->findItem($product_id, $size);
        
        if ($item_key !== false) {
            // Update quantity
            $_SESSION['cart'][$item_key]['quantity'] += $quantity;
        } else {
            // Add new item
            $_SESSION['cart'][] = array(
                'product_id' => $product_id,
                'name' => $product_info['name'],
                'price' => $product_info['price'],
                'quantity' => $quantity,
                'size' => $size,
                'image' => $product_info['image']
            );
        }
        
        return true;
    }
    
    /**
     * Update cart item quantity
     * @param int $index
     * @param int $quantity
     * @return bool
     */
    public function update($index, $quantity) {
        if (isset($_SESSION['cart'][$index])) {
            if ($quantity <= 0) {
                // Remove item if quantity is 0 or less
                $this->remove($index);
            } else {
                $_SESSION['cart'][$index]['quantity'] = $quantity;
            }
            return true;
        }
        return false;
    }
    
    /**
     * Remove item from cart
     * @param int $index
     * @return bool
     */
    public function remove($index) {
        if (isset($_SESSION['cart'][$index])) {
            unset($_SESSION['cart'][$index]);
            // Reindex array
            $_SESSION['cart'] = array_values($_SESSION['cart']);
            return true;
        }
        return false;
    }
    
    /**
     * Clear cart
     */
    public function clear() {
        $_SESSION['cart'] = array();
    }
    
    /**
     * Get cart items
     * @return array
     */
    public function getItems() {
        return isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
    }
    
    /**
     * Get cart total
     * @return float
     */
    public function getTotal() {
        $total = 0;
        
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    /**
     * Get cart item count
     * @return int
     */
    public function getItemCount() {
        $count = 0;
        
        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $count += $item['quantity'];
            }
        }
        
        return $count;
    }
    
    /**
     * Find item in cart
     * @param int $product_id
     * @param string $size
     * @return int|bool
     */
    private function findItem($product_id, $size) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['product_id'] == $product_id && $item['size'] == $size) {
                return $key;
            }
        }
        return false;
    }
    
    /**
     * Check if cart is empty
     * @return bool
     */
    public function isEmpty() {
        return empty($_SESSION['cart']);
    }
    
    /**
     * Save cart to database (for logged in users)
     * @param int $user_id
     * @return bool
     */
    public function saveToDb($user_id) {
        // First clear existing cart items for this user
        $stmt = $this->db->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        // Insert new cart items
        foreach ($_SESSION['cart'] as $item) {
            $stmt = $this->db->prepare("INSERT INTO cart_items (user_id, product_id, quantity, size) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $user_id, $item['product_id'], $item['quantity'], $item['size']);
            $stmt->execute();
        }
        
        return true;
    }
    
    /**
     * Load cart from database (for logged in users)
     * @param int $user_id
     * @return bool
     */
    public function loadFromDb($user_id) {
        $stmt = $this->db->prepare("SELECT c.*, p.name, p.price, p.image FROM cart_items c 
                                   JOIN products p ON c.product_id = p.id 
                                   WHERE c.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Clear current cart
        $this->clear();
        
        // Load items from database
        while ($row = $result->fetch_assoc()) {
            $_SESSION['cart'][] = array(
                'product_id' => $row['product_id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'quantity' => $row['quantity'],
                'size' => $row['size'],
                'image' => $row['image']
            );
        }
        
        return true;
    }
}
?>