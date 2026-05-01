<?php
// /frontend/pages/settings.php
// Premium Metallic User Settings

requireAuth();

$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT username, email, auth_provider FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="max-w-3xl mx-auto w-full">
    <div class="mb-6 pb-4 border-b border-gray-800/50 flex items-center justify-between">
        <h2 class="text-2xl md:text-3xl font-bold text-white uppercase tracking-widest flex items-center gap-3">
            <i data-lucide="settings-2" class="text-premium-gold w-8 h-8"></i> Configuration
        </h2>
    </div>

    <div id="settings-alert" class="hidden p-4 mb-6 rounded-xl font-mono text-sm border shadow-inner"></div>

    <div class="glass-panel p-6 md:p-8 rounded-3xl border-gray-800 relative overflow-hidden shadow-2xl">
        <form id="profile-form" class="space-y-6">
            
            <div>
                <label class="block text-xs text-gray-400 mb-2 font-bold uppercase tracking-wider">Operative Alias</label>
                <input type="text" id="cfg-username" value="<?= htmlspecialchars($user['username']) ?>" class="w-full bg-black/40 border border-gray-700 rounded-xl p-4 text-white outline-none focus:border-premium-gold transition-colors">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-2 font-bold uppercase tracking-wider">Email Address (Read-Only)</label>
                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled class="w-full bg-gray-900/50 border border-gray-800 rounded-xl p-4 text-gray-600 cursor-not-allowed">
            </div>

            <?php if($user['auth_provider'] === 'native'): ?>
                <div class="pt-6 border-t border-gray-800/50 mt-6">
                    <h3 class="text-white font-bold mb-4 uppercase tracking-widest text-sm flex items-center gap-2">
                        <i data-lucide="key" class="text-gray-400 w-4 h-4"></i> Access Control
                    </h3>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs text-gray-400 mb-2 font-bold uppercase tracking-wider">Current Password</label>
                            <input type="password" id="cfg-old-pass" class="w-full bg-black/40 border border-gray-700 rounded-xl p-4 text-white outline-none focus:border-premium-gold transition-colors font-mono tracking-widest">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-2 font-bold uppercase tracking-wider">New Password</label>
                            <input type="password" id="cfg-new-pass" class="w-full bg-black/40 border border-gray-700 rounded-xl p-4 text-white outline-none focus:border-premium-gold transition-colors font-mono tracking-widest">
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="pt-8 flex flex-col sm:flex-row gap-4 border-t border-gray-800/50">
                <button type="submit" id="cfg-submit" class="w-full sm:w-auto bg-premium-silver text-premium-dark py-4 px-8 rounded-xl hover:bg-white transition-all uppercase text-xs font-bold flex items-center justify-center gap-2 active:scale-95 shadow-lg">
                    <i data-lucide="save" size="16"></i> Save Changes
                </button>
                <button type="button" onclick="systemLogout()" class="w-full sm:w-auto bg-transparent border border-red-500/50 text-red-500 py-4 px-8 rounded-xl hover:bg-red-500 hover:text-white transition-all uppercase text-xs font-bold flex items-center justify-center gap-2 active:scale-95">
                    <i data-lucide="power" size="16"></i> Terminate Session
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('profile-form').addEventListener('submit', (e) => {
        e.preventDefault();
        const alertBox = document.getElementById('settings-alert');
        const btn = document.getElementById('cfg-submit');
        
        const payload = {
            username: document.getElementById('cfg-username').value,
            old_pass: document.getElementById('cfg-old-pass') ? document.getElementById('cfg-old-pass').value : '',
            new_pass: document.getElementById('cfg-new-pass') ? document.getElementById('cfg-new-pass').value : ''
        };

        btn.innerHTML = `<i data-lucide="loader" class="animate-spin" size="16"></i>`;
        lucide.createIcons();

        fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/update_profile.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            alertBox.classList.remove('hidden', 'bg-red-900/20', 'border-red-500', 'text-red-400', 'bg-green-900/20', 'border-green-500', 'text-green-400');
            
            if(data.status === 'success') {
                alertBox.classList.add('bg-green-900/20', 'border-green-500', 'text-green-400');
                alertBox.innerHTML = `${data.message}`;
                if(document.getElementById('cfg-old-pass')) {
                    document.getElementById('cfg-old-pass').value = '';
                    document.getElementById('cfg-new-pass').value = '';
                }
            } else {
                alertBox.classList.add('bg-red-900/20', 'border-red-500', 'text-red-400');
                alertBox.innerHTML = `${data.message}`;
            }
            btn.innerHTML = `<i data-lucide="save" size="16"></i> Save Changes`;
            lucide.createIcons();
        });
    });
</script>