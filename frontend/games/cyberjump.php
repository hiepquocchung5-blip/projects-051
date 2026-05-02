<?php
// /frontend/games/cyberjump.php
// Side-scrolling platformer (Mario physics style)
?>
<div class="flex flex-col items-center w-full relative h-full">
    <div class="absolute top-4 left-6 z-10 text-white font-bold text-lg drop-shadow-md flex items-center gap-2">
        <i data-lucide="activity" size="18" class="text-premium-gold"></i> <span id="jump-score">0</span>
    </div>
    
    <canvas id="jump-canvas" class="bg-[#0a0a0c] rounded-2xl shadow-inner touch-none w-full max-w-[800px] aspect-[16/10] border border-gray-800"></canvas>

    <div id="jump-overlay" class="absolute inset-0 bg-black/80 flex flex-col items-center justify-center rounded-2xl z-20 backdrop-blur-md">
        <div class="w-16 h-16 bg-gradient-to-br from-[#d4af37] to-[#aa8c2c] rounded-2xl flex items-center justify-center mb-4 shadow-lg border border-gray-700">
            <i data-lucide="activity" class="text-black w-8 h-8"></i>
        </div>
        <h2 class="text-3xl font-black text-white mb-2 tracking-widest uppercase text-center">Cyber Jump</h2>
        <p class="text-gray-400 mb-8 text-sm text-center">Tap screen or Spacebar to jump over spikes.</p>
        <button onclick="startJumpGame()" class="bg-white text-black px-8 py-4 rounded-xl font-bold tracking-widest active:scale-95 shadow-xl hover:bg-gray-200 transition-colors text-sm uppercase">
            Initialize Run
        </button>
    </div>
</div>

<script>
    const canvasJ = document.getElementById('jump-canvas');
    const ctxJ = canvasJ.getContext('2d');
    
    function resizeJumpCanvas() {
        const parent = canvasJ.parentElement;
        canvasJ.width = parent.clientWidth;
        canvasJ.height = parent.clientWidth * (10/16);
    }
    window.addEventListener('resize', resizeJumpCanvas);
    resizeJumpCanvas();
    
    let framesJ = 0; let scoreJ = 0; let gameActiveJ = false; let animIdJ;

    const player = {
        x: 50, y: 0, width: 24, height: 24, velocityY: 0, gravity: 0.6, jumpPower: -10, grounded: false,
        draw() {
            ctxJ.fillStyle = '#d4af37';
            ctxJ.shadowBlur = 15; ctxJ.shadowColor = '#d4af37';
            ctxJ.fillRect(this.x, this.y, this.width, this.height);
            ctxJ.shadowBlur = 0;
        },
        update() {
            this.velocityY += this.gravity;
            this.y += this.velocityY;
            
            // Floor Collision
            const floorY = canvasJ.height - 40;
            if (this.y + this.height >= floorY) {
                this.y = floorY - this.height;
                this.velocityY = 0;
                this.grounded = true;
            } else {
                this.grounded = false;
            }
        },
        jump() {
            if(this.grounded) { this.velocityY = this.jumpPower; this.grounded = false; }
        }
    };

    const obstacles = {
        items: [], speed: 5,
        draw() {
            ctxJ.fillStyle = '#ef4444';
            for (let i = 0; i < this.items.length; i++) {
                let obs = this.items[i];
                // Draw Spike (Triangle)
                ctxJ.beginPath();
                ctxJ.moveTo(obs.x + (obs.w/2), obs.y);
                ctxJ.lineTo(obs.x + obs.w, obs.y + obs.h);
                ctxJ.lineTo(obs.x, obs.y + obs.h);
                ctxJ.fill();
            }
        },
        update() {
            if (framesJ % 80 === 0) { // Spawn rate
                this.items.push({ x: canvasJ.width, y: canvasJ.height - 40 - 30, w: 20, h: 30, passed: false });
            }
            for (let i = 0; i < this.items.length; i++) {
                let obs = this.items[i];
                obs.x -= this.speed;

                // Collision Detection (AABB simple box for triangle)
                if (player.x < obs.x + obs.w && player.x + player.width > obs.x &&
                    player.y < obs.y + obs.h && player.y + player.height > obs.y) {
                    endJumpGame();
                }

                // Score
                if (obs.x + obs.w < player.x && !obs.passed) {
                    scoreJ++; obs.passed = true;
                    document.getElementById('jump-score').innerText = scoreJ;
                    if(scoreJ % 5 === 0) this.speed += 0.5; // Increases difficulty
                }

                if (obs.x + obs.w < 0) this.items.shift();
            }
        }
    };

    function drawFloor() {
        ctxJ.fillStyle = '#151518';
        ctxJ.fillRect(0, canvasJ.height - 40, canvasJ.width, 40);
        ctxJ.strokeStyle = '#2d2d35'; ctxJ.lineWidth = 2;
        ctxJ.beginPath(); ctxJ.moveTo(0, canvasJ.height - 40); ctxJ.lineTo(canvasJ.width, canvasJ.height - 40); ctxJ.stroke();
    }

    function animateJ() {
        if(!gameActiveJ || !gameLoopActive) return;
        ctxJ.fillStyle = '#0a0a0c';
        ctxJ.fillRect(0, 0, canvasJ.width, canvasJ.height);
        
        drawFloor();
        player.update(); player.draw();
        obstacles.update(); obstacles.draw();
        
        framesJ++;
        animIdJ = requestAnimationFrame(animateJ);
    }

    function startJumpGame() {
        resizeJumpCanvas();
        player.y = canvasJ.height - 40 - player.height; player.velocityY = 0;
        obstacles.items = []; obstacles.speed = 5;
        scoreJ = 0; framesJ = 0;
        document.getElementById('jump-score').innerText = scoreJ;
        document.getElementById('jump-overlay').classList.add('hidden');
        gameActiveJ = true;
        animateJ();
    }

    function endJumpGame() {
        gameActiveJ = false; cancelAnimationFrame(animIdJ);
        document.getElementById('jump-overlay').classList.remove('hidden');
        document.getElementById('jump-overlay').innerHTML = `
            <div class="w-16 h-16 bg-red-900/50 border border-red-500 rounded-2xl flex items-center justify-center mb-4"><i data-lucide="skull" class="text-red-500 w-8 h-8"></i></div>
            <h2 class="text-3xl font-black text-white mb-2 uppercase tracking-widest text-center">System Crash</h2>
            <p class="text-gray-400 mb-8 font-mono bg-black/50 px-4 py-2 rounded-lg border border-gray-800">Spikes Cleared: ${scoreJ}</p>
            <button onclick="startJumpGame()" class="bg-white text-black px-8 py-4 rounded-xl font-bold uppercase tracking-widest text-sm hover:bg-gray-200 active:scale-95">Reboot Sequence</button>
        `;
        lucide.createIcons();
        let reward = scoreJ * 100;
        if(reward > 0) fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/wallet.php', { method: 'POST', body: JSON.stringify({action: 'game_win', amount: reward}) });
    }

    // Input
    window.addEventListener('keydown', (e) => { if((e.code === 'Space' || e.code === 'ArrowUp') && gameActiveJ) { e.preventDefault(); player.jump(); }});
    canvasJ.addEventListener('pointerdown', (e) => { e.preventDefault(); if(gameActiveJ) player.jump(); });
</script>