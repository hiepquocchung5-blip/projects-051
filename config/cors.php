<?php
// /config/cors.php
// Centralized Cross-Origin Security Layer (Strict Preflight)

$allowedOrigins = [
    'https://adurbanix.online',
    'https://www.adurbanix.online',
    'https://admin.adurbanix.online',
    'http://localhost:8000'
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $origin);
} else {
    header("Access-Control-Allow-Origin: https://adurbanix.online");
}

header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Max-Age: 86400"); // Cache OPTIONS request
header("Content-Type: application/json; charset=UTF-8");

// Instantly kill OPTIONS preflight to prevent API execution
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>