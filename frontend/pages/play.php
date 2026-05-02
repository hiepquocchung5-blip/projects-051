<?php
// /frontend/pages/play.php
// V4 Wrapper - 30s Ad Enforcement & Premium UI

if (!isset($_GET['game'])) { renderSystemError("Simulation Error", "No target specified.", "ERR_NO_TARGET"); return; }

$slug = htmlspecialchars($_GET['game']);
$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT * FROM games WHERE slug = :slug AND is_active = 1 LIMIT 1");
$stmt->execute(['slug' => $slug]);
$gameInfo = $stmt->fetch();

if (!$gameInfo) { renderSystemError("Offline", "Simulation deactivated.", "ERR_DB_MISSING"); return; }

$stmtEvent = $db->query("SELECT coin_multiplier FROM events WHERE is_active = 1 AND NOW() BETWEEN start_time AND end_time LIMIT 1");
$activeEvent = $stmtEvent->fetch();
$multiplier = $activeEvent ? (float)$activeEvent['coin_multiplier'] : 1.0;

$gameFile = 'games/' . $slug . '.php';
?>

<div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-6 w-full relative z-10 h-full min-h-[75vh]">
    <!-- Arena -->
    <div class="w-full lg:w-3/4 glass-panel p-0 rounded-3xl border border-gray-700/50 relative overflow-hidden flex flex-col shadow-2xl h-full">
        <div class="w-full px-6 py-4 flex justify-between items-center bg-black/60 border-b border-gray-800/80 backdrop-blur-md z-30">
            <h2 class="text-lg font-black text-white flex items-center gap-3 tracking-widest uppercase">
                <i data-lucide="<?= $gameInfo['icon'] ?>" class="text-<?= $gameInfo['theme_color'] ?>"></i> <?= $gameInfo['title'] ?> v4
            </h2>
            <div class="font-mono text-[10px] text-gray-400 bg-black/80 px-3 py-1.5 rounded-lg border border-gray-700 flex items-center gap-2 shadow-inner">
                <i data-lucide="clock" size="12"></i> Ad Cycle: <span id="ad-timer-display" class="text-premium-gold font-bold"><?= defined('AD_INTERVAL_SECONDS') ? AD_INTERVAL_SECONDS : 60 ?>s</span>
            </div>
        </div>

        <div id="game-mount" class="w-full flex-grow relative bg-[#050507] flex items-center justify-center p-4 z-10 overflow-hidden">
            <?php 
                if (file_exists($gameFile)) { include_once $gameFile; } 
                else { renderSystemError("File Missing", "Core logic module missing.", "FILE_NOT_FOUND"); }
            ?>
        </div>

        <!-- 30s Video Ad Overlay -->
        <div id="ad-overlay" class="absolute inset-0 bg-black/95 z-[60] hidden flex-col items-center justify-center pointer-events-auto backdrop-blur-2xl">
            <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,_rgba(212,175,55,0.15)_0%,_transparent_70%)] pointer-events-none"></div>
            
            <h3 class="text-sm font-black text-white mb-6 tracking-widest uppercase text-center drop-shadow-md flex items-center gap-2 border border-premium-gold/30 bg-premium-gold/10 px-4 py-2 rounded-full">
                <i data-lucide="play-circle" class="text-premium-gold animate-pulse"></i> Mandatory Sponsor Broadcast
            </h3>
            
            <!-- Video Ad Container -->
            <div id="video-ad-container" class="w-full max-w-2xl aspect-video bg-[#0a0a0c] border border-gray-700 rounded-3xl mb-8 flex items-center justify-center shadow-[0_0_50px_rgba(0,0,0,0.8)] relative overflow-hidden">
                <!-- AD NETWORK SCRIPT GOES HERE -->
                <div class="text-center p-4">
                    <div class="w-16 h-16 rounded-full border-t-2 border-r-2 border-premium-gold animate-spin mx-auto mb-4"></div>
                    <span class="text-gray-500 font-mono text-[10px] font-bold tracking-[0.3em] uppercase block">Awaiting Adsterra VAST Tag...</span>
                </div>
            </div>
            
            <div class="h-14 flex items-center justify-center w-full max-w-2xl">
                <div id="ad-skip-text" class="w-full text-center text-gray-400 font-mono text-sm bg-black/80 px-6 py-4 rounded-xl border border-gray-800 shadow-inner">
                    Assets unlocking in <span id="ad-skip-timer" class="text-premium-gold font-black text-lg ml-2">30</span>s
                </div>
                <button id="ad-close-btn" class="hidden w-full bg-gradient-to-r from-premium-gold to-premium-goldDark text-premium-dark px-8 py-4 rounded-xl font-black shadow-[0_0_20px_rgba(212,175,55,0.3)] transition-all uppercase tracking-widest text-sm active:scale-95" onclick="resumeGame()">
                    <i data-lucide="check-circle" class="inline-block mr-2" size="18"></i> Claim Assets
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Panel -->
    <div class="w-full lg:w-1/4 flex flex-col gap-4">
        <div class="glass-panel p-6 rounded-3xl border-gray-700/50 relative overflow-hidden shadow-xl">
            <?php if($multiplier > 1.0): ?>
                <div class="absolute inset-0 bg-premium-gold/5 animate-pulse pointer-events-none"></div>
            <?php endif; ?>
            <h3 class="text-gray-500 text-[10px] font-bold mb-2 uppercase tracking-widest relative z-10">Base Yield</h3>
            <div class="flex items-end gap-2 relative z-10">
                <p class="text-4xl font-black text-<?= htmlspecialchars($gameInfo['theme_color']) ?> drop-shadow-md tracking-tighter">
                    <?= number_format($gameInfo['base_reward']) ?>
                </p>
                <?php if($multiplier > 1.0): ?>
                    <span class="text-[10px] bg-premium-gold text-premium-dark font-black px-2 py-1 rounded mb-2 shadow-lg tracking-widest">
                        <?= $multiplier ?>x ONLINE
                    </span>
                <?php endif; ?>
            </div>
            <div class="w-full h-px bg-gradient-to-r from-gray-700 to-transparent my-6 relative z-10"></div>
            <p class="text-xs text-gray-400 leading-relaxed font-sans relative z-10"><?= htmlspecialchars($gameInfo['description']) ?></p>
        </div>
        
        <a href="?route=home" class="glass-panel p-5 rounded-2xl border-gray-800 text-center hover:bg-white hover:text-black transition-colors flex items-center justify-center gap-3 text-gray-400 font-bold uppercase text-xs active:scale-95 shadow-lg group">
            <i data-lucide="power" class="group-hover:text-red-500 transition-colors"></i> Terminate Link
        </a>
    </div>
