<?php
/**
 * IoTdelivers - Configuration File
 * Database Connection and Global Settings
 */

// Define environment mode
define('ENVIRONMENT', 'production'); // Change to 'development' for debugging

// Enable error reporting in development mode
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Change this in production
define('DB_NAME', 'iotdelivers_db');
define('DB_PORT', 3306);

// Business Information
define('BUSINESS_NAME', 'IoTdelivers');
define('BUSINESS_OWNER', 'James');
define('BUSINESS_EMAIL', 'james@iotdelivers.com');
define('BUSINESS_PHONE', '+254 (your phone number)');
define('BUSINESS_COUNTRY', 'Kenya');

// Currency Settings
define('CURRENCY_CODE', 'KES');
define('CURRENCY_SYMBOL', 'KES ');
define('CURRENCY_DECIMAL', 2);

// Color Scheme
define('PRIMARY_COLOR', '#6A0DAD'); // Purple
define('SECONDARY_COLOR', '#FFFFFF'); // White
define('ACCENT_COLOR', '#FF6B6B'); // Red accent

// Security Settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('PASSWORD_MIN_LENGTH', 8);
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);

// M-Pesa Daraja API Configuration
define('MPESA_CONSUMER_KEY', 'YOUR_CONSUMER_KEY_HERE');
define('MPESA_CONSUMER_SECRET', 'YOUR_CONSUMER_SECRET_HERE');
define('MPESA_BUSINESS_SHORTCODE', '174379'); // Use your test shortcode
define('MPESA_PASSKEY', 'YOUR_PASSKEY_HERE');
define('MPESA_TIMESTAMP', date('YmdHis'));
define('MPESA_BASE_URL', 'https://sandbox.safaricom.co.ke'); // Use production URL in production
define('MPESA_CALLBACK_URL', 'https://yoursite.com/includes/mpesa_callback.php');

// File Upload Settings
define('MAX_UPLOAD_SIZE', 5242880); // 5MB in bytes
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Pagination
define('ITEMS_PER_PAGE', 12);

// Email Settings
define('MAIL_FROM', 'noreply@iotdelivers.com');
define('MAIL_FROM_NAME', 'IoTdelivers');

/**
 * Database Connection Function
 * Returns MySQLi connection object
 */
function createDatabaseConnection() {
    // Create connection
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    // Check connection
    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }
    
    // Set charset
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Create database connection
$conn = createDatabaseConnection();

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => SESSION_TIMEOUT,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set true if using HTTPS
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Helper function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Helper function to format currency
function formatCurrency($amount) {
    return CURRENCY_SYMBOL . number_format($amount, CURRENCY_DECIMAL);
}

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

// Helper function to generate unique order number
function generateOrderNumber() {
    return 'ORD-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
}

// Helper function to generate unique booking number
function generateBookingNumber() {
    return 'BKG-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));
}

// Helper function to log errors
function logError($message) {
    $logFile = __DIR__ . '/../logs/errors.log';
    if (!is_dir(dirname($logFile))) {
        mkdir(dirname($logFile), 0777, true);
    }
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Global error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    if (ENVIRONMENT === 'development') {
        echo "<pre>Error [$errno]: $errstr in $errfile on line $errline</pre>";
    }
    logError("[$errno] $errstr in $errfile on line $errline");
    return true;
});
