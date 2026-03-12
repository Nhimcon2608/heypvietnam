<?php
namespace App\Models;

use App\Core\Database;

class Setting {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Lấy tất cả cài đặt
    public function getAllSettings() {
        $this->db->query("SELECT * FROM settings");
        return $this->db->resultSet();
    }
    
    // Lấy setting theo key
    public function getSettingByKey($key) {
        $this->db->query("SELECT * FROM settings WHERE `key` = :key");
        $this->db->bind(':key', $key);
        return $this->db->single();
    }
    
    // Lấy giá trị setting theo key
    public function getSettingValue($key, $default = '') {
        $this->db->query("SELECT `value` FROM settings WHERE `key` = :key");
        $this->db->bind(':key', $key);
        $result = $this->db->single();
        
        // Kiểm tra dữ liệu trả về dạng nào và xử lý phù hợp
        if ($result) {
            // Nếu là object
            if (is_object($result) && isset($result->value)) {
                return $result->value;
            }
            // Nếu là array
            else if (is_array($result) && isset($result['value'])) {
                return $result['value'];
            }
        }
        
        return $default;
    }
    
    // Tạo mới setting
    public function createSetting($key, $value) {
        $this->db->query("INSERT INTO settings (`key`, `value`) VALUES (:key, :value)");
        $this->db->bind(':key', $key);
        $this->db->bind(':value', $value);
        return $this->db->execute();
    }
    
    // Cập nhật setting
    public function updateSetting($key, $value) {
        $this->db->query("UPDATE settings SET `value` = :value WHERE `key` = :key");
        $this->db->bind(':key', $key);
        $this->db->bind(':value', $value);
        return $this->db->execute();
    }
    
    // Xóa setting
    public function deleteSetting($key) {
        $this->db->query("DELETE FROM settings WHERE `key` = :key");
        $this->db->bind(':key', $key);
        return $this->db->execute();
    }
    
    // Cập nhật nhiều setting cùng lúc
    public function updateMultipleSettings($settings) {
        $success = true;
        foreach ($settings as $key => $value) {
            if ($this->getSettingByKey($key)) {
                $result = $this->updateSetting($key, $value);
            } else {
                $result = $this->createSetting($key, $value);
            }
            if (!$result) {
                $success = false;
            }
        }
        return $success;
    }
} 