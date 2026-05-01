<?php
// /frontend/pages/leaderboard.php
// Public Leaderboard ranking top coin holders

require_once '../config/database.php';
$db = (new Database())->getConnection();

// Fetch Top 10 Users by Urban Coins
$stmt = $db->query("SELECT username, urban_coins FROM users ORDER BY urban_coins DESC LIMIT 10");
$topUsers = $stmt->fetchAll();
?>

<div class="max-w-4xl mx-auto">
    <div class="mb-8 flex items-center justify-between border-b border-neon-purple/30 pb-4">
        <div>
            <h1 class="text-3xl font-black text-neon-purple uppercase drop-shadow-[0_0_8px_#b026ff]">Global Rankings</h1>
            <p class="text-gray-400 font-mono mt-1">Top operatives by Urban Coin accumulation.</p>
        </div>
        <i data-lucide="trophy" class="w-10 h-10 text-yellow-400 drop-shadow-[0_0_8px_#facc15]"></i>
    </div>

    <div class="glass-panel rounded-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-900 border-b border-gray-800 text-gray-400 font-mono text-sm">
                    <th class="p-4">Rank</th>
                    <th class="p-4">Operative Alias</th>
                    <th class="p-4 text-right">Urban Coins</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($topUsers) > 0): ?>
                    <?php foreach ($topUsers as $index => $user): ?>
                        <tr class="border-b border-gray-800/50 hover:bg-white/5 transition group">
                            <td class="p-4 font-mono font-bold text-gray-500 group-hover:text-white">
                                #<?= $index + 1 ?>
                            </td>
                            <td class="p-4 font-bold text-white flex items-center gap-2">
                                <?php if($index === 0): ?> <i data-lucide="crown" class="w-4 h-4 text-yellow-400"></i> <?php endif; ?>
                                <?= htmlspecialchars($user['username']) ?>
                            </td>
                            <td class="p-4 text-right font-mono text-neon-cyan font-bold">
                                <?= number_format($user['urban_coins']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" class="p-8 text-center text-gray-500 font-mono">No data available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>