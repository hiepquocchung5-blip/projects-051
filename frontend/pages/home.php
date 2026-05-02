<?php
// /frontend/pages/home.php
// V4 Dynamic Dashboard - Categorized Layout

require_once 'components/game_card.php';
requireAuth();

$db = (new Database())->getConnection();

// 1. Fetch Active Games
$stmtGames = $db->query("SELECT * FROM games WHERE is_active = 1 ORDER BY id ASC");
$allGames = $stmtGames->fetchAll();

// 2. Fetch Active Events
$stmtEvent = $db->query("SELECT title, coin_multiplier FROM events WHERE is_active = 1 AND NOW() BETWEEN start_time AND end_time LIMIT 1");
$activeEvent = $stmtEvent->fetch();

// 3. Categorize Games programmatically
$kineticGames = [];
$decryptionGames = [];

foreach ($allGames as $game) {
    // Logic & Puzzle Games
    if (in_array($game['slug'], ['tictactoe', 'neonguess', 'dataworm', 'nodematch'])) {
        $decryptionGames[] = $game;
    } else {
        // Action & Survival Games
        $kineticGames[] = $game;
    }
}
?>

<div class="w-full relative z-10 pb-24">
    
    <?php if($activeEvent): ?>
    <div class="mb-8 p-1 rounded-2xl bg-gradient-to-r from-premium-gold via-white to-premium-gold animate-gradient-x shadow-[0_0_30px_rgba(212,175,55,0.4)]">
        <div class="bg-premium-panel/90 rounded-xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4 backdrop-blur-md">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 bg-black rounded-full flex items-center justify-center border border-premium-gold/50 shadow-inner">
                    <i data-lucide="zap" class="text-premium-gold w-5 h-5 animate-pulse"></i>
                </div>
                <div>
                    <h3 class="text-white font-black tracking-widest uppercase text-sm"><?= htmlspecialchars($activeEvent['title']) ?></h3>
                    <p class="text-premium-gold text-[10px] font-mono font-bold mt-1">GLOBAL YIELD MULTIPLIER: <?= number_format($activeEvent['coin_multiplier'], 1) ?>x</p>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="mb-12 p-8 md:p-10 rounded-3xl bg-[#050507]/80 border border-gray-800/80 relative overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] backdrop-blur-md">
        <div class="absolute top-0 right-0 w-64 h-64 bg-premium-gold/10 rounded-full blur-[80px] pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-full h-px bg-gradient-to-r from-transparent via-gray-700 to-transparent"></div>
        
        <h1 class="text-3xl md:text-5xl font-black text-white tracking-widest mb-3 uppercase drop-shadow-md">
            The <span class="text-transparent bg-clip-text bg-gradient-to-r from-premium-gold to-premium-goldDark">Urbanix</span> Network
        </h1>
        <p class="text-gray-400 mt-2 text-xs md:text-sm max-w-xl leading-relaxed font-sans">
            Welcome, Operative <span class="text-white font-bold"><?= htmlspecialchars($_SESSION['username']) ?></span>. Select a simulation module to begin asset extraction. Modules are categorized by operational parameters.
        </p>
    </div>

    <?php if(count($kineticGames) > 0): ?>
    <div class="mb-12 relative">
        <div class="flex items-center gap-4 mb-6 border-b border-gray-800/50 pb-4">
            <div class="w-12 h-12 bg-red-900/20 border border-red-500/30 rounded-xl flex items-center justify-center shadow-inner">
                <i data-lucide="crosshair" class="text-red-500 w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-widest">Kinetic Operations</h2>
                <p class="text-[10px] text-gray-500 font-mono tracking-widest uppercase">Action / Reflex / Survival</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($kineticGames as $game): ?>
                <?php renderGameCard($game['slug'], $game['title'], $game['description'], $game['icon'], $game['theme_color'], 'play'); ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if(count($decryptionGames) > 0): ?>
    <div class="mb-12 relative">
        <div class="flex items-center gap-4 mb-6 border-b border-gray-800/50 pb-4">
            <div class="w-12 h-12 bg-blue-900/20 border border-blue-500/30 rounded-xl flex items-center justify-center shadow-inner">
                <i data-lucide="cpu" class="text-blue-500 w-6 h-6"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-widest">Decryption Protocols</h2>
                <p class="text-[10px] text-gray-500 font-mono tracking-widest uppercase">Logic / Puzzle / Memory</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($decryptionGames as $game): ?>
                <?php renderGameCard($game['slug'], $game['title'], $game['description'], $game['icon'], $game['theme_color'], 'play'); ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>