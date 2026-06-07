<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Manage Drivers' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800" x-data="{ showAddModal: false, showEditModal: false, editDriver: {} }">

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
                    <a href="<?= URLROOT ?>/admin/transactions" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Transactions
                    </a>
                    <a href="<?= URLROOT ?>/admin/companies" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Companies
                    </a>
                    <a href="<?= URLROOT ?>/admin/drivers" class="flex items-center px-4 py-3 bg-eco-light text-eco-dark rounded-xl font-medium transition duration-200">
                        <svg class="w-5 h-5 mr-3 text-eco-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
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
                <h1 class="text-2xl font-bold text-gray-800">Manage Drivers</h1>
                <p class="text-sm text-gray-500">Add and manage vehicle drivers</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <button @click="showAddModal = true" class="bg-eco-primary hover:bg-eco-dark text-white px-5 py-2.5 rounded-xl font-medium transition shadow-sm flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    Add Driver
                </button>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-8 space-y-8 max-w-7xl">
            <?php if(isset($_GET['success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">Action completed successfully!</span>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['error']) && $_GET['error'] == 'linked'): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">Cannot delete driver because they are linked to a bus.</span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Registered Drivers</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-gray-400 text-sm uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">Driver</th>
                                <th class="px-6 py-4 font-semibold">Personal Info</th>
                                <th class="px-6 py-4 font-semibold">License Code</th>
                                <th class="px-6 py-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            <?php if (empty($data['drivers'])): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">No drivers registered yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data['drivers'] as $driver): ?>
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <?php if (!empty($driver->ProfileImage)): ?>
                                                <img src="<?= URLROOT ?>/<?= htmlspecialchars($driver->ProfileImage) ?>" alt="" class="w-10 h-10 rounded-full object-cover mr-3 border border-gray-200 shadow-sm">
                                            <?php else: ?>
                                                <div class="w-10 h-10 rounded-full bg-eco-light text-eco-dark flex items-center justify-center font-bold mr-3 border border-eco-primary/20">
                                                    <?= strtoupper(substr($driver->DriverName, 0, 1)) ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="font-medium text-gray-800"><?= htmlspecialchars($driver->DriverName) ?></div>
                                                <div class="text-xs text-gray-500"><?= htmlspecialchars($driver->DriverID) ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm">
                                            <div class="text-gray-800"><span class="text-gray-500 mr-1">NRC:</span> <?= htmlspecialchars($driver->NRC ?: 'N/A') ?></div>
                                            <div class="text-gray-500 text-xs mt-0.5">DOB: <?= htmlspecialchars($driver->DateOfBirth ?: 'N/A') ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium border border-gray-200">
                                            <?= htmlspecialchars($driver->LicenseCode) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button @click="editDriver = {
                                            DriverID: '<?= $driver->DriverID ?>',
                                            DriverName: '<?= htmlspecialchars($driver->DriverName, ENT_QUOTES) ?>',
                                            LicenseCode: '<?= htmlspecialchars($driver->LicenseCode, ENT_QUOTES) ?>',
                                            DateOfBirth: '<?= htmlspecialchars($driver->DateOfBirth ?? '', ENT_QUOTES) ?>',
                                            NRC: '<?= htmlspecialchars($driver->NRC ?? '', ENT_QUOTES) ?>'
                                        }; showEditModal = true" class="text-blue-500 hover:text-blue-700 font-medium">Edit</button>

                                        <form action="<?= URLROOT ?>/admin/drivers" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="DriverID" value="<?= $driver->DriverID ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Driver Modal -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showAddModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Add New Driver</h3>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="<?= URLROOT ?>/admin/drivers" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="action" value="create">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Driver Name</label>
                    <input type="text" name="DriverName" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License Code</label>
                    <input type="text" name="LicenseCode" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" name="DateOfBirth" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NRC</label>
                        <input type="text" name="NRC" placeholder="e.g. 12/KAMAYA(N)123456" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Image</label>
                    <input type="file" name="ProfileImage" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-eco-light file:text-eco-dark hover:file:bg-eco-primary hover:file:text-white file:transition">
                </div>
                <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="bg-eco-primary hover:bg-eco-dark text-white px-6 py-2 rounded-xl transition font-medium">Save Driver</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Driver Modal -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Edit Driver</h3>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="<?= URLROOT ?>/admin/drivers" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="DriverID" x-model="editDriver.DriverID">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Driver Name</label>
                    <input type="text" name="DriverName" x-model="editDriver.DriverName" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">License Code</label>
                    <input type="text" name="LicenseCode" x-model="editDriver.LicenseCode" required class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                        <input type="date" name="DateOfBirth" x-model="editDriver.DateOfBirth" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NRC</label>
                        <input type="text" name="NRC" x-model="editDriver.NRC" placeholder="e.g. 12/KAMAYA(N)123456" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profile Image (Leave blank to keep current)</label>
                    <input type="file" name="ProfileImage" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-eco-light file:text-eco-dark hover:file:bg-eco-primary hover:file:text-white file:transition">
                </div>
                <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-100">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition">Cancel</button>
                    <button type="submit" class="bg-eco-primary hover:bg-eco-dark text-white px-6 py-2 rounded-xl transition font-medium">Update Driver</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
