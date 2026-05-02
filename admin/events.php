<?php
// /admin/pages/events.php
// CMS: Global Event & Multiplier Management

$db = (new Database())->getConnection();

// Handle creation/updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create_event') {
        $title = htmlspecialchars($_POST['title']);
        $multi = floatval($_POST['multiplier']);
        $start = $_POST['start_time'];
        $end = $_POST['end_time'];
        $active = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $db->prepare("INSERT INTO events (title, coin_multiplier, start_time, end_time, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $multi, $start, $end, $active]);
        
        echo "<div class='bg-green-900/20 border border-green-500 text-green-400 p-4 rounded-xl mb-6 font-mono text-sm tracking-wide shadow-inner'>EVENT DEPLOYED: Global multiplier configured.</div>";
    }
}

// Fetch events
$events = $db->query("SELECT * FROM events ORDER BY start_time DESC")->fetchAll();
?>

<div class="mb-8 flex justify-between items-center border-b border-gray-800 pb-4">
    <h2 class="text-2xl font-bold text-white uppercase tracking-widest font-mono">Global Multiplier Events</h2>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    
    <!-- Create Event Form -->
    <div class="xl:col-span-1">
        <form method="POST" class="admin-panel p-6 rounded-2xl border border-gray-800 shadow-2xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-premium-gold/5 rounded-full blur-3xl pointer-events-none -mr-10 -mt-10"></div>
            <input type="hidden" name="action" value="create_event">
            
            <h3 class="text-white font-bold tracking-widest uppercase mb-6 flex items-center gap-2">
                <i data-lucide="zap" class="text-premium-gold"></i> Initialize Event
            </h3>
            
            <div class="space-y-4 relative z-10">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Event Designation</label>
                    <input type="text" name="title" required class="w-full bg-black/50 border border-gray-700 rounded-xl px-4 py-3 text-white focus:border-premium-gold outline-none transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Yield Multiplier (e.g. 1.5, 2.0)</label>
                    <input type="number" step="0.1" name="multiplier" required class="w-full bg-black/50 border border-gray-700 rounded-xl px-4 py-3 text-white font-mono focus:border-premium-gold outline-none transition-colors">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">Start Time</label>
                        <input type="datetime-local" name="start_time" required class="w-full bg-black/50 border border-gray-700 rounded-xl px-3 py-3 text-gray-300 font-mono text-xs focus:border-premium-gold outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1">End Time</label>
                        <input type="datetime-local" name="end_time" required class="w-full bg-black/50 border border-gray-700 rounded-xl px-3 py-3 text-gray-300 font-mono text-xs focus:border-premium-gold outline-none">
                    </div>
                </div>
                <div class="pt-2 flex items-center gap-3">
                    <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 accent-premium-gold bg-black border-gray-700 rounded">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Enable Immediately</label>
                </div>
                <button type="submit" class="w-full bg-premium-silver text-premium-dark font-bold py-4 rounded-xl mt-4 hover:bg-white transition-all uppercase tracking-widest text-xs active:scale-95 shadow-lg">
                    Deploy Event to Network
                </button>
            </div>
        </form>
    </div>

    <!-- Event Registry -->
    <div class="xl:col-span-2 admin-panel rounded-2xl overflow-hidden border border-gray-800 shadow-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead class="bg-black/60 text-gray-500 text-[10px] uppercase tracking-widest font-bold border-b border-gray-800">
                    <tr>
                        <th class="p-5">Status</th>
                        <th class="p-5">Designation</th>
                        <th class="p-5">Multiplier</th>
                        <th class="p-5">Schedule</th>
                        <th class="p-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/50">
                    <?php foreach($events as $ev): ?>
                        <?php 
                            $now = time();
                            $start = strtotime($ev['start_time']);
                            $end = strtotime($ev['end_time']);
                            $isLive = ($ev['is_active'] && $now >= $start && $now <= $end);
                        ?>
                        <tr class="hover:bg-white/5 transition-colors">
                            <td class="p-5">
                                <?php if($isLive): ?>
                                    <span class="bg-green-500/20 text-green-500 border border-green-500/50 px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest animate-pulse flex items-center gap-1 w-max">
                                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full"></div> LIVE
                                    </span>
                                <?php elseif(!$ev['is_active']): ?>
                                    <span class="bg-red-500/10 text-red-500 border border-red-500/30 px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest w-max block">OFFLINE</span>
                                <?php else: ?>
                                    <span class="bg-gray-700/50 text-gray-400 border border-gray-600 px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest w-max block">SCHEDULED</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-5 text-white font-bold text-sm tracking-wide"><?= htmlspecialchars($ev['title']) ?></td>
                            <td class="p-5 font-mono text-premium-gold font-black"><?= number_format($ev['coin_multiplier'], 1) ?>x</td>
                            <td class="p-5 text-[10px] text-gray-400 font-mono space-y-1">
                                <div><span class="text-gray-600">IN:</span> <?= date('M d, H:i', $start) ?></div>
                                <div><span class="text-gray-600">OUT:</span> <?= date('M d, H:i', $end) ?></div>
                            </td>
                            <td class="p-5 text-right">
                                <button class="text-gray-500 hover:text-red-500 transition-colors p-2"><i data-lucide="trash-2" size="16"></i></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>