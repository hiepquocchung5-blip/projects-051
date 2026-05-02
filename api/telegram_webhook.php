<?php
// /api/telegram_webhook.php
// Handles incoming POST requests from Telegram Bot securely

require_once '../config/env_parser.php';
EnvParser::load(__DIR__ . '/../.env');

$botToken = $_ENV['TELEGRAM_BOT_TOKEN'] ?? null;

if (!$botToken) {
    error_log("Telegram Webhook Error: Bot token missing from environment.");
    exit;
}

$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) exit;

require_once '../config/database.php';
$db = (new Database())->getConnection();

if (isset($update["message"])) {
    $chatId = $update["message"]["chat"]["id"];
    $text = trim($update["message"]["text"]);

    if ($text === "/start") {
        sendMessage($chatId, "Welcome to Urbanix! Send your unique Sync Code (e.g. URBX-1234) to establish a secure link.", $botToken);
    } elseif (strpos($text, "URBX-") === 0) {
        sendMessage($chatId, "Command Received. Telemetry link processing...", $botToken);
    }
}

function sendMessage($chatId, $message, $token) {
    $url = "https://api.telegram.org/bot" . $token . "/sendMessage";
    $data = ['chat_id' => $chatId, 'text' => $message];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_exec($ch);
    curl_close($ch);
}
?>