<?php
// /admin/pages/system.php
// Economy & Global Logic Control

$db = (new Database())->getConnection();

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_system'])) {
    foreach ($_POST['settings'] as $key => $value) {
        $stmt = $db->prepare("UPDATE system_settings SET setting_value = :val WHERE setting_key = :key");
        $stmt->execute(['val' => $value, 'key' => $key]);
    }
    echo "<div class='bg-green-900/20 border border-green-500 text-green-400 p-4 rounded-xl mb-6 font-mono text-xs'>> SYSTEM CONFIGURATION UPDATED SUCCESSFULLY.</div>";
}

// Fetch Settings
$settings = $db->query("SELECT * FROM system_settings")->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<div class="mb-8 border-b border-gray-800 pb-4">
    <h2 class="text-xl font-bold text-white uppercase tracking-widest font-mono">System Parameters</h2>
</div>

<form method="POST" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <input type="hidden" name="update_system" value="1">
    
    <div class="admin-panel p-8 rounded-3xl border-gray-800">
        <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-6 flex items-center gap-2">
            <i data-lucide="trending-up" class="text-premium-gold" size="18"></i> Economy Multipliers
        </h3>
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2">Coins per 1,000 MMK</label>
                <input type="number" name="settings[coin_to_mmk_rate]" value="<?= $settings['coin_to_mmk_rate'] ?>" class="w-full bg-black border border-gray-800 rounded-xl p-4 text-white font-mono focus:border-premium-gold outline-none">
            </div>
            <div>
                <label class="block text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2">Minimum Withdrawal (MMK)</label>
                <input type="number" name="settings[minimum_withdrawal_mmk]" value="<?= $settings['minimum_withdrawal_mmk'] ?>" class="w-full bg-black border border-gray-800 rounded-xl p-4 text-white font-mono focus:border-premium-gold outline-none">
            </div>
        </div>
    </div>

    <div class="admin-panel p-8 rounded-3xl border-gray-800">
        <h3 class="text-sm font-bold text-white uppercase tracking-widest mb-6 flex items-center gap-2">
            <i data-lucide="monitor-play" class="text-premium-gold" size="18"></i> Monetization Logic
        </h3>
        <div class="space-y-6">
            <div>
                <label class="block text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2">Ad Interval (Seconds)</label>
                <input type="number" name="settings[ad_interval_seconds]" value="<?= $settings['ad_interval_seconds'] ?>" class="w-full bg-black border border-gray-800 rounded-xl p-4 text-white font-mono focus:border-premium-gold outline-none">
            </div>
            <button type="submit" class="w-full bg-white text-black font-black py-4 rounded-xl mt-4 hover:bg-premium-gold transition-all uppercase tracking-widest text-xs active:scale-95 shadow-xl">
                Deploy System Updates
            </button>
        </div>
    </div>
</form>