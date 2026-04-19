<?php
namespace App\Controllers;

use App\Core\Controller;

class SubscribeController extends Controller {
    private $subscriberModel;
    
    public function __construct() {
        $this->subscriberModel = $this->model('Subscriber');
    }
    
    public function index() {
        // Handle subscription form submission
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            $email = trim($_POST['email']);
            
            // Validate email
            if (empty($email)) {
                $_SESSION['subscribe_message'] = 'Vui lòng nhập email';
                $_SESSION['subscribe_class'] = 'error';
                redirect('');
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $_SESSION['subscribe_message'] = 'Email không hợp lệ';
                $_SESSION['subscribe_class'] = 'error';
                redirect('');
            }
            
            // Check if already subscribed
            if (!$this->subscriberModel->subscribe($email)) {
                $_SESSION['subscribe_message'] = 'Email này đã đăng ký';
                $_SESSION['subscribe_class'] = 'error';
                redirect('');
            } else {
                $_SESSION['subscribe_message'] = 'Cảm ơn bạn đã đăng ký nhận tin!';
                $_SESSION['subscribe_class'] = 'success';
                redirect('');
            }
        } else {
            redirect('');
        }
    }
} 