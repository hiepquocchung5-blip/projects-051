<?php
// /api/index.php
// Centralized API Gateway - Secure Router

// 1. MUST LOAD CORS FIRST
require_once '../config/cors.php';

ini_set('display_errors', 0);
error_reporting(E_ALL);

set_exception_handler(function($e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Internal System Fault."]);
    exit;
});

require_once '../config/env_parser.php';
EnvParser::load(__DIR__ . '/../.env');
require_once '../config/database.php';
require_once 'includes/Response.php';
require_once 'includes/JWT.php';

$route = isset($_GET['route']) ? htmlspecialchars(trim($_GET['route'])) : '';
$method = $_SERVER['REQUEST_METHOD'];

$route = preg_replace('/[^a-zA-Z0-9_]/', '', $route);
$targetFile = __DIR__ . '/routes/' . $route . '.php';

if ($route && file_exists($targetFile)) {
    $db = (new Database())->getConnection();
    $requestData = json_decode(file_get_contents("php://input"), true) ?? [];
    require_once $targetFile;
} else {
    Response::error("API Endpoint '[{$route}]' not recognized by gateway.", 404);
}
?>