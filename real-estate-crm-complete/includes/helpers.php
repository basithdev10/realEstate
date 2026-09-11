<?php
/**
 * Helper Functions
 */

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Check if user is authenticated
 */
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user has role
 */
function hasRole($role) {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === $role;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return hasRole('admin');
}

/**
 * Require authentication
 */
function requireAuth() {
    if (!isAuthenticated()) {
        redirect('/index.php?page=auth&action=login');
    }
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        header("HTTP/1.1 403 Forbidden");
        die("Access denied. Admin privileges required.");
    }
}

/**
 * Escape HTML output
 */
function esc($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Escape attribute
 */
function escAttr($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Get current user
 */
function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

/**
 * Set flash message
 */
function setFlash($message, $type = MESSAGE_SUCCESS) {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

/**
 * Get and clear flash message
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return '₹ ' . number_format($amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'M d, Y') {
    if (!$date) return '-';
    return date($format, strtotime($date));
}

/**
 * Format datetime
 */
function formatDateTime($datetime, $format = 'M d, Y h:i A') {
    if (!$datetime) return '-';
    return date($format, strtotime($datetime));
}

/**
 * Get badge class for stage
 */
function getStageBadgeClass($stage) {
    $classes = [
        'New' => 'bg-blue-100 text-blue-800',
        'Contacted' => 'bg-yellow-100 text-yellow-800',
        'Site Visit' => 'bg-purple-100 text-purple-800',
        'Interested' => 'bg-indigo-100 text-indigo-800',
        'Negotiation' => 'bg-orange-100 text-orange-800',
        'Booked' => 'bg-green-100 text-green-800',
        'Lost' => 'bg-red-100 text-red-800'
    ];
    return $classes[$stage] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Get badge class for booking status
 */
function getBookingBadgeClass($status) {
    $classes = [
        'Pending' => 'bg-yellow-100 text-yellow-800',
        'Confirmed' => 'bg-green-100 text-green-800',
        'Cancelled' => 'bg-red-100 text-red-800'
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Get badge class for unit status
 */
function getUnitBadgeClass($status) {
    $classes = [
        'Available' => 'bg-green-100 text-green-800',
        'Booked' => 'bg-red-100 text-red-800',
        'Unavailable' => 'bg-gray-100 text-gray-800'
    ];
    return $classes[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Pagination
 */
function getPaginationOffset($page, $limit = ITEMS_PER_PAGE) {
    $page = max(1, intval($page ?? 1));
    return ($page - 1) * $limit;
}

/**
 * Get pagination info
 */
function getPaginationInfo($total, $limit = ITEMS_PER_PAGE) {
    $totalPages = ceil($total / $limit);
    $currentPage = max(1, intval($_GET['p'] ?? 1));
    
    return [
        'total' => $total,
        'limit' => $limit,
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'offset' => ($currentPage - 1) * $limit
    ];
}

/**
 * Build pagination links
 */
function buildPaginationLinks($totalPages, $currentPage, $baseUrl) {
    $links = '';
    
    if ($currentPage > 1) {
        $links .= '<a href="' . $baseUrl . '&p=1" class="px-3 py-1 border rounded">First</a> ';
        $links .= '<a href="' . $baseUrl . '&p=' . ($currentPage - 1) . '" class="px-3 py-1 border rounded">Prev</a> ';
    }
    
    for ($i = max(1, $currentPage - 2); $i <= min($totalPages, $currentPage + 2); $i++) {
        if ($i === $currentPage) {
            $links .= '<span class="px-3 py-1 bg-blue-500 text-white rounded">' . $i . '</span> ';
        } else {
            $links .= '<a href="' . $baseUrl . '&p=' . $i . '" class="px-3 py-1 border rounded">' . $i . '</a> ';
        }
    }
    
    if ($currentPage < $totalPages) {
        $links .= '<a href="' . $baseUrl . '&p=' . ($currentPage + 1) . '" class="px-3 py-1 border rounded">Next</a> ';
        $links .= '<a href="' . $baseUrl . '&p=' . $totalPages . '" class="px-3 py-1 border rounded">Last</a>';
    }
    
    return $links;
}

/**
 * Get number words
 */
function numberToWords($num) {
    $ones = ['', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine'];
    $tens = ['', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'];
    $teens = ['ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'];
    
    if ($num < 10) return $ones[$num];
    if ($num < 20) return $teens[$num - 10];
    if ($num < 100) return $tens[floor($num / 10)] . (($num % 10) ? ' ' . $ones[$num % 10] : '');
    if ($num < 1000) return $ones[floor($num / 100)] . ' hundred' . (($num % 100) ? ' ' . numberToWords($num % 100) : '');
    
    return strval($num);
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    if (is_array($input)) {
        return array_map('sanitizeInput', $input);
    }
    return trim(stripslashes($input));
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone
 */
function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

/**
 * Generate random token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}
?>
