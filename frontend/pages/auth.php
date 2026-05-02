<?php
// /frontend/pages/auth.php
// V5 Premium Auth Screen - Failsafe Captcha & API Integration

$redirectTarget = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'home';
$referralCode = isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : '';
?>
<style>
    .float-input:focus ~ .float-label, .float-input:not(:placeholder-shown) ~ .float-label { transform: translateY(-1.5rem) scale(0.85); color: #d4af37; }
    .form-section { transition: opacity 0.4s ease, transform 0.4s ease; }
    .hidden-state { opacity: 0; transform: translateY(10px); pointer-events: none; position: absolute; visibility: hidden; }
    .active-state { opacity: 1; transform: translateY(0); position: relative; visibility: visible; }
</style>

<div class="flex items-center justify-center min-h-[85vh] w-full px-4 relative z-10 pb-20 mt-10">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl w-full max-w-md relative overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.8)] border border-gray-700/50 backdrop-blur-2xl">
        <div class="absolute top-0 right-0 w-48 h-48 bg-premium-gold/5 rounded-full blur-[60px] pointer-events-none -mr-10 -mt-10"></div>
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-premium-gold to-transparent opacity-50"></div>

        <div class="text-center mb-8 pt-4 relative z-10">
            <div class="w-20 h-20 bg-gradient-to-br from-[#d4af37] to-[#aa8c2c] rounded-3xl mx-auto flex items-center justify-center mb-6 shadow-lg shadow-premium-gold/20 border border-premium-gold/50 transition-transform duration-500" id="auth-icon-container">
                <i data-lucide="shield-check" class="text-premium-dark w-10 h-10" id="auth-icon"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-widest uppercase drop-shadow-md transition-all" id="auth-title">Welcome Back</h2>
            <p class="text-xs text-gray-400 mt-2 font-mono uppercase tracking-widest transition-all" id="auth-subtitle">Sign in to access the <?= ucfirst($redirectTarget) ?> module.</p>
        </div>

        <div id="auth-error" class="hidden bg-red-900/30 border border-red-500/50 text-red-500 p-4 rounded-xl text-[10px] mb-6 text-center shadow-inner font-mono font-bold tracking-widest uppercase relative z-10"></div>

        <div id="captcha-container" class="mb-6 relative z-10 active-state">
            <div class="flex items-center justify-between mb-2">
                <label class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Anti-Bot Verification</label>
                <button type="button" onclick="loadCaptcha()" class="text-premium-gold hover:text-white transition-colors text-[9px] font-bold uppercase flex items-center gap-1">
                    <i data-lucide="refresh-cw" size="10"></i> Refresh
                </button>
            </div>
            <div class="flex gap-3">
                <img id="captcha-img" src="" alt="Captcha" class="h-[55px] rounded-xl border border-gray-700 bg-[#050507]">
                <input type="text" id="captcha-input" placeholder="LOADING..." maxlength="8" required disabled class="w-full bg-black/60 border border-gray-700 rounded-xl px-4 text-white outline-none focus:border-premium-gold transition-all shadow-inner font-mono font-bold tracking-widest text-center uppercase text-sm disabled:opacity-50">
            </div>
        </div>

        <form id="login-form" class="space-y-6 form-section active-state z-10">
            <div class="relative">
                <input type="email" id="login-email" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-gold transition-all peer shadow-inner font-bold">
                <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Email Address</label>
            </div>
            <div class="relative">
                <input type="password" id="login-pass" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 pr-12 text-white outline-none focus:border-premium-gold transition-all peer tracking-[0.5em] shadow-inner font-bold">
                <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Decryption Key</label>
                <button type="button" onclick="togglePassword('login-pass', 'login-eye')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-premium-gold transition-colors"><i data-lucide="eye" id="login-eye" size="18"></i></button>
            </div>
            <div class="flex justify-end"><button type="button" onclick="toggleAuth('recovery')" class="text-[9px] text-gray-500 hover:text-premium-gold font-bold uppercase tracking-widest border-b border-transparent hover:border-premium-gold pb-0.5">Key Recovery Protocol</button></div>
            <button type="submit" id="login-btn" class="w-full bg-gradient-to-r from-premium-gold to-premium-goldDark text-premium-dark font-black py-4 rounded-xl hover:shadow-[0_0_20px_rgba(212,175,55,0.4)] transition-all text-xs flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest">Authenticate</button>
            <div class="flex items-center my-6"><div class="flex-grow h-px bg-gray-800"></div><span class="px-4 text-[9px] text-gray-600 font-bold uppercase tracking-widest font-mono">System Bypass</span><div class="flex-grow h-px bg-gray-800"></div></div>
            <div id="google-btn-container" class="flex justify-center w-full"></div>
            <p class="text-center text-[10px] text-gray-500 mt-6 font-bold uppercase tracking-widest">New Operative? <span class="text-white cursor-pointer hover:text-premium-gold ml-2 border-b border-transparent hover:border-premium-gold pb-0.5" onclick="toggleAuth('register')">Initialize Profile</span></p>
        </form>

        <form id="register-form" class="space-y-6 form-section hidden-state z-10">
            <div class="relative"><input type="text" id="reg-user" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-silver transition-all peer font-bold"><label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Operative Alias</label></div>
            <div class="relative"><input type="email" id="reg-email" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-silver transition-all peer font-bold"><label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Email Address</label></div>
            <div class="relative"><input type="password" id="reg-pass" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 pr-12 text-white outline-none focus:border-premium-silver transition-all peer tracking-[0.5em] font-bold"><label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Encryption Key</label><button type="button" onclick="togglePassword('reg-pass', 'reg-eye')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-premium-silver transition-colors"><i data-lucide="eye" id="reg-eye" size="18"></i></button></div>
            <div class="relative"><input type="text" id="reg-ref" placeholder=" " value="<?= $referralCode ?>" class="float-input w-full bg-[#151518] border border-premium-gold/30 rounded-2xl px-5 pt-7 pb-3 text-premium-gold outline-none focus:border-premium-gold transition-all peer shadow-inner font-mono font-bold"><label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-premium-gold/70 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Sponsor Alias (Optional)</label></div>
            <button type="submit" id="reg-btn" class="w-full bg-premium-silver text-premium-dark font-black py-4 rounded-xl mt-4 hover:bg-white transition-all text-xs flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest shadow-[0_0_20px_rgba(226,232,240,0.3)]">Establish Connection</button>
            <p class="text-center text-[10px] text-gray-500 mt-6 font-bold uppercase tracking-widest">Already registered? <span class="text-white cursor-pointer hover:text-premium-silver ml-2 border-b border-transparent hover:border-premium-silver pb-0.5" onclick="toggleAuth('login')">Return to Login</span></p>
        </form>

        <form id="recovery-form" class="space-y-6 form-section hidden-state z-10">
            <p class="text-xs text-gray-400 font-sans leading-relaxed text-center mb-6">Enter your registered email address.</p>
            <div class="relative"><input type="email" id="recovery-email" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-blue-500 transition-all peer font-bold"><label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Uplink Email</label></div>
            <button type="submit" id="recovery-btn" class="w-full bg-blue-600 text-white font-black py-4 rounded-xl hover:bg-blue-500 transition-all text-xs flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest shadow-[0_0_20px_rgba(59,130,246,0.3)]">Transmit Recovery Link</button>
            <p class="text-center text-[10px] text-gray-500 mt-6 font-bold uppercase tracking-widest">Protocol aborted? <span class="text-white cursor-pointer hover:text-blue-400 ml-2 border-b border-transparent hover:border-blue-400 pb-0.5" onclick="toggleAuth('login')">Return to Login</span></p>
        </form>
    </div>
