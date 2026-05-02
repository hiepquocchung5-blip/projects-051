<?php
// /config/database.php
// Secure PDO Connection - Strict .ENV Enforcement

class Database {
    public $conn;

    public function getConnection() {
        $this->conn = null;
        
        // Failsafe: If ENV isn't loaded yet, load it directly.
        if (!isset($_ENV['DB_HOST'])) {
            require_once __DIR__ . '/env_parser.php';
            EnvParser::load(__DIR__ . '/../.env');
        }

        $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
        $db_name = $_ENV['DB_NAME'] ?? 'urbanix_db';
        $username = $_ENV['DB_USER'] ?? 'root';
        $password = $_ENV['DB_PASS'] ?? '';

        try {
            $dsn = "mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $this->conn = new PDO($dsn, $username, $password, $options);
            
        } catch(PDOException $exception) {
            error_log("CRITICAL DB FAULT: " . $exception->getMessage());
            die("<div style='background:#0a0a0c;color:#d4af37;height:100vh;display:flex;align-items:center;justify-content:center;font-family:monospace;flex-direction:column;'>
                    <h1 style='font-size:2rem;margin-bottom:10px;'>[SYSTEM_HALTED]</h1>
                    <p>Database Uplink Failure. Verify .env credentials.</p>
                 </div>");
        }
        return $this->conn;
    }
}
?>