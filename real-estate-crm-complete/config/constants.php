<?php
/**
 * Application Constants
 */

define('APP_NAME', 'Real Estate CRM');
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');
define('BASE_PATH', dirname(dirname(__FILE__)));
define('PUBLIC_PATH', BASE_PATH . '/public');

// Session settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('REMEMBER_ME_DURATION', 86400 * 30); // 30 days

// Lead stages
define('LEAD_STAGES', [
    'New',
    'Contacted',
    'Site Visit',
    'Interested',
    'Negotiation',
    'Booked',
    'Lost'
]);

// User roles
define('USER_ROLES', [
    'admin' => 'Admin',
    'sales_employee' => 'Sales Employee'
]);

// Property types
define('PROPERTY_TYPES', [
    '1BHK' => '1 BHK',
    '2BHK' => '2 BHK',
    '3BHK' => '3 BHK',
    '4BHK' => '4 BHK',
    'Commercial' => 'Commercial'
]);

// Unit statuses
define('UNIT_STATUSES', [
    'Available' => 'Available',
    'Booked' => 'Booked',
    'Unavailable' => 'Unavailable'
]);

// Booking statuses
define('BOOKING_STATUSES', [
    'Pending' => 'Pending',
    'Confirmed' => 'Confirmed',
    'Cancelled' => 'Cancelled'
]);

// Messages
define('MESSAGE_SUCCESS', 'success');
define('MESSAGE_ERROR', 'error');
define('MESSAGE_WARNING', 'warning');
define('MESSAGE_INFO', 'info');

// Pagination
define('ITEMS_PER_PAGE', 10);
?>
