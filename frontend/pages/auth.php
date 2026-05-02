<?php
// /frontend/pages/auth.php
// V4 Premium Auth Screen - JWT API Gateway Integration

$redirectTarget = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'home';
$referralCode = isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : '';
?>
<style>
    /* Premium Floating Label CSS */
    .float-input:focus ~ .float-label,
    .float-input:not(:placeholder-shown) ~ .float-label {
        transform: translateY(-1.5rem) scale(0.85);
        color: #d4af37; /* Premium Gold */
    }
</style>

<div class="flex items-center justify-center min-h-[85vh] w-full px-4 relative z-10 pb-20">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl w-full max-w-md relative overflow-hidden transition-all duration-500 shadow-[0_20px_50px_rgba(0,0,0,0.8)] border border-gray-700/50 backdrop-blur-2xl">
        
        <!-- Premium Accent Glow -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-premium-gold/5 rounded-full blur-[60px] pointer-events-none -mr-10 -mt-10"></div>
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-premium-gold to-transparent opacity-50"></div>

        <div class="text-center mb-8 pt-4 relative z-10">
            <div class="w-20 h-20 bg-gradient-to-br from-[#d4af37] to-[#aa8c2c] rounded-3xl mx-auto flex items-center justify-center mb-6 shadow-lg shadow-premium-gold/20 border border-premium-gold/50">
                <i data-lucide="shield-check" class="text-premium-dark w-10 h-10"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-widest uppercase drop-shadow-md" id="auth-title">Welcome Back</h2>
            <p class="text-xs text-gray-400 mt-2 font-mono uppercase tracking-widest" id="auth-subtitle">Sign in to access the <?= ucfirst($redirectTarget) ?> module.</p>
        </div>

        <div id="auth-error" class="hidden bg-red-900/30 border border-red-500/50 text-red-500 p-4 rounded-xl text-xs mb-6 text-center shadow-inner font-mono break-words font-bold tracking-widest uppercase relative z-10"></div>

        <!-- LOGIN FORM -->
        <form id="login-form" class="space-y-6 transition-all duration-300 relative z-10">
            <div class="relative">
                <input type="email" id="login-email" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-gold focus:ring-1 focus:ring-premium-gold/50 transition-all peer shadow-inner font-bold">
                <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 origin-[0] left-5 pointer-events-none">Email Address</label>
            </div>
            <div class="relative">
                <input type="password" id="login-pass" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-gold focus:ring-1 focus:ring-premium-gold/50 transition-all peer tracking-[0.5em] shadow-inner font-bold">
                <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 origin-[0] left-5 pointer-events-none">Decryption Key</label>
            </div>
            
            <div class="flex justify-end">
                <a href="#" class="text-[10px] text-gray-500 hover:text-premium-gold transition-colors font-bold uppercase tracking-widest">Forgot Key?</a>
            </div>

            <button type="submit" id="login-btn" class="w-full bg-gradient-to-r from-premium-gold to-premium-goldDark text-premium-dark font-black py-4 rounded-xl mt-2 hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] transition-all text-xs flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest border border-premium-gold/50">
                Authenticate
            </button>
            
            <div class="flex items-center my-6">
                <div class="flex-grow h-px bg-gray-800"></div>
                <span class="px-4 text-[10px] text-gray-600 font-bold uppercase tracking-widest font-mono">System Bypass</span>
                <div class="flex-grow h-px bg-gray-800"></div>
            </div>

            <!-- Dynamic Google Login Container (Injected by auth_scripts.php) -->
            <div id="google-btn-container" class="flex justify-center w-full"></div>

            <p class="text-center text-[10px] text-gray-500 mt-8 font-bold uppercase tracking-widest">
                New Operative? <span class="text-white cursor-pointer hover:text-premium-gold transition-colors ml-2 border-b border-transparent hover:border-premium-gold" onclick="toggleAuth('register')">Initialize Profile</span>
            </p>
        </form>

        <!-- REGISTER FORM -->
        <form id="register-form" class="space-y-6 hidden transition-all duration-300 relative z-10">
            <div class="relative">
                <input type="text" id="reg-user" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-silver focus:ring-1 focus:ring-premium-silver/50 transition-all peer shadow-inner font-bold">
                <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 origin-[0] left-5 pointer-events-none">Operative Alias</label>
            </div>
            <div class="relative">
                <input type="email" id="reg-email" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-silver focus:ring-1 focus:ring-premium-silver/50 transition-all peer shadow-inner font-bold">
                <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 origin-[0] left-5 pointer-events-none">Email Address</label>
            </div>
            <div class="relative">
                <input type="password" id="reg-pass" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-silver focus:ring-1 focus:ring-premium-silver/50 transition-all peer tracking-[0.5em] shadow-inner font-bold">
                <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 origin-[0] left-5 pointer-events-none">Encryption Key</label>
            </div>
            
            <!-- Referral Field -->
            <div class="relative">
                <input type="text" id="reg-ref" placeholder=" " value="<?= $referralCode ?>" class="float-input w-full bg-[#151518] border border-premium-gold/30 rounded-2xl px-5 pt-7 pb-3 text-premium-gold outline-none focus:border-premium-gold transition-all peer shadow-inner font-mono font-bold">
                <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-premium-gold/70 duration-300 transform -translate-y-4 scale-75 top-5 z-10 origin-[0] left-5 pointer-events-none">Sponsor Alias (Optional)</label>
            </div>
            
            <button type="submit" id="reg-btn" class="w-full bg-premium-silver text-premium-dark font-black py-4 rounded-xl mt-4 hover:bg-white transition-all text-xs flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest shadow-[0_0_20px_rgba(226,232,240,0.3)] border border-white/50">
                Establish Connection
            </button>
            <p class="text-center text-[10px] text-gray-500 mt-8 font-bold uppercase tracking-widest">
                Already registered? <span class="text-white cursor-pointer hover:text-premium-silver transition-colors ml-2 border-b border-transparent hover:border-premium-silver" onclick="toggleAuth('login')">Return to Login</span>
            </p>
        </form>
    </div>
