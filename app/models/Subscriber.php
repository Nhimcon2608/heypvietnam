<?php
namespace App\Models;

use App\Core\Database;

class Subscriber {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Add a new subscriber
    public function subscribe($email) {
        // Check if email already exists
        $this->db->query("SELECT * FROM subscribers WHERE email = :email");
        $this->db->bind(':email', $email);
        $row = $this->db->single();

        if ($row) {
            return false; // Email already exists
        }

        // Add new subscriber
        $this->db->query("INSERT INTO subscribers (email) VALUES (:email)");
        $this->db->bind(':email', $email);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    // Get all subscribers
    public function getSubscribers() {
        $this->db->query("SELECT * FROM subscribers ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    // Delete a subscriber
    public function deleteSubscriber($id) {
        $this->db->query("DELETE FROM subscribers WHERE id = :id");
        $this->db->bind(':id', $id);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }
} 