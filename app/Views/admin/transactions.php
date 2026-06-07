<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Transactions' ?></title>
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
</head>
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800">

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
                    <a href="<?= URLROOT ?>/admin/transactions" class="flex items-center px-4 py-3 bg-eco-light text-eco-dark rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3 text-eco-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Transactions
                    </a>
                    <a href="<?= URLROOT ?>/admin/companies" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        B2B Sales
                    </a>
                    <a href="<?= URLROOT ?>/admin/drivers" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                        Drivers
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
                <h1 class="text-2xl font-bold text-gray-800">Financials</h1>
                <p class="text-sm text-gray-500">View recent transactions and donations</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <button class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-4 py-2 rounded-lg font-medium transition flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export CSV
                </button>
                <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-eco-primary to-blue-400 p-0.5 ml-4">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'A') ?>&background=ffffff&color=10B981" alt="Profile" class="w-full h-full rounded-full border-2 border-white object-cover">
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-8 space-y-8 max-w-7xl">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Transaction History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-gray-400 text-sm uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Transaction ID</th>
                                <th class="px-6 py-4 font-semibold">Customer</th>
                                <th class="px-6 py-4 font-semibold">Package</th>
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 font-semibold">Amount</th>
                                <th class="px-6 py-4 font-semibold text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            <?php foreach ($data['transactions'] as $tx): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 font-medium text-gray-900"><?= $tx->TransactionID ?></td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($tx->Username) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($tx->Email) ?></p>
                                </td>
                                <td class="px-6 py-4 text-gray-600"><?= htmlspecialchars($tx->PackageName ?? 'Donation/Other') ?></td>
                                <td class="px-6 py-4 text-gray-500"><?= date('M j, Y', strtotime($tx->TransactionDate)) ?></td>
                                <td class="px-6 py-4 font-bold text-gray-800">$<?= number_format($tx->TotalAmount, 2) ?></td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        <?= strtolower($tx->Status) === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= htmlspecialchars($tx->Status) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($data['transactions'])): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                    <p>No transactions found.</p>
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
