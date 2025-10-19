<?php
/**
 * Product Class
 * Handles product (jersey) CRUD operations
 */
class Product {
    private $db;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Add new product
     * @param array $data
     * @param string $image
     * @return bool
     */
    public function create($data, $image) {
        $name = $data['name'];
        $description = $data['description'];
        $price = $data['price'];
        $category = $data['category'];
        $team = $data['team'];
        $size = $data['size'];
        $stock = $data['stock'];
        
        $stmt = $this->db->prepare("INSERT INTO products (name, description, price, category, team, size, stock, image) 
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsssss", $name, $description, $price, $category, $team, $size, $stock, $image);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Get all products
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAll($limit = 0, $offset = 0) {
        $sql = "SELECT * FROM products ORDER BY created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT ?, ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("ii", $offset, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $result = $this->db->query($sql);
        }
        
        $products = array();
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    /**
     * Get product by ID
     * @param int $id
     * @return array|bool
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
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
     * Update product
     * @param int $id
     * @param array $data
     * @param string $image
     * @return bool
     */
    public function update($id, $data, $image = null) {
        $name = $data['name'];
        $description = $data['description'];
        $price = $data['price'];
        $category = $data['category'];
        $team = $data['team'];
        $size = $data['size'];
        $stock = $data['stock'];
        
        if ($image) {
            $stmt = $this->db->prepare("UPDATE products SET name = ?, description = ?, price = ?, 
                                        category = ?, team = ?, size = ?, stock = ?, image = ? WHERE id = ?");
            $stmt->bind_param("ssdsssssi", $name, $description, $price, $category, $team, $size, $stock, $image, $id);
        } else {
            $stmt = $this->db->prepare("UPDATE products SET name = ?, description = ?, price = ?, 
                                        category = ?, team = ?, size = ?, stock = ? WHERE id = ?");
            $stmt->bind_param("ssdssssi", $name, $description, $price, $category, $team, $size, $stock, $id);
        }
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Delete product
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Search products
     * @param string $keyword
     * @return array
     */
    public function search($keyword) {
        $keyword = "%{$keyword}%";
        
        $stmt = $this->db->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ? OR team LIKE ?");
        $stmt->bind_param("sss", $keyword, $keyword, $keyword);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = array();
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    /**
     * Filter products by category
     * @param string $category
     * @return array
     */
    public function filterByCategory($category) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE category = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = array();
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    /**
     * Filter products by team
     * @param string $team
     * @return array
     */
    public function filterByTeam($team) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE team = ?");
        $stmt->bind_param("s", $team);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = array();
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    /**
     * Get featured products
     * @param int $limit
     * @return array
     */
    public function getFeatured($limit = 4) {
        $stmt = $this->db->prepare("SELECT * FROM products ORDER BY RAND() LIMIT ?");
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $products = array();
        
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    /**
     * Update product stock
     * @param int $id
     * @param int $quantity
     * @return bool
     */
    public function updateStock($id, $quantity) {
        $stmt = $this->db->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $id);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}
?>