<?php
// /api/routes/withdraw.php
// Endpoint: /api/index.php?route=withdraw

$authUser = JWT::validate();
$userId = $authUser['id'];

if ($method !== 'POST') Response::error("Method not allowed.", 405);

$amount = floatval($requestData['amount'] ?? 0);
$paymentMethod = $requestData['method'] ?? 'KPay';
$phone = htmlspecialchars($requestData['phone_number'] ?? '');

$minWithdraw = $db->query("SELECT setting_value FROM system_settings WHERE setting_key = 'minimum_withdrawal_mmk'")->fetchColumn() ?: 1000;

if($amount < $minWithdraw) Response::error("Minimum extraction is {$minWithdraw} Ks.");
if(empty($phone)) Response::error("Target account required.");

try {
    $db->beginTransaction();

    $stmt = $db->prepare("SELECT mmk_balance FROM users WHERE id = :id FOR UPDATE");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if($user['mmk_balance'] < $amount) throw new Exception("Insufficient MMK liquid assets.");

    $db->prepare("UPDATE users SET mmk_balance = mmk_balance - :amount WHERE id = :id")
       ->execute(['amount' => $amount, 'id' => $userId]);

    $db->prepare("INSERT INTO withdrawals (user_id, phone_number, payment_method, amount_mmk, status) VALUES (:uid, :phone, :method, :amount, 'pending')")
       ->execute([
           'uid' => $userId, 
           'phone' => $phone,
           'method' => in_array($paymentMethod, ['KPay', 'WaveMoney']) ? $paymentMethod : 'KPay', 
           'amount' => $amount
       ]);

    $db->commit();
    $newBal = $user['mmk_balance'] - $amount;
    
    Response::success("Transfer queued for Overseer review.", ["new_balance" => $newBal]);

} catch (Exception $e) {
    if($db->inTransaction()) $db->rollBack();
    Response::error($e->getMessage(), 500);
}
?>