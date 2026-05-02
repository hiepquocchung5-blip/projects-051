<?php
// /frontend/games/dataworm.php
// Premium Grid-based Snake
?>
<div class="flex flex-col items-center w-full relative h-full">
    <div class="absolute top-4 left-6 z-10 text-white font-bold text-lg drop-shadow-md flex items-center gap-2">
        <i data-lucide="git-commit" size="18" class="text-premium-silver"></i> Length: <span id="worm-score">1</span>
    </div>
    
    <canvas id="worm-canvas" class="bg-[#0a0a0c] rounded-2xl shadow-inner touch-none w-full max-w-[800px] aspect-[16/10] border border-gray-800"></canvas>

    <div id="worm-overlay" class="absolute inset-0 bg-black/80 flex flex-col items-center justify-center rounded-2xl z-20 backdrop-blur-md">
        <div class="w-16 h-16 bg-gradient-to-br from-gray-300 to-gray-500 rounded-2xl flex items-center justify-center mb-4 shadow-lg border border-gray-700">
            <i data-lucide="git-commit" class="text-black w-8 h-8"></i>
        </div>
        <h2 class="text-3xl font-black text-white mb-2 tracking-widest uppercase text-center">Data Worm</h2>
        <p class="text-gray-400 mb-8 text-sm text-center">Swipe to steer. Consume packets.</p>
        <button onclick="startWormGame()" class="bg-white text-black px-8 py-4 rounded-xl font-bold tracking-widest active:scale-95 shadow-xl hover:bg-gray-200 transition-colors text-sm uppercase">
            Initialize Sequence
        </button>
    </div>
</div>

