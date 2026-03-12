<?php
// Nạp cấu hình chung để có DB_*, APP_ROOT, URL_ROOT, session...
require_once dirname(__FILE__) . '/../../../../config.php';

// Kết nối database dùng cấu hình từ config.php
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Khởi tạo các biến
$success = "";
$error = "";
$category = [
    'name' => '',
    'description' => '',
    'image' => ''
];

// Hiển thị thông báo thành công nếu có
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Hiển thị thông báo lỗi nếu có
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Xử lý form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $category['name'] = isset($_POST['name']) ? trim($_POST['name']) : '';
    $category['description'] = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($category['name'])) {
        $error = "Tên danh mục không được để trống";
    } else {
        // Xử lý upload ảnh
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            // Sử dụng APP_ROOT từ config.php
            $uploadDir = APP_ROOT . '/public/img/categories/';
            
            // Kiểm tra và tạo thư mục nếu không tồn tại
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // Tạo tên file duy nhất
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $uploadFile = $uploadDir . $fileName;
            
            // Debug
            error_log("Upload path: " . $uploadDir);
            error_log("Full file path: " . $uploadFile);
            
            // Upload file
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
                $image = $fileName;
            } else {
                // Debug thông tin lỗi upload
                $uploadErrors = array(
                    0 => 'Không có lỗi, file đã được upload thành công',
                    1 => 'File vượt quá kích thước tối đa cho phép',
                    2 => 'File vượt quá kích thước tối đa cho phép trong HTML form',
                    3 => 'File chỉ được upload một phần',
                    4 => 'Không có file nào được upload',
                    6 => 'Thư mục tạm thời bị thiếu',
                    7 => 'Không thể ghi file vào ổ đĩa',
                    8 => 'Một extension PHP đã ngăn việc upload file',
                );
                
                $error_code = $_FILES['image']['error'];
                $error_message = isset($uploadErrors[$error_code]) ? $uploadErrors[$error_code] : "Lỗi không xác định";
                
                $error = "Không thể upload ảnh: " . $error_message;
                $error .= " | Đường dẫn upload: " . $uploadDir;
                $error .= " | Quyền thư mục: " . (is_writable($uploadDir) ? "Có quyền ghi" : "Không có quyền ghi");
            }
        }
        
        // Nếu không có lỗi, thêm danh mục vào cơ sở dữ liệu
        if (empty($error)) {
            $category['image'] = $image;
            
            try {
            // Thêm danh mục mới
            $stmt = $db->prepare("INSERT INTO categories (name, description, image) VALUES (:name, :description, :image)");
            $stmt->bindParam(':name', $category['name']);
            $stmt->bindParam(':description', $category['description']);
            $stmt->bindParam(':image', $category['image']);
            
            // Thực thi truy vấn
            if ($stmt->execute()) {
                    $success = 'Thêm danh mục thành công';
                    // Reset form
                $category = [
                    'name' => '',
                    'description' => '',
                    'image' => ''
                ];
            } else {
                $error = "Có lỗi xảy ra khi thêm danh mục";
            }
            } catch (PDOException $e) {
                $error = "Lỗi database: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm danh mục - HeypVietNam Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="admin-dashboard">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-container">
                    <img src="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png" alt="HeypVietNam" class="circular-logo">
                </div>
                <h2>HeypVietNam</h2>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="<?php echo URL_ROOT; ?>/admin/dashboard">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo URL_ROOT; ?>/admin/products">
                        <i class="fas fa-box"></i>
                        <span>Sản phẩm</span>
                    </a>
                </li>
                <li class="active">
                    <a href="<?php echo URL_ROOT; ?>/admin/categories">
                        <i class="fas fa-list"></i>
                        <span>Danh mục</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo URL_ROOT; ?>/admin/settings">
                        <i class="fas fa-cog"></i>
                        <span>Cài đặt</span>
                    </a>
                </li>
                <li class="logout">
                    <a href="<?php echo URL_ROOT; ?>/admin/logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="main-header">
            <div class="container-fluid">
                <div class="header-content">
                    <button id="toggle-sidebar" class="btn-toggle-sidebar">
                        <i class="fas fa-bars"></i>
                    </button>
                    
                    <div class="user-dropdown">
                        <div class="dropdown-toggle">
                            <div class="avatar">
                                <i class="fas fa-user-circle"></i>
                            </div>
                            <span><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="dropdown-menu">
                            <a href="<?php echo URL_ROOT; ?>/admin/settings">
                                <i class="fas fa-cog"></i> Cài đặt tài khoản
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo URL_ROOT; ?>/admin/logout">
                                <i class="fas fa-sign-out-alt"></i> Đăng xuất
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="page-title">Thêm danh mục mới</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/dashboard">Bảng điều khiển</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/categories">Danh mục</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Thêm mới</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo URL_ROOT; ?>/admin/categories" class="btn btn-secondary" style="padding: 10px 20px; font-size: 1rem; font-weight: 500; display: inline-block; text-decoration: none; cursor: pointer;">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 col-lg-6 mx-auto">
                        <div class="content-card">
                            <div class="card-body p-4">
                                <?php if (!empty($success)): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?php echo $success; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($error)): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?php echo $error; ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                    </div>
                                <?php endif; ?>

                                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data" class="category-form">
                                    <div class="form-group mb-3">
                                        <label for="name" class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($category['name']); ?>">
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea class="form-control" id="description" name="description" rows="4"><?php echo htmlspecialchars($category['description']); ?></textarea>
                                    </div>
                                    
                                    <div class="form-group mb-3">
                                        <label for="image" class="form-label">Hình ảnh</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                        <div id="image-preview" class="mt-2"></div>
                                </div>
                                
                                    <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Lưu danh mục
                                    </button>
                                        <a href="<?php echo URL_ROOT; ?>/admin/categories" class="btn btn-secondary ms-2">
                                        <i class="fas fa-arrow-left me-1"></i> Quay lại
                                    </a>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar
    const toggleBtn = document.getElementById('toggle-sidebar');
    const dashboard = document.querySelector('.admin-dashboard');
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function(e) {
            e.preventDefault();
            dashboard.classList.toggle('sidebar-collapsed');
        });
    }
    
    // User dropdown
    const userDropdown = document.querySelector('.user-dropdown');
    userDropdown.addEventListener('click', function() {
        this.classList.toggle('active');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!userDropdown.contains(e.target)) {
            userDropdown.classList.remove('active');
        }
    });
    
    // Image preview
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const preview = document.createElement('img');
                preview.className = 'img-thumbnail mt-2';
                preview.style.maxHeight = '150px';
                
                const previewContainer = document.getElementById('image-preview');
                previewContainer.innerHTML = '';
                previewContainer.appendChild(preview);
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    // Kiểm tra và gửi form
    const categoryForm = document.querySelector('.category-form');
    if (categoryForm) {
        categoryForm.addEventListener('submit', function(e) {
            const nameInput = document.getElementById('name');
            if (!nameInput.value.trim()) {
                e.preventDefault();
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Vui lòng nhập tên danh mục',
                    icon: 'error',
                    confirmButtonText: 'Đóng'
                });
                return false;
            }
            
            // Hiển thị thông báo đang xử lý
            Swal.fire({
                title: 'Đang xử lý...',
                html: 'Vui lòng đợi trong giây lát',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            return true;
        });
    }
    
    // Hiển thị thông báo lỗi/thành công nếu có
    <?php if (!empty($success)): ?>
    Swal.fire({
        title: 'Thành công!',
        text: '<?php echo addslashes($success); ?>',
        icon: 'success',
        confirmButtonText: 'Đóng'
    });
    <?php endif; ?>
    
    <?php if (!empty($error)): ?>
    Swal.fire({
        title: 'Lỗi!',
        text: '<?php echo addslashes($error); ?>',
        icon: 'error',
        confirmButtonText: 'Đóng'
    });
    <?php endif; ?>
});
</script>

