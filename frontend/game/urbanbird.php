<?php
// /frontend/games/urbanbird.php
// Infinite runner - Flappy Bird clone with Neon Circuit aesthetics
?>
<div class="flex flex-col items-center w-full relative">
    <div class="absolute top-4 left-4 z-10 text-white font-mono text-xl drop-shadow-md">SCORE: <span id="bird-score" class="text-green-400">0</span></div>
    
    <!-- Canvas for rendering the game -->
    <canvas id="bird-canvas" width="800" height="500" class="bg-gray-900 border-2 border-green-400 rounded-lg shadow-[0_0_20px_rgba(74,222,128,0.3)] cursor-pointer"></canvas>

    <div id="bird-overlay" class="absolute inset-0 bg-black/80 flex flex-col items-center justify-center rounded-lg z-20">
        <h2 class="text-4xl font-black text-green-400 mb-4 tracking-widest uppercase">Urbanix Bird</h2>
        <p class="text-gray-400 mb-8 font-mono">Click or press SPACE to jump.</p>
        <button onclick="startBirdGame()" class="bg-green-500 text-black px-8 py-3 rounded hover:bg-white transition font-bold tracking-widest">
            INITIALIZE FLIGHT
        </button>
    </div>
</div>

<script>
    const canvas = document.getElementById('bird-canvas');
    const ctx = canvas.getContext('2d');
    
    let frames = 0;
    let score = 0;
    let gameActive = false;
    let animationId;

    const bird = {
        x: 150, y: 150, radius: 12, velocity: 0, gravity: 0.25, jump: -5.5,
        draw() {
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = '#4ade80';
            ctx.fill();
            ctx.shadowBlur = 15;
            ctx.shadowColor = '#4ade80';
            ctx.closePath();
            ctx.shadowBlur = 0; // reset
        },
        update() {
            this.velocity += this.gravity;
            this.y += this.velocity;
            // Floor collision
            if (this.y + this.radius >= canvas.height) { this.y = canvas.height - this.radius; gameOver(); }
            // Ceiling collision
            if (this.y - this.radius <= 0) { this.y = this.radius; this.velocity = 0; }
        },
        flap() { this.velocity = this.jump; }
    };

    const pipes = {
        items: [], dx: 3, gap: 140,
        draw() {
            for (let i = 0; i < this.items.length; i++) {
                let p = this.items[i];
                ctx.fillStyle = 'rgba(74, 222, 128, 0.2)';
                ctx.strokeStyle = '#4ade80';
                ctx.lineWidth = 2;
                
                // Top Pipe
                ctx.fillRect(p.x, 0, p.w, p.top);
                ctx.strokeRect(p.x, 0, p.w, p.top);
                // Bottom Pipe
                ctx.fillRect(p.x, canvas.height - p.bottom, p.w, p.bottom);
                ctx.strokeRect(p.x, canvas.height - p.bottom, p.w, p.bottom);
            }
        },
        update() {
            if (frames % 120 === 0) {
                let topH = Math.random() * (canvas.height - this.gap - 100) + 50;
                let bottomH = canvas.height - this.gap - topH;
                this.items.push({ x: canvas.width, w: 60, top: topH, bottom: bottomH, passed: false });
            }
            for (let i = 0; i < this.items.length; i++) {
                let p = this.items[i];
                p.x -= this.dx;

                // Collision Detection
                if (bird.x + bird.radius > p.x && bird.x - bird.radius < p.x + p.w &&
                   (bird.y - bird.radius < p.top || bird.y + bird.radius > canvas.height - p.bottom)) {
                    gameOver();
                }

                // Score Update
                if (p.x + p.w < bird.x && !p.passed) {
                    score++;
                    p.passed = true;
                    document.getElementById('bird-score').innerText = score;
                }

                // Remove off-screen pipes
                if (p.x + p.w < 0) this.items.shift();
            }
        }
    };

    function draw() {
        ctx.fillStyle = '#0a0a0f';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        bird.draw();
        pipes.draw();
    }

    function update() {
        bird.update();
        pipes.update();
    }

    function loop() {
        if(!gameActive || !gameLoopActive) return; // gameLoopActive is from play.php (Ad blocker)
        update();
        draw();
        frames++;
        animationId = requestAnimationFrame(loop);
    }

    function startBirdGame() {
        bird.y = 150; bird.velocity = 0;
        pipes.items = [];
        score = 0; frames = 0;
        document.getElementById('bird-score').innerText = score;
        document.getElementById('bird-overlay').classList.add('hidden');
        gameActive = true;
        loop();
    }

    function gameOver() {
        gameActive = false;
        cancelAnimationFrame(animationId);
        document.getElementById('bird-overlay').classList.remove('hidden');
        document.getElementById('bird-overlay').innerHTML = `
            <h2 class="text-4xl font-black text-red-500 mb-2 uppercase">System Crashed</h2>
            <p class="text-white mb-6 font-mono">Data Nodes Passed: ${score}</p>
            <button onclick="startBirdGame()" class="bg-green-500 text-black px-6 py-2 rounded font-bold hover:bg-white">Reboot Flight</button>
        `;
        
        let reward = score * 50; // 50 coins per pipe
        if(reward > 0) {
            fetch('<?= API_URL ?>/wallet.php', {
                method: 'POST', body: JSON.stringify({action: 'game_win', amount: reward})
            });
        }
    }

    // Controls
    window.addEventListener('keydown', (e) => { if(e.code === 'Space' && gameActive) bird.flap(); });
    canvas.addEventListener('mousedown', () => { if(gameActive) bird.flap(); });
</script>