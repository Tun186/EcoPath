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
                <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">NGO Verification Standard</h1>
                <p class="mt-2 text-sm text-gray-500">Our commitment against Greenwashing</p>
            </div>

            <div class="space-y-8 text-gray-600 leading-relaxed">
                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">The EcoPath Promise</h2>
                    <p>At EcoPath, we recognize that "sustainability" is often used merely as a marketing buzzword. To combat greenwashing, we have established a rigorous NGO Verification Standard. Every environmental partner visible on our platform must pass this continuous audit.</p>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Verification Criteria</h2>
                    <p>To be listed as an official recipient of EcoPath user donations or automatic tree-planting funds, an NGO must prove:</p>
                    <ul class="list-disc pl-5 mt-2 space-y-3">
                        <li><strong>Legal Registration:</strong> Valid operational registration in the country of impact (e.g., Myanmar).</li>
                        <li><strong>Financial Transparency:</strong> Publicly auditable financial records demonstrating that at least 85% of funds go directly into field operations, not administrative overhead.</li>
                        <li><strong>Geographic Proof:</strong> GPS coordinates or blockchain-backed ledgers of planted trees and conservation areas.</li>
                        <li><strong>Local Community Impact:</strong> Demonstrated integration with local communities, ensuring indigenous and local populations benefit economically from the reforestation efforts.</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-xl font-bold text-gray-900 mb-3">Continuous Auditing</h2>
                    <p>Verification is not a one-time event. EcoPath conducts biannual reviews of all partners. If an NGO fails to maintain these strict standards, their Verification Status is immediately revoked, and user funds are dynamically routed to compliant partners.</p>
                </section>

                <section>
                    <div class="bg-green-50 border-l-4 border-eco-primary p-4 rounded-r-md mt-6">
                        <p class="text-sm text-green-800 font-medium">If you represent an NGO that meets these standards and wishes to partner with EcoPath, please use the Contact Us form on our homepage to request an application packet.</p>
                    </div>
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
