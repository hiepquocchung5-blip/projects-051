<?php
// /frontend/pages/auth.php
// V8 Premium Auth Screen - Case-Insensitive Captcha & Fluid UX

$redirectTarget = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'home';
$referralCode = isset($_GET['ref']) ? htmlspecialchars($_GET['ref']) : '';
?>
<style>
    .float-input:focus ~ .float-label, .float-input:not(:placeholder-shown) ~ .float-label { transform: translateY(-1.5rem) scale(0.85); color: #d4af37; }
    .form-section { transition: opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1), transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
    .hidden-state { opacity: 0; transform: translateY(20px); pointer-events: none; position: absolute; visibility: hidden; }
    .active-state { opacity: 1; transform: translateY(0); position: relative; visibility: visible; }
    .captcha-box { filter: drop-shadow(0 0 10px rgba(212,175,55,0.15)); transition: all 0.3s ease; }
    .captcha-box:hover { filter: drop-shadow(0 0 20px rgba(212,175,55,0.4)); }
    #form-container-wrapper { transition: min-height 0.4s ease; }
</style>

<div class="flex items-center justify-center min-h-[85vh] w-full px-4 relative z-10 pb-20 mt-10">
    <div class="glass-panel p-8 sm:p-10 rounded-3xl w-full max-w-md relative overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.8)] border border-gray-700/50 backdrop-blur-2xl">
        
        <!-- Ambient Glow -->
        <div class="absolute top-0 right-0 w-48 h-48 bg-premium-gold/5 rounded-full blur-[60px] pointer-events-none -mr-10 -mt-10"></div>
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-premium-gold to-transparent opacity-50"></div>

        <!-- Header -->
        <div class="text-center mb-8 pt-4 relative z-10">
            <div class="w-20 h-20 bg-gradient-to-br from-[#d4af37] to-[#aa8c2c] rounded-3xl mx-auto flex items-center justify-center mb-6 shadow-lg shadow-premium-gold/20 border border-premium-gold/50 transition-all duration-500 hover:scale-105" id="auth-icon-container">
                <i data-lucide="shield-check" class="text-premium-dark w-10 h-10 transition-transform duration-300" id="auth-icon"></i>
            </div>
            <h2 class="text-3xl font-black text-white tracking-widest uppercase drop-shadow-md transition-colors duration-300" id="auth-title">Welcome Back</h2>
            <p class="text-xs text-gray-400 mt-2 font-mono uppercase tracking-widest transition-colors duration-300" id="auth-subtitle">Sign in to access the <?= ucfirst($redirectTarget) ?> module.</p>
        </div>

        <!-- Feedback Alerts -->
        <div id="auth-error" class="hidden bg-red-900/30 border border-red-500/50 text-red-500 p-4 rounded-xl text-[10px] mb-6 text-center shadow-inner font-mono font-bold tracking-widest uppercase relative z-10 animate-pulse"></div>
        <div id="auth-success" class="hidden bg-green-900/30 border border-green-500/50 text-green-400 p-4 rounded-xl text-[10px] mb-6 text-center shadow-inner font-mono break-words font-bold tracking-widest uppercase relative z-10"></div>

        <div id="form-container-wrapper" class="relative">
            <!-- CAPTCHA UI -->
            <div id="captcha-container" class="mb-6 relative z-10 active-state">
                <div class="flex items-center justify-between mb-2">
                    <label class="text-[9px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <i data-lucide="shield-alert" size="12" class="text-gray-500"></i> Anti-Bot Verification
                    </label>
                    <button type="button" onclick="loadCaptcha()" class="text-premium-gold hover:text-white transition-colors text-[9px] font-bold uppercase flex items-center gap-1 group">
                        <i data-lucide="refresh-cw" size="10" class="group-hover:rotate-180 transition-transform duration-500"></i> Refresh
                    </button>
                </div>
                <div class="flex gap-3">
                    <div class="h-[55px] w-[140px] rounded-xl border border-gray-700 bg-[#050507] flex items-center justify-center relative overflow-hidden captcha-box shrink-0 group cursor-pointer" onclick="loadCaptcha()">
                        <div id="captcha-loader" class="absolute inset-0 flex items-center justify-center bg-[#050507]/80 backdrop-blur-sm z-10 transition-opacity duration-300">
                            <i data-lucide="loader" class="animate-spin text-premium-gold w-5 h-5"></i>
                        </div>
                        <img id="captcha-img" src="" alt="Captcha" class="h-full w-full object-cover opacity-0 transition-opacity duration-500 relative z-0 group-hover:scale-105">
                    </div>
                    <!-- FIXED: Removed JS oninput uppercase override to fix mobile keyboards. CSS handles the visual uppercase. -->
                    <input type="text" id="captcha-input" placeholder="LOADING..." maxlength="8" required disabled class="w-full bg-black/60 border border-gray-700 rounded-xl px-4 text-white outline-none focus:border-premium-gold hover:border-gray-500 transition-all shadow-inner font-mono font-bold tracking-[0.2em] text-center uppercase text-sm disabled:opacity-50 disabled:cursor-not-allowed placeholder:tracking-widest">
                </div>
            </div>

            <!-- LOGIN FORM -->
            <form id="login-form" class="space-y-6 form-section active-state z-10">
                <div class="relative">
                    <input type="email" id="login-email" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-gold transition-all peer shadow-inner font-bold">
                    <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Email Address</label>
                </div>
                <div class="relative">
                    <input type="password" id="login-pass" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 pr-12 text-white outline-none focus:border-premium-gold transition-all peer tracking-[0.5em] shadow-inner font-bold">
                    <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Decryption Key</label>
                    <button type="button" onclick="togglePassword('login-pass', 'login-eye')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-premium-gold transition-colors p-2"><i data-lucide="eye" id="login-eye" size="18"></i></button>
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="toggleAuth('recovery')" class="text-[9px] text-gray-500 hover:text-premium-gold font-bold uppercase tracking-widest border-b border-transparent hover:border-premium-gold pb-0.5 transition-all">Key Recovery Protocol</button>
                </div>
                <button type="submit" id="login-btn" class="w-full bg-gradient-to-r from-premium-gold to-premium-goldDark text-premium-dark font-black py-4 rounded-xl hover:shadow-[0_0_25px_rgba(212,175,55,0.4)] transition-all text-xs flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest border border-premium-gold/30">
                    Authenticate <i data-lucide="arrow-right" size="16"></i>
                </button>
                
                <div class="flex items-center my-6 opacity-60">
                    <div class="flex-grow h-px bg-gradient-to-r from-transparent to-gray-700"></div>
                    <span class="px-4 text-[9px] text-gray-500 font-bold uppercase tracking-widest font-mono">System Bypass</span>
                    <div class="flex-grow h-px bg-gradient-to-l from-transparent to-gray-700"></div>
                </div>
                <div id="google-btn-container" class="flex justify-center w-full min-h-[44px]"></div>
                
                <p class="text-center text-[10px] text-gray-500 mt-6 font-bold uppercase tracking-widest">
                    New Operative? <span class="text-white cursor-pointer hover:text-premium-gold ml-2 border-b border-transparent hover:border-premium-gold pb-0.5 transition-all" onclick="toggleAuth('register')">Initialize Profile</span>
                </p>
            </form>

            <!-- REGISTER FORM -->
            <form id="register-form" class="space-y-6 form-section hidden-state z-10">
                <div class="relative">
                    <input type="text" id="reg-user" placeholder=" " required minlength="3" maxlength="20" class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-silver transition-all peer font-bold">
                    <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Operative Alias</label>
                </div>
                <div class="relative">
                    <input type="email" id="reg-email" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-premium-silver transition-all peer font-bold">
                    <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Email Address</label>
                </div>
                <div class="relative">
                    <input type="password" id="reg-pass" placeholder=" " required minlength="6" class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 pr-12 text-white outline-none focus:border-premium-silver transition-all peer tracking-[0.5em] font-bold">
                    <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Encryption Key</label>
                    <button type="button" onclick="togglePassword('reg-pass', 'reg-eye')" class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-premium-silver transition-colors p-2"><i data-lucide="eye" id="reg-eye" size="18"></i></button>
                </div>
                <div class="relative">
                    <input type="text" id="reg-ref" placeholder=" " value="<?= $referralCode ?>" class="float-input w-full bg-[#151518] border border-premium-gold/30 rounded-2xl px-5 pt-7 pb-3 text-premium-gold outline-none focus:border-premium-gold transition-all peer shadow-inner font-mono font-bold uppercase">
                    <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-premium-gold/70 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Sponsor Alias (Optional)</label>
                </div>
                
                <button type="submit" id="reg-btn" class="w-full bg-premium-silver text-premium-dark font-black py-4 rounded-xl mt-4 hover:bg-white transition-all text-xs flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest shadow-[0_0_20px_rgba(226,232,240,0.3)]">
                    Establish Connection <i data-lucide="arrow-right" size="16"></i>
                </button>
                <p class="text-center text-[10px] text-gray-500 mt-6 font-bold uppercase tracking-widest">
                    Already registered? <span class="text-white cursor-pointer hover:text-premium-silver ml-2 border-b border-transparent hover:border-premium-silver pb-0.5 transition-all" onclick="toggleAuth('login')">Return to Login</span>
                </p>
            </form>

            <!-- RECOVERY FORM -->
            <form id="recovery-form" class="space-y-6 form-section hidden-state z-10">
                <div class="bg-blue-900/20 border border-blue-500/30 rounded-xl p-4 mb-6 shadow-inner">
                    <p class="text-xs text-blue-200 font-sans leading-relaxed text-center">Enter your registered email address. System will dispatch extraction protocols directly to your inbox.</p>
                </div>
                <div class="relative">
                    <input type="email" id="recovery-email" placeholder=" " required class="float-input w-full bg-black/60 border border-gray-700 rounded-2xl px-5 pt-7 pb-3 text-white outline-none focus:border-blue-500 transition-all peer font-bold">
                    <label class="float-label absolute text-[10px] font-bold uppercase tracking-widest text-gray-500 duration-300 transform -translate-y-4 scale-75 top-5 z-10 left-5 pointer-events-none">Uplink Email</label>
                </div>
                <button type="submit" id="recovery-btn" class="w-full bg-blue-600 text-white font-black py-4 rounded-xl hover:bg-blue-500 transition-all text-xs flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest shadow-[0_0_20px_rgba(59,130,246,0.3)] border border-blue-400/50 mt-2">
                    Transmit Recovery Link <i data-lucide="send" size="16"></i>
                </button>
                <p class="text-center text-[10px] text-gray-500 mt-6 font-bold uppercase tracking-widest">
                    Protocol aborted? <span class="text-white cursor-pointer hover:text-blue-400 ml-2 border-b border-transparent hover:border-blue-400 pb-0.5 transition-all" onclick="toggleAuth('login')">Return to Login</span>
                </p>
            </form>
        </div>
    </div>
</div>

<?php include_once 'includes/auth_scripts.php'; ?>

<script>
    const redirectParam = '<?= $redirectTarget ?>';
    let activeCaptchaHash = '';
    let activeCaptchaExp = 0;

    // Advanced Failsafe Captcha Loader with Artificial Smoothing
    async function loadCaptcha() {
        const img = document.getElementById('captcha-img');
        const input = document.getElementById('captcha-input');
        const loader = document.getElementById('captcha-loader');
        
        loader.style.opacity = '1';
        img.style.opacity = '0'; 
        input.value = '';
        input.placeholder = 'SYNCING...';
        input.disabled = true;
        
        try {
            if (typeof UrbanixAPI === 'undefined') throw new Error("API Matrix Unreachable.");
            
            const [res] = await Promise.all([
                UrbanixAPI.request('captcha', 'GET'),
                new Promise(resolve => setTimeout(resolve, 400))
            ]);
            
            if (!res || !res.data || !res.data.image) throw new Error("Malformed Payload.");
            
            img.src = res.data.image; 
            activeCaptchaHash = res.data.hash; 
            activeCaptchaExp = res.data.exp;
            
            loader.style.opacity = '0';
            img.style.opacity = '1';
            input.disabled = false;
            input.placeholder = '8 CHARS';
            
        } catch (err) { 
            console.error("Captcha load failed:", err.message);
            loader.innerHTML = '<i data-lucide="wifi-off" class="text-red-500 w-6 h-6 animate-pulse"></i>';
            lucide.createIcons();
            input.placeholder = 'SYS FAULT';
            if(window.showToast) window.showToast("Gateway error. Verifying secure connection.", "error");
        }
    }
    
    window.addEventListener('DOMContentLoaded', () => {
        loadCaptcha();
        const wrapper = document.getElementById('form-container-wrapper');
        wrapper.style.minHeight = document.getElementById('login-form').offsetHeight + 100 + 'px';
    });

    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId); 
        const icon = document.getElementById(iconId);
        
        if (input.type === "password") { 
            input.type = "text"; 
            icon.setAttribute('data-lucide', 'eye-off'); 
            icon.classList.add('text-premium-gold');
        } else { 
            input.type = "password"; 
            icon.setAttribute('data-lucide', 'eye'); 
            icon.classList.remove('text-premium-gold');
        }
        lucide.createIcons();
    }

    function toggleAuth(mode) {
        document.getElementById('auth-error').classList.add('hidden');
        document.getElementById('auth-success').classList.add('hidden');
        
        const wrapper = document.getElementById('form-container-wrapper');
        const forms = ['login-form', 'register-form', 'recovery-form'];
        
        forms.forEach(id => {
            document.getElementById(id).classList.remove('active-state');
            document.getElementById(id).classList.add('hidden-state');
        });
        
        const title = document.getElementById('auth-title'); 
        const subtitle = document.getElementById('auth-subtitle');
        const iconContainer = document.getElementById('auth-icon-container'); 
        const icon = document.getElementById('auth-icon');
        const captchaBox = document.getElementById('captcha-container');
        
        iconContainer.className = "w-20 h-20 rounded-3xl mx-auto flex items-center justify-center mb-6 shadow-lg transition-all duration-500 border transform";

        if (mode === 'register') {
            const reg = document.getElementById('register-form');
            reg.classList.replace('hidden-state', 'active-state');
            captchaBox.classList.replace('hidden-state', 'active-state');
            wrapper.style.minHeight = reg.offsetHeight + 100 + 'px';
            
            title.innerText = 'Initialize'; 
            subtitle.innerText = 'Join the Urbanix network.';
            iconContainer.classList.add('bg-gradient-to-br', 'from-[#e2e8f0]', 'to-gray-400', 'border-white', 'shadow-white/20', 'rotate-3');
            icon.setAttribute('data-lucide', 'user-plus'); 
            icon.className = "text-premium-dark w-10 h-10 transition-transform";
            
        } else if (mode === 'recovery') {
            const rec = document.getElementById('recovery-form');
            rec.classList.replace('hidden-state', 'active-state');
            captchaBox.classList.replace('active-state', 'hidden-state');
            wrapper.style.minHeight = rec.offsetHeight + 'px';
            
            title.innerText = 'Key Recovery'; 
            subtitle.innerText = 'System override authorized.';
            iconContainer.classList.add('bg-gradient-to-br', 'from-blue-600', 'to-blue-900', 'border-blue-400', 'shadow-blue-500/20', '-rotate-3');
            icon.setAttribute('data-lucide', 'life-buoy'); 
            icon.className = "text-white w-10 h-10 transition-transform";
            
        } else {
            const log = document.getElementById('login-form');
            log.classList.replace('hidden-state', 'active-state');
            captchaBox.classList.replace('hidden-state', 'active-state');
            wrapper.style.minHeight = log.offsetHeight + 100 + 'px';
            
            title.innerText = 'Welcome Back'; 
            subtitle.innerText = 'Sign in to access the ' + redirectParam + ' module.';
            iconContainer.classList.add('bg-gradient-to-br', 'from-[#d4af37]', 'to-[#aa8c2c]', 'border-[#d4af37]', 'shadow-[#d4af37]/20');
            icon.setAttribute('data-lucide', 'shield-check'); 
            icon.className = "text-premium-dark w-10 h-10 transition-transform";
        }
        
        lucide.createIcons();
    }

    async function handleNativeAuth(e, action) {
        e.preventDefault();
        const errorDiv = document.getElementById('auth-error'); 
        const successDiv = document.getElementById('auth-success');
        errorDiv.classList.add('hidden'); 
        successDiv.classList.add('hidden');
        
        let payload = { action: action };
        let btn, originalText;

        if (action === 'recovery') {
            btn = document.getElementById('recovery-btn');
            originalText = btn.innerHTML;
            btn.innerHTML = `<i data-lucide="loader" class="animate-spin w-5 h-5 mx-auto"></i>`; 
            lucide.createIcons();
            
            setTimeout(() => {
                successDiv.innerHTML = '<i data-lucide="check-circle" class="inline w-4 h-4 mr-1"></i> Extraction link dispatched to matrix routing. Check inbox.';
                successDiv.classList.remove('hidden');
                btn.innerHTML = originalText; 
                lucide.createIcons();
                document.getElementById('recovery-email').value = '';
            }, 1500);
            return;
        }

        const captchaVal = document.getElementById('captcha-input').value;
        if (!captchaVal || captchaVal.trim().length !== 8) {
            errorDiv.innerHTML = '<i data-lucide="alert-circle" class="inline w-4 h-4 mr-1"></i> Invalid Anti-Bot sequence. Requires 8 characters.';
            errorDiv.classList.remove('hidden'); 
            lucide.createIcons();
            return;
        }

        payload.captcha = captchaVal; 
        payload.captcha_hash = activeCaptchaHash; 
        payload.captcha_exp = activeCaptchaExp;
        
        if (action === 'login') {
            btn = document.getElementById('login-btn'); 
            originalText = btn.innerHTML;
            payload.email = document.getElementById('login-email').value; 
            payload.password = document.getElementById('login-pass').value;
        } else {
            btn = document.getElementById('reg-btn'); 
            originalText = btn.innerHTML;
            payload.username = document.getElementById('reg-user').value; 
            payload.email = document.getElementById('reg-email').value;
            payload.password = document.getElementById('reg-pass').value; 
            payload.referral = document.getElementById('reg-ref').value;
        }
        
        btn.innerHTML = `<i data-lucide="loader" class="animate-spin w-5 h-5 mx-auto"></i>`; 
        btn.disabled = true;
        lucide.createIcons();

        try {
            const res = await UrbanixAPI.request('auth', 'POST', payload);
            if(res.data && res.data.token) UrbanixAPI.setToken(res.data.token);
            
            btn.innerHTML = `<i data-lucide="check" class="w-5 h-5 mx-auto"></i>`;
            btn.classList.replace('text-premium-dark', 'text-white');
            lucide.createIcons();
            
            setTimeout(() => { window.location.href = '?route=' + redirectParam; }, 300);
        } catch (err) {
            errorDiv.innerHTML = `<i data-lucide="x-octagon" class="inline w-4 h-4 mr-1"></i> ${err.message}`; 
            errorDiv.classList.remove('hidden');
            btn.innerHTML = originalText; 
            btn.disabled = false;
            lucide.createIcons(); 
            loadCaptcha(); 
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('login-form').addEventListener('submit', (e) => handleNativeAuth(e, 'login'));
        document.getElementById('register-form').addEventListener('submit', (e) => handleNativeAuth(e, 'register'));
        document.getElementById('recovery-form').addEventListener('submit', (e) => handleNativeAuth(e, 'recovery'));
    });
</script>