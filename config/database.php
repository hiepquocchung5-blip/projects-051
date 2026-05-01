<?php
// /config/database.php
// Secure PDO Database Connection

class Database {
    private $host = "localhost";
    private $db_name = "urbanix_db";
    private $username = "root";
    private $password = "Stephan2k03"; // Local default
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Using PDO for prepared statements (security against SQL injection)
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>