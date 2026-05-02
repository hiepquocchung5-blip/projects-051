<?php
// /frontend/pages/leaderboard.php
// Premium Metallic Leaderboard UI

requireAuth(); // Ensure user is logged in to view rankings
require_once '../config/database.php';

$db = (new Database())->getConnection();
$stmt = $db->query("SELECT username, urban_coins FROM users ORDER BY urban_coins DESC LIMIT 20");
$topUsers = $stmt->fetchAll();
?>

<div class="max-w-4xl mx-auto w-full relative z-10 pb-20">
    
    <!-- Premium Header -->
    <div class="mb-8 p-8 rounded-3xl bg-gradient-to-r from-premium-goldDark/20 to-transparent border border-premium-gold/30 backdrop-blur-md flex items-center justify-between overflow-hidden relative shadow-[0_10px_40px_rgba(212,175,55,0.15)]">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI0IiBoZWlnaHQ9IjQiPgo8cmVjdCB3aWR0aD0iNCIgaGVpZ2h0PSI0IiBmaWxsPSIjMDAwIiBmaWxsLW9wYWNpdHk9IjAiLz4KPHBhdGggZD0iTTAgMGgxdjRIMGpNMCAwaDR2MUgweiIgZmlsbD0iI2ZmZiIgZmlsbC1vcGFjaXR5PSIwLjA1Ii8+Cjwvc3ZnPg==')] opacity-30"></div>
        <div class="absolute top-0 right-0 w-64 h-64 bg-premium-gold/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="z-10">
            <h1 class="text-3xl md:text-5xl font-black text-white uppercase tracking-widest drop-shadow-md">Global <span class="text-gold-gradient">Rankings</span></h1>
            <p class="text-gray-300 font-mono mt-2 text-xs md:text-sm max-w-md leading-relaxed">Top operatives classified by total Asset Generation across all simulation vectors.</p>
        </div>
        <i data-lucide="trophy" class="hidden md:block w-20 h-20 text-premium-gold drop-shadow-[0_0_20px_rgba(212,175,55,0.5)] z-10 opacity-90 animate-pulse"></i>
    </div>

    <!-- Data Table -->
    <div class="glass-panel rounded-3xl overflow-hidden shadow-2xl border border-gray-700/80">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[500px]">
                <thead class="bg-black/80 border-b border-gray-800 backdrop-blur-xl">
                    <tr class="text-gray-500 font-mono text-[10px] tracking-widest uppercase">
                        <th class="p-5 font-bold">Rank</th>
                        <th class="p-5 font-bold">Operative Alias</th>
                        <th class="p-5 text-right font-bold">Total Asset Volume</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50 bg-premium-panel/40">
                    <?php if (count($topUsers) > 0): ?>
                        <?php foreach ($topUsers as $index => $user): ?>
                            <?php 
                                // Premium Podium Styling
                                $rankColor = 'text-gray-600'; $rowBg = 'hover:bg-white/[0.02]'; $icon = 'user';
                                if($index === 0) { $rankColor = 'text-premium-gold drop-shadow-[0_0_8px_#d4af37]'; $rowBg = 'bg-premium-gold/5 border-l-4 border-premium-gold'; $icon = 'crown'; }
                                elseif($index === 1) { $rankColor = 'text-premium-silver drop-shadow-[0_0_8px_#e2e8f0]'; $rowBg = 'bg-premium-silver/5 border-l-4 border-premium-silver'; $icon = 'award'; }
                                elseif($index === 2) { $rankColor = 'text-orange-500 drop-shadow-[0_0_8px_#f97316]'; $rowBg = 'bg-orange-500/5 border-l-4 border-orange-500'; $icon = 'medal'; }
                            ?>
                            <tr class="<?= $rowBg ?> transition-colors duration-300 group">
                                <td class="p-5 font-mono font-black <?= $rankColor ?> text-lg w-20">
                                    #<?= $index + 1 ?>
                                </td>
                                <td class="p-5 font-bold text-white flex items-center gap-4 text-sm md:text-base tracking-wide">
                                    <div class="w-8 h-8 rounded-lg bg-black/50 border border-gray-700 flex items-center justify-center shadow-inner">
                                        <i data-lucide="<?= $icon ?>" class="w-4 h-4 <?= $rankColor ?>"></i>
                                    </div>
                                    <?= htmlspecialchars($user['username']) ?>
                                    <?php if($user['username'] === $_SESSION['username']): ?>
                                        <span class="text-[9px] bg-white text-black px-2 py-0.5 rounded font-black tracking-widest uppercase ml-2">You</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-5 text-right font-mono text-gray-300 font-black tracking-wider text-sm md:text-lg group-hover:text-premium-gold transition-colors">
                                    <?= number_format($user['urban_coins']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="p-10 text-center text-gray-500 font-mono text-xs tracking-widest uppercase bg-black/20">No data available in the registry.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>