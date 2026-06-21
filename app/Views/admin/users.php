<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['title'] ?? 'Manage Users' ?></title>
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
<body class="bg-gray-50 font-sans min-h-screen flex text-gray-800" x-data="{ mobileMenuOpen: false }">

    <?php
    $activePage = 'users';
    require APPROOT . '/app/Views/admin/inc/sidebar.php';
    ?>

    <!-- Main Content -->
    <main class="flex-1 flex flex-col relative z-10 overflow-y-auto">
        
        <?php
        $headerTitle = 'Manage Users';
        $headerSubtitle = 'View and update user roles';
        require APPROOT . '/app/Views/admin/inc/header.php';
        ?>

        <!-- Page Content -->
        <div class="p-8 space-y-8 max-w-7xl">
            
            <?php if(isset($_GET['success'])): ?>
                <?php if($_GET['success'] == 'reset_sent'): ?>
                <div class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">Password reset link sent successfully!</span>
                </div>
                <?php else: ?>
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">User role successfully updated!</span>
                </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">All Registered Users</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white text-gray-400 text-sm uppercase tracking-wider">
                                <th class="px-6 py-4 font-semibold">User</th>
                                <th class="px-6 py-4 font-semibold">Phone</th>
                                <th class="px-6 py-4 font-semibold">EcoPoints</th>
                                <th class="px-6 py-4 font-semibold">Joined Date</th>
                                <th class="px-6 py-4 font-semibold text-right">Role & Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            <?php foreach ($data['users'] as $user): ?>
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="px-6 py-4 flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center font-bold mr-3 uppercase">
                                        <?= substr($user->Username, 0, 1) ?>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800"><?= htmlspecialchars($user->Username) ?></p>
                                        <p class="text-xs text-gray-500"><?= htmlspecialchars($user->Email) ?></p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-500"><?= htmlspecialchars($user->Phone ?? 'N/A') ?></td>
                                <td class="px-6 py-4 font-semibold text-eco-primary"><?= $user->EcoPoints ?></td>
                                <td class="px-6 py-4 text-gray-500"><?= date('M j, Y', strtotime($user->RegistrationDate)) ?></td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-4">
                                        <form action="<?= URLROOT ?>/admin/users" method="POST" class="inline-flex items-center space-x-2">
                                            <input type="hidden" name="user_id" value="<?= $user->UserID ?>">
                                            <select name="role_id" class="bg-gray-50 border border-gray-200 text-gray-700 py-1.5 px-3 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-eco-primary">
                                                <option value="01" <?= $user->RoleID == '01' ? 'selected' : '' ?>>Admin</option>
                                                <option value="02" <?= $user->RoleID == '02' ? 'selected' : '' ?>>User</option>
                                                <option value="03" <?= $user->RoleID == '03' ? 'selected' : '' ?>>Planner</option>
                                                <option value="04" <?= $user->RoleID == '04' ? 'selected' : '' ?>>Accountant</option>
                                            </select>
                                            <button type="submit" class="bg-eco-primary hover:bg-eco-dark text-white px-3 py-1.5 rounded-lg text-sm transition shadow-sm font-medium">Update</button>
                                        </form>
                                        
                                        <form action="<?= URLROOT ?>/admin/users" method="POST" class="inline">
                                            <input type="hidden" name="action" value="send_reset_link">
                                            <input type="hidden" name="email" value="<?= htmlspecialchars($user->Email) ?>">
                                            <button type="submit" class="text-blue-500 hover:text-blue-700 text-sm font-medium underline" onclick="return confirm('Send a password reset link to this user?');">Send Reset Link</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
