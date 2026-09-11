<?php
/**
 * Edit Lead Form
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lead</title>
    <link rel="stylesheet" href="/css/output.css">
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md fixed h-screen">
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold text-blue-600"><?php echo esc(APP_NAME); ?></h1>
            </div>
            <nav class="mt-6 space-y-1">
                <a href="?page=dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">📊 Dashboard</a>
                <a href="?page=leads" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 bg-blue-50 text-blue-600 border-l-4 border-blue-600">👥 Leads</a>
                <a href="?page=properties" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">🏢 Properties</a>
                <a href="?page=bookings" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">📋 Bookings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow">
                <div class="px-8 py-4 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800">Edit Lead</h2>
                    <a href="?page=auth&action=logout" class="text-red-600 text-sm">Logout</a>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="max-w-2xl">
                    <?php if (isset($error)): ?>
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700">
                        <?php echo esc($error); ?>
                    </div>
                    <?php endif; ?>

                    <div class="bg-white rounded-lg shadow p-6">
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="update">

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                                    <input type="text" name="first_name" value="<?php echo esc($lead['first_name']); ?>" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                                    <input type="text" name="last_name" value="<?php echo esc($lead['last_name']); ?>" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" value="<?php echo esc($lead['email'] ?? ''); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input type="tel" name="phone" value="<?php echo esc($lead['phone'] ?? ''); ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign To</label>
                                    <select name="assigned_to" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Unassigned</option>
                                        <?php foreach ($salesEmployees as $emp): ?>
                                        <option value="<?php echo $emp['id']; ?>" <?php echo ($lead['assigned_to'] == $emp['id']) ? 'selected' : ''; ?>>
                                            <?php echo esc($emp['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Budget (₹)</label>
                                    <input type="number" name="budget" step="100000" min="0" value="<?php echo $lead['budget'] ?? ''; ?>"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Type</label>
                                <select name="preferred_property_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Any</option>
                                    <?php foreach (PROPERTY_TYPES as $key => $type): ?>
                                    <option value="<?php echo esc($key); ?>" <?php echo ($lead['preferred_property_type'] === $key) ? 'selected' : ''; ?>>
                                        <?php echo esc($type); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Source</label>
                                <input type="text" name="source" placeholder="Website, Referral, etc." value="<?php echo esc($lead['source'] ?? ''); ?>"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div class="flex gap-4">
                                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                                    Update Lead
                                </button>
                                <a href="?page=leads&action=view&id=<?php echo $lead['id']; ?>" class="flex-1 text-center bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 rounded-lg transition">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
