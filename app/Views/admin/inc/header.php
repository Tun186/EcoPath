<!-- Sticky Top Header -->
<header class="h-20 bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-100 flex items-center justify-between px-6 md:px-8 z-10 sticky top-0 transition-all duration-300">
    <div class="flex items-center space-x-4 min-w-0">
        <!-- Mobile Drawer Toggle Button -->
        <button @click="mobileMenuOpen = true" class="md:hidden text-gray-500 hover:text-gray-800 p-1.5 hover:bg-gray-100 rounded-xl transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
        </button>
        
        <!-- Titles -->
        <div class="min-w-0">
            <h1 class="text-lg md:text-xl font-bold text-gray-800 truncate"><?= htmlspecialchars($headerTitle ?? 'Admin Dashboard') ?></h1>
            <p class="text-xxs md:text-xs text-gray-400 font-medium truncate mt-0.5"><?= htmlspecialchars($headerSubtitle ?? 'Overview & stats') ?></p>
        </div>
    </div>
    
    <!-- Actions / Widgets -->
    <div class="flex items-center space-x-3.5">
        <!-- Live System Status (Desktop only) -->
        <div class="hidden sm:flex items-center space-x-1.5 px-3 py-1.5 bg-gray-50 rounded-xl border border-gray-100 text-xxs font-semibold text-gray-500 relative">
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-ping"></span>
            <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full absolute"></span>
            <span class="pl-1">System Live</span>
        </div>
        
        <!-- Page-specific action slot -->
        <?php if (isset($headerAction)): ?>
            <?= $headerAction ?>
        <?php endif; ?>
    </div>
</header>
