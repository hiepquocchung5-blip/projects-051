<?php
// /api/admin_actions.php
// Dashboard Actions

ini_set('display_errors', 0);

// Inject Centralized CORS
require_once '../config/cors.php';

require_once '../config/database.php';
require_once '../config/globals.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized Action"]); exit;
}

$data = json_decode(file_get_contents("php://input"));

if (isset($data->action) && $data->action === 'process_withdrawal') {
    $withdrawal_id = intval($data->id);
    $new_status = $data->status;

    if (!in_array($new_status, ['approved', 'rejected'])) {
        echo json_encode(["status" => "error", "message" => "Invalid status payload"]); exit;
    }

    try {
        $db = (new Database())->getConnection();
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
        echo json_encode(["status" => "success", "message" => "Withdrawal {$new_status}."]);

    } catch (Exception $e) {
        if(isset($db) && $db->inTransaction()) $db->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Unknown action"]);
}
?>