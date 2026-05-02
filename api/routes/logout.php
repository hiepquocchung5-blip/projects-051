<?php
// /api/routes/logout.php
// Endpoint: /api/index.php?route=logout

// Even if client purges JWT, we clear PHP sessions here for fallback safety
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
Response::success("Mainframe disconnected.");
?>