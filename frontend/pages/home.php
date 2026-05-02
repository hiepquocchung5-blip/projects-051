<?php
// /frontend/pages/home.php
// Dynamic Dashboard - No Mock Data

require_once 'components/game_card.php';
requireAuth();

$db = (new Database())->getConnection();

// 1. DYNAMIC FETCH: Active Games
$stmtGames = $db->query("SELECT * FROM games WHERE is_active = 1 ORDER BY id ASC");
$games = $stmtGames->fetchAll();

// 2. DYNAMIC FETCH: Active Events / Multipliers
$stmtEvent = $db->query("SELECT title, coin_multiplier FROM events WHERE is_active = 1 AND NOW() BETWEEN start_time AND end_time LIMIT 1");
$activeEvent = $stmtEvent->fetch();
?>

<div class="w-full">
    
    <?php if($activeEvent): ?>
    <div class="mb-6 p-1 rounded-2xl bg-gradient-to-r from-premium-gold via-white to-premium-gold animate-gradient-x shadow-[0_0_20px_rgba(212,175,55,0.4)]">
        <div class="bg-premium-panel/90 rounded-xl p-5 flex items-center justify-between gap-4 backdrop-blur-md">
            <div class="flex items-center gap-4">
                <i data-lucide="zap" class="text-premium-gold w-6 h-6 animate-pulse"></i>
                <div>
                    <h3 class="text-white font-bold tracking-widest uppercase text-sm"><?= htmlspecialchars($activeEvent['title']) ?></h3>
                    <p class="text-premium-gold text-xs font-mono font-bold mt-1">GLOBAL YIELD MULTIPLIER: <?= number_format($activeEvent['coin_multiplier'], 1) ?>x</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="mb-8 p-8 rounded-3xl bg-black/40 border border-gray-800/50 relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-premium-gold/5 rounded-full blur-3xl pointer-events-none"></div>
        <h1 class="text-3xl md:text-5xl font-bold text-white tracking-wide mb-2">
            The <span class="text-gold-gradient">Urbanix</span> Network
        </h1>
        <p class="text-gray-400 mt-2 text-sm max-w-lg leading-relaxed">
            Initialize a simulation module to generate network assets. Modules are fetched securely from the mainframe.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6 pb-12">
        <?php if(count($games) > 0): ?>
            <?php foreach ($games as $game): ?>
                <?php 
                    // renderGameCard(id, title, description, icon, colorClass, route)
                    renderGameCard(
                        $game['slug'], 
                        $game['title'], 
                        $game['description'], 
                        $game['icon'], 
                        $game['theme_color'], 
                        'play'
                    ); 
                ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full p-10 text-center glass-panel rounded-3xl border-red-900/30">
                <i data-lucide="shield-alert" class="w-12 h-12 text-red-500 mx-auto mb-4"></i>
                <p class="text-red-400 font-mono text-sm uppercase tracking-widest">NO SIMULATIONS ONLINE. CHECK DATABASE CONFIGURATION.</p>
            </div>
        <?php endif; ?>
    </div>
</div>