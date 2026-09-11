<?php
/**
 * Bookings Management View
 */

requireAuth();

$pageTitle = 'Bookings';

$filters = ['booking_status' => $_GET['status'] ?? ''];
$page = intval($_GET['p'] ?? 1);
$limit = ITEMS_PER_PAGE;
$offset = ($page - 1) * $limit;

$bookings = $bookingModel->getBookings($filters, $limit, $offset);
$totalBookings = $bookingModel->countBookings($filters);

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
                <a href="?page=properties" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">🏢 Properties</a>
                <a href="?page=bookings" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 bg-blue-50 text-blue-600 border-l-4 border-blue-600">📋 Bookings</a>
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
                <!-- KPIs -->
                <div class="grid grid-cols-3 gap-6 mb-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-500 text-sm">Total Bookings</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?php echo $totalBookings; ?></h3>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-500 text-sm">Confirmed</p>
                        <h3 class="text-3xl font-bold text-green-600"><?php echo $bookingModel->countBookings(['booking_status' => 'Confirmed']); ?></h3>
                    </div>
                    <div class="bg-white rounded-lg shadow p-6">
                        <p class="text-gray-500 text-sm">Pending</p>
                        <h3 class="text-3xl font-bold text-yellow-600"><?php echo $bookingModel->countBookings(['booking_status' => 'Pending']); ?></h3>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <form method="GET" class="grid grid-cols-3 gap-4">
                        <input type="hidden" name="page" value="bookings">
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">All Status</option>
                                <?php foreach (BOOKING_STATUSES as $key => $status): ?>
                                <option value="<?php echo esc($key); ?>" <?php echo ($filters['booking_status'] === $key) ? 'selected' : ''; ?>>
                                    <?php echo esc($status); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div></div>
                        
                        <div class="flex items-end">
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 font-medium">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Bookings Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <?php if (empty($bookings)): ?>
                    <div class="p-8 text-center text-gray-500">
                        <p>No bookings found</p>
                    </div>
                    <?php else: ?>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left py-3 px-4 font-semibold">Lead</th>
                                <th class="text-left py-3 px-4 font-semibold">Unit</th>
                                <th class="text-left py-3 px-4 font-semibold">Project</th>
                                <th class="text-left py-3 px-4 font-semibold">Status</th>
                                <th class="text-left py-3 px-4 font-semibold">Amount</th>
                                <th class="text-left py-3 px-4 font-semibold">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-3 px-4">
                                    <a href="?page=leads&action=view&id=<?php echo $booking['lead_id']; ?>" class="text-blue-600 hover:underline font-medium">
                                        <?php echo esc($booking['first_name'] . ' ' . $booking['last_name']); ?>
                                    </a>
                                </td>
                                <td class="py-3 px-4"><?php echo esc($booking['unit_number']); ?></td>
                                <td class="py-3 px-4"><?php echo esc($booking['project_name']); ?></td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo getBookingBadgeClass($booking['booking_status']); ?>">
                                        <?php echo esc($booking['booking_status']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 font-medium"><?php echo $booking['payment_amount'] ? formatCurrency($booking['payment_amount']) : '-'; ?></td>
                                <td class="py-3 px-4"><?php echo formatDate($booking['created_at']); ?></td>
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
