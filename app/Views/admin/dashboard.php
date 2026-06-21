<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Admin Dashboard' ?></title>
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
    $activePage = 'dashboard';
    require APPROOT . '/app/Views/admin/inc/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative z-10 overflow-y-auto">
        
        <?php
        $headerTitle = 'Overview';
        $headerSubtitle = 'Welcome back, ' . ($_SESSION['username'] ?? 'Admin') . ' 👋';
        require APPROOT . '/app/Views/admin/inc/header.php';
        ?>

        <!-- Dashboard Content -->
        <div class="p-8 space-y-8 max-w-7xl">
            
            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <!-- Stat Card 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4 transform transition hover:-translate-y-1">
                    <div class="p-4 bg-eco-light text-eco-primary rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <h3 class="text-2xl font-bold text-gray-800">1,248</h3>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4 transform transition hover:-translate-y-1">
                    <div class="p-4 bg-blue-50 text-blue-500 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Active Packages</p>
                        <h3 class="text-2xl font-bold text-gray-800">42</h3>
                    </div>
                </div>

                <!-- Stat Card 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4 transform transition hover:-translate-y-1">
                    <div class="p-4 bg-green-50 text-green-600 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Trees Planted</p>
                        <h3 class="text-2xl font-bold text-gray-800">8,900</h3>
                    </div>
                </div>

                <!-- Stat Card 4 -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4 transform transition hover:-translate-y-1">
                    <div class="p-4 bg-purple-50 text-purple-500 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Donations</p>
                        <h3 class="text-2xl font-bold text-gray-800">$12,450</h3>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800">Recent Registrations</h3>
                    <a href="#" class="text-sm text-eco-primary font-medium hover:text-green-600">View all</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-gray-400 text-sm uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">User</th>
                                <th class="px-6 py-4 font-semibold">Role</th>
                                <th class="px-6 py-4 font-semibold">Date</th>
                                <th class="px-6 py-4 font-semibold text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            <!-- Example Row -->
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center font-bold mr-3">J</div>
                                    <div>
                                        <p class="font-medium text-gray-800">John Doe</p>
                                        <p class="text-xs text-gray-500">john@example.com</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-eco-light text-eco-dark">
                                        Admin
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">May 24, 2026</td>
                                <td class="px-6 py-4 text-right">
                                    <button class="text-gray-400 hover:text-eco-primary"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
