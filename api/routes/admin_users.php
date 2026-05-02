<?php
// /api/routes/admin_users.php
// Endpoint: /api/index.php?route=admin_users

global $db, $method, $requestData;
/** @var PDO $db */
/** @var string $method */
/** @var array $requestData */

$authUser = JWT::validate();
if ($authUser['role'] !== 'admin') {
    Response::error("Overseer clearance required.", 403);
}

if ($method !== 'POST') Response::error("Method not allowed.", 405);

if(isset($requestData['id']) && isset($requestData['coins']) && isset($requestData['mmk']) && isset($requestData['role'])) {
    try {
        $stmt = $db->prepare("UPDATE users SET urban_coins = :coins, mmk_balance = :mmk, role = :role WHERE id = :id");
        $stmt->execute([
            'coins' => intval($requestData['coins']),
            'mmk' => floatval($requestData['mmk']),
            'role' => in_array($requestData['role'], ['player', 'admin']) ? $requestData['role'] : 'player',
            'id' => intval($requestData['id'])
        ]);

        $db->prepare("INSERT INTO transactions (user_id, amount, source) VALUES (:uid, 0, 'admin_bonus')")
           ->execute(['uid' => intval($requestData['id'])]);

        Response::success("Operative data updated.");

    } catch (Exception $e) {
        Response::error("Database override failed.", 500);
    }
} else {
    Response::error("Malformed request syntax.", 400);
}
?>