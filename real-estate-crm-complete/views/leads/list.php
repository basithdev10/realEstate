<?php
/**
 * Leads List View
 */
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
                <p class="text-xs text-gray-500">Property Management</p>
            </div>

            <nav class="mt-6 space-y-1">
                <a href="?page=dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                    📊 Dashboard
                </a>
                <a href="?page=leads" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 bg-blue-50 text-blue-600 border-l-4 border-blue-600">
                    👥 Leads
                </a>
                <a href="?page=properties" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                    🏢 Properties
                </a>
                <a href="?page=bookings" class="block px-4 py-2 text-gray-700 hover:bg-gray-100 transition">
                    📋 Bookings
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="ml-64 flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow sticky top-0 z-10">
                <div class="px-8 py-4 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800"><?php echo esc($pageTitle); ?></h2>
                    <div class="flex items-center gap-4">
                        <a href="?page=leads&action=create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                            + Add Lead
                        </a>
                        <a href="?page=auth&action=logout" class="text-red-600 hover:text-red-800 text-sm">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Flash Message -->
            <?php if ($flash): ?>
            <div class="mx-8 mt-4 p-4 rounded-lg bg-green-100 text-green-800">
                <?php echo esc($flash['message']); ?>
            </div>
            <?php endif; ?>

            <!-- Content -->
            <div class="p-8">
                <!-- Filters -->
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <form method="GET" class="space-y-4">
                        <input type="hidden" name="page" value="leads">
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" name="search" value="<?php echo esc($filters['search']); ?>" 
                                    placeholder="Name, email or phone..." 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Stage</label>
                                <select name="stage" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All Stages</option>
                                    <?php foreach (LEAD_STAGES as $stage): ?>
                                    <option value="<?php echo esc($stage); ?>" <?php echo ($filters['stage'] === $stage) ? 'selected' : ''; ?>>
                                        <?php echo esc($stage); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                                <select name="assigned_to" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">All</option>
                                    <?php foreach ($salesEmployees as $emp): ?>
                                    <option value="<?php echo $emp['id']; ?>" <?php echo ($filters['assigned_to'] == $emp['id']) ? 'selected' : ''; ?>>
                                        <?php echo esc($emp['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="flex items-end gap-2">
                                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition font-medium">
                                    Search
                                </button>
                                <a href="?page=leads" class="w-full text-center bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Leads Table -->
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <?php if (empty($leads)): ?>
                    <div class="p-8 text-center text-gray-500">
                        <p class="text-lg">No leads found</p>
                        <a href="?page=leads&action=create" class="text-blue-600 hover:underline mt-2 inline-block">Create your first lead</a>
                    </div>
                    <?php else: ?>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Name</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Contact</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Stage</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Assigned To</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Budget</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Date</th>
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leads as $lead): ?>
                            <tr class="border-b hover:bg-gray-50 transition">
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-800">
                                        <?php echo esc($lead['first_name'] . ' ' . $lead['last_name']); ?>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-xs text-gray-600">
                                        <?php echo esc($lead['email']); ?><br>
                                        <?php echo esc($lead['phone']); ?>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold <?php echo getStageBadgeClass($lead['stage']); ?>">
                                        <?php echo esc($lead['stage']); ?>
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <?php echo esc($lead['assigned_to_name'] ?? 'Unassigned'); ?>
                                </td>
                                <td class="py-3 px-4 text-sm">
                                    <?php echo $lead['budget'] ? formatCurrency($lead['budget']) : '-'; ?>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-500">
                                    <?php echo formatDate($lead['created_at']); ?>
                                </td>
                                <td class="py-3 px-4">
                                    <a href="?page=leads&action=view&id=<?php echo $lead['id']; ?>" class="text-blue-600 hover:underline text-xs font-medium">
                                        View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="bg-gray-50 px-4 py-3 flex justify-between items-center text-sm">
                        <span class="text-gray-600">
                            Showing <?php echo (($page - 1) * $limit) + 1; ?> to <?php echo min($page * $limit, $totalLeads); ?> of <?php echo $totalLeads; ?>
                        </span>
                        <div class="flex gap-2">
                            <?php if ($page > 1): ?>
                                <a href="?page=leads&p=<?php echo $page - 1; ?>&search=<?php echo esc($filters['search']); ?>&stage=<?php echo esc($filters['stage']); ?>" class="px-3 py-1 border rounded hover:bg-gray-100">
                                    ← Prev
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <a href="?page=leads&p=<?php echo $i; ?>&search=<?php echo esc($filters['search']); ?>&stage=<?php echo esc($filters['stage']); ?>" 
                                   class="px-3 py-1 border rounded <?php echo ($i === $page) ? 'bg-blue-500 text-white' : 'hover:bg-gray-100'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <a href="?page=leads&p=<?php echo $page + 1; ?>&search=<?php echo esc($filters['search']); ?>&stage=<?php echo esc($filters['stage']); ?>" class="px-3 py-1 border rounded hover:bg-gray-100">
                                    Next →
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
