<?php
/**
 * Main App Class
 * Handles URL routing
 */
namespace App\Core;

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // Check if this is admin section (could be at index 0 or 1 depending on URL structure)
        $adminIndex = array_search('admin', $url);
        if($adminIndex !== false) {
            $this->controller = 'AdminController';

            // Remove everything before and including 'admin'
            $url = array_slice($url, $adminIndex + 1);
            $url = array_values($url); // Re-index array

            // Get the controller class name with namespace
            $controllerClass = '\\App\\Controllers\\' . $this->controller;

            // Require the controller file
            require_once 'app/controllers/' . $this->controller . '.php';

            // Handle nested admin routes
            if(isset($url[0])) {
                $section = $url[0];

                if(isset($url[1])) {
                    $action = $url[1];
                    $sectionSingular = $section;
                    if (substr($section, -3) === 'ies') {
                        $sectionSingular = substr($section, 0, -3) . 'y';
                    } elseif (substr($section, -1) === 's') {
                        $sectionSingular = substr($section, 0, -1);
                    }
                    $methodName = $action . ucfirst($sectionSingular);

                    if (method_exists($controllerClass, $methodName)) {
                        $this->method = $methodName;
                        // Params start from index 2 (e.g., the ID)
                        if (isset($url[2])) {
                            $this->params = array_slice($url, 2);
                        } else {
                            $this->params = [];
                        }

                    } else {
                        // Fallback to section method with all remaining params
                        $this->method = $section;
                        $this->params = array_slice($url, 1);

                    }
                } else {
                    if (method_exists($controllerClass, $section)) {
                        $this->method = $section;
                        $this->params = [];
                    }
                }
            }

            // Instantiate controller
            $this->controller = new $controllerClass();

            // Debug log
            error_log("=== ROUTING DEBUG ===");
            error_log("Controller: " . get_class($this->controller));
            error_log("Method: " . $this->method);
            error_log("Params: " . print_r($this->params, true));
            error_log("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);

            // Call the method with params
            call_user_func_array([$this->controller, $this->method], $this->params);
            return;
        }
        
        // User section (default)
        if(isset($url[0])) {
            $requestedController = ucwords($url[0]) . 'Controller';
            if (file_exists('app/controllers/' . $requestedController . '.php')) {
                $this->controller = $requestedController;
                unset($url[0]);
            } else {
                http_response_code(404);
                echo 'Trang không tồn tại';
                return;
            }
        }
        
        // Require the controller
        require_once 'app/controllers/' . $this->controller . '.php';
        
        // Determine the full controller class name with namespace
        $controllerClass = '\\App\\Controllers\\' . $this->controller;
        
        // Instantiate controller
        $this->controller = new $controllerClass();
        
        // Check for method in URL
        if(isset($url[1])) {
            if(method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        
        // Get params from URL
        $this->params = $url ? array_values($url) : [];
        
        // Call the method with params
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    /**
     * Parse URL into controller, method and params
     */
    public function parseUrl() {
        if(isset($_GET['url']) && !empty($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            
            // Kiểm tra xem có phải là chuỗi hợp lệ không trước khi explode
            if(is_string($url) && !empty($url)) {
                $url = explode('/', $url);
                return $url;
            }
        }
        return [];
    }
} 
