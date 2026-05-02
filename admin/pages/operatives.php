<?php
// /admin/pages/operatives.php
// Executive User Management - Updated with Edit Link

$db = (new Database())->getConnection();
$stmt = $db->query("SELECT id, username, email, urban_coins, mmk_balance, role, last_login FROM users ORDER BY created_at DESC");
$operatives = $stmt->fetchAll();
?>

<div class="mb-8 flex justify-between items-center border-b border-gray-800 pb-4">
    <h2 class="text-xl font-bold text-white uppercase tracking-widest font-mono">Registry: Operative Data</h2>
</div>

<div class="admin-panel rounded-2xl overflow-hidden border border-gray-800 shadow-2xl">
    <table class="w-full text-left text-sm">
        <thead class="bg-black/40 text-gray-500 text-[10px] uppercase tracking-widest font-bold border-b border-gray-800">
            <tr>
                <th class="p-5">Status</th>
                <th class="p-5">Alias / Email</th>
                <th class="p-5">Asset Volume (Coins)</th>
                <th class="p-5">MMK Balance</th>
                <th class="p-5">Last Uplink</th>
                <th class="p-5 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800/50">
            <?php foreach($operatives as $op): ?>
                <tr class="hover:bg-white/[0.02] transition-colors group">
                    <td class="p-5">
                        <span class="inline-block w-2 h-2 rounded-full <?= (strtotime($op['last_login']) > strtotime('-1 hour')) ? 'bg-green-500 animate-pulse shadow-[0_0_8px_#22c55e]' : 'bg-gray-700' ?>"></span>
                    </td>
                    <td class="p-5">
                        <div class="text-white font-bold flex items-center gap-2">
                            <?= htmlspecialchars($op['username']) ?>
                            <?php if($op['role'] === 'admin'): ?>
                                <span class="bg-premium-gold/20 text-premium-gold px-1.5 py-0.5 rounded text-[8px] tracking-widest border border-premium-gold/50">ADMIN</span>
                            <?php endif; ?>
                        </div>
                        <div class="text-[10px] text-gray-500 font-mono mt-1"><?= htmlspecialchars($op['email']) ?></div>
                    </td>
                    <td class="p-5 font-mono text-premium-gold font-bold"><?= number_format($op['urban_coins']) ?></td>
                    <td class="p-5 font-mono text-white"><?= number_format($op['mmk_balance']) ?> Ks</td>
                    <td class="p-5 text-gray-500 text-xs font-mono"><?= $op['last_login'] ? date('M d, H:i', strtotime($op['last_login'])) : 'Never' ?></td>
                    <td class="p-5 text-right">
                        <a href="?route=user_edit&id=<?= $op['id'] ?>" class="inline-block p-2 text-gray-500 hover:text-white hover:bg-gray-800 rounded-lg transition-colors"><i data-lucide="edit-3" size="16"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>