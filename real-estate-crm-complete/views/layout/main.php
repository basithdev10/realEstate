<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($pageTitle ?? APP_NAME); ?> - <?php echo esc(APP_NAME); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-icons@latest/dist/umd/lucide.min.js">
    <style>
        .sidebar-link {
            @apply block px-4 py-2 text-gray-700 hover:bg-gray-100 transition;
        }
        .sidebar-link.active {
            @apply bg-blue-50 text-blue-600 border-l-4 border-blue-600;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <div class="p-6 border-b">
                <h1 class="text-2xl font-bold text-blue-600"><?php echo esc(APP_NAME); ?></h1>
                <p class="text-xs text-gray-500">Property Management</p>
            </div>

            <nav class="mt-6">
                <a href="?page=dashboard" class="sidebar-link <?php echo $page === 'dashboard' ? 'active' : ''; ?>">
                    📊 Dashboard
                </a>
                <a href="?page=leads" class="sidebar-link <?php echo $page === 'leads' ? 'active' : ''; ?>">
                    👥 Leads
                </a>
                <a href="?page=properties" class="sidebar-link <?php echo $page === 'properties' ? 'active' : ''; ?>">
                    🏢 Properties
                </a>
                <a href="?page=bookings" class="sidebar-link <?php echo $page === 'bookings' ? 'active' : ''; ?>">
                    📋 Bookings
                </a>
                
                <?php if (isAdmin()): ?>
                <div class="mt-6 pt-6 border-t">
                    <p class="px-4 text-xs font-semibold text-gray-500 uppercase">Admin</p>
                    <a href="#" class="sidebar-link">
                        ⚙️ Settings
                    </a>
                </div>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Top Bar -->
            <div class="bg-white shadow">
                <div class="px-6 py-4 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800"><?php echo esc($pageTitle ?? 'Dashboard'); ?></h2>
                    <div class="flex items-center gap-4">
                        <span class="text-sm text-gray-600">
                            👤 <?php echo esc($currentUser['name'] ?? 'Guest'); ?> 
                            <span class="text-xs text-gray-500">(<?php echo esc($currentUser['role'] === 'admin' ? 'Admin' : 'Sales'); ?>)</span>
                        </span>
                        <a href="?page=auth&action=logout" class="text-red-600 hover:text-red-800 text-sm">Logout</a>
                    </div>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php $flash = getFlash(); if ($flash): ?>
            <div class="mx-6 mt-4 p-4 rounded-lg bg-<?php echo $flash['type'] === MESSAGE_SUCCESS ? 'green' : ($flash['type'] === MESSAGE_ERROR ? 'red' : 'blue'); ?>-100 text-<?php echo $flash['type'] === MESSAGE_SUCCESS ? 'green' : ($flash['type'] === MESSAGE_ERROR ? 'red' : 'blue'); ?>-800">
                <?php echo esc($flash['message']); ?>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="p-6">
                <?php require_once $contentView; ?>
            </div>
        </div>
    </div>

    <script>
        // Simple tooltip and interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Add any global interactions here
            console.log('CRM Loaded');
        });
    </script>
</body>
</html>
