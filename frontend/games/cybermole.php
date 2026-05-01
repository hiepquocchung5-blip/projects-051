<?php
// /frontend/games/cybermole.php
// Version 2: Combo Multipliers & Floating Text
?>
<div class="flex flex-col items-center w-full relative">
    <div class="flex justify-between w-full max-w-sm mb-6 font-mono text-neon-purple px-4">
        <div class="text-xl">SCORE: <span id="mole-score" class="font-black text-white drop-shadow-[0_0_5px_#fff]">0</span></div>
        <div class="text-xl">TIME: <span id="mole-time" class="font-black text-white">30</span>s</div>
    </div>
    
    <!-- Combo Meter -->
    <div class="h-2 w-full max-w-sm bg-gray-900 rounded-full mb-6 overflow-hidden border border-gray-800">
        <div id="combo-bar" class="h-full bg-neon-purple w-0 transition-all duration-200 shadow-[0_0_10px_#b026ff]"></div>
    </div>
    
    <div class="grid grid-cols-4 gap-3 sm:gap-4 bg-gray-900/50 p-4 sm:p-6 rounded-2xl border border-neon-purple/30 shadow-[0_0_30px_rgba(176,38,255,0.1)] relative" id="mole-board">
        <!-- JS Generated -->
    </div>
    
    <button id="mole-start-btn" onclick="startMoleGame()" class="mt-8 bg-neon-purple/20 border border-neon-purple text-neon-purple px-8 py-3 rounded-xl hover:bg-neon-purple hover:text-white transition-all font-bold tracking-widest active:scale-95 shadow-[0_0_15px_rgba(176,38,255,0.3)]">
        INITIALIZE BREACH
    </button>
</div>

<style>
    .mole-hole { transition: all 0.15s cubic-bezier(0.4, 0, 0.2, 1); touch-action: manipulation; }
    .mole-active { 
        background-color: #b026ff; box-shadow: inset 0 0 10px #fff, 0 0 20px #b026ff; 
        border-color: #fff; transform: scale(1.05); cursor: pointer; 
    }
    .float-text {
        position: absolute; color: #00f0ff; font-family: monospace; font-weight: bold; font-size: 14px;
        pointer-events: none; animation: floatUp 0.6s ease-out forwards; z-index: 50;
    }
    @keyframes floatUp { 0% { opacity: 1; transform: translateY(0) scale(1); } 100% { opacity: 0; transform: translateY(-30px) scale(1.2); } }
</style>

<script>
    let moleScore = 0; let moleTime = 30;
    let moleInterval; let countdownInterval;
    let moleActive = false;
    
    let combo = 0; let comboTimer = null;

    function initMoleBoard() {
        const board = document.getElementById('mole-board');
        board.innerHTML = '';
        for(let i=0; i<16; i++) {
            const hole = document.createElement('div');
            hole.id = `hole-${i}`;
            hole.className = 'mole-hole w-14 h-14 sm:w-16 sm:h-16 bg-black border border-gray-800 rounded-2xl flex items-center justify-center relative';
            
            // Prevent 300ms delay on mobile
            hole.addEventListener('pointerdown', (e) => { e.preventDefault(); hitMole(i, e); });
            board.appendChild(hole);
        }
    }

    function startMoleGame() {
        if(!gameLoopActive) return;
        moleScore = 0; moleTime = 30; combo = 0; moleActive = true;
        document.getElementById('mole-score').innerText = '0';
        updateComboBar();
        document.getElementById('mole-start-btn').classList.add('hidden');
        
        countdownInterval = setInterval(() => {
            if(!gameLoopActive) return; 
            moleTime--; document.getElementById('mole-time').innerText = moleTime;
            if(moleTime <= 0) endMoleGame();
        }, 1000);

        moleInterval = setInterval(popMole, 750);
    }

    function popMole() {
        if(!moleActive || !gameLoopActive) return;
        
        // Decay combo if missed a beat
        if(combo > 0 && Math.random() > 0.5) { combo--; updateComboBar(); }

        for(let i=0; i<16; i++) document.getElementById(`hole-${i}`).classList.remove('mole-active');
        
        let pops = Math.floor(Math.random() * 3) + 1; // 1 to 3 targets
        for(let j=0; j<pops; j++) {
            document.getElementById(`hole-${Math.floor(Math.random() * 16)}`).classList.add('mole-active');
        }
    }

    function hitMole(id, e) {
        if(!moleActive || !gameLoopActive) return;
        const hole = document.getElementById(`hole-${id}`);
        
        if(hole.classList.contains('mole-active')) {
            hole.classList.remove('mole-active');
            
            // Combo Logic
            combo = Math.min(10, combo + 1);
            updateComboBar();
            
            let points = 100 * (1 + (combo * 0.2)); // up to 3x multiplier
            moleScore += Math.floor(points);
            document.getElementById('mole-score').innerText = moleScore;
            
            // Visuals
            hole.style.backgroundColor = '#00f0ff';
            hole.style.boxShadow = '0 0 20px #00f0ff';
            setTimeout(() => { hole.style.backgroundColor = ''; hole.style.boxShadow = ''; }, 150);
            
            // Floating Text
            createFloatingText(`+${Math.floor(points)}`, hole);
        } else {
            // Penalty miss
            combo = 0; updateComboBar();
        }
    }
    
    function updateComboBar() {
        document.getElementById('combo-bar').style.width = `${combo * 10}%`;
        if(combo === 10) document.getElementById('combo-bar').style.backgroundColor = '#00f0ff';
        else document.getElementById('combo-bar').style.backgroundColor = '#b026ff';
    }

    function createFloatingText(text, parent) {
        const span = document.createElement('span');
        span.className = 'float-text';
        span.innerText = text;
        parent.appendChild(span);
        setTimeout(() => span.remove(), 600);
    }

    function endMoleGame() {
        moleActive = false; clearInterval(moleInterval); clearInterval(countdownInterval);
        for(let i=0; i<16; i++) document.getElementById(`hole-${i}`).classList.remove('mole-active');
        
        const btn = document.getElementById('mole-start-btn');
        btn.classList.remove('hidden'); btn.innerText = "RESTART BREACH";
        
        if(moleScore > 0) {
            fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/wallet.php', { method: 'POST', body: JSON.stringify({action: 'game_win', amount: moleScore}) })
            .then(() => alert(`Hack Complete. Earned ${moleScore} Coins.`));
        }
    }

    initMoleBoard();
</script>