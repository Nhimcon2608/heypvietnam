<?php
// Đường dẫn gốc - chỉ định nghĩa nếu chưa tồn tại
if (!defined('URL_ROOT')) {
    if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
        define('URL_ROOT', 'http://localhost/heypvietnam');
    } else {
        define('URL_ROOT', 'http://' . $_SERVER['HTTP_HOST']);
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt - HeypVietNam Admin</title>
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
                <li>
                    <a href="<?php echo URL_ROOT; ?>/admin/categories">
                        <i class="fas fa-list"></i>
                        <span>Danh mục</span>
                    </a>
                </li>
                <li class="active">
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
                            <h1 class="header-title">Cài đặt</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Cài đặt</li>
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
                            <h1 class="page-title">Cài đặt</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Cài đặt</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <?php if (!empty($data['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $data['error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($data['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $data['success']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $data['activeTab'] === 'account' ? 'active' : ''; ?>" id="account-tab" data-bs-toggle="tab" data-bs-target="#account-tab-pane" type="button" role="tab" aria-controls="account-tab-pane" aria-selected="<?php echo $data['activeTab'] === 'account' ? 'true' : 'false'; ?>">
                                    <i class="fas fa-user"></i> Thông tin tài khoản
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $data['activeTab'] === 'password' ? 'active' : ''; ?>" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab" aria-controls="password-tab-pane" aria-selected="<?php echo $data['activeTab'] === 'password' ? 'true' : 'false'; ?>">
                                    <i class="fas fa-key"></i> Đổi mật khẩu
                                </button>
                            </li>

                        </ul>

                        <div class="tab-content" id="settingsTabContent">
                            <!-- Tab Thông tin tài khoản -->
                            <div class="tab-pane fade <?php echo $data['activeTab'] === 'account' ? 'show active' : ''; ?>" id="account-tab-pane" role="tabpanel" aria-labelledby="account-tab" tabindex="0">
                                <form action="<?php echo URL_ROOT; ?>/admin/settings" method="POST" class="settings-form">
                                    <input type="hidden" name="tab" value="account">
                                    <div class="row mb-3">
                                        <label for="admin_name" class="col-sm-3 col-form-label fw-medium">Tên hiển thị <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="admin_name" name="admin_name" value="<?php echo isset($data['admin']['name']) ? $data['admin']['name'] : 'HeypVietNam Admin'; ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="admin_username" class="col-sm-3 col-form-label fw-medium">Tên đăng nhập <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="admin_username" name="admin_username" value="<?php echo isset($data['admin']['username']) ? $data['admin']['username'] : 'heypvietnam'; ?>" required>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="admin_email" class="col-sm-3 col-form-label fw-medium">Email</label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo isset($data['admin']['email']) ? $data['admin']['email'] : 'admin@heypvietnam.com'; ?>">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" class="btn btn-success px-4">
                                                <i class="fas fa-save me-2"></i> Cập nhật thông tin
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Tab Đổi mật khẩu -->
                            <div class="tab-pane fade <?php echo $data['activeTab'] === 'password' ? 'show active' : ''; ?>" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                                <form action="<?php echo URL_ROOT; ?>/admin/settings" method="POST" class="settings-form">
                                    <input type="hidden" name="tab" value="password">
                                    <div class="row mb-3">
                                        <label for="current_password" class="col-sm-3 col-form-label fw-medium">Mật khẩu hiện tại <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="password-field-container">
                                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                                                <span class="password-toggle" onclick="togglePasswordVisibility('current_password')">
                                                    <i class="fas fa-eye" id="current_password_toggle"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="new_password" class="col-sm-3 col-form-label fw-medium">Mật khẩu mới <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="password-field-container">
                                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                                                <span class="password-toggle" onclick="togglePasswordVisibility('new_password')">
                                                    <i class="fas fa-eye" id="new_password_toggle"></i>
                                                </span>
                                            </div>
                                            <div class="form-text">Mật khẩu phải có ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường và số</div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label for="confirm_password" class="col-sm-3 col-form-label fw-medium">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                        <div class="col-sm-9">
                                            <div class="password-field-container">
                                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                                <span class="password-toggle" onclick="togglePasswordVisibility('confirm_password')">
                                                    <i class="fas fa-eye" id="confirm_password_toggle"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-9 offset-sm-3">
                                            <button type="submit" class="btn btn-success px-4">
                                                <i class="fas fa-key me-2"></i> Đổi mật khẩu
                                            </button>
                                        </div>
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

.content-card {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.card-body {
    padding: 20px;
}

.nav-tabs {
    border-bottom: 1px solid #dee2e6;
}

.nav-tabs .nav-link {
    border: none;
    padding: 12px 20px;
    color: #555;
    font-weight: 500;
    position: relative;
    margin-right: 5px;
}

.nav-tabs .nav-link.active {
    color: var(--secondary-bg-1);
    background-color: transparent;
    border-bottom: 2px solid var(--secondary-bg-1);
}

.nav-tabs .nav-link:hover {
    color: var(--secondary-bg-1);
}

.settings-form {
    max-width: 100%;
}

.settings-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--heading-color);
}

.btn-success {
    background-color: var(--secondary-bg-1);
    border-color: var(--secondary-bg-1);
}

.btn-success:hover {
    background-color: var(--plant-color);
    border-color: var(--plant-color);
}

/* Password field styling */
.password-field-container {
    position: relative;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
    padding: 5px;
    z-index: 10;
}

.password-toggle:hover {
    color: var(--secondary-bg-1);
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
    
    .content-wrapper {
        padding: 15px;
    }
    
    .page-title {
        font-size: 1.5rem;
    }

    .settings-form .col-sm-3 {
        width: 100%;
        margin-bottom: 8px;
    }
    
    .settings-form .col-sm-9 {
        width: 100%;
    }
    
    .settings-form .offset-sm-3 {
        margin-left: 0;
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
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User dropdown
    const userDropdown = document.querySelector('.user-dropdown');
    if (userDropdown) {
        userDropdown.addEventListener('click', function() {
            this.classList.toggle('active');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!userDropdown.contains(e.target)) {
                userDropdown.classList.remove('active');
            }
        });
    }
    
    // Tab handling with URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    
    if (tab) {
        const tabElement = document.getElementById(`${tab}-tab`);
        if (tabElement) {
            const tabInstance = new bootstrap.Tab(tabElement);
            tabInstance.show();
        }
    }
    
    // Password confirmation validation
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    
    if (confirmPassword && newPassword) {
        confirmPassword.addEventListener('input', function() {
            if (this.value !== newPassword.value) {
                this.setCustomValidity('Mật khẩu xác nhận không khớp');
            } else {
                this.setCustomValidity('');
            }
        });
        
        newPassword.addEventListener('input', function() {
            if (confirmPassword.value && this.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Mật khẩu xác nhận không khớp');
            } else {
                confirmPassword.setCustomValidity('');
            }
        });
    }
});

// Hàm toggle hiển thị mật khẩu
function togglePasswordVisibility(inputId) {
    const passwordInput = document.getElementById(inputId);
    const toggleIcon = document.getElementById(inputId + '_toggle');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
</script>
</body>
</html>