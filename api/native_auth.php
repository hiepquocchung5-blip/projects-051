<?php
// /api/native_auth.php
// Handles Native (Email/Password) Login and Registration

header("Access-Control-Allow-Origin: http://localhost:8010"); // Restrict to frontend origin
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';
require_once '../config/globals.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->action) || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["status" => "error", "message" => "Missing core parameters."]);
    exit;
}

$db = (new Database())->getConnection();
$email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
$password = $data->password;

if ($data->action === 'register') {
    if (!isset($data->username)) {
        echo json_encode(["status" => "error", "message" => "Operative alias required."]);
        exit;
    }
    $username = htmlspecialchars($data->username);

    // Check if email exists
    $stmt = $db->prepare("SELECT auth_provider FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    if ($existingUser = $stmt->fetch()) {
        $msg = $existingUser['auth_provider'] === 'google' 
            ? "Email linked to a Google account. Please use Google Login." 
            : "Email already registered. Proceed to Login.";
        echo json_encode(["status" => "error", "message" => $msg]);
        exit;
    }

    // Create User
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $insertStmt = $db->prepare("INSERT INTO users (username, email, password_hash, auth_provider) VALUES (:uname, :email, :hash, 'native')");
    
    try {
        $insertStmt->execute(['uname' => $username, 'email' => $email, 'hash' => $hash]);
        $user_id = $db->lastInsertId();
        
        // Auto-login after register
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['urban_coins'] = 0;
        $_SESSION['mmk_balance'] = 0;
        $_SESSION['role'] = 'player';

        echo json_encode(["status" => "success", "message" => "Registration successful."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database fault: " . $e->getMessage()]);
    }

} elseif ($data->action === 'login') {
    // Authenticate User
    $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user) {
        echo json_encode(["status" => "error", "message" => "Credentials not found in mainframe."]);
        exit;
    }

    if ($user['auth_provider'] === 'google') {
        echo json_encode(["status" => "error", "message" => "This email requires Google Authorization. Use the Google button."]);
        exit;
    }

    if (password_verify($password, $user['password_hash'])) {
        // Update last login
        $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute(['id' => $user['id']]);

        // Set Session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['urban_coins'] = $user['urban_coins'];
        $_SESSION['mmk_balance'] = $user['mmk_balance'];
        $_SESSION['role'] = $user['role'];

        echo json_encode(["status" => "success", "message" => "Authentication successful."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid decryption sequence (Wrong Password)."]);
    }
}
?>