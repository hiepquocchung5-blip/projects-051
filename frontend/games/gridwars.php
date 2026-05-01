<?php
// /frontend/games/gridwars.php
// Battle Royale Lite - Arena Survival (WASD + Mouse)
?>
<div class="flex flex-col items-center w-full relative h-full">
    <div class="absolute top-4 left-4 z-10 text-white font-mono text-xl">SURVIVAL TIME: <span id="bw-time" class="text-red-500">0</span>s</div>
    
    <canvas id="bw-canvas" width="800" height="500" class="bg-[#0a0a0f] border border-red-500/30 rounded-lg shadow-[inset_0_0_50px_rgba(239,68,68,0.1)] cursor-crosshair"></canvas>

    <div id="bw-overlay" class="absolute inset-0 bg-black/90 flex flex-col items-center justify-center rounded-lg z-20">
        <h2 class="text-4xl font-black text-red-500 mb-2 uppercase">Grid Wars Lite</h2>
        <p class="text-gray-400 mb-2 font-mono">WASD to move. Mouse to aim & shoot.</p>
        <p class="text-gray-500 mb-8 font-mono text-sm">Survive the incoming rogue drones.</p>
        <button onclick="startBWGame()" class="bg-red-600 text-white px-8 py-3 rounded hover:bg-white hover:text-black transition font-bold tracking-widest">
            ENTER ARENA
        </button>
    </div>
</div>

<script>
    const canvasBW = document.getElementById('bw-canvas');
    const ctxBW = canvasBW.getContext('2d');
    
    let bwActive = false;
    let animIdBW;
    let timeSurvived = 0;
    let timeInterval;

    const keys = { w: false, a: false, s: false, d: false };
    const mouse = { x: canvasBW.width/2, y: canvasBW.height/2 };

    let player, projectiles, enemies;

    class Player {
        constructor() { this.x = canvasBW.width/2; this.y = canvasBW.height/2; this.radius = 15; this.speed = 4; }
        draw() {
            ctxBW.beginPath();
            ctxBW.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctxBW.fillStyle = '#00f0ff';
            ctxBW.fill();
            ctxBW.closePath();
        }
        update() {
            if(keys.w && this.y - this.radius > 0) this.y -= this.speed;
            if(keys.s && this.y + this.radius < canvasBW.height) this.y += this.speed;
            if(keys.a && this.x - this.radius > 0) this.x -= this.speed;
            if(keys.d && this.x + this.radius < canvasBW.width) this.x += this.speed;
            this.draw();
        }
    }

    class Projectile {
        constructor(x, y, velocity) { this.x = x; this.y = y; this.radius = 4; this.velocity = velocity; }
        draw() {
            ctxBW.beginPath();
            ctxBW.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctxBW.fillStyle = '#fff';
            ctxBW.fill();
        }
        update() {
            this.x += this.velocity.x; this.y += this.velocity.y;
            this.draw();
        }
    }

    class Enemy {
        constructor(x, y) { this.x = x; this.y = y; this.radius = 12; }
        draw() {
            ctxBW.beginPath();
            ctxBW.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctxBW.fillStyle = '#ef4444';
            ctxBW.fill();
        }
        update() {
            const angle = Math.atan2(player.y - this.y, player.x - this.x);
            this.x += Math.cos(angle) * 1.5;
            this.y += Math.sin(angle) * 1.5;
            this.draw();
        }
    }

    function spawnEnemy() {
        if(!bwActive) return;
        let x, y;
        if(Math.random() < 0.5) {
            x = Math.random() < 0.5 ? 0 - 30 : canvasBW.width + 30;
            y = Math.random() * canvasBW.height;
        } else {
            x = Math.random() * canvasBW.width;
            y = Math.random() < 0.5 ? 0 - 30 : canvasBW.height + 30;
        }
        enemies.push(new Enemy(x, y));
        setTimeout(spawnEnemy, 1000 - (timeSurvived * 10)); // Gets faster
    }

    function startBWGame() {
        player = new Player();
        projectiles = []; enemies = [];
        timeSurvived = 0; bwActive = true;
        document.getElementById('bw-overlay').classList.add('hidden');
        
        timeInterval = setInterval(() => {
            if(!gameLoopActive) return;
            timeSurvived++;
            document.getElementById('bw-time').innerText = timeSurvived;
        }, 1000);

        spawnEnemy();
        animateBW();
    }

    function animateBW() {
        if(!bwActive || !gameLoopActive) { 
            if(bwActive) requestAnimationFrame(animateBW); // Just pause visually
            return; 
        }
        
        animIdBW = requestAnimationFrame(animateBW);
        ctxBW.fillStyle = 'rgba(10, 10, 15, 0.2)'; // trailing effect
        ctxBW.fillRect(0, 0, canvasBW.width, canvasBW.height);
        
        player.update();

        // Projectiles
        projectiles.forEach((p, i) => {
            p.update();
            if(p.x < 0 || p.x > canvasBW.width || p.y < 0 || p.y > canvasBW.height) {
                projectiles.splice(i, 1);
            }
        });

        // Enemies & Collision
        enemies.forEach((e, ei) => {
            e.update();
            // Player hit
            const distP = Math.hypot(player.x - e.x, player.y - e.y);
            if(distP - e.radius - player.radius < 1) {
                endBWGame();
            }
            // Projectile hit
            projectiles.forEach((p, pi) => {
                const distShot = Math.hypot(p.x - e.x, p.y - e.y);
                if(distShot - e.radius - p.radius < 1) {
                    enemies.splice(ei, 1);
                    projectiles.splice(pi, 1);
                }
            });
        });
    }

    function endBWGame() {
        bwActive = false;
        clearInterval(timeInterval);
        cancelAnimationFrame(animIdBW);
        
        document.getElementById('bw-overlay').classList.remove('hidden');
        document.getElementById('bw-overlay').innerHTML = `
            <h2 class="text-4xl font-black text-red-500 mb-2 uppercase">Arena Cleared</h2>
            <p class="text-white mb-6 font-mono">Survived: ${timeSurvived}s</p>
            <button onclick="startBWGame()" class="bg-red-600 text-white px-6 py-2 rounded font-bold hover:bg-white hover:text-black">Respawn</button>
        `;

        let reward = timeSurvived * 100;
        if(reward > 0) {
            fetch('<?= API_URL ?>/wallet.php', {
                method: 'POST', body: JSON.stringify({action: 'game_win', amount: reward})
            });
        }
    }

    // Controls
    window.addEventListener('keydown', (e) => {
        if(e.key === 'w' || e.key === 'W') keys.w = true;
        if(e.key === 'a' || e.key === 'A') keys.a = true;
        if(e.key === 's' || e.key === 'S') keys.s = true;
        if(e.key === 'd' || e.key === 'D') keys.d = true;
    });
    window.addEventListener('keyup', (e) => {
        if(e.key === 'w' || e.key === 'W') keys.w = false;
        if(e.key === 'a' || e.key === 'A') keys.a = false;
        if(e.key === 's' || e.key === 'S') keys.s = false;
        if(e.key === 'd' || e.key === 'D') keys.d = false;
    });

    canvasBW.addEventListener('click', (e) => {
        if(!bwActive || !gameLoopActive) return;
        const rect = canvasBW.getBoundingClientRect();
        const mouseX = e.clientX - rect.left;
        const mouseY = e.clientY - rect.top;
        const angle = Math.atan2(mouseY - player.y, mouseX - player.x);
        const velocity = { x: Math.cos(angle) * 8, y: Math.sin(angle) * 8 };
        projectiles.push(new Projectile(player.x, player.y, velocity));
    });
</script>