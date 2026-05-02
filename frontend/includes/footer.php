<?php
// /frontend/includes/footer.php
// Premium Metallic Edition - Now powered by JWT API Client
$currentRoute = isset($_GET['route']) ? $_GET['route'] : 'home';
?>
    </main> </div> <div id="global-toast" class="fixed top-20 right-4 z-[9999] transform translate-x-[150%] transition-transform duration-500 ease-out flex items-center gap-3 glass-panel px-6 py-4 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] border-l-4 border-premium-gold">
    <div id="toast-icon" class="w-8 h-8 rounded-full bg-premium-gold/20 flex items-center justify-center text-premium-gold border border-premium-gold/50">
        <i data-lucide="info" size="16"></i>
    </div>
    <p id="toast-msg" class="text-sm font-bold text-white tracking-wide font-sans">Notification</p>
</div>

<script src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/js/api_client.js"></script>

<script>
    lucide.createIcons();

    window.showToast = function(message, type = 'info') {
        const toast = document.getElementById('global-toast');
        const msgEl = document.getElementById('toast-msg');
        const iconEl = document.getElementById('toast-icon');
        msgEl.innerText = message;
        
        if(type === 'success') {
            toast.className = "fixed top-20 right-4 z-[9999] transform transition-transform duration-500 ease-out flex items-center gap-3 glass-panel px-6 py-4 rounded-2xl shadow-xl border-l-4 border-green-500 translate-x-0";
            iconEl.className = "w-8 h-8 rounded-full bg-green-500/20 flex items-center justify-center text-green-500 border border-green-500/50";
            iconEl.innerHTML = '<i data-lucide="check-circle" size="16"></i>';
        } else if(type === 'error') {
            toast.className = "fixed top-20 right-4 z-[9999] transform transition-transform duration-500 ease-out flex items-center gap-3 glass-panel px-6 py-4 rounded-2xl shadow-xl border-l-4 border-red-500 translate-x-0";
            iconEl.className = "w-8 h-8 rounded-full bg-red-500/20 flex items-center justify-center text-red-500 border border-red-500/50";
            iconEl.innerHTML = '<i data-lucide="alert-triangle" size="16"></i>';
        }
        lucide.createIcons();
        setTimeout(() => { toast.classList.remove('translate-x-0'); toast.classList.add('translate-x-[150%]'); }, 4000);
    };

    // SECURE DISCONNECT (JWT PURGE)
    function systemLogout() {
        if(confirm("INITIALIZE DISCONNECT: Sever connection to the mainframe?")) {
            UrbanixAPI.clearToken(); // Destroy JWT client-side
            
            // Optional: Still call backend to clear PHP session if running hybrid
            fetch('<?= defined("API_URL") ? str_replace('/api', '', API_URL) : "" ?>/api/index.php?route=logout', {method: 'POST'})
            .finally(() => window.location.href = '?route=auth');
        }
    }

    // (Three.js and Boot Sequence omitted for brevity - Remains the same)
</script>
</body>
</html>