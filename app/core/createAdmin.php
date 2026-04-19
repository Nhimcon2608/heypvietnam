<?php
// Database connection settings - use the same as in config.php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'heypvietnam';

// Connect to MySQL
try {
    // Create connection without database first
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected to MySQL server successfully!<br>";
    
    // Check if database exists
    $result = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$dbname'");
    
    if ($result->num_rows == 0) {
        // Create database
        $sql = "CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        
        if ($conn->query($sql) === TRUE) {
            echo "Database created successfully!<br>";
        } else {
            echo "Error creating database: " . $conn->error . "<br>";
        }
    } else {
        echo "Database already exists!<br>";
    }
    
    // Connect to the specific database
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        die("Connection to database failed: " . $conn->connect_error);
    }
    
    echo "Connected to database successfully!<br>";
    
    // Check if s table exists
    $result = $conn->query("SHOW TABLES LIKE 's'");
    
    if ($result->num_rows == 0) {
        // Create s table
        $sql = "CREATE TABLE IF NOT EXISTS `s` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL,
                `email` varchar(255) NOT NULL,
                `username` varchar(100) NOT NULL,
                `password` varchar(255) NOT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `username` (`username`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        if ($conn->query($sql) === TRUE) {
            echo "Admins table created successfully!<br>";
        } else {
            echo "Error creating s table: " . $conn->error . "<br>";
        }
    } else {
        echo "Admins table already exists!<br>";
    }
    
    // Check if  user exists
    $stmt = $conn->prepare("SELECT * FROM s WHERE username = ?");
    $username = 'heypvietnam';
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        // Create  user
        $stmt = $conn->prepare("INSERT INTO s (name, email, username, password) VALUES (?, ?, ?, ?)");
        $name = 'HeypVietNam Admin';
        $email = '@heypvietnam.com';
        $username = 'heypvietnam';
        $password = password_hash('heypvietnam123', PASSWORD_DEFAULT);
        
        $stmt->bind_param("ssss", $name, $email, $username, $password);
        
        if ($stmt->execute()) {
            echo "Admin user created successfully!<br>";
            echo "Username: heypvietnam<br>";
            echo "Password: heypvietnam123<br>";
        } else {
            echo "Error creating  user: " . $stmt->error . "<br>";
        }
    } else {
        echo "Admin user already exists!<br>";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
} 