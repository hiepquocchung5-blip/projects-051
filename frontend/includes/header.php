<?php
// /frontend/includes/header.php
// Updated to include Navigation and Google Auth Scripts
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> | Gaming Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Google Identity Services -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        neon: { cyan: '#00f0ff', purple: '#b026ff', dark: '#0a0a0f', red: '#ff3333' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['Fira Code', 'monospace'] }
                }
            }
        }
    </script>
    <style>
        body { margin: 0; overflow: hidden; background-color: #0a0a0f; color: white; }
        #canvas-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; }
        #ui-layer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; overflow-y: auto;}
        .glass-panel { background: rgba(19, 19, 31, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(0, 240, 255, 0.2); }
        .neon-text { color: #00f0ff; text-shadow: 0 0 5px #00f0ff; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0f; }
        ::-webkit-scrollbar-thumb { background: #00f0ff; border-radius: 3px; }
    </style>
</head>
<body>

<!-- 3D Background -->
<div id="canvas-container"></div>

<!-- Interactive UI Layer -->
<div id="ui-layer" class="flex flex-col h-full pointer-events-auto">
    
    <!-- Top Nav -->
    <header class="glass-panel w-full p-4 flex justify-between items-center border-b border-neon-cyan/30 sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <a href="<?= BASE_URL ?>" class="flex items-center gap-3 hover:opacity-80 transition">
                <i data-lucide="cpu" class="text-neon-cyan"></i>
                <h1 class="text-2xl font-black tracking-widest neon-text"><?= APP_NAME ?></h1>
            </a>
            
            <!-- Navigation Links -->
            <nav class="hidden md:flex gap-6 font-mono text-sm">
                <a href="<?= BASE_URL ?>" class="text-gray-400 hover:text-neon-cyan transition flex items-center gap-2">
                    <i data-lucide="layout-dashboard" size="16"></i> Hub
                </a>
                <a href="<?= BASE_URL ?>?route=leaderboard" class="text-gray-400 hover:text-neon-purple transition flex items-center gap-2">
                    <i data-lucide="trophy" size="16"></i> Rankings
                </a>
            </nav>
        </div>
        
        <div class="flex gap-6 items-center">
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- Logged In State -->
                <div class="flex flex-col items-end hidden sm:flex">
                    <span class="text-xs text-gray-400 font-mono">BALANCE</span>
                    <span class="text-lg font-bold text-green-400 drop-shadow-[0_0_5px_#4ade80]">
                        <?= number_format($_SESSION['mmk_balance']) ?> Ks
                    </span>
                </div>
                <div class="flex flex-col items-end">
                    <span class="text-xs text-gray-400 font-mono">COINS</span>
                    <span id="global-coins" class="text-lg font-bold text-neon-purple drop-shadow-[0_0_5px_#b026ff]">
                        <?= number_format($_SESSION['user_coins']) ?>
                    </span>
                </div>
                <!-- Dropdown / Profile (Simplified) -->
                <div class="w-10 h-10 rounded-full border-2 border-neon-cyan flex items-center justify-center bg-gray-900 overflow-hidden cursor-pointer" onclick="alert('Open Profile/Withdraw Modal')">
                    <i data-lucide="user" class="text-neon-cyan"></i>
                </div>
            <?php else: ?>
                <!-- Logged Out State - Google Auth Button -->
                <div id="g_id_onload"
                     data-client_id="YOUR_GOOGLE_CLIENT_ID_HERE.apps.googleusercontent.com"
                     data-callback="handleCredentialResponse"
                     data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="rectangular"
                     data-theme="filled_black"
                     data-text="signin_with"
                     data-size="large"
                     data-logo_alignment="left">
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow p-8">