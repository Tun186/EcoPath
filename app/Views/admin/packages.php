<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Manage Packages' ?></title>
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
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800" x-data="{ openModal: false, mobileMenuOpen: false }">

    <?php
    $activePage = 'packages';
    require APPROOT . '/app/Views/admin/inc/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative z-10 overflow-y-auto">
        
        <?php
        $headerTitle = 'Travel Packages';
        $headerSubtitle = 'Manage eco-friendly tours and trips';
        $headerAction = '
            <button @click="openModal = true" class="bg-eco-primary hover:bg-eco-dark text-white px-4 py-2 rounded-lg font-medium shadow-sm transition flex items-center text-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Package
            </button>
        ';
        require APPROOT . '/app/Views/admin/inc/header.php';
        ?>

        <!-- Page Content -->
        <div class="p-8 space-y-8 max-w-7xl">
            
            <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline">Package successfully created!</span>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($data['packages'] as $package): ?>
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transform transition hover:-translate-y-1 hover:shadow-md">
                    <div class="h-32 bg-gray-200 relative">
                        <!-- Placeholder Image -->
                        <img src="https://images.unsplash.com/photo-1542314831-c6a4d27ce6a2?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover" alt="Package Image">
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-bold text-eco-dark">
                            $<?= number_format($package->Price, 2) ?>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($package->PackageName) ?></h3>
                        
                        <div class="flex items-center text-gray-500 mb-4 text-sm">
                            <svg class="w-4 h-4 mr-1 text-eco-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                            <span><?= $package->BaseTreeCount ?> Trees Planted</span>
                        </div>
                        
                        <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                            <span class="text-xs text-gray-400">ID: <?= $package->PackageID ?></span>
                            <button class="text-sm font-medium text-blue-600 hover:text-blue-800">Edit</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if(empty($data['packages'])): ?>
                <div class="col-span-full bg-white p-8 rounded-2xl border border-dashed border-gray-300 text-center">
                    <div class="text-gray-400 mb-2">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-800">No packages yet</h3>
                    <p class="text-gray-500 text-sm mt-1">Create your first eco-friendly travel package.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Create Package Modal -->
    <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div x-show="openModal" class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-900 opacity-50"></div>
            </div>

            <!-- Modal panel -->
            <div x-show="openModal" @click.away="openModal = false" class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-xl font-bold leading-6 text-gray-900">Create New Package</h3>
                    <button @click="openModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form action="<?= URLROOT ?>/admin/packages" method="POST">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Package Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-eco-primary focus:border-eco-primary outline-none transition" placeholder="e.g. Bagan Heritage Tour">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                                <input type="number" step="0.01" name="price" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-eco-primary focus:border-eco-primary outline-none transition" placeholder="299.99">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Base Trees Planted</label>
                                <input type="number" name="trees" required class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-eco-primary focus:border-eco-primary outline-none transition" placeholder="10">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" @click="openModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-gray-500 transition">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-eco-primary rounded-xl hover:bg-eco-dark focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-eco-primary transition shadow-sm">Save Package</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
