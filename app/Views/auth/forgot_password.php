<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITENAME ?> - Forgot Password</title>
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
</head>
<body class="bg-gradient-to-br from-eco-light to-green-50 min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <div class="text-center">
            <a href="<?= URLROOT ?>" class="text-eco-primary font-bold text-4xl tracking-tight transition duration-300 hover:text-green-600">🌿 EcoPath</a>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
            Forgot Password
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
            Enter your email to receive a password reset link.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-2xl sm:rounded-xl sm:px-10 border border-gray-100 relative overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-green-50 opacity-50 pointer-events-none"></div>

            <?php if(isset($data['success'])): ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative mb-6 text-sm" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($data['success']) ?></span>
                </div>
            <?php endif; ?>

            <?php if(isset($data['error'])): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative mb-6 text-sm" role="alert">
                    <span class="block sm:inline"><?= htmlspecialchars($data['error']) ?></span>
                </div>
            <?php endif; ?>

            <form class="space-y-6 relative z-10" action="<?= URLROOT ?>/auth/sendResetLink" method="POST">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                    <div class="mt-1">
                        <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-eco-primary focus:border-eco-primary sm:text-sm transition duration-150 ease-in-out">
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-eco-primary hover:bg-eco-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-eco-primary transition duration-150 ease-in-out">
                        Send Reset Link
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <a href="<?= URLROOT ?>/auth/login" class="font-medium text-eco-primary hover:text-green-500 transition duration-150 ease-in-out text-sm">
                    Back to login
                </a>
            </div>
        </div>
    </div>
</body>
</html>
