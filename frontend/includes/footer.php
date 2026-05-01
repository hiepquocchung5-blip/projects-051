<?php
// /frontend/includes/footer.php
// Closes layout, injects Mobile Nav, Logout Protocol, and runs Three.js
$currentRoute = isset($_GET['route']) ? $_GET['route'] : 'home';
?>
    </main> <!-- End Main Content -->
</div> <!-- End UI Layer -->

<!-- MOBILE FLOATING BOTTOM NAV (App Style) -->
<nav class="md:hidden glass-nav-mobile fixed bottom-0 left-0 w-full h-16 z-[100] flex justify-around items-center px-2 pb-safe shadow-[0_-10px_30px_rgba(0,0,0,0.8)]">
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>" class="flex flex-col items-center justify-center w-1/4 h-full <?= $currentRoute === 'home' ? 'nav-active' : 'text-gray-500' ?> transition active:scale-90">
        <i data-lucide="layout-dashboard" size="20" class="mb-1"></i>
        <span class="text-[10px] font-mono font-bold tracking-widest uppercase">Hub</span>
    </a>
    
    <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>" class="relative -top-5 flex items-center justify-center w-14 h-14 rounded-full bg-black border-2 border-neon-cyan shadow-[0_0_15px_rgba(0,240,255,0.4)] text-neon-cyan transition transform active:scale-90 z-10">
        <i data-lucide="gamepad-2" size="24"></i>
    </a>
    
    <a href="?route=profile" class="flex flex-col items-center justify-center w-1/4 h-full <?= $currentRoute === 'profile' ? 'nav-active text-neon-purple' : 'text-gray-500' ?> transition active:scale-90">
        <i data-lucide="user" size="20" class="mb-1"></i>
        <span class="text-[10px] font-mono font-bold tracking-widest uppercase">Profile</span>
    </a>

    <a href="?route=settings" class="flex flex-col items-center justify-center w-1/4 h-full <?= $currentRoute === 'settings' ? 'nav-active text-green-400' : 'text-gray-500' ?> transition active:scale-90">
        <i data-lucide="settings" size="20" class="mb-1"></i>
        <span class="text-[10px] font-mono font-bold tracking-widest uppercase">Settings</span>
    </a>
</nav>

<script>
    lucide.createIcons();

    // GLOBAL DISCONNECT PROTOCOL
    function systemLogout() {
        if(confirm("INITIALIZE DISCONNECT: Are you sure you want to sever connection to the mainframe?")) {
            fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/logout.php')
            .then(() => window.location.href = '<?= defined("BASE_URL") ? BASE_URL : "/" ?>');
        }
    }

    // THREE.JS CIRCUIT CHAOS BACKGROUND
    const scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(0x0a0a0f, 0.002);
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true, powerPreference: "high-performance" });
    
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.getElementById('canvas-container').appendChild(renderer.domElement);

    const gridHelper = new THREE.GridHelper(200, 100, 0x00f0ff, 0x13131f);
    gridHelper.position.y = -15;
    scene.add(gridHelper);

    camera.position.z = 40; camera.position.y = 10;
    let clock = new THREE.Clock();
    
    function animate() {
        requestAnimationFrame(animate);
        const delta = clock.getDelta();
        gridHelper.position.z = (gridHelper.position.z + delta * 5) % 2; 
        renderer.render(scene, camera);
    }
    
    window.addEventListener('resize', () => {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });

    window.onload = animate;
</script>
</body>
</html>