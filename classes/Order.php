<?php
class Order {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create new order
     * @param int $user_id
     * @param array $items
     * @param float $total
     * @param string $shipping_address
     * @param string $payment_method
     * @param string $payment_id
     * @param string $razorpay_order_id
     * @return int|bool
     */
    public function create($user_id, $items, $total, $shipping_address, $payment_method, $payment_id = null, $razorpay_order_id = null) {
        $this->db->begin_transaction();
        
        try {
            // Insert order
            $stmt = $this->db->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, payment_id, razorpay_order_id, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->bind_param("idssss", $user_id, $total, $shipping_address, $payment_method, $payment_id, $razorpay_order_id);
            $stmt->execute();
            
            $order_id = $stmt->insert_id;
            
            // Insert order items
            $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity, size, price) VALUES (?, ?, ?, ?, ?)");
            
            foreach ($items as $item) {
                $stmt->bind_param("iiiss", $order_id, $item['product_id'], $item['quantity'], $item['size'], $item['price']);
                $stmt->execute();
            }
            
            $this->db->commit();
            return $order_id;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }
    
    /**
     * Get order by ID
     * @param int $id
     * @return array|bool
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        
        return false;
    }
    
    /**
     * Get order items
     * @param int $order_id
     * @return array
     */
    public function getOrderItems($order_id) {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name, p.image 
            FROM order_items oi 
            JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?
        ");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $items = array();
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        
        return $items;
    }
    
    /**
     * Get orders by user ID
     * @param int $user_id
     * @return array
     */
    public function getByUserId($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Get all orders (for admin)
     * @return array
     */
    public function getAllOrders() {
        $result = $this->db->query("
            SELECT o.*, u.username 
            FROM orders o 
            JOIN users u ON o.user_id = u.id 
            ORDER BY o.created_at DESC
        ");
        
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Update order status
     * @param int $order_id
     * @param string $status
     * @return bool
     */
    public function updateStatus($order_id, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
        return $stmt->execute();
    }
}
?>