<?php
namespace App\Models;

use App\Core\Database;
use App\Helpers\ImageHelper;
use PDO;

class ProductImage {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all images for a product
    public function getByProductId($product_id) {
        $this->db->query("SELECT * FROM product_images WHERE product_id = :product_id ORDER BY display_order ASC, created_at ASC");
        $this->db->bind(':product_id', $product_id);
        return $this->db->resultSet();
    }

    // Get image by ID
    public function getById($id) {
        $this->db->query("SELECT * FROM product_images WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Add new image to product
    public function add($product_id, $image_filename, $display_order = 0, $image_data = null, $image_mime_type = null) {
        $query = "INSERT INTO product_images (product_id, image_filename, display_order";
        $values = "VALUES (:product_id, :image_filename, :display_order";

        // Add image_data and mime_type if provided
        if ($image_data !== null) {
            $query .= ", image_data, image_mime_type";
            $values .= ", :image_data, :image_mime_type";
        }

        $query .= ") " . $values . ")";

        $this->db->query($query);
        $this->db->bind(':product_id', $product_id);
        $this->db->bind(':image_filename', $image_filename);
        $this->db->bind(':display_order', $display_order);

        // Bind image data if provided
        if ($image_data !== null) {
            $this->db->bind(':image_data', $image_data);
            $this->db->bind(':image_mime_type', $image_mime_type ?? 'image/jpeg');
        }

        return $this->db->execute();
    }

    // Update image (primarily used for changing display order)
    public function update($id, $data) {
        $this->db->query("UPDATE product_images SET display_order = :display_order WHERE id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':display_order', $data['display_order']);
        return $this->db->execute();
    }

    // Delete image
    public function delete($id) {
        $this->db->query("DELETE FROM product_images WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Delete all images for a product
    public function deleteByProductId($product_id) {
        $this->db->query("DELETE FROM product_images WHERE product_id = :product_id");
        $this->db->bind(':product_id', $product_id);
        return $this->db->execute();
    }

    // Set image as main/first image (display_order = 0)
    public function setAsMainImage($id, $product_id) {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // First reset all product images to higher display order
            $this->db->query("UPDATE product_images SET display_order = display_order + 1 WHERE product_id = :product_id");
            $this->db->bind(':product_id', $product_id);
            $this->db->execute();
            
            // Then set the selected image as main
            $this->db->query("UPDATE product_images SET display_order = 0 WHERE id = :id");
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

    // Get the main/first image for a product
    public function getMainImage($product_id) {
        $this->db->query("SELECT * FROM product_images WHERE product_id = :product_id ORDER BY display_order ASC, created_at ASC LIMIT 1");
        $this->db->bind(':product_id', $product_id);
        return $this->db->single();
    }

    // Count images for a product
    public function countByProductId($product_id) {
        $this->db->query("SELECT COUNT(*) as count FROM product_images WHERE product_id = :product_id");
        $this->db->bind(':product_id', $product_id);
        $result = $this->db->single();
        return $result['count'];
    }

    // Helper function to process uploaded image
    public function processImageUpload($file) {
        try {
            $result = ImageHelper::processUploadedImage($file);

            // Validate final image size for database storage
            ImageHelper::validateImageSize($result['data']);

            return [
                'filename' => time() . '_' . basename($file['name']),
                'data' => $result['data'],
                'mime_type' => $result['mime_type']
            ];
        } catch (\Exception $e) {
            error_log("Image processing error: " . $e->getMessage());
            throw $e;
        }
    }

    // Get image URL for display
    public function getImageUrl($image_id) {
        return URL_ROOT . "/image.php?type=additional&id={$image_id}";
    }
}