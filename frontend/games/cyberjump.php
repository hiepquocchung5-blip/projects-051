<?php
// /frontend/games/cyberjump.php
// V4 Parallax Upgrade & API Integration
?>
<div class="flex flex-col items-center w-full relative h-full justify-center min-h-[500px]">
    <div class="absolute top-4 left-6 z-10 bg-black/60 backdrop-blur-md px-4 py-2 rounded-xl border border-gray-800 shadow-lg text-white font-bold text-lg flex items-center gap-3">
        <i data-lucide="activity" size="18" class="text-premium-gold animate-pulse"></i> <span id="jump-score" class="font-mono">0</span>
    </div>
    
    <canvas id="jump-canvas" class="bg-[#050507] rounded-3xl shadow-[0_0_50px_rgba(0,0,0,0.8)] touch-none w-full max-w-[900px] aspect-[16/10] border border-gray-800/80"></canvas>

    <div id="jump-overlay" class="absolute inset-0 bg-black/85 flex flex-col items-center justify-center rounded-3xl z-20 backdrop-blur-md border border-gray-800/50 shadow-2xl transition-all duration-300">
        <div class="w-20 h-20 bg-gradient-to-br from-[#d4af37] to-[#aa8c2c] rounded-3xl flex items-center justify-center mb-6 shadow-lg border border-gray-700">
            <i data-lucide="activity" class="text-black w-10 h-10"></i>
        </div>
        <h2 class="text-3xl sm:text-4xl font-black text-white mb-2 tracking-widest uppercase text-center drop-shadow-md">Cyber Jump</h2>
        <p class="text-gray-400 mb-8 text-sm text-center font-sans max-w-[70%]">Tap screen or Spacebar to traverse the physical firewall.</p>
        <button onclick="startJumpGame()" class="bg-white text-black px-10 py-4 rounded-xl font-black tracking-widest active:scale-95 shadow-xl hover:bg-gray-200 transition-colors text-xs uppercase flex items-center gap-2">
            <i data-lucide="play" size="16"></i> Initialize Run
        </button>
    </div>
</div>

