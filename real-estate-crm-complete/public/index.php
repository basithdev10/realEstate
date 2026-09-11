<?php
/**
 * Real Estate CRM - Main Router
 * Entry point for all requests
 */

session_start();

// Load configuration
require_once dirname(__DIR__) . '/config/constants.php';
require_once dirname(__DIR__) . '/config/database.php';

// Load models
require_once dirname(__DIR__) . '/models/User.php';
require_once dirname(__DIR__) . '/models/Lead.php';
require_once dirname(__DIR__) . '/models/Property.php';
require_once dirname(__DIR__) . '/models/Booking.php';

// Helper functions
require_once dirname(__DIR__) . '/includes/helpers.php';

// Initialize models
$db = Database::getInstance()->getConnection();
$userModel = new User($db);
$leadModel = new Lead($db);
$propertyModel = new Property($db);
$bookingModel = new Booking($db);

// Routes
$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;
$method = $_SERVER['REQUEST_METHOD'];

// Check authentication for all pages except login
if ($page !== 'auth' || $action !== 'login') {
    if (!isset($_SESSION['user_id'])) {
        redirect('/index.php?page=auth&action=login');
    }
}

// Set current user
$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $currentUser = $userModel->getUserById($_SESSION['user_id']);
}

// Route the request
try {
    switch ($page) {
        case 'auth':
            require_once dirname(__DIR__) . '/views/auth/auth.php';
            break;

        case 'dashboard':
            require_once dirname(__DIR__) . '/views/dashboard/dashboard.php';
            break;

        case 'leads':
            require_once dirname(__DIR__) . '/views/leads/leads.php';
            break;

        case 'properties':
            require_once dirname(__DIR__) . '/views/properties/properties.php';
            break;

        case 'bookings':
            require_once dirname(__DIR__) . '/views/bookings/bookings.php';
            break;

        case 'api':
            require_once dirname(__DIR__) . '/api/handler.php';
            break;

        default:
            redirect('/index.php?page=dashboard');
    }
} catch (Exception $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}
?>
