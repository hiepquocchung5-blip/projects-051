<?php
// /frontend/games/neonguess.php
// Premium Metallic Edition - Decryption Interface
?>
<div class="flex flex-col items-center w-full max-w-md mx-auto relative z-10 h-full justify-center min-h-[500px]">
    <div class="bg-premium-panel/90 p-8 rounded-3xl w-full text-center border border-gray-700/80 shadow-2xl backdrop-blur-xl relative overflow-hidden">
        
        <!-- Premium Accents -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-premium-gold/5 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none"></div>

        <div class="w-16 h-16 bg-gradient-to-br from-gray-800 to-gray-900 rounded-2xl mx-auto flex items-center justify-center mb-6 shadow-inner border border-gray-700">
            <i data-lucide="terminal" class="w-8 h-8 text-premium-gold"></i>
        </div>
        
        <h2 class="text-2xl font-black text-white mb-2 tracking-widest uppercase drop-shadow-md">Decryption Protocol</h2>
        <p class="text-xs text-gray-400 mb-8 font-sans leading-relaxed px-4">Determine the secure numeric key (1-100). Execution efficiency determines asset yield.</p>
        
        <!-- Terminal Log -->
        <div id="guess-log" class="h-32 overflow-y-auto bg-[#050507] border border-gray-800 rounded-2xl p-4 mb-6 font-mono text-xs text-left text-gray-400 space-y-2 shadow-inner custom-scrollbar">
            <div class="text-premium-silver">> Link established. Awaiting input...</div>
        </div>

        <div class="flex gap-3 relative group">
            <input type="number" id="guess-input" min="1" max="100" class="w-full bg-black/50 border border-gray-700 rounded-2xl px-6 py-4 text-white font-mono outline-none focus:border-premium-gold focus:ring-1 focus:ring-premium-gold/50 transition-all text-xl text-center shadow-inner" placeholder="00">
            <button onclick="makeGuess()" class="bg-gradient-to-br from-premium-gold to-premium-goldDark text-premium-dark font-bold px-6 py-4 rounded-2xl hover:shadow-[0_0_15px_rgba(212,175,55,0.4)] transition-all active:scale-95 shadow-lg flex items-center justify-center">
                <i data-lucide="arrow-right" size="24"></i>
            </button>
        </div>

        <div class="mt-6 flex justify-between items-center border-t border-gray-800/80 pt-6">
            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Attempts: <span id="guess-attempts" class="text-white text-sm bg-gray-900 px-2 py-1 rounded ml-1">0</span></span>
            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest flex items-center gap-1">Yield: <span id="guess-reward" class="text-premium-gold text-sm bg-premium-gold/10 px-2 py-1 rounded ml-1 border border-premium-gold/30">5000</span></span>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 4px; }
</style>

<script>
    let targetKey = Math.floor(Math.random() * 100) + 1;
    let attempts = 0; let baseReward = 5000; let gameEnded = false;

    function logTerminal(msg, type = 'info') {
        const log = document.getElementById('guess-log');
        const entry = document.createElement('div');
        if(type === 'error') entry.className = 'text-red-400 drop-shadow-[0_0_2px_#ef4444]';
        else if(type === 'success') entry.className = 'text-premium-gold font-bold drop-shadow-[0_0_5px_rgba(212,175,55,0.6)]';
        else entry.className = 'text-premium-silver';
        entry.innerText = `> ${msg}`;
        log.appendChild(entry);
        log.scrollTop = log.scrollHeight;
    }

    function makeGuess() {
        if(gameEnded || !gameLoopActive) return;
        
        const input = document.getElementById('guess-input');
        const guess = parseInt(input.value);
        input.value = '';

        if(isNaN(guess) || guess < 1 || guess > 100) { 
            logTerminal("ERR: Invalid syntax. Range is 1-100.", 'error'); 
            if(window.showToast) window.showToast("Invalid Input", "error");
            return; 
        }

        attempts++;
        document.getElementById('guess-attempts').innerText = attempts;
        
        let currentReward = Math.max(100, baseReward - ((attempts - 1) * 500));
        document.getElementById('guess-reward').innerText = currentReward;

        if(guess === targetKey) {
            logTerminal(`ACCESS GRANTED. Key [${guess}] verified.`, 'success');
            if(window.showToast) window.showToast("Decryption Successful", "success");
            endGuessGame(currentReward);
        } else if (guess < targetKey) {
            logTerminal(`[${guess}] REJECTED. Target is HIGHER.`, 'error');
        } else {
            logTerminal(`[${guess}] REJECTED. Target is LOWER.`, 'error');
        }
    }

    function endGuessGame(reward) {
        gameEnded = true;
        document.getElementById('guess-input').disabled = true;
        
        fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/wallet.php', { method: 'POST', credentials: 'include', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({action: 'game_win', amount: reward}) })
        .then(() => {
            logTerminal(`Asset transfer complete: +${reward}.`, 'success');
            setTimeout(() => {
                targetKey = Math.floor(Math.random() * 100) + 1;
                attempts = 0; gameEnded = false;
                document.getElementById('guess-attempts').innerText = 0;
                document.getElementById('guess-reward').innerText = baseReward;
                document.getElementById('guess-input').disabled = false;
                document.getElementById('guess-log').innerHTML = '<div class="text-premium-silver">> System rebooted. Awaiting input...</div>';
            }, 3000);
        });
    }

    document.getElementById('guess-input').addEventListener('keypress', function (e) { if (e.key === 'Enter') { e.preventDefault(); makeGuess(); } });
</script>