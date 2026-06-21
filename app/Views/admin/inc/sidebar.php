<?php
$activePage = $activePage ?? '';
?>
<!-- Desktop Sidebar -->
<aside class="w-64 bg-white shadow-xl border-r border-gray-100 flex-shrink-0 relative hidden md:block z-20 transition-all duration-300">
    <div class="h-full flex flex-col justify-between">
        <div>
            <!-- Logo Section -->
            <div class="h-20 flex items-center px-8 border-b border-gray-100">
                <a href="<?= URLROOT ?>" class="text-eco-primary hover:text-eco-dark font-bold text-2xl tracking-tight transition flex items-center">
                    <span class="mr-2">🌿</span> EcoPath
                </a>
            </div>
            
            <!-- Navigation -->
            <div class="p-5">
                <p class="text-[10px] uppercase tracking-widest text-gray-400 font-bold mb-4 px-3">Administration</p>
                <nav class="space-y-1">
                    <!-- Dashboard Link -->
                    <a href="<?= URLROOT ?>/admin" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-300 group <?= $activePage === 'dashboard' ? 'text-eco-primary bg-emerald-50/60 border-r-4 border-eco-primary' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>

                    <!-- Users Link -->
                    <a href="<?= URLROOT ?>/admin/users" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-300 group <?= $activePage === 'users' ? 'text-eco-primary bg-emerald-50/60 border-r-4 border-eco-primary' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Users
                    </a>

                    <!-- Packages Link -->
                    <a href="<?= URLROOT ?>/admin/packages" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-300 group <?= $activePage === 'packages' ? 'text-eco-primary bg-emerald-50/60 border-r-4 border-eco-primary' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Packages
                    </a>

                    <!-- Transactions Link -->
                    <a href="<?= URLROOT ?>/admin/transactions" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-300 group <?= $activePage === 'transactions' ? 'text-eco-primary bg-emerald-50/60 border-r-4 border-eco-primary' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Transactions
                    </a>

                    <!-- Companies Link -->
                    <a href="<?= URLROOT ?>/admin/companies" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-300 group <?= $activePage === 'companies' ? 'text-eco-primary bg-emerald-50/60 border-r-4 border-eco-primary' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Companies
                    </a>

                    <!-- Drivers Link -->
                    <a href="<?= URLROOT ?>/admin/drivers" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition-all duration-300 group <?= $activePage === 'drivers' ? 'text-eco-primary bg-emerald-50/60 border-r-4 border-eco-primary' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5 transition group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Drivers
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- User Profile Card -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/40">
            <div class="flex items-center justify-between p-2 rounded-xl bg-white shadow-sm border border-gray-150/40">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'Admin') ?>&background=10B981&color=ffffff&bold=true" 
                         alt="Admin Avatar" class="w-8.5 h-8.5 rounded-xl border object-cover">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate" title="<?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>">
                            <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
                        </p>
                        <span class="inline-flex px-1.5 py-0.5 bg-eco-light/50 text-eco-dark text-[8px] font-bold rounded-md uppercase tracking-wider scale-95 origin-left">
                            Admin
                        </span>
                    </div>
                </div>
                <a href="<?= URLROOT ?>/auth/logout" class="text-gray-400 hover:text-red-500 p-1.5 hover:bg-red-50 rounded-lg transition duration-200" title="Sign Out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>
    </div>
</aside>

<!-- Slide-out Mobile Sidebar Drawer -->
<div x-show="mobileMenuOpen" class="fixed inset-0 z-40 md:hidden flex" style="display: none;" x-transition>
    <!-- Overlay -->
    <div @click="mobileMenuOpen = false" class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"></div>
    
    <!-- Drawer Menu -->
    <div class="relative w-64 max-w-xs bg-white h-full shadow-2xl flex flex-col justify-between z-50"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full">
         
        <div>
            <!-- Logo Section -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-gray-100">
                <a href="<?= URLROOT ?>" class="text-eco-primary font-bold text-2xl flex items-center">
                    <span class="mr-2">🌿</span> EcoPath
                </a>
                <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Navigation -->
            <div class="p-5">
                <nav class="space-y-1.5">
                    <!-- Dashboard -->
                    <a href="<?= URLROOT ?>/admin" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition <?= $activePage === 'dashboard' ? 'text-eco-primary bg-emerald-50' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>

                    <!-- Users -->
                    <a href="<?= URLROOT ?>/admin/users" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition <?= $activePage === 'users' ? 'text-eco-primary bg-emerald-50' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Users
                    </a>

                    <!-- Packages -->
                    <a href="<?= URLROOT ?>/admin/packages" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition <?= $activePage === 'packages' ? 'text-eco-primary bg-emerald-50' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Packages
                    </a>

                    <!-- Transactions -->
                    <a href="<?= URLROOT ?>/admin/transactions" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition <?= $activePage === 'transactions' ? 'text-eco-primary bg-emerald-50' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Transactions
                    </a>

                    <!-- Companies -->
                    <a href="<?= URLROOT ?>/admin/companies" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition <?= $activePage === 'companies' ? 'text-eco-primary bg-emerald-50' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Companies
                    </a>

                    <!-- Drivers -->
                    <a href="<?= URLROOT ?>/admin/drivers" 
                       class="flex items-center px-4 py-3 text-xs font-semibold rounded-xl transition <?= $activePage === 'drivers' ? 'text-eco-primary bg-emerald-50' : 'text-gray-500 hover:text-eco-primary hover:bg-gray-50' ?>">
                        <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Drivers
                    </a>
                </nav>
            </div>
        </div>
        
        <!-- User Profile Card -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/40">
            <div class="flex items-center justify-between p-2 rounded-xl bg-white shadow-sm border border-gray-150/40">
                <div class="flex items-center space-x-2.5 min-w-0">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'Admin') ?>&background=10B981&color=ffffff&bold=true" 
                         alt="Admin Avatar" class="w-8.5 h-8.5 rounded-xl border object-cover">
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-gray-800 truncate">
                            <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>
                        </p>
                        <span class="inline-flex px-1.5 py-0.5 bg-eco-light/55 text-eco-dark text-[8px] font-bold rounded-md uppercase tracking-wider">
                            Admin
                        </span>
                    </div>
                </div>
                <a href="<?= URLROOT ?>/auth/logout" class="text-gray-400 hover:text-red-500 p-1.5 hover:bg-red-50 rounded-lg transition" title="Sign Out">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </a>
            </div>
        </div>
    </div>
</div>