</div>

<script>
    let adInterval = <?= defined('AD_INTERVAL_SECONDS') ? AD_INTERVAL_SECONDS : 60 ?>;
    let currentTimer = adInterval;
    let gameLoopActive = true; 
    let adSkipDuration = 30; // STRICT 30 SECOND REQUIREMENT

    <?php if (file_exists($gameFile)): ?>
    setInterval(() => {
        if(!gameLoopActive) return;
        currentTimer--;
        const timerDisplay = document.getElementById('ad-timer-display');
        if(timerDisplay) timerDisplay.innerText = currentTimer + 's';
        if(currentTimer <= 0) triggerAdInterstitial();
    }, 1000);
    <?php endif; ?>

    function triggerAdInterstitial() {
        gameLoopActive = false; 
        const overlaysToHide = ['bird-overlay', 'bw-overlay', 'jump-overlay', 'worm-overlay', 'nm-overlay', 'mole-overlay'];
        overlaysToHide.forEach(id => { const el = document.getElementById(id); if(el) el.style.opacity = '0'; });

        document.getElementById('ad-overlay').classList.remove('hidden');
        document.getElementById('ad-overlay').style.display = 'flex';
        document.getElementById('ad-close-btn').classList.add('hidden');
        document.getElementById('ad-skip-text').classList.remove('hidden');

        let skipTime = adSkipDuration;
        document.getElementById('ad-skip-timer').innerText = skipTime;
        
        let skipInt = setInterval(() => {
            skipTime--;
            document.getElementById('ad-skip-timer').innerText = skipTime;
            if(skipTime <= 0) {
                clearInterval(skipInt);
                document.getElementById('ad-skip-text').classList.add('hidden');
                document.getElementById('ad-close-btn').classList.remove('hidden');
                
                // Reward API Call via JWT wrapper
                UrbanixAPI.request('wallet', 'POST', {action: 'ad_view', amount: 5000})
                .then(res => { if(window.showToast) window.showToast('Sponsor Assets Secured.', 'success'); })
                .catch(err => console.error(err));
            }
        }, 1000);
    }

    function resumeGame() {
        document.getElementById('ad-overlay').classList.add('hidden');
        document.getElementById('ad-overlay').style.display = 'none';
        const overlaysToHide = ['bird-overlay', 'bw-overlay', 'jump-overlay', 'worm-overlay', 'nm-overlay', 'mole-overlay'];
        overlaysToHide.forEach(id => { const el = document.getElementById(id); if(el) el.style.opacity = '1'; });
        currentTimer = adInterval;
        gameLoopActive = true;
    }
</script>