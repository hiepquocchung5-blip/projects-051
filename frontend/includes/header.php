<?php
// /frontend/includes/header.php
// Contains `<head>`, Three.js setup, and Top Navigation
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
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        neon: { cyan: '#00f0ff', purple: '#b026ff', dark: '#0a0a0f' }
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
    </style>
</head>
<body>

<!-- 3D Background -->
<div id="canvas-container"></div>

<!-- Interactive UI Layer -->
<div id="ui-layer" class="flex flex-col h-full pointer-events-auto">
    
    <!-- Top Nav -->
    <header class="glass-panel w-full p-4 flex justify-between items-center border-b border-neon-cyan/30">
        <a href="<?= BASE_URL ?>" class="flex items-center gap-3 hover:opacity-80 transition">
            <i data-lucide="cpu" class="text-neon-cyan"></i>
            <h1 class="text-2xl font-black tracking-widest neon-text"><?= APP_NAME ?></h1>
        </a>
        
        <div class="flex gap-6 items-center">
            <div class="flex flex-col items-end">
                <span class="text-xs text-gray-400 font-mono">COINS</span>
                <span id="global-coins" class="text-lg font-bold text-neon-purple drop-shadow-[0_0_5px_#b026ff]">
                    <?= isset($_SESSION['user_coins']) ? number_format($_SESSION['user_coins']) : '0' ?>
                </span>
            </div>
            <button onclick="alert('Google Login triggers here')" class="border border-neon-cyan text-neon-cyan px-4 py-2 rounded-md hover:bg-neon-cyan/20 transition font-mono text-sm">
                Login / Auth
            </button>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow p-8">