<?php
// /api/withdraw.php
// Extraction Protocol

ini_set('display_errors', 0);

// Inject Centralized CORS
require_once '../config/cors.php';

require_once '../config/database.php';
require_once '../config/globals.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized connection."]); exit;
}

$data = json_decode(file_get_contents("php://input"));

if(isset($data->phone_number) && isset($data->amount) && isset($data->method)) {
    $amount = floatval($data->amount);
    $user_id = $_SESSION['user_id'];
    
    $db = (new Database())->getConnection();
    $minWithdraw = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'minimum_withdrawal_mmk'")->fetchColumn() ?: 1000;

    if($amount < $minWithdraw) {
        echo json_encode(["status" => "error", "message" => "Minimum withdrawal is {$minWithdraw} Ks."]); exit;
    }

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("SELECT mmk_balance FROM users WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch();

        if($user['mmk_balance'] < $amount) { throw new Exception("Insufficient MMK liquid assets."); }

        $db->prepare("UPDATE users SET mmk_balance = mmk_balance - :amount WHERE id = :id")
           ->execute(['amount' => $amount, 'id' => $user_id]);

        $db->prepare("INSERT INTO withdrawals (user_id, phone_number, payment_method, amount_mmk, status) VALUES (:uid, :phone, :method, :amount, 'pending')")
           ->execute([
               'uid' => $user_id, 'phone' => htmlspecialchars($data->phone_number),
               'method' => in_array($data->method, ['KPay', 'WaveMoney']) ? $data->method : 'KPay', 'amount' => $amount
           ]);

        $_SESSION['mmk_balance'] -= $amount;
        $db->commit();
        echo json_encode(["status" => "success", "message" => "Withdrawal queued.", "new_balance" => $_SESSION['mmk_balance']]);

    } catch (Exception $e) {
        if($db->inTransaction()) $db->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing required syntax."]);
}
?>