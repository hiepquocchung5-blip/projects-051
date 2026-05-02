<?php
// /frontend/includes/header.php
// Premium Boot Sequence & Global Cookie Consent
$currentRoute = isset($_GET['route']) ? $_GET['route'] : 'home';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= defined('APP_NAME') ? APP_NAME : 'URBANIX' ?> | Premium Gaming</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { premium: { dark: '#0a0a0c', panel: '#151518', metal: '#2d2d35', silver: '#e2e8f0', gold: '#d4af37', goldDark: '#aa8c2c' } },
                    fontFamily: { sans: ['Inter', '-apple-system', 'sans-serif'], mono: ['SF Mono', 'ui-monospace', 'monospace'] }
                }
            }
        }
    </script>
    <style>
        body { margin: 0; overflow: hidden; background-color: #0a0a0c; color: #e2e8f0; touch-action: manipulation; }
        #canvas-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; opacity: 0.6; }
        
        /* App UI Layer */
        #ui-layer { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; overflow-y: auto; overflow-x: hidden; scroll-behavior: smooth; opacity: 0; transition: opacity 0.8s ease-in-out; }
        
        /* Premium Frosted Glass */
        .glass-panel { background: rgba(21, 21, 24, 0.6); backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px); border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4); }
        
        /* Advanced Bootloader CSS */
        #bootloader { position: fixed; inset: 0; z-index: 99999; background: #0a0a0c; display: flex; flex-direction: column; align-items: center; justify-content: center; transition: opacity 0.6s cubic-bezier(0.4, 0, 0.2, 1), visibility 0.6s; }
        .loader-ring { position: absolute; inset: 0; border-radius: 50%; border: 2px solid transparent; }
        .ring-outer { border-top-color: #d4af37; border-bottom-color: #d4af37; animation: spin-slow 2s linear infinite; }
        .ring-inner { inset: 8px; border-left-color: #e2e8f0; border-right-color: #e2e8f0; animation: spin-fast 1.5s linear infinite reverse; }
        .ring-core { inset: 16px; border-top-color: rgba(212, 175, 55, 0.3); border-bottom-color: rgba(212, 175, 55, 0.3); animation: spin-pulse 3s ease-in-out infinite; }
        
        @keyframes spin-slow { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes spin-fast { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        @keyframes spin-pulse { 0% { transform: rotate(0deg) scale(1); opacity: 0.5; } 50% { transform: rotate(180deg) scale(1.1); opacity: 1; } 100% { transform: rotate(360deg) scale(1); opacity: 0.5; } }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 4px; } ::-webkit-scrollbar-track { background: transparent; } ::-webkit-scrollbar-thumb { background: rgba(212, 175, 55, 0.3); border-radius: 4px; }
    </style>
</head>
<body>

<!-- PREMIUM ADVANCED BOOTLOADER -->
<div id="bootloader">
    <div class="relative w-32 h-32 flex items-center justify-center mb-8">
        <div class="loader-ring ring-outer drop-shadow-[0_0_8px_#d4af37]"></div>
        <div class="loader-ring ring-inner"></div>
        <div class="loader-ring ring-core"></div>
        <div class="bg-gradient-to-br from-premium-gold to-premium-goldDark p-3 rounded-xl shadow-[0_0_20px_rgba(212,175,55,0.4)] z-10 animate-pulse">
            <i data-lucide="cpu" class="text-premium-dark w-6 h-6"></i>
        </div>
    </div>
    <h1 class="text-2xl font-black tracking-[0.3em] text-white uppercase mb-3 shadow-premium-gold/50"><?= defined('APP_NAME') ? APP_NAME : 'URBANIX' ?></h1>
    <div class="flex items-center gap-2">
        <span class="w-1.5 h-1.5 rounded-full bg-premium-gold animate-ping"></span>
        <p id="boot-text" class="text-xs text-gray-500 font-mono uppercase tracking-[0.4em]">Decrypting Network...</p>
    </div>
</div>

<!-- COOKIE CONSENT BANNER -->
<div id="cookie-banner" class="fixed bottom-20 md:bottom-6 left-1/2 transform -translate-x-1/2 w-[95%] max-w-2xl z-[9999] glass-panel p-5 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-6 translate-y-[150%] opacity-0 transition-all duration-700 ease-out hidden shadow-[0_20px_50px_rgba(0,0,0,0.8)]">
    <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-black/50 border border-gray-700 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="cookie" class="text-premium-gold w-6 h-6"></i>
        </div>
        <div>
            <h3 class="text-white font-bold text-sm tracking-wide">Data Telemetry Protocols</h3>
            <p class="text-xs text-gray-400 mt-1 font-sans leading-relaxed">We utilize secure cookies to maintain session integrity, personalize your interface, and analyze network traffic. By proceeding, you accept our data extraction protocols.</p>
        </div>
    </div>
    <div class="flex items-center gap-3 shrink-0 w-full sm:w-auto">
        <button onclick="acceptCookies()" class="w-full sm:w-auto bg-gradient-to-r from-premium-gold to-premium-goldDark text-premium-dark px-6 py-3 rounded-xl font-bold uppercase tracking-widest text-[10px] hover:shadow-[0_0_15px_rgba(212,175,55,0.4)] transition-all active:scale-95">
            Accept All
        </button>
    </div>
</div>

<div id="canvas-container"></div>

<div id="ui-layer" class="flex flex-col h-full pointer-events-auto pb-24 md:pb-0">
    <!-- Top Navigation Header -->
    <header class="glass-panel w-full p-4 flex justify-between items-center sticky top-0 z-50">
        <div class="flex items-center gap-8">
            <a href="<?= BASE_URL ?? '/' ?>" class="flex items-center gap-3 transition transform active:scale-95 group">
                <div class="bg-gradient-to-br from-premium-gold to-premium-goldDark p-2 rounded-lg shadow-lg group-hover:shadow-[0_0_15px_rgba(212,175,55,0.4)] transition-shadow">
                    <i data-lucide="cpu" class="text-premium-dark"></i>
                </div>
                <h1 class="text-xl font-bold tracking-widest text-white hidden sm:block"><?= defined('APP_NAME') ? APP_NAME : 'URBANIX' ?></h1>
            </a>
            
            <nav class="hidden md:flex gap-8 font-sans text-sm ml-4 font-medium">
                <a href="?route=home" class="<?= $currentRoute === 'home' ? 'text-premium-gold' : 'text-gray-400 hover:text-white' ?> transition flex items-center gap-2">
                    <i data-lucide="layout-grid" size="16"></i> Dashboard
                </a>
                <a href="?route=leaderboard" class="<?= $currentRoute === 'leaderboard' ? 'text-premium-gold' : 'text-gray-400 hover:text-white' ?> transition flex items-center gap-2">
                    <i data-lucide="award" size="16"></i> Rankings
                </a>
            </nav>
        </div>
        
        <div class="flex gap-4 items-center">
            <?php if(isset($_SESSION['user_id'])): ?>
                <div class="flex flex-col items-end mr-2">
                    <span class="text-[9px] text-gray-500 font-bold uppercase tracking-widest leading-tight">Assets</span>
                    <span class="text-sm md:text-base font-bold text-premium-gold leading-tight"><?= number_format($_SESSION['user_coins'] ?? 0) ?></span>
                </div>
            <?php endif; ?>
            <a href="?route=profile" class="w-10 h-10 rounded-full border border-gray-700 flex items-center justify-center bg-premium-panel overflow-hidden hover:border-premium-gold transition active:scale-90 shadow-inner group">
                <i data-lucide="<?= isset($_SESSION['user_id']) ? 'user' : 'lock' ?>" class="<?= isset($_SESSION['user_id']) ? 'text-white' : 'text-premium-gold' ?> w-5 h-5 group-hover:scale-110 transition-transform"></i>
            </a>
        </div>
    </header>
    <main class="flex-grow p-4 md:p-8 w-full max-w-7xl mx-auto">