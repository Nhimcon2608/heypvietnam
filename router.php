<?php
// Router for PHP built-in server to handle URL rewriting

$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Serve static files directly
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $path)) {
    return false; // Let PHP serve the file directly
}

// For all other requests, route through index.php
if ($path !== '/index.php') {
    // Remove leading slash and set as url parameter
    $url = ltrim($path, '/');
    $_GET['url'] = $url;
}

// Include the main application file
require_once 'index.php';
?>