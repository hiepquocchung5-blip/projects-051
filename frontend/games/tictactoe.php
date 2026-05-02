<?php
// /frontend/games/tictactoe.php
// Premium Metallic Edition
?>
<div class="flex flex-col items-center w-full max-w-sm mx-auto relative z-10 h-full justify-center">
    <div id="ttt-status" class="mb-6 text-sm font-bold text-gray-400 tracking-widest uppercase h-6">Awaiting input...</div>
    
    <div class="grid grid-cols-3 gap-2 sm:gap-3 bg-premium-panel/80 p-3 sm:p-4 rounded-3xl border border-gray-700/50 shadow-2xl w-full aspect-square" id="ttt-board">
        <!-- JS Cells -->
    </div>
    
    <button onclick="resetTTT()" class="mt-8 bg-black/50 border border-gray-700 text-gray-300 px-8 py-4 rounded-xl hover:bg-white hover:text-black transition-all font-bold uppercase text-xs tracking-widest active:scale-95 shadow-lg flex items-center gap-2">
        <i data-lucide="rotate-ccw" size="16"></i> Reboot Grid
    </button>
</div>

<style>
    .ttt-cell { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); touch-action: manipulation; }
    .ttt-cell:active { transform: scale(0.95); }
</style>

<script>
    let tttBoard = Array(9).fill(null);
    let tttActive = true;
    const baseReward = <?= isset($gameInfo['base_reward']) ? $gameInfo['base_reward'] : 5000 ?>;

    function initTTT() {
        const boardDiv = document.getElementById('ttt-board');
        boardDiv.innerHTML = '';
        for(let i=0; i<9; i++) {
            const cell = document.createElement('div');
            cell.className = 'ttt-cell bg-black border border-gray-800/80 rounded-2xl flex items-center justify-center text-5xl sm:text-6xl cursor-pointer hover:border-premium-gold/50 shadow-inner';
            cell.addEventListener('pointerdown', (e) => { e.preventDefault(); makeTTTMove(i); });
            boardDiv.appendChild(cell);
        }
        lucide.createIcons();
    }

    function makeTTTMove(index) {
        if(tttBoard[index] || !tttActive || !gameLoopActive) return;
        
        tttBoard[index] = 'X'; updateTTTUI();
        if(checkTTTWin('X')) { handleTTTEnd('VICTORY', baseReward); return; }
        if(tttBoard.every(c => c)) { handleTTTEnd('DRAW', baseReward / 2); return; }

        tttActive = false;
        const status = document.getElementById('ttt-status');
        status.innerText = "System AI Processing...";
        status.classList.add('text-premium-gold'); status.classList.remove('text-gray-400');
        
        setTimeout(() => {
            if(!gameLoopActive) return;
            
            let empty = tttBoard.map((v, i) => v === null ? i : null).filter(v => v !== null);
            let aiMove = getBestMove() ?? empty[Math.floor(Math.random() * empty.length)];
            
            tttBoard[aiMove] = 'O'; updateTTTUI();
            
            if(checkTTTWin('O')) { handleTTTEnd('SYSTEM FAILURE', 0); }
            else if(tttBoard.every(c => c)) { handleTTTEnd('DRAW', baseReward / 2); }
            else {
                tttActive = true;
                status.innerText = "Awaiting input...";
                status.classList.remove('text-premium-gold'); status.classList.add('text-gray-400');
            }
        }, 800);
    }

    function getBestMove() {
        const wins = [[0,1,2], [3,4,5], [6,7,8], [0,3,6], [1,4,7], [2,5,8], [0,4,8], [2,4,6]];
        for(let comb of wins) {
            let xCount = 0; let emptyIdx = null;
            for(let i of comb) {
                if(tttBoard[i] === 'X') xCount++;
                else if(tttBoard[i] === null) emptyIdx = i;
            }
            if(xCount === 2 && emptyIdx !== null) return emptyIdx; 
        }
        return null;
    }

    function updateTTTUI() {
        const cells = document.getElementById('ttt-board').children;
        tttBoard.forEach((val, i) => {
            if(val === 'X' && cells[i].innerHTML === '') {
                cells[i].innerHTML = `<span class="text-premium-silver drop-shadow-md font-sans font-black">X</span>`;
                cells[i].classList.add('shadow-[inset_0_0_20px_rgba(255,255,255,0.1)]', 'border-gray-500/50');
            }
            if(val === 'O' && cells[i].innerHTML === '') {
                cells[i].innerHTML = `<span class="text-premium-gold drop-shadow-[0_0_10px_rgba(212,175,55,0.5)] font-sans font-black">O</span>`;
                cells[i].classList.add('shadow-[inset_0_0_20px_rgba(212,175,55,0.15)]', 'border-premium-gold/50');
            }
        });
    }

    function checkTTTWin(player) {
        const wins = [[0,1,2], [3,4,5], [6,7,8], [0,3,6], [1,4,7], [2,5,8], [0,4,8], [2,4,6]];
        return wins.some(comb => comb.every(i => tttBoard[i] === player));
    }

    function handleTTTEnd(msg, reward) {
        tttActive = false;
        document.getElementById('ttt-status').innerText = msg;
        if(reward > 0) {
            fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/wallet.php', { method: 'POST', credentials: 'include', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({action: 'game_win', amount: reward}) })
            .then(() => { 
                document.getElementById('ttt-status').innerText += ` [+${reward}]`; 
                if(window.showToast) window.showToast(`Yield Transferred: +${reward}`, 'success');
            });
        }
    }

    function resetTTT() {
        tttBoard = Array(9).fill(null); tttActive = true;
        const status = document.getElementById('ttt-status');
        status.innerText = "Awaiting input...";
        status.classList.remove('text-premium-gold', 'text-red-500'); status.classList.add('text-gray-400');
        initTTT();
    }

    initTTT();
</script>