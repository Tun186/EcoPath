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
                    colors: { 'eco-primary': '#10B981', 'eco-dark': '#064E3B', 'eco-light': '#D1FAE5' },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800" x-data="packageCreator()">

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
                    <a href="<?= URLROOT ?>/planner/packages" class="flex items-center px-4 py-3 bg-eco-light text-eco-dark rounded-xl font-medium transition">
                        <svg class="w-5 h-5 mr-3 text-eco-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Packages
                    </a>
                    <a href="<?= URLROOT ?>/planner/locations" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 rounded-xl font-medium transition">
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
                <h1 class="text-2xl font-bold text-gray-800">Package Logistics</h1>
                <p class="text-sm text-gray-500">Design routes and calculate carbon emissions</p>
            </div>
        </header>

        <div class="p-8 space-y-8 max-w-7xl">
            <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative">
                <span class="block sm:inline">Package successfully designed and saved!</span>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Package Form -->
                <div class="lg:col-span-1 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold mb-4">Design Expedition</h3>
                    
                    <form action="<?= URLROOT ?>/planner/packages" method="POST" class="space-y-4" @submit="onSubmit">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Package Name</label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                                <input type="number" step="any" name="price" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Trees Planted</label>
                                <input type="number" name="trees" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                            </div>
                        </div>

                        <hr class="border-gray-100 my-4">

                        <!-- OSM Data Selectors -->
                        <div x-data="{ hotelSearch: '' }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Include Hotels</label>
                            <input type="text" x-model="hotelSearch" placeholder="Search hotels..." class="w-full px-3 py-2 mb-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-eco-primary/50">
                            <div class="max-h-40 overflow-y-auto border rounded-xl p-3 bg-gray-50 space-y-2" x-show="hotelSearch !== '' || selectedHotels.length > 0" style="display: none;">
                                <?php foreach($data['hotels'] as $h): ?>
                                <label class="flex items-center space-x-2" x-show="(hotelSearch === '' && selectedHotels.includes('<?= $h->HotelID ?>')) || (hotelSearch !== '' && '<?= strtolower(htmlspecialchars(addslashes($h->HotelName))) ?>'.includes(hotelSearch.toLowerCase()))">
                                    <input type="checkbox" name="hotel_ids[]" value="<?= $h->HotelID ?>" 
                                           class="rounded text-eco-primary focus:ring-eco-primary"
                                           data-json='{"lat": <?= $h->Lat ?>, "lng": <?= $h->Lng ?>, "name": "<?= htmlspecialchars(addslashes($h->HotelName)) ?>"}'
                                           @change="updateMap" x-model="selectedHotels">
                                    <span class="text-sm"><?= htmlspecialchars($h->HotelName) ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div x-data="{ landmarkSearch: '' }">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Include Landmarks</label>
                            <input type="text" x-model="landmarkSearch" placeholder="Search landmarks..." class="w-full px-3 py-2 mb-2 border border-gray-200 rounded-lg text-sm outline-none focus:ring-2 focus:ring-eco-primary/50">
                            <div class="max-h-40 overflow-y-auto border rounded-xl p-3 bg-gray-50 space-y-2" x-show="landmarkSearch !== '' || selectedLandmarks.length > 0" style="display: none;">
                                <?php foreach($data['landmarks'] as $l): ?>
                                <label class="flex items-center space-x-2" x-show="(landmarkSearch === '' && selectedLandmarks.includes('<?= $l->LandmarkID ?>')) || (landmarkSearch !== '' && '<?= strtolower(htmlspecialchars(addslashes($l->LandmarkName))) ?>'.includes(landmarkSearch.toLowerCase()))">
                                    <input type="checkbox" name="landmark_ids[]" value="<?= $l->LandmarkID ?>" 
                                           class="rounded text-eco-primary focus:ring-eco-primary"
                                           data-json='{"lat": <?= $l->Lat ?>, "lng": <?= $l->Lng ?>, "name": "<?= htmlspecialchars(addslashes($l->LandmarkName)) ?>"}'
                                           @change="updateMap" x-model="selectedLandmarks">
                                    <span class="text-sm"><?= htmlspecialchars($l->LandmarkName) ?></span>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Transport Vehicle</label>
                            <select name="bus_id" x-model="selectedBus" @change="calculateCarbon" required class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-eco-primary">
                                <option value="">Select transport</option>
                                <?php foreach($data['buses'] as $b): ?>
                                <option value="<?= $b->BusID ?>" data-emission="<?= $b->EmissionRate ?>">
                                    <?= htmlspecialchars($b->OperatorName) ?> (<?= $b->EmissionRate ?> kg/km)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mt-4">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm text-gray-500">Calculated Distance:</span>
                                <span class="font-bold text-gray-800" x-text="distance.toFixed(2) + ' km'">0 km</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Estimated Carbon:</span>
                                <span class="font-bold text-eco-primary" x-text="co2.toFixed(2) + ' kg'">0 kg</span>
                            </div>
                        </div>

                        <!-- Hidden fields to pass to PHP -->
                        <input type="hidden" name="distance" :value="distance.toFixed(2)">
                        <input type="hidden" name="calculated_co2" :value="co2.toFixed(2)">

                        <button type="submit" class="w-full bg-eco-primary hover:bg-eco-dark text-white font-medium py-3 rounded-xl transition">
                            Save Expedition
                        </button>
                    </form>
                </div>

                <!-- OSM Map Display -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden h-full flex flex-col min-h-[500px]">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">OpenStreetMap Route Analysis</h3>
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-lg font-mono">OSRM Active</span>
                        </div>
                        <div id="map" class="flex-1 w-full bg-gray-200 z-0"></div>
                    </div>
                </div>
            </div>
            
            <!-- Existing Packages -->
            <div class="mt-8">
                <h3 class="text-xl font-bold mb-4">Active Packages</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($data['packages'] as $p): ?>
                    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                        <h4 class="font-bold text-lg mb-2"><?= htmlspecialchars($p->PackageName) ?></h4>
                        <div class="flex justify-between text-sm text-gray-500 mb-1">
                            <span>Distance:</span> <span class="font-mono"><?= $p->Distance ?? 0 ?> km</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 mb-1">
                            <span>Hotels:</span> <span class="font-mono truncate ml-2"><?= htmlspecialchars($p->Hotels ?? 'None') ?></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 mb-3">
                            <span>Landmarks:</span> <span class="font-mono truncate ml-2"><?= htmlspecialchars($p->Landmarks ?? 'None') ?></span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 mb-3">
                            <span>CO2 Cost:</span> <span class="font-mono text-red-500 font-medium"><?= $p->CalculatedCO2 ?? 0 ?> kg</span>
                        </div>
                        <div class="bg-eco-light/30 px-3 py-2 rounded-lg flex justify-between text-sm font-medium">
                            <span class="text-eco-dark">Trees Planted:</span>
                            <span class="text-eco-primary"><?= $p->BaseTreeCount ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
        </div>
    </main>

    <script>
        function packageCreator() {
            return {
                map: null,
                markers: [],
                routeLine: null,
                
                selectedHotels: [],
                selectedLandmarks: [],
                selectedBus: '',
                
                distance: 0,
                co2: 0,
                
                init() {
                    // Initialize Leaflet map
                    this.map = L.map('map').setView([21.9162, 95.9560], 6); // Default view (Myanmar)
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap contributors'
                    }).addTo(this.map);
                },
                
                updateMap() {
                    // Clear previous markers and line
                    this.markers.forEach(m => this.map.removeLayer(m));
                    this.markers = [];
                    if (this.routeLine) this.map.removeLayer(this.routeLine);
                    
                    let points = [];
                    let bounds = [];
                    
                    // Collect all checked hotels
                    document.querySelectorAll('input[name="hotel_ids[]"]:checked').forEach(cb => {
                        let data = JSON.parse(cb.getAttribute('data-json'));
                        let m = L.marker([data.lat, data.lng]).addTo(this.map).bindPopup("Hotel: " + data.name);
                        this.markers.push(m);
                        points.push([data.lat, data.lng]);
                        bounds.push([data.lat, data.lng]);
                    });
                    
                    // Collect all checked landmarks
                    document.querySelectorAll('input[name="landmark_ids[]"]:checked').forEach(cb => {
                        let data = JSON.parse(cb.getAttribute('data-json'));
                        let m = L.marker([data.lat, data.lng]).addTo(this.map).bindPopup("Landmark: " + data.name);
                        this.markers.push(m);
                        points.push([data.lat, data.lng]);
                        bounds.push([data.lat, data.lng]);
                    });
                    
                    if (bounds.length > 0) {
                        this.map.fitBounds(bounds, { padding: [50, 50], maxZoom: 14 });
                    }
                    
                    if (points.length > 1) {
                        // Draw polyline connecting all points
                        this.routeLine = L.polyline(points, {color: 'blue', weight: 4, opacity: 0.6, dashArray: '10, 10'}).addTo(this.map);
                        
                        // Calculate total Haversine distance
                        let totalDist = 0;
                        for(let i = 0; i < points.length - 1; i++) {
                            totalDist += this.calculateHaversine(points[i][0], points[i][1], points[i+1][0], points[i+1][1]);
                        }
                        this.distance = totalDist;
                        this.calculateCarbon();
                    } else {
                        this.distance = 0;
                        this.co2 = 0;
                    }
                },
                
                calculateHaversine(lat1, lon1, lat2, lon2) {
                    const R = 6371; // Earth radius in km
                    const dLat = (lat2 - lat1) * Math.PI / 180;
                    const dLon = (lon2 - lon1) * Math.PI / 180;
                    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                              Math.sin(dLon/2) * Math.sin(dLon/2);
                    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                    return R * c; // Distance in km
                },
                
                calculateCarbon() {
                    if (this.distance > 0 && this.selectedBus) {
                        // Get emission rate from the selected bus option
                        let select = document.querySelector('select[name="bus_id"]');
                        let option = select.options[select.selectedIndex];
                        let emissionRate = parseFloat(option.getAttribute('data-emission'));
                        
                        this.co2 = this.distance * emissionRate;
                    } else {
                        this.co2 = 0;
                    }
                },
                
                onSubmit(e) {
                    if (this.distance === 0 || this.selectedHotels.length === 0 || this.selectedLandmarks.length === 0) {
                        e.preventDefault();
                        alert('Please select at least one Hotel and one Landmark to form a valid route!');
                    }
                }
            }
        }
    </script>
</body>
</html>
