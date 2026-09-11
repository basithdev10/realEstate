<?php
/**
 * Properties Management View
 */

requireAuth();

$pageTitle = 'Properties & Units';

$filters = [
    'project_id' => $_GET['project_id'] ?? '',
    'type' => $_GET['type'] ?? '',
    'status' => $_GET['status'] ?? '',
    'search' => $_GET['search'] ?? ''
];

$page = intval($_GET['p'] ?? 1);
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

$units = $propertyModel->getUnits($filters, $limit, $offset);
$totalUnits = $propertyModel->countUnits($filters);
$projects = $propertyModel->getProjects();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($pageTitle); ?></title>
    <link rel="stylesheet" href="/css/output.css">
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md fixed h-screen overflow-y-auto">
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold text-blue-600"><?php echo esc(APP_NAME); ?></h1>
            </div>
            <nav class="mt-6 space-y-1">
                <a href="?page=dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">📊 Dashboard</a>
                <a href="?page=leads" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">👥 Leads</a>
                <a href="?page=properties" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 bg-blue-50 text-blue-600 border-l-4 border-blue-600">🏢 Properties</a>
                <a href="?page=bookings" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">📋 Bookings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow">
                <div class="px-8 py-4 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo esc($pageTitle); ?></h2>
                    <a href="?page=auth&action=logout" class="text-red-600 text-sm">Logout</a>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <form method="GET" class="space-y-4">
                        <input type="hidden" name="page" value="properties">
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                                <select name="project_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Projects</option>
                                    <?php foreach ($projects as $proj): ?>
                                    <option value="<?php echo $proj['id']; ?>" <?php echo ($filters['project_id'] == $proj['id']) ? 'selected' : ''; ?>>
                                        <?php echo esc($proj['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Types</option>
                                    <?php foreach (PROPERTY_TYPES as $key => $type): ?>
                                    <option value="<?php echo esc($key); ?>" <?php echo ($filters['type'] === $key) ? 'selected' : ''; ?>>
                                        <?php echo esc($type); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Status</option>
                                    <?php foreach (UNIT_STATUSES as $key => $status): ?>
                                    <option value="<?php echo esc($key); ?>" <?php echo ($filters['status'] === $key) ? 'selected' : ''; ?>>
                                        <?php echo esc($status); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="flex items-end gap-2">
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                                    Filter
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Units Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <?php if (empty($units)): ?>
                    <div class="p-8 text-center text-gray-500">
                        <p class="text-lg">No units found</p>
                    </div>
                    <?php else: ?>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Project</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Unit</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Type</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Price</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Status</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Area</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($units as $unit): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4 font-medium"><?php echo esc($unit['project_name']); ?></td>
                                <td class="py-3 px-4"><?php echo esc($unit['building_name'] . ' - ' . $unit['unit_number']); ?></td>
                                <td class="py-3 px-4"><?php echo esc(PROPERTY_TYPES[$unit['type']] ?? $unit['type']); ?></td>
                                <td class="py-3 px-4 font-medium"><?php echo formatCurrency($unit['price']); ?></td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo getUnitBadgeClass($unit['status']); ?>">
                                        <?php echo esc($unit['status']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4"><?php echo $unit['built_area'] ? number_format($unit['built_area'], 0) . ' sq.ft' : '-'; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