<style>
:root {
    --primary-bg: #FAFAF9;
    --secondary-bg-1: #2D5A27;
    --secondary-bg-2: #4A7C59;
    --heading-color: #1B3B36;
    --text-color: #374151;
    --accent-color: #FFFFFF;
    --plant-color: #6B8E23;
    --sidebar-bg: #1F2937;
    --sidebar-text: #FFFFFF;
    --main-bg: #FAFAF9;
    --card-bg: #FFFFFF;
    --border-color: #E5E7EB;
    --success-color: #10B981;
    --info-color: #4A7C59;
    --danger-color: #EF4444;
    --warning-color: #F59E0B;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Open Sans', sans-serif;
    background-color: var(--main-bg);
    overflow-x: hidden;
}

h1, h2, h3, h4, h5 {
    font-family: 'Montserrat', sans-serif;
    color: var(--heading-color);
}

a {
    text-decoration: none;
    color: inherit;
}

ul {
    list-style: none;
}

.admin-dashboard {
    display: flex;
    min-height: 100vh;
    width: 100%;
    position: relative;
}

/* Sidebar */
.sidebar {
    width: 250px;
    background-color: var(--sidebar-bg);
    color: var(--sidebar-text);
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    z-index: 1000;
    transition: all 0.3s ease;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
    padding: 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
    display: flex;
    align-items: center;
}

