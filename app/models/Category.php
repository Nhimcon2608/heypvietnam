<?php
namespace App\Models;

use App\Core\Database;

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Get all categories
    public function getAll() {
        $this->db->query("SELECT * FROM categories ORDER BY name");
        return $this->db->resultSet();
    }

    // Get category by ID
    public function getById($id) {
        $this->db->query("SELECT * FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Add a category
    public function create($data) {
        $this->db->query("INSERT INTO categories (name, description, image) VALUES (:name, :description, :image)");
        // Bind values
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':image', $data['image']);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Update a category
    public function update($id, $data) {
        // Always update image field, even if empty
        $this->db->query("UPDATE categories SET name = :name, description = :description, image = :image WHERE id = :id");

        // Bind values
        $this->db->bind(':id', $id);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':description', $data['description']);
        $this->db->bind(':image', isset($data['image']) ? $data['image'] : '');

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Delete a category
    public function delete($id) {
        $this->db->query("DELETE FROM categories WHERE id = :id");
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get total number of categories
    public function getCount() {
        $this->db->query("SELECT COUNT(*) as count FROM categories");
        $result = $this->db->single();
        return $result['count'];
    }
    
    // Get categories with product count
    public function getCategoriesWithProductCount() {
        $this->db->query("SELECT c.*, COUNT(p.id) as product_count 
                          FROM categories c 
                          LEFT JOIN products p ON c.id = p.category_id 
                          GROUP BY c.id 
                          ORDER BY c.name");
        return $this->db->resultSet();
    }
} 