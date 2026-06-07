<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?></title>
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
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    
    <!-- Simple Nav -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?= URLROOT ?>" class="flex-shrink-0 flex items-center text-eco-primary font-bold text-2xl tracking-tight hover:text-green-600 transition duration-300">
                        🌿 EcoPath
                    </a>
                </div>
                <div class="flex items-center">
                    <a href="<?= URLROOT ?>" class="text-sm font-medium text-gray-500 hover:text-eco-primary transition duration-150">Back to Home</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="flex-grow max-w-3xl mx-auto py-12 px-4 sm:px-6 lg:px-8 w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 sm:p-12">
            <div class="mb-8 border-b border-gray-200 pb-6">
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Terms of Service</h1>
                <p class="mt-2 text-sm text-gray-500">Last Updated: May 2026</p>
            </div>

            <div class="space-y-8 text-gray-600 leading-relaxed">
                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">1. Acceptance of Terms</h2>
                    <p>By creating an account and utilizing the EcoPath platform, you agree to comply with these Terms of Service. EcoPath serves as a booking intermediary linking travelers with transportation and accommodation providers while executing environmental offset initiatives.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">2. Bookings & Transactions</h2>
                    <ul class="list-disc pl-5 mt-2 space-y-2">
                        <li>All bookings are subject to availability. A transaction is only complete when an official EcoPath confirmation receipt is issued.</li>
                        <li>Cancellations and refunds are dictated by the individual policies of the bus operators and hotels. EcoPath's service fee and direct NGO donations are strictly <strong>non-refundable</strong> once processed.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">3. Eco-Points & Gamification</h2>
                    <ul class="list-disc pl-5 mt-2 space-y-2">
                        <li>Eco-Points are rewarded based on algorithmic calculations of carbon savings.</li>
                        <li>Eco-Points have <strong>no cash value</strong> outside of the EcoPath platform and cannot be withdrawn to a bank account.</li>
                        <li>Eco-Points may only be redeemed for discounts on future travel packages or converted into direct NGO donations via our internal Point Exchange system.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">4. Eco-Subscriptions</h2>
                    <p>Subscription tiers (e.g., Premium, Eco-VIP) apply a "Tree Multiplier" to your travel impact. This multiplier is only valid while the subscription is actively maintained and paid. Downgrading a tier will immediately reduce your multiplier for subsequent bookings.</p>
                </section>
                
                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">5. Platform Integrity</h2>
                    <p>Users attempting to manipulate the Carbon Engine, abuse the Eco-Points system, or utilize fraudulent payment methods will face immediate account termination and forfeiture of all points and active bookings without refund.</p>
                </section>
            </div>
        </div>
    </main>

    <!-- Simple Footer -->
    <footer class="bg-white border-t border-gray-200 py-8 text-center text-sm text-gray-500">
        &copy; <?= date('Y') ?> EcoPath Sustainable Travel. All rights reserved.
    </footer>
</body>
</html>
