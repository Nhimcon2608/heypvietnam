<?php
namespace App\Models;

use App\Core\Database;

class Admin {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
        
        // Tự động kiểm tra và tạo tài khoản admin mặc định nếu cần
        $this->ensureAdminExists();
    }
    
    // Get Admin by ID
    public function getById($id) {
        $this->db->query("SELECT * FROM admins WHERE id = :id");
        $this->db->bind(':id', $id);
        
        return $this->db->single();
    }
    
    // Get Admin by Username
    public function getByUsername($username) {
        try {
        $this->db->query("SELECT * FROM admins WHERE username = :username");
        $this->db->bind(':username', $username);
        
            $result = $this->db->single();
            
            // Debug message if needed
            if(defined('DEBUG') && DEBUG === true) {
                error_log("Admin lookup for username '$username': " . ($result ? 'found' : 'not found'));
            }
            
            return $result;
        } catch (\Exception $e) {
            if(defined('DEBUG') && DEBUG === true) {
                error_log("Error in getByUsername: " . $e->getMessage());
            }
            return false;
        }
    }
    
    // Create new Admin
    public function create($data) {
        $this->db->query("INSERT INTO admins (name, email, username, password) VALUES (:name, :email, :username, :password)");
        
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        
        return $this->db->execute();
    }
    
    // Update Admin Password
    public function updatePassword($id, $password) {
        $this->db->query("UPDATE admins SET password = :password WHERE id = :id");
        
        $this->db->bind(':id', $id);
        $this->db->bind(':password', $password);
        
        return $this->db->execute();
    }
    
    // Update Admin Info
    public function updateInfo($id, $data) {
        $this->db->query("UPDATE admins SET name = :name, email = :email, username = :username WHERE id = :id");
        
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':username', $data['username']);
        
        return $this->db->execute();
    }
    
    // Check if any admin exists
    public function adminExists() {
        $this->db->query("SELECT COUNT(*) as count FROM admins");
        $result = $this->db->single();
        
        return $result['count'] > 0;
    }
    
    // Get all Admins
    public function getAll() {
        $this->db->query("SELECT id, name, email, username, created_at FROM admins");
        
        return $this->db->resultSet();
    }
    
    // Kiểm tra và tạo admin mặc định nếu cần
    private function ensureAdminExists() {
        try {
            if (!$this->adminExists()) {
                // Tạo tài khoản admin mặc định nếu chưa có admin nào
                $defaultAdmin = [
                    'name' => 'Admin',
                    'email' => 'admin@heypvietnam.com',
                    'username' => 'admin',
                    'password' => 'admin123'
                ];
                
                $this->create($defaultAdmin);
                
                if(defined('DEBUG') && DEBUG === true) {
                    error_log("Created default admin account with username: admin, password: admin123");
                }
            }
        } catch (\Exception $e) {
            if(defined('DEBUG') && DEBUG === true) {
                error_log("Error ensuring admin exists: " . $e->getMessage());
            }
        }
    }
} 