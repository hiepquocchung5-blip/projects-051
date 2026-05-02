<?php
// /frontend/games/gridwars.php
// Premium Metallic Arena Survival
?>
<div class="flex flex-col items-center w-full relative h-full">
    <div class="absolute top-4 left-6 z-10 flex items-center gap-3">
        <div class="bg-black/50 border border-gray-700/50 backdrop-blur-md px-4 py-2 rounded-xl flex items-center gap-2 shadow-lg">
            <i data-lucide="crosshair" size="16" class="text-red-500 animate-pulse"></i>
            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Time: <span id="bw-time" class="text-white text-sm ml-1 font-mono">0</span>s</span>
        </div>
    </div>
    
    <canvas id="bw-canvas" class="bg-[#050507] rounded-3xl shadow-[inset_0_0_80px_rgba(0,0,0,0.8)] touch-none w-full max-w-[900px] aspect-[16/10] border border-gray-800/80 relative z-0"></canvas>

    <div id="bw-overlay" class="absolute inset-0 bg-black/85 flex flex-col items-center justify-center rounded-3xl z-20 backdrop-blur-md border border-gray-800/50 shadow-2xl transition-all duration-300">
        <div class="w-16 h-16 bg-gradient-to-br from-red-500/20 to-red-900/20 border border-red-500/50 rounded-2xl flex items-center justify-center mb-6 shadow-[0_0_30px_rgba(239,68,68,0.2)]">
            <i data-lucide="crosshair" class="text-red-500 w-8 h-8"></i>
        </div>
        <h2 class="text-3xl sm:text-5xl font-black text-white mb-2 tracking-widest uppercase text-center drop-shadow-md">Grid Wars</h2>
        <p class="text-gray-400 mb-8 text-xs sm:text-sm text-center max-w-[70%] leading-relaxed font-sans">Touch and drag to steer. Combat systems auto-engage nearest targets.</p>
        <button onclick="startBWGame()" class="bg-white text-black px-10 py-4 rounded-xl font-bold tracking-widest active:scale-95 shadow-xl hover:bg-gray-200 transition-all text-xs uppercase flex items-center gap-2">
            <i data-lucide="swords" size="16"></i> Enter Arena
        </button>
    </div>
</div>

