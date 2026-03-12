<?php
namespace App\Models;

use App\Core\Database;

class ProductCustomField {
    private $db;
    
    public function __construct() {
        $this->db = new Database();
    }
    
    // Lấy tất cả các trường tùy chỉnh của một sản phẩm
    public function getByProductId($product_id) {
        $this->db->query('SELECT * FROM product_custom_fields WHERE product_id = :product_id ORDER BY display_order ASC, id ASC');
        $this->db->bind(':product_id', $product_id);
        return $this->db->resultSet();
    }
    
    // Thêm trường tùy chỉnh mới
    public function create($data) {
        $this->db->query('INSERT INTO product_custom_fields (product_id, field_title, field_content, display_order) VALUES (:product_id, :field_title, :field_content, :display_order)');
        
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':field_title', $data['field_title']);
        $this->db->bind(':field_content', $data['field_content']);
        $this->db->bind(':display_order', isset($data['display_order']) ? $data['display_order'] : 0);
        
        return $this->db->execute();
    }
    
    // Cập nhật trường tùy chỉnh
    public function update($id, $data) {
        $this->db->query('UPDATE product_custom_fields SET field_title = :field_title, field_content = :field_content, display_order = :display_order WHERE id = :id');
        
        $this->db->bind(':id', $id);
        $this->db->bind(':field_title', $data['field_title']);
        $this->db->bind(':field_content', $data['field_content']);
        $this->db->bind(':display_order', isset($data['display_order']) ? $data['display_order'] : 0);
        
        return $this->db->execute();
    }
    
    // Xóa trường tùy chỉnh
    public function delete($id) {
        $this->db->query('DELETE FROM product_custom_fields WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
    
    // Xóa tất cả trường tùy chỉnh của một sản phẩm
    public function deleteByProductId($product_id) {
        $this->db->query('DELETE FROM product_custom_fields WHERE product_id = :product_id');
        $this->db->bind(':product_id', $product_id);
        return $this->db->execute();
    }
    
    // Lấy trường tùy chỉnh theo ID
    public function getById($id) {
        $this->db->query('SELECT * FROM product_custom_fields WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    // Cập nhật thứ tự hiển thị
    public function updateDisplayOrder($id, $display_order) {
        $this->db->query('UPDATE product_custom_fields SET display_order = :display_order WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->bind(':display_order', $display_order);
        return $this->db->execute();
    }
    
    // Lấy thứ tự hiển thị cao nhất cho một sản phẩm
    public function getMaxDisplayOrder($product_id) {
        $this->db->query('SELECT MAX(display_order) as max_order FROM product_custom_fields WHERE product_id = :product_id');
        $this->db->bind(':product_id', $product_id);
        $result = $this->db->single();
        return $result ? (int)$result['max_order'] : 0;
    }
}