<?php
// /config/globals.php
// FIXED: Dynamic Host Detection & Session Integrity

// Secure session cookie settings before starting session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Dynamically detect the current host and port (e.g., localhost:8010)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST']; 

// Assuming your structure is accessed via http://localhost:8010/frontend/
// If you run your server directly INSIDE the frontend folder, change this accordingly.
$basePath = '/frontend'; 
$apiPath = '/api';

define('APP_NAME', 'URBANIX');
define('APP_VERSION', '2.0.0');

define('BASE_URL', $protocol . $host . $basePath);
define('API_URL', $protocol . $host . $apiPath);

// Economy Constants
define('COIN_TO_MMK_RATE', 10000000); 
define('MMK_BASE_VALUE', 1000);       
define('AD_INTERVAL_SECONDS', 60);
?>