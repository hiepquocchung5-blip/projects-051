<?php
// /frontend/includes/footer_minimal.php
// Closes auth layout and runs subtle 3D background
?>
</div> <!-- End UI Layer -->
<script>
    lucide.createIcons();
    
    const scene = new THREE.Scene();
    scene.fog = new THREE.FogExp2(0x0a0a0c, 0.004);
    const camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
    const renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
    
    renderer.setSize(window.innerWidth, window.innerHeight);
    document.getElementById('canvas-container').appendChild(renderer.domElement);

    const gridHelper = new THREE.GridHelper(300, 60, 0x333333, 0x1a1a24);
    gridHelper.position.y = -15;
    scene.add(gridHelper);
    camera.position.z = 30; camera.position.y = 5;

    function animate() {
        requestAnimationFrame(animate);
        gridHelper.position.z = (gridHelper.position.z + 0.02) % 5; 
        renderer.render(scene, camera);
    }
    window.onload = animate;
</script>
</body>
</html>