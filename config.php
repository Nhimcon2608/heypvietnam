<?php
/**
 * HeypVietNam Configuration File
 * Main configuration for the application
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load ignored local overrides or environment variables for sensitive settings.
$local_config_path = __DIR__ . '/config.local.php';
$config_overrides = [];
if (is_file($local_config_path)) {
    $config_overrides = require $local_config_path;
    if (!is_array($config_overrides)) {
        $config_overrides = [];
    }
}

function getConfigValue(array $configOverrides, $key, $default = '')
{
    $environmentValue = getenv($key);
    if ($environmentValue !== false && $environmentValue !== '') {
        return $environmentValue;
    }

    return array_key_exists($key, $configOverrides) ? $configOverrides[$key] : $default;
}

// Check if current URL contains 'localhost/heypvietnam' or other pattern
$temp_current_url = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$temp_document_root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';

// Enhanced environment detection for initial setup
$temp_is_local_development = (
    // Check for XAMPP installation paths
    strpos(__DIR__, 'xampp') !== false ||
    strpos(__DIR__, 'XAMPP') !== false ||
    strpos(__DIR__, 'htdocs') !== false ||
    strpos(__DIR__, '/Applications/') !== false ||
    // Check URL patterns
    strpos($temp_current_url, 'localhost') !== false ||
    strpos($temp_current_url, '127.0.0.1') !== false ||
    strpos($temp_current_url, '.local') !== false ||
    // Check document root patterns
    strpos($temp_document_root, 'xampp') !== false ||
    strpos($temp_document_root, 'XAMPP') !== false ||
    strpos($temp_document_root, 'htdocs') !== false ||
    strpos($temp_document_root, 'Applications') !== false ||
    // Check server software
    (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false && strpos($temp_document_root, '/Applications/') !== false)
);

// Error reporting based on environment
if ($temp_is_local_development) {
    // Development environment
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    define('ENVIRONMENT', 'development');
} else {
    // Production environment
    error_reporting(E_ALL); // Bật debug cho production để kiểm tra lỗi
    ini_set('display_errors', 1); // Hiển thị lỗi
    define('ENVIRONMENT', 'development'); // Chuyển sang development để debug
    // Sau khi fix xong, khôi phục lại:
    // error_reporting(0);
    // ini_set('display_errors', 0);
    // define('ENVIRONMENT', 'production');
}

// Check if current URL contains 'localhost/heypvietnam' or other pattern
$current_url = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$server_path = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
$document_root = isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : '';

// Determine if we're using HTTPS
$protocol = 'http://';
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $protocol = 'https://';
} elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $protocol = 'https://';
} elseif (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) {
    $protocol = 'https://';
}

// Enhanced environment detection
$is_local_development = (
    // Check for XAMPP installation paths using __DIR__
    strpos(__DIR__, 'xampp') !== false ||
    strpos(__DIR__, 'XAMPP') !== false ||
    strpos(__DIR__, 'htdocs') !== false ||
    strpos(__DIR__, '/Applications/') !== false ||
    // Check URL patterns
    strpos($current_url, 'localhost') !== false ||
    strpos($current_url, '127.0.0.1') !== false ||
    strpos($current_url, '.local') !== false ||
    // Check document root patterns
    strpos($document_root, 'xampp') !== false ||
    strpos($document_root, 'XAMPP') !== false ||
    strpos($document_root, 'htdocs') !== false ||
    strpos($document_root, 'Applications') !== false ||
    // Check server software
    (isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false && strpos($document_root, '/Applications/') !== false)
);

if ($is_local_development) {
    // Development environment (chạy trên máy local)
    define('DB_HOST', getConfigValue($config_overrides, 'DB_HOST', 'localhost'));
    define('DB_USER', getConfigValue($config_overrides, 'DB_USER', 'root'));
    define('DB_PASS', getConfigValue($config_overrides, 'DB_PASS', ''));
    define('DB_NAME', getConfigValue($config_overrides, 'DB_NAME', 'heypvietnam'));
} else {
    // Production credentials should come from config.local.php or environment variables.
    define('DB_HOST', getConfigValue($config_overrides, 'DB_HOST', '127.0.0.1:3306'));
    define('DB_USER', getConfigValue($config_overrides, 'DB_USER', ''));
    define('DB_PASS', getConfigValue($config_overrides, 'DB_PASS', ''));
    define('DB_NAME', getConfigValue($config_overrides, 'DB_NAME', 'u386182091_heypvietnam'));
}

define('DB_CHARSET', 'utf8mb4');

// App configuration
define('SITE_NAME', 'HeypVietNam');
define('APP_ROOT', dirname(__FILE__));
define('URL_SUBFOLDER', ''); // Nếu có subfolder (ví dụ: /app), thay bằng '/app'

// Dynamic URL_ROOT based on environment
if ($is_local_development) {
    // Development environment
    if (strpos($current_url, 'localhost') !== false) {
        define('URL_ROOT', $protocol . $current_url . '/heypvietnam');
    } else {
        // For local development with custom domain mapping
        define('URL_ROOT', $protocol . $current_url);
    }
} else {
    // Production environment
    define('URL_ROOT', 'https://heypvietnam.com');
}

// File upload configuration
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_PATH', APP_ROOT . '/public/img/products/');
define('VIDEO_UPLOAD_PATH', APP_ROOT . '/public/videos/products/');
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'avi', 'mov', 'wmv']);

// Site colors
define('COLOR_PRIMARY_BG', '#F5F5F0');
define('COLOR_SECONDARY_BG_1', '#A8B5A2'); // sage green
define('COLOR_SECONDARY_BG_2', '#D4C9B7'); // taupe
define('COLOR_HEADING', '#333333');
define('COLOR_TEXT', '#666666');
define('COLOR_ACCENT', '#FFFFFF');

// Security configuration
define('SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_NAME', 'csrf_token');

// Pagination
define('PRODUCTS_PER_PAGE', 20);
define('ADMIN_PRODUCTS_PER_PAGE', 50);

// Debug mode
define('DEBUG', ENVIRONMENT === 'development');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Auto-create upload directories if they don't exist
if (!is_dir(UPLOAD_PATH)) {
    if (!@mkdir(UPLOAD_PATH, 0755, true) && !is_dir(UPLOAD_PATH)) {
        // Silently fail if we can't create the directory
        error_log("Warning: Could not create upload directory: " . UPLOAD_PATH);
    }
}
if (!is_dir(VIDEO_UPLOAD_PATH)) {
    if (!@mkdir(VIDEO_UPLOAD_PATH, 0755, true) && !is_dir(VIDEO_UPLOAD_PATH)) {
        // Silently fail if we can't create the directory
        error_log("Warning: Could not create video upload directory: " . VIDEO_UPLOAD_PATH);
    }
}

// Helper function to generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Helper function to verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Helper function to check if user is admin
function isAdmin() {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

// Helper function to get absolute file path
function getFilePath($relativePath) {
    return APP_ROOT . '/' . ltrim($relativePath, '/');
}

// Helper function to get public file path (for uploads, images, etc.)
function getPublicPath($relativePath) {
    return APP_ROOT . '/public/' . ltrim($relativePath, '/');
}

// Debug function to check environment detection (for troubleshooting)
function debugEnvironment() {
    if (defined('DEBUG') && DEBUG) {
        $info = [
            'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'N/A',
            'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
            'SERVER_SOFTWARE' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'ENVIRONMENT' => ENVIRONMENT ?? 'N/A',
            'DB_HOST' => DB_HOST ?? 'N/A',
            'DB_NAME' => DB_NAME ?? 'N/A',
            'URL_ROOT' => URL_ROOT ?? 'N/A',
            'APP_ROOT' => APP_ROOT ?? 'N/A'
        ];
        
        echo "<!-- Environment Debug Info: " . json_encode($info, JSON_PRETTY_PRINT) . " -->\n";
    }
}
