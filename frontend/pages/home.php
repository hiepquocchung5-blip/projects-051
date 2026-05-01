<?php
// /frontend/pages/home.php
// Dashboard - Upgraded with Daily Bonus Claim
require_once 'components/game_card.php';

// Check Daily Bonus Eligibility
$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT created_at FROM transactions WHERE user_id = :id AND source = 'daily_login' ORDER BY created_at DESC LIMIT 1");
$stmt->execute(['id' => $_SESSION['user_id']]);
$lastClaim = $stmt->fetchColumn();

// Eligible if no claim exists, or if the last claim was before today (server time)
$canClaim = !$lastClaim || (date('Y-m-d', strtotime($lastClaim)) < date('Y-m-d'));
?>

<div class="w-full">
    
    <!-- Daily Bonus Banner -->
    <?php if($canClaim): ?>
    <div id="daily-bonus-banner" class="mb-6 p-1 rounded-2xl bg-gradient-to-r from-premium-gold via-premium-goldDark to-premium-gold animate-gradient-x shadow-[0_0_20px_rgba(212,175,55,0.3)]">
        <div class="bg-premium-panel/90 rounded-xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 backdrop-blur-md">
            <div class="flex items-center gap-4 text-center sm:text-left">
                <div class="w-12 h-12 bg-premium-gold/20 rounded-full flex items-center justify-center border border-premium-gold">
                    <i data-lucide="gift" class="text-premium-gold"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold tracking-widest uppercase">Daily Asset Drop Ready</h3>
                    <p class="text-gray-400 text-xs font-mono mt-1">+10,000 Network Coins available for extraction.</p>
                </div>
            </div>
            <button onclick="claimDailyBonus()" id="claim-btn" class="w-full sm:w-auto bg-premium-gold text-premium-dark px-8 py-3 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-white transition-colors active:scale-95">
                Initialize Claim
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="mb-8 p-8 rounded-3xl bg-black/40 border border-gray-800/50 relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 w-64 h-64 bg-premium-gold/5 rounded-full blur-3xl pointer-events-none"></div>
        <h1 class="text-3xl md:text-5xl font-bold text-white tracking-wide mb-2">
            The <span class="text-gold-gradient">Urbanix</span> Network
        </h1>
        <p class="text-gray-400 mt-2 text-sm max-w-lg leading-relaxed">
            Initialize a simulation module to generate network assets. Ensure your connection is secure.
        </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
        <?php
            $games = [
                ['id' => 'tictactoe', 'title' => 'Quantum Tic-Tac', 'desc' => 'Logic combat against system AI.', 'icon' => 'grid-3x3', 'color' => 'premium-gold'],
                ['id' => 'cybermole', 'title' => 'Cyber-Mole', 'desc' => 'High-speed target neutralization.', 'icon' => 'target', 'color' => 'gray-300'],
                ['id' => 'urbanbird', 'title' => 'Urban Flight', 'desc' => 'Navigate the firewall.', 'icon' => 'plane-takeoff', 'color' => 'premium-silver'],
                ['id' => 'neonguess', 'title' => 'Encryption Breach', 'desc' => 'Logic decryption protocol.', 'icon' => 'terminal', 'color' => 'premium-goldDark'],
                ['id' => 'gridwars', 'title' => 'Grid Wars Lite', 'desc' => 'Arena survival combat.', 'icon' => 'crosshair', 'color' => 'red-500']
            ];

            foreach ($games as $game) {
                renderGameCard($game['id'], $game['title'], $game['desc'], $game['icon'], $game['color'], 'play');
            }
        ?>
    </div>
</div>

<script>
function claimDailyBonus() {
    const btn = document.getElementById('claim-btn');
    btn.disabled = true;
    btn.innerHTML = `<i data-lucide="loader" class="animate-spin w-4 h-4"></i>`;
    lucide.createIcons();

    fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/daily_bonus.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' }
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') {
            document.getElementById('daily-bonus-banner').innerHTML = `
                <div class="bg-green-900/30 rounded-xl p-6 text-center border border-green-500/50 backdrop-blur-md">
                    <p class="text-green-400 font-bold uppercase tracking-widest text-sm flex items-center justify-center gap-2">
                        <i data-lucide="check-circle" size="18"></i> ${data.message}
                    </p>
                </div>
            `;
            lucide.createIcons();
            // Optional: update header balance without reload
            setTimeout(() => location.reload(), 2000);
        } else {
            alert(data.message);
            btn.disabled = false;
            btn.innerHTML = "Initialize Claim";
        }
    });
}
</script>