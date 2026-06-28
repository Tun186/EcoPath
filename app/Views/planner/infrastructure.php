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
          
          // Visual Seating Layout View State
          openViewSeats: false,
          viewSeatsBusName: '',
          loadingViewSeats: false,
          viewSeats: [],
          viewSeatsBus: null,
          
          showBusSeats(busId, operatorName) {
              this.viewSeatsBusName = operatorName;
              this.openViewSeats = true;
              this.loadingViewSeats = true;
              this.viewSeats = [];
              this.viewSeatsBus = null;
              
              fetch('<?= URLROOT ?>/planner/bus_seats/' + busId)
                  .then(res => res.json())
                  .then(data => {
                      this.viewSeats = data.seats || [];
                      this.viewSeatsBus = data.bus || null;
                      this.loadingViewSeats = false;
                  })
                  .catch(err => {
                      console.error('Error fetching bus seats:', err);
                      this.loadingViewSeats = false;
                  });
          },

          getViewLeftColumns() {
              const layout = this.viewSeatsBus ? this.viewSeatsBus.SeatLayout : '2+2';
              if (layout === '1+1') return [1];
              return [1, 2];
          },
          
          getViewRightColumns() {
              const layout = this.viewSeatsBus ? this.viewSeatsBus.SeatLayout : '2+2';
              if (layout === '1+1') return [2];
              if (layout === '2+1') return [3];
              return [3, 4];
          },

          getViewSeatRows() {
              const rowLetters = new Set();
              this.viewSeats.forEach(s => {
                  if (s.SeatNumber) {
                      const letter = s.SeatNumber.match(/^[A-Z]+/);
                      if (letter) rowLetters.add(letter[0]);
                  }
              });
              return Array.from(rowLetters).sort();
          },
          
          getViewRowSeats(rowLetter, columns) {
              return this.viewSeats.filter(s => {
                  const match = s.SeatNumber.match(/^([A-Z]+)(\d+)$/);
                  if (match) {
                      const letter = match[1];
                      const col = parseInt(match[2]);
                      return letter === rowLetter && columns.includes(col);
                  }
                  return false;
              }).sort((a, b) => a.SeatNumber.localeCompare(b.SeatNumber));
          },
          
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
                        <tr>
                            <th class="px-6 py-3">Bus ID</th>
                            <th class="px-6 py-3">Operator</th>
                            <th class="px-6 py-3">Company</th>
                            <th class="px-6 py-3">Fuel Type</th>
                            <th class="px-6 py-3">HP</th>
                            <th class="px-6 py-3">Layout</th>
                            <th class="px-6 py-3">Total Seats</th>
                            <th class="px-6 py-3 text-center">Emission Rate (kg CO2/km)</th>
                            <th class="px-6 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100" x-ref="tbody" x-init="initTable($el)">
                        <?php foreach($data['buses'] as $b): ?>
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-3 font-mono text-xs text-gray-500 font-semibold"><?= htmlspecialchars($b->BusID) ?></td>
                            <td class="px-6 py-3 font-medium">
                                <div class="flex items-center space-x-3">
                                    <?php if (!empty($b->Image1)): ?>
                                        <img src="<?= URLROOT . '/' . $b->Image1 ?>" class="w-10 h-10 object-cover rounded-lg border shadow-sm" alt="Bus image">
                                    <?php else: ?>
                                        <div class="w-10 h-10 bg-gray-100 border rounded-lg flex items-center justify-center text-gray-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                    <?php endif; ?>
                                    <span><?= htmlspecialchars($b->OperatorName) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-3 font-medium text-gray-700"><?= htmlspecialchars($b->BusCompany ?? 'Unknown') ?></td>
                            <td class="px-6 py-3">
                                <?php 
                                $fuel = strtolower($b->FuelType ?? 'oil');
                                if ($fuel === 'ev'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-800 border border-green-200">EV</span>
                                <?php elseif ($fuel === 'gas'): ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-blue-100 text-blue-800 border border-blue-200">Gas</span>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">Oil</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3 font-semibold text-slate-700"><?= htmlspecialchars($b->HP ?? 0) ?> HP</td>
                            <td class="px-6 py-3 text-gray-600 font-medium">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                    <?= htmlspecialchars($b->SeatLayout ?? '2+2') ?> Lanes
                                </span>
                            </td>
                            <td class="px-6 py-3 font-medium"><?= htmlspecialchars($b->TotalSeats ?? 40) ?> seats</td>
                            <td class="px-6 py-3 text-center font-mono"><?= $b->EmissionRate ?></td>
                            <td class="px-6 py-3 text-right">
                                <button type="button" @click="showBusSeats('<?= $b->BusID ?>', '<?= htmlspecialchars($b->OperatorName, ENT_QUOTES) ?>')" class="text-eco-primary hover:text-eco-dark font-medium transition text-xs flex items-center inline-flex bg-eco-light/50 px-2.5 py-1.5 rounded-lg border border-eco-primary/20">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    View Seats
                                </button>
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
                <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="add_bus">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Actual Bus ID (License Plate / Code)</label>
                        <input type="text" name="custom_bus_id" required placeholder="e.g., CC-0000" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bus Operator Name</label>
                        <input type="text" name="operator" required placeholder="e.g., Scania VIP Express" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    </div>
                    <select name="company" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                        <option value="" disabled selected>Select Bus Company</option>
                        <option value="Scania">Scania</option>
                        <option value="MAN">MAN</option>
                        <option value="Hyundai">Hyundai</option>
                        <option value="Volvo">Volvo</option>
                        <option value="Yutong">Yutong</option>
                        <option value="Other">Other</option>
                    </select>
                    <select name="fuel_type" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                        <option value="" disabled selected>Select Fuel Type</option>
                        <option value="EV">EV (Electric)</option>
                        <option value="Gas">Gas (CNG/LNG)</option>
                        <option value="Oil">Oil (Diesel)</option>
                    </select>
                    <select name="seat_layout" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                        <option value="" disabled selected>Select Seating Layout</option>
                        <option value="2+2">2+2 (Standard - 4 seats/row)</option>
                        <option value="2+1">2+1 (VIP/Luxury - 3 seats/row)</option>
                        <option value="1+1">1+1 (Sleeper - 2 seats/row)</option>
                    </select>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Engine Horsepower (HP)</label>
                        <input type="number" name="hp" required placeholder="e.g., 410" min="1" max="2000" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Passenger Seats</label>
                        <input type="number" name="total_seats" required placeholder="e.g., 40" min="1" max="100" value="40" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bus Image 1 <span class="text-red-500">*</span></label>
                        <input type="file" name="bus_image_1" required accept=".jpg,.jpeg,.png" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bus Image 2 (Optional)</label>
                        <input type="file" name="bus_image_2" accept=".jpg,.jpeg,.png" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bus Image 3 (Optional)</label>
                        <input type="file" name="bus_image_3" accept=".jpg,.jpeg,.png" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary text-sm">
                    </div>
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
    <!-- View Seats Modal -->
    <div x-show="openViewSeats" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="openViewSeats = false"></div>
            
            <div class="inline-block w-full max-w-lg p-6 my-8 text-left bg-white shadow-2xl rounded-3xl z-10 relative border border-gray-100 animate-fade-in">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Bus Seating Chart</h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="viewSeatsBusName"></p>
                    </div>
                    <button @click="openViewSeats = false" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Loading State -->
                <div x-show="loadingViewSeats" class="py-12 flex flex-col justify-center items-center space-y-3">
                    <svg class="animate-spin h-8 w-8 text-eco-primary" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-500 text-sm">Fetching seats layout...</span>
                </div>

                <!-- Seating Section -->
                <div x-show="!loadingViewSeats">
                    <!-- Bus Layout Box -->
                    <div class="bg-gray-50 border border-gray-200/50 rounded-2xl p-6 mb-6">
                        <!-- Bus Front Indicator -->
                        <div class="flex justify-between items-center pb-4 mb-6 border-b border-gray-200 text-xs font-semibold text-gray-400 tracking-wider">
                            <span>FRONT OF BUS</span>
                            <span class="flex items-center text-gray-500 bg-white px-2 py-1 rounded-md shadow-sm border border-gray-100">
                                <span class="w-2 h-2 bg-eco-primary rounded-full mr-1.5 animate-pulse"></span> Driver
                            </span>
                        </div>

                        <!-- Seats Grid -->
                        <div class="grid gap-y-4 max-h-[350px] overflow-y-auto pr-2">
                            <template x-for="rowLetter in getViewSeatRows()" :key="rowLetter">
                                <div class="flex justify-between items-center">
                                    <!-- Left side -->
                                    <div class="flex space-x-2">
                                        <template x-for="seat in getViewRowSeats(rowLetter, getViewLeftColumns())" :key="seat.SeatID">
                                            <div 
                                                class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-xs border shadow-sm select-none"
                                                :class="{
                                                    'bg-red-50 border-red-200 text-red-700': seat.IsBooked == 1,
                                                    'bg-green-50 border-green-200 text-green-700': seat.IsBooked != 1
                                                }"
                                                :title="seat.IsBooked == 1 ? 'Booked' : 'Available'"
                                            >
                                                <span x-text="seat.SeatNumber"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <!-- Aisle -->
                                    <div class="text-[10px] font-bold text-gray-300 tracking-widest uppercase">AISLE</div>

                                    <!-- Right side -->
                                    <div class="flex space-x-2">
                                        <template x-for="seat in getViewRowSeats(rowLetter, getViewRightColumns())" :key="seat.SeatID">
                                            <div 
                                                class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-xs border shadow-sm select-none"
                                                :class="{
                                                    'bg-red-50 border-red-200 text-red-700': seat.IsBooked == 1,
                                                    'bg-green-50 border-green-200 text-green-700': seat.IsBooked != 1
                                                }"
                                                :title="seat.IsBooked == 1 ? 'Booked' : 'Available'"
                                            >
                                                <span x-text="seat.SeatNumber"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="flex justify-between items-center text-sm text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center space-x-6 mx-auto">
                            <span class="flex items-center"><span class="w-3.5 h-3.5 bg-green-50 border border-green-200 rounded-md mr-1.5 shadow-sm"></span> Available</span>
                            <span class="flex items-center"><span class="w-3.5 h-3.5 bg-red-50 border border-red-200 rounded-md mr-1.5 shadow-sm"></span> Booked</span>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button type="button" @click="openViewSeats = false" class="w-full bg-gray-900 hover:bg-gray-800 text-white py-3.5 rounded-xl font-semibold transition text-sm">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
