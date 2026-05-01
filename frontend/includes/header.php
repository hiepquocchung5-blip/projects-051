<?php
// /frontend/includes/header.php
// Upgraded with Mobile Bottom Navigation and Native App Feel
$currentRoute = isset($_GET['route']) ? $_GET['route'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= defined('APP_NAME') ? APP_NAME : 'URBANIX' ?> | Gaming Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
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
        body { margin: 0; overflow: hidden; background-color: #0a0a0f; color: white; touch-action: manipulation; }
        #canvas-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; opacity: 0.8; }
        #ui-layer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; overflow-y: auto; overflow-x: hidden; scroll-behavior: smooth; }
        
        /* Glassmorphism Utilities */
        .glass-panel { background: rgba(10, 10, 15, 0.75); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border: 1px solid rgba(0, 240, 255, 0.15); }
        .glass-nav-mobile { background: rgba(15, 15, 20, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border-top: 1px solid rgba(0, 240, 255, 0.2); }
        
        .neon-text { color: #00f0ff; text-shadow: 0 0 8px rgba(0, 240, 255, 0.6); }
        
        /* Mobile Scrollbar hiding */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0, 240, 255, 0.5); border-radius: 4px; }
        
        /* Active Nav Indicator */
        .nav-active { color: #00f0ff; text-shadow: 0 0 10px #00f0ff; }
        .nav-active i { filter: drop-shadow(0 0 8px #00f0ff); }
    </style>
</head>
<body>

<!-- 3D Background -->
<div id="canvas-container"></div>

<!-- Interactive UI Layer -->
<div id="ui-layer" class="flex flex-col h-full pointer-events-auto pb-24 md:pb-0">
    
    <!-- Top Header (Desktop & Mobile Minimal) -->
    <header class="glass-panel w-full p-4 flex justify-between items-center sticky top-0 z-50 shadow-[0_4px_30px_rgba(0,0,0,0.5)]">
        <div class="flex items-center gap-8">
            <a href="<?= BASE_URL ?? '/' ?>" class="flex items-center gap-3 hover:scale-105 transition transform active:scale-95">
                <div class="bg-neon-cyan/10 p-2 rounded-lg border border-neon-cyan/30">
                    <i data-lucide="cpu" class="text-neon-cyan"></i>
                </div>
                <h1 class="text-2xl font-black tracking-widest neon-text hidden sm:block"><?= defined('APP_NAME') ? APP_NAME : 'URBANIX' ?></h1>
            </a>
            
            <!-- Desktop Navigation -->
            <nav class="hidden md:flex gap-8 font-mono text-sm ml-4">
                <a href="<?= BASE_URL ?? '/' ?>" class="<?= $currentRoute === 'home' ? 'nav-active' : 'text-gray-400 hover:text-neon-cyan' ?> transition flex items-center gap-2">
                    <i data-lucide="layout-dashboard" size="18"></i> Hub
                </a>
                <a href="?route=leaderboard" class="<?= $currentRoute === 'leaderboard' ? 'nav-active' : 'text-gray-400 hover:text-neon-purple' ?> transition flex items-center gap-2">
                    <i data-lucide="trophy" size="18"></i> Rankings
                </a>
            </nav>
        </div>
        
        <!-- Wallet & Auth Status -->
        <div class="flex gap-4 items-center">
            <?php if(isset($_SESSION['user_id'])): ?>
                <!-- ... existing logged-in profile logic ... -->
            <?php else: ?>
                <!-- CLEAN UPDATED UNAUTHENTICATED STATE -->
                <a href="?route=auth" class="bg-neon-cyan/10 border border-neon-cyan text-neon-cyan hover:bg-neon-cyan hover:text-black transition-colors px-5 py-2 rounded-lg font-mono text-xs font-bold uppercase tracking-widest flex items-center gap-2 shadow-[0_0_10px_rgba(0,240,255,0.2)]">
                    <i data-lucide="power" size="14"></i> Connect
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="flex-grow p-4 md:p-8 w-full max-w-7xl mx-auto">