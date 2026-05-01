<?php
// /frontend/components/game_card.php
// Upgraded UI Component for rendering premium game cards

function renderGameCard($id, $title, $description, $icon, $colorClass, $route) {
    $title = htmlspecialchars($title);
    $desc = htmlspecialchars($description);
    
    // Map tailwind colors to hex for dynamic shadows
    $shadowColor = 'rgba(255,255,255,0.1)';
    if(strpos($colorClass, 'cyan') !== false) $shadowColor = 'rgba(0, 240, 255, 0.4)';
    if(strpos($colorClass, 'purple') !== false) $shadowColor = 'rgba(176, 38, 255, 0.4)';
    if(strpos($colorClass, 'green') !== false) $shadowColor = 'rgba(74, 222, 128, 0.4)';

    echo "
    <a href='" . (defined('BASE_URL') ? BASE_URL : '') . "?route=play&game=" . $id . "' 
       class='relative group block w-full rounded-2xl overflow-hidden bg-gray-900/40 border border-gray-800 backdrop-blur-md transition-all duration-300 hover:scale-[1.02] active:scale-95'
       style='box-shadow: 0 4px 20px rgba(0,0,0,0.5);'>
        
        <!-- Hover Glow Effect -->
        <div class='absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none' 
             style='box-shadow: inset 0 0 30px {$shadowColor};'></div>
             
        <div class='p-5 flex flex-col h-full'>
            <div class='h-36 bg-black/60 rounded-xl mb-4 flex items-center justify-center border border-gray-800 group-hover:border-{$colorClass} transition-colors duration-300 relative overflow-hidden'>
                <!-- Tech grid background -->
                <div class='absolute inset-0 opacity-20' style='background-image: radial-gradient({$shadowColor} 1px, transparent 1px); background-size: 10px 10px;'></div>
                
                <i data-lucide='{$icon}' class='w-14 h-14 text-gray-500 group-hover:text-{$colorClass} transition-colors duration-300 relative z-10' style='filter: drop-shadow(0 0 10px {$shadowColor});'></i>
            </div>
            
            <h2 class='text-lg font-black mb-1 text-white group-hover:text-{$colorClass} transition-colors duration-300 uppercase tracking-wide'>{$title}</h2>
            <p class='text-xs text-gray-400 font-mono leading-relaxed line-clamp-2'>{$desc}</p>
            
            <div class='mt-4 flex items-center gap-2 text-[10px] font-mono text-gray-500 font-bold uppercase'>
                <span class='w-2 h-2 rounded-full bg-green-500 animate-pulse'></span> System Online
            </div>
        </div>
    </a>
    ";
}
?>