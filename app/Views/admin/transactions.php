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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800" x-data="{ mobileMenuOpen: false }">

    <?php
    $activePage = 'transactions';
    require APPROOT . '/app/Views/admin/inc/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative z-10 overflow-y-auto">
        
        <?php
        $headerTitle = 'Financials';
        $headerSubtitle = 'View recent transactions and donations';
        ob_start();
        ?>
        <button class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-5 py-2.5 rounded-xl font-medium transition flex items-center text-xs">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            Export CSV
        </button>
        <?php
        $headerAction = ob_get_clean();
        require APPROOT . '/app/Views/admin/inc/header.php';
        ?>

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