.logo-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 10px;
}

.circular-logo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--secondary-bg-1);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    transition: transform 0.3s ease;
}

.circular-logo:hover {
    transform: scale(1.1);
}

.logo h2 {
    color: var(--sidebar-text);
    font-size: 1.3rem;
    margin: 0;
    font-weight: 700;
}

.sidebar-nav {
    padding: 20px 0;
}

.sidebar-nav ul li {
    padding: 0;
    margin-bottom: 5px;
}

.sidebar-nav ul li a {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.7);
    transition: all 0.3s ease;
}

.sidebar-nav ul li a i {
    width: 20px;
    margin-right: 10px;
}

.sidebar-nav ul li.active a,
.sidebar-nav ul li a:hover {
    color: #fff;
    background-color: rgba(255, 255, 255, 0.1);
    border-left: 4px solid var(--secondary-bg-1);
}

.sidebar-nav ul li.logout {
    margin-top: 30px;
}

.sidebar-nav ul li.logout a {
    color: #ff6b6b;
}

.sidebar-nav ul li.logout a:hover {
    background-color: rgba(255, 99, 99, 0.1);
    border-left: 4px solid #ff6b6b;
}

/* Main Content */
.main-content {
    flex: 1;
    margin-left: 250px;
    width: calc(100% - 250px);
    transition: all 0.3s ease;
}

