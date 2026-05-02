<?php
// /api/routes/admin_actions.php
// Endpoint: /api/index.php?route=admin_actions

global $db, $method, $requestData;
/** @var PDO $db */
/** @var string $method */
/** @var array $requestData */

$authUser = JWT::validate();
if ($authUser['role'] !== 'admin') {
    Response::error("Unauthorized Action", 403);
}

if ($method !== 'POST') Response::error("Method not allowed.", 405);

if (isset($requestData['action']) && $requestData['action'] === 'process_withdrawal') {
    $withdrawal_id = intval($requestData['id']);
    $new_status = $requestData['status'];

    if (!in_array($new_status, ['approved', 'rejected'])) {
        Response::error("Invalid status payload");
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("SELECT user_id, amount_mmk, status FROM withdrawals WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $withdrawal_id]);
        $withdrawal = $stmt->fetch();

        if (!$withdrawal || $withdrawal['status'] !== 'pending') {
            throw new Exception("Withdrawal not found or already processed.");
        }

        $db->prepare("UPDATE withdrawals SET status = :status, updated_at = NOW() WHERE id = :id")
           ->execute(['status' => $new_status, 'id' => $withdrawal_id]);

        if ($new_status === 'rejected') {
            $db->prepare("UPDATE users SET mmk_balance = mmk_balance + :amount WHERE id = :user_id")
               ->execute(['amount' => $withdrawal['amount_mmk'], 'user_id' => $withdrawal['user_id']]);
        }

        $db->commit();
        Response::success("Withdrawal {$new_status}.");

    } catch (Exception $e) {
        if($db->inTransaction()) $db->rollBack();
        Response::error($e->getMessage(), 500);
    }
} else {
    Response::error("Unknown action", 400);
}
?>