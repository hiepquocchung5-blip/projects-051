<?php
// /api/native_auth.php
// FIXED: Strict JSON enforcement and Error Suppression

// 1. Prevent PHP from outputting HTML warnings that break JSON parsing
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 2. CORS Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/database.php';
require_once '../config/globals.php';

// Catch raw input safely
$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);

if (!$data || !isset($data->action) || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["status" => "error", "message" => "Invalid payload received by server."]);
    exit;
}

try {
    $db = (new Database())->getConnection();
    $email = filter_var($data->email, FILTER_SANITIZE_EMAIL);
    $password = $data->password;

    if ($data->action === 'register') {
        if (empty($data->username)) {
            echo json_encode(["status" => "error", "message" => "Operative alias required."]);
            exit;
        }
        $username = htmlspecialchars($data->username);

        $stmt = $db->prepare("SELECT auth_provider FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        if ($existingUser = $stmt->fetch()) {
            $msg = $existingUser['auth_provider'] === 'google' 
                ? "Email linked to Google. Use Google Login." 
                : "Email already registered. Return to Login.";
            echo json_encode(["status" => "error", "message" => $msg]);
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $insertStmt = $db->prepare("INSERT INTO users (username, email, password_hash, auth_provider) VALUES (:uname, :email, :hash, 'native')");
        $insertStmt->execute(['uname' => $username, 'email' => $email, 'hash' => $hash]);
        
        $user_id = $db->lastInsertId();
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['urban_coins'] = 0;
        $_SESSION['mmk_balance'] = 0;
        $_SESSION['role'] = 'player';

        echo json_encode(["status" => "success", "message" => "Registration successful."]);

    } elseif ($data->action === 'login') {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(["status" => "error", "message" => "Credentials not found."]);
            exit;
        }

        if ($user['auth_provider'] === 'google') {
            echo json_encode(["status" => "error", "message" => "Requires Google Authorization."]);
            exit;
        }

        if (password_verify($password, $user['password_hash'])) {
            $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute(['id' => $user['id']]);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['urban_coins'] = $user['urban_coins'];
            $_SESSION['mmk_balance'] = $user['mmk_balance'];
            $_SESSION['role'] = $user['role'];

            echo json_encode(["status" => "success", "message" => "Authentication successful."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid decryption key (Password incorrect)."]);
        }
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "System error: " . $e->getMessage()]);
}
?>