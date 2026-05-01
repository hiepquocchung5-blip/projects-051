<?php
// /frontend/index.php
// Main Front Controller

require_once '../config/globals.php';
require_once '../config/database.php';

// Simple Routing Logic
$route = isset($_GET['route']) ? $_GET['route'] : 'home';

// Include Header
include_once 'includes/header.php';

// Route matching
switch ($route) {
    case 'home':
        include_once 'pages/home.php';
        break;
    case 'play':
        include_once 'pages/play.php';
        break;
    default:
        echo "<div class='flex items-center justify-center h-full text-neon-cyan text-2xl font-black'>404 - SECTOR NOT FOUND</div>";
        break;
}

// Include Footer
include_once 'includes/footer.php';
?>