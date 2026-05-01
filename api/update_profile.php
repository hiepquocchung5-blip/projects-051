<?php
// /api/update_profile.php
// Secure endpoint to handle username and password updates

header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';
require_once '../config/globals.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized connection."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));
$db = (new Database())->getConnection();
$user_id = $_SESSION['user_id'];

try {
    $db->beginTransaction();

    // 1. Update Username
    if (!empty($data->username) && $data->username !== $_SESSION['username']) {
        $uname = htmlspecialchars($data->username);
        $stmt = $db->prepare("UPDATE users SET username = :uname WHERE id = :id");
        $stmt->execute(['uname' => $uname, 'id' => $user_id]);
        $_SESSION['username'] = $uname;
    }

    // 2. Update Password (Native Auth Only)
    if (!empty($data->new_pass)) {
        if(empty($data->old_pass)) {
            throw new Exception("Current decryption key required to set a new one.");
        }

        $stmt = $db->prepare("SELECT password_hash, auth_provider FROM users WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch();

        if($user['auth_provider'] !== 'native') {
            throw new Exception("Account linked to Google. Password change denied.");
        }

        if (!password_verify($data->old_pass, $user['password_hash'])) {
            throw new Exception("Current decryption key is invalid.");
        }

        $new_hash = password_hash($data->new_pass, PASSWORD_BCRYPT);
        $updatePass = $db->prepare("UPDATE users SET password_hash = :hash WHERE id = :id");
        $updatePass->execute(['hash' => $new_hash, 'id' => $user_id]);
    }

    $db->commit();
    echo json_encode(["status" => "success", "message" => "System configuration committed successfully."]);

} catch (Exception $e) {
    $db->rollBack();
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>