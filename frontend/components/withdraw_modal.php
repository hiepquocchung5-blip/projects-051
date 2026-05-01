<?php
// /frontend/components/withdraw_modal.php
// Modal for initiating MMK withdrawals to local payment methods
// Included globally via index.php
?>
<div id="withdraw-modal" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-[100] hidden flex-col items-center justify-center pointer-events-auto">
    <div class="glass-panel p-8 rounded-xl w-full max-w-md border border-green-500 relative shadow-[0_0_40px_rgba(74,222,128,0.15)]">
        
        <!-- Close Button -->
        <button onclick="closeWithdrawModal()" class="absolute top-4 right-4 text-gray-400 hover:text-white transition">
            <i data-lucide="x"></i>
        </button>
        
        <div class="flex flex-col items-center mb-6">
            <div class="w-16 h-16 rounded-full bg-green-500/20 border border-green-500 flex items-center justify-center mb-4">
                <i data-lucide="banknote" class="w-8 h-8 text-green-400 drop-shadow-[0_0_8px_#4ade80]"></i>
            </div>
            <h2 class="text-2xl font-black text-green-400 tracking-widest uppercase text-center drop-shadow-[0_0_5px_#4ade80]">Extract Funds</h2>
            <p class="text-center text-xs text-gray-400 font-mono mt-1">Available: <?= isset($_SESSION['mmk_balance']) ? number_format($_SESSION['mmk_balance']) : '0' ?> Ks</p>
        </div>

        <div class="space-y-4 font-mono">
            <div>
                <label class="block text-xs text-green-400 mb-1">> SELECT NETWORK</label>
                <select id="w-method" class="w-full bg-gray-900 border border-gray-700 rounded p-3 text-white outline-none focus:border-green-400 transition cursor-pointer appearance-none">
                    <option value="KPay">KBZPay (KPay)</option>
                    <option value="WaveMoney">Wave Money</option>
                </select>
            </div>
            
            <div>
                <label class="block text-xs text-green-400 mb-1">> TARGET ACCOUNT NUMBER</label>
                <input type="text" id="w-phone" placeholder="e.g. 09xxxxxxxxx" class="w-full bg-gray-900 border border-gray-700 rounded p-3 text-white outline-none focus:border-green-400 transition" autocomplete="off">
            </div>
            
            <div>
                <label class="block text-xs text-green-400 mb-1">> AMOUNT (MMK)</label>
                <input type="number" id="w-amount" placeholder="Min 1000 Ks" min="1000" class="w-full bg-gray-900 border border-gray-700 rounded p-3 text-white outline-none focus:border-green-400 transition" autocomplete="off">
            </div>

            <div id="w-error" class="hidden text-red-500 text-xs text-center font-mono py-2 bg-red-900/20 border border-red-900 rounded"></div>
            
            <button onclick="submitWithdrawal()" id="w-submit-btn" class="w-full bg-green-500 text-black font-bold py-3 rounded mt-4 hover:bg-white transition flex items-center justify-center gap-2">
                <i data-lucide="arrow-right-to-line" size="18"></i> INITIATE TRANSFER
            </button>
        </div>
    </div>
</div>

<script>
    function openWithdrawModal() {
        <?php if(!isset($_SESSION['user_id'])): ?>
            alert("You must authenticate via Google to access the withdrawal framework.");
            return;
        <?php endif; ?>
        document.getElementById('withdraw-modal').classList.remove('hidden');
        document.getElementById('withdraw-modal').style.display = 'flex';
    }

    function closeWithdrawModal() {
        document.getElementById('withdraw-modal').classList.add('hidden');
        document.getElementById('withdraw-modal').style.display = 'none';
        document.getElementById('w-error').classList.add('hidden');
    }

    function submitWithdrawal() {
        const method = document.getElementById('w-method').value;
        const phone = document.getElementById('w-phone').value;
        const amount = parseFloat(document.getElementById('w-amount').value);
        const errorDiv = document.getElementById('w-error');
        const btn = document.getElementById('w-submit-btn');

        errorDiv.classList.add('hidden');

        if(!phone || phone.length < 9) {
            errorDiv.innerText = "Invalid target account number.";
            errorDiv.classList.remove('hidden');
            return;
        }

        if(isNaN(amount) || amount < 1000) {
            errorDiv.innerText = "Minimum transfer amount is 1,000 Ks.";
            errorDiv.classList.remove('hidden');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = `<i data-lucide="loader" class="animate-spin"></i> PROCESSING...`;
        lucide.createIcons();

        fetch('<?= API_URL ?>/withdraw.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ method: method, phone_number: phone, amount: amount })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert("Transfer initiated. Awaiting CMS approval.");
                location.reload(); // Reload to update balance UI
            } else {
                errorDiv.innerText = data.message;
                errorDiv.classList.remove('hidden');
                btn.disabled = false;
                btn.innerHTML = `<i data-lucide="arrow-right-to-line" size="18"></i> INITIATE TRANSFER`;
                lucide.createIcons();
            }
        })
        .catch(err => {
            errorDiv.innerText = "Network connection failed.";
            errorDiv.classList.remove('hidden');
            btn.disabled = false;
        });
    }

    // Bind this to the UI button created in previous step
    // In frontend/includes/header.php, update the profile icon onclick:
    // onclick="openWithdrawModal()"
</script>