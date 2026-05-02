<?php
// /frontend/includes/footer.php
// Premium Metallic Edition: Closes layout, injects Mobile Nav, Logout Protocol, Global Toasts, and runs Three.js
$currentRoute = isset($_GET['route']) ? $_GET['route'] : 'home';
?>
    </main> <!-- End Main Content -->
</div> <!-- End UI Layer -->

<!-- GLOBAL TOAST NOTIFICATION -->
<div id="global-toast" class="fixed top-20 right-4 z-[9999] transform translate-x-[150%] transition-transform duration-500 ease-out flex items-center gap-3 glass-panel px-6 py-4 rounded-2xl shadow-[0_10px_40px_rgba(0,0,0,0.5)] border-l-4 border-premium-gold">
    <div id="toast-icon" class="w-8 h-8 rounded-full bg-premium-gold/20 flex items-center justify-center text-premium-gold border border-premium-gold/50">
        <i data-lucide="info" size="16"></i>
    </div>
    <p id="toast-msg" class="text-sm font-bold text-white tracking-wide font-sans">Notification</p>
</div>

<!-- PREMIUM MOBILE FLOATING BOTTOM NAV -->
<nav class="md:hidden fixed bottom-0 left-0 w-full h-16 z-[100] flex justify-around items-center px-4 pb-safe bg-premium-panel/90 backdrop-blur-xl border-t border-gray-800/80 rounded-t-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.8)]">
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>?route=home" class="flex flex-col items-center justify-center w-1/4 h-full <?= $currentRoute === 'home' ? 'text-premium-gold drop-shadow-[0_0_8px_rgba(212,175,55,0.4)]' : 'text-gray-500 hover:text-gray-300' ?> transition-all active:scale-90">
        <i data-lucide="layout-grid" size="20" class="mb-1"></i>
        <span class="text-[9px] font-bold tracking-widest uppercase">Hub</span>
    </a>
    
    <!-- Floating Play Button -->
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>?route=home" class="relative -top-6 flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-premium-gold to-premium-goldDark shadow-[0_8px_20px_rgba(212,175,55,0.3)] text-premium-dark transition-all transform active:scale-90 z-10 border border-premium-gold/50">
        <i data-lucide="play" size="24" class="fill-current ml-1"></i>
    </a>
    
    <a href="?route=profile" class="flex flex-col items-center justify-center w-1/4 h-full <?= $currentRoute === 'profile' ? 'text-white drop-shadow-[0_0_8px_rgba(255,255,255,0.4)]' : 'text-gray-500 hover:text-gray-300' ?> transition-all active:scale-90">
        <i data-lucide="user" size="20" class="mb-1"></i>
        <span class="text-[9px] font-bold tracking-widest uppercase">Profile</span>
    </a>

    <a href="?route=settings" class="flex flex-col items-center justify-center w-1/4 h-full <?= $currentRoute === 'settings' ? 'text-white drop-shadow-[0_0_8px_rgba(255,255,255,0.4)]' : 'text-gray-500 hover:text-gray-300' ?> transition-all active:scale-90">
        <i data-lucide="settings" size="20" class="mb-1"></i>
        <span class="text-[9px] font-bold tracking-widest uppercase">Settings</span>
    </a>
</nav>

<script>
    lucide.createIcons();

    // GLOBAL TOAST SYSTEM
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
        } else {
            toast.className = "fixed top-20 right-4 z-[9999] transform transition-transform duration-500 ease-out flex items-center gap-3 glass-panel px-6 py-4 rounded-2xl shadow-xl border-l-4 border-premium-gold translate-x-0";
            iconEl.className = "w-8 h-8 rounded-full bg-premium-gold/20 flex items-center justify-center text-premium-gold border border-premium-gold/50";
            iconEl.innerHTML = '<i data-lucide="info" size="16"></i>';
        }
        
        lucide.createIcons();
        
        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-[150%]');
        }, 4000);
    };

    // GLOBAL DISCONNECT PROTOCOL
    function systemLogout() {
        if(confirm("INITIALIZE DISCONNECT: Sever connection to the mainframe?")) {
            fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/logout.php', {credentials: 'include'})
            .then(() => window.location.href = '?route=auth');
        }
    }

    // COOKIE CONSENT FUNCTIONS
    function acceptCookies() {
        localStorage.setItem('cookiesAccepted', 'true');
        const cookieBanner = document.getElementById('cookie-banner');
        if(cookieBanner) {
            cookieBanner.style.transform = 'translate(-50%, 150%)';
            cookieBanner.style.opacity = '0';
            setTimeout(() => cookieBanner.classList.add('hidden'), 700);
        }
    }

    // THREE.JS PREMIUM ENVIRONMENT (Performance Aware)
    const isPerfMode = localStorage.getItem('urbanix_perf_mode') === 'true';

    if (!isPerfMode) {
        const scene = new THREE.Scene();
        scene.fog = new THREE.FogExp2(0x0a0a0c, 0.003); // Metallic Dark Fog
        const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true, powerPreference: "high-performance" });
        
        renderer.setPixelRatio(Math.min(window.devicePixelRatio, 1.5)); // Optimized for mobile
        renderer.setSize(window.innerWidth, window.innerHeight);
        document.getElementById('canvas-container').appendChild(renderer.domElement);

        // Premium Metallic Grid
        const gridHelper = new THREE.GridHelper(300, 60, 0x333333, 0x1a1a24);
        gridHelper.position.y = -20;
        scene.add(gridHelper);

        camera.position.z = 50; camera.position.y = 5;
        let clock = new THREE.Clock();
        
        function animate() {
            requestAnimationFrame(animate);
            const delta = clock.getDelta();
            gridHelper.position.z = (gridHelper.position.z + delta * 2) % 5; // Slower, luxurious pan
            renderer.render(scene, camera);
        }
        
        window.addEventListener('resize', () => {
            camera.aspect = window.innerWidth / window.innerHeight;
            camera.updateProjectionMatrix();
            renderer.setSize(window.innerWidth, window.innerHeight);
        });

        window.init3D = animate;
    } else {
        document.getElementById('canvas-container').style.background = 'radial-gradient(circle at 50% 0%, #151518 0%, #0a0a0c 80%)';
        window.init3D = () => {}; 
    }

    // BOOT SEQUENCE COMPLETION
    function completeBootSequence() {
        const bootloader = document.getElementById('bootloader');
        const uiLayer = document.getElementById('ui-layer');
        const cookieBanner = document.getElementById('cookie-banner');
        
        if(bootloader) {
            bootloader.style.opacity = '0';
            setTimeout(() => bootloader.style.visibility = 'hidden', 600);
        }
        
        if(uiLayer) {
            setTimeout(() => {
                uiLayer.style.opacity = '1';
                if (!localStorage.getItem('cookiesAccepted') && cookieBanner) {
                    setTimeout(() => {
                        cookieBanner.classList.remove('hidden');
                        setTimeout(() => {
                            cookieBanner.style.transform = 'translate(-50%, 0)';
                            cookieBanner.style.opacity = '1';
                        }, 50);
                    }, 800); 
                }
            }, 600);
        }
    }

    window.onload = () => {
        if(typeof window.init3D === 'function') window.init3D();
        if(typeof textInterval !== 'undefined') clearInterval(textInterval);
        setTimeout(completeBootSequence, 2000);
    };
</script>
</body>
</html>