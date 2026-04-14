<?php
namespace App\Models;

use App\Core\Database;

class Page {
    private $db;

    public function __construct() {
        $this->db = new Database();
        $this->ensurePagesTable();
    }

    private function ensurePagesTable() {
        $this->db->query("CREATE TABLE IF NOT EXISTS pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL,
            content JSON NOT NULL,
            UNIQUE KEY pages_slug_unique (slug)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $this->db->execute();

        $this->ensureColumn('title', "ALTER TABLE pages ADD COLUMN title VARCHAR(255) NULL");
        $this->ensureColumn('slug', "ALTER TABLE pages ADD COLUMN slug VARCHAR(255) NULL");
        $this->ensureColumn('content', "ALTER TABLE pages ADD COLUMN content JSON NULL");
    }

    private function ensureColumn($column, $alterSql) {
        $this->db->query("SHOW COLUMNS FROM pages LIKE :column");
        $this->db->bind(':column', $column);

        if (!$this->db->single()) {
            $this->db->query($alterSql);
            $this->db->execute();
        }
    }

    public function getAll() {
        $this->db->query("SELECT id, title, slug, content FROM pages ORDER BY id DESC");
        return $this->db->resultSet();
    }

    public function getById($id) {
        $this->db->query("SELECT id, title, slug, content FROM pages WHERE id = :id LIMIT 1");
        $this->db->bind(':id', (int) $id);
        return $this->db->single();
    }

    public function getBySlug($slug) {
        $this->db->query("SELECT id, title, slug, content FROM pages WHERE slug = :slug LIMIT 1");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    public function loadBySlug($slug) {
        $page = $this->getBySlug($slug);
        if (!$page) {
            return null;
        }

        $page['content'] = $this->decodeContent($page['content']);
        return $page;
    }

    public function create($title, $slug, array $content) {
        $this->db->query("INSERT INTO pages (title, slug, content) VALUES (:title, :slug, :content)");
        $this->db->bind(':title', $title);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':content', $this->encodeContent($content));
        $this->db->execute();

        return $this->getById($this->db->lastInsertId());
    }

    public function update($id, $title, $slug, array $content) {
        $this->db->query("UPDATE pages SET title = :title, slug = :slug, content = :content WHERE id = :id");
        $this->db->bind(':title', $title);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':content', $this->encodeContent($content));
        $this->db->bind(':id', (int) $id);
        $this->db->execute();

        return $this->getById($id);
    }

    public function saveBySlug($slug, array $content, $title = null) {
        $json = $this->encodeContent($content);
        $page = $this->getBySlug($slug);

        if ($page) {
            $this->db->query("UPDATE pages SET title = :title, content = :content WHERE slug = :slug");
            $this->db->bind(':title', $title ?: $page['title']);
            $this->db->bind(':content', $json);
            $this->db->bind(':slug', $slug);
            $this->db->execute();

            return $this->getBySlug($slug);
        }

        $this->db->query("INSERT INTO pages (title, slug, content) VALUES (:title, :slug, :content)");
        $this->db->bind(':title', $title ?: $slug);
        $this->db->bind(':slug', $slug);
        $this->db->bind(':content', $json);
        $this->db->execute();

        return $this->getBySlug($slug);
    }

    public function getOrCreateBySlug($slug, $title, array $defaultContent) {
        $page = $this->getBySlug($slug);

        if ($page && !empty($page['content'])) {
            return $page;
        }

        return $this->saveBySlug($slug, $defaultContent, $title);
    }

    public function delete($id) {
        $this->db->query("DELETE FROM pages WHERE id = :id");
        $this->db->bind(':id', (int) $id);
        return $this->db->execute();
    }

    public function deleteBySlug($slug) {
        $this->db->query("DELETE FROM pages WHERE slug = :slug");
        $this->db->bind(':slug', $slug);
        return $this->db->execute();
    }

    private function encodeContent(array $content) {
        $json = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new \InvalidArgumentException('Page content JSON encode error: ' . json_last_error_msg());
        }

        return $json;
    }

    private function decodeContent($json) {
        $content = json_decode((string) $json, true);

        if (!is_array($content)) {
            throw new \InvalidArgumentException('Page content JSON decode error: ' . json_last_error_msg());
        }

        return $content;
    }
}
