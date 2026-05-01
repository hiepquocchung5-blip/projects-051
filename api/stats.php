<?php
// /api/stats.php
// Returns JSON data for Admin dashboard visualizations

// CORS Headers & Preflight handling
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../config/globals.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    exit(json_encode(["error" => "Unauthorized"]));
}

$db = (new Database())->getConnection();

// Get Registration trend (last 7 days)
$registrations = $db->query("
    SELECT DATE(created_at) as date, COUNT(*) as count 
    FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
    GROUP BY DATE(created_at) 
    ORDER BY date ASC
")->fetchAll();

// Get Transaction volume by source
$volume = $db->query("
    SELECT source, SUM(amount) as total 
    FROM transactions 
    GROUP BY source
")->fetchAll();

echo json_encode([
    "registrations" => $registrations,
    "volume" => $volume
]);
?>