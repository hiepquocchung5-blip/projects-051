<?php
// /api/routes/wallet.php
// Endpoint: /api/index.php?route=wallet

global $db, $method, $requestData;
/** @var PDO $db */
/** @var string $method */
/** @var array $requestData */

$authUser = JWT::validate();
$userId = $authUser['id'];

if ($method === 'POST') {
    $amount = intval($requestData['amount'] ?? 0);
    $action = htmlspecialchars($requestData['action'] ?? 'unknown');

    if ($amount <= 0 || $amount > 100000) Response::error("Anomalous payload size.", 403);

    try {
        $db->beginTransaction();

        $stmtEvent = $db->query("SELECT coin_multiplier FROM events WHERE is_active = 1 AND NOW() BETWEEN start_time AND end_time LIMIT 1");
        $event = $stmtEvent->fetch();
        $multiplier = $event ? (float)$event['coin_multiplier'] : 1.0;
        $finalAmount = (int)($amount * $multiplier);

        $db->prepare("INSERT INTO transactions (user_id, amount, source) VALUES (:uid, :amount, :source)")
           ->execute(['uid' => $userId, 'amount' => $finalAmount, 'source' => $action]);

        $db->prepare("UPDATE users SET urban_coins = urban_coins + :amount WHERE id = :id")
           ->execute(['amount' => $finalAmount, 'id' => $userId]);

        $db->commit();
        
        $newBal = $db->prepare("SELECT urban_coins FROM users WHERE id = ?");
        $newBal->execute([$userId]);
        $currentCoins = $newBal->fetchColumn();

        Response::success("Assets transferred.", ["new_balance" => $currentCoins, "yield" => $finalAmount]);

    } catch (Exception $e) {
        if($db->inTransaction()) $db->rollBack();
        Response::error("Ledger fault: " . $e->getMessage(), 500);
    }
} else {
    Response::error("Method not allowed.", 405);
}
?>