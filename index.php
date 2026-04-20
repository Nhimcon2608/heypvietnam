<?php
// Clean any existing output buffers and start fresh
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

// Display all PHP errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session
session_start();

// Set custom error handler - but not for AJAX requests
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Check if this is an AJAX request
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
              strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    // Check if Content-Type is JSON (for fetch requests)
    $isJsonRequest = isset($_SERVER['CONTENT_TYPE']) &&
                     strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false;

    if ($isAjax || $isJsonRequest) {
        // For AJAX/JSON requests, return JSON error
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Error: [$errno] $errstr in $errfile on line $errline"]);
        exit;
    }

    // For regular requests, show HTML error
    echo "<h2>PHP Error:</h2>";
    echo "<p><strong>Error:</strong> [$errno] $errstr</p>";
    echo "<p><strong>File:</strong> $errfile on line $errline</p>";

    // Print a stack trace
    echo "<h3>Stack Trace:</h3>";
    echo "<pre>";
    debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
    echo "</pre>";

    return true;
}
set_error_handler("customErrorHandler");

// Entry point for the application
require_once 'config.php';
require_once 'app/helpers/helpers.php';
require_once 'app/helpers/LandingPageRenderer.php';
require_once 'app/helpers/LandingPageDefaults.php';
require_once 'app/helpers/FooterSettings.php';
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Database.php';

// Load all models
foreach ([
    'app/models/Admin.php',
    'app/models/Subscriber.php',
    'app/models/Setting.php',
    'app/models/Page.php'
] as $modelFile) {
    if (file_exists($modelFile)) {
        require_once $modelFile;
    }
}

try {
    // Initialize the application
    $app = new \App\Core\App();
} catch (Exception $e) {
    echo "<div style='background-color: #f8d7da; color: #721c24; padding: 20px; margin: 20px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
    echo "<h2>Lỗi Ứng Dụng:</h2>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . " on line " . $e->getLine() . "</p>";
    echo "<h3>Chi tiết lỗi:</h3>";
    echo "<pre style='background-color: #f2f2f2; padding: 15px; border-radius: 3px;'>" . $e->getTraceAsString() . "</pre>";
    echo "</div>";
}