<script>
    const canvasBW = document.getElementById('bw-canvas');
    const ctxBW = canvasBW.getContext('2d');
    
    function resizeCanvas() {
        const parent = canvasBW.parentElement;
        canvasBW.width = parent.clientWidth;
        canvasBW.height = parent.clientWidth * (10/16);
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();
    
    let bwActive = false; let animIdBW; let timeSurvived = 0; let timeInterval;
    let targetTouch = null; 
    let player, projectiles, enemies;

    class Player {
        constructor() { this.x = canvasBW.width/2; this.y = canvasBW.height/2; this.radius = 12; this.speed = 3.5; }
        draw() {
            ctxBW.beginPath(); ctxBW.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctxBW.fillStyle = '#e2e8f0'; ctxBW.shadowBlur = 20; ctxBW.shadowColor = '#e2e8f0';
            ctxBW.fill(); ctxBW.closePath(); ctxBW.shadowBlur = 0;
            
            // Core
            ctxBW.beginPath(); ctxBW.arc(this.x, this.y, 4, 0, Math.PI * 2);
            ctxBW.fillStyle = '#0a0a0c'; ctxBW.fill(); ctxBW.closePath();
        }
        update() {
            if(targetTouch) {
                const dx = targetTouch.x - this.x;
                const dy = targetTouch.y - this.y;
                const dist = Math.hypot(dx, dy);
                if(dist > 5) { this.x += (dx / dist) * this.speed; this.y += (dy / dist) * this.speed; }
            }
            this.x = Math.max(this.radius, Math.min(canvasBW.width - this.radius, this.x));
            this.y = Math.max(this.radius, Math.min(canvasBW.height - this.radius, this.y));
            this.draw();
        }
    }

    class Projectile {
        constructor(x, y, velocity) { this.x = x; this.y = y; this.radius = 3; this.velocity = velocity; }
        draw() {
            ctxBW.beginPath(); ctxBW.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctxBW.fillStyle = '#d4af37'; ctxBW.shadowBlur = 10; ctxBW.shadowColor = '#d4af37';
            ctxBW.fill(); ctxBW.shadowBlur = 0;
        }
        update() { this.x += this.velocity.x; this.y += this.velocity.y; this.draw(); }
    }

    class Enemy {
        constructor(x, y) { this.x = x; this.y = y; this.radius = 10; this.speed = 1.0 + (timeSurvived * 0.025); }
        draw() {
            ctxBW.beginPath();
            ctxBW.moveTo(this.x, this.y - this.radius);
            ctxBW.lineTo(this.x + this.radius, this.y + this.radius);
            ctxBW.lineTo(this.x - this.radius, this.y + this.radius);
            ctxBW.fillStyle = '#ef4444'; ctxBW.shadowBlur = 15; ctxBW.shadowColor = '#ef4444';
            ctxBW.fill(); ctxBW.shadowBlur = 0;
        }
        update() {
            const angle = Math.atan2(player.y - this.y, player.x - this.x);
            this.x += Math.cos(angle) * this.speed;
            this.y += Math.sin(angle) * this.speed;
            this.draw();
        }
    }

    function spawnEnemy() {
        if(!bwActive) return;
        let x, y;
        if(Math.random() < 0.5) { x = Math.random() < 0.5 ? -30 : canvasBW.width + 30; y = Math.random() * canvasBW.height; } 
        else { x = Math.random() * canvasBW.width; y = Math.random() < 0.5 ? -30 : canvasBW.height + 30; }
        enemies.push(new Enemy(x, y));
        setTimeout(spawnEnemy, Math.max(300, 1000 - (timeSurvived * 15)));
    }

    setInterval(() => {
        if(!bwActive || !gameLoopActive || enemies.length === 0) return;
        let closest = null; let minDist = Infinity;
        enemies.forEach(e => {
            const dist = Math.hypot(player.x - e.x, player.y - e.y);
            if(dist < minDist) { minDist = dist; closest = e; }
        });
        if(closest) {
            const angle = Math.atan2(closest.y - player.y, closest.x - player.x);
            const velocity = { x: Math.cos(angle) * 8, y: Math.sin(angle) * 8 };
            projectiles.push(new Projectile(player.x, player.y, velocity));
        }
    }, 400); 

    function startBWGame() {
        resizeCanvas();
        player = new Player(); projectiles = []; enemies = []; targetTouch = null;
        timeSurvived = 0; bwActive = true;
        document.getElementById('bw-overlay').classList.add('hidden');
        
        timeInterval = setInterval(() => {
            if(!gameLoopActive) return;
            timeSurvived++;
            document.getElementById('bw-time').innerText = timeSurvived;
        }, 1000);

        spawnEnemy(); animateBW();
    }

    function animateBW() {
        if(!bwActive || !gameLoopActive) { if(bwActive) requestAnimationFrame(animateBW); return; }
        animIdBW = requestAnimationFrame(animateBW);
        
        ctxBW.fillStyle = 'rgba(5, 5, 7, 0.4)'; // Premium motion blur
        ctxBW.fillRect(0, 0, canvasBW.width, canvasBW.height);
        
        // Metallic Grid Background
        ctxBW.strokeStyle = 'rgba(255, 255, 255, 0.03)';
        ctxBW.lineWidth = 1;
        for(let i=0; i<canvasBW.width; i+=40) { ctxBW.beginPath(); ctxBW.moveTo(i, 0); ctxBW.lineTo(i, canvasBW.height); ctxBW.stroke(); }
        for(let j=0; j<canvasBW.height; j+=40) { ctxBW.beginPath(); ctxBW.moveTo(0, j); ctxBW.lineTo(canvasBW.width, j); ctxBW.stroke(); }

        player.update();

        projectiles.forEach((p, i) => {
            p.update();
            if(p.x < 0 || p.x > canvasBW.width || p.y < 0 || p.y > canvasBW.height) projectiles.splice(i, 1);
        });

        enemies.forEach((e, ei) => {
            e.update();
            if(Math.hypot(player.x - e.x, player.y - e.y) - e.radius - player.radius < 1) endBWGame();
            
            projectiles.forEach((p, pi) => {
                if(Math.hypot(p.x - e.x, p.y - e.y) - e.radius - p.radius < 1) {
                    enemies.splice(ei, 1); projectiles.splice(pi, 1);
                }
            });
        });
    }

    function endBWGame() {
        bwActive = false; clearInterval(timeInterval); cancelAnimationFrame(animIdBW);
        let reward = timeSurvived * 150;
        
        const overlay = document.getElementById('bw-overlay');
        overlay.classList.remove('hidden');
        overlay.innerHTML = `
            <div class="w-16 h-16 bg-red-900/30 border border-red-500 rounded-2xl flex items-center justify-center mb-6 shadow-inner shadow-red-500/20">
                <i data-lucide="skull" class="text-red-500 w-8 h-8"></i>
            </div>
            <h2 class="text-3xl sm:text-4xl font-black text-white mb-2 uppercase tracking-widest text-center">Arena Failed</h2>
            <div class="flex flex-col items-center gap-2 mb-8 bg-black/50 p-4 rounded-xl border border-gray-800 w-3/4">
                <p class="text-gray-400 font-mono text-xs uppercase tracking-widest">Time: <span class="text-white">${timeSurvived}s</span></p>
                <p class="text-premium-gold font-mono font-bold text-sm uppercase tracking-widest drop-shadow-md">Yield: +${reward}</p>
            </div>
            <button onclick="startBWGame()" class="bg-white text-black px-8 py-4 rounded-xl font-bold tracking-widest active:scale-95 shadow-xl hover:bg-gray-200 transition-all text-xs uppercase flex items-center gap-2">
                <i data-lucide="refresh-cw" size="16"></i> Respawn
            </button>
        `;
        lucide.createIcons();

        if(reward > 0) {
            fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/wallet.php', { method: 'POST', credentials: 'include', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({action: 'game_win', amount: reward}) })
            .then(res => { if(window.showToast) window.showToast(`Yield Transferred: +${reward}`, 'success'); });
        }
    }

    // Controls
    function handlePointer(e) {
        if(!bwActive) return;
        e.preventDefault();
        const rect = canvasBW.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        targetTouch = { x: clientX - rect.left, y: clientY - rect.top };
    }
    
    canvasBW.addEventListener('mousedown', handlePointer);
    canvasBW.addEventListener('mousemove', (e) => { if(targetTouch) handlePointer(e); });
    canvasBW.addEventListener('mouseup', () => targetTouch = null);
    canvasBW.addEventListener('mouseleave', () => targetTouch = null);
    canvasBW.addEventListener('touchstart', handlePointer, {passive: false});
    canvasBW.addEventListener('touchmove', handlePointer, {passive: false});
    canvasBW.addEventListener('touchend', () => targetTouch = null);
</script>