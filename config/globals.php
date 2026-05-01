<?php
// /config/globals.php
// System-wide constants

define('APP_NAME', 'URBANIX');
define('APP_VERSION', '1.0.0');

// Economy System
define('COIN_TO_MMK_RATE', 10000000); // 10M Urban Coins
define('MMK_BASE_VALUE', 1000);       // = 1000 MMK

// Base URLs (Adjust for production)
define('BASE_URL', 'http://localhost/urbanix/frontend');
define('API_URL', 'http://localhost/urbanix/api');

// Ad Timers
define('AD_INTERVAL_SECONDS', 60);

session_start(); // Initialize session globally
?>