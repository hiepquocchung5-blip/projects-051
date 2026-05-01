<?php
// /frontend/pages/landing.php
// Premium Executive Landing Page - Public Showcase
?>
<div class="flex flex-col items-center justify-center min-h-[80vh] w-full text-center px-4 relative z-10">
    
    <div class="max-w-4xl">
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-premium-gold/10 border border-premium-gold/20 text-premium-gold text-[10px] font-bold uppercase tracking-[0.2em] mb-8 animate-fade-in">
            <span class="w-2 h-2 rounded-full bg-premium-gold animate-pulse"></span> Network Status: Active
        </div>
        
        <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter mb-6 leading-tight">
            The Next Generation of <br>
            <span class="text-gold-gradient">Digital Asset Generation.</span>
        </h1>
        
        <p class="text-gray-400 text-lg md:text-xl font-medium max-w-2xl mx-auto mb-10 leading-relaxed">
            Enter the Urbanix ecosystem. Engage in high-fidelity simulations, accrue network coins, and convert digital effort into real-world liquid assets.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="?route=auth" class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-white text-premium-dark font-black text-sm uppercase tracking-widest hover:bg-premium-gold transition-all duration-300 shadow-xl active:scale-95">
                Initialize Connection
            </a>
            <a href="?route=leaderboard" class="w-full sm:w-auto px-10 py-4 rounded-2xl bg-transparent border border-gray-700 text-white font-bold text-sm uppercase tracking-widest hover:bg-white/5 transition-all active:scale-95">
                View Rankings
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-24 w-full max-w-6xl">
        <div class="glass-panel p-8 rounded-3xl border-gray-800/50 text-left group">
            <i data-lucide="zap" class="text-premium-gold mb-4 group-hover:scale-110 transition-transform"></i>
            <h3 class="text-white font-bold mb-2">Simulate</h3>
            <p class="text-gray-500 text-sm leading-relaxed">Proprietary logic-based games designed for optimal engagement and reward yields.</p>
        </div>
        <div class="glass-panel p-8 rounded-3xl border-gray-800/50 text-left group">
            <i data-lucide="refresh-cw" class="text-premium-gold mb-4 group-hover:rotate-45 transition-transform"></i>
            <h3 class="text-white font-bold mb-2">Convert</h3>
            <p class="text-gray-500 text-sm leading-relaxed">Automated 5-hour conversion protocols turning your coins into Myanmar Kyats.</p>
        </div>
        <div class="glass-panel p-8 rounded-3xl border-gray-800/50 text-left group">
            <i data-lucide="shield-check" class="text-premium-gold mb-4 group-hover:scale-110 transition-transform"></i>
            <h3 class="text-white font-bold mb-2">Extract</h3>
            <p class="text-gray-500 text-sm leading-relaxed">Secure, verified withdrawal gateway supporting local payment providers.</p>
        </div>
    </div>
</div>