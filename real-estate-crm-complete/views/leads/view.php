<?php
/**
 * View Lead Details
 */
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Details</title>
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
                <a href="?page=leads" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 bg-blue-50 text-blue-600 border-l-4 border-blue-600">👥 Leads</a>
                <a href="?page=properties" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">🏢 Properties</a>
                <a href="?page=bookings" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">📋 Bookings</a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow sticky top-0 z-10">
                <div class="px-8 py-4 flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800"><?php echo esc($lead['first_name'] . ' ' . $lead['last_name']); ?></h2>
                        <p class="text-sm text-gray-500"><a href="?page=leads" class="text-blue-600 hover:underline">← Back to Leads</a></p>
                    </div>
                    <a href="?page=auth&action=logout" class="text-red-600 text-sm">Logout</a>
                </div>
            </div>

            <!-- Content -->
            <div class="p-8">
                <div class="grid grid-cols-3 gap-6">
                    <!-- Lead Info -->
                    <div class="col-span-2">
                        <div class="bg-white rounded-lg shadow p-6 mb-6">
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-4">Lead Information</h3>
                                </div>
                                <a href="?page=leads&action=edit&id=<?php echo $lead['id']; ?>" class="text-blue-600 hover:underline text-sm font-medium">
                                    ✏️ Edit
                                </a>
                            </div>

                            <div class="grid grid-cols-2 gap-4 mb-6 pb-6 border-b">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Email</p>
                                    <p class="text-sm font-medium text-gray-800"><?php echo esc($lead['email'] ?? '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Phone</p>
                                    <p class="text-sm font-medium text-gray-800"><?php echo esc($lead['phone'] ?? '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Budget</p>
                                    <p class="text-sm font-medium text-gray-800"><?php echo $lead['budget'] ? formatCurrency($lead['budget']) : '-'; ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Preferred Type</p>
                                    <p class="text-sm font-medium text-gray-800"><?php echo esc($lead['preferred_property_type'] ?? 'Any'); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Source</p>
                                    <p class="text-sm font-medium text-gray-800"><?php echo esc($lead['source'] ?? '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">Assigned To</p>
                                    <p class="text-sm font-medium text-gray-800"><?php echo esc($lead['assigned_to_name'] ?? 'Unassigned'); ?></p>
                                </div>
                            </div>

                            <!-- Stage Change -->
                            <form method="POST" class="flex gap-2 items-end">
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-500 uppercase mb-2">Current Stage</label>
                                    <select name="stage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <?php foreach (LEAD_STAGES as $stage): ?>
                                        <option value="<?php echo esc($stage); ?>" <?php echo ($lead['stage'] === $stage) ? 'selected' : ''; ?>>
                                            <?php echo esc($stage); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <input type="hidden" name="action" value="updateStage">
                                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                                    Update
                                </button>
                            </form>
                        </div>

                        <!-- Notes -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Notes & Follow-ups</h3>

                            <!-- Add Note Form -->
                            <form method="POST" class="mb-6 pb-6 border-b">
                                <input type="hidden" name="action" value="addNote">
                                <div class="mb-3">
                                    <textarea name="note" placeholder="Add a note..." required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                        rows="3"></textarea>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="text-xs text-gray-500">Follow-up Date (optional)</label>
                                        <input type="date" name="follow_up_date" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    </div>
                                    <div></div>
                                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium h-fit">
                                        Add Note
                                    </button>
                                </div>
                            </form>

                            <!-- Notes List -->
                            <div class="space-y-4">
                                <?php if (empty($lead['notes'])): ?>
                                <p class="text-gray-500 text-sm text-center py-4">No notes yet</p>
                                <?php else: ?>
                                    <?php foreach ($lead['notes'] as $note): ?>
                                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                                        <p class="text-sm font-medium text-gray-800"><?php echo esc($note['note']); ?></p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            By <?php echo esc($note['created_by_name']); ?> • <?php echo formatDateTime($note['created_at']); ?>
                                        </p>
                                        <?php if ($note['follow_up_date']): ?>
                                        <p class="text-xs text-orange-600 mt-1">📌 Follow-up: <?php echo formatDate($note['follow_up_date']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div>
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-lg shadow p-6 mb-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Actions</h3>
                            <div class="space-y-2">
                                <a href="?page=leads&action=edit&id=<?php echo $lead['id']; ?>" class="block w-full text-center bg-blue-600 text-white px-3 py-2 rounded-lg hover:bg-blue-700 text-sm font-medium">
                                    ✏️ Edit Lead
                                </a>
                                <a href="?page=bookings&action=create&lead_id=<?php echo $lead['id']; ?>" class="block w-full text-center bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 text-sm font-medium">
                                    📋 Create Booking
                                </a>
                            </div>
                        </div>

                        <!-- Bookings -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Bookings</h3>
                            <?php 
                                $bookings = $bookingModel->getLeadBookings($lead['id']);
                                if (empty($bookings)):
                            ?>
                            <p class="text-gray-500 text-sm text-center py-4">No bookings yet</p>
                            <?php else: ?>
                                <?php foreach ($bookings as $booking): ?>
                                <div class="mb-3 pb-3 border-b last:border-b-0">
                                    <p class="text-sm font-medium text-gray-800"><?php echo esc($booking['unit_number']); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo esc($booking['project_name']); ?></p>
                                    <span class="inline-block mt-1 text-xs px-2 py-1 rounded <?php echo getBookingBadgeClass($booking['booking_status']); ?>">
                                        <?php echo esc($booking['booking_status']); ?>
                                    </span>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
