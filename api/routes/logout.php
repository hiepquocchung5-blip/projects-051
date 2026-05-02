<?php
// /api/routes/logout.php
// Endpoint: /api/index.php?route=logout

global $method;
/** @var string $method */

if ($method !== 'POST') Response::error("Method not allowed.", 405);

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