</div>

<?php include_once 'includes/auth_scripts.php'; ?>

<script>
    const redirectParam = '<?= $redirectTarget ?>';
    let activeCaptchaHash = '';
    let activeCaptchaExp = 0;

    // Failsafe Captcha Loader
    async function loadCaptcha() {
        const img = document.getElementById('captcha-img');
        const input = document.getElementById('captcha-input');
        
        img.style.opacity = '0.3'; 
        input.value = '';
        input.placeholder = 'LOADING...';
        input.disabled = true;
        
        try {
            const res = await UrbanixAPI.request('captcha', 'GET');
            img.src = res.data.image; 
            activeCaptchaHash = res.data.hash; 
            activeCaptchaExp = res.data.exp;
            
            img.style.opacity = '1';
            input.disabled = false;
            input.placeholder = '8 CHARS';
            input.focus();
        } catch (err) { 
            console.error("Captcha load failed:", err.message);
            input.placeholder = 'SYS FAULT';
            if(window.showToast) window.showToast("Gateway error. Check API routing.", "error");
        }
    }
    window.addEventListener('DOMContentLoaded', loadCaptcha);

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId); const icon = document.getElementById(iconId);
        if (input.type === "password") { input.type = "text"; icon.setAttribute('data-lucide', 'eye-off'); } 
        else { input.type = "password"; icon.setAttribute('data-lucide', 'eye'); }
        lucide.createIcons();
    }

    function toggleAuth(mode) {
        document.getElementById('auth-error').classList.add('hidden');
        ['login-form', 'register-form', 'recovery-form'].forEach(id => {
            document.getElementById(id).classList.remove('active-state');
            document.getElementById(id).classList.add('hidden-state');
        });
        
        const title = document.getElementById('auth-title'); const subtitle = document.getElementById('auth-subtitle');
        const iconContainer = document.getElementById('auth-icon-container'); const icon = document.getElementById('auth-icon');
        const captchaBox = document.getElementById('captcha-container');
        iconContainer.className = "w-20 h-20 rounded-3xl mx-auto flex items-center justify-center mb-6 shadow-lg transition-transform duration-500 border";

        if (mode === 'register') {
            document.getElementById('register-form').classList.replace('hidden-state', 'active-state');
            captchaBox.classList.replace('hidden-state', 'active-state');
            title.innerText = 'Initialize'; subtitle.innerText = 'Join the Urbanix network.';
            iconContainer.classList.add('bg-gradient-to-br', 'from-[#e2e8f0]', 'to-gray-400', 'border-white', 'shadow-white/20');
            icon.setAttribute('data-lucide', 'user-plus'); icon.className = "text-premium-dark w-10 h-10";
        } else if (mode === 'recovery') {
            document.getElementById('recovery-form').classList.replace('hidden-state', 'active-state');
            captchaBox.classList.replace('active-state', 'hidden-state');
            title.innerText = 'Key Recovery'; subtitle.innerText = 'System override authorized.';
            iconContainer.classList.add('bg-gradient-to-br', 'from-blue-600', 'to-blue-900', 'border-blue-400', 'shadow-blue-500/20');
            icon.setAttribute('data-lucide', 'life-buoy'); icon.className = "text-white w-10 h-10";
        } else {
            document.getElementById('login-form').classList.replace('hidden-state', 'active-state');
            captchaBox.classList.replace('hidden-state', 'active-state');
            title.innerText = 'Welcome Back'; subtitle.innerText = 'Sign in to access the ' + redirectParam + ' module.';
            iconContainer.classList.add('bg-gradient-to-br', 'from-[#d4af37]', 'to-[#aa8c2c]', 'border-[#d4af37]', 'shadow-[#d4af37]/20');
            icon.setAttribute('data-lucide', 'shield-check'); icon.className = "text-premium-dark w-10 h-10";
        }
        lucide.createIcons();
    }

    async function handleNativeAuth(e, action) {
        e.preventDefault();
        const errorDiv = document.getElementById('auth-error'); errorDiv.classList.add('hidden');
        let payload = { action: action };
        let btn, originalText;

        if (action === 'recovery') return; // Simulated

        const captchaVal = document.getElementById('captcha-input').value;
        if (!captchaVal || captchaVal.length !== 8) {
            errorDiv.innerText = "> Invalid Anti-Bot sequence. Requires 8 characters.";
            errorDiv.classList.remove('hidden'); return;
        }

        payload.captcha = captchaVal; payload.captcha_hash = activeCaptchaHash; payload.captcha_exp = activeCaptchaExp;
        
        if (action === 'login') {
            btn = document.getElementById('login-btn'); originalText = btn.innerHTML;
            payload.email = document.getElementById('login-email').value; payload.password = document.getElementById('login-pass').value;
        } else {
            btn = document.getElementById('reg-btn'); originalText = btn.innerHTML;
            payload.username = document.getElementById('reg-user').value; payload.email = document.getElementById('reg-email').value;
            payload.password = document.getElementById('reg-pass').value; payload.referral = document.getElementById('reg-ref').value;
        }
        
        btn.innerHTML = `<i data-lucide="loader" class="animate-spin w-5 h-5"></i>`; lucide.createIcons();

        try {
            const res = await UrbanixAPI.request('auth', 'POST', payload);
            if(res.data && res.data.token) UrbanixAPI.setToken(res.data.token);
            window.location.href = '?route=' + redirectParam;
        } catch (err) {
            errorDiv.innerText = "> " + err.message; errorDiv.classList.remove('hidden');
            btn.innerHTML = originalText; lucide.createIcons(); loadCaptcha();
        }
    }

    document.getElementById('login-form').addEventListener('submit', (e) => handleNativeAuth(e, 'login'));
    document.getElementById('register-form').addEventListener('submit', (e) => handleNativeAuth(e, 'register'));
    document.getElementById('recovery-form').addEventListener('submit', (e) => handleNativeAuth(e, 'recovery'));
</script>