<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'B2B Companies' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'eco-primary': '#10B981',
                        'eco-dark': '#064E3B',
                        'eco-light': '#D1FAE5'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800" x-data="{ openCreateModal: false, openBuyModal: false, selectedCompanyId: '', selectedCompanyName: '' }">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg border-r border-gray-100 flex-shrink-0 relative hidden md:block z-20">
        <div class="h-full flex flex-col">
            <div class="h-20 flex items-center px-8 border-b border-gray-100">
                <a href="<?= URLROOT ?>" class="text-eco-primary font-bold text-3xl tracking-tight">🌿 EcoPath</a>
            </div>
            
            <div class="p-6">
                <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-4">Administration</p>
                <nav class="space-y-2">
                    <a href="<?= URLROOT ?>/admin" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>
                    <a href="<?= URLROOT ?>/admin/users" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        Users
                    </a>
                    <a href="<?= URLROOT ?>/admin/packages" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Packages
                    </a>
                    <a href="<?= URLROOT ?>/admin/companies" class="flex items-center px-4 py-3 bg-eco-light text-eco-dark rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3 text-eco-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        B2B Sales
                    </a>
                    <a href="<?= URLROOT ?>/admin/drivers" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Drivers
                    </a>
                    <a href="<?= URLROOT ?>/admin/transactions" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                        Transactions
                    </a>
                </nav>
            </div>
            
            <div class="mt-auto p-6 border-t border-gray-100">
                <a href="<?= URLROOT ?>/auth/logout" class="flex items-center text-red-500 hover:text-red-700 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Sign Out
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative z-10 overflow-y-auto">
        
        <!-- Header -->
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 z-10 border-b border-gray-100 sticky top-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Corporate Carbon Sales</h1>
                <p class="text-sm text-gray-500">Manage B2B partners and sell carbon credits</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="flex flex-col text-right mr-4">
                    <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Available Pool</span>
                    <span class="text-lg font-bold text-eco-primary"><?= number_format($data['availableCredits']) ?> Credits</span>
                </div>
                <button @click="openCreateModal = true" class="bg-eco-primary hover:bg-eco-dark text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Company
                </button>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-8 space-y-8 max-w-7xl">
            
            <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative">
                <span class="block sm:inline">Action completed successfully!</span>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative">
                <span class="block sm:inline">Not enough available global credits to fulfill this order!</span>
            </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Registered Corporate Partners</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-gray-400 text-sm uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Company ID</th>
                                <th class="px-6 py-4 font-semibold">Company Name</th>
                                <th class="px-6 py-4 font-semibold">Contact Email</th>
                                <th class="px-6 py-4 font-semibold">Total Credits Held</th>
                                <th class="px-6 py-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            <?php foreach ($data['companies'] as $company): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 font-medium text-gray-500"><?= $company->CompanyID ?></td>
                                <td class="px-6 py-4 font-bold text-gray-800"><?= htmlspecialchars($company->CompanyName) ?></td>
                                <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($company->ContactEmail) ?></td>
                                <td class="px-6 py-4 font-bold text-eco-primary"><?= number_format($company->PurchasedCredits) ?></td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openBuyModal = true; selectedCompanyId = '<?= $company->CompanyID ?>'; selectedCompanyName = '<?= htmlspecialchars($company->CompanyName, ENT_QUOTES) ?>';" class="bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                                        Sell Credits
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($data['companies'])): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                    <p>No corporate partners registered yet.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Create Company Modal -->
    <div x-show="openCreateModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="openCreateModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
            </div>

            <div x-show="openCreateModal" @click.away="openCreateModal = false" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold leading-6 text-gray-900">Add B2B Partner</h3>
                </div>
                
                <form action="<?= URLROOT ?>/admin/companies" method="POST">
                    <input type="hidden" name="action" value="create">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-eco-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                            <input type="text" name="registration" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-eco-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                            <input type="email" name="email" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-eco-primary outline-none">
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="openCreateModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-eco-primary rounded-xl hover:bg-eco-dark transition">Save Company</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sell Credits Modal -->
    <div x-show="openBuyModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="openBuyModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
            </div>

            <div x-show="openBuyModal" @click.away="openBuyModal = false" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold leading-6 text-gray-900">Sell Carbon Credits</h3>
                </div>
                
                <form action="<?= URLROOT ?>/admin/companies" method="POST">
                    <input type="hidden" name="action" value="buy">
                    <input type="hidden" name="company_id" x-model="selectedCompanyId">
                    
                    <div class="space-y-4">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-sm text-gray-500">Selling to</p>
                            <p class="font-bold text-gray-800" x-text="selectedCompanyName"></p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount to Sell</label>
                            <div class="flex items-center">
                                <input type="number" name="amount" min="1" max="<?= $data['availableCredits'] ?>" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-eco-primary outline-none">
                                <span class="ml-3 text-gray-500 font-medium whitespace-nowrap">Credits</span>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Maximum available: <?= number_format($data['availableCredits']) ?></p>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="openBuyModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition">Complete Sale</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
