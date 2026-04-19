<?php
namespace App\Controllers;

use App\Core\Controller;

class ContactController extends Controller {
    public function __construct() {
        // No models needed for this controller
    }

    public function index() {
        // Check for POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process form
            
            // Sanitize POST data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            
            // Init data
            $data = [
                'title' => 'Liên Hệ',
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'subject' => trim($_POST['subject']),
                'message' => trim($_POST['message']),
                'name_err' => '',
                'email_err' => '',
                'subject_err' => '',
                'message_err' => '',
                'success' => false
            ];
            
            // Validate name
            if (empty($data['name'])) {
                $data['name_err'] = 'Vui lòng nhập tên';
            }
            
            // Validate email
            if (empty($data['email'])) {
                $data['email_err'] = 'Vui lòng nhập email';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $data['email_err'] = 'Email không hợp lệ';
            }
            
            // Validate subject
            if (empty($data['subject'])) {
                $data['subject_err'] = 'Vui lòng nhập tiêu đề';
            }
            
            // Validate message
            if (empty($data['message'])) {
                $data['message_err'] = 'Vui lòng nhập nội dung';
            }
            
            // Make sure no errors
            if (empty($data['name_err']) && empty($data['email_err']) && empty($data['subject_err']) && empty($data['message_err'])) {
                // Here would normally send email, but for now just mark as success
                $data['success'] = true;
                $data['name'] = '';
                $data['email'] = '';
                $data['subject'] = '';
                $data['message'] = '';
            }
            
            $this->viewWithLayout('pages/contact', $data);
        } else {
            // Init data
            $data = [
                'title' => 'Liên Hệ',
                'name' => '',
                'email' => '',
                'subject' => '',
                'message' => '',
                'name_err' => '',
                'email_err' => '',
                'subject_err' => '',
                'message_err' => '',
                'success' => false
            ];
            
            // Load view
            $this->viewWithLayout('pages/contact', $data);
        }
    }
} 