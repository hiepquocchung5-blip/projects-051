<?php
// /api/auth.php
// Google Auth Logic

ini_set('display_errors', 0);

// Inject Centralized CORS
require_once '../config/cors.php';

require_once '../config/database.php';
require_once '../config/globals.php';

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput);

if(isset($data->google_id) && isset($data->email)) {
    try {
        $db = (new Database())->getConnection();
        
        $stmt = $db->prepare("SELECT id, username, urban_coins, mmk_balance, role FROM users WHERE google_id = :google_id LIMIT 1");
        $stmt->execute(['google_id' => $data->google_id]);
        $user = $stmt->fetch();

        if(!$user) {
            $username = explode('@', $data->email)[0] . '_' . rand(1000, 9999);
            $insertStmt = $db->prepare("INSERT INTO users (google_id, username, email, auth_provider) VALUES (:gid, :uname, :email, 'google')");
            $insertStmt->execute([
                'gid' => $data->google_id,
                'uname' => $username,
                'email' => filter_var($data->email, FILTER_SANITIZE_EMAIL)
            ]);
            $user_id = $db->lastInsertId();
            $user = ['id' => $user_id, 'username' => $username, 'urban_coins' => 0, 'mmk_balance' => 0, 'role' => 'player'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['urban_coins'] = $user['urban_coins'];
        $_SESSION['mmk_balance'] = $user['mmk_balance'];
        $_SESSION['role'] = $user['role'];

        $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute(['id' => $user['id']]);

        echo json_encode(["status" => "success", "message" => "Authenticated", "user" => $user]);
    } catch(PDOException $e) {
        echo json_encode(["status" => "error", "message" => "System fault: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing Auth Data payload."]);
}
?>