<?php
// /config/globals.php
// Production Setup: .ENV Integration & Subdomain Session Sharing

// 1. Load Environment Variables
require_once __DIR__ . '/env_parser.php';
EnvParser::load(__DIR__ . '/../.env');

$env = $_ENV['APP_ENV'] ?? 'development';
$protocol = $_ENV['APP_PROTOCOL'] ?? 'http://';
$baseDomain = $_ENV['APP_DOMAIN'] ?? 'localhost';

// 2. Subdomain Session Sharing (Only apply domain parameter in production)
$cookieParams = session_get_cookie_params();
$sessionConfig = [
    'lifetime' => $cookieParams["lifetime"],
    'path' => '/',
    'secure' => $protocol === 'https://', 
    'httponly' => true,
    'samesite' => 'Lax'
];

// Add leading dot for subdomain sharing if in production
if ($env === 'production') {
    $sessionConfig['domain'] = '.' . $baseDomain;
}

session_set_cookie_params($sessionConfig);
session_start();

// 3. Dynamic URLs based on .env
define('APP_NAME', 'URBANIX');
define('APP_VERSION', '2.2.0');

if ($env === 'production') {
    define('BASE_URL', $protocol . $baseDomain);
    define('API_URL', $protocol . 'api.' . $baseDomain);
    define('ADMIN_URL', $protocol . 'admin.' . $baseDomain);
} else {
    // Localhost fallback structure
    define('BASE_URL', $protocol . $baseDomain . '/frontend');
    define('API_URL', $protocol . $baseDomain . '/api');
    define('ADMIN_URL', $protocol . $baseDomain . '/admin');
}

// 4. Economy Constants
define('COIN_TO_MMK_RATE', 10000000); 
define('MMK_BASE_VALUE', 1000);       
define('AD_INTERVAL_SECONDS', 60);
?>