</div>

<!-- Inject Google Identity Script logic if not already loaded in header -->
<?php include_once 'includes/auth_scripts.php'; ?>

<script>
    const redirectParam = '<?= $redirectTarget ?>';

    function toggleAuth(mode) {
        document.getElementById('auth-error').classList.add('hidden');
        if (mode === 'register') {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.remove('hidden');
            document.getElementById('auth-title').innerText = 'Create Account';
            document.getElementById('auth-subtitle').innerText = 'Join the Urbanix network.';
        } else {
            document.getElementById('register-form').classList.add('hidden');
            document.getElementById('login-form').classList.remove('hidden');
            document.getElementById('auth-title').innerText = 'Welcome Back';
            document.getElementById('auth-subtitle').innerText = 'Sign in to access the ' + redirectParam + ' module.';
        }
    }

    async function handleNativeAuth(e, action) {
        e.preventDefault();
        const errorDiv = document.getElementById('auth-error');
        errorDiv.classList.add('hidden');
        
        const payload = { action: action };
        const btnId = action === 'login' ? 'login-btn' : 'reg-btn';
        const btn = document.getElementById(btnId);
        const originalText = btn.innerHTML;
        
        if (action === 'login') {
            payload.email = document.getElementById('login-email').value;
            payload.password = document.getElementById('login-pass').value;
        } else {
            payload.username = document.getElementById('reg-user').value;
            payload.email = document.getElementById('reg-email').value;
            payload.password = document.getElementById('reg-pass').value;
            payload.referral = document.getElementById('reg-ref').value;
        }
        
        btn.innerHTML = `<i data-lucide="loader" class="animate-spin w-5 h-5"></i>`;
        lucide.createIcons();

        try {
            // CRITICAL: Uses the UrbanixAPI Wrapper to target /api/index.php?route=auth
            if (typeof UrbanixAPI === 'undefined') throw new Error("API Client not loaded. Refresh system.");
            
            const res = await UrbanixAPI.request('auth', 'POST', payload);
            
            // Store the JWT Securely
            if(res.data && res.data.token) {
                UrbanixAPI.setToken(res.data.token);
            }
            
            // Redirect upon successful session sync & JWT acquisition
            window.location.href = '?route=' + redirectParam;
        } catch (err) {
            errorDiv.innerText = "> " + err.message;
            errorDiv.classList.remove('hidden');
            btn.innerHTML = originalText;
            lucide.createIcons();
        }
    }

    document.getElementById('login-form').addEventListener('submit', (e) => handleNativeAuth(e, 'login'));
    document.getElementById('register-form').addEventListener('submit', (e) => handleNativeAuth(e, 'register'));
</script>