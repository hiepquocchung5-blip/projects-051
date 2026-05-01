<?php
// /cron/convert.php
// Automated script to run every 5 hours via crontab
// Command: 0 */5 * * * /usr/bin/php /path/to/urbanix/cron/convert.php

// Prevent HTTP execution (Security)
if (php_sapi_name() !== 'cli') {
    die("Permission Denied. CLI execution only.");
}

require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/config/globals.php';

echo "[".date('Y-m-d H:i:s')."] Starting Urban Coin to MMK Conversion...\n";

try {
    $db = (new Database())->getConnection();
    
    // Begin Transaction to ensure data integrity
    $db->beginTransaction();

    // Find users who have enough coins to convert
    $rate = COIN_TO_MMK_RATE; // 10,000,000
    $baseValue = MMK_BASE_VALUE; // 1000
    
    // Select users with at least 10M coins
    $stmt = $db->prepare("SELECT id, urban_coins, mmk_balance FROM users WHERE urban_coins >= :rate");
    $stmt->execute(['rate' => $rate]);
    $users = $stmt->fetchAll();

    $processedCount = 0;
    $totalMMKDistributed = 0;

    foreach ($users as $user) {
        $coinsToConvert = floor($user['urban_coins'] / $rate) * $rate; // Round down to nearest 10M
        $mmkToAdd = ($coinsToConvert / $rate) * $baseValue;

        // Update User Balance
        $updateStmt = $db->prepare("
            UPDATE users 
            SET urban_coins = urban_coins - :deduct_coins, 
                mmk_balance = mmk_balance + :add_mmk 
            WHERE id = :id
        ");
        
        $updateStmt->execute([
            'deduct_coins' => $coinsToConvert,
            'add_mmk' => $mmkToAdd,
            'id' => $user['id']
        ]);

        $processedCount++;
        $totalMMKDistributed += $mmkToAdd;
    }

    $db->commit();
    echo "[SUCCESS] Processed {$processedCount} accounts. Distributed {$totalMMKDistributed} MMK.\n";

} catch (Exception $e) {
    if(isset($db)) $db->rollBack();
    echo "[ERROR] Transaction failed: " . $e->getMessage() . "\n";
}
?>