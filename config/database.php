<?php
// /config/database.php
// Secure PDO Database Connection - Production Ready

class Database {
    private $host = "localhost";
    private $db_name = "urbanix_db";
    private $username = "root";
    private $password = "Stephan2k03"; // Change for production
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // DSN string
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            
            // Options for secure, high-performance querying
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Real prepared statements
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $exception) {
            // In production, log this to a file instead of echoing to prevent credential leakage
            error_log("Database Connection Error: " . $exception->getMessage());
            die("<div style='background:#0a0a0f;color:#ef4444;height:100vh;display:flex;align-items:center;justify-content:center;font-family:monospace;'>CRITICAL_SYSTEM_FAILURE: DB_UNREACHABLE</div>");
        }
        return $this->conn;
    }
}
?>