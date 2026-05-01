<?php
// /frontend/includes/security.php
// Global Security & Sanitization Layer

/**
 * Sanitizes all global input arrays
 */
function sanitizeInput(&$input) {
    if (is_array($input)) {
        foreach ($input as &$value) {
            sanitizeInput($value);
        }
    } else {
        $input = htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

sanitizeInput($_GET);
sanitizeInput($_POST);

/**
 * Simple CSRF Protection for sensitive actions
 */
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function validateCSRF($token) {
    return hash_equals($_SESSION['csrf_token'], $token);
}
?>