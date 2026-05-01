<?php
// /frontend/pages/settings.php
// Premium Metallic User Settings - Expanded

requireAuth();

$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT username, email, auth_provider FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="max-w-3xl mx-auto w-full pb-20">
    <div class="mb-6 pb-4 border-b border-gray-800/50 flex items-center justify-between">
        <h2 class="text-2xl md:text-3xl font-bold text-white uppercase tracking-widest flex items-center gap-3">
            <i data-lucide="sliders" class="text-premium-silver w-8 h-8"></i> Configuration
        </h2>
    </div>

    <div id="settings-alert" class="hidden p-4 mb-6 rounded-xl font-mono text-sm border shadow-inner"></div>

    <!-- PROFILE SETTINGS -->
    <div class="glass-panel p-6 md:p-8 rounded-3xl border-gray-800 relative overflow-hidden shadow-2xl mb-6">
        <h3 class="text-white font-bold mb-6 uppercase tracking-widest text-sm flex items-center gap-2">
            <i data-lucide="user" class="text-premium-gold w-4 h-4"></i> Identity & Access
        </h3>
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
                    <div class="space-y-5">
                        <div>
                            <label class="block text-xs text-gray-400 mb-2 font-bold uppercase tracking-wider">Current Password</label>
                            <input type="password" id="cfg-old-pass" class="w-full bg-black/40 border border-gray-700 rounded-xl p-4 text-white outline-none focus:border-premium-gold transition-colors font-mono tracking-widest" placeholder="Leave blank to keep current">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-2 font-bold uppercase tracking-wider">New Password</label>
                            <input type="password" id="cfg-new-pass" class="w-full bg-black/40 border border-gray-700 rounded-xl p-4 text-white outline-none focus:border-premium-gold transition-colors font-mono tracking-widest">
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="pt-4">
                <button type="submit" id="cfg-submit" class="w-full sm:w-auto bg-premium-silver text-premium-dark py-4 px-8 rounded-xl hover:bg-white transition-all uppercase text-xs font-bold flex items-center justify-center gap-2 active:scale-95 shadow-lg">
                    <i data-lucide="save" size="16"></i> Save Identity
                </button>
            </div>
        </form>
    </div>

    <!-- HARDWARE SETTINGS -->
    <div class="glass-panel p-6 md:p-8 rounded-3xl border-gray-800 relative overflow-hidden shadow-2xl mb-6">
        <h3 class="text-white font-bold mb-6 uppercase tracking-widest text-sm flex items-center gap-2">
            <i data-lucide="cpu" class="text-gray-400 w-4 h-4"></i> Hardware Optimization
        </h3>
        
        <div class="flex items-center justify-between bg-black/40 p-4 rounded-xl border border-gray-700/50">
            <div>
                <p class="text-white font-bold text-sm tracking-wide">Performance Mode</p>
                <p class="text-xs text-gray-500 font-mono mt-1">Disables 3D background rendering to save battery.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" id="toggle-performance" class="sr-only peer">
                <div class="w-14 h-7 bg-gray-800 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-premium-gold peer-checked:after:bg-white"></div>
            </label>
        </div>
    </div>

    <!-- DANGER ZONE -->
    <div class="glass-panel p-6 md:p-8 rounded-3xl border-red-900/30 relative overflow-hidden shadow-2xl">
        <h3 class="text-red-500 font-bold mb-4 uppercase tracking-widest text-sm flex items-center gap-2">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i> Danger Zone
        </h3>
        <p class="text-xs text-gray-400 font-mono mb-6 leading-relaxed">Permanently delete your operative profile. This will instantly vaporize all unextracted network coins and MMK liquid assets. This action is irreversible.</p>
        
        <div class="flex flex-col sm:flex-row gap-4">
            <button type="button" onclick="systemLogout()" class="w-full bg-gray-900 border border-gray-700 text-gray-300 py-4 px-8 rounded-xl hover:bg-gray-800 transition-all uppercase text-xs font-bold flex items-center justify-center gap-2 active:scale-95">
                <i data-lucide="log-out" size="16"></i> Secure Logout
            </button>
            <button type="button" onclick="deleteAccount()" class="w-full bg-red-900/20 border border-red-500/50 text-red-500 py-4 px-8 rounded-xl hover:bg-red-600 hover:text-white transition-all uppercase text-xs font-bold flex items-center justify-center gap-2 active:scale-95">
                <i data-lucide="trash-2" size="16"></i> Erase Data
            </button>
        </div>
    </div>
</div>

<script>
    // Hardware Toggle Logic
    const perfToggle = document.getElementById('toggle-performance');
    perfToggle.checked = localStorage.getItem('urbanix_perf_mode') === 'true';
    
    perfToggle.addEventListener('change', (e) => {
        localStorage.setItem('urbanix_perf_mode', e.target.checked);
        // Refresh to apply/remove Three.js
        window.location.reload(); 
    });

    // Profile Update Logic
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
                if(document.getElementById('cfg-old-pass')) { document.getElementById('cfg-old-pass').value = ''; document.getElementById('cfg-new-pass').value = ''; }
            } else {
                alertBox.classList.add('bg-red-900/20', 'border-red-500', 'text-red-400');
            }
            alertBox.innerHTML = `${data.message}`;
            btn.innerHTML = `<i data-lucide="save" size="16"></i> Save Identity`;
            lucide.createIcons();
        });
    });

    // Delete Account Logic
    function deleteAccount() {
        if(confirm("CRITICAL WARNING: Are you absolutely sure you want to permanently erase your account and all assets?")) {
            if(prompt("Type 'ERASE' to confirm:") === 'ERASE') {
                fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/account_actions.php', {
                    method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({action: 'delete_account'})
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') window.location.href = '?route=auth';
                    else alert(data.message);
                });
            }
        }
    }
</script>