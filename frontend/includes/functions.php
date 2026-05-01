<?php
// /frontend/includes/functions.php
// Reusable UI components - Premium Metallic Edit

/**
 * Smart Authentication Enforcer
 * Redirects unauthenticated users to the auth portal and remembers where they tried to go.
 */
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        $targetRoute = isset($_GET['route']) ? $_GET['route'] : 'home';
        header("Location: ?route=auth&redirect=" . urlencode($targetRoute));
        exit;
    }
}

function renderSystemError($title, $message, $errorCode = "404") {
    echo "
    <div class='flex flex-col items-center justify-center h-full w-full p-6'>
        <div class='glass-panel border-gray-700 p-8 rounded-3xl max-w-md w-full text-center relative overflow-hidden shadow-2xl'>
            
            <div class='w-16 h-16 bg-gray-800 rounded-2xl mx-auto flex items-center justify-center mb-6 shadow-inner border border-gray-700'>
                <i data-lucide='alert-circle' class='w-8 h-8 text-gray-400'></i>
            </div>
            
            <h2 class='text-xl font-bold text-white tracking-wide mb-2'>
                {$title}
            </h2> 
            
            <p class='text-sm text-gray-400 mb-6 leading-relaxed'>
                {$message}
            </p>
            
            <div class='bg-black/40 border border-gray-800 p-4 rounded-xl text-xs text-left mb-8 text-gray-500 font-mono'>
                Code: {$errorCode}
            </div>
            
            <a href='" . (defined('BASE_URL') ? BASE_URL : '') . "' class='inline-flex items-center justify-center gap-2 w-full bg-white text-black font-bold px-6 py-3 rounded-xl hover:bg-gray-200 transition-colors active:scale-95'>
                Return to Dashboard
            </a>
        </div>
    </div>
    ";
}
?>