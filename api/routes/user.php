<?php
// /api/routes/user.php
// Endpoint: /api/index.php?route=user

global $db, $method, $requestData;
/** @var PDO $db */
/** @var string $method */
/** @var array $requestData */

$authUser = JWT::validate();
$userId = $authUser['id'];

if ($method === 'GET') {
    $stmt = $db->prepare("SELECT id, username, email, urban_coins, mmk_balance, role FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();
    
    if($user) Response::success("Profile retrieved.", $user);
    else Response::error("Operative not found.", 404);

} elseif ($method === 'PUT') {
    $newUsername = htmlspecialchars($requestData['username'] ?? '');
    if(!$newUsername) Response::error("Username required.");

    $stmt = $db->prepare("UPDATE users SET username = :uname WHERE id = :id");
    $stmt->execute(['uname' => $newUsername, 'id' => $userId]);

    Response::success("Alias updated.", ["username" => $newUsername]);

} elseif ($method === 'DELETE') {
    $db->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $userId]);
    Response::success("Operative purged from system.");
} else {
    Response::error("Method not allowed.", 405);
}
?>