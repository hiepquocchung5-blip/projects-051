<?php
// /frontend/pages/auth.php
// Premium Auth Screen with Floating Labels & Safe Error Catching
$redirectTarget = isset($_GET['redirect']) ? htmlspecialchars($_GET['redirect']) : 'home';
?>
<style>
    /* Premium Floating Label CSS */
    .float-input:focus ~ .float-label,
    .float-input:not(:placeholder-shown) ~ .float-label {
        transform: translateY(-1.5rem) scale(0.85);
        color: #d4af37; /* Premium Gold */
    }
</style>

<div class="flex items-center justify-center min-h-screen w-full px-4 relative z-10">
    <div class="glass-panel p-8 rounded-3xl w-full max-w-md relative overflow-hidden transition-all duration-500 shadow-2xl">
        
        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-premium-gold to-transparent opacity-50"></div>

        <div class="text-center mb-8 pt-4">
            <div class="w-16 h-16 bg-gradient-to-br from-premium-gold to-premium-goldDark rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-lg shadow-premium-gold/20">
                <i data-lucide="lock" class="text-premium-dark w-8 h-8"></i>
            </div>
            <h2 class="text-2xl font-bold text-white tracking-wide" id="auth-title">Welcome Back</h2>
            <p class="text-sm text-gray-400 mt-2 font-medium" id="auth-subtitle">Sign in to access the <?= ucfirst($redirectTarget) ?> module.</p>
        </div>

        <div id="auth-error" class="hidden bg-red-900/30 border border-red-500/50 text-red-400 p-4 rounded-xl text-xs mb-6 text-center shadow-inner font-mono break-words"></div>

        <form id="login-form" class="space-y-6 transition-all duration-300">
            <div class="relative">
                <input type="email" id="login-email" placeholder=" " required class="float-input w-full bg-black/40 border border-gray-700 rounded-xl px-4 pt-6 pb-2 text-white outline-none focus:border-premium-gold focus:ring-1 focus:ring-premium-gold/50 transition-all peer">
                <label class="float-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 pointer-events-none">Email Address</label>
            </div>
            <div class="relative">
                <input type="password" id="login-pass" placeholder=" " required class="float-input w-full bg-black/40 border border-gray-700 rounded-xl px-4 pt-6 pb-2 text-white outline-none focus:border-premium-gold focus:ring-1 focus:ring-premium-gold/50 transition-all peer tracking-widest">
                <label class="float-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 pointer-events-none">Password</label>
            </div>
            
            <div class="flex justify-end">
                <a href="#" class="text-xs text-gray-500 hover:text-premium-gold transition-colors font-medium">Forgot Password?</a>
            </div>

            <button type="submit" id="login-btn" class="w-full bg-gradient-to-r from-premium-gold to-premium-goldDark text-premium-dark font-bold py-4 rounded-xl mt-2 hover:shadow-lg hover:shadow-premium-gold/20 transition-all text-sm flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest">
                Authenticate
            </button>
            <p class="text-center text-sm text-gray-500 mt-6 font-medium">
                New Operative? <span class="text-white cursor-pointer hover:text-premium-gold transition-colors" onclick="toggleAuth('register')">Create Account</span>
            </p>
        </form>

        <form id="register-form" class="space-y-6 hidden transition-all duration-300">
            <div class="relative">
                <input type="text" id="reg-user" placeholder=" " required class="float-input w-full bg-black/40 border border-gray-700 rounded-xl px-4 pt-6 pb-2 text-white outline-none focus:border-premium-silver focus:ring-1 focus:ring-premium-silver/50 transition-all peer">
                <label class="float-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 pointer-events-none">Operative Alias</label>
            </div>
            <div class="relative">
                <input type="email" id="reg-email" placeholder=" " required class="float-input w-full bg-black/40 border border-gray-700 rounded-xl px-4 pt-6 pb-2 text-white outline-none focus:border-premium-silver focus:ring-1 focus:ring-premium-silver/50 transition-all peer">
                <label class="float-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 pointer-events-none">Email Address</label>
            </div>
            <div class="relative">
                <input type="password" id="reg-pass" placeholder=" " required class="float-input w-full bg-black/40 border border-gray-700 rounded-xl px-4 pt-6 pb-2 text-white outline-none focus:border-premium-silver focus:ring-1 focus:ring-premium-silver/50 transition-all peer tracking-widest">
                <label class="float-label absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] left-4 pointer-events-none">Encryption Key</label>
            </div>
            
            <button type="submit" id="reg-btn" class="w-full bg-premium-silver text-premium-dark font-bold py-4 rounded-xl mt-4 hover:bg-white transition-all text-sm flex justify-center items-center gap-2 active:scale-95 uppercase tracking-widest">
                Initialize Profile
            </button>
            <p class="text-center text-sm text-gray-500 mt-6 font-medium">
                Already registered? <span class="text-white cursor-pointer hover:text-premium-silver transition-colors" onclick="toggleAuth('login')">Return to Login</span>
            </p>
        </form>
    </div>
</div>

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

    function handleNativeAuth(e, action) {
        e.preventDefault();
        const errorDiv = document.getElementById('auth-error');
        errorDiv.classList.add('hidden');
        const payload = { action: action };
        
        if (action === 'login') {
            payload.email = document.getElementById('login-email').value;
            payload.password = document.getElementById('login-pass').value;
            document.getElementById('login-btn').innerHTML = `<i data-lucide="loader" class="animate-spin w-5 h-5"></i>`;
        } else {
            payload.username = document.getElementById('reg-user').value;
            payload.email = document.getElementById('reg-email').value;
            payload.password = document.getElementById('reg-pass').value;
            document.getElementById('reg-btn').innerHTML = `<i data-lucide="loader" class="animate-spin w-5 h-5"></i>`;
        }
        lucide.createIcons();

        // BULLETPROOF FETCH LOGIC
        fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/native_auth.php', {
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' }, 
            body: JSON.stringify(payload)
        })
        .then(async res => {
            // First, get the raw text instead of strictly parsing JSON
            const text = await res.text();
            try {
                // Attempt to parse it
                return JSON.parse(text);
            } catch (err) {
                // If it fails, log the raw PHP output to console and throw an error
                console.error("SERVER CRASH OUTPUT:", text);
                throw new Error("Server returned an invalid response. Check console for PHP errors.");
            }
        })
        .then(data => {
            if(data.status === 'success') {
                window.location.href = '?route=' + redirectParam;
            } else {
                errorDiv.innerText = "> " + data.message;
                errorDiv.classList.remove('hidden');
                resetButtons(action);
            }
        })
        .catch(err => {
            // This catches network errors AND our custom JSON parse error
            errorDiv.innerText = "> FATAL ERR: " + err.message;
            errorDiv.classList.remove('hidden');
            resetButtons(action);
        });
    }

    function resetButtons(action) {
        if(action === 'login') document.getElementById('login-btn').innerHTML = `Authenticate`;
        else document.getElementById('reg-btn').innerHTML = `Initialize Profile`;
        lucide.createIcons();
    }

    document.getElementById('login-form').addEventListener('submit', (e) => handleNativeAuth(e, 'login'));
    document.getElementById('register-form').addEventListener('submit', (e) => handleNativeAuth(e, 'register'));
</script>