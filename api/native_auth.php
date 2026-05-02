<?php
// /api/native_auth.php
// Handles Native Login and Registration

ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once '../config/cors.php';
require_once '../config/database.php';
require_once '../config/globals.php';

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
            echo json_encode(["status" => "error", "message" => "Operative alias required."]); exit;
        }
        $username = htmlspecialchars($data->username);

        $stmt = $db->prepare("SELECT auth_provider FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        if ($existingUser = $stmt->fetch()) {
            $msg = $existingUser['auth_provider'] === 'google' ? "Email linked to Google." : "Email already registered.";
            echo json_encode(["status" => "error", "message" => $msg]); exit;
        }

        $db->beginTransaction();

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $insertStmt = $db->prepare("INSERT INTO users (username, email, password_hash, auth_provider) VALUES (:uname, :email, :hash, 'native')");
        $insertStmt->execute(['uname' => $username, 'email' => $email, 'hash' => $hash]);
        $user_id = $db->lastInsertId();

        // Referral Logic
        if (!empty($data->referral)) {
            $sponsor = htmlspecialchars($data->referral);
            $refStmt = $db->prepare("SELECT id FROM users WHERE username = :uname LIMIT 1");
            $refStmt->execute(['uname' => $sponsor]);
            if ($sponsorData = $refStmt->fetch()) {
                $db->prepare("UPDATE users SET urban_coins = urban_coins + 50000 WHERE id = :id")->execute(['id' => $sponsorData['id']]);
                $db->prepare("INSERT INTO transactions (user_id, amount, source) VALUES (:uid, 50000, 'referral')")->execute(['uid' => $sponsorData['id']]);
            }
        }

        $db->commit();

        $_SESSION['user_id'] = $user_id; $_SESSION['username'] = $username;
        $_SESSION['urban_coins'] = 0; $_SESSION['mmk_balance'] = 0; $_SESSION['role'] = 'player';

        echo json_encode(["status" => "success", "message" => "Registration successful."]);

    } elseif ($data->action === 'login') {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user) { echo json_encode(["status" => "error", "message" => "Credentials not found."]); exit; }
        if ($user['auth_provider'] === 'google') { echo json_encode(["status" => "error", "message" => "Requires Google Auth."]); exit; }

        if (password_verify($password, $user['password_hash'])) {
            $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute(['id' => $user['id']]);

            $_SESSION['user_id'] = $user['id']; $_SESSION['username'] = $user['username'];
            $_SESSION['urban_coins'] = $user['urban_coins']; $_SESSION['mmk_balance'] = $user['mmk_balance'];
            $_SESSION['role'] = $user['role'];

            echo json_encode(["status" => "success", "message" => "Authentication successful."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid decryption key."]);
        }
    }
} catch (Exception $e) {
    if(isset($db) && $db->inTransaction()) $db->rollBack();
    echo json_encode(["status" => "error", "message" => "System error: " . $e->getMessage()]);
}
?>