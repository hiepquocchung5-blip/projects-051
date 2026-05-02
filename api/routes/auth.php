<?php
// /api/routes/auth.php
// Endpoint: /api/index.php?route=auth

global $db, $method, $requestData;
/** @var PDO $db */
/** @var string $method */
/** @var array $requestData */

if ($method !== 'POST') Response::error("Method not allowed.", 405);

$action = $requestData['action'] ?? '';
$email = filter_var(trim($requestData['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = $requestData['password'] ?? '';

if (!$action || !$email || empty($password)) {
    Response::error("Malformed credentials or invalid email format.");
}

if ($action === 'login') {
    $stmt = $db->prepare("SELECT id, username, password_hash, auth_provider, role, urban_coins, mmk_balance FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) Response::error("Operative not found.", 404);
    if ($user['auth_provider'] === 'google') Response::error("This account is bound to Google Auth. Please use Gmail Login.", 403);

    if (password_verify($password, $user['password_hash'])) {
        $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute(['id' => $user['id']]);
        
        // CRITICAL FIX: Re-sync PHP Session for the frontend HTML router
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['urban_coins'] = $user['urban_coins'];
        $_SESSION['mmk_balance'] = $user['mmk_balance'];

        $token = JWT::encode(['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']]);

        Response::success("Authentication verified.", [
            "token" => $token,
            "user" => ["id" => $user['id'], "username" => $user['username'], "urban_coins" => $user['urban_coins']]
        ]);
    } else {
        Response::error("Decryption Key Invalid.", 401);
    }
} 
elseif ($action === 'register') {
    $rawUsername = trim($requestData['username'] ?? '');
    
    if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $rawUsername)) {
        Response::error("Alias must be 3-20 characters (letters, numbers, underscores).");
    }
    
    $username = htmlspecialchars($rawUsername, ENT_QUOTES, 'UTF-8');

    $stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) Response::error("Email already registered in the matrix.");

    $db->beginTransaction();
    $hash = password_hash($password, PASSWORD_BCRYPT);
    
    $insertStmt = $db->prepare("INSERT INTO users (username, email, password_hash, auth_provider) VALUES (:uname, :email, :hash, 'native')");
    $insertStmt->execute(['uname' => $username, 'email' => $email, 'hash' => $hash]);
    $user_id = $db->lastInsertId();

    if (!empty($requestData['referral'])) {
        $sponsor = htmlspecialchars(trim($requestData['referral']), ENT_QUOTES, 'UTF-8');
        $refStmt = $db->prepare("SELECT id FROM users WHERE username = :uname LIMIT 1");
        $refStmt->execute(['uname' => $sponsor]);
        if ($sponsorData = $refStmt->fetch()) {
            $db->prepare("UPDATE users SET urban_coins = urban_coins + 50000 WHERE id = :id")->execute(['id' => $sponsorData['id']]);
            $db->prepare("INSERT INTO transactions (user_id, amount, source) VALUES (:uid, 50000, 'referral')")->execute(['uid' => $sponsorData['id']]);
        }
    }

    $db->commit();
    
    // CRITICAL FIX: Re-sync PHP Session for the frontend HTML router
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'player';
    $_SESSION['urban_coins'] = 0;
    $_SESSION['mmk_balance'] = 0;

    $token = JWT::encode(['id' => $user_id, 'username' => $username, 'role' => 'player']);

    Response::success("Registration successful.", ["token" => $token]);
}
?>