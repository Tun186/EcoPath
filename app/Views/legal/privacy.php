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
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Privacy Policy</h1>
                <p class="mt-2 text-sm text-gray-500">Last Updated: May 2026</p>
            </div>

            <div class="space-y-8 text-gray-600 leading-relaxed">
                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">1. Information We Collect</h2>
                    <p>To accurately calculate your carbon footprint and process bookings, EcoPath collects:</p>
                    <ul class="list-disc pl-5 mt-2 space-y-2">
                        <li><strong>Profile Information:</strong> Name, email address, phone number, and encrypted passwords.</li>
                        <li><strong>Travel Data:</strong> Booking history, transportation modes (bus operator, seat), and accommodation choices.</li>
                        <li><strong>Financial Data:</strong> Payment histories and e-wallet donation records. (Note: We do not store full credit card numbers; these are handled by our secure payment gateways).</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">2. How We Use Your Data</h2>
                    <p>Your data is strictly utilized to operate the EcoPath platform and fulfill our ecological promises:</p>
                    <ul class="list-disc pl-5 mt-2 space-y-2">
                        <li>Running our Scientific Carbon Engine to determine exact CO2 emissions for your specific trips.</li>
                        <li>Allocating and calculating Eco-Points based on your sustainable travel choices.</li>
                        <li>Generating transparent, legally verifiable E-Certificates for your donations.</li>
                        <li>Processing your tiered Eco-Subscription benefits (e.g., Tree Multipliers).</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">3. Data Sharing & Third Parties</h2>
                    <p>We <strong>never</strong> sell your personal data. We only share necessary data with:</p>
                    <ul class="list-disc pl-5 mt-2 space-y-2">
                        <li><strong>Verified Environmental NGOs:</strong> Only to issue your official E-Certificates (usually limited to your Name and Donation Amount).</li>
                        <li><strong>Travel Partners:</strong> Bus operators and hotels receive basic passenger manifests required for your reservation.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">4. Security</h2>
                    <p>We implement robust, industry-standard security measures, including bcrypt password hashing and TLS/SSL encryption for all data transmissions, to protect your account and financial data against unauthorized access.</p>
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
