<?php
// /frontend/components/game_card.php
// Premium Metallic Game Card Component

function renderGameCard($id, $title, $description, $icon, $colorClass, $route) {
    $title = htmlspecialchars($title);
    $desc = htmlspecialchars($description);
    
    // Map tailwind classes to specific premium hex colors for shadows
    $shadowColor = 'rgba(255,255,255,0.1)';
    if(strpos($colorClass, 'gold') !== false) $shadowColor = 'rgba(212, 175, 55, 0.4)';
    if(strpos($colorClass, 'silver') !== false) $shadowColor = 'rgba(226, 232, 240, 0.4)';
    if(strpos($colorClass, 'red') !== false) $shadowColor = 'rgba(239, 68, 68, 0.4)';

    echo "
    <a href='?route=play&game={$id}' class='group relative block w-full rounded-3xl overflow-hidden bg-premium-panel/60 border border-gray-700/50 backdrop-blur-xl transition-all duration-300 hover:scale-[1.02] active:scale-95 shadow-xl hover:shadow-2xl'>
        
        <!-- Subtle Glow Mesh -->
        <div class='absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none' style='box-shadow: inset 0 0 40px {$shadowColor};'></div>
             
        <div class='p-6 flex flex-col h-full relative z-10'>
            <div class='h-40 bg-black/50 rounded-2xl mb-5 flex items-center justify-center border border-gray-800 group-hover:border-{$colorClass} transition-colors duration-300 relative overflow-hidden shadow-inner'>
                
                <!-- Metallic Grid Background -->
                <div class='absolute inset-0 opacity-20' style='background-image: linear-gradient({$shadowColor} 1px, transparent 1px), linear-gradient(90deg, {$shadowColor} 1px, transparent 1px); background-size: 20px 20px;'></div>
                
                <div class='bg-gray-900 p-4 rounded-xl shadow-lg border border-gray-800 z-10 group-hover:scale-110 transition-transform duration-300'>
                    <i data-lucide='{$icon}' class='w-10 h-10 text-gray-500 group-hover:text-{$colorClass} transition-colors duration-300' style='filter: drop-shadow(0 0 8px {$shadowColor});'></i>
                </div>
            </div>
            
            <h2 class='text-lg font-bold mb-2 text-white group-hover:text-{$colorClass} transition-colors duration-300 tracking-wide'>{$title}</h2>
            <p class='text-xs text-gray-400 font-sans leading-relaxed line-clamp-2'>{$desc}</p>
            
            <div class='mt-5 flex items-center gap-2 text-[10px] font-bold text-gray-500 uppercase tracking-widest border-t border-gray-800 pt-4'>
                <span class='w-1.5 h-1.5 rounded-full bg-premium-gold animate-pulse'></span> Link Active
            </div>
        </div>
    </a>
    ";
}
?>