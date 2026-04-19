<?php
/**
 * Redirect to a specific page
 */
function redirect($page) {
    // Use JavaScript redirect to avoid header issues
    $url = URL_ROOT . '/' . $page;
    echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Redirecting...</title>
    <script>
        window.location.href = "' . $url . '";
    </script>
</head>
<body>
    <p>Redirecting to <a href="' . $url . '">' . $url . '</a>...</p>
</body>
</html>';
    exit;
}

/**
 * Clean input data
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags($data));
}

/**
 * Format price with currency
 */
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . 'đ';
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    if(isset($_SESSION['user_id'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    if(isset($_SESSION['admin_id'])) {
        return true;
    } else {
        return false;
    }
}

/**
 * Format date
 */
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

/**
 * Generate a random string
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

/**
 * Upload an image
 */
function uploadImage($file, $directory = 'uploads') {
    // Check if file was uploaded
    if(!isset($file['name']) || $file['error'] != 0) {
        return false;
    }
    
    // Check file type
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if(!in_array($file_ext, $allowed)) {
        return false;
    }
    
    // Generate new filename
    $new_name = generateRandomString() . '.' . $file_ext;
    $upload_path = 'public/img/' . $directory . '/' . $new_name;
    
    // Move uploaded file
    if(move_uploaded_file($file_tmp, $upload_path)) {
        return $directory . '/' . $new_name;
    } else {
        return false;
    }
}

/**
 * Flash message helper
 * Example: flash('register_success', 'You are now registered');
 * Display in view: <?php echo flash('register_success'); ?>
 */
function flash($name = '', $message = '', $type = 'success') {
    if(!empty($name)) {
        // Set flash message
        if(!empty($message) && empty($_SESSION[$name])) {
            if(!empty($_SESSION[$name])) {
                unset($_SESSION[$name]);
            }
            
            if(!empty($_SESSION[$name . '_type'])) {
                unset($_SESSION[$name . '_type']);
            }
            
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_type'] = $type;
        } 
        // Display flash message
        else if(empty($message) && !empty($_SESSION[$name])) {
            $output = '<div class="alert alert-' . $_SESSION[$name . '_type'] . ' alert-dismissible fade show" role="alert">';
            $output .= $_SESSION[$name];
            $output .= '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
            $output .= '</div>';
            
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_type']);
            
            return $output;
        }
    }
    
    return '';
}