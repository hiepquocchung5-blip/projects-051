<?php
// /frontend/games/cybermole.php
// Timed reflex game
?>
<div class="flex flex-col items-center w-full">
    <div class="flex gap-8 mb-6 font-mono text-neon-purple">
        <div class="text-xl">Score: <span id="mole-score" class="font-bold text-white">0</span></div>
        <div class="text-xl">Time: <span id="mole-time" class="font-bold text-white">30</span>s</div>
    </div>
    
    <div class="grid grid-cols-4 gap-4 bg-gray-900 p-6 rounded-xl border border-neon-purple/50" id="mole-board">
        <!-- JS Generated -->
    </div>
    
    <button id="mole-start-btn" onclick="startMoleGame()" class="mt-8 bg-neon-purple text-white px-8 py-3 rounded-full hover:bg-white hover:text-black transition font-bold tracking-widest">
        INITIALIZE BREACH
    </button>
</div>

<style>
    .mole-hole { transition: all 0.1s; }
    .mole-active { background-color: #b026ff; box-shadow: 0 0 15px #b026ff; border-color: white; transform: scale(1.1); cursor: pointer; }
</style>

<script>
    let moleScore = 0;
    let moleTime = 30;
    let moleInterval;
    let countdownInterval;
    let moleActive = false;

    function initMoleBoard() {
        const board = document.getElementById('mole-board');
        board.innerHTML = '';
        for(let i=0; i<16; i++) {
            const hole = document.createElement('div');
            hole.id = `hole-${i}`;
            hole.className = 'mole-hole w-16 h-16 bg-black border border-gray-700 rounded-full flex items-center justify-center';
            hole.onclick = () => hitMole(i);
            board.appendChild(hole);
        }
    }

    function startMoleGame() {
        if(!gameLoopActive) return; // Prevent start if ad is showing
        moleScore = 0;
        moleTime = 30;
        moleActive = true;
        document.getElementById('mole-score').innerText = '0';
        document.getElementById('mole-start-btn').classList.add('hidden');
        
        countdownInterval = setInterval(() => {
            if(!gameLoopActive) return; // Pause timer during ads
            moleTime--;
            document.getElementById('mole-time').innerText = moleTime;
            if(moleTime <= 0) endMoleGame();
        }, 1000);

        moleInterval = setInterval(popMole, 800);
    }

    function popMole() {
        if(!moleActive || !gameLoopActive) return;
        
        // Reset all
        for(let i=0; i<16; i++) {
            document.getElementById(`hole-${i}`).classList.remove('mole-active');
        }
        
        // Pop 1-3 new ones
        let pops = Math.floor(Math.random() * 3) + 1;
        for(let j=0; j<pops; j++) {
            let rId = Math.floor(Math.random() * 16);
            document.getElementById(`hole-${rId}`).classList.add('mole-active');
        }
    }

    function hitMole(id) {
        if(!moleActive || !gameLoopActive) return;
        const hole = document.getElementById(`hole-${id}`);
        if(hole.classList.contains('mole-active')) {
            moleScore += 100;
            document.getElementById('mole-score').innerText = moleScore;
            hole.classList.remove('mole-active');
            
            // Visual click effect
            hole.style.backgroundColor = '#00f0ff';
            setTimeout(() => hole.style.backgroundColor = '', 100);
        }
    }

    function endMoleGame() {
        moleActive = false;
        clearInterval(moleInterval);
        clearInterval(countdownInterval);
        for(let i=0; i<16; i++) document.getElementById(`hole-${i}`).classList.remove('mole-active');
        
        document.getElementById('mole-start-btn').classList.remove('hidden');
        document.getElementById('mole-start-btn').innerText = "RESTART BREACH";
        
        if(moleScore > 0) {
            fetch('<?= API_URL ?>/wallet.php', {
                method: 'POST', body: JSON.stringify({action: 'game_win', amount: moleScore})
            }).then(() => alert(`Hack Complete. Earned ${moleScore} Coins.`));
        }
    }

    initMoleBoard();
</script>