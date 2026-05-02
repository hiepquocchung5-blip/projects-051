<?php
// /api/index.php
// Production API Gateway - 100% JSON Enforcement

// 1. Force JSON header immediately to stop HTML defaults
header('Content-Type: application/json; charset=UTF-8');

// 2. Load CORS First
require_once '../config/cors.php';

// 3. Global Error/Exception Shield
set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Matrix Fault: " . $e->getMessage()
    ]);
    exit;
});

set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) return;
    throw new ErrorException($message, 0, $severity, $file, $line);
});

require_once '../config/env_parser.php';
EnvParser::load(__DIR__ . '/../.env');
require_once '../config/database.php';
require_once 'includes/Response.php';
require_once 'includes/JWT.php';

$route = isset($_GET['route']) ? preg_replace('/[^a-z_]/', '', $_GET['route']) : '';
$method = $_SERVER['REQUEST_METHOD'];

// Route Selection
$targetFile = __DIR__ . '/routes/' . $route . '.php';

if ($route && file_exists($targetFile)) {
    try {
        $db = (new Database())->getConnection();
        $requestData = json_decode(file_get_contents("php://input"), true) ?? [];
        require_once $targetFile;
    } catch (Exception $ex) {
        Response::error("Uplink Error: " . $ex->getMessage(), 500);
    }
} else {
    Response::error("Target module [{$route}] is offline or unreachable.", 404);
}