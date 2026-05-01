<?php
// /frontend/games/neonguess.php
// Number Guessing Game (Logic + UI)
?>
<div class="flex flex-col items-center w-full max-w-md mx-auto">
    <div class="glass-panel p-8 rounded-xl w-full text-center border-orange-500 relative">
        <i data-lucide="terminal" class="absolute top-4 right-4 text-orange-500/50 w-8 h-8"></i>
        <h2 class="text-2xl font-black text-orange-500 mb-2">Decrypt the Node</h2>
        <p class="text-sm text-gray-400 font-mono mb-6">Find the correct encryption key (1-100). Fewer attempts = Higher reward.</p>
        
        <div id="guess-log" class="h-32 overflow-y-auto bg-black border border-gray-800 rounded p-2 mb-4 font-mono text-xs text-left text-green-400 space-y-1">
            <div>> System initialized. Awaiting input...</div>
        </div>

        <div class="flex gap-2">
            <input type="number" id="guess-input" min="1" max="100" class="w-full bg-gray-900 border border-gray-700 rounded px-4 py-2 text-white font-mono outline-none focus:border-orange-500" placeholder="Enter key...">
            <button onclick="makeGuess()" class="bg-orange-500 text-black font-bold px-6 py-2 rounded hover:bg-white transition">SUBMIT</button>
        </div>

        <div class="mt-4 flex justify-between font-mono text-xs text-gray-500">
            <span>Attempts: <span id="guess-attempts" class="text-white">0</span></span>
            <span>Est. Reward: <span id="guess-reward" class="text-orange-400">5000</span></span>
        </div>
    </div>
</div>

<script>
    let targetKey = Math.floor(Math.random() * 100) + 1;
    let attempts = 0;
    let baseReward = 5000;
    let gameEnded = false;

    function logTerminal(msg, isError = false) {
        const log = document.getElementById('guess-log');
        const entry = document.createElement('div');
        entry.className = isError ? 'text-red-500' : 'text-green-400';
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
            logTerminal("ERR: Invalid key range.", true);
            return;
        }

        attempts++;
        document.getElementById('guess-attempts').innerText = attempts;
        
        let currentReward = Math.max(100, baseReward - ((attempts - 1) * 500));
        document.getElementById('guess-reward').innerText = currentReward;

        if(guess === targetKey) {
            logTerminal(`ACCESS GRANTED. Key [${guess}] accepted.`);
            endGuessGame(currentReward);
        } else if (guess < targetKey) {
            logTerminal(`Key [${guess}] rejected. Target value is HIGHER.`, true);
        } else {
            logTerminal(`Key [${guess}] rejected. Target value is LOWER.`, true);
        }
    }

    function endGuessGame(reward) {
        gameEnded = true;
        document.getElementById('guess-input').disabled = true;
        
        fetch('<?= API_URL ?>/wallet.php', {
            method: 'POST', body: JSON.stringify({action: 'game_win', amount: reward})
        }).then(() => {
            logTerminal(`Reward dispatched: +${reward} Coins.`);
            setTimeout(() => {
                // Reset
                targetKey = Math.floor(Math.random() * 100) + 1;
                attempts = 0; gameEnded = false;
                document.getElementById('guess-attempts').innerText = 0;
                document.getElementById('guess-reward').innerText = baseReward;
                document.getElementById('guess-input').disabled = false;
                document.getElementById('guess-log').innerHTML = '<div>> System rebooted. Awaiting input...</div>';
            }, 3000);
        });
    }

    // Allow Enter key
    document.getElementById('guess-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') makeGuess();
    });
</script>