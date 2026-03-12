<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class ProductVideo {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all videos for a product
    public function getByProductId($product_id) {
        $this->db->query("SELECT * FROM product_videos WHERE product_id = :product_id ORDER BY display_order ASC, created_at ASC");
        $this->db->bind(':product_id', $product_id);
        return $this->db->resultSet();
    }

    // Get video by ID
    public function getById($id) {
        $this->db->query("SELECT * FROM product_videos WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Add new video to product
    public function add($product_id, $video_filename, $video_type = 'mp4', $display_order = 0) {
        $this->db->query("INSERT INTO product_videos (product_id, video_filename, video_type, display_order) VALUES (:product_id, :video_filename, :video_type, :display_order)");
        $this->db->bind(':product_id', $product_id);
        $this->db->bind(':video_filename', $video_filename);
        $this->db->bind(':video_type', $video_type);
        $this->db->bind(':display_order', $display_order);
        return $this->db->execute();
    }

    // Update video (primarily used for changing display order)
    public function update($id, $data) {
        $this->db->query("UPDATE product_videos SET display_order = :display_order WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':display_order', $data['display_order']);
        return $this->db->execute();
    }

    // Delete video
    public function delete($id) {
        $this->db->query("DELETE FROM product_videos WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Delete all videos for a product
    public function deleteByProductId($product_id) {
        $this->db->query("DELETE FROM product_videos WHERE product_id = :product_id");
        $this->db->bind(':product_id', $product_id);
        return $this->db->execute();
    }

    // Set video as main/first video (display_order = 0)
    public function setAsMainVideo($id, $product_id) {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // First reset all product videos to higher display order
            $this->db->query("UPDATE product_videos SET display_order = display_order + 1 WHERE product_id = :product_id");
            $this->db->bind(':product_id', $product_id);
            $this->db->execute();
            
            // Then set the selected video as main
            $this->db->query("UPDATE product_videos SET display_order = 0 WHERE id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // Commit transaction
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            return false;
        }
    }

    // Get the main/first video for a product
    public function getMainVideo($product_id) {
        $this->db->query("SELECT * FROM product_videos WHERE product_id = :product_id ORDER BY display_order ASC, created_at ASC LIMIT 1");
        $this->db->bind(':product_id', $product_id);
        return $this->db->single();
    }

    // Count videos for a product
    public function countByProductId($product_id) {
        $this->db->query("SELECT COUNT(*) as count FROM product_videos WHERE product_id = :product_id");
        $this->db->bind(':product_id', $product_id);
        $result = $this->db->single();
        return $result['count'];
    }

    // Check if video file type is allowed
    public function isAllowedVideoType($filename) {
        $allowed_types = ['mp4', 'webm', 'avi', 'mov'];
        $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($file_extension, $allowed_types);
    }

    // Get video file extension
    public function getVideoType($filename) {
        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}
?>
