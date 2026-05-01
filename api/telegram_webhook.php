<?php
// /api/telegram_webhook.php
// Handles incoming POST requests from Telegram Bot

$botToken = "YOUR_BOT_TOKEN_HERE"; // Set in global config for production

// Get raw POST body from Telegram
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    exit;
}

require_once '../config/database.php';
$db = (new Database())->getConnection();

if (isset($update["message"])) {
    $chatId = $update["message"]["chat"]["id"];
    $text = trim($update["message"]["text"]);

    // Basic Command Router
    if ($text === "/start") {
        sendMessage($chatId, "Welcome to Urbanix! Please send your unique Sync Code (e.g. URBX-1234) to link your account.", $botToken);
    } 
    elseif (strpos($text, "URBX-") === 0) {
        // Handle linking logic
        $codeParts = explode('-', $text);
        // Add logic here to verify code against DB and link $chatId to the user row
        sendMessage($chatId, "Account Successfully Linked! We will notify you here when your MMK withdrawals are approved.", $botToken);
    }
}

// Helper Function to send messages back
function sendMessage($chatId, $message, $token) {
    $url = "https://api.telegram.org/bot" . $token . "/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);
}
?>