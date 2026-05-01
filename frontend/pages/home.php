<?php
// /frontend/pages/home.php
// Dashboard displaying available games

require_once 'components/game_card.php';
?>

<div class="max-w-6xl mx-auto">
    <div class="mb-8 border-l-4 border-neon-cyan pl-4">
        <h1 class="text-3xl font-black neon-text uppercase">Available Subroutines</h1>
        <p class="text-gray-400 font-mono mt-2">Select a simulation to begin earning Urban Coins.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
            // In a real app, this array comes from the MySQL Database
            $games = [
                ['id' => 'tictactoe', 'title' => 'Quantum Tic-Tac-Toe', 'desc' => 'Play against rogue AI.', 'icon' => 'grid-3x3', 'color' => 'neon-cyan'],
                ['id' => 'cybermole', 'title' => 'Cyber-Mole', 'desc' => 'Timed clicking simulation.', 'icon' => 'target', 'color' => 'neon-purple'],
                ['id' => 'urbanbird', 'title' => 'Urbanix Bird', 'desc' => 'Dodge firewall pillars.', 'icon' => 'bird', 'color' => 'green-400']
            ];

            foreach ($games as $game) {
                renderGameCard($game['id'], $game['title'], $game['desc'], $game['icon'], $game['color'], 'play');
            }
        ?>
    </div>
</div>