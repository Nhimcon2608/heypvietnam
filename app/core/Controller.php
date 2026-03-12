<?php
/**
 * Base Controller
 * Loads models and views
 */
namespace App\Core;

class Controller {
    /**
     * Constructor
     */
    public function __construct() {
        // Empty constructor
    }

    /**
     * Load model
     */
    protected function model($model) {
        // Require the model file first
        $modelFile = 'app/models/' . $model . '.php';
        if(file_exists($modelFile)) {
            require_once $modelFile;
        }
        
        // Instantiate model with namespace
        $modelClass = '\\App\\Models\\' . $model;
        
        // Check if class exists
        if(class_exists($modelClass)) {
            return new $modelClass();
        } else {
            die('Model class does not exist: ' . $modelClass);
        }
    }

    /**
     * Load view
     */
    protected function view($view, $data = []) {
        // Extract data array to variables
        if (!empty($data)) {
            extract($data);
        }

        // Check for view file
        if(file_exists('app/views/' . $view . '.php')) {
            require_once 'app/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }

    /**
     * Load view with layout
     */
    protected function viewWithLayout($view, $data = [], $layout = 'main') {
        // Start output buffering
        ob_start();
        
        // Load the view
        if(file_exists('app/views/' . $view . '.php')) {
            require_once 'app/views/' . $view . '.php';
        } else {
            die('View does not exist: ' . $view);
        }
        
        // Get contents of the view
        $content = ob_get_clean();
        
        // Check which layout to use
        if ($layout === 'admin') {
            // Use admin layout
            if(!file_exists('app/views/layouts/admin_header.php')) {
                die('Admin header layout does not exist');
            }
            
            if(!file_exists('app/views/layouts/admin_footer.php')) {
                die('Admin footer layout does not exist');
            }
            
            require_once 'app/views/layouts/admin_header.php';
            echo $content;
            require_once 'app/views/layouts/admin_footer.php';
        } else {
            // Use main layout
            if(!file_exists('app/views/layouts/main.php')) {
                die('Main layout does not exist');
            }
            
            require_once 'app/views/layouts/main.php';
        }
    }
    
    /**
     * Get the current method being called
     */
    protected function getMethod() {
        $backtrace = debug_backtrace();
        
        // Skip the current method and look for the calling method
        foreach ($backtrace as $trace) {
            if (isset($trace['class']) && $trace['class'] === get_class($this) && $trace['function'] !== 'getMethod') {
                return $trace['function'];
            }
        }
        
        // If we can't determine the method from backtrace, try using URL
        if(isset($_GET['url']) && !empty($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            
            // Kiểm tra xem $url có phải chuỗi hợp lệ trước khi explode
            if(is_string($url) && !empty($url)) {
                $url = explode('/', $url);
                
                // If we're in admin section and have a second parameter, it's the method
                if(isset($url[0]) && $url[0] == 'admin' && isset($url[1])) {
                    return $url[1];
                }
            }
        }
        
        // Default to index
        return 'index';
    }
}