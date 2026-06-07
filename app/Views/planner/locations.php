<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Manage Locations' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'eco-primary': '#10B981', 'eco-dark': '#064E3B', 'eco-light': '#D1FAE5' },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        // Pagination logic for tables
        document.addEventListener('alpine:init', () => {
            Alpine.data('tablePagination', (itemsPerPage = 10) => ({
                search: '',
                currentPage: 1,
                itemsPerPage: itemsPerPage,
                totalItems: 0,
                rows: [],
                initTable(tbody) {
                    this.rows = Array.from(tbody.querySelectorAll('tr.searchable-row'));
                    this.totalItems = this.rows.length;
                    this.filterAndPaginate();
                },
                filterAndPaginate() {
                    const searchLower = this.search.toLowerCase();
                    let visibleCount = 0;
                    
                    this.rows.forEach(row => {
                        const text = row.innerText.toLowerCase();
                        const matches = text.includes(searchLower);
                        row.dataset.match = matches;
                        if (matches) visibleCount++;
                    });
                    
                    this.totalItems = visibleCount;
                    const totalPages = Math.ceil(this.totalItems / this.itemsPerPage) || 1;
                    if (this.currentPage > totalPages) this.currentPage = 1;
                    
                    let currentIndex = 0;
                    this.rows.forEach(row => {
                        if (row.dataset.match === 'true') {
                            const show = currentIndex >= (this.currentPage - 1) * this.itemsPerPage && currentIndex < this.currentPage * this.itemsPerPage;
                            row.style.display = show ? '' : 'none';
                            currentIndex++;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                },
                nextPage() {
                    if (this.currentPage * this.itemsPerPage < this.totalItems) {
                        this.currentPage++;
                        this.filterAndPaginate();
                    }
                },
                prevPage() {
                    if (this.currentPage > 1) {
                        this.currentPage--;
                        this.filterAndPaginate();
                    }
                },
                get totalPages() {
                    return Math.ceil(this.totalItems / this.itemsPerPage) || 1;
                }
            }));
        });
    </script>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800" 
      x-data="{ 
          openRegion: false, openCity: false,
          editRegionModal: false, editCityModal: false,
          editRegionData: {}, editCityData: {}
      }">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-lg border-r border-gray-100 flex-shrink-0 relative hidden md:block z-20">
        <div class="h-full flex flex-col">
            <div class="h-20 flex items-center px-8 border-b border-gray-100">
                <a href="<?= URLROOT ?>" class="text-eco-primary font-bold text-3xl tracking-tight">🌿 EcoPath</a>
            </div>
            
            <div class="p-6">
                <p class="text-xs uppercase tracking-wider text-gray-400 font-semibold mb-4">Planner Portal</p>
                <nav class="space-y-2">
                    <a href="<?= URLROOT ?>/planner" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Dashboard
                    </a>
                    <a href="<?= URLROOT ?>/planner/packages" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Packages
                    </a>
                    <a href="<?= URLROOT ?>/planner/locations" class="flex items-center px-4 py-3 bg-eco-light text-eco-dark rounded-xl font-medium transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Locations
                    </a>
                    <a href="<?= URLROOT ?>/planner/infrastructure" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Infrastructure
                    </a>
                </nav>
            </div>
            
            <div class="mt-auto p-6 border-t border-gray-100">
                <a href="<?= URLROOT ?>/auth/logout" class="flex items-center px-4 py-3 text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-xl font-medium transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto flex flex-col relative">
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center px-8 flex-shrink-0 z-10 sticky top-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Locations</h1>
                <p class="text-sm text-gray-500">Manage Geographic Regions and Cities</p>
            </div>
        </header>

        <div class="p-8 space-y-8 max-w-7xl">
            <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative">
                <span class="block sm:inline">Action completed successfully!</span>
            </div>
            <?php endif; ?>

            <?php if(isset($_GET['error']) && $_GET['error'] == 'import_failed'): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative">
                <span class="block sm:inline font-bold">Import Blocked:</span>
                <ul class="list-disc ml-5 mt-1">
                    <?php if(!empty($_GET['missing_regions'])): ?>
                        <li>The following regions do not exist in the system: <strong><?= htmlspecialchars($_GET['missing_regions']) ?></strong>. Please create them first before importing.</li>
                    <?php endif; ?>
                    <?php if(!empty($_GET['existing_cities'])): ?>
                        <li>The following cities already exist in the system: <strong><?= htmlspecialchars($_GET['existing_cities']) ?></strong>. Duplicates are not allowed.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="flex space-x-4 items-center">
                <button @click="openRegion = true" class="bg-white border-2 border-eco-primary text-eco-dark hover:bg-eco-light px-4 py-2 rounded-lg font-medium transition">
                    + Add Region
                </button>
                <button @click="openCity = true" class="bg-white border-2 border-eco-primary text-eco-dark hover:bg-eco-light px-4 py-2 rounded-lg font-medium transition">
                    + Add City
                </button>

                <!-- Temporary City Import -->
                <form action="<?= URLROOT ?>/planner/import_cities_temp" method="POST" enctype="multipart/form-data" class="flex items-center ml-auto bg-gray-100 p-2 rounded-lg">
                    <span class="text-xs font-bold text-gray-500 mr-2 uppercase">Temp Import:</span>
                    <input type="file" name="excel_file" accept=".xlsx" required class="text-sm">
                    <button type="submit" class="bg-blue-500 text-white px-3 py-1 text-sm rounded-md hover:bg-blue-600 transition">Import Cities (.xlsx)</button>
                </form>
            </div>

            <!-- Existing Data Tables -->
            <div class="grid grid-cols-1 gap-8">
                <!-- Grouped Cities by Region -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Regions & Cities</h3>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        <?php 
                        // Group cities by RegionID
                        $groupedCities = [];
                        foreach ($data['cities'] as $c) {
                            $groupedCities[$c->RegionID][] = $c;
                        }
                        
                        foreach($data['regions'] as $r): 
                            $citiesInRegion = $groupedCities[$r->RegionID] ?? [];
                        ?>
                        <div class="p-6 hover:bg-gray-50/50 transition duration-150">
                            <!-- Region Header -->
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h4 class="text-lg font-bold text-eco-dark flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-eco-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <?= htmlspecialchars($r->RegionName) ?>
                                    </h4>
                                    <div class="text-xs text-gray-500 mt-1 ml-7">
                                        <span class="mr-3">Created by: <?= htmlspecialchars($r->CreatorName ?? 'Unknown') ?></span>
                                        <?php if (!empty($r->UpdaterName)): ?>
                                            <span>Updated by: <?= htmlspecialchars($r->UpdaterName) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <button @click="editRegionData = { id: '<?= $r->RegionID ?>', name: '<?= htmlspecialchars($r->RegionName, ENT_QUOTES) ?>' }; editRegionModal = true" class="text-blue-500 hover:text-blue-700 text-sm font-medium">Edit Region</button>
                                </div>
                            </div>
                            
                            <!-- Cities List -->
                            <div class="ml-7 pl-4 border-l-2 border-gray-100 space-y-3">
                                <?php if (empty($citiesInRegion)): ?>
                                    <div class="text-sm text-gray-400 italic">No cities added to this region yet.</div>
                                <?php else: ?>
                                    <table class="w-full text-left text-sm">
                                        <thead class="text-gray-400 uppercase text-xs border-b border-gray-100">
                                            <tr>
                                                <th class="py-2 w-1/3">City Name</th>
                                                <th class="py-2 w-1/2">Audit Info</th>
                                                <th class="py-2 text-right">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-50">
                                            <?php foreach ($citiesInRegion as $c): ?>
                                            <tr class="group">
                                                <td class="py-2 font-medium text-gray-700"><?= htmlspecialchars($c->CityName) ?></td>
                                                <td class="py-2 text-xs text-gray-500">
                                                    <div>Created by: <?= htmlspecialchars($c->CreatorName ?? 'Unknown') ?></div>
                                                    <?php if (!empty($c->UpdaterName)): ?>
                                                        <div class="text-gray-400">Updated by: <?= htmlspecialchars($c->UpdaterName) ?></div>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="py-2 text-right">
                                                    <button @click="editCityData = { id: '<?= $c->CityID ?>', region_id: '<?= $c->RegionID ?>', name: '<?= htmlspecialchars($c->CityName, ENT_QUOTES) ?>' }; editCityModal = true" class="opacity-0 group-hover:opacity-100 text-blue-500 hover:text-blue-700 transition font-medium">Edit</button>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Region Modal -->
    <div x-show="openRegion" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="openRegion = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold">Add Region</h3>
                <button @click="openRegion = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="<?= URLROOT ?>/planner/locations" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="action" value="add_region">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Region/State Name</label>
                    <input type="text" name="name" required placeholder="e.g., Yangon Region" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" @click="openRegion = false" class="w-1/2 bg-gray-100 text-gray-800 py-2.5 rounded-xl font-medium hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="w-1/2 bg-eco-primary text-white py-2.5 rounded-xl font-medium hover:bg-eco-dark transition">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Region Modal -->
    <div x-show="editRegionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="editRegionModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold">Edit Region</h3>
                <button @click="editRegionModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="<?= URLROOT ?>/planner/locations" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="action" value="update_region">
                <input type="hidden" name="id" x-model="editRegionData.id">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Region/State Name</label>
                    <input type="text" name="name" x-model="editRegionData.name" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" @click="editRegionModal = false" class="w-1/2 bg-gray-100 text-gray-800 py-2.5 rounded-xl font-medium hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="w-1/2 bg-blue-500 text-white py-2.5 rounded-xl font-medium hover:bg-blue-600 transition">Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add City Modal -->
    <div x-show="openCity" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="openCity = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold">Add City</h3>
                <button @click="openCity = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="<?= URLROOT ?>/planner/locations" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="action" value="add_city">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City Name</label>
                    <input type="text" name="name" required placeholder="e.g., Mandalay" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                    <select name="region_id" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary bg-white" required>
                        <option value="">Select Region</option>
                        <?php foreach($data['regions'] as $r): ?>
                            <option value="<?= $r->RegionID ?>"><?= htmlspecialchars($r->RegionName) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" @click="openCity = false" class="w-1/2 bg-gray-100 text-gray-800 py-2.5 rounded-xl font-medium hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="w-1/2 bg-eco-primary text-white py-2.5 rounded-xl font-medium hover:bg-eco-dark transition">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit City Modal -->
    <div x-show="editCityModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/50 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="editCityModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden relative">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="text-lg font-bold">Edit City</h3>
                <button @click="editCityModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <form action="<?= URLROOT ?>/planner/locations" method="POST" class="p-6 space-y-4">
                <input type="hidden" name="action" value="update_city">
                <input type="hidden" name="id" x-model="editCityData.id">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City Name</label>
                    <input type="text" name="name" x-model="editCityData.name" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                    <select name="region_id" x-model="editCityData.region_id" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary bg-white" required>
                        <?php foreach($data['regions'] as $r): ?>
                            <option value="<?= $r->RegionID ?>"><?= htmlspecialchars($r->RegionName) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex space-x-3 pt-4">
                    <button type="button" @click="editCityModal = false" class="w-1/2 bg-gray-100 text-gray-800 py-2.5 rounded-xl font-medium hover:bg-gray-200 transition">Cancel</button>
                    <button type="submit" class="w-1/2 bg-blue-500 text-white py-2.5 rounded-xl font-medium hover:bg-blue-600 transition">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
