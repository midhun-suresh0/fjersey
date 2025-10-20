<?php
require_once __DIR__ . '/Database.php';

/**
 * Admin Class
 * Handles admin-specific functions
 */
class Admin {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Check if user is admin
     * @return bool
     */
    public function isAdmin() {
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
            return true;
        }
        return false;
    }
    
    /**
     * Get dashboard statistics
     * @return array
     */
    public function getDashboardStats() {
        $stats = array();
        
        // Total users
        $result = $this->db->query("SELECT COUNT(*) as count FROM users");
        $row = $result->fetch_assoc();
        $stats['total_users'] = $row['count'];
        
        // Total products
        $result = $this->db->query("SELECT COUNT(*) as count FROM products");
        $row = $result->fetch_assoc();
        $stats['total_products'] = $row['count'];
        
        // Total orders
        $result = $this->db->query("SELECT COUNT(*) as count FROM orders");
        $row = $result->fetch_assoc();
        $stats['total_orders'] = $row['count'];
        
        // Total sales
        $result = $this->db->query("SELECT SUM(total_amount) as total FROM orders WHERE status != 'cancelled'");
        $row = $result->fetch_assoc();
        $stats['total_sales'] = $row['total'] ? $row['total'] : 0;
        
        // Recent orders
        $result = $this->db->query("SELECT o.*, u.username FROM orders o 
                                   JOIN users u ON o.user_id = u.id 
                                   ORDER BY o.created_at DESC LIMIT 5");
        $stats['recent_orders'] = array();
        while ($row = $result->fetch_assoc()) {
            $stats['recent_orders'][] = $row;
        }
        
        // Low stock products
        $result = $this->db->query("SELECT * FROM products WHERE stock < 5 ORDER BY stock ASC LIMIT 5");
        $stats['low_stock'] = array();
        while ($row = $result->fetch_assoc()) {
            $stats['low_stock'][] = $row;
        }
        
        return $stats;
    }
    
    /**
     * Get monthly sales data (for charts)
     * @return array
     */
    public function getMonthlySales() {
        $result = $this->db->query("SELECT 
                                    MONTH(created_at) as month, 
                                    YEAR(created_at) as year,
                                    SUM(total_amount) as total 
                                    FROM orders 
                                    WHERE status != 'cancelled' 
                                    GROUP BY YEAR(created_at), MONTH(created_at) 
                                    ORDER BY YEAR(created_at), MONTH(created_at) 
                                    LIMIT 12");
        
        $sales = array();
        
        while ($row = $result->fetch_assoc()) {
            $month_name = date('F', mktime(0, 0, 0, $row['month'], 1));
            $sales[] = array(
                'month' => $month_name,
                'year' => $row['year'],
                'total' => $row['total']
            );
        }
        
        return $sales;
    }
    
    /**
     * Get top selling products
     * @param int $limit
     * @return array
     */
    public function getTopSellingProducts($limit = 5) {
    $result = $this->db->query("SELECT p.id, p.name, p.image, p.price, p.team,
                    SUM(oi.quantity) as total_sold 
                    FROM products p 
                                    JOIN order_items oi ON p.id = oi.product_id 
                                    JOIN orders o ON oi.order_id = o.id 
                                    WHERE o.status != 'cancelled' 
                                    GROUP BY p.id 
                                    ORDER BY total_sold DESC 
                                    LIMIT $limit");
        
        $products = array();
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    /**
     * Get new users
     * @param int $limit
     * @return array
     */
    public function getNewUsers($limit = 5) {
        $result = $this->db->query("SELECT id, username, email, role, created_at 
                                   FROM users 
                                   ORDER BY created_at DESC 
                                   LIMIT $limit");
        
        $users = array();
        
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }

    /**
     * Get recent orders with customer name
     * @param int $limit
     * @return array
     */
    public function getRecentOrders($limit = 5) {
        $stmt = $this->db->prepare(
            "SELECT o.*, u.username AS customer_name
             FROM orders o
             JOIN users u ON o.user_id = u.id
             ORDER BY o.created_at DESC
             LIMIT ?"
        );
        if ($stmt === false) {
            return array();
        }
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        return $orders;
    }

    /**
     * Get low stock products
     * @param int $limit
     * @return array
     */
    public function getLowStockProducts($limit = 5) {
        $stmt = $this->db->prepare(
            "SELECT * FROM products WHERE stock < 5 ORDER BY stock ASC LIMIT ?"
        );
        if ($stmt === false) {
            return array();
        }
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        $result = $stmt->get_result();

        $products = array();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        return $products;
    }

    /**
     * Login as admin (authenticate only users with role = 'admin')
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT id, username, email, password, role FROM users WHERE email = ? AND role = 'admin'");
        if ($stmt === false) {
            return false;
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                // Set session just like User::login
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $row['role'];
                return true;
            }
        }

        return false;
    }
}
?>