<?php
// /cron/convert.php
// Runs every 5 hours via aaPanel Cron: /www/server/php/80/bin/php /www/wwwroot/adurbanix.online/cron/convert.php

if (php_sapi_name() !== 'cli') {
    die("Permission Denied. CLI execution only.");
}

$rootDir = dirname(__DIR__);

require_once $rootDir . '/config/env_parser.php';
EnvParser::load($rootDir . '/.env');

require_once $rootDir . '/config/database.php';
require_once $rootDir . '/config/globals.php';

echo "[".date('Y-m-d H:i:s')."] Starting Urban Coin to MMK Conversion...\n";

try {
    $db = (new Database())->getConnection();
    $db->beginTransaction();

    $rate = COIN_TO_MMK_RATE; 
    $baseValue = MMK_BASE_VALUE; 
    
    $stmt = $db->prepare("SELECT id, urban_coins FROM users WHERE urban_coins >= :rate");
    $stmt->execute(['rate' => $rate]);
    $users = $stmt->fetchAll();

    $processedCount = 0; $totalMMK = 0;

    foreach ($users as $user) {
        $coinsToConvert = floor($user['urban_coins'] / $rate) * $rate;
        $mmkToAdd = ($coinsToConvert / $rate) * $baseValue;

        $updateStmt = $db->prepare("UPDATE users SET urban_coins = urban_coins - :deduct, mmk_balance = mmk_balance + :add WHERE id = :id");
        $updateStmt->execute(['deduct' => $coinsToConvert, 'add' => $mmkToAdd, 'id' => $user['id']]);

        $processedCount++; $totalMMK += $mmkToAdd;
    }

    $db->commit();
    echo "[SUCCESS] Processed {$processedCount} accounts. Distributed {$totalMMK} MMK.\n";

} catch (Exception $e) {
    if(isset($db)) $db->rollBack();
    echo "[ERROR] Transaction failed: " . $e->getMessage() . "\n";
}
?>