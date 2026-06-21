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
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800" x-data="{ openCreateModal: false, openBuyModal: false, selectedCompanyId: '', selectedCompanyName: '', mobileMenuOpen: false }">

    <?php
    $activePage = 'companies';
    require APPROOT . '/app/Views/admin/inc/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative z-10 overflow-y-auto">
        
        <?php
        $headerTitle = 'Corporate Carbon Sales';
        $headerSubtitle = 'Manage B2B partners and sell carbon credits';
        ob_start();
        ?>
        <div class="flex items-center space-x-3.5">
            <div class="flex flex-col text-right mr-1.5 sm:mr-3.5">
                <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-bold tracking-wider">Available Pool</span>
                <span class="text-xs sm:text-sm font-bold text-eco-primary"><?= number_format($data['availableCredits']) ?> Credits</span>
            </div>
            <button @click="openCreateModal = true" class="bg-eco-primary hover:bg-eco-dark text-white px-4 py-2.5 rounded-xl font-semibold shadow-sm transition flex items-center text-xxs sm:text-xs">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Company
            </button>
        </div>
        <?php
        $headerAction = ob_get_clean();
        require APPROOT . '/app/Views/admin/inc/header.php';
        ?>

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
