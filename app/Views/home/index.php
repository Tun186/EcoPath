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
</head>
<body class="bg-gray-50 font-sans min-h-screen text-gray-800">

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
                    
                    <form action="<?= URLROOT ?>/home/book/<?= $package->PackageID ?>" method="POST">
                        <button type="submit" class="w-full bg-gray-900 hover:bg-eco-primary text-white font-medium py-3 rounded-xl transition duration-300 flex justify-center items-center">
                            Book Package
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    </form>
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

</body>
</html>
