<?php
// /api/index.php
// Centralized API Gateway & Global Exception Shield

// 1. Prevent 500 Errors by catching everything and returning JSON
ini_set('display_errors', 0);
error_reporting(E_ALL);

set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Internal Server Error: " . $e->getMessage()]);
    exit;
});

set_error_handler(function($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

// 2. Load Core Dependencies
require_once '../config/env_parser.php';
EnvParser::load(__DIR__ . '/../.env');
require_once '../config/cors.php';
require_once '../config/database.php';
require_once 'includes/Response.php';
require_once 'includes/JWT.php';

// 3. Dynamic Router Logic
$route = isset($_GET['route']) ? htmlspecialchars($_GET['route']) : '';
$method = $_SERVER['REQUEST_METHOD'];

// Ensure route exists
$targetFile = __DIR__ . '/routes/' . $route . '.php';

if ($route && file_exists($targetFile)) {
    // Inject DB Connection globally for routes
    $db = (new Database())->getConnection();
    $requestData = json_decode(file_get_contents("php://input"), true) ?? [];
    
    require_once $targetFile;
} else {
    Response::error("API Endpoint '{$route}' not found.", 404);
}
?>