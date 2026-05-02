<?php
// /config/cors.php
// Centralized Cross-Origin Security Layer

// 1. Load ENV to determine environments if needed
require_once __DIR__ . '/env_parser.php';
EnvParser::load(__DIR__ . '/../.env');

// 2. Define Allowed Origins (Strict HTTPS + Local Dev)
$allowedOrigins = [
    'https://adurbanix.online',
    'https://admin.adurbanix.online',
    'https://www.adurbanix.online',
    'http://localhost:8000',
    'http://localhost:8010'
];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// 3. Dynamic Origin Mirroring (If allowed)
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $origin);
} else {
    // Default fallback strict lockdown
    header("Access-Control-Allow-Origin: https://adurbanix.online");
}

// 4. Critical Security Headers
header("Access-Control-Allow-Credentials: true"); // Crucial for subdomain session sharing
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Max-Age: 86400"); // Cache preflight for 24 hours to reduce server load
header("Content-Type: application/json; charset=UTF-8");

// 5. Intercept Preflight (XML/Fetch) Requests instantly
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
?>