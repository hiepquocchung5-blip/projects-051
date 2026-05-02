<?php
// /config/globals.php
// Production Setup: Single Domain Architecture (Bypasses DNS/CORS issues)

require_once __DIR__ . '/env_parser.php';
EnvParser::load(__DIR__ . '/../.env');

$env = $_ENV['APP_ENV'] ?? 'development';
$protocol = $_ENV['APP_PROTOCOL'] ?? 'https://';
$baseDomain = $_ENV['APP_DOMAIN'] ?? 'adurbanix.online';

// Session config (No need for wildcard domains anymore)
$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => $cookieParams["lifetime"],
    'path' => '/',
    'secure' => $protocol === 'https://', 
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();

define('APP_NAME', 'URBANIX');
define('APP_VERSION', '2.2.1');

// CRITICAL FIX: Use subfolders instead of subdomains
if ($env === 'production') {
    define('BASE_URL', $protocol . $baseDomain);
    define('API_URL', $protocol . $baseDomain . '/api');
    define('ADMIN_URL', $protocol . $baseDomain . '/admin');
} else {
    define('BASE_URL', $protocol . $baseDomain . '/frontend');
    define('API_URL', $protocol . $baseDomain . '/api');
    define('ADMIN_URL', $protocol . $baseDomain . '/admin');
}

define('COIN_TO_MMK_RATE', 10000000); 
define('MMK_BASE_VALUE', 1000);       
define('AD_INTERVAL_SECONDS', 60);
?>