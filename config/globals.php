<?php
// /config/globals.php
// Production Setup: STRICT Subdomain Architecture Override

require_once __DIR__ . '/env_parser.php';
EnvParser::load(__DIR__ . '/../.env');

$protocol = $_ENV['APP_PROTOCOL'] ?? 'https://';
$baseDomain = $_ENV['APP_DOMAIN'] ?? 'adurbanix.online';

$cookieParams = session_get_cookie_params();
session_set_cookie_params([
    'lifetime' => $cookieParams["lifetime"],
    'path' => '/',
    'secure' => true, 
    'httponly' => true,
    'samesite' => 'Lax'
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('APP_NAME', 'URBANIX');
define('APP_VERSION', '2.5.1');

// CRITICAL FIX: Hardcoded to strict subdomains to prevent HTML/404 errors
define('BASE_URL', $protocol . $baseDomain);
define('API_URL', $protocol . 'api.' . $baseDomain);
define('ADMIN_URL', $protocol . 'admin.' . $baseDomain);

define('COIN_TO_MMK_RATE', 10000000); 
define('MMK_BASE_VALUE', 1000);       
define('AD_INTERVAL_SECONDS', 60);
?>