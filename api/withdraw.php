<?php
// /api/withdraw.php
// Secure endpoint for users to request MMK payouts

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';
require_once '../config/globals.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Unauthorized access."]);
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if(isset($data->phone_number) && isset($data->amount) && isset($data->method)) {
    $amount = floatval($data->amount);
    $user_id = $_SESSION['user_id'];
    
    // Minimum withdrawal check (e.g., 1000 MMK)
    if($amount < 1000) {
        echo json_encode(["status" => "error", "message" => "Minimum withdrawal is 1000 Ks."]);
        exit;
    }

    try {
        $db = (new Database())->getConnection();
        $db->beginTransaction();

        // 1. Lock the user row and check balance
        $stmt = $db->prepare("SELECT mmk_balance FROM users WHERE id = :id FOR UPDATE");
        $stmt->execute(['id' => $user_id]);
        $user = $stmt->fetch();

        if($user['mmk_balance'] < $amount) {
            throw new Exception("Insufficient MMK balance.");
        }

        // 2. Deduct from user
        $deductStmt = $db->prepare("UPDATE users SET mmk_balance = mmk_balance - :amount WHERE id = :id");
        $deductStmt->execute(['amount' => $amount, 'id' => $user_id]);

        // 3. Insert into withdrawals table
        $insertStmt = $db->prepare("INSERT INTO withdrawals (user_id, phone_number, payment_method, amount_mmk, status) VALUES (:uid, :phone, :method, :amount, 'pending')");
        $insertStmt->execute([
            'uid' => $user_id,
            'phone' => htmlspecialchars($data->phone_number),
            'method' => in_array($data->method, ['KPay', 'WaveMoney']) ? $data->method : 'KPay',
            'amount' => $amount
        ]);

        // Update session balance
        $_SESSION['mmk_balance'] -= $amount;

        $db->commit();
        echo json_encode(["status" => "success", "message" => "Withdrawal request submitted.", "new_balance" => $_SESSION['mmk_balance']]);

    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Missing required fields."]);
}
?>