.main-header {
    background-color: var(--card-bg);
    padding: 15px 0;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    position: sticky;
    top: 0;
    z-index: 900;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-toggle-sidebar {
    background: none;
    border: none;
    color: var(--heading-color);
    font-size: 1.2rem;
    cursor: pointer;
    padding: 5px 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.btn-toggle-sidebar:hover {
    background-color: rgba(0, 0, 0, 0.05);
    border-radius: 4px;
}

.user-dropdown {
    position: relative;
}

.dropdown-toggle {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 6px 10px;
    border-radius: 4px;
    transition: all 0.3s;
}

.dropdown-toggle:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

.dropdown-toggle .avatar {
    margin-right: 10px;
    font-size: 1.5rem;
    color: var(--heading-color);
}

.dropdown-toggle span {
    margin-right: 5px;
    font-weight: 500;
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    right: 0;
    width: 200px;
    background-color: var(--card-bg);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border-radius: 4px;
    padding: 10px 0;
    margin-top: 10px;
    display: none;
    z-index: 1000;
}

.user-dropdown.active .dropdown-menu {
    display: block;
    animation: fadeIn 0.3s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.dropdown-menu a {
    display: flex;
    align-items: center;
    padding: 10px 20px;
    color: var(--text-color);
    transition: all 0.3s;
}

.dropdown-menu a i {
    width: 20px;
    margin-right: 10px;
}

.dropdown-menu a:hover {
    background-color: rgba(0, 0, 0, 0.05);
}

/* Content */
.content-wrapper {
    padding: 20px;
}

.page-header {
    margin-bottom: 20px;
}

.page-title {
    font-size: 1.8rem;
    margin-bottom: 5px;
    font-weight: 700;
    color: var(--heading-color);
}

.breadcrumb {
    margin-bottom: 0;
}

.breadcrumb-item a {
    color: var(--secondary-bg-1);
}

.content-card {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.card-body {
    padding: 25px;
}

.form-label {
    font-weight: 500;
    color: var(--heading-color);
    margin-bottom: 0.5rem;
}

.form-control {
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 0.5rem 0.75rem;
    transition: all 0.3s;
}

.form-control:focus {
    border-color: var(--secondary-bg-1);
    box-shadow: 0 0 0 0.25rem rgba(90, 107, 0, 0.25);
}

.form-text {
    font-size: 0.85rem;
    color: #6c757d;
    margin-top: 0.25rem;
}

.btn-primary {
    background-color: var(--secondary-bg-1);
    border-color: var(--secondary-bg-1);
    font-weight: 500;
    transition: all 0.3s;
}

.btn-primary:hover {
    background-color: var(--plant-color);
    border-color: var(--plant-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    font-weight: 500;
    transition: all 0.3s;
}

.btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.image-preview-container {
    margin-bottom: 15px;
    text-align: center;
    border: 1px dashed var(--border-color);
    padding: 10px;
    border-radius: 5px;
}

/* Responsive */
@media (max-width: 992px) {
    .sidebar {
        width: 70px;
    }
    
    .sidebar .logo h2,
    .sidebar-nav ul li a span {
        display: none;
    }
    
    .sidebar-nav ul li a {
        justify-content: center;
        padding: 15px;
    }
    
    .sidebar-nav ul li a i {
        margin-right: 0;
        font-size: 1.1rem;
    }
    
    .main-content {
        margin-left: 70px;
        width: calc(100% - 70px);
    }
    
    .admin-dashboard.sidebar-collapsed .sidebar {
        width: 250px;
    }
    
    .admin-dashboard.sidebar-collapsed .sidebar .logo h2,
    .admin-dashboard.sidebar-collapsed .sidebar-nav ul li a span {
        display: block;
    }
    
    .admin-dashboard.sidebar-collapsed .sidebar-nav ul li a {
        justify-content: flex-start;
        padding: 12px 20px;
    }
    
    .admin-dashboard.sidebar-collapsed .sidebar-nav ul li a i {
        margin-right: 10px;
    }
    
    .admin-dashboard.sidebar-collapsed .main-content {
        margin-left: 250px;
        width: calc(100% - 250px);
    }
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        width: 100%;
    }
    
    .sidebar {
        left: -250px;
        width: 250px;
    }
    
    .sidebar .logo h2,
    .sidebar-nav ul li a span {
        display: block;
    }
    
    .sidebar-nav ul li a {
        justify-content: flex-start;
        padding: 12px 20px;
    }
    
    .sidebar-nav ul li a i {
        margin-right: 10px;
    }
    
    .admin-dashboard.sidebar-collapsed .sidebar {
        left: 0;
    }
    
    .admin-dashboard.sidebar-collapsed .main-content {
        margin-left: 0;
        width: 100%;
    }
    
    .page-header .row {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .page-header .col-auto {
        margin-top: 15px;
    }
}
</style>

</body>
</html> 