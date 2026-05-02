<?php
// /admin/pages/login.php
// High-security Overseer Login Interface - ENV Driven

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = $_POST['admin_pin'] ?? '';
    
    // Fetch expected PIN from .env
    $secure_env_pin = $_ENV['ADMIN_PIN'] ?? 'NOT_SET'; 
    
    if ($secure_env_pin === 'NOT_SET') {
        $error = "SYSTEM ERROR: ADMIN_PIN not configured in mainframe environment.";
    } elseif ($pin === $secure_env_pin) {
        $_SESSION['user_id'] = 999; 
        $_SESSION['username'] = 'OVERSEER_ACTUAL';
        $_SESSION['role'] = 'admin';
        header("Location: index.php?route=dashboard");
        exit;
    } else {
        $error = "ACCESS DENIED. INVALID CLEARANCE CODE.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- Head block omitted for brevity, identical to previous login.php -->
<body class="flex items-center justify-center h-screen bg-[#050505] font-mono relative">
    <div class="z-10 bg-black/80 border border-red-900 p-8 rounded max-w-md w-full shadow-[0_0_30px_rgba(255,51,51,0.15)] relative">
        <div class="absolute top-0 left-0 w-full h-1 bg-red-600 shadow-[0_0_10px_#ff0000]"></div>
        
        <h1 class="text-2xl font-black text-center mb-2 tracking-widest text-red-500 uppercase">Overseer Access</h1>
        <p class="text-xs text-gray-500 text-center mb-8">> Awaiting level 5 clearance authorization.</p>

        <?php if($error): ?>
            <div class="bg-red-900/30 border border-red-500 text-red-500 p-3 text-xs mb-6 text-center animate-pulse">
                > <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <div>
                <input type="password" name="admin_pin" autocomplete="off" autofocus class="w-full bg-black border border-red-900 rounded p-3 text-red-500 text-center text-2xl tracking-[1em] outline-none focus:border-red-500 transition-colors shadow-[inset_0_0_10px_rgba(255,0,0,0.1)]" placeholder="****">
            </div>
            <button type="submit" class="w-full bg-red-900/50 hover:bg-red-600 text-red-400 hover:text-white border border-red-800 hover:border-red-500 py-3 transition-all duration-300 uppercase text-xs tracking-widest font-bold">
                Initialize Decryption
            </button>
        </form>
    </div>
</body>
</html>