<?php
// /frontend/components/withdraw_modal.php
// Premium Extract Funds UI
?>
<div id="withdraw-modal" class="fixed inset-0 bg-black/90 backdrop-blur-md z-[100] hidden flex-col items-center justify-center pointer-events-auto">
    <div class="glass-panel p-8 rounded-3xl w-full max-w-md relative shadow-2xl border border-gray-700">
        
        <button onclick="closeWithdrawModal()" class="absolute top-6 right-6 text-gray-500 hover:text-white transition-colors bg-black/50 p-2 rounded-full">
            <i data-lucide="x" size="18"></i>
        </button>
        
        <div class="flex flex-col items-center mb-8">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-700 flex items-center justify-center mb-4 shadow-inner">
                <i data-lucide="banknote" class="w-8 h-8 text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-white tracking-wide text-center">Extract Assets</h2>
            <p class="text-center text-xs text-gray-400 font-mono mt-2 bg-black/40 px-3 py-1 rounded-full border border-gray-800">Available: <?= isset($_SESSION['mmk_balance']) ? number_format($_SESSION['mmk_balance']) : '0' ?> Ks</p>
        </div>

        <div class="space-y-5">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Select Network</label>
                <div class="relative">
                    <select id="w-method" class="w-full bg-black/40 border border-gray-700 rounded-xl p-4 text-white outline-none focus:border-premium-silver transition-colors appearance-none cursor-pointer">
                        <option value="KPay">KBZPay (KPay)</option>
                        <option value="WaveMoney">Wave Money</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-4 top-4 text-gray-500 pointer-events-none"></i>
                </div>
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Target Account</label>
                <input type="text" id="w-phone" placeholder="e.g. 09xxxxxxxxx" class="w-full bg-black/40 border border-gray-700 rounded-xl p-4 text-white outline-none focus:border-premium-silver transition-colors font-mono" autocomplete="off">
            </div>
            
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Amount (MMK)</label>
                <input type="number" id="w-amount" placeholder="Min 1,000" min="1000" class="w-full bg-black/40 border border-gray-700 rounded-xl p-4 text-white outline-none focus:border-premium-silver transition-colors font-mono" autocomplete="off">
            </div>

            <div id="w-error" class="hidden text-red-400 text-xs text-center font-bold p-3 bg-red-900/20 border border-red-900/50 rounded-xl"></div>
            
            <button onclick="submitWithdrawal()" id="w-submit-btn" class="w-full bg-white text-black font-bold py-4 rounded-xl mt-4 hover:bg-gray-200 transition-colors flex items-center justify-center gap-2 active:scale-95 text-sm">
                <i data-lucide="arrow-right" size="18"></i> Initiate Transfer
            </button>
        </div>
    </div>
</div>

<script>
    function openWithdrawModal() {
        <?php if(!isset($_SESSION['user_id'])): ?> return; <?php endif; ?>
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

        if(!phone || phone.length < 9) { errorDiv.innerText = "Invalid target account number."; errorDiv.classList.remove('hidden'); return; }
        if(isNaN(amount) || amount < 1000) { errorDiv.innerText = "Minimum transfer amount is 1,000 Ks."; errorDiv.classList.remove('hidden'); return; }

        btn.disabled = true;
        btn.innerHTML = `<i data-lucide="loader" class="animate-spin w-5 h-5"></i>`;
        lucide.createIcons();

        fetch('<?= defined("API_URL") ? API_URL : "/api" ?>/withdraw.php', {
            method: 'POST', headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ method: method, phone_number: phone, amount: amount })
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                alert("Transfer initiated. Awaiting Overseer approval.");
                location.reload(); 
            } else {
                errorDiv.innerText = data.message; errorDiv.classList.remove('hidden');
                btn.disabled = false; btn.innerHTML = `<i data-lucide="arrow-right" size="18"></i> Initiate Transfer`;
                lucide.createIcons();
            }
        }).catch(err => {
            errorDiv.innerText = "Network connection failed."; errorDiv.classList.remove('hidden');
            btn.disabled = false; btn.innerHTML = `<i data-lucide="arrow-right" size="18"></i> Initiate Transfer`;
            lucide.createIcons();
        });
    }
</script>