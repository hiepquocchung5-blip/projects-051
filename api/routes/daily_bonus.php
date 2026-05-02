<?php
// /api/routes/daily_bonus.php
// Endpoint: /api/index.php?route=daily_bonus

global $db, $method, $requestData;
/** @var PDO $db */
/** @var string $method */
/** @var array $requestData */

$authUser = JWT::validate();
$userId = $authUser['id'];

if ($method !== 'POST') Response::error("Method not allowed.", 405);

try {
    $db->beginTransaction();
    $rewardAmount = 10000; 

    $stmt = $db->prepare("SELECT created_at FROM transactions WHERE user_id = :id AND source = 'daily_login' ORDER BY created_at DESC LIMIT 1 FOR UPDATE");
    $stmt->execute(['id' => $userId]);
    $lastClaim = $stmt->fetchColumn();

    if ($lastClaim && date('Y-m-d', strtotime($lastClaim)) >= date('Y-m-d')) {
        throw new Exception("Asset drop already claimed today.");
    }

    $db->prepare("INSERT INTO transactions (user_id, amount, source) VALUES (:uid, :amount, 'daily_login')")
       ->execute(['uid' => $userId, 'amount' => $rewardAmount]);

    $db->prepare("UPDATE users SET urban_coins = urban_coins + :amount WHERE id = :uid")
       ->execute(['amount' => $rewardAmount, 'uid' => $userId]);

    $db->commit();
    Response::success("Asset Drop Secured: +10,000 Coins");

} catch (Exception $e) {
    if($db->inTransaction()) $db->rollBack();
    Response::error($e->getMessage(), 400);
}
?>