<?php
// Calculate statistics from the list of drivers
$driversList = $data['drivers'] ?? [];
$totalDrivers = count($driversList);
$activeDrivers = 0;
$expiredDrivers = 0;
$expiringSoonDrivers = 0;
$bloodGroupCounts = [];
$classCounts = [];

$today = time();
$thirtyDaysLater = strtotime('+30 days');

foreach ($driversList as $drv) {
    if (!empty($drv->LicenseExpDate)) {
        $expTime = strtotime($drv->LicenseExpDate);
        if ($expTime < $today) {
            $expiredDrivers++;
        } else {
            $activeDrivers++;
            if ($expTime <= $thirtyDaysLater) {
                $expiringSoonDrivers++;
            }
        }
    } else {
        $activeDrivers++; // Default
    }

    if (!empty($drv->BloodType)) {
        $blood = strtoupper(trim($drv->BloodType));
        $bloodGroupCounts[$blood] = ($bloodGroupCounts[$blood] ?? 0) + 1;
    }

    if (!empty($drv->LicenseClass)) {
        $lclass = strtoupper(trim($drv->LicenseClass));
        $classCounts[$lclass] = ($classCounts[$lclass] ?? 0) + 1;
    }
}
?>
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
    <style>
        /* Scanning Laser Line Animation */
        @keyframes scan {
            0% { top: 0%; opacity: 0.8; }
            50% { top: 100%; opacity: 0.8; }
            100% { top: 0%; opacity: 0.8; }
        }
        
        .laser-line {
            height: 3px;
            background: linear-gradient(90deg, transparent, #10B981, #34D399, #10B981, transparent);
            box-shadow: 0 0 10px #10B981, 0 0 20px #34D399;
            animation: scan 2s infinite linear;
        }

        /* Pulse glow for AI status highlights */
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 0 0px rgba(16, 185, 129, 0.4); }
            50% { box-shadow: 0 0 0 8px rgba(16, 185, 129, 0); }
        }

        .ai-pulse {
            animation: pulseGlow 1.5s infinite;
        }

        .transition-glow {
            transition: all 0.5s ease-in-out;
        }

        /* Hide Scrollbar */
        .scroll-hidden::-webkit-scrollbar {
            display: none;
        }
        .scroll-hidden {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
</head>
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800" x-data="driverApp()">

    <?php
    $activePage = 'drivers';
    require APPROOT . '/app/Views/admin/inc/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative z-10 overflow-y-auto max-h-screen">
        <?php
        $headerTitle = 'Manage Drivers';
        $headerSubtitle = 'View and update system vehicle operators';
        $headerAction = '
            <button @click="resetAddDriver()" class="bg-eco-primary hover:bg-eco-dark text-white px-5 py-2.5 rounded-xl font-medium transition shadow-sm flex items-center text-xs">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Add Driver
            </button>
        ';
        require APPROOT . '/app/Views/admin/inc/header.php';
        ?>

        <!-- Page Content Wrapper -->
        <div class="p-8 space-y-6 max-w-7xl w-full mx-auto">
            
            <?php if(isset($_GET['success'])): ?>
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3.5 rounded-xl flex items-center" role="alert">
                    <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span class="font-medium text-sm">Action completed successfully!</span>
                </div>
            <?php endif; ?>
            <?php if(isset($_GET['error']) && $_GET['error'] == 'linked'): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3.5 rounded-xl flex items-center" role="alert">
                    <svg class="w-5 h-5 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span class="font-medium text-sm">Cannot delete driver because they are currently linked to a active bus assignment.</span>
                </div>
            <?php endif; ?>

            <!-- Redesigned Statistics Row -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
                <!-- Stat Card 1 -->
                <div class="bg-white rounded-2xl p-5 border border-gray-150 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Fleet Drivers</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $totalDrivers ?></h3>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <!-- Stat Card 2 -->
                <div class="bg-white rounded-2xl p-5 border border-gray-150 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Active Licences</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $activeDrivers ?></h3>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <!-- Stat Card 3 -->
                <div class="bg-white rounded-2xl p-5 border border-gray-150 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Expired Licenses</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1"><?= $expiredDrivers ?></h3>
                    </div>
                    <div class="p-3 rounded-xl bg-red-50 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                </div>
                <!-- Stat Card 4 -->
                <div class="bg-white rounded-2xl p-5 border border-gray-150 shadow-sm flex items-center justify-between hover:shadow-md transition">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Classes Registered</p>
                        <div class="flex items-center space-x-1.5 mt-1.5">
                            <?php if (empty($classCounts)): ?>
                                <span class="text-gray-500 text-xs">None</span>
                            <?php else: ?>
                                <?php 
                                $limit = 0; 
                                foreach($classCounts as $cls => $count): 
                                    if ($limit++ >= 3) break;
                                ?>
                                    <span class="px-2 py-0.5 bg-purple-50 text-purple-700 text-xxs font-bold rounded border border-purple-100" title="<?= $count ?> driver(s)">
                                        Class <?= htmlspecialchars($cls) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="p-3 rounded-xl bg-purple-50 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Controller / Filter / Layout switcher row -->
            <div class="bg-white rounded-2xl border border-gray-150 p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Search Box -->
                <div class="relative max-w-md w-full">
                    <input type="text" x-model="searchQuery" placeholder="Search driver by name, license, NRC, address..."
                           class="w-full pl-10 pr-4 py-2 bg-gray-50/50 hover:bg-gray-50 focus:bg-white border border-gray-200 focus:border-eco-primary rounded-xl outline-none transition text-sm">
                    <div class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <!-- View Switcher -->
                <div class="flex items-center bg-gray-100/80 rounded-xl p-1 self-end md:self-auto">
                    <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                            class="px-4 py-1.5 rounded-lg text-xs font-semibold flex items-center transition">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        List View
                    </button>
                    <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-800'"
                            class="px-4 py-1.5 rounded-lg text-xs font-semibold flex items-center transition">
                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        Grid Cards
                    </button>
                </div>
            </div>

            <!-- View Layouts -->
            <div class="w-full mt-6">
                <!-- 1. Table View -->
                <div x-show="viewMode === 'list'" class="bg-white rounded-2xl shadow-sm border border-gray-150 overflow-hidden" x-transition>
                    <div class="overflow-x-auto scroll-hidden">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-100 text-gray-400 text-[11px] uppercase tracking-wider">
                                    <th class="px-6 py-4 font-bold">Driver Info</th>
                                    <th class="px-6 py-4 font-bold">Personal Credentials</th>
                                    <th class="px-6 py-4 font-bold">License Information</th>
                                    <th class="px-6 py-4 font-bold">License Scans</th>
                                    <th class="px-6 py-4 font-bold text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                <!-- Empty state -->
                                <template x-if="filteredDrivers.length === 0">
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">No operators matching query found.</td>
                                    </tr>
                                </template>

                                <!-- Table Rows -->
                                <template x-for="driver in filteredDrivers" :key="driver.DriverID">
                                    <tr class="hover:bg-gray-50/60 transition duration-150">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <template x-if="driver.ProfileImage">
                                                    <img :src="'<?= URLROOT ?>/' + driver.ProfileImage" @click="openLightbox('<?= URLROOT ?>/' + driver.ProfileImage, driver.DriverName + ' Profile')"
                                                         class="w-10 h-10 rounded-full object-cover mr-3 border border-gray-200 shadow-sm cursor-pointer hover:opacity-90 transition">
                                                </template>
                                                <template x-if="!driver.ProfileImage">
                                                    <div class="w-10 h-10 rounded-full bg-eco-light text-eco-dark flex items-center justify-center font-bold mr-3 border border-eco-primary/20">
                                                        <span x-text="driver.DriverName.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                </template>
                                                <div>
                                                    <div class="font-semibold text-gray-800 flex items-center">
                                                        <span x-text="driver.DriverName"></span>
                                                        <template x-if="driver.BloodType">
                                                            <span class="ml-2 px-2 py-0.5 bg-red-50 text-red-600 rounded-md text-[9px] font-bold border border-red-100" x-text="'🩸 ' + driver.BloodType"></span>
                                                        </template>
                                                    </div>
                                                    <div class="text-xs text-gray-400 mt-0.5" x-text="driver.DriverID"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs space-y-1">
                                                <div><span class="text-gray-400 font-medium">NRC:</span> <span class="text-gray-700 font-medium" x-text="driver.NRC || 'N/A'"></span></div>
                                                <div><span class="text-gray-400 font-medium">DOB:</span> <span class="text-gray-700 font-medium" x-text="driver.DateOfBirth || 'N/A'"></span></div>
                                                <template x-if="driver.Address">
                                                    <div class="max-w-xs truncate text-gray-500" :title="driver.Address">
                                                        <span class="text-gray-400 font-medium">Add:</span> <span x-text="driver.Address"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-xs space-y-1.5">
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded font-mono text-[10px] font-bold border border-gray-200" x-text="driver.LicenseCode"></span>
                                                    <template x-if="driver.LicenseClass">
                                                        <span class="px-1.5 py-0.5 bg-blue-50 text-blue-700 rounded text-[9px] font-bold border border-blue-100" x-text="'Class ' + driver.LicenseClass"></span>
                                                    </template>
                                                </div>
                                                <div class="text-[10px] text-gray-400">
                                                    <span x-text="'Issued: ' + (driver.LicenseIssueYear || 'N/A')"></span>
                                                    <span class="mx-1">•</span>
                                                    <span x-text="'Exp: ' + (driver.LicenseExpDate || 'N/A')"></span>
                                                </div>
                                                
                                                <template x-if="driver.LicenseExpDate">
                                                    <div>
                                                        <!-- Expired status badge -->
                                                        <template x-if="new Date(driver.LicenseExpDate) < new Date()">
                                                            <span class="inline-flex items-center px-2 py-0.5 bg-red-50 text-red-700 rounded-full text-[9px] font-semibold border border-red-100">
                                                                <span class="w-1 h-1 mr-1 bg-red-500 rounded-full"></span>
                                                                Expired
                                                            </span>
                                                        </template>
                                                        <!-- Active status badge -->
                                                        <template x-if="new Date(driver.LicenseExpDate) >= new Date()">
                                                            <span class="inline-flex items-center px-2 py-0.5 bg-green-50 text-green-700 rounded-full text-[9px] font-semibold border border-green-150">
                                                                <span class="w-1 h-1 mr-1 bg-emerald-500 rounded-full"></span>
                                                                Active
                                                            </span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2.5">
                                                <template x-if="driver.LicenseFrontImage">
                                                    <button type="button" @click="openLightbox('<?= URLROOT ?>/' + driver.LicenseFrontImage, driver.DriverName + ' Front')" 
                                                            class="relative group rounded-lg overflow-hidden border border-gray-200">
                                                        <img :src="'<?= URLROOT ?>/' + driver.LicenseFrontImage" class="w-12 h-8 object-cover group-hover:scale-105 transition duration-150">
                                                        <span class="absolute inset-0 bg-black/40 text-white text-[8px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">Front</span>
                                                    </button>
                                                </template>
                                                <template x-if="!driver.LicenseFrontImage">
                                                    <span class="text-xxs text-gray-400 italic">No Front</span>
                                                </template>

                                                <template x-if="driver.LicenseBackImage">
                                                    <button type="button" @click="openLightbox('<?= URLROOT ?>/' + driver.LicenseBackImage, driver.DriverName + ' Back')" 
                                                            class="relative group rounded-lg overflow-hidden border border-gray-200">
                                                        <img :src="'<?= URLROOT ?>/' + driver.LicenseBackImage" class="w-12 h-8 object-cover group-hover:scale-105 transition duration-150">
                                                        <span class="absolute inset-0 bg-black/40 text-white text-[8px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">Back</span>
                                                    </button>
                                                </template>
                                                <template x-if="!driver.LicenseBackImage">
                                                    <span class="text-xxs text-gray-400 italic">No Back</span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right space-x-1">
                                            <button @click="openEditModal(driver)" class="text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition">Edit</button>
                                            
                                            <form :action="'<?= URLROOT ?>/admin/drivers'" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="DriverID" :value="driver.DriverID">
                                                <button type="submit" class="text-red-600 hover:text-red-800 hover:bg-red-50 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Grid Cards View -->
                <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-3 gap-6" x-transition>
                    <!-- Empty state -->
                    <template x-if="filteredDrivers.length === 0">
                        <div class="col-span-3 bg-white border border-gray-150 rounded-2xl p-12 text-center text-gray-400 italic shadow-sm">
                            No operators matching query found.
                        </div>
                    </template>

                    <!-- Driver Cards Loop -->
                    <template x-for="driver in filteredDrivers" :key="driver.DriverID">
                        <div class="bg-white rounded-2xl border border-gray-150 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col justify-between">
                            <!-- Header Info -->
                            <div class="p-5 flex items-start justify-between border-b border-gray-50 bg-gray-50/20">
                                <div class="flex items-center space-x-3.5">
                                    <template x-if="driver.ProfileImage">
                                        <img :src="'<?= URLROOT ?>/' + driver.ProfileImage" @click="openLightbox('<?= URLROOT ?>/' + driver.ProfileImage, driver.DriverName + ' Profile')"
                                             class="w-12 h-12 rounded-full object-cover border border-gray-200 shadow-sm cursor-pointer hover:opacity-90 transition">
                                    </template>
                                    <template x-if="!driver.ProfileImage">
                                        <div class="w-12 h-12 rounded-full bg-eco-light text-eco-dark flex items-center justify-center font-bold border border-eco-primary/20">
                                            <span x-text="driver.DriverName.charAt(0).toUpperCase()"></span>
                                        </div>
                                    </template>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-sm flex items-center">
                                            <span x-text="driver.DriverName"></span>
                                            <template x-if="driver.BloodType">
                                                <span class="ml-2 px-1.5 py-0.5 bg-red-50 text-red-600 rounded text-[8px] font-bold border border-red-100" x-text="'🩸 ' + driver.BloodType"></span>
                                            </template>
                                        </h4>
                                        <p class="text-xxs text-gray-400 mt-0.5" x-text="driver.DriverID"></p>
                                    </div>
                                </div>
                                <div>
                                    <template x-if="driver.LicenseExpDate">
                                        <div>
                                            <!-- Expired -->
                                            <template x-if="new Date(driver.LicenseExpDate) < new Date()">
                                                <span class="inline-flex items-center px-2 py-0.5 bg-red-50 text-red-700 rounded-full text-[9px] font-semibold border border-red-100">
                                                    <span class="w-1 h-1 mr-1 bg-red-500 rounded-full"></span>
                                                    Expired
                                                </span>
                                            </template>
                                            <!-- Active -->
                                            <template x-if="new Date(driver.LicenseExpDate) >= new Date()">
                                                <span class="inline-flex items-center px-2 py-0.5 bg-green-50 text-green-700 rounded-full text-[9px] font-semibold border border-green-150">
                                                    <span class="w-1 h-1 mr-1 bg-emerald-500 rounded-full"></span>
                                                    Active
                                                </span>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Details -->
                            <div class="p-5 space-y-3.5 flex-1">
                                <div class="grid grid-cols-2 gap-3 text-xxs">
                                    <div>
                                        <span class="text-gray-400 block mb-0.5 uppercase tracking-wider font-semibold">NRC Number</span>
                                        <span class="text-gray-700 font-medium" x-text="driver.NRC || 'N/A'"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5 uppercase tracking-wider font-semibold">Date of Birth</span>
                                        <span class="text-gray-700 font-medium" x-text="driver.DateOfBirth || 'N/A'"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5 uppercase tracking-wider font-semibold">License Code</span>
                                        <span class="px-1.5 py-0.5 bg-gray-100 text-gray-700 font-mono font-bold border border-gray-200 inline-block rounded" x-text="driver.LicenseCode"></span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 block mb-0.5 uppercase tracking-wider font-semibold">Class / Issued</span>
                                        <span class="text-gray-700 font-medium">
                                            <span class="text-blue-600 font-bold" x-text="driver.LicenseClass ? 'Class ' + driver.LicenseClass : 'N/A'"></span>
                                            <span x-text="driver.LicenseIssueYear ? ' (' + driver.LicenseIssueYear + ')' : ''"></span>
                                        </span>
                                    </div>
                                    <div class="col-span-2">
                                        <span class="text-gray-400 block mb-0.5 uppercase tracking-wider font-semibold">Expiry Date</span>
                                        <span class="text-gray-700 font-medium" x-text="driver.LicenseExpDate || 'N/A'"></span>
                                    </div>
                                    <template x-if="driver.Address">
                                        <div class="col-span-2 bg-gray-50/50 p-2.5 rounded-xl border border-gray-100">
                                            <span class="text-gray-400 block mb-0.5 text-[9px] uppercase tracking-wider font-bold">Residential Address</span>
                                            <span class="text-gray-600 leading-tight block" x-text="driver.Address"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Scans preview inside Card -->
                                <div class="pt-3.5 flex items-center justify-between border-t border-gray-100">
                                    <span class="text-xs text-gray-400 font-medium">License Scan Docs:</span>
                                    <div class="flex space-x-2">
                                        <template x-if="driver.LicenseFrontImage">
                                            <button type="button" @click="openLightbox('<?= URLROOT ?>/' + driver.LicenseFrontImage, driver.DriverName + ' Front')" 
                                                    class="relative group rounded overflow-hidden border border-gray-200">
                                                <img :src="'<?= URLROOT ?>/' + driver.LicenseFrontImage" class="w-10 h-7 object-cover group-hover:scale-105 transition duration-150">
                                                <span class="absolute inset-0 bg-black/40 text-white text-[8px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">Front</span>
                                            </button>
                                        </template>
                                        <template x-if="!driver.LicenseFrontImage">
                                            <span class="text-xxs text-gray-400 italic">No Front</span>
                                        </template>

                                        <template x-if="driver.LicenseBackImage">
                                            <button type="button" @click="openLightbox('<?= URLROOT ?>/' + driver.LicenseBackImage, driver.DriverName + ' Back')" 
                                                    class="relative group rounded overflow-hidden border border-gray-200">
                                                <img :src="'<?= URLROOT ?>/' + driver.LicenseBackImage" class="w-10 h-7 object-cover group-hover:scale-105 transition duration-150">
                                                <span class="absolute inset-0 bg-black/40 text-white text-[8px] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">Back</span>
                                            </button>
                                        </template>
                                        <template x-if="!driver.LicenseBackImage">
                                            <span class="text-xxs text-gray-400 italic">No Back</span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/20 flex justify-end space-x-2">
                                <button @click="openEditModal(driver)" class="px-3 py-1.5 text-xs text-blue-600 hover:text-blue-800 hover:bg-blue-50 font-semibold rounded-lg transition">Edit Details</button>
                                
                                <form :action="'<?= URLROOT ?>/admin/drivers'" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this driver?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="DriverID" :value="driver.DriverID">
                                    <button type="submit" class="px-3 py-1.5 text-xs text-red-600 hover:text-red-800 hover:bg-red-50 font-semibold rounded-lg transition">Delete</button>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </main>

    <!-- Add Driver Modal Wizard -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showAddModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Add New Operator</h3>
                    <p class="text-xxs text-gray-400 mt-0.5">Follow the steps to scan and save a driver</p>
                </div>
                <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Step Indicators -->
            <div class="px-6 pt-5">
                <div class="flex items-center justify-center space-x-2 border-b border-gray-100 pb-3.5">
                    <div class="flex items-center">
                        <div class="w-6.5 h-6.5 rounded-full flex items-center justify-center text-[10px] font-bold transition"
                             :class="addStep >= 1 ? 'bg-eco-primary text-white' : 'bg-gray-100 text-gray-400'">1</div>
                        <span class="ml-1.5 text-xxs font-semibold uppercase tracking-wider" :class="addStep === 1 ? 'text-gray-800' : 'text-gray-400'">AI OCR Scan</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <div class="flex items-center">
                        <div class="w-6.5 h-6.5 rounded-full flex items-center justify-center text-[10px] font-bold transition"
                             :class="addStep >= 2 ? 'bg-eco-primary text-white' : 'bg-gray-100 text-gray-400'">2</div>
                        <span class="ml-1.5 text-xxs font-semibold uppercase tracking-wider" :class="addStep === 2 ? 'text-gray-800' : 'text-gray-400'">Credentials</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <div class="flex items-center">
                        <div class="w-6.5 h-6.5 rounded-full flex items-center justify-center text-[10px] font-bold transition"
                             :class="addStep >= 3 ? 'bg-eco-primary text-white' : 'bg-gray-100 text-gray-400'">3</div>
                        <span class="ml-1.5 text-xxs font-semibold uppercase tracking-wider" :class="addStep === 3 ? 'text-gray-800' : 'text-gray-400'">Address</span>
                    </div>
                </div>
            </div>

            <!-- Form wrapped inside modal -->
            <form action="<?= URLROOT ?>/admin/drivers" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col justify-between overflow-y-auto">
                <input type="hidden" name="action" value="create">
                
                <div class="p-6 space-y-5 flex-1 overflow-y-auto">
                    <!-- STEP 1: LICENSE SCANS (OCR) -->
                    <div x-show="addStep === 1" class="space-y-4" x-transition>
                        <p class="text-xs text-gray-500 leading-relaxed text-center bg-emerald-50/30 p-2.5 rounded-xl border border-emerald-100/35">
                            ✨ <strong>EcoPath OCR Assist:</strong> Upload Front & Back license scans. Our Gemini AI engine will parse the information and auto-fill the forms in the next steps automatically.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Front Image drag/drop -->
                            <div>
                                <label class="block text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">License Front Image</label>
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-eco-primary transition duration-200 bg-white min-h-[110px] flex items-center justify-center overflow-hidden"
                                     :class="frontOcrLoading ? 'border-eco-primary bg-eco-light/5' : ''">
                                    <input type="file" name="LicenseFrontImage" accept="image/*" 
                                           @change="handleOcrUpload($event, 'front', 'add')" 
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                    <div x-show="frontOcrLoading" class="absolute inset-x-0 laser-line z-10"></div>
                                    <div class="space-y-1 z-0 relative">
                                        <div x-show="!frontOcrLoading && !frontOcrSuccess" class="flex flex-col items-center">
                                            <svg class="mx-auto h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <p class="text-[10px] text-gray-500 mt-1"><span class="text-eco-primary font-semibold">Upload Front Scan</span></p>
                                        </div>
                                        <div x-show="frontOcrLoading" class="flex flex-col items-center py-1">
                                            <div class="relative w-7 h-7 flex items-center justify-center">
                                                <div class="absolute inset-0 rounded-full border-2 border-eco-primary/20"></div>
                                                <div class="absolute inset-0 rounded-full border-2 border-eco-primary border-t-transparent animate-spin"></div>
                                            </div>
                                            <p class="text-[10px] text-eco-primary font-medium mt-1 animate-pulse">AI OCR Scanning...</p>
                                        </div>
                                        <div x-show="frontOcrSuccess" class="flex flex-col items-center">
                                            <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100 ai-pulse mb-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <p class="text-[10px] text-emerald-600 font-semibold">Front Auto-Filled!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Back Image drag/drop -->
                            <div>
                                <label class="block text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">License Back Image</label>
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-eco-primary transition duration-200 bg-white min-h-[110px] flex items-center justify-center overflow-hidden"
                                     :class="backOcrLoading ? 'border-eco-primary bg-eco-light/5' : ''">
                                    <input type="file" name="LicenseBackImage" accept="image/*" 
                                           @change="handleOcrUpload($event, 'back', 'add')" 
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                    <div x-show="backOcrLoading" class="absolute inset-x-0 laser-line z-10"></div>
                                    <div class="space-y-1 z-0 relative">
                                        <div x-show="!backOcrLoading && !backOcrSuccess" class="flex flex-col items-center">
                                            <svg class="mx-auto h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <p class="text-[10px] text-gray-500 mt-1"><span class="text-eco-primary font-semibold">Upload Back Scan</span></p>
                                        </div>
                                        <div x-show="backOcrLoading" class="flex flex-col items-center py-1">
                                            <div class="relative w-7 h-7 flex items-center justify-center">
                                                <div class="absolute inset-0 rounded-full border-2 border-eco-primary/20"></div>
                                                <div class="absolute inset-0 rounded-full border-2 border-eco-primary border-t-transparent animate-spin"></div>
                                            </div>
                                            <p class="text-[10px] text-eco-primary font-medium mt-1 animate-pulse">AI OCR Scanning...</p>
                                        </div>
                                        <div x-show="backOcrSuccess" class="flex flex-col items-center">
                                            <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100 ai-pulse mb-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <p class="text-[10px] text-emerald-600 font-semibold">Back Auto-Filled!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: CREDENTIALS & INFO -->
                    <div x-show="addStep === 2" class="grid grid-cols-2 gap-4" x-transition>
                        <!-- Name -->
                        <div class="col-span-2">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Driver Name</label>
                                <span x-show="frontHighlighted && addDriver.DriverName" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Filled
                                </span>
                            </div>
                            <input type="text" name="DriverName" x-model="addDriver.DriverName" required 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="frontHighlighted && addDriver.DriverName ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- License Code -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">License Code</label>
                                <span x-show="frontHighlighted && addDriver.LicenseCode" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Filled
                                </span>
                            </div>
                            <input type="text" name="LicenseCode" x-model="addDriver.LicenseCode" required 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="frontHighlighted && addDriver.LicenseCode ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- License Class -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">License Class</label>
                                <span x-show="backHighlighted && addDriver.LicenseClass" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Filled
                                </span>
                            </div>
                            <input type="text" name="LicenseClass" x-model="addDriver.LicenseClass" placeholder="e.g. B" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="backHighlighted && addDriver.LicenseClass ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Date of Birth</label>
                                <span x-show="frontHighlighted && addDriver.DateOfBirth" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Filled
                                </span>
                            </div>
                            <input type="date" name="DateOfBirth" x-model="addDriver.DateOfBirth" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm text-gray-700"
                                   :class="frontHighlighted && addDriver.DateOfBirth ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- NRC -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">NRC Number</label>
                                <span x-show="frontHighlighted && addDriver.NRC" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Filled
                                </span>
                            </div>
                            <input type="text" name="NRC" x-model="addDriver.NRC" placeholder="e.g. 12/KAMATA(N)091837" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="frontHighlighted && addDriver.NRC ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- Blood Type -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Blood Type</label>
                                <span x-show="frontHighlighted && addDriver.BloodType" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Filled
                                </span>
                            </div>
                            <input type="text" name="BloodType" x-model="addDriver.BloodType" placeholder="e.g. O, AB" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="frontHighlighted && addDriver.BloodType ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- Issued Year -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Issued Year</label>
                                <span x-show="backHighlighted && addDriver.LicenseIssueYear" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Filled
                                </span>
                            </div>
                            <input type="text" name="LicenseIssueYear" x-model="addDriver.LicenseIssueYear" placeholder="e.g. 2025" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="backHighlighted && addDriver.LicenseIssueYear ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- Expire Date -->
                        <div class="col-span-2">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Expiry Valid Until</label>
                                <span x-show="frontHighlighted && addDriver.LicenseExpDate" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Filled
                                </span>
                            </div>
                            <input type="date" name="LicenseExpDate" x-model="addDriver.LicenseExpDate" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm text-gray-700"
                                   :class="frontHighlighted && addDriver.LicenseExpDate ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>
                    </div>

                    <!-- STEP 3: RESIDENTIAL ADDRESS & PROFILE -->
                    <div x-show="addStep === 3" class="space-y-4" x-transition>
                        <!-- Profile Image Preview and Input -->
                        <div class="flex items-center space-x-4 p-3.5 bg-gray-50/50 rounded-2xl border border-gray-100">
                            <div class="relative">
                                <template x-if="addProfilePreview">
                                    <img :src="addProfilePreview" class="w-16 h-16 rounded-full object-cover border-2 border-eco-primary shadow-md">
                                </template>
                                <template x-if="!addProfilePreview">
                                    <div class="w-16 h-16 rounded-full bg-gray-150 text-gray-400 flex items-center justify-center font-bold border border-gray-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                </template>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Driver Profile Picture</label>
                                <input type="file" name="ProfileImage" accept="image/*" @change="handleProfilePreview($event, 'add')"
                                       class="w-full text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-eco-light file:text-eco-dark hover:file:bg-eco-primary hover:file:text-white file:transition cursor-pointer">
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Residential Address</label>
                                <span x-show="backHighlighted && addDriver.Address" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Filled
                                </span>
                            </div>
                            <textarea name="Address" x-model="addDriver.Address" rows="3" placeholder="Enter residential street address, city, township..." 
                                      class="w-full px-4 py-2.5 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                      :class="backHighlighted && addDriver.Address ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Buttons -->
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div>
                        <!-- Back button -->
                        <button type="button" x-show="addStep > 1" @click="addStep--" 
                                class="px-4 py-2 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-100 text-xs font-semibold transition">Back</button>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 text-gray-500 rounded-xl hover:bg-gray-100 text-xs font-semibold transition">Cancel</button>
                        
                        <!-- Next button -->
                        <button type="button" x-show="addStep < 3" @click="addStep++" 
                                class="bg-eco-primary hover:bg-eco-dark text-white px-5 py-2 rounded-xl text-xs font-bold transition shadow-sm">Next Step</button>
                        
                        <!-- Submit button -->
                        <button type="submit" x-show="addStep === 3" 
                                class="bg-eco-primary hover:bg-eco-dark text-white px-6 py-2 rounded-xl text-xs font-bold transition shadow-sm">Save Driver</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Driver Modal Wizard -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;" x-transition>
        <div @click.away="showEditModal = false" class="bg-white rounded-2xl shadow-xl w-full max-w-xl overflow-hidden flex flex-col max-h-[90vh]">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div>
                    <h3 class="text-base font-bold text-gray-800">Edit Operator Details</h3>
                    <p class="text-xxs text-gray-400 mt-0.5">Modify information for driver record</p>
                </div>
                <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <!-- Step Indicators -->
            <div class="px-6 pt-5">
                <div class="flex items-center justify-center space-x-2 border-b border-gray-100 pb-3.5">
                    <div class="flex items-center">
                        <div class="w-6.5 h-6.5 rounded-full flex items-center justify-center text-[10px] font-bold transition"
                             :class="editStep >= 1 ? 'bg-eco-primary text-white' : 'bg-gray-100 text-gray-400'">1</div>
                        <span class="ml-1.5 text-xxs font-semibold uppercase tracking-wider" :class="editStep === 1 ? 'text-gray-800' : 'text-gray-400'">Scans</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <div class="flex items-center">
                        <div class="w-6.5 h-6.5 rounded-full flex items-center justify-center text-[10px] font-bold transition"
                             :class="editStep >= 2 ? 'bg-eco-primary text-white' : 'bg-gray-100 text-gray-400'">2</div>
                        <span class="ml-1.5 text-xxs font-semibold uppercase tracking-wider" :class="editStep === 2 ? 'text-gray-800' : 'text-gray-400'">Credentials</span>
                    </div>
                    <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    <div class="flex items-center">
                        <div class="w-6.5 h-6.5 rounded-full flex items-center justify-center text-[10px] font-bold transition"
                             :class="editStep >= 3 ? 'bg-eco-primary text-white' : 'bg-gray-100 text-gray-400'">3</div>
                        <span class="ml-1.5 text-xxs font-semibold uppercase tracking-wider" :class="editStep === 3 ? 'text-gray-800' : 'text-gray-400'">Address</span>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form action="<?= URLROOT ?>/admin/drivers" method="POST" enctype="multipart/form-data" class="flex-1 flex flex-col justify-between overflow-y-auto">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="DriverID" x-model="editDriver.DriverID">
                
                <div class="p-6 space-y-5 flex-1 overflow-y-auto">
                    <!-- STEP 1: LICENSE SCANS (OCR) -->
                    <div x-show="editStep === 1" class="space-y-4" x-transition>
                        <p class="text-xs text-gray-500 leading-relaxed text-center bg-blue-50/20 p-2.5 rounded-xl border border-blue-100/30">
                            Upload new scans to overwrite existing front/back document images. Doing so triggers AI re-extraction to auto-update form fields.
                        </p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Front Image uploader -->
                            <div>
                                <label class="block text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">License Front Image</label>
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-eco-primary transition duration-200 bg-white min-h-[110px] flex items-center justify-center overflow-hidden"
                                     :class="frontOcrLoading ? 'border-eco-primary bg-eco-light/5' : ''">
                                    <input type="file" name="LicenseFrontImage" accept="image/*" 
                                           @change="handleOcrUpload($event, 'front', 'edit')" 
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                    <div x-show="frontOcrLoading" class="absolute inset-x-0 laser-line z-10"></div>
                                    <div class="space-y-1 z-0 relative w-full">
                                        <!-- Has current scan but no new scan -->
                                        <template x-if="!frontOcrLoading && !frontOcrSuccess && editDriver.LicenseFrontImage">
                                            <div class="flex flex-col items-center">
                                                <img :src="'<?= URLROOT ?>/' + editDriver.LicenseFrontImage" class="h-10 object-cover rounded border shadow-sm mb-1">
                                                <p class="text-[9px] text-gray-400">Click/Drag to replace front</p>
                                            </div>
                                        </template>
                                        <!-- No scan at all -->
                                        <template x-if="!frontOcrLoading && !frontOcrSuccess && !editDriver.LicenseFrontImage">
                                            <div class="flex flex-col items-center">
                                                <svg class="mx-auto h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <p class="text-[10px] text-gray-500 mt-1"><span class="text-eco-primary font-semibold">Upload Front Scan</span></p>
                                            </div>
                                        </template>
                                        <!-- Scanning -->
                                        <div x-show="frontOcrLoading" class="flex flex-col items-center py-1">
                                            <div class="relative w-7 h-7 flex items-center justify-center">
                                                <div class="absolute inset-0 rounded-full border-2 border-eco-primary/20"></div>
                                                <div class="absolute inset-0 rounded-full border-2 border-eco-primary border-t-transparent animate-spin"></div>
                                            </div>
                                            <p class="text-[10px] text-eco-primary font-medium mt-1 animate-pulse">AI Re-scanning...</p>
                                        </div>
                                        <!-- Successfully updated -->
                                        <div x-show="frontOcrSuccess" class="flex flex-col items-center">
                                            <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100 ai-pulse mb-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <p class="text-[10px] text-emerald-600 font-semibold">Front Updated!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Back Image uploader -->
                            <div>
                                <label class="block text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">License Back Image</label>
                                <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-5 text-center hover:border-eco-primary transition duration-200 bg-white min-h-[110px] flex items-center justify-center overflow-hidden"
                                     :class="backOcrLoading ? 'border-eco-primary bg-eco-light/5' : ''">
                                    <input type="file" name="LicenseBackImage" accept="image/*" 
                                           @change="handleOcrUpload($event, 'back', 'edit')" 
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20">
                                    <div x-show="backOcrLoading" class="absolute inset-x-0 laser-line z-10"></div>
                                    <div class="space-y-1 z-0 relative w-full">
                                        <template x-if="!backOcrLoading && !backOcrSuccess && editDriver.LicenseBackImage">
                                            <div class="flex flex-col items-center">
                                                <img :src="'<?= URLROOT ?>/' + editDriver.LicenseBackImage" class="h-10 object-cover rounded border shadow-sm mb-1">
                                                <p class="text-[9px] text-gray-400">Click/Drag to replace back</p>
                                            </div>
                                        </template>
                                        <template x-if="!backOcrLoading && !backOcrSuccess && !editDriver.LicenseBackImage">
                                            <div class="flex flex-col items-center">
                                                <svg class="mx-auto h-7 w-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                <p class="text-[10px] text-gray-500 mt-1"><span class="text-eco-primary font-semibold">Upload Back Scan</span></p>
                                            </div>
                                        </template>
                                        <div x-show="backOcrLoading" class="flex flex-col items-center py-1">
                                            <div class="relative w-7 h-7 flex items-center justify-center">
                                                <div class="absolute inset-0 rounded-full border-2 border-eco-primary/20"></div>
                                                <div class="absolute inset-0 rounded-full border-2 border-eco-primary border-t-transparent animate-spin"></div>
                                            </div>
                                            <p class="text-[10px] text-eco-primary font-medium mt-1 animate-pulse">AI Re-scanning...</p>
                                        </div>
                                        <div x-show="backOcrSuccess" class="flex flex-col items-center">
                                            <div class="w-7 h-7 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100 ai-pulse mb-1">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <p class="text-[10px] text-emerald-600 font-semibold">Back Updated!</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: CREDENTIALS & INFO -->
                    <div x-show="editStep === 2" class="grid grid-cols-2 gap-4" x-transition>
                        <!-- Name -->
                        <div class="col-span-2">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Driver Name</label>
                                <span x-show="frontHighlighted && editDriver.DriverName" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Updated
                                </span>
                            </div>
                            <input type="text" name="DriverName" x-model="editDriver.DriverName" required 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="frontHighlighted && editDriver.DriverName ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- License Code -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">License Code</label>
                                <span x-show="frontHighlighted && editDriver.LicenseCode" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Updated
                                </span>
                            </div>
                            <input type="text" name="LicenseCode" x-model="editDriver.LicenseCode" required 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="frontHighlighted && editDriver.LicenseCode ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- License Class -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">License Class</label>
                                <span x-show="backHighlighted && editDriver.LicenseClass" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Updated
                                </span>
                            </div>
                            <input type="text" name="LicenseClass" x-model="editDriver.LicenseClass" placeholder="e.g. B" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="backHighlighted && editDriver.LicenseClass ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Date of Birth</label>
                                <span x-show="frontHighlighted && editDriver.DateOfBirth" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Updated
                                </span>
                            </div>
                            <input type="date" name="DateOfBirth" x-model="editDriver.DateOfBirth" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm text-gray-700"
                                   :class="frontHighlighted && editDriver.DateOfBirth ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- NRC -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">NRC Number</label>
                                <span x-show="frontHighlighted && editDriver.NRC" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Updated
                                </span>
                            </div>
                            <input type="text" name="NRC" x-model="editDriver.NRC" placeholder="e.g. 12/KAMATA(N)091837" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="frontHighlighted && editDriver.NRC ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- Blood Type -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Blood Type</label>
                                <span x-show="frontHighlighted && editDriver.BloodType" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Updated
                                </span>
                            </div>
                            <input type="text" name="BloodType" x-model="editDriver.BloodType" placeholder="e.g. O, AB" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="frontHighlighted && editDriver.BloodType ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- Issued Year -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Issued Year</label>
                                <span x-show="backHighlighted && editDriver.LicenseIssueYear" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Updated
                                </span>
                            </div>
                            <input type="text" name="LicenseIssueYear" x-model="editDriver.LicenseIssueYear" placeholder="e.g. 2025" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                   :class="backHighlighted && editDriver.LicenseIssueYear ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>

                        <!-- Expire Date -->
                        <div class="col-span-2">
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Expiry Valid Until</label>
                                <span x-show="frontHighlighted && editDriver.LicenseExpDate" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Updated
                                </span>
                            </div>
                            <input type="date" name="LicenseExpDate" x-model="editDriver.LicenseExpDate" 
                                   class="w-full px-4 py-2 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm text-gray-700"
                                   :class="frontHighlighted && editDriver.LicenseExpDate ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'">
                        </div>
                    </div>

                    <!-- STEP 3: RESIDENTIAL ADDRESS & PROFILE -->
                    <div x-show="editStep === 3" class="space-y-4" x-transition>
                        <!-- Profile Image Preview and Input -->
                        <div class="flex items-center space-x-4 p-3.5 bg-gray-50/50 rounded-2xl border border-gray-100">
                            <div class="relative">
                                <template x-if="editProfilePreview">
                                    <img :src="editProfilePreview" class="w-16 h-16 rounded-full object-cover border-2 border-eco-primary shadow-md">
                                </template>
                                <template x-if="!editProfilePreview && editDriver.ProfileImage">
                                    <img :src="'<?= URLROOT ?>/' + editDriver.ProfileImage" class="w-16 h-16 rounded-full object-cover border-2 border-eco-primary shadow-md">
                                </template>
                                <template x-if="!editProfilePreview && !editDriver.ProfileImage">
                                    <div class="w-16 h-16 rounded-full bg-gray-150 text-gray-400 flex items-center justify-center font-bold border border-gray-200">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                </template>
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Driver Profile Picture (Leave blank to keep current)</label>
                                <input type="file" name="ProfileImage" accept="image/*" @change="handleProfilePreview($event, 'edit')"
                                       class="w-full text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-eco-light file:text-eco-dark hover:file:bg-eco-primary hover:file:text-white file:transition cursor-pointer">
                            </div>
                        </div>

                        <!-- Address -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Residential Address</label>
                                <span x-show="backHighlighted && editDriver.Address" x-transition class="text-[10px] text-emerald-600 font-semibold flex items-center">
                                    <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                    AI Updated
                                </span>
                            </div>
                            <textarea name="Address" x-model="editDriver.Address" rows="3" placeholder="Enter residential street address, city, township..." 
                                      class="w-full px-4 py-2.5 border rounded-xl focus:ring-eco-primary focus:border-eco-primary outline-none transition-glow text-sm"
                                      :class="backHighlighted && editDriver.Address ? 'ring-2 ring-emerald-300 border-emerald-400 bg-emerald-50/20' : 'border-gray-200'"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Buttons -->
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-between items-center">
                    <div>
                        <!-- Back button -->
                        <button type="button" x-show="editStep > 1" @click="editStep--" 
                                class="px-4 py-2 border border-gray-300 text-gray-600 rounded-xl hover:bg-gray-100 text-xs font-semibold transition">Back</button>
                    </div>
                    
                    <div class="flex space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 text-gray-500 rounded-xl hover:bg-gray-100 text-xs font-semibold transition">Cancel</button>
                        
                        <!-- Next button -->
                        <button type="button" x-show="editStep < 3" @click="editStep++" 
                                class="bg-eco-primary hover:bg-eco-dark text-white px-5 py-2 rounded-xl text-xs font-bold transition shadow-sm">Next Step</button>
                        
                        <!-- Submit button -->
                        <button type="submit" x-show="editStep === 3" 
                                class="bg-eco-primary hover:bg-eco-dark text-white px-6 py-2 rounded-xl text-xs font-bold transition shadow-sm">Update Driver</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lightbox Modal -->
    <div x-show="lightboxOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-950/80 backdrop-blur-md" style="display: none;" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <div @click.away="closeLightbox()" class="relative max-w-4xl max-h-[85vh] overflow-hidden flex flex-col items-center bg-white rounded-2xl shadow-2xl border border-gray-200/20">
            <!-- Close Button -->
            <button @click="closeLightbox()" class="absolute top-4 right-4 z-10 bg-black/60 hover:bg-black/80 text-white rounded-full p-2 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <!-- Image Title -->
            <div class="px-6 py-4 border-b border-gray-100 w-full bg-gray-50/50 flex justify-between items-center">
                <h4 class="font-bold text-gray-800 text-sm" x-text="lightboxTitle">Image Preview</h4>
            </div>
            
            <!-- Big Image Container -->
            <div class="p-6 bg-gray-900 flex items-center justify-center overflow-auto w-full max-h-[70vh]">
                <img :src="lightboxImage" class="max-w-full max-h-[60vh] object-contain rounded-lg shadow-md transition duration-300 hover:scale-[1.01]">
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-3.5 bg-gray-50 w-full text-center text-xxs text-gray-400 font-semibold border-t border-gray-100 uppercase tracking-widest">
                EcoPath Operations Console
            </div>
        </div>
    </div>

    <!-- AlpineJS Application State -->
    <script>
        function driverApp() {
            return {
                viewMode: 'list',
                mobileMenuOpen: false,
                showAddModal: false,
                showEditModal: false,
                addStep: 1,
                editStep: 1,
                
                // Lightbox
                lightboxOpen: false,
                lightboxImage: '',
                lightboxTitle: '',
                
                frontOcrLoading: false,
                backOcrLoading: false,
                frontOcrSuccess: false,
                backOcrSuccess: false,
                frontHighlighted: false,
                backHighlighted: false,
                
                // Real-time local profiles previews
                addProfilePreview: '',
                editProfilePreview: '',
                
                // Form fields for Add Mode
                addDriver: {
                    DriverName: '',
                    LicenseCode: '',
                    DateOfBirth: '',
                    NRC: '',
                    BloodType: '',
                    LicenseExpDate: '',
                    LicenseIssueYear: '',
                    LicenseClass: '',
                    Address: ''
                },
                
                // Form fields for Edit Mode
                editDriver: {
                    DriverID: '',
                    DriverName: '',
                    LicenseCode: '',
                    DateOfBirth: '',
                    NRC: '',
                    BloodType: '',
                    LicenseExpDate: '',
                    LicenseIssueYear: '',
                    LicenseClass: '',
                    Address: '',
                    ProfileImage: '',
                    LicenseFrontImage: '',
                    LicenseBackImage: ''
                },
                
                // Search query
                searchQuery: '',
                
                // Inject the drivers records straight from database
                drivers: <?= json_encode($data['drivers'] ?? []) ?>,
                
                // Filtered records computed property
                get filteredDrivers() {
                    if (!this.searchQuery) return this.drivers;
                    const query = this.searchQuery.toLowerCase();
                    return this.drivers.filter(d => {
                        const nameMatch = d.DriverName ? d.DriverName.toLowerCase().includes(query) : false;
                        const idMatch = d.DriverID ? d.DriverID.toLowerCase().includes(query) : false;
                        const licenseMatch = d.LicenseCode ? d.LicenseCode.toLowerCase().includes(query) : false;
                        const nrcMatch = d.NRC ? d.NRC.toLowerCase().includes(query) : false;
                        const addressMatch = d.Address ? d.Address.toLowerCase().includes(query) : false;
                        return nameMatch || idMatch || licenseMatch || nrcMatch || addressMatch;
                    });
                },
                
                resetAddDriver() {
                    this.addDriver = {
                        DriverName: '',
                        LicenseCode: '',
                        DateOfBirth: '',
                        NRC: '',
                        BloodType: '',
                        LicenseExpDate: '',
                        LicenseIssueYear: '',
                        LicenseClass: '',
                        Address: ''
                    };
                    this.addStep = 1;
                    this.addProfilePreview = '';
                    this.frontOcrSuccess = false;
                    this.backOcrSuccess = false;
                    this.frontHighlighted = false;
                    this.backHighlighted = false;
                    this.showAddModal = true;
                },
                
                openEditModal(driver) {
                    this.editDriver = {
                        DriverID: driver.DriverID,
                        DriverName: driver.DriverName || '',
                        LicenseCode: driver.LicenseCode || '',
                        DateOfBirth: driver.DateOfBirth || '',
                        NRC: driver.NRC || '',
                        BloodType: driver.BloodType || '',
                        LicenseExpDate: driver.LicenseExpDate || '',
                        LicenseIssueYear: driver.LicenseIssueYear || '',
                        LicenseClass: driver.LicenseClass || '',
                        Address: driver.Address || '',
                        ProfileImage: driver.ProfileImage || '',
                        LicenseFrontImage: driver.LicenseFrontImage || '',
                        LicenseBackImage: driver.LicenseBackImage || ''
                    };
                    this.editStep = 1;
                    this.editProfilePreview = '';
                    this.frontOcrSuccess = false;
                    this.backOcrSuccess = false;
                    this.frontHighlighted = false;
                    this.backHighlighted = false;
                    this.showEditModal = true;
                },
                
                openLightbox(imageUrl, title) {
                    this.lightboxImage = imageUrl;
                    this.lightboxTitle = title;
                    this.lightboxOpen = true;
                },
                
                closeLightbox() {
                    this.lightboxOpen = false;
                },
                
                handleProfilePreview(event, mode) {
                    const file = event.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            if (mode === 'add') {
                                this.addProfilePreview = e.target.result;
                            } else {
                                this.editProfilePreview = e.target.result;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                },

                async handleOcrUpload(event, side, mode) {
                    const file = event.target.files[0];
                    if (!file) return;

                    if (side === 'front') {
                        this.frontOcrLoading = true;
                        this.frontOcrSuccess = false;
                    } else {
                        this.backOcrLoading = true;
                        this.backOcrSuccess = false;
                    }

                    const formData = new FormData();
                    formData.append('image', file);
                    formData.append('side', side);

                    try {
                        const response = await fetch('<?= URLROOT ?>/admin/ocr', {
                            method: 'POST',
                            body: formData
                        });

                        const result = await response.json();

                        if (result.error) {
                            alert('OCR Error: ' + result.error);
                            return;
                        }

                        const target = mode === 'add' ? this.addDriver : this.editDriver;

                        if (side === 'front') {
                            if (result.license_no) target.LicenseCode = result.license_no;
                            if (result.name) target.DriverName = result.name;
                            if (result.nrc_no) target.NRC = result.nrc_no;
                            if (result.date_of_birth) target.DateOfBirth = result.date_of_birth;
                            if (result.blood_type) target.BloodType = blockClean(result.blood_type);
                            if (result.valid_up_to) target.LicenseExpDate = result.valid_up_to;
                            
                            this.frontOcrSuccess = true;
                            this.frontHighlighted = true;
                            setTimeout(() => this.frontHighlighted = false, 3500);
                        } else {
                            if (result.issued_year) target.LicenseIssueYear = result.issued_year;
                            if (result.license_class) target.LicenseClass = blockClean(result.license_class);
                            if (result.address) target.Address = result.address;

                            this.backOcrSuccess = true;
                            this.backHighlighted = true;
                            setTimeout(() => this.backHighlighted = false, 3500);
                        }
                    } catch (err) {
                        console.error(err);
                        alert('An unexpected error occurred during OCR scanning.');
                    } finally {
                        if (side === 'front') {
                            this.frontOcrLoading = false;
                        } else {
                            this.backOcrLoading = false;
                        }
                    }
                }
            };
            
            // Short helper to clean blood/class types of any formatting anomalies
            function blockClean(str) {
                if(!str) return '';
                return str.replace(/🩸|🩸\s*/g, '').trim();
            }
        }
    </script>

</body>
</html>
