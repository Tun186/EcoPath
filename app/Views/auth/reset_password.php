<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITENAME ?> - Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'eco-primary': '#10B981', 'eco-dark': '#064E3B' },
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen font-sans">
    <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-eco-primary mb-2">🌿 EcoPath</h1>
            <h2 class="text-xl font-semibold text-gray-800">Reset Your Password</h2>
            <p class="text-gray-500 text-sm mt-2">Enter your new password below.</p>
        </div>

        <?php if (isset($data['error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6 text-sm">
                <?= htmlspecialchars($data['error']) ?>
            </div>
        <?php endif; ?>

        <?php if (!isset($data['error']) || $data['error'] !== 'Invalid or expired token.'): ?>
        <form action="<?= URLROOT ?>/auth/updatePassword" method="POST" class="space-y-5">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token'] ?? '') ?>">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-eco-primary/50 transition">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-eco-primary/50 transition">
            </div>

            <button type="submit" class="w-full bg-eco-primary hover:bg-eco-dark text-white font-semibold py-3 rounded-xl transition duration-300 shadow-md">
                Update Password
            </button>
        </form>
        <?php else: ?>
            <div class="text-center mt-6">
                <a href="<?= URLROOT ?>/auth/login" class="text-eco-primary hover:underline font-medium">Return to Login</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
