<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'EcoPath' ?></title>
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
<body class="bg-gray-50 font-sans min-h-screen text-gray-800" x-data="bookingApp()">

    <!-- Header / Nav -->
    <header class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center">
                    <a href="<?= URLROOT ?>" class="text-eco-primary font-bold text-3xl tracking-tight">🌿 EcoPath</a>
                </div>
                <div class="flex items-center space-x-6">
                    <span class="text-gray-500 text-sm font-medium">Hello, <?= htmlspecialchars($_SESSION['username'] ?? 'Explorer') ?>!</span>
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-eco-primary to-blue-400 p-0.5">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username'] ?? 'User') ?>&background=ffffff&color=10B981" alt="Profile" class="w-full h-full rounded-full border-2 border-white object-cover">
                    </div>
                    <a href="<?= URLROOT ?>/auth/logout" class="text-sm font-medium text-red-500 hover:text-red-700 transition">Sign Out</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="bg-eco-dark text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-30" style="background-image: url('https://images.unsplash.com/photo-1542314831-c6a4d27ce6a2?auto=format&fit=crop&q=80&w=2000');"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Travel the world.<br><span class="text-eco-primary">Restore the planet.</span></h1>
            <p class="text-lg md:text-xl text-gray-300 max-w-2xl mb-8">Every package you book automatically plants trees and offsets carbon emissions. Start your sustainable journey today.</p>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        
        <?php if(isset($_GET['success']) && $_GET['success'] == 'booked'): ?>
        <div class="bg-green-50 border-l-4 border-eco-primary p-4 mb-8 rounded-r-xl shadow-sm">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-eco-primary" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700 font-medium">
                        Booking Confirmed! You just generated fresh carbon credits that will be used to plant trees globally. 🌍
                        <?php if (isset($_GET['seat'])): ?>
                            <span class="block mt-1 font-bold text-eco-dark">Your reserved seat is: <?= htmlspecialchars($_GET['seat']) ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex justify-between items-end mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Featured Expeditions</h2>
                <p class="text-gray-500 mt-1 text-sm">Select an eco-friendly travel package.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($data['packages'] as $package): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transform transition duration-300 hover:-translate-y-2 hover:shadow-xl group">
                <div class="h-48 bg-gray-200 relative overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&q=80&w=800" class="w-full h-full object-cover transition duration-500 group-hover:scale-110" alt="Destination">
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-4 py-1.5 rounded-full text-sm font-bold text-eco-dark shadow-sm">
                        $<?= number_format($package->Price, 2) ?>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-1"><?= htmlspecialchars($package->PackageName) ?></h3>
                    
                    <div class="flex items-center text-eco-dark bg-eco-light/50 inline-flex px-3 py-1.5 rounded-lg mb-6 text-sm font-medium">
                        <svg class="w-4 h-4 mr-2 text-eco-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        <span>Plants <?= $package->BaseTreeCount ?> Trees</span>
                    </div>
                    
                    <button type="button" @click="selectSeat('<?= $package->PackageID ?>', '<?= htmlspecialchars($package->PackageName, ENT_QUOTES) ?>')" class="w-full bg-gray-900 hover:bg-eco-primary text-white font-medium py-3 rounded-xl transition duration-300 flex justify-center items-center">
                        Book Package
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if(empty($data['packages'])): ?>
            <div class="col-span-full bg-white p-12 rounded-3xl border border-dashed border-gray-200 text-center">
                <div class="text-gray-300 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800">No Packages Yet</h3>
                <p class="text-gray-500 mt-2">Planners are still working on exciting new expeditions.</p>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Seat Selection Modal -->
    <div x-show="showSeatModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showSeatModal = false"></div>
            
            <div class="inline-block w-full max-w-lg p-6 my-8 text-left bg-white shadow-2xl rounded-3xl z-10 relative transform transition-all border border-gray-100">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Select Your Seat</h3>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="text-sm text-gray-700 font-semibold" x-text="activePackageName"></span>
                            <template x-if="bus">
                                <div class="flex items-center space-x-1.5 text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full border border-gray-200/50">
                                    <span class="font-medium" x-text="bus.BusCompany"></span>
                                    <span class="text-gray-300">|</span>
                                    <span class="font-medium" x-text="bus.OperatorName"></span>
                                    <span class="text-gray-300">|</span>
                                    <span class="px-1 py-0.5 rounded text-[9px] font-bold" 
                                          :class="{
                                              'bg-green-100 text-green-800': bus.FuelType === 'EV',
                                              'bg-blue-100 text-blue-800': bus.FuelType === 'Gas',
                                              'bg-gray-250 text-gray-800': bus.FuelType === 'Oil'
                                          }"
                                          x-text="bus.FuelType">
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>
                    <button @click="showSeatModal = false" class="text-gray-400 hover:text-gray-600 bg-gray-100 hover:bg-gray-200 p-2 rounded-full transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Loading State -->
                <div x-show="loadingSeats" class="py-12 flex flex-col justify-center items-center space-y-3">
                    <svg class="animate-spin h-8 w-8 text-eco-primary" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-500 text-sm">Fetching seat layout...</span>
                </div>

                <!-- Seating Section -->
                <div x-show="!loadingSeats">
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
                            <template x-for="rowLetter in getSeatRows()" :key="rowLetter">
                                <div class="flex justify-between items-center">
                                    <!-- Left side -->
                                    <div class="flex space-x-2">
                                        <template x-for="seat in getRowSeats(rowLetter, getLeftColumns())" :key="seat.SeatID">
                                            <button 
                                                type="button"
                                                :disabled="seat.IsBooked == 1"
                                                @click="selectedSeatId = seat.SeatID; selectedSeatNumber = seat.SeatNumber"
                                                class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-xs border transition duration-200 shadow-sm"
                                                :class="{
                                                    'bg-gray-200 border-gray-300 text-gray-400 cursor-not-allowed shadow-none': seat.IsBooked == 1,
                                                    'bg-eco-primary border-eco-primary text-white': selectedSeatId === seat.SeatID && seat.IsBooked != 1,
                                                    'bg-white border-gray-200 hover:border-eco-primary text-gray-700': selectedSeatId !== seat.SeatID && seat.IsBooked != 1
                                                }"
                                            >
                                                <span x-text="seat.SeatNumber"></span>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Aisle -->
                                    <div class="text-[10px] font-bold text-gray-300 tracking-widest uppercase">AISLE</div>

                                    <!-- Right side -->
                                    <div class="flex space-x-2">
                                        <template x-for="seat in getRowSeats(rowLetter, getRightColumns())" :key="seat.SeatID">
                                            <button 
                                                type="button"
                                                :disabled="seat.IsBooked == 1"
                                                @click="selectedSeatId = seat.SeatID; selectedSeatNumber = seat.SeatNumber"
                                                class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-xs border transition duration-200 shadow-sm"
                                                :class="{
                                                    'bg-gray-200 border-gray-300 text-gray-400 cursor-not-allowed shadow-none': seat.IsBooked == 1,
                                                    'bg-eco-primary border-eco-primary text-white': selectedSeatId === seat.SeatID && seat.IsBooked != 1,
                                                    'bg-white border-gray-200 hover:border-eco-primary text-gray-700': selectedSeatId !== seat.SeatID && seat.IsBooked != 1
                                                }"
                                            >
                                                <span x-text="seat.SeatNumber"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Legend & Selected Seat Display -->
                    <div class="flex justify-between items-center mb-6 text-sm text-gray-600 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="flex items-center space-x-4">
                            <span class="flex items-center"><span class="w-3.5 h-3.5 bg-white border border-gray-200 rounded-md mr-1.5 shadow-sm"></span> Available</span>
                            <span class="flex items-center"><span class="w-3.5 h-3.5 bg-gray-200 rounded-md mr-1.5"></span> Booked</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-500">Selected:</span> 
                            <span class="font-bold text-eco-primary text-base" x-text="selectedSeatNumber || 'None'"></span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <form :action="'<?= URLROOT ?>/home/book/' + activePackageId" method="POST">
                        <input type="hidden" name="seat_id" :value="selectedSeatId">
                        <div class="flex space-x-3">
                            <button type="button" @click="showSeatModal = false" class="w-1/2 bg-gray-100 hover:bg-gray-250 text-gray-700 py-3.5 rounded-xl font-semibold transition text-sm">Cancel</button>
                            <button type="submit" :disabled="!selectedSeatId" class="w-1/2 bg-eco-primary hover:bg-eco-dark text-white py-3.5 rounded-xl font-semibold transition disabled:opacity-50 disabled:cursor-not-allowed shadow-md text-sm">Confirm Booking</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bookingApp() {
            return {
                showSeatModal: false,
                loadingSeats: false,
                activePackageId: '',
                activePackageName: '',
                selectedSeatId: '',
                selectedSeatNumber: '',
                seats: [],
                bus: null,

                selectSeat(packageId, packageName) {
                    this.activePackageId = packageId;
                    this.activePackageName = packageName;
                    this.selectedSeatId = '';
                    this.selectedSeatNumber = '';
                    this.seats = [];
                    this.bus = null;
                    this.showSeatModal = true;
                    this.loadingSeats = true;

                    fetch('<?= URLROOT ?>/home/seats/' + packageId)
                        .then(res => res.json())
                        .then(data => {
                            this.seats = data.seats || [];
                            this.bus = data.bus || null;
                            this.loadingSeats = false;
                        })
                        .catch(err => {
                            console.error('Error fetching seats:', err);
                            this.loadingSeats = false;
                        });
                },

                getSeatRows() {
                    const rowLetters = new Set();
                    this.seats.forEach(s => {
                        if (s.SeatNumber) {
                            const letter = s.SeatNumber.match(/^[A-Z]+/);
                            if (letter) rowLetters.add(letter[0]);
                        }
                    });
                    return Array.from(rowLetters).sort();
                },

                getLeftColumns() {
                    const layout = this.bus ? this.bus.SeatLayout : '2+2';
                    if (layout === '1+1') return [1];
                    return [1, 2];
                },

                getRightColumns() {
                    const layout = this.bus ? this.bus.SeatLayout : '2+2';
                    if (layout === '1+1') return [2];
                    if (layout === '2+1') return [3];
                    return [3, 4];
                },

                getRowSeats(rowLetter, columns) {
                    return this.seats.filter(s => {
                        const match = s.SeatNumber.match(/^([A-Z]+)(\d+)$/);
                        if (match) {
                            const letter = match[1];
                            const col = parseInt(match[2]);
                            return letter === rowLetter && columns.includes(col);
                        }
                        return false;
                    }).sort((a, b) => a.SeatNumber.localeCompare(b.SeatNumber));
                }
            };
        }
    </script>
</body>
</html>
