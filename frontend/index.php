<?php
// /frontend/index.php
// Production Router - Smart Landing vs Dashboard Logic

require_once '../config/globals.php';
require_once '../config/database.php';
require_once 'includes/functions.php'; 
require_once 'includes/security.php'; // New security layer

$route = isset($_GET['route']) ? $_GET['route'] : 'home';
$isLoggedIn = isset($_SESSION['user_id']);

// Force Auth Wall for protected modules
$protected = ['play', 'profile', 'settings'];
if (in_array($route, $protected) && !$isLoggedIn) {
    header("Location: ?route=auth&redirect=" . urlencode($route));
    exit;
}

// Redirect logged-in users from Auth/Landing to Dashboard
if ($isLoggedIn && ($route === 'auth')) {
    header("Location: ?route=home");
    exit;
}

// Global Header (Handles minimal vs full)
if ($route === 'auth') {
    include_once 'includes/header_minimal.php';
} else {
    include_once 'includes/header.php';
}

// Route Switcher
switch ($route) {
    case 'home':
        if ($isLoggedIn) {
            include_once 'pages/home.php'; // The Dashboard
        } else {
            include_once 'pages/landing.php'; // The Showcase
        }
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
        renderSystemError("Module Offline", "The requested interface is currently unavailable.", "404_SEC_FAILURE");
        break;
}

// Global Footer
if ($route === 'auth') {
    include_once 'includes/footer_minimal.php';
} else {
    include_once 'includes/footer.php';
}
?>