<script>
    // Logic remains nearly identical, updating drawing for V4 and integrating UrbanixAPI
    const canvasJ = document.getElementById('jump-canvas');
    const ctxJ = canvasJ.getContext('2d');
    
    function resizeJumpCanvas() { const parent = canvasJ.parentElement; canvasJ.width = parent.clientWidth; canvasJ.height = parent.clientWidth * (10/16); }
    window.addEventListener('resize', resizeJumpCanvas); resizeJumpCanvas();
    
    let framesJ = 0; let scoreJ = 0; let gameActiveJ = false; let animIdJ;

    const player = {
        x: 60, y: 0, width: 20, height: 20, velocityY: 0, gravity: 0.6, jumpPower: -11, grounded: false,
        draw() {
            ctxJ.fillStyle = '#e2e8f0'; // Silver drone
            ctxJ.shadowBlur = 15; ctxJ.shadowColor = '#e2e8f0';
            ctxJ.fillRect(this.x, this.y, this.width, this.height);
            ctxJ.fillStyle = '#d4af37'; ctxJ.fillRect(this.x+4, this.y+4, this.width-8, this.height-8); // Gold core
            ctxJ.shadowBlur = 0;
        },
        update() {
            this.velocityY += this.gravity; this.y += this.velocityY;
            const floorY = canvasJ.height - 40;
            if (this.y + this.height >= floorY) { this.y = floorY - this.height; this.velocityY = 0; this.grounded = true; } 
            else { this.grounded = false; }
        },
        jump() { if(this.grounded) { this.velocityY = this.jumpPower; this.grounded = false; } }
    };

    const obstacles = {
        items: [], speed: 5.5,
        draw() {
            ctxJ.fillStyle = '#ef4444';
            for (let i = 0; i < this.items.length; i++) {
                let obs = this.items[i];
                ctxJ.beginPath(); ctxJ.moveTo(obs.x + (obs.w/2), obs.y); ctxJ.lineTo(obs.x + obs.w, obs.y + obs.h); ctxJ.lineTo(obs.x, obs.y + obs.h);
                ctxJ.shadowBlur = 10; ctxJ.shadowColor = '#ef4444';
                ctxJ.fill(); ctxJ.shadowBlur = 0;
            }
        },
        update() {
            if (framesJ % 70 === 0) { this.items.push({ x: canvasJ.width, y: canvasJ.height - 40 - 35, w: 25, h: 35, passed: false }); }
            for (let i = 0; i < this.items.length; i++) {
                let obs = this.items[i]; obs.x -= this.speed;
                if (player.x < obs.x + obs.w && player.x + player.width > obs.x && player.y < obs.y + obs.h && player.y + player.height > obs.y) { endJumpGame(); }
                if (obs.x + obs.w < player.x && !obs.passed) {
                    scoreJ++; obs.passed = true; document.getElementById('jump-score').innerText = scoreJ;
                    if(scoreJ % 5 === 0) this.speed += 0.5; 
                }
                if (obs.x + obs.w < 0) this.items.shift();
            }
        }
    };

    function drawFloor() {
        ctxJ.fillStyle = '#0a0a0c'; ctxJ.fillRect(0, canvasJ.height - 40, canvasJ.width, 40);
        ctxJ.strokeStyle = '#d4af37'; ctxJ.lineWidth = 2;
        ctxJ.beginPath(); ctxJ.moveTo(0, canvasJ.height - 40); ctxJ.lineTo(canvasJ.width, canvasJ.height - 40); ctxJ.stroke();
    }

    function animateJ() {
        if(!gameActiveJ || !gameLoopActive) return;
        ctxJ.fillStyle = '#050507'; ctxJ.fillRect(0, 0, canvasJ.width, canvasJ.height);
        
        // V4 Parallax
        ctxJ.fillStyle = 'rgba(255,255,255,0.05)';
        let bgOffset = (framesJ * 0.5) % 100;
        for(let i=-bgOffset; i<canvasJ.width; i+=100) { ctxJ.fillRect(i, canvasJ.height - 100, 20, 60); }
        
        drawFloor(); player.update(); player.draw(); obstacles.update(); obstacles.draw();
        framesJ++; animIdJ = requestAnimationFrame(animateJ);
    }

    function startJumpGame() {
        resizeJumpCanvas();
        player.y = canvasJ.height - 40 - player.height; player.velocityY = 0;
        obstacles.items = []; obstacles.speed = 5.5; scoreJ = 0; framesJ = 0;
        document.getElementById('jump-score').innerText = scoreJ;
        document.getElementById('jump-overlay').classList.add('hidden');
        gameActiveJ = true; animateJ();
    }

    async function endJumpGame() {
        gameActiveJ = false; cancelAnimationFrame(animIdJ);
        let reward = scoreJ * 100;
        
        const overlay = document.getElementById('jump-overlay');
        overlay.classList.remove('hidden');
        overlay.innerHTML = `
            <div class="w-16 h-16 bg-red-900/30 border border-red-500 rounded-2xl flex items-center justify-center mb-6 shadow-inner shadow-red-500/20"><i data-lucide="skull" class="text-red-500 w-8 h-8"></i></div>
            <h2 class="text-3xl font-black text-white mb-2 uppercase tracking-widest text-center">System Crash</h2>
            <div class="flex flex-col items-center gap-2 mb-8 bg-black/50 p-4 rounded-xl border border-gray-800 w-3/4">
                <p class="text-gray-400 font-mono text-xs uppercase tracking-widest">Spikes Cleared: <span class="text-white">${scoreJ}</span></p>
                <p class="text-premium-gold font-mono font-bold text-sm uppercase tracking-widest drop-shadow-md">Yield: +${reward}</p>
            </div>
            <button onclick="startJumpGame()" class="bg-white text-black px-8 py-4 rounded-xl font-bold uppercase tracking-widest text-xs hover:bg-gray-200 active:scale-95 flex items-center gap-2"><i data-lucide="refresh-cw" size="16"></i> Reboot Sequence</button>
        `;
        lucide.createIcons();
        if(reward > 0) {
            try { const res = await UrbanixAPI.request('wallet', 'POST', {action: 'game_win', amount: reward}); if(window.showToast) window.showToast(`Yield Transferred: +${res.data.yield}`, 'success'); } catch (err) {}
        }
    }

    window.addEventListener('keydown', (e) => { if((e.code === 'Space' || e.code === 'ArrowUp') && gameActiveJ) { e.preventDefault(); player.jump(); }});
    canvasJ.addEventListener('pointerdown', (e) => { e.preventDefault(); if(gameActiveJ) player.jump(); });
</script>