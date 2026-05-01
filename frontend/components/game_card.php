<?php
// /frontend/components/game_card.php
// Reusable UI Component for rendering games in the grid

function renderGameCard($id, $title, $description, $icon, $colorClass, $route) {
    // Escaping output for security
    $title = htmlspecialchars($title);
    $desc = htmlspecialchars($description);
    
    echo "
    <a href='" . BASE_URL . "?route=play&game=" . $id . "' class='glass-panel p-6 rounded-xl hover:scale-105 transition group block'>
        <div class='h-40 bg-black rounded-lg mb-4 flex items-center justify-center border border-gray-800 group-hover:border-{$colorClass} transition'>
            <i data-lucide='{$icon}' class='w-16 h-16 text-gray-600 group-hover:text-{$colorClass} transition'></i>
        </div>
        <h2 class='text-xl font-bold mb-2 text-{$colorClass} drop-shadow-[0_0_5px_currentColor]'>{$title}</h2>
        <p class='text-sm text-gray-400'>{$desc}</p>
    </a>
    ";
}
?>