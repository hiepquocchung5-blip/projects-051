<?php
// /frontend/includes/footer.php
// Closes layout, initializes icons, and runs the Three.js background
?>
    </main> <!-- End Main Content -->
</div> <!-- End UI Layer -->

<script>
    // Initialize Icons
    lucide.createIcons();

    // -----------------------------------------
    // THREE.JS CIRCUIT CHAOS BACKGROUND
    // -----------------------------------------
    const scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(0x0a0a0f, 0.002);
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.getElementById('canvas-container').appendChild(renderer.domElement);

    const gridHelper = new THREE.GridHelper(200, 100, 0x00f0ff, 0x13131f);
    gridHelper.position.y = -10;
    scene.add(gridHelper);

    camera.position.z = 30;
    camera.position.y = 5;

    function animate() {
        requestAnimationFrame(animate);
        gridHelper.position.z = (Date.now() * 0.001 * 10) % 2; // Forward motion effect
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