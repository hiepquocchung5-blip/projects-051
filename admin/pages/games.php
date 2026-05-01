<?php
// /admin/pages/games.php
// CMS module to manage active games, offline statuses, and reward pools

$db = (new Database())->getConnection();

// Handle form submissions for updating games
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_game'])) {
    $gameId = intval($_POST['game_id']);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    $baseReward = intval($_POST['base_reward']);

    $updateStmt = $db->prepare("UPDATE games SET is_active = :status, base_reward = :reward WHERE id = :id");
    $updateStmt->execute(['status' => $isActive, 'reward' => $baseReward, 'id' => $gameId]);
    
    echo "<div class='bg-green-900/20 border border-green-500 text-green-400 p-3 rounded mb-6 font-mono text-sm'>SYSTEM UPDATED: Game settings saved successfully.</div>";
}

// Fetch all games
$stmt = $db->query("SELECT * FROM games ORDER BY id ASC");
$games = $stmt->fetchAll();
?>

<div class="mb-6 flex justify-between items-center border-b border-gray-800 pb-4">
    <h2 class="text-2xl font-bold text-white uppercase tracking-widest font-mono">Simulation Matrix Control</h2>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach($games as $game): ?>
        <form method="POST" class="admin-panel p-6 rounded-xl border-t-4 border-<?= htmlspecialchars($game['theme_color']) ?> relative">
            <input type="hidden" name="update_game" value="1">
            <input type="hidden" name="game_id" value="<?= $game['id'] ?>">
            
            <div class="flex items-center gap-3 mb-4">
                <i data-lucide="<?= $game['icon'] ?>" class="text-gray-400"></i>
                <h3 class="text-lg font-bold text-white"><?= htmlspecialchars($game['title']) ?></h3>
            </div>
            
            <p class="text-xs text-gray-500 font-mono mb-4 h-8"><?= htmlspecialchars($game['description']) ?></p>
            
            <div class="space-y-4 border-t border-gray-800 pt-4">
                <!-- Base Reward Modifier -->
                <div>
                    <label class="block text-xs text-gray-400 font-mono mb-1">Base Reward (Coins)</label>
                    <input type="number" name="base_reward" value="<?= $game['base_reward'] ?>" class="w-full bg-black border border-gray-700 rounded px-3 py-2 text-white font-mono focus:border-neon-red outline-none transition">
                </div>
                
                <!-- Status Toggle -->
                <div class="flex items-center justify-between">
                    <label class="text-xs text-gray-400 font-mono">System Status</label>
                    <label class="relative inline-flex items-center cursor-pointer">
                      <input type="checkbox" name="is_active" class="sr-only peer" <?= $game['is_active'] ? 'checked' : '' ?>>
                      <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-green-500"></div>
                      <span class="ml-3 text-xs font-mono font-bold <?= $game['is_active'] ? 'text-green-500' : 'text-red-500' ?>">
                          <?= $game['is_active'] ? 'ONLINE' : 'OFFLINE' ?>
                      </span>
                    </label>
                </div>
            </div>
            
            <button type="submit" class="w-full mt-6 bg-gray-800 hover:bg-neon-red text-white py-2 rounded transition font-mono text-sm uppercase tracking-wider font-bold">
                Deploy Changes
            </button>
        </form>
    <?php endforeach; ?>
</div>