<script>
    const canvasW = document.getElementById('worm-canvas');
    const ctxW = canvasW.getContext('2d');
    const gridSize = 20;
    let tileCountX, tileCountY;
    
    function resizeWormCanvas() {
        const parent = canvasW.parentElement;
        canvasW.width = parent.clientWidth;
        canvasW.height = parent.clientWidth * (10/16);
        // Snap to grid
        canvasW.width = Math.floor(canvasW.width / gridSize) * gridSize;
        canvasW.height = Math.floor(canvasW.height / gridSize) * gridSize;
        tileCountX = canvasW.width / gridSize;
        tileCountY = canvasW.height / gridSize;
    }
    window.addEventListener('resize', resizeWormCanvas);
    resizeWormCanvas();
    
    let wormActive = false; let wormInterval;
    let snake = []; let velocityX = 0; let velocityY = 0;
    let packetX = 10; let packetY = 10;

    function resetWorm() {
        snake = [{x: Math.floor(tileCountX/2), y: Math.floor(tileCountY/2)}];
        velocityX = 1; velocityY = 0;
        placePacket();
    }

    function placePacket() {
        packetX = Math.floor(Math.random() * tileCountX);
        packetY = Math.floor(Math.random() * tileCountY);
        // Prevent placing on snake
        snake.forEach(part => { if(part.x === packetX && part.y === packetY) placePacket(); });
    }

    function gameLoopWorm() {
        if(!wormActive || !gameLoopActive) return;
        
        const head = {x: snake[0].x + velocityX, y: snake[0].y + velocityY};
        
        // Wall Collision
        if (head.x < 0 || head.x >= tileCountX || head.y < 0 || head.y >= tileCountY) { endWormGame(); return; }
        
        // Self Collision
        for(let i=0; i<snake.length; i++) { if(head.x === snake[i].x && head.y === snake[i].y) { endWormGame(); return; } }
        
        snake.unshift(head);
        
        // Consume Packet
        if (head.x === packetX && head.y === packetY) {
            document.getElementById('worm-score').innerText = snake.length;
            placePacket();
        } else {
            snake.pop(); // Remove tail if no packet consumed
        }

        drawWorm();
    }

    function drawWorm() {
        ctxW.fillStyle = '#0a0a0c';
        ctxW.fillRect(0, 0, canvasW.width, canvasW.height);

        // Draw Packet
        ctxW.fillStyle = '#d4af37';
        ctxW.shadowBlur = 10; ctxW.shadowColor = '#d4af37';
        ctxW.fillRect(packetX * gridSize, packetY * gridSize, gridSize - 2, gridSize - 2);
        ctxW.shadowBlur = 0;

        // Draw Snake
        ctxW.fillStyle = '#e2e8f0';
        snake.forEach(part => {
            ctxW.fillRect(part.x * gridSize, part.y * gridSize, gridSize - 2, gridSize - 2);
        });
    }

    function startWormGame() {
        resizeWormCanvas(); resetWorm();
        document.getElementById('worm-score').innerText = 1;
        document.getElementById('worm-overlay').classList.add('hidden');
        wormActive = true;
        clearInterval(wormInterval);
        wormInterval = setInterval(gameLoopWorm, 100); // Speed
    }

    function endWormGame() {
        wormActive = false; clearInterval(wormInterval);
        document.getElementById('worm-overlay').classList.remove('hidden');
        document.getElementById('worm-overlay').innerHTML = `
            <div class="w-16 h-16 bg-red-900/50 border border-red-500 rounded-2xl flex items-center justify-center mb-4"><i data-lucide="x-circle" class="text-red-500 w-8 h-8"></i></div>
            <h2 class="text-3xl font-black text-white mb-2 uppercase tracking-widest text-center">Corruption Detected</h2>
            <p class="text-gray-400 mb-8 font-mono bg-black/50 px-4 py-2 rounded-lg border border-gray-800">Final Length: ${snake.length}</p>
            <button onclick="startWormGame()" class="bg-white text-black px-8 py-4 rounded-xl font-bold uppercase tracking-widest text-sm hover:bg-gray-200 active:scale-95">Reboot Sequence</button>
        `;
        lucide.createIcons();
        let reward = (snake.length - 1) * 200;
        if(reward > 0) fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/wallet.php', { method: 'POST', body: JSON.stringify({action: 'game_win', amount: reward}) });
    }

    // Controls
    window.addEventListener('keydown', (e) => {
        if(!wormActive) return;
        if(e.code === 'ArrowUp' && velocityY === 0) { velocityX = 0; velocityY = -1; e.preventDefault(); }
        if(e.code === 'ArrowDown' && velocityY === 0) { velocityX = 0; velocityY = 1; e.preventDefault(); }
        if(e.code === 'ArrowLeft' && velocityX === 0) { velocityX = -1; velocityY = 0; e.preventDefault(); }
        if(e.code === 'ArrowRight' && velocityX === 0) { velocityX = 1; velocityY = 0; e.preventDefault(); }
    });

    // Touch Swipe Logic for Mobile
    let touchStartX = 0; let touchStartY = 0;
    canvasW.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
        touchStartY = e.changedTouches[0].screenY;
    }, {passive: false});

    canvasW.addEventListener('touchend', (e) => {
        if(!wormActive) return;
        let touchEndX = e.changedTouches[0].screenX;
        let touchEndY = e.changedTouches[0].screenY;
        handleSwipe(touchStartX, touchStartY, touchEndX, touchEndY);
    }, {passive: false});

    function handleSwipe(startX, startY, endX, endY) {
        let dx = endX - startX; let dy = endY - startY;
        if (Math.abs(dx) > Math.abs(dy)) {
            // Horizontal
            if (dx > 0 && velocityX === 0) { velocityX = 1; velocityY = 0; }
            else if (dx < 0 && velocityX === 0) { velocityX = -1; velocityY = 0; }
        } else {
            // Vertical
            if (dy > 0 && velocityY === 0) { velocityX = 0; velocityY = 1; }
            else if (dy < 0 && velocityY === 0) { velocityX = 0; velocityY = -1; }
        }
    }
</script>