<?php
/**
 * PDO Database Class
 * Connect to database
 * Create prepared statements
 * Bind values
 * Return rows and results
 */
namespace App\Core;

use PDO;
use PDOException;
use Exception;

class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;

    private $dbh;
    private $stmt;
    private $error;

    public function __construct() {
        $this->connect();
    }

    // Connect to database with improved error handling
    private function connect() {
        // For x10hosting, use localhost directly (don't change to 127.0.0.1)
        // x10hosting may not support 127.0.0.1
        $host = $this->host;

        // Check if we're on x10hosting
        $is_x10hosting = (strpos($_SERVER['HTTP_HOST'] ?? '', 'x10.mx') !== false);

        if ($is_x10hosting) {
            // x10hosting specific settings
            $dsn = 'mysql:host=' . $host . ';dbname=' . $this->dbname;
            $options = array(
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_TIMEOUT => 15 // Shorter timeout for shared hosting
            );
        } else {
            // Local development settings
            $host = ($this->host === 'localhost') ? '127.0.0.1' : $this->host;
            $dsn = 'mysql:host=' . $host . ';port=3307;dbname=' . $this->dbname;
            $options = array(
                PDO::ATTR_PERSISTENT => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                PDO::ATTR_TIMEOUT => 30
            );
        }

        // Create PDO instance
        try {
            if ($is_x10hosting) {
                // For x10hosting, database should already exist, connect directly
                $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            } else {
                // For local development, check and create database if needed
                $tempdb = new PDO('mysql:host=' . $host . ';port=3307', $this->user, $this->pass);
                $tempdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Check if database exists, create if not
                $result = $tempdb->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $this->dbname . "'");
                if (!$result->fetchColumn()) {
                    // Database doesn't exist, create it
                    $tempdb->exec("CREATE DATABASE IF NOT EXISTS `" . $this->dbname . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
                }

                // Now connect to the specific database
                $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
            }

            // Set MySQL session variables to prevent timeout issues
            try {
                if ($is_x10hosting) {
                    // x10hosting may have restrictions on session variables
                    $this->dbh->exec("SET SESSION wait_timeout = 300");
                    $this->dbh->exec("SET SESSION interactive_timeout = 300");
                } else {
                    // Local development - more generous timeouts
                    $this->dbh->exec("SET SESSION wait_timeout = 600");
                    $this->dbh->exec("SET SESSION interactive_timeout = 600");
                    $this->dbh->exec("SET SESSION net_read_timeout = 60");
                    $this->dbh->exec("SET SESSION net_write_timeout = 60");
                }
            } catch(PDOException $e) {
                // Log but don't fail on session variable errors
                error_log("Warning: Could not set some MySQL session variables: " . $e->getMessage());
            }

            // Check if required tables exist, create if not
            $this->ensureRequiredTablesExist();

        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Database connection error: " . $this->error);
            throw new Exception("Database connection error: " . $this->error);
        }
    }

    // Check if connection is alive and reconnect if needed
    private function checkConnection() {
        try {
            // Simple query to test connection
            if ($this->dbh) {
                $this->dbh->query('SELECT 1');
            } else {
                throw new PDOException("Connection is null");
            }
        } catch(PDOException $e) {
            // Connection lost, try to reconnect
            error_log("Database connection lost, attempting to reconnect: " . $e->getMessage());
            try {
                $this->connect();
            } catch(Exception $reconnectError) {
                error_log("Failed to reconnect to database: " . $reconnectError->getMessage());
                throw $reconnectError;
            }
        }
    }

    // Get connection status
    public function isConnected() {
        try {
            if ($this->dbh) {
                $this->dbh->query('SELECT 1');
                return true;
            }
        } catch(PDOException $e) {
            return false;
        }
        return false;
    }

    // Ensure required tables exist
    private function ensureRequiredTablesExist() {
        try {
            // Check if database.sql file exists and import it if tables are missing
            $requiredTables = ['categories', 'products', 'product_images', 'admins', 'subscribers', 'settings'];
            $missingTables = [];
            
            foreach ($requiredTables as $table) {
                $result = $this->dbh->query("SHOW TABLES LIKE '$table'");
                if ($result->rowCount() == 0) {
                    $missingTables[] = $table;
                }
            }
            
            if (!empty($missingTables)) {
                // Try to import database.sql if it exists
                $sqlFile = dirname(dirname(dirname(__FILE__))) . '/database.sql';
                if (file_exists($sqlFile)) {
                    $sql = file_get_contents($sqlFile);
                    
                    // Split SQL into individual statements and execute
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    
                    foreach ($statements as $statement) {
                        if (!empty($statement) && !preg_match('/^\s*--/', $statement)) {
                            try {
                                $this->dbh->exec($statement);
                            } catch(PDOException $e) {
                                // Continue on error but log it
                                error_log("Error executing SQL statement: " . $e->getMessage());
                            }
                        }
                    }
                } else {
                    // Fallback: create minimal admins table
                    $this->createMinimalAdminsTable();
                }
            }

            $this->ensureSettingsTableSchema();
        } catch(PDOException $e) {
            // Just log the error but don't stop the application
            error_log("Error ensuring required tables exist: " . $e->getMessage());
        }
    }

    // Settings are written through multiple legacy paths, so keep the table shape compatible.
    private function ensureSettingsTableSchema() {
        try {
            $result = $this->dbh->query("SHOW TABLES LIKE 'settings'");
            if (!$result->fetch(PDO::FETCH_ASSOC)) {
                $this->dbh->exec("CREATE TABLE IF NOT EXISTS `settings` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `key` varchar(255) NOT NULL,
                    `value` text DEFAULT NULL,
                    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `settings_key_unique` (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
                return;
            }

            $idColumn = $this->dbh->query("SHOW COLUMNS FROM `settings` LIKE 'id'")->fetch(PDO::FETCH_ASSOC);
            if (!$idColumn) {
                $this->dbh->exec("ALTER TABLE `settings` ADD COLUMN `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
            } else {
                $idIndex = $this->dbh->query("SHOW INDEX FROM `settings` WHERE Column_name = 'id'")->fetch(PDO::FETCH_ASSOC);
                $primaryIndex = $this->dbh->query("SHOW INDEX FROM `settings` WHERE Key_name = 'PRIMARY'")->fetch(PDO::FETCH_ASSOC);

                if (!$idIndex && !$primaryIndex) {
                    $this->dbh->exec("ALTER TABLE `settings` ADD PRIMARY KEY (`id`)");
                    $idIndex = true;
                } elseif (!$idIndex) {
                    $this->dbh->exec("ALTER TABLE `settings` ADD KEY `settings_id_index` (`id`)");
                    $idIndex = true;
                }

                if ($idIndex && stripos($idColumn['Extra'] ?? '', 'auto_increment') === false) {
                    $this->dbh->exec("ALTER TABLE `settings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT");
                }
            }

            $uniqueKey = $this->dbh->query("SHOW INDEX FROM `settings` WHERE Column_name = 'key' AND Non_unique = 0")->fetch(PDO::FETCH_ASSOC);
            if (!$uniqueKey) {
                $duplicateKeys = $this->dbh->query("SELECT COUNT(*) FROM (
                    SELECT `key` FROM `settings` GROUP BY `key` HAVING COUNT(*) > 1
                ) duplicate_settings")->fetchColumn();

                if ((int) $duplicateKeys === 0) {
                    $this->dbh->exec("ALTER TABLE `settings` ADD UNIQUE KEY `settings_key_unique` (`key`)");
                }
            }
        } catch(PDOException $e) {
            error_log("Error ensuring settings table schema: " . $e->getMessage());
        }
    }
    
    // Fallback method to create minimal admins table
    private function createMinimalAdminsTable() {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `admins` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `username` varchar(100) NOT NULL,
                `password` varchar(255) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `username` (`username`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
            
            $this->dbh->exec($sql);
        } catch(PDOException $e) {
            error_log("Error creating minimal admins table: " . $e->getMessage());
        }
    }

    // Prepare statement with query
    public function query($sql) {
        try {
            // Check connection before preparing statement
            $this->checkConnection();
            $this->stmt = $this->dbh->prepare($sql);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Query preparation error: " . $this->error);
            throw $e; // Rethrow to be handled by the application
        }
    }

    // Bind values
    public function bind($param, $value, $type = null) {
        if(is_null($type)) {
            switch(true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement
    public function execute() {
        try {
            // Check connection before executing
            $this->checkConnection();
            return $this->stmt->execute();
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            error_log("Execution error: " . $this->error);
            throw $e; // Rethrow to be handled by the application
        }
    }

    // Get result set as array
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get single record as array
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get row count
    public function rowCount() {
        return $this->stmt->rowCount();
    }

    // Get last insert ID
    public function lastInsertId() {
        try {
            // Don't check connection here as it might interfere with lastInsertId
            if ($this->dbh) {
                return $this->dbh->lastInsertId();
            }
            return '0';
        } catch(PDOException $e) {
            error_log("Last insert ID error: " . $e->getMessage());
            return '0';
        }
    }

    // Begin a transaction
    public function beginTransaction() {
        try {
            $this->checkConnection();
            return $this->dbh->beginTransaction();
        } catch(PDOException $e) {
            error_log("Begin transaction error: " . $e->getMessage());
            throw $e;
        }
    }

    // Commit the transaction
    public function commit() {
        try {
            $this->checkConnection();
            return $this->dbh->commit();
        } catch(PDOException $e) {
            error_log("Commit transaction error: " . $e->getMessage());
            throw $e;
        }
    }

    // Rollback the transaction
    public function rollBack() {
        try {
            // Check if we're in a transaction before rolling back
            if ($this->dbh && $this->dbh->inTransaction()) {
                $this->checkConnection();
                return $this->dbh->rollBack();
            }
            return true; // If not in transaction, consider it successful
        } catch(PDOException $e) {
            error_log("Rollback transaction error: " . $e->getMessage());
            // Don't throw exception on rollback failure to avoid masking original error
            return false;
        }
    }

    // Get error information
    public function errorInfo() {
        if ($this->stmt) {
            return $this->stmt->errorInfo();
        } elseif ($this->dbh) {
            return $this->dbh->errorInfo();
        }
        return ['00000', null, $this->error ?? 'Unknown error'];
    }

    // Get last error message
    public function getError() {
        return $this->error;
    }
}
