<?php
// Image serving endpoint - serves images from database
// Load configuration
require_once __DIR__ . '/config.php';

// Get parameters
$type = $_GET['type'] ?? ''; // 'product', 'additional', 'size'
$id = $_GET['id'] ?? '';

if (empty($type) || empty($id)) {
    // Return default placeholder image
    header('Content-Type: image/svg+xml');
    echo '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg"><rect width="100%" height="100%" fill="#f0f0f0"/><text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999">No Image</text></svg>';
    exit;
}

try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $image_data = null;
    $mime_type = 'image/jpeg'; // default
    $file_path = null; // fallback file on disk

    switch ($type) {
        case 'product':
            // Get main product image
            $stmt = $db->prepare("SELECT image, image_data, image_mime_type FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['image_data'])) {
                $image_data = $result['image_data'];
                $mime_type = $result['image_mime_type'] ?: 'image/jpeg';
            } elseif ($result && !empty($result['image'])) {
                // Fallback to file stored on disk
                $file_path = APP_ROOT . '/public/img/products/' . $result['image'];
            }
            break;

        case 'main':
            // Get main product image (same as 'product' type)
            $stmt = $db->prepare("SELECT image, image_data, image_mime_type FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['image_data'])) {
                $image_data = $result['image_data'];
                $mime_type = $result['image_mime_type'] ?: 'image/jpeg';
            } elseif ($result && !empty($result['image'])) {
                $file_path = APP_ROOT . '/public/img/products/' . $result['image'];
            }
            break;

        case 'additional':
            // Get additional product image
            $stmt = $db->prepare("SELECT image_filename, image_data, image_mime_type FROM product_images WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['image_data'])) {
                $image_data = $result['image_data'];
                $mime_type = $result['image_mime_type'] ?: 'image/jpeg';
            } elseif ($result && !empty($result['image_filename'])) {
                $file_path = APP_ROOT . '/public/img/products/' . $result['image_filename'];
            }
            break;

        case 'size':
            // Get size image
            $stmt = $db->prepare("SELECT image, image_data, image_mime_type FROM product_sizes WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && !empty($result['image_data'])) {
                $image_data = $result['image_data'];
                $mime_type = $result['image_mime_type'] ?: 'image/jpeg';
            } elseif ($result && !empty($result['image'])) {
                $file_path = APP_ROOT . '/public/img/products/sizes/' . $result['image'];
            }
            break;
    }

    // If DB blob not available, try filesystem fallback
    if (!$image_data && $file_path && file_exists($file_path)) {
        $image_data = file_get_contents($file_path);
        // Guess mime type from extension
        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp'
        ];
        if (isset($map[$ext])) {
            $mime_type = $map[$ext];
        }
    }

    if ($image_data) {
        // Set appropriate headers
        header('Content-Type: ' . $mime_type);
        header('Content-Length: ' . strlen($image_data));
        header('Cache-Control: public, max-age=31536000'); // Cache for 1 year
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
        
        // Output image data
        echo $image_data;
    } else {
        // Return placeholder image
        header('Content-Type: image/svg+xml');
        header('Cache-Control: public, max-age=3600'); // Cache for 1 hour
        echo '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
                <rect width="100%" height="100%" fill="#f8f9fa" stroke="#dee2e6"/>
                <text x="50%" y="45%" text-anchor="middle" dy=".3em" fill="#6c757d" font-family="Arial, sans-serif" font-size="16">No Image</text>
                <text x="50%" y="55%" text-anchor="middle" dy=".3em" fill="#adb5bd" font-family="Arial, sans-serif" font-size="12">Available</text>
              </svg>';
    }

} catch(PDOException $e) {
    // Log the error for debugging
    error_log("Image.php Database Error: " . $e->getMessage() . " for type=$type, id=$id");
    
    // Return error placeholder
    header('Content-Type: image/svg+xml');
    header('HTTP/1.1 500 Internal Server Error');
    echo '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
            <rect width="100%" height="100%" fill="#f8d7da" stroke="#f5c6cb"/>
            <text x="50%" y="45%" text-anchor="middle" dy=".3em" fill="#721c24" font-family="Arial, sans-serif" font-size="16">Error</text>
            <text x="50%" y="55%" text-anchor="middle" dy=".3em" fill="#856404" font-family="Arial, sans-serif" font-size="12">Loading Image</text>
          </svg>';
} catch(Exception $e) {
    // Log any other errors
    error_log("Image.php General Error: " . $e->getMessage() . " for type=$type, id=$id");
    
    // Return error placeholder
    header('Content-Type: image/svg+xml');
    header('HTTP/1.1 500 Internal Server Error');
    echo '<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
            <rect width="100%" height="100%" fill="#f8d7da" stroke="#f5c6cb"/>
            <text x="50%" y="45%" text-anchor="middle" dy=".3em" fill="#721c24" font-family="Arial, sans-serif" font-size="16">Error</text>
            <text x="50%" y="55%" text-anchor="middle" dy=".3em" fill="#856404" font-family="Arial, sans-serif" font-size="12">System Error</text>
          </svg>';
}
?>
