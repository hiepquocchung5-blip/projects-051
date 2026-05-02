<?php
// /admin/includes/header.php
// Executive Control Center Aesthetic
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Control | Urbanix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { background-color: #050507; color: #e2e8f0; }
        .admin-panel { background: #0f0f13; border: 1px solid #1f1f2e; }
        .text-exec-gold { color: #d4af37; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #050507; }
        ::-webkit-scrollbar-thumb { background: #2d2d35; border-radius: 3px; }
    </style>
</head>
<body class="flex h-screen overflow-hidden font-sans">

    <!-- Sidebar -->
    <aside class="w-64 admin-panel h-full flex flex-col border-r border-gray-800 shadow-2xl z-20 relative">
        <div class="p-6 border-b border-gray-800 flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#d4af37] to-[#aa8c2c] flex items-center justify-center shadow-lg">
                <i data-lucide="shield" class="text-black w-5 h-5"></i>
            </div>
            <h1 class="text-lg font-bold text-white tracking-widest uppercase">Overseer</h1>
        </div>
        
       <nav class="flex-grow p-4 space-y-1 text-sm font-medium">
            <a href="?route=dashboard" class="flex items-center gap-3 p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800/30 transition-colors">
                <i data-lucide="layout-grid" size="18"></i> Dashboard
            </a>
            <a href="?route=games" class="flex items-center gap-3 p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800/30 transition-colors">
                <i data-lucide="gamepad-2" size="18"></i> Modules
            </a>
            <a href="?route=events" class="flex items-center gap-3 p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800/30 transition-colors">
                <i data-lucide="zap" size="18"></i> Global Events
            </a>
            <a href="?route=operatives" class="flex items-center gap-3 p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800/30 transition-colors">
                <i data-lucide="users" size="18"></i> Operatives
            </a>
            <a href="?route=ledger" class="flex items-center gap-3 p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800/30 transition-colors">
                <i data-lucide="file-spreadsheet" size="18"></i> Ledger
            </a>
            <a href="?route=system" class="flex items-center gap-3 p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800/30 transition-colors">
                <i data-lucide="settings-2" size="18"></i> System Config
            </a>
        </nav>
        
        <div class="p-4 border-t border-gray-800">
            <a href="<?= defined('BASE_URL') ? BASE_URL : '/' ?>" class="flex items-center justify-center gap-2 text-gray-500 hover:text-white bg-black/50 p-3 rounded-lg text-xs font-bold uppercase tracking-widest transition-colors border border-gray-800">
                <i data-lucide="log-out" size="14"></i> Exit to Portal
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-grow flex flex-col h-full overflow-y-auto relative bg-[#0a0a0c]">
        
        <header class="h-16 border-b border-gray-800 flex items-center justify-between px-8 z-10 bg-[#0f0f13]/80 backdrop-blur sticky top-0">
            <h2 class="text-white font-bold tracking-wide">Executive Overview</h2>
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-2 text-xs font-bold text-gray-500 bg-black/40 px-3 py-1.5 rounded-full border border-gray-800">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> SYSTEM NOMINAL
                </span>
            </div>
        </header>

        <main class="p-8 z-10 flex-grow max-w-7xl mx-auto w-full">