<?php
// /frontend/components/telegram_modal.php
// Include at bottom of pages to allow linking
?>
<!-- Telegram Webhook Link Modal -->
<div id="telegram-modal" class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 hidden flex-col items-center justify-center pointer-events-auto">
    <div class="glass-panel p-8 rounded-xl w-full max-w-md border border-blue-500 relative">
        <button onclick="document.getElementById('telegram-modal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-white">
            <i data-lucide="x"></i>
        </button>
        
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center text-blue-400 border border-blue-500">
                <i data-lucide="send" size="32"></i>
            </div>
        </div>

        <h2 class="text-2xl font-bold mb-2 text-center text-blue-400">Sync with Telegram</h2>
        <p class="text-center text-sm text-gray-400 mb-6">Link the Urbanix bot to receive withdrawal notifications and daily task claims directly to your phone.</p>

        <div class="bg-gray-900 p-4 rounded text-center border border-gray-700 font-mono mb-6">
            <p class="text-xs text-gray-500 mb-1">Your Unique Sync Code:</p>
            <!-- In reality, generate this securely per user session -->
            <p class="text-xl font-bold text-white tracking-widest">
                URBX-<?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] . rand(100,999) : 'GUEST' ?>
            </p>
        </div>

        <button onclick="window.open('https://t.me/YourUrbanixBot', '_blank')" class="w-full bg-blue-500 text-white font-bold py-3 rounded hover:bg-blue-400 transition flex items-center justify-center gap-2">
            Open Telegram <i data-lucide="external-link" size="16"></i>
        </button>
    </div>
</div>