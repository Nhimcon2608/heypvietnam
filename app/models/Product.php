<?php
namespace App\Models;

use App\Core\Database;
use App\Helpers\ImageHelper;
use PDO;

class Product {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all products with category information
    public function getAllWithCategory() {
        $this->db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC");
        return $this->db->resultSet();
    }

    // Get all products
    public function getAll() {
        $this->db->query("SELECT * FROM products ORDER BY COALESCE(out_of_stock, 0) ASC, created_at DESC");
        return $this->db->resultSet();
    }

    // Get product by ID
    public function getById($id) {
        $this->db->query("SELECT * FROM products WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Get product by ID with category information
    public function getByIdWithCategory($id) {
        $this->db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Get products by category
    public function getByCategory($category_id) {
        $this->db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = :category_id ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC");
        $this->db->bind(':category_id', $category_id);
        return $this->db->resultSet();
    }

    // Get featured products
    public function getFeatured($limit = 8) {
        $this->db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.featured = 1 ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Get latest products
    public function getLatest($limit = 8) {
        $this->db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT :limit");
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    // Add product
    public function create($data) {
        $query = "INSERT INTO products (name, description, price, category_id, image, featured, shopee_link";
        $values = "VALUES (:name, :description, :price, :category_id, :image, :featured, :shopee_link";

        // Add image_data and mime_type if provided
        if (isset($data['image_data'])) {
            $query .= ", image_data, image_mime_type";
            $values .= ", :image_data, :image_mime_type";
        }

        $query .= ") " . $values . ")";

        $this->db->query($query);

        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':image', $data['image']);
        $this->db->bind(':featured', $data['featured']);
        $this->db->bind(':shopee_link', $data['shopee_link'] ?? '');

        // Bind image data if provided
        if (isset($data['image_data'])) {
            $this->db->bind(':image_data', $data['image_data']);
            $this->db->bind(':image_mime_type', $data['image_mime_type'] ?? 'image/jpeg');
        }

        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    // Get last insert ID
    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    // Update product
    public function update($id, $data) {
        // Build the query based on whether an image is provided
        $query = "UPDATE products SET name = :name, description = :description, price = :price, category_id = :category_id, featured = :featured, shopee_link = :shopee_link";

        if (!empty($data['image'])) {
            $query .= ", image = :image";
        }

        // Add image_data and mime_type if provided
        if (isset($data['image_data'])) {
            $query .= ", image_data = :image_data, image_mime_type = :image_mime_type";
        }

        $query .= " WHERE id = :id";

        $this->db->query($query);

        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':price', $data['price']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':featured', $data['featured']);
        $this->db->bind(':shopee_link', $data['shopee_link'] ?? '');

        if (!empty($data['image'])) {
            $this->db->bind(':image', $data['image']);
        }

        // Bind image data if provided
        if (isset($data['image_data'])) {
            $this->db->bind(':image_data', $data['image_data']);
            $this->db->bind(':image_mime_type', $data['image_mime_type'] ?? 'image/jpeg');
        }

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete product
    public function delete($id) {
        $this->db->query("DELETE FROM products WHERE id = :id");
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Search products
    public function search($term) {
        $this->db->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.name LIKE :term OR p.description LIKE :term ORDER BY COALESCE(p.out_of_stock, 0) ASC, p.created_at DESC");
        $this->db->bind(':term', '%' . $term . '%');
        return $this->db->resultSet();
    }

    // Get total number of products
    public function getCount() {
        $this->db->query("SELECT COUNT(*) as count FROM products");
        $result = $this->db->single();
        return $result['count'];
    }
    
    // Toggle featured status
    public function toggleFeatured($id) {
        $this->db->query("UPDATE products SET featured = NOT featured WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // API tương thích với ProductsController
    
    // Alias cho getAll
    public function getProducts() {
        return $this->getAll();
    }
    
    // Alias cho getById
    public function getProductById($id) {
        return $this->getById($id);
    }
    
    // Alias cho getByCategory
    public function getProductsByCategory($category_id) {
        return $this->getByCategory($category_id);
    }
    
    // Alias cho search
    public function searchProducts($term) {
        return $this->search($term);
    }
    
    // Alias cho getFeatured
    public function getFeaturedProducts($limit = 4) {
        return $this->getFeatured($limit);
    }

    // Get the total number of featured products
    public function getFeaturedCount() {
        $this->db->query("SELECT COUNT(*) as count FROM products WHERE featured = 1");
        $result = $this->db->single();
        return $result['count'];
    }

    // Get total product views (sum of views of all products)
    public function getTotalViews() {
        $this->db->query("SELECT SUM(views) as total_views FROM products");
        $result = $this->db->single();
        return $result['total_views'] ?: 0;
    }
    
    public function incrementViews($id) {
        $this->db->query("UPDATE products SET views = views + 1 WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    // Database transaction methods
    public function beginTransaction() {
        return $this->db->beginTransaction();
    }
    
    public function commit() {
        return $this->db->commit();
    }
    
    public function rollBack() {
        return $this->db->rollBack();
    }
    
    // Delete product sizes
    public function deleteSizes($product_id) {
        $this->db->query("DELETE FROM product_sizes WHERE product_id = :product_id");
        $this->db->bind(':product_id', $product_id);
        return $this->db->execute();
    }

    // Get sizes by product ID
    public function getSizesByProductId($product_id) {
        $this->db->query("SELECT * FROM product_sizes WHERE product_id = :product_id ORDER BY display_order, id");
        $this->db->bind(':product_id', $product_id);
        return $this->db->resultSet();
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
    public function getImageUrl($product_id, $type = 'product') {
        return URL_ROOT . "/image.php?type={$type}&id={$product_id}";
    }
}