<?php
// /admin/pages/dashboard.php
// Executive Dashboard

$db = (new Database())->getConnection();

// Stats Queries
$stats = [];
$stats['users'] = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stats['coins'] = $db->query("SELECT SUM(urban_coins) FROM users")->fetchColumn();
$stats['mmk_pending'] = $db->query("SELECT SUM(amount_mmk) FROM withdrawals WHERE status='pending'")->fetchColumn() ?: 0;

$stmt = $db->query("SELECT w.id, u.username, w.phone_number, w.amount_mmk, w.created_at, w.payment_method FROM withdrawals w JOIN users u ON w.user_id = u.id WHERE w.status = 'pending' ORDER BY w.created_at ASC");
$withdrawals = $stmt->fetchAll();
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="admin-panel p-6 rounded-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-6 opacity-10"><i data-lucide="users" size="64"></i></div>
        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2">Total Operatives</p>
        <p class="text-3xl font-black text-white"><?= number_format($stats['users']) ?></p>
    </div>
    <div class="admin-panel p-6 rounded-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-6 opacity-10 text-[#d4af37]"><i data-lucide="coins" size="64"></i></div>
        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2">Assets in Circulation</p>
        <p class="text-3xl font-black text-[#d4af37]"><?= number_format($stats['coins']) ?></p>
    </div>
    <div class="admin-panel p-6 rounded-2xl relative overflow-hidden border-t-2 border-t-green-500/50">
        <div class="absolute top-0 right-0 p-6 opacity-10"><i data-lucide="banknote" size="64"></i></div>
        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mb-2">Pending Payouts</p>
        <p class="text-3xl font-black text-white"><?= number_format($stats['mmk_pending']) ?> <span class="text-lg text-gray-500">Ks</span></p>
    </div>
</div>

<div class="flex items-center justify-between mb-4 mt-8">
    <h2 class="text-lg font-bold text-white tracking-wide">Withdrawal Queue</h2>
    <span class="text-xs text-gray-500 font-bold uppercase bg-gray-900 px-3 py-1 rounded-lg border border-gray-800"><?= count($withdrawals) ?> Pending</span>
</div>

<div class="admin-panel rounded-2xl overflow-hidden shadow-xl">
    <table class="w-full text-left text-sm whitespace-nowrap">
        <thead class="bg-black/40 border-b border-gray-800 text-gray-500 text-xs uppercase tracking-widest">
            <tr>
                <th class="p-5 font-bold">Date/Time</th>
                <th class="p-5 font-bold">Alias</th>
                <th class="p-5 font-bold">Target Account</th>
                <th class="p-5 font-bold">Amount (MMK)</th>
                <th class="p-5 font-bold text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-800/50">
            <?php if(count($withdrawals) > 0): ?>
                <?php foreach($withdrawals as $w): ?>
                    <tr class="hover:bg-white/5 transition-colors">
                        <td class="p-5 text-gray-400 text-xs font-mono"><?= date('M d, H:i', strtotime($w['created_at'])) ?></td>
                        <td class="p-5 text-white font-bold flex items-center gap-2">
                            <div class="w-6 h-6 rounded bg-gray-800 flex items-center justify-center"><i data-lucide="user" size="12"></i></div>
                            <?= htmlspecialchars($w['username']) ?>
                        </td>
                        <td class="p-5 text-gray-300 font-mono text-xs">
                            <?= htmlspecialchars($w['phone_number']) ?> 
                            <span class="ml-2 text-[9px] font-sans font-bold bg-gray-800 text-gray-400 px-2 py-1 rounded"><?= $w['payment_method'] ?></span>
                        </td>
                        <td class="p-5 font-mono text-white font-bold"><?= number_format($w['amount_mmk']) ?></td>
                        <td class="p-5 text-right space-x-2">
                            <button onclick="processAction(<?= $w['id'] ?>, 'approve')" class="bg-white text-black font-bold px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-xs active:scale-95 shadow-lg">Approve</button>
                            <button onclick="processAction(<?= $w['id'] ?>, 'reject')" class="bg-transparent border border-red-500/50 text-red-500 font-bold px-4 py-2 rounded-lg hover:bg-red-500 hover:text-white transition-colors text-xs active:scale-95">Reject</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="p-10 text-center text-gray-500 text-sm bg-black/20">The queue is currently empty.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function processAction(id, action) {
    if(!confirm(`Confirm ${action} for request #${id}?`)) return;
    
    fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/admin_actions.php', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'process_withdrawal', id: id, status: action === 'approve' ? 'approved' : 'rejected' })
    })
    .then(res => res.json())
    .then(data => {
        if(data.status === 'success') location.reload();
        else alert('Error: ' + data.message);
    });
}
</script>