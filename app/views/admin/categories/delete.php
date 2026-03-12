<?php
// Đường dẫn gốc - chỉ định nghĩa nếu chưa tồn tại
if (!defined('URL_ROOT')) {
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        define('URL_ROOT', 'http://localhost/heypvietnam');
    } else {
        define('URL_ROOT', 'http://' . $_SERVER['HTTP_HOST']);
    }
}

// Khởi tạo session nếu chưa start
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kết nối database 
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'heypvietnam';

try {
    $db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Khởi tạo các biến
$success = "";
$error = "";

// Check if ID is set in URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $error = 'ID danh mục không hợp lệ';
    header('Location: ' . URL_ROOT . '/admin/categories');
    exit();
}

$id = $_GET['id'];

// Check if this is a form submission for confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Check if category has associated products
    $stmt = $db->prepare('SELECT COUNT(*) as count FROM products WHERE category_id = :id');
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        $_SESSION['error'] = 'Không thể xóa danh mục này vì có sản phẩm đang sử dụng';
    } else {
        // Delete the category
        $stmt = $db->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = 'Xóa danh mục thành công';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra khi xóa danh mục';
        }
    }
    
    header('Location: ' . URL_ROOT . '/admin/categories');
    exit();
}

// Get category details for confirmation
$stmt = $db->prepare('SELECT * FROM categories WHERE id = :id');
$stmt->bindParam(':id', $id);
$stmt->execute();
$category = $stmt->fetch(PDO::FETCH_ASSOC);

// If category doesn't exist
if (!$category) {
    $_SESSION['error'] = 'Danh mục không tồn tại';
    header('Location: ' . URL_ROOT . '/admin/categories');
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xóa Danh Mục - HeypVietNam</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .delete-container {
            max-width: 500px;
            width: 100%;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
        }
        
        .delete-title {
            text-align: center !important;
            color: #dc3545;
            margin-bottom: 20px;
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
        
        .warning-icon {
            font-size: 50px;
            color: #dc3545;
            text-align: center;
            margin-bottom: 20px;
            display: block;
        }
        
        .category-name {
            font-weight: 600;
            text-align: center;
            font-size: 1.2rem;
            margin-bottom: 20px;
        }
        
        .delete-warning {
            text-align: center;
            margin-bottom: 25px;
            color: #555;
        }
        
        .button-group {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        
        .btn-danger {
            background-color: #dc3545;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        
        .btn-cancel {
            background-color: #6c757d;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            color: white;
            font-weight: 500;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-cancel:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="delete-container">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <i class="fas fa-exclamation-triangle warning-icon"></i>
        <h2 class="delete-title">Xác nhận xóa danh mục</h2>
        
        <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
        
        <p class="delete-warning">
            Bạn có chắc chắn muốn xóa danh mục này? Hành động này không thể hoàn tác và tất cả dữ liệu liên quan đến danh mục sẽ bị xóa vĩnh viễn.
        </p>
        
        <form method="POST">
            <div class="button-group">
                <button type="submit" name="confirm_delete" class="btn-danger">
                    <i class="fas fa-trash me-2"></i> Xác nhận xóa
                </button>
                <a href="<?php echo URL_ROOT; ?>/admin/categories" class="btn-cancel">
                    <i class="fas fa-times me-2"></i> Hủy bỏ
                </a>
            </div>
        </form>
    </div>
</body>
</html> 