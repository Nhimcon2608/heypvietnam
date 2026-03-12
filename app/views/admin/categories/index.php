<?php
// Đường dẫn gốc - chỉ định nghĩa nếu chưa tồn tại
if (!defined('URL_ROOT')) {
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        define('URL_ROOT', 'http://localhost/heypvietnam');
    } else {
        define('URL_ROOT', 'http://' . $_SERVER['HTTP_HOST']);
    }
}

// Kết nối database 
// Sử dụng config từ config.php
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Lấy danh sách danh mục
$stmt = $db->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hiển thị thông báo thành công nếu có
$success = "";
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Hiển thị thông báo lỗi nếu có
$error = "";
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục - HeypVietNam Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="public/img/logoHEYP.png">
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
                    <div class="header-left">
                        <div class="page-title-wrapper">
                            <h1 class="header-title">Quản lý danh mục</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Danh mục</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    
                    <div class="header-right">
                        <div class="header-action-buttons">
                            <a href="<?php echo URL_ROOT; ?>" class="header-btn view-site-btn" target="_blank">
                                <i class="fas fa-globe"></i>
                                <span class="btn-text">Xem trang web</span>
                            </a>
                        </div>
                        
                        <div class="user-dropdown">
                            <div class="dropdown-toggle">
                                <div class="avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <span><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?></span>
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
            </div>
        </header>

        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="page-title">Quản lý danh mục</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/dashboard">Bảng điều khiển</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Danh mục</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo URL_ROOT; ?>/app/views/admin/categories/add.php" class="btn btn-primary" style="padding: 10px 20px; font-size: 1rem; font-weight: 500; display: inline-block; text-decoration: none; cursor: pointer;">
                                <i class="fas fa-plus-circle me-2"></i>Thêm danh mục mới
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <?php if (empty($categories)): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-list"></i>
                            </div>
                            <h3>Chưa có danh mục nào</h3>
                            <p>Bắt đầu bằng cách thêm danh mục mới vào hệ thống.</p>
                            <a href="<?php echo URL_ROOT; ?>/app/views/admin/categories/add.php" class="btn btn-primary" style="padding: 10px 20px; font-size: 1rem; font-weight: 500; display: inline-block; text-decoration: none; cursor: pointer;">
                                <i class="fas fa-plus"></i> Thêm danh mục mới
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table table-hover product-table">
                                    <thead>
                                        <tr>
                                            <th width="5%">ID</th>
                                            <th width="15%">Hình ảnh</th>
                                            <th width="20%">Tên danh mục</th>
                                            <th width="35%">Mô tả</th>
                                            <th width="15%">Ngày tạo</th>
                                            <th width="10%" class="text-center">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($categories as $category): ?>
                                            <tr>
                                                <td><span class="id-badge"><?= $category['id'] ?></span></td>
                                                <td>
                                                    <div class="product-image">
                                                        <?php if (!empty($category['image'])): ?>
                                                            <img src="<?php echo URL_ROOT; ?>/public/img/categories/<?= $category['image'] ?>" alt="<?= $category['name'] ?>" class="img-thumbnail">
                                                        <?php else: ?>
                                                            <div class="no-image">
                                                                <i class="fas fa-image"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="fw-medium category-name"><?= $category['name'] ?></td>
                                                <td class="category-description"><?= substr($category['description'], 0, 100) ?><?= strlen($category['description']) > 100 ? '...' : '' ?></td>
                                                <td><span class="date-display"><?= date('d/m/Y', strtotime($category['created_at'])) ?></span></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="<?php echo URL_ROOT; ?>/app/views/admin/categories/edit.php?id=<?= $category['id'] ?>" class="btn-edit" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" class="btn-delete" title="Xóa" 
                                                           onclick="confirmDelete(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name'], ENT_QUOTES) ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
    
    // Hiển thị thông báo thành công nếu có
    <?php if (!empty($success)): ?>
    Swal.fire({
        title: 'Thành công!',
        text: '<?php echo addslashes($success); ?>',
        icon: 'success',
        confirmButtonText: 'Đóng'
    });
    <?php endif; ?>
    
    // Hiển thị thông báo lỗi nếu có
    <?php if (!empty($error)): ?>
    Swal.fire({
        title: 'Lỗi!',
        text: '<?php echo addslashes($error); ?>',
        icon: 'error',
        confirmButtonText: 'Đóng'
    });
    <?php endif; ?>
});

