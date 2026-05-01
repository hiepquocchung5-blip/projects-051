<?php
// /frontend/index.php
// Main Front Controller - v1.3

require_once '../config/globals.php';
require_once '../config/database.php';
require_once 'includes/functions.php'; 

$route = isset($_GET['route']) ? $_GET['route'] : 'home';

include_once 'includes/header.php';

switch ($route) {
    case 'home':
        include_once 'pages/home.php';
        break;
    case 'play':
        include_once 'pages/play.php';
        break;
    case 'leaderboard':
        include_once 'pages/leaderboard.php';
        break;
    case 'profile':
        include_once 'pages/profile.php';
        break;
    case 'settings':
        include_once 'pages/settings.php';
        break;
    case 'auth':
        include_once 'pages/auth.php';
        break;
    default:
        renderSystemError("Sector Not Found", "The requested routing pathway does not exist in the mainframe.", "404_NOT_FOUND");
        break;
}

include_once 'components/withdraw_modal.php';
include_once 'includes/footer.php';
?>