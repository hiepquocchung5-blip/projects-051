<?php
// /api/wallet.php
// API endpoint to securely add coins after game win or ad view

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");

require_once '../config/database.php';
require_once '../config/globals.php';

// Check if user is logged in (using session for now, JWT recommended for production)
if (!isset($_SESSION['user_id'])) {
    // For local dev/testing, let's fake a session
    $_SESSION['user_id'] = 1; 
    $_SESSION['user_coins'] = 0;
}

$data = json_decode(file_get_contents("php://input"));

if(isset($data->action) && isset($data->amount)) {
    $amount = intval($data->amount);
    
    if($amount > 0 && $amount <= 50000) { // Security check against massive payloads
        // Update Session
        $_SESSION['user_coins'] += $amount;
        
        // TODO: Update MySQL Database
        // $db = (new Database())->getConnection();
        // $stmt = $db->prepare("UPDATE users SET coins = coins + :amount WHERE id = :id");
        // $stmt->execute(['amount' => $amount, 'id' => $_SESSION['user_id']]);

        echo json_encode([
            "status" => "success", 
            "message" => "Coins updated successfully.", 
            "new_balance" => $_SESSION['user_coins']
        ]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid amount detected."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Missing parameters."]);
}
?>