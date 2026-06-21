<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Manage Infrastructure' ?></title>
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50 font-sans h-screen flex overflow-hidden text-gray-800" 
      x-data="{ 
          openHotel: false, openLandmark: false, openBus: false, 
          openEditHotel: false, openEditLandmark: false, 
          openRegion: false, openCity: false,
          openImportHotel: false, openImportLandmark: false,
          editHotelData: {}, editLandmarkData: {}, 
          allCities: <?= htmlspecialchars(json_encode($data['cities']), ENT_QUOTES, 'UTF-8') ?>,
          hotelRegion: '', editHotelRegion: '',
          landmarkRegion: '', editLandmarkRegion: '',
          
          init() {
              this.$watch('openHotel', v => { if(v) setTimeout(() => setupMap('hotelMap', 'hotel_lat', 'hotel_lng'), 100) });
              this.$watch('openLandmark', v => { if(v) setTimeout(() => setupMap('landmarkMap', 'landmark_lat', 'landmark_lng'), 100) });
              this.$watch('openEditHotel', v => { if(v) setTimeout(() => setupMap('editHotelMap', 'edit_hotel_lat', 'edit_hotel_lng', this.editHotelData.lat, this.editHotelData.lng), 100) });
              this.$watch('openEditLandmark', v => { if(v) setTimeout(() => setupMap('editLandmarkMap', 'edit_landmark_lat', 'edit_landmark_lng', this.editLandmarkData.lat, this.editLandmarkData.lng), 100) });
          }
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
                    <a href="<?= URLROOT ?>/planner/locations" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Locations
                    </a>
                    <a href="<?= URLROOT ?>/planner/infrastructure" class="flex items-center px-4 py-3 bg-eco-light text-eco-dark rounded-xl font-medium transition">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        Infrastructure
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
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 z-10 border-b border-gray-100 sticky top-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Infrastructure</h1>
                <p class="text-sm text-gray-500">Manage locations and transport for packages</p>
            </div>
        </header>

        <div class="p-8 space-y-8 max-w-7xl">
            <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative">
                <span class="block sm:inline">Item successfully added/updated!</span>
            </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['import_success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative">
                <span class="block sm:inline"><?= $_SESSION['import_success'] ?></span>
            </div>
            <?php unset($_SESSION['import_success']); endif; ?>

            <?php if(isset($_SESSION['import_errors'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative">
                <strong class="font-bold">Import Errors:</strong>
                <ul class="list-disc ml-5 mt-2 text-sm space-y-1">
                    <?php foreach($_SESSION['import_errors'] as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php unset($_SESSION['import_errors']); endif; ?>

            <!-- Action Buttons -->
            <div class="flex justify-between items-center">
                <div class="flex space-x-4">
                    <button @click="openHotel = true" class="bg-white border-2 border-eco-primary text-eco-dark hover:bg-eco-light px-4 py-2 rounded-lg font-medium transition">
                        + Add Hotel
                    </button>
                    <button @click="openLandmark = true" class="bg-white border-2 border-eco-primary text-eco-dark hover:bg-eco-light px-4 py-2 rounded-lg font-medium transition">
                        + Add Landmark
                    </button>
                    <button @click="openBus = true" class="bg-white border-2 border-eco-primary text-eco-dark hover:bg-eco-light px-4 py-2 rounded-lg font-medium transition">
                        + Add Bus
                    </button>
                </div>
                
                <div>
                    <?php if($data['showInactive']): ?>
                        <a href="<?= URLROOT ?>/planner/infrastructure" class="text-sm font-medium text-blue-500 hover:text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200">View Active Records</a>
                    <?php else: ?>
                        <a href="<?= URLROOT ?>/planner/infrastructure?show_inactive=1" class="text-sm font-medium text-gray-500 hover:text-gray-700 bg-gray-200 px-3 py-1.5 rounded-lg border border-gray-300">View Inactive Records</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Existing Data Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Hotels Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="tablePagination(10)">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Hotels</h3>
                        <div class="flex items-center space-x-3">
                            <button @click="openImportHotel = true" class="text-sm bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-gray-600 hover:text-eco-primary shadow-sm font-medium transition flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import
                            </button>
                            <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" class="inline m-0">
                                <input type="hidden" name="action" value="export_hotels">
                                <button type="submit" class="text-sm bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-gray-600 hover:text-eco-primary shadow-sm font-medium transition flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Export
                                </button>
                            </form>
                            <input type="text" x-model="search" @input="filterAndPaginate" placeholder="Search hotels..." class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-eco-primary/50 w-56 ml-3">
                        </div>
                    </div>
                    <table class="w-full text-left text-sm">
                        <thead class="text-gray-400 uppercase text-xs">
                            <tr><th class="px-6 py-3">Name</th><th class="px-6 py-3">Location</th><th class="px-6 py-3">Eco Rating</th><th class="px-6 py-3">Audit</th><th class="px-6 py-3">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" x-ref="tbody" x-init="initTable($el)">
                            <?php foreach($data['hotels'] as $h): ?>
                            <tr>
                                <td class="px-6 py-3 font-medium"><?= htmlspecialchars($h->HotelName) ?></td>
                                <td class="px-6 py-3 text-gray-500 text-sm">
                                    <?= htmlspecialchars($h->CityName ?? 'None') ?>, <?= htmlspecialchars($h->RegionName ?? 'None') ?>
                                </td>
                                <td class="px-6 py-3 text-eco-primary"><?= htmlspecialchars($h->EcoRating) ?></td>
                                <td class="px-6 py-3 text-gray-500 text-xs">
                                    <div>Created by: <?= htmlspecialchars($h->CreatorName ?? 'Unknown') ?></div>
                                    <?php if($h->UpdaterName): ?>
                                    <div class="text-eco-primary">Updated by: <?= htmlspecialchars($h->UpdaterName) ?> (<?= $h->UpdatedAt ?>)</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex space-x-2">
                                        <button @click="editHotelData = { id: '<?= $h->HotelID ?>', name: '<?= htmlspecialchars(addslashes($h->HotelName)) ?>', eco: '<?= htmlspecialchars(addslashes($h->EcoRating)) ?>', lat: '<?= $h->Lat ?>', lng: '<?= $h->Lng ?>', desc: '<?= htmlspecialchars(addslashes($h->Description)) ?>' }; editHotelRegion = '<?= $h->RegionID ?>'; $nextTick(() => { editHotelData.cityId = '<?= $h->CityID ?>'; }); openEditHotel = true" class="text-blue-500 hover:text-blue-700 text-sm font-medium">Edit</button>
                                        
                                        <form action="<?= URLROOT ?>/planner/infrastructure<?= $data['showInactive'] ? '?show_inactive=1' : '' ?>" method="POST" class="inline">
                                            <input type="hidden" name="hotel_id" value="<?= $h->HotelID ?>">
                                            <?php if($data['showInactive']): ?>
                                                <input type="hidden" name="action" value="restore_hotel">
                                                <button type="submit" class="text-green-500 hover:text-green-700 text-sm font-medium">Restore</button>
                                            <?php else: ?>
                                                <input type="hidden" name="action" value="deactivate_hotel">
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium" onclick="return confirm('Are you sure you want to deactivate this hotel?');">Deactivate</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex justify-between items-center text-sm" x-show="totalPages > 1" style="display: none;">
                        <button @click="prevPage" :disabled="page === 1" class="text-gray-600 hover:text-eco-primary disabled:opacity-50 font-medium px-3 py-1 rounded-md border border-gray-200 bg-white shadow-sm">&larr; Previous</button>
                        <span class="text-gray-500">Page <span x-text="page" class="font-bold text-gray-800"></span> of <span x-text="totalPages" class="font-bold text-gray-800"></span></span>
                        <button @click="nextPage" :disabled="page === totalPages" class="text-gray-600 hover:text-eco-primary disabled:opacity-50 font-medium px-3 py-1 rounded-md border border-gray-200 bg-white shadow-sm">Next &rarr;</button>
                    </div>
                </div>

                <!-- Landmarks Table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="tablePagination(10)">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Landmarks</h3>
                        <div class="flex items-center space-x-3">
                            <button @click="openImportLandmark = true" class="text-sm bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-gray-600 hover:text-eco-primary shadow-sm font-medium transition flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                Import
                            </button>
                            <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" class="inline m-0">
                                <input type="hidden" name="action" value="export_landmarks">
                                <button type="submit" class="text-sm bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-gray-600 hover:text-eco-primary shadow-sm font-medium transition flex items-center">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Export
                                </button>
                            </form>
                            <input type="text" x-model="search" @input="filterAndPaginate" placeholder="Search landmarks..." class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-eco-primary/50 w-56 ml-3">
                        </div>
                    </div>
                    <table class="w-full text-left text-sm">
                        <thead class="text-gray-400 uppercase text-xs">
                            <tr><th class="px-6 py-3">Name</th><th class="px-6 py-3">Location</th><th class="px-6 py-3">Audit</th><th class="px-6 py-3">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100" x-ref="tbody" x-init="initTable($el)">
                            <?php foreach($data['landmarks'] as $l): ?>
                            <tr>
                                <td class="px-6 py-3 font-medium"><?= htmlspecialchars($l->LandmarkName) ?></td>
                                <td class="px-6 py-3 text-gray-500 text-sm">
                                    <?= htmlspecialchars($l->CityName ?? 'None') ?>, <?= htmlspecialchars($l->RegionName ?? 'None') ?>
                                </td>
                                <td class="px-6 py-3 text-gray-500 text-xs">
                                    <div>Created by: <?= htmlspecialchars($l->CreatorName ?? 'Unknown') ?></div>
                                    <?php if($l->UpdaterName): ?>
                                    <div class="text-eco-primary">Updated by: <?= htmlspecialchars($l->UpdaterName) ?> (<?= $l->UpdatedAt ?>)</div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex space-x-2">
                                        <button @click="editLandmarkData = { id: '<?= $l->LandmarkID ?>', name: '<?= htmlspecialchars(addslashes($l->LandmarkName)) ?>', lat: '<?= $l->Lat ?>', lng: '<?= $l->Lng ?>', desc: '<?= htmlspecialchars(addslashes($l->Description)) ?>' }; editLandmarkRegion = '<?= $l->RegionID ?>'; $nextTick(() => { editLandmarkData.cityId = '<?= $l->CityID ?>'; }); openEditLandmark = true" class="text-blue-500 hover:text-blue-700 text-sm font-medium">Edit</button>
                                        
                                        <form action="<?= URLROOT ?>/planner/infrastructure<?= $data['showInactive'] ? '?show_inactive=1' : '' ?>" method="POST" class="inline">
                                            <input type="hidden" name="landmark_id" value="<?= $l->LandmarkID ?>">
                                            <?php if($data['showInactive']): ?>
                                                <input type="hidden" name="action" value="restore_landmark">
                                                <button type="submit" class="text-green-500 hover:text-green-700 text-sm font-medium">Restore</button>
                                            <?php else: ?>
                                                <input type="hidden" name="action" value="deactivate_landmark">
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium" onclick="return confirm('Are you sure you want to deactivate this landmark?');">Deactivate</button>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex justify-between items-center text-sm" x-show="totalPages > 1" style="display: none;">
                        <button @click="prevPage" :disabled="page === 1" class="text-gray-600 hover:text-eco-primary disabled:opacity-50 font-medium px-3 py-1 rounded-md border border-gray-200 bg-white shadow-sm">&larr; Previous</button>
                        <span class="text-gray-500">Page <span x-text="page" class="font-bold text-gray-800"></span> of <span x-text="totalPages" class="font-bold text-gray-800"></span></span>
                        <button @click="nextPage" :disabled="page === totalPages" class="text-gray-600 hover:text-eco-primary disabled:opacity-50 font-medium px-3 py-1 rounded-md border border-gray-200 bg-white shadow-sm">Next &rarr;</button>
                    </div>
                </div>
            </div>

            <!-- Buses Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden" x-data="tablePagination(10)">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Transport Buses</h3>
                    <input type="text" x-model="search" @input="filterAndPaginate" placeholder="Search buses..." class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-eco-primary/50 w-64">
                </div>
                <table class="w-full text-left text-sm">
                    <thead class="text-gray-400 uppercase text-xs">
                        <tr><th class="px-6 py-3">Operator</th><th class="px-6 py-3">Emission Rate (kg CO2/km)</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tbody" x-init="initTable($el)">
                        <?php foreach($data['buses'] as $b): ?>
                        <tr>
                            <td class="px-6 py-3 font-medium"><?= htmlspecialchars($b->OperatorName) ?></td>
                            <td class="px-6 py-3 font-mono"><?= $b->EmissionRate ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="px-6 py-3 border-t border-gray-100 bg-gray-50 flex justify-between items-center text-sm" x-show="totalPages > 1" style="display: none;">
                    <button @click="prevPage" :disabled="page === 1" class="text-gray-600 hover:text-eco-primary disabled:opacity-50 font-medium px-3 py-1 rounded-md border border-gray-200 bg-white shadow-sm">&larr; Previous</button>
                    <span class="text-gray-500">Page <span x-text="page" class="font-bold text-gray-800"></span> of <span x-text="totalPages" class="font-bold text-gray-800"></span></span>
                    <button @click="nextPage" :disabled="page === totalPages" class="text-gray-600 hover:text-eco-primary disabled:opacity-50 font-medium px-3 py-1 rounded-md border border-gray-200 bg-white shadow-sm">Next &rarr;</button>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    <!-- Add Hotel Modal -->
    <div x-show="openHotel" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 opacity-50"></div>
            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white shadow-xl rounded-2xl z-10 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Add Hotel</h3>
                    <button @click="openHotel = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add_hotel">
                    <input type="text" name="name" required placeholder="Hotel Name" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    
                    <select x-model="hotelRegion" name="region_id" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                        <option value="">Select Region</option>
                        <?php foreach($data['regions'] as $r): ?>
                            <option value="<?= $r->RegionID ?>"><?= htmlspecialchars($r->RegionName) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="city_id" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary" :disabled="!hotelRegion" required>
                        <option value="">Select City</option>
                        <template x-for="city in allCities.filter(c => c.RegionID === hotelRegion)" :key="city.CityID">
                            <option :value="city.CityID" x-text="city.CityName"></option>
                        </template>
                    </select>

                    <input type="text" name="eco_rating" required placeholder="Eco Rating (e.g. 5 Green Stars)" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-2">Click on the map to set location:</label>
                        <div id="hotelMap" class="w-full h-48 rounded-xl border z-0"></div>
                        <input type="hidden" id="hotel_lat" name="lat" required>
                        <input type="hidden" id="hotel_lng" name="lng" required>
                    </div>

                    <textarea name="description" placeholder="Description" class="w-full px-4 py-2 border rounded-xl outline-none"></textarea>
                    <div class="flex space-x-3">
                        <button type="button" @click="openHotel = false" class="w-1/2 bg-gray-200 text-gray-800 py-2 rounded-xl">Cancel</button>
                        <button type="submit" class="w-1/2 bg-eco-primary text-white py-2 rounded-xl">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Landmark Modal -->
    <div x-show="openLandmark" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 opacity-50"></div>
            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white shadow-xl rounded-2xl z-10 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Add Landmark</h3>
                    <button @click="openLandmark = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add_landmark">
                    <input type="text" name="name" required placeholder="Landmark Name" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    
                    <select x-model="landmarkRegion" name="region_id" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                        <option value="">Select Region</option>
                        <?php foreach($data['regions'] as $r): ?>
                            <option value="<?= $r->RegionID ?>"><?= htmlspecialchars($r->RegionName) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="city_id" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary" :disabled="!landmarkRegion" required>
                        <option value="">Select City</option>
                        <template x-for="city in allCities.filter(c => c.RegionID === landmarkRegion)" :key="city.CityID">
                            <option :value="city.CityID" x-text="city.CityName"></option>
                        </template>
                    </select>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-2">Click on the map to set location:</label>
                        <div id="landmarkMap" class="w-full h-48 rounded-xl border z-0"></div>
                        <input type="hidden" id="landmark_lat" name="lat" required>
                        <input type="hidden" id="landmark_lng" name="lng" required>
                    </div>

                    <textarea name="description" placeholder="Description" class="w-full px-4 py-2 border rounded-xl outline-none"></textarea>
                    <div class="flex space-x-3">
                        <button type="button" @click="openLandmark = false" class="w-1/2 bg-gray-200 text-gray-800 py-2 rounded-xl">Cancel</button>
                        <button type="submit" class="w-1/2 bg-eco-primary text-white py-2 rounded-xl">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Bus Modal -->
    <div x-show="openBus" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 opacity-50"></div>
            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white shadow-xl rounded-2xl z-10 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Add Bus</h3>
                    <button @click="openBus = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="add_bus">
                    <input type="text" name="operator" required placeholder="Bus Operator Name" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    <input type="number" step="any" name="emission_rate" required placeholder="Emission Rate (kg CO2 per km)" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    <div class="flex space-x-3">
                        <button type="button" @click="openBus = false" class="w-1/2 bg-gray-200 text-gray-800 py-2 rounded-xl">Cancel</button>
                        <button type="submit" class="w-1/2 bg-eco-primary text-white py-2 rounded-xl">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Hotel Modal -->
    <div x-show="openEditHotel" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 opacity-50"></div>
            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white shadow-xl rounded-2xl z-10 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Edit Hotel</h3>
                    <button @click="openEditHotel = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="edit_hotel">
                    <input type="hidden" name="hotel_id" x-model="editHotelData.id">
                    <input type="text" name="name" x-model="editHotelData.name" required placeholder="Hotel Name" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">

                    <select x-model="editHotelRegion" name="region_id" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                        <option value="">Select Region</option>
                        <?php foreach($data['regions'] as $r): ?>
                            <option value="<?= $r->RegionID ?>"><?= htmlspecialchars($r->RegionName) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="city_id" x-model="editHotelData.cityId" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary" :disabled="!editHotelRegion" required>
                        <option value="">Select City</option>
                        <template x-for="city in allCities.filter(c => c.RegionID === editHotelRegion)" :key="city.CityID">
                            <option :value="city.CityID" x-text="city.CityName"></option>
                        </template>
                    </select>

                    <input type="text" name="eco_rating" x-model="editHotelData.eco" required placeholder="Eco Rating" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    
                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-2">Click on the map to update location:</label>
                        <div id="editHotelMap" class="w-full h-48 rounded-xl border z-0"></div>
                        <input type="hidden" id="edit_hotel_lat" name="lat" x-model="editHotelData.lat" required>
                        <input type="hidden" id="edit_hotel_lng" name="lng" x-model="editHotelData.lng" required>
                    </div>

                    <textarea name="description" x-model="editHotelData.desc" placeholder="Description" class="w-full px-4 py-2 border rounded-xl outline-none"></textarea>
                    <div class="flex space-x-3">
                        <button type="button" @click="openEditHotel = false" class="w-1/2 bg-gray-200 text-gray-800 py-2 rounded-xl">Cancel</button>
                        <button type="submit" class="w-1/2 bg-eco-primary text-white py-2 rounded-xl">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Landmark Modal -->
    <div x-show="openEditLandmark" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 opacity-50"></div>
            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white shadow-xl rounded-2xl z-10 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Edit Landmark</h3>
                    <button @click="openEditLandmark = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="edit_landmark">
                    <input type="hidden" name="landmark_id" x-model="editLandmarkData.id">
                    <input type="text" name="name" x-model="editLandmarkData.name" required placeholder="Landmark Name" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">

                    <select x-model="editLandmarkRegion" name="region_id" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                        <option value="">Select Region</option>
                        <?php foreach($data['regions'] as $r): ?>
                            <option value="<?= $r->RegionID ?>"><?= htmlspecialchars($r->RegionName) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="city_id" x-model="editLandmarkData.cityId" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary" :disabled="!editLandmarkRegion" required>
                        <option value="">Select City</option>
                        <template x-for="city in allCities.filter(c => c.RegionID === editLandmarkRegion)" :key="city.CityID">
                            <option :value="city.CityID" x-text="city.CityName"></option>
                        </template>
                    </select>

                    <div class="mb-4">
                        <label class="block text-sm text-gray-600 mb-2">Click on the map to update location:</label>
                        <div id="editLandmarkMap" class="w-full h-48 rounded-xl border z-0"></div>
                        <input type="hidden" id="edit_landmark_lat" name="lat" x-model="editLandmarkData.lat" required>
                        <input type="hidden" id="edit_landmark_lng" name="lng" x-model="editLandmarkData.lng" required>
                    </div>

                    <textarea name="description" x-model="editLandmarkData.desc" placeholder="Description" class="w-full px-4 py-2 border rounded-xl outline-none"></textarea>
                    <div class="flex space-x-3">
                        <button type="button" @click="openEditLandmark = false" class="w-1/2 bg-gray-200 text-gray-800 py-2 rounded-xl">Cancel</button>
                        <button type="submit" class="w-1/2 bg-eco-primary text-white py-2 rounded-xl">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Hotel Modal -->
    <div x-show="openImportHotel" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 opacity-50"></div>
            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white shadow-xl rounded-2xl z-10 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Import Hotels (.xlsx)</h3>
                    <button @click="openImportHotel = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="bg-blue-50 text-blue-800 text-sm p-3 rounded-lg mb-4 flex justify-between items-center">
                    <div>
                        <strong>Format Required:</strong><br>
                        Name, City Name, EcoRating, Latitude, Longitude, Description
                    </div>
                    <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" class="m-0">
                        <input type="hidden" name="action" value="template_hotels">
                        <button type="submit" class="bg-white border border-blue-200 text-blue-700 px-3 py-1.5 rounded-lg font-medium hover:bg-blue-100 transition whitespace-nowrap">
                            Download Template
                        </button>
                    </form>
                </div>
                
                <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="import_hotels">
                    <input type="file" name="excel_file" accept=".xlsx" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary bg-gray-50 text-sm">
                    <div class="flex space-x-3 mt-4">
                        <button type="button" @click="openImportHotel = false" class="w-1/2 bg-gray-200 text-gray-800 py-2 rounded-xl">Cancel</button>
                        <button type="submit" class="w-1/2 bg-eco-primary text-white py-2 rounded-xl flex justify-center items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Import Landmark Modal -->
    <div x-show="openImportLandmark" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900 opacity-50"></div>
            <div class="inline-block w-full max-w-md p-6 my-8 text-left bg-white shadow-xl rounded-2xl z-10 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold">Import Landmarks (.xlsx)</h3>
                    <button @click="openImportLandmark = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <div class="bg-blue-50 text-blue-800 text-sm p-3 rounded-lg mb-4 flex justify-between items-center">
                    <div>
                        <strong>Format Required:</strong><br>
                        Name, City Name, Latitude, Longitude, Description
                    </div>
                    <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" class="m-0">
                        <input type="hidden" name="action" value="template_landmarks">
                        <button type="submit" class="bg-white border border-blue-200 text-blue-700 px-3 py-1.5 rounded-lg font-medium hover:bg-blue-100 transition whitespace-nowrap">
                            Download Template
                        </button>
                    </form>
                </div>

                <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="import_landmarks">
                    <input type="file" name="excel_file" accept=".xlsx" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary bg-gray-50 text-sm">
                    <div class="flex space-x-3 mt-4">
                        <button type="button" @click="openImportLandmark = false" class="w-1/2 bg-gray-200 text-gray-800 py-2 rounded-xl">Cancel</button>
                        <button type="submit" class="w-1/2 bg-eco-primary text-white py-2 rounded-xl flex justify-center items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script>
        // Store map instances to properly destroy/recreate them or just update them
        let activeMaps = {};
        
        function setupMap(mapId, latInputId, lngInputId, initialLat = null, initialLng = null) {
            let center = [21.9162, 95.9560]; // Default Myanmar
            let zoom = 5;
            
            if (initialLat && initialLng) {
                center = [parseFloat(initialLat), parseFloat(initialLng)];
                zoom = 13;
            }

            // If map already initialized, just reset view and return
            if (activeMaps[mapId]) {
                activeMaps[mapId].map.setView(center, zoom);
                activeMaps[mapId].map.invalidateSize();
                
                if (activeMaps[mapId].marker) {
                    activeMaps[mapId].map.removeLayer(activeMaps[mapId].marker);
                }
                if (initialLat && initialLng) {
                    activeMaps[mapId].marker = L.marker(center).addTo(activeMaps[mapId].map);
                }
                return;
            }

            let map = L.map(mapId).setView(center, zoom);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);
            
            // Fix modal rendering issues
            setTimeout(() => map.invalidateSize(), 50);

            let marker = null;
            if (initialLat && initialLng) {
                marker = L.marker(center).addTo(map);
            }

            map.on('click', function(e) {
                if (marker) {
                    map.removeLayer(marker);
                }
                marker = L.marker(e.latlng).addTo(map);
                
                // Update hidden inputs
                document.getElementById(latInputId).value = e.latlng.lat;
                document.getElementById(lngInputId).value = e.latlng.lng;
                
                // Trigger input event for AlpineJS x-model if applicable
                document.getElementById(latInputId).dispatchEvent(new Event('input'));
                document.getElementById(lngInputId).dispatchEvent(new Event('input'));
            });

            activeMaps[mapId] = { map: map, marker: marker };
        }

        function tablePagination(perPage = 10) {
            return {
                search: '',
                page: 1,
                perPage: perPage,
                rows: [],
                filteredRows: [],
                totalPages: 1,
                
                initTable(tbodyElement) {
                    this.$nextTick(() => {
                        this.rows = Array.from(tbodyElement.children);
                        this.filterAndPaginate();
                    });
                },
                
                filterAndPaginate() {
                    let s = this.search.toLowerCase();
                    this.filteredRows = this.rows.filter(row => row.innerText.toLowerCase().includes(s));
                    this.totalPages = Math.ceil(this.filteredRows.length / this.perPage) || 1;
                    if (this.page > this.totalPages) this.page = this.totalPages;
                    
                    this.render();
                },
                
                render() {
                    let start = (this.page - 1) * this.perPage;
                    let end = start + this.perPage;
                    
                    this.rows.forEach(row => row.style.display = 'none');
                    this.filteredRows.slice(start, end).forEach(row => row.style.display = '');
                },
                
                nextPage() {
                    if (this.page < this.totalPages) {
                        this.page++;
                        this.render();
                    }
                },
                
                prevPage() {
                    if (this.page > 1) {
                        this.page--;
                        this.render();
                    }
                }
            }
        }
    </script>
</body>
</html>
