<?php
// /frontend/pages/play.php
// Wraps specific games, handles Ad timers, and verifies DB game data

if (!isset($_GET['game'])) {
    echo "<h2 class='text-red-500 text-center text-2xl mt-10 font-black'>ERR: NO_GAME_SPECIFIED</h2>";
    exit;
}

$slug = htmlspecialchars($_GET['game']);

// Fetch Game Details from DB
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT * FROM games WHERE slug = :slug AND is_active = 1 LIMIT 1");
$stmt->execute(['slug' => $slug]);
$gameInfo = $stmt->fetch();

if (!$gameInfo) {
    echo "<h2 class='text-neon-purple text-center text-2xl mt-10 font-mono'>ERR: SIMULATION_OFFLINE</h2>";
    exit;
}

$gameFile = 'games/' . $slug . '.php';
?>

<div class="max-w-5xl mx-auto flex flex-col md:flex-row gap-6">
    
    <!-- Game Container -->
    <div class="w-full md:w-3/4 glass-panel p-2 rounded-xl border-<?= $gameInfo['theme_color'] ?> relative overflow-hidden min-h-[600px]">
        
        <!-- Top Bar -->
        <div class="absolute top-0 left-0 w-full p-4 flex justify-between items-center bg-black/50 border-b border-gray-800 z-20">
            <h2 class="text-xl font-bold text-<?= $gameInfo['theme_color'] ?> flex items-center gap-2">
                <i data-lucide="<?= $gameInfo['icon'] ?>"></i> <?= $gameInfo['title'] ?>
            </h2>
            <div class="font-mono text-sm">
                Ad in: <span id="ad-timer-display" class="text-yellow-400 font-bold"><?= AD_INTERVAL_SECONDS ?>s</span>
            </div>
        </div>

        <!-- The Actual Game Mount Point -->
        <div id="game-mount" class="w-full h-full pt-16 flex items-center justify-center">
            <?php 
                if (file_exists($gameFile)) {
                    include_once $gameFile; 
                } else {
                    echo "<p class='text-gray-500 font-mono'>Game logic file missing.</p>";
                }
            ?>
        </div>

        <!-- Ad Interstitial Overlay (Hidden by default) -->
        <div id="ad-overlay" class="absolute inset-0 bg-black/95 z-50 hidden flex-col items-center justify-center">
            <h3 class="text-3xl font-black text-yellow-500 mb-4 animate-pulse">SPONSORED TRANSMISSION</h3>
            <div class="w-80 h-64 bg-gray-900 border border-yellow-500 mb-4 flex items-center justify-center">
                <span class="text-gray-500 font-mono">[ Adsterra / AppLovin Ad Unit ]</span>
            </div>
            <p id="ad-skip-text" class="text-white font-mono mb-4">Watching transmission... <span id="ad-skip-timer">5</span>s</p>
            <button id="ad-close-btn" class="hidden bg-yellow-500 text-black px-6 py-2 rounded font-bold hover:bg-yellow-400 transition" onclick="resumeGame()">Return to Simulation</button>
        </div>
    </div>

    <!-- Sidebar / Stats -->
    <div class="w-full md:w-1/4 flex flex-col gap-4">
        <div class="glass-panel p-4 rounded-xl border-gray-800">
            <h3 class="text-gray-400 text-xs font-mono mb-2">REWARD POOL</h3>
            <p class="text-2xl font-black text-neon-cyan"><?= number_format($gameInfo['base_reward']) ?> <span class="text-sm">Coins</span></p>
            <p class="text-xs text-gray-500 mt-2"><?= $gameInfo['description'] ?></p>
        </div>
        
        <button onclick="window.location.href='<?= BASE_URL ?>'" class="glass-panel p-4 rounded-xl border-gray-800 text-center hover:bg-gray-800 transition flex items-center justify-center gap-2 text-red-400">
            <i data-lucide="power"></i> Disconnect
        </button>
    </div>
</div>

<script>
    // Global Ad Controller for Gameplay
    let adInterval = <?= AD_INTERVAL_SECONDS ?>;
    let currentTimer = adInterval;
    let gameLoopActive = true; // Games should check this before moving

    setInterval(() => {
        if(!gameLoopActive) return;
        currentTimer--;
        document.getElementById('ad-timer-display').innerText = currentTimer + 's';
        
        if(currentTimer <= 0) {
            triggerAdInterstitial();
        }
    }, 1000);

    function triggerAdInterstitial() {
        gameLoopActive = false; // Pause game logic
        document.getElementById('ad-overlay').classList.remove('hidden');
        document.getElementById('ad-close-btn').classList.add('hidden');
        document.getElementById('ad-skip-text').classList.remove('hidden');
        
        // Call your Adsterra/Applovin API here
        console.log("Triggering Ad Network...");

        let skipTime = 5;
        let skipInt = setInterval(() => {
            skipTime--;
            document.getElementById('ad-skip-timer').innerText = skipTime;
            if(skipTime <= 0) {
                clearInterval(skipInt);
                document.getElementById('ad-skip-text').classList.add('hidden');
                document.getElementById('ad-close-btn').classList.remove('hidden');
                
                // Reward for watching ad
                fetch('<?= API_URL ?>/wallet.php', {
                    method: 'POST', body: JSON.stringify({action: 'ad_view', amount: 5000})
                });
            }
        }, 1000);
    }

    function resumeGame() {
        document.getElementById('ad-overlay').classList.add('hidden');
        currentTimer = adInterval;
        gameLoopActive = true;
    }
</script>