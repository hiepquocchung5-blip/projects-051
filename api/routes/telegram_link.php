<?php
// /api/routes/telegram_link.php
// Endpoint: /api/index.php?route=telegram_link

global $db, $method;

$authUser = JWT::validate();
$userId = $authUser['id'];

if ($method !== 'GET') Response::error("Method not allowed.", 405);

// Generate a secure, 6-character bind code
$code = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
$bindToken = "URBX-BIND-" . $code;

// Store in database (Requires adding `telegram_bind_token` column to users table)
try {
    $stmt = $db->prepare("UPDATE users SET telegram_bind_token = :token WHERE id = :id");
    $stmt->execute(['token' => $bindToken, 'id' => $userId]);

    Response::success("Bind token generated.", ["token" => $bindToken]);
} catch (Exception $e) {
    Response::error("Failed to generate linking token.", 500);
}
?>