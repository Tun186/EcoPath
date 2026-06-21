<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Import Preview' ?></title>
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
</head>
<body class="bg-gray-50 font-sans h-screen flex overflow-hidden text-gray-800">

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
                <a href="<?= URLROOT ?>/auth/logout" class="flex items-center px-4 py-3 text-gray-500 hover:text-red-500 hover:bg-red-50 rounded-xl font-medium transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-hidden flex flex-col relative h-screen">
        <header class="h-20 bg-white/80 backdrop-blur-md border-b border-gray-100 flex items-center px-8 flex-shrink-0 z-10 sticky top-0">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Preview Import</h1>
                <p class="text-sm text-gray-500">Review your Excel data before confirming</p>
            </div>
        </header>

        <div class="p-8 space-y-8 max-w-7xl overflow-y-auto">
            <?php 
                $preview = $data['preview'];
                $isHotelType = $preview['type'] === 'hotels';
                $headers = $isHotelType 
                    ? ['Name', 'City Name', 'EcoRating', 'Latitude', 'Longitude', 'Description']
                    : ['Name', 'City Name', 'Latitude', 'Longitude', 'Description'];
                
                $validCount = count($preview['validRows']);
                $failedCount = count($preview['failedRows']);
                
                // Merge and sort by row number for the display
                $allRows = array_merge($preview['validRows'], $preview['failedRows']);
                usort($allRows, function($a, $b) {
                    return $a['row'] <=> $b['row'];
                });
            ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold">Import Summary (<?= ucfirst($preview['type']) ?>)</h2>
                    <p class="text-sm mt-1">
                        <span class="font-semibold text-green-600"><?= $validCount ?> Valid Rows</span> • 
                        <span class="font-semibold text-red-600"><?= $failedCount ?> Rows with Errors</span>
                    </p>
                </div>
                <div class="flex space-x-3">
                    <form action="<?= URLROOT ?>/planner/infrastructure" method="POST" enctype="multipart/form-data" class="m-0">
                        <input type="hidden" name="action" value="import_<?= $preview['type'] ?>">
                        <label class="bg-blue-50 text-blue-700 hover:bg-blue-100 px-4 py-2.5 rounded-xl font-medium transition cursor-pointer flex items-center border border-blue-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                            Re-upload File
                            <input type="file" name="excel_file" accept=".xlsx" class="hidden" onchange="this.form.submit()">
                        </label>
                    </form>

                    <form action="<?= URLROOT ?>/planner/importConfirm" method="POST" class="m-0">
                        <input type="hidden" name="action" value="cancel_import">
                        <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-5 py-2.5 rounded-xl font-medium transition">
                            Cancel
                        </button>
                    </form>
                    
                    <form action="<?= URLROOT ?>/planner/importConfirm" method="POST" class="m-0">
                        <input type="hidden" name="action" value="confirm_import">
                        <button type="submit" <?= $validCount == 0 ? 'disabled' : '' ?> class="bg-eco-primary hover:bg-eco-dark text-white px-5 py-2.5 rounded-xl font-medium transition disabled:opacity-50 disabled:cursor-not-allowed shadow-md flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Confirm Import (<?= $validCount ?>)
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold text-gray-600 border-r border-gray-200 w-16 text-center">Row</th>
                                <?php foreach($headers as $h): ?>
                                    <th class="px-4 py-3 font-semibold text-gray-600 border-r border-gray-200"><?= htmlspecialchars($h) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($allRows as $r): 
                                $isFailed = isset($r['errors']);
                            ?>
                                <tr class="border-b border-gray-100 hover:bg-gray-50/50">
                                    <td class="px-4 py-2 border-r border-gray-200 text-center text-gray-400 font-medium <?= $isFailed ? 'bg-red-50' : '' ?>">
                                        <?= $r['row'] ?>
                                    </td>
                                    <?php foreach($headers as $colIndex => $colName): 
                                        $cellVal = $r['data'][$colIndex] ?? '';
                                        $hasError = $isFailed && isset($r['errors'][$colIndex]);
                                        $errorMsg = $hasError ? $r['errors'][$colIndex] : '';
                                    ?>
                                    <td class="px-4 py-2 border-r border-gray-200 <?= $hasError ? 'bg-red-100 text-red-900 cursor-help font-medium shadow-inner' : 'text-gray-700' ?>" 
                                        <?= $hasError ? 'title="'.htmlspecialchars($errorMsg).'"' : '' ?>>
                                        <?= htmlspecialchars($cellVal) ?>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if(empty($allRows)): ?>
                            <tr>
                                <td colspan="<?= count($headers) + 1 ?>" class="px-4 py-8 text-center text-gray-500">
                                    No data found in the Excel file.
                                </td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <?php if($failedCount > 0): ?>
            <p class="text-sm text-red-500 font-medium">* Note: Red cells indicate errors. Rows containing errors will NOT be imported.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
