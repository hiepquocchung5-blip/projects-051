<?php
// /api/auth.php
// Handles Google Auth and creates secure user sessions

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';
require_once '../config/globals.php';

$data = json_decode(file_get_contents("php://input"));

if(isset($data->google_id) && isset($data->email)) {
    $db = (new Database())->getConnection();
    
    // 1. Check if user exists
    $stmt = $db->prepare("SELECT id, username, urban_coins, mmk_balance, role FROM users WHERE google_id = :google_id LIMIT 1");
    $stmt->execute(['google_id' => $data->google_id]);
    $user = $stmt->fetch();

    if(!$user) {
        // 2. Register new user
        $username = explode('@', $data->email)[0] . '_' . rand(1000, 9999);
        $insertStmt = $db->prepare("INSERT INTO users (google_id, username, email) VALUES (:gid, :uname, :email)");
        $insertStmt->execute([
            'gid' => $data->google_id,
            'uname' => $username,
            'email' => $data->email
        ]);
        $user_id = $db->lastInsertId();
        $user = ['id' => $user_id, 'username' => $username, 'urban_coins' => 0, 'mmk_balance' => 0, 'role' => 'player'];
    }

    // 3. Set Session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['urban_coins'] = $user['urban_coins'];
    $_SESSION['mmk_balance'] = $user['mmk_balance'];
    $_SESSION['role'] = $user['role'];

    // Update last login
    $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id")->execute(['id' => $user['id']]);

    echo json_encode(["status" => "success", "message" => "Authenticated", "user" => $user]);
} else {
    echo json_encode(["status" => "error", "message" => "Missing Auth Data"]);
}
?>