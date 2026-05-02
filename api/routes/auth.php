<?php
// /api/routes/auth.php
// Endpoint: /api/index.php?route=auth
// Generates JWT on successful login

if ($method !== 'POST') Response::error("Method not allowed.", 405);

$action = $requestData['action'] ?? '';
$email = filter_var($requestData['email'] ?? '', FILTER_SANITIZE_EMAIL);
$password = $requestData['password'] ?? '';

if (!$action || !$email || !$password) Response::error("Missing credentials.");

if ($action === 'login') {
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) Response::error("Operative not found.", 404);
    if ($user['auth_provider'] === 'google') Response::error("Use Google Auth for this account.", 403);

    if (password_verify($password, $user['password_hash'])) {
        $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute(['id' => $user['id']]);

        // Generate JWT
        $token = JWT::encode([
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role']
        ]);

        Response::success("Authentication verified.", [
            "token" => $token,
            "user" => [
                "id" => $user['id'],
                "username" => $user['username'],
                "urban_coins" => $user['urban_coins']
            ]
        ]);
    } else {
        Response::error("Decryption Key Invalid.", 401);
    }
}
// Add 'register' logic here similar to previous native_auth, returning JWT on success.
?>