// Hàm xác nhận xóa danh mục
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Xác nhận xóa?',
        html: `Bạn có chắc chắn muốn xóa danh mục <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Xóa',
        cancelButtonText: 'Hủy',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Hiển thị loading
            Swal.fire({
                title: 'Đang xử lý...',
                html: 'Vui lòng đợi trong giây lát',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
            }
    });
    
            // Thực hiện AJAX request để xóa danh mục
            const xhr = new XMLHttpRequest();
            xhr.open('GET', '<?php echo URL_ROOT; ?>/admin/deleteCategory/' + id, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                if (this.status === 200) {
                    try {
                        const response = JSON.parse(this.responseText);
                        if (response.success) {
                            Swal.fire({
                                title: 'Thành công!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Đóng'
                            }).then(() => {
                                // Reload trang sau khi xóa thành công
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Lỗi!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'Đóng'
                            });
                        }
                    } catch (e) {
                        Swal.fire({
                            title: 'Lỗi!',
                            text: 'Có lỗi xảy ra khi xử lý phản hồi từ máy chủ',
                            icon: 'error',
                            confirmButtonText: 'Đóng'
                        });
                    }
                } else {
                    Swal.fire({
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra khi kết nối đến máy chủ',
                        icon: 'error',
                        confirmButtonText: 'Đóng'
                    });
                }
            };
            xhr.onerror = function() {
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Không thể kết nối đến máy chủ',
                    icon: 'error',
                    confirmButtonText: 'Đóng'
                });
            };
            xhr.send();
    }
});
}
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
    padding: 0;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    position: sticky;
    top: 0;
    z-index: 900;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 25px;
    height: 70px;
}

.header-left {
    display: flex;
    align-items: center;
}

.page-title-wrapper {
    display: flex;
    flex-direction: column;
}

.header-title {
    font-size: 1.2rem;
    font-weight: 600;
    margin: 0;
    color: var(--heading-color);
}

.main-header .breadcrumb {
    margin: 0;
    padding: 0;
    background: none;
    font-size: 0.85rem;
}

.main-header .breadcrumb-item a {
    color: var(--secondary-bg-1);
    transition: color 0.2s;
}

.main-header .breadcrumb-item a:hover {
    color: var(--secondary-bg-2);
    text-decoration: underline;
}

.main-header .breadcrumb-item.active {
    color: #777;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 15px;
}

.header-action-buttons {
    display: flex;
    gap: 10px;
}

.header-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px 15px;
    background-color: rgba(90, 107, 0, 0.1);
    color: var(--secondary-bg-1);
    border-radius: 6px;
    transition: all 0.2s;
    gap: 8px;
    position: relative;
}

.header-btn:hover {
    background-color: rgba(90, 107, 0, 0.15);
    transform: translateY(-2px);
}

.view-site-btn {
    background-color: var(--secondary-bg-1);
    color: white;
}

.view-site-btn:hover {
    background-color: var(--plant-color);
    color: white;
}

/* Enhanced User Dropdown */
.user-dropdown {
    position: relative;
}

.dropdown-toggle {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 6px 10px;
    border-radius: 30px;
    transition: all 0.3s;
    background-color: rgba(0,0,0,0.03);
    gap: 10px;
    border: 1px solid rgba(0,0,0,0.08);
}

.dropdown-toggle:hover {
    background-color: rgba(0,0,0,0.05);
}

.dropdown-toggle .avatar {
    font-size: 1.5rem;
    color: var(--heading-color);
    display: flex;
    align-items: center;
}

.dropdown-toggle span {
    font-weight: 500;
    color: var(--heading-color);
}

.dropdown-menu {
    position: absolute;
    top: 120%;
    right: 0;
    width: 220px;
    background-color: var(--card-bg);
    box-shadow: 0 5px 25px rgba(0,0,0,0.12);
    border-radius: 10px;
    padding: 0;
    margin-top: 10px;
    display: none;
    z-index: 1000;
    overflow: hidden;
    border: 1px solid rgba(0,0,0,0.08);
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
    padding: 12px 20px;
    color: var(--text-color);
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.dropdown-menu a i {
    width: 20px;
    margin-right: 10px;
    color: var(--secondary-bg-1);
}

.dropdown-menu a:hover {
    background-color: rgba(90, 107, 0, 0.05);
    border-left: 3px solid var(--secondary-bg-1);
}

.dropdown-divider {
    height: 1px;
    background-color: rgba(0,0,0,0.06);
    margin: 0;
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

.add-new-btn {
    background-color: var(--secondary-bg-1);
    border-color: var(--secondary-bg-1);
    transition: all 0.3s;
}

.add-new-btn:hover {
    background-color: var(--plant-color);
    border-color: var(--plant-color);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.content-card {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

/* Empty state */
.empty-state {
    padding: 60px 20px;
    text-align: center;
}

.empty-state-icon {
    font-size: 3rem;
    color: var(--secondary-bg-1);
    opacity: 0.3;
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 1.4rem;
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--text-color);
    margin-bottom: 25px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

/* Table styles */
.table-container {
    padding: 20px;
}

.product-table {
    border-collapse: separate;
    border-spacing: 0;
}

.product-table thead th {
    font-weight: 600;
    color: var(--heading-color);
    border-bottom: 2px solid var(--secondary-bg-1);
    padding: 12px 15px;
    font-size: 0.9rem;
}

.product-table tbody tr {
    transition: all 0.3s;
}

.product-table tbody tr:hover {
    background-color: rgba(90, 107, 0, 0.05);
}

.product-table td {
    vertical-align: middle;
    padding: 12px 15px;
}

.id-badge {
    background-color: #f0f0f0;
    color: #555;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.product-image {
    width: 60px;
    height: 60px;
    border-radius: 4px;
    overflow: hidden;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #aaa;
    font-size: 1.2rem;
}

.category-name {
    font-weight: 600;
    color: var(--heading-color);
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.category-description {
    font-size: 0.9rem;
    color: var(--text-color);
    max-width: 300px;
}

.date-display {
    color: #666;
    font-size: 0.9rem;
}

.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: center;
}

.action-buttons a {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    color: white;
    transition: all 0.3s;
}

.btn-edit {
    background-color: var(--info-color);
}

.btn-edit:hover {
    background-color: #138496;
    transform: translateY(-2px);
}

.btn-delete {
    background-color: var(--danger-color);
}

.btn-delete:hover {
    background-color: #c82333;
    transform: translateY(-2px);
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
    
    .category-name {
        max-width: 150px;
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
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 5px;
    }
    
    .action-buttons a {
        width: 28px;
        height: 28px;
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .page-header .row {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .page-header .col-auto {
        margin-top: 15px;
    }
    
    .product-image {
        width: 40px;
        height: 40px;
    }
    
    .product-table td, .product-table th {
        padding: 10px 8px;
        font-size: 0.85rem;
    }
    
    .category-name {
        max-width: 100px;
    }
}
</style>

</body>
</html>