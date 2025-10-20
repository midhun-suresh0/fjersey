<?php
/**
 * Database Class
 * Handles database connections and operations
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = "StrongPass!23";
    private $dbname = DB_NAME;
    
    private $conn;
    private $error;
    private static $instance = null;
    
    /**
     * Constructor - Creates database connection
     */
    private function __construct() {
        // Create connection using mysqli
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);
        
        // Check connection
        if ($this->conn->connect_error) {
            $this->error = 'Connection Failed: ' . $this->conn->connect_error;
            die($this->error);
        }
    }
    
    /**
     * Get singleton instance
     * @return Database
     */
    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    /**
     * Get database connection
     * @return mysqli
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Execute query
     * @param string $sql
     * @return mysqli_result|bool
     */
    public function query($sql) {
        return $this->conn->query($sql);
    }
    
    /**
     * Prepare statement
     * @param string $sql
     * @return mysqli_stmt
     */
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }
    
    /**
     * Get last inserted ID
     * @return int
     */
    public function lastInsertId() {
        return $this->conn->insert_id;
    }
    
    /**
     * Get error
     * @return string
     */
    public function getError() {
        return $this->conn->error;
    }
    
    /**
     * Close connection
     */
    public function __destruct() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>