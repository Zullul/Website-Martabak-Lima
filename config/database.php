<?php
/**
 * Database Configuration for Martabak Lima Website
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'martabak_lima');

class Database {
    private $conn;
    
    public function __construct() {
        $this->connect();
    }
    
    private function connect() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
    
    public function getConnection() {
        return $this->conn;
    }
    
    // Test database connection
    public function testConnection() {
        try {
            $stmt = $this->conn->query("SELECT 1");
            return true;
        } catch(PDOException $e) {
            return false;
        }
    }
}

// Initialize database connection
try {
    $database = new Database();
    $pdo = $database->getConnection();
} catch(Exception $e) {
    die("Database connection error: " . $e->getMessage());
}
?>