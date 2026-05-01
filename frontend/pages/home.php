<?php
// /frontend/pages/home.php
// Dashboard displaying available games - App Style

require_once 'components/game_card.php';
?>

<div class="w-full">
    <!-- Welcome Header -->
    <div class="mb-8 p-6 rounded-2xl bg-gradient-to-r from-neon-cyan/10 to-transparent border border-neon-cyan/20 backdrop-blur-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-neon-cyan/5 rounded-full blur-3xl -mr-20 -mt-20"></div>
        <h1 class="text-3xl md:text-4xl font-black text-white uppercase tracking-wider">
            Select <span class="neon-text">Simulation</span>
        </h1>
        <p class="text-gray-400 font-mono mt-2 text-sm max-w-lg">
            Initialize a module to generate Urban Coins. Systems are optimized for high-yield returns.
        </p>
    </div>

    <!-- Game Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-6">
        <?php
            // Mock DB Pull (Replace with actual query in production)
            $games = [
                ['id' => 'tictactoe', 'title' => 'Quantum Tic-Tac', 'desc' => 'Logic combat against rogue AI entities.', 'icon' => 'grid-3x3', 'color' => 'neon-cyan'],
                ['id' => 'cybermole', 'title' => 'Cyber-Mole', 'desc' => 'High-speed target neutralization.', 'icon' => 'target', 'color' => 'neon-purple'],
                ['id' => 'urbanbird', 'title' => 'Urban Flight', 'desc' => 'Navigate the firewall. Infinite evasion.', 'icon' => 'plane-takeoff', 'color' => 'green-400'],
                ['id' => 'neonguess', 'title' => 'Encryption Breach', 'desc' => 'Decrypt the node. Less attempts = more coins.', 'icon' => 'terminal', 'color' => 'orange-400'],
                ['id' => 'gridwars', 'title' => 'Grid Wars Lite', 'desc' => 'Arena survival. Eliminate drone swarms.', 'icon' => 'crosshair', 'color' => 'red-500']
            ];

            foreach ($games as $game) {
                renderGameCard($game['id'], $game['title'], $game['desc'], $game['icon'], $game['color'], 'play');
            }
        ?>
    </div>
</div>