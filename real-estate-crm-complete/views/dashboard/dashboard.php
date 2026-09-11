<?php
/**
 * Dashboard View
 * Shows key metrics and sales information
 */

requireAuth();

$pageTitle = 'Dashboard';
$contentView = __DIR__ . '/content.php';

// Get dashboard data
$leadsCountByStage = $leadModel->getLeadsCountByStage();
$totalLeads = $leadModel->countLeads();
$pendingFollowUps = $leadModel->getPendingFollowUps(7);
$recentBookings = $bookingModel->getRecentBookings(5);
$projects = $propertyModel->getProjects(null, 0);
$unitsCountByStatus = $propertyModel->getUnitsCountByStatus();
$totalRevenue = $bookingModel->getTotalRevenue();
$confirmedBookings = $bookingModel->countBookings(['booking_status' => 'Confirmed']);
$availableUnits = $propertyModel->countUnits(['status' => 'Available']);

// Prepare stage counts
$stageData = [];
foreach (LEAD_STAGES as $stage) {
    $count = 0;
    foreach ($leadsCountByStage as $item) {
        if ($item['stage'] === $stage) {
            $count = $item['count'];
            break;
        }
    }
    $stageData[$stage] = $count;
}

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
        <div class="w-64 bg-white shadow-md fixed h-screen">
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold text-blue-600"><?php echo esc(APP_NAME); ?></h1>
                <p class="text-xs text-gray-500">Property Management</p>
            </div>

            <nav class="mt-6 space-y-1">
                <a href="?page=dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 bg-blue-50 text-blue-600 border-l-4 border-blue-600 transition">
                    📊 Dashboard
                </a>
                <a href="?page=leads" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                    👥 Leads
                </a>
                <a href="?page=properties" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                    🏢 Properties
                </a>
                <a href="?page=bookings" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                    📋 Bookings
                </a>
                
                <?php if (isAdmin()): ?>
                <div class="mt-6 pt-6 border-t space-y-1">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase">Admin</p>
                    <a href="#" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                        ⚙️ Settings
                    </a>
                </div>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow sticky top-0 z-10">
                <div class="px-8 py-4 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo esc($pageTitle); ?></h2>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">
                            👤 <?php echo esc($currentUser['name'] ?? 'Guest'); ?> 
                            <span class="text-xs text-gray-500">(<?php echo esc($currentUser['role'] === 'admin' ? 'Admin' : 'Sales'); ?>)</span>
                        </span>
                        <a href="?page=auth&action=logout" class="text-red-600 hover:text-red-800 text-sm font-medium">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="p-8">
                <!-- KPI Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <!-- Total Leads -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Total Leads</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?php echo $totalLeads; ?></h3>
                            </div>
                            <span class="text-4xl">👥</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-4">
                            <a href="?page=leads" class="text-blue-600 hover:underline">View all →</a>
                        </p>
                    </div>

                    <!-- Bookings -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Confirmed Bookings</p>
                                <h3 class="text-3xl font-bold text-green-600"><?php echo $confirmedBookings; ?></h3>
                            </div>
                            <span class="text-4xl">✅</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-4">
                            Revenue: <strong><?php echo formatCurrency($totalRevenue); ?></strong>
                        </p>
                    </div>

                    <!-- Available Units -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Available Units</p>
                                <h3 class="text-3xl font-bold text-blue-600"><?php echo $availableUnits; ?></h3>
                            </div>
                            <span class="text-4xl">🏠</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-4">
                            <a href="?page=properties" class="text-blue-600 hover:underline">View properties →</a>
                        </p>
                    </div>

                    <!-- Follow-ups Due -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm">Follow-ups (7 days)</p>
                                <h3 class="text-3xl font-bold text-orange-600"><?php echo count($pendingFollowUps); ?></h3>
                            </div>
                            <span class="text-4xl">📞</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-4">
                            Pending attention
                        </p>
                    </div>
                </div>

                <!-- Leads by Stage & Recent Bookings -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Leads Pipeline -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Sales Pipeline</h3>
                        <div class="space-y-3">
                            <?php foreach (LEAD_STAGES as $stage): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700"><?php echo esc($stage); ?></span>
                                <div class="flex items-center gap-3">
                                    <div class="w-40 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: <?php echo ($stageData[$stage] / max($totalLeads, 1)) * 100; ?>%"></div>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800 w-8 text-right"><?php echo $stageData[$stage]; ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Recent Bookings -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Recent Bookings</h3>
                        <div class="space-y-3 max-h-80 overflow-y-auto">
                            <?php if (empty($recentBookings)): ?>
                            <p class="text-gray-500 text-sm text-center py-4">No bookings yet</p>
                            <?php else: ?>
                                <?php foreach ($recentBookings as $booking): ?>
                                <div class="border-l-4 border-green-500 pl-4 py-2">
                                    <p class="text-sm font-medium text-gray-800">
                                        <?php echo esc($booking['first_name'] . ' ' . $booking['last_name']); ?>
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        <?php echo esc($booking['unit_number']); ?> • <?php echo esc($booking['project_name']); ?>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        <?php echo formatDateTime($booking['created_at']); ?>
                                    </p>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Pending Follow-ups -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">📞 Pending Follow-ups (Next 7 Days)</h3>
                    <?php if (empty($pendingFollowUps)): ?>
                    <p class="text-gray-500 text-center py-8">All follow-ups are up to date! 🎉</p>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">Lead</th>
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">Follow-up Date</th>
                                    <th class="text-left py-2 px-2 font-semibold text-gray-700">Note</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingFollowUps as $note): ?>
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="py-2 px-2">
                                        <a href="?page=leads&action=view&id=<?php echo $note['lead_id']; ?>" class="text-blue-600 hover:underline">
                                            <?php echo esc($note['first_name'] . ' ' . $note['last_name']); ?>
                                        </a>
                                    </td>
                                    <td class="py-2 px-2 font-medium text-orange-600"><?php echo formatDate($note['follow_up_date']); ?></td>
                                    <td class="py-2 px-2 text-gray-600"><?php echo esc(substr($note['note'], 0, 50)); ?>...</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
