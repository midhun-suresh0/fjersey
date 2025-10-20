<?php
/**
 * Order Class
 * Handles order placement and tracking
 */
class Order {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Create new order
     * @param int $user_id
     * @param array $order_data
     * @param array $cart_items
     * @return int|bool
     */
    public function create($user_id, $order_data, $cart_items) {
        // Start transaction
        $this->db->begin_transaction();
        
        try {
            // Calculate total amount
            $total_amount = 0;
            foreach ($cart_items as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }
            $total_amount += 10.00; // Add shipping fee
            
            // Ensure shipping fields exist and provide sensible defaults.
            // Accept both plain keys (address) and prefixed keys (shipping_address)
            $shipping_address = '';
            if (isset($order_data['shipping_address'])) {
                $shipping_address = $order_data['shipping_address'];
            } elseif (isset($order_data['address'])) {
                $shipping_address = $order_data['address'];
            }

            // billing_address: if not provided, reuse shipping_address
            $billing_address = isset($order_data['billing_address']) ? $order_data['billing_address'] : $shipping_address;

            // payment method: default to 'online' or use provided
            $payment_method = isset($order_data['payment_method']) ? $order_data['payment_method'] : 'Razorpay';

            // Insert order with fields matching the database schema
            $stmt = $this->db->prepare(
                "INSERT INTO orders (
                    user_id, total_amount, shipping_address, billing_address, payment_method, status
                ) VALUES (?, ?, ?, ?, ?, 'pending')"
            );
            if ($stmt === false) {
                throw new Exception('Prepare failed for orders insert: ' . $this->db->error);
            }
            $stmt->bind_param(
                "idsss",
                $user_id,
                $total_amount,
                $shipping_address,
                $billing_address,
                $payment_method
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create order: " . $stmt->error);
            }
            
            $order_id = $this->db->insert_id;
            
            // Insert order items
            // order_items table schema: (order_id, product_id, quantity, price, size)
            $stmt = $this->db->prepare(
                "INSERT INTO order_items (
                    order_id, product_id, quantity, price, size
                ) VALUES (?, ?, ?, ?, ?)"
            );
            if ($stmt === false) {
                throw new Exception('Prepare failed for order_items insert: ' . $this->db->error);
            }
            
            foreach ($cart_items as $item) {
                // Ensure optional fields exist on cart item
                $product_id = isset($item['product_id']) ? (int)$item['product_id'] : 0;
                $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
                $price = isset($item['price']) ? (float)$item['price'] : 0.0;
                $size = isset($item['size']) ? $item['size'] : '';

                $stmt->bind_param(
                    "iiids",
                    $order_id,
                    $product_id,
                    $quantity,
                    $price,
                    $size
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Failed to create order item: " . $stmt->error);
                }
                
                // Update product stock
                $product = new Product();
                if (!$product->updateStock($item['product_id'], $item['quantity'])) {
                    throw new Exception("Failed to update product stock");
                }
            }
            
            // Commit transaction
            $this->db->commit();
            
            return $order_id;
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get order by ID
     * @param int $id
     * @return array|bool
     */
    public function getById($id) {
    $stmt = $this->db->prepare("SELECT o.*, o.created_at AS order_date FROM orders o WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $order = $result->fetch_assoc();
            
            // Get order items
            $order['items'] = $this->getOrderItems($id);
            
            return $order;
        } else {
            return false;
        }
    }
    
    /**
     * Get order items
     * @param int $order_id
     * @return array
     */
    public function getOrderItems($order_id) {
        $stmt = $this->db->prepare(
            "SELECT oi.*, p.image, p.name AS product_name, p.team AS product_team, p.category AS product_category
             FROM order_items oi 
             LEFT JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?"
        );
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
    public function getAll() {
        $result = $this->db->query(
            "SELECT o.*, u.username 
             FROM orders o 
             JOIN users u ON o.user_id = u.id 
             ORDER BY o.created_at DESC"
        );
        
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }

    /**
     * Get all orders with user info for admin listing
     * @return array
     */
    public function getAllWithUserInfo() {
        $result = $this->db->query(
            "SELECT o.id, o.total_amount, o.status, o.payment_method, o.created_at AS order_date, u.username AS user_name
             FROM orders o
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC"
        );

        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        return $orders;
    }
    
    /**
     * Update order status
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
    
    /**
     * Get order count (for admin dashboard)
     * @return int
     */
    public function getOrderCount() {
        $result = $this->db->query("SELECT COUNT(*) as count FROM orders");
        $row = $result->fetch_assoc();
        return $row['count'];
    }
    
    /**
     * Get recent orders (for admin dashboard)
     * @param int $limit
     * @return array
     */
    public function getRecentOrders($limit = 5) {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.username 
             FROM orders o 
             JOIN users u ON o.user_id = u.id 
             ORDER BY o.created_at DESC 
             LIMIT ?"
        );
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        
        return $orders;
    }
    
    /**
     * Get total sales (for admin dashboard)
     * @return float
     */
    public function getTotalSales() {
        $result = $this->db->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
        $row = $result->fetch_assoc();
        return $row['total'] ? $row['total'] : 0;
    }
    
    /**
     * Update payment details for an order
     * @param int $order_id
     * @param array $payment_details
     * @return bool
     */
    public function updatePaymentDetails($order_id, $payment_details) {
        try {
            $stmt = $this->db->prepare(
                "UPDATE orders 
                 SET payment_id = ?, 
                     razorpay_order_id = ?, 
                     status = 'paid',
                     updated_at = CURRENT_TIMESTAMP 
                 WHERE id = ?"
            );
            $stmt->bind_param("ssi", $payment_details['payment_id'], $payment_details['order_id'], $order_id);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to update payment details: " . $stmt->error);
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Payment details update failed: " . $e->getMessage());
            return false;
        }
    }
}
?>