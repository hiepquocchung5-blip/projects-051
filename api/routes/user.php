<?php
// /api/routes/user.php
// Endpoint: /api/index.php?route=user
// Protected CRUD endpoint

// 1. Verify JWT before proceeding
$authUser = JWT::validate();
$userId = $authUser['id'];

if ($method === 'GET') {
    // Read Profile
    $stmt = $db->prepare("SELECT id, username, email, urban_coins, mmk_balance, role FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();
    
    if($user) Response::success("Profile retrieved.", $user);
    else Response::error("Operative not found.", 404);

} elseif ($method === 'PUT') {
    // Update Profile (e.g., change username)
    $newUsername = htmlspecialchars($requestData['username'] ?? '');
    if(!$newUsername) Response::error("Username required.");

    $stmt = $db->prepare("UPDATE users SET username = :uname WHERE id = :id");
    $stmt->execute(['uname' => $newUsername, 'id' => $userId]);

    Response::success("Alias updated.", ["username" => $newUsername]);

} elseif ($method === 'DELETE') {
    // Delete Account
    $db->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $userId]);
    Response::success("Operative purged from system.");
} else {
    Response::error("Method not allowed.", 405);
}
?>