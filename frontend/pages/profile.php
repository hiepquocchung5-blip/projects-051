<?php
// /frontend/pages/profile.php
// Premium Mobile-First Profile & Affiliate Network

requireAuth(); 

$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

$transStmt = $db->prepare("SELECT * FROM transactions WHERE user_id = :id ORDER BY created_at DESC LIMIT 10");
$transStmt->execute(['id' => $_SESSION['user_id']]);
$transactions = $transStmt->fetchAll();

// Generate Referral Link
$refLink = (defined('BASE_URL') ? BASE_URL : '') . "?route=auth&ref=" . urlencode($user['username']);
?>

<div class="mb-6 pb-4 border-b border-gray-800/50 flex items-center justify-between">
    <h2 class="text-2xl md:text-3xl font-bold text-white tracking-wide flex items-center gap-3">
        <i data-lucide="fingerprint" class="text-premium-gold w-8 h-8"></i> Operative Data
    </h2>
</div>

<div class="flex flex-col lg:flex-row gap-6 w-full pb-20">
    <div class="w-full lg:w-1/3 flex flex-col gap-6">
        
        <!-- Asset Card -->
        <div class="glass-panel p-6 rounded-3xl relative overflow-hidden group shadow-2xl">
            <div class="absolute top-0 right-0 w-40 h-40 bg-premium-gold/5 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none group-hover:bg-premium-gold/10 transition-all"></div>
            
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-800/50">
                <div class="w-16 h-16 rounded-2xl bg-black border border-gray-700 flex items-center justify-center shadow-inner">
                    <i data-lucide="user" class="w-8 h-8 text-premium-gold"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white tracking-wide"><?= htmlspecialchars($user['username']) ?></h2>
                    <div class="inline-flex items-center gap-1 mt-1 bg-gray-900 px-2 py-1 rounded text-[10px] text-gray-400 font-mono border border-gray-800">
                        OP_ID: URBX-<?= $user['id'] ?>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-black/40 p-4 rounded-2xl border border-gray-800/50 flex justify-between items-center">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Network Coins</p>
                    <p class="text-xl font-mono font-bold text-premium-gold"><?= number_format($user['urban_coins']) ?></p>
                </div>
                <div class="bg-black/40 p-4 rounded-2xl border border-gray-800/50 flex justify-between items-center">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Liquid (MMK)</p>
                    <p class="text-xl font-mono font-bold text-white"><?= number_format($user['mmk_balance']) ?></p>
                </div>
                
                <button onclick="openWithdrawModal()" class="w-full bg-premium-silver text-premium-dark py-4 rounded-xl hover:bg-white transition-all uppercase text-xs font-bold flex justify-center items-center gap-2 active:scale-95 shadow-lg mt-2">
                    Extract Funds <i data-lucide="arrow-up-right" size="16"></i>
                </button>
            </div>
        </div>

        <!-- Affiliate Network Card -->
        <div class="glass-panel p-6 rounded-3xl relative overflow-hidden shadow-2xl border-premium-gold/30">
            <h3 class="text-premium-gold font-bold mb-4 uppercase tracking-widest text-sm flex items-center gap-2">
                <i data-lucide="network" class="w-4 h-4"></i> Affiliate Protocol
            </h3>
            <p class="text-xs text-gray-400 mb-4 font-sans leading-relaxed">Recruit new operatives. Yield 50,000 Coins per successful integration.</p>
            
            <div class="bg-black/60 border border-gray-700 p-4 rounded-2xl relative shadow-inner mb-4 flex items-center justify-between gap-2">
                <input type="text" id="ref-link" value="<?= $refLink ?>" readonly class="bg-transparent text-gray-300 font-mono text-[10px] w-full outline-none truncate">
                <button onclick="copyReferral()" class="text-premium-gold hover:text-white transition-colors shrink-0 p-2 bg-gray-800 rounded-lg">
                    <i data-lucide="copy" size="16"></i>
                </button>
            </div>
        </div>

    </div>

    <!-- Telemetry Logs -->
    <div class="w-full lg:w-2/3 glass-panel p-6 rounded-3xl shadow-2xl">
        <h3 class="text-lg font-bold text-white mb-6 uppercase tracking-widest border-b border-gray-800 pb-4 flex items-center gap-3">
            <i data-lucide="activity" class="text-gray-400"></i> Telemetry Logs
        </h3>

        <div class="overflow-x-auto pb-4 custom-scrollbar">
            <table class="w-full text-left text-sm font-mono whitespace-nowrap min-w-[500px]">
                <thead class="text-gray-500 border-b border-gray-800/50 bg-black/20">
                    <tr>
                        <th class="p-4 rounded-tl-lg text-xs font-bold uppercase tracking-wider">Timestamp</th>
                        <th class="p-4 text-xs font-bold uppercase tracking-wider">Source</th>
                        <th class="p-4 rounded-tr-lg text-right text-xs font-bold uppercase tracking-wider">Delta</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300 divide-y divide-gray-800/30">
                    <?php if(count($transactions) > 0): ?>
                        <?php foreach($transactions as $tx): ?>
                            <tr class="hover:bg-white/5 transition-colors group">
                                <td class="p-4 text-xs text-gray-500"><?= date('M d, H:i', strtotime($tx['created_at'])) ?></td>
                                <td class="p-4 text-[10px] uppercase text-gray-300 flex items-center gap-2 mt-1">
                                    <span class="bg-gray-800 px-2 py-1 rounded border border-gray-700"><?= htmlspecialchars($tx['source']) ?></span>
                                </td>
                                <td class="p-4 text-right font-bold text-white group-hover:text-premium-gold transition-colors">+<?= number_format($tx['amount']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="p-8 text-center text-gray-600 bg-black/20 rounded-b-lg border-t border-gray-800/50">No telemetry data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function copyReferral() {
        const link = document.getElementById('ref-link');
        link.select();
        document.execCommand('copy');
        if(window.showToast) window.showToast('Affiliate Link Copied', 'success');
    }
</script>