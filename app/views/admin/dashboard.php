<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - HeyPVietnam</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/app/assets/css/admin-effects.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    
    <!-- Inline styles include nội dung của admin-style.css -->
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

/* Add styles for clickable cards */
.stats-card.clickable-card {
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.stats-card.clickable-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(255, 255, 255, 0);
    transition: all 0.3s ease;
    pointer-events: none;
}

.stats-card.clickable-card:hover::after {
    background-color: rgba(255, 255, 255, 0.1);
}

.stats-card.clickable-card:active::after {
    background-color: rgba(0, 0, 0, 0.05);
}

/* Improve toggle sidebar button */
.btn-toggle-sidebar {
    display: inline-block;
    background-color: rgba(0, 0, 0, 0.05);
    color: var(--heading-color);
    font-size: 1.2rem;
    cursor: pointer;
    padding: 10px 15px;
    margin-right: 15px;
    border-radius: 5px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 1001;
    text-decoration: none;
}

.btn-toggle-sidebar:hover {
    background-color: rgba(0, 0, 0, 0.15);
    color: var(--heading-color);
}

.btn-toggle-sidebar:active, 
.btn-toggle-sidebar:focus {
    background-color: rgba(0, 0, 0, 0.2);
    color: var(--heading-color);
    outline: none;
}

.btn-toggle-sidebar i {
    pointer-events: none;
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

/* Enhanced Header Styles */
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

.clickable-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.clickable-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Content */
.content-wrapper {
    padding: 20px;
}

.page-header {
    margin-bottom: 25px;
}

.page-title {
    font-size: 1.8rem;
    margin-bottom: 5px;
    font-weight: 700;
    color: var(--heading-color);
}

.welcome-message {
    margin-bottom: 0;
    color: var(--text-color);
}

.date-display {
    background: linear-gradient(135deg, var(--secondary-bg-1) 0%, var(--secondary-bg-2) 100%);
    color: white;
    padding: 8px 15px;
    border-radius: 30px;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    box-shadow: 0 4px 12px rgba(45, 90, 39, 0.25);
}

.date-display i {
    margin-right: 8px;
}

/* Stats Cards */
.stats-cards {
    margin-bottom: 25px;
}

.stats-card {
    background-color: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(45, 90, 39, 0.08);
    padding: 20px;
    height: 100%;
    display: flex;
    flex-direction: column;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: 1px solid var(--border-color);
}

.stats-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(45, 90, 39, 0.15);
    border-color: var(--secondary-bg-2);
}

.stats-card-content {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
}

.stats-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-right: 15px;
}

.products-card .stats-card-icon {
    background: linear-gradient(135deg, rgba(45, 90, 39, 0.1) 0%, rgba(74, 124, 89, 0.1) 100%);
    color: var(--secondary-bg-1);
}

.categories-card .stats-card-icon {
    background: linear-gradient(135deg, rgba(74, 124, 89, 0.1) 0%, rgba(107, 142, 35, 0.1) 100%);
    color: var(--secondary-bg-2);
}

.featured-card .stats-card-icon {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.1) 0%, rgba(251, 191, 36, 0.1) 100%);
    color: var(--warning-color);
}

.views-card .stats-card-icon {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.1) 100%);
    color: var(--success-color);
}

.stats-card-info h5 {
    margin: 0;
    font-size: 0.9rem;
    font-weight: 500;
    color: var(--text-color);
    opacity: 0.8;
}

.stats-card-info h3 {
    margin: 5px 0 0;
    font-size: 1.8rem;
    font-weight: 700;
}

.stats-card-action {
    margin-top: auto;
    border-top: 1px solid var(--border-color);
    padding-top: 15px;
}

.stats-card-action a {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--secondary-bg-1);
    transition: all 0.3s;
}

.stats-card-action a:hover {
    color: var(--plant-color);
}

/* Content Cards */
.content-card {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    height: 100%;
}

.card-header {
    padding: 15px 20px;
    border-bottom: 1px solid var(--border-color);
}

.card-header h5 {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 600;
}

.card-body {
    padding: 20px;
}

/* Quick Actions */
.quick-actions {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 15px;
}

.quick-action-btn {
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    display: flex;
    align-items: center;
    transition: all 0.3s;
}

.quick-action-btn:hover {
    background: linear-gradient(135deg, var(--secondary-bg-1) 0%, var(--secondary-bg-2) 100%);
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(45, 90, 39, 0.25);
}

.quick-action-btn .icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, rgba(45, 90, 39, 0.1) 0%, rgba(74, 124, 89, 0.1) 100%);
    color: var(--secondary-bg-1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-right: 12px;
    transition: all 0.3s;
}

.quick-action-btn:hover .icon {
    background-color: rgba(255, 255, 255, 0.2);
    color: white;
}

.quick-action-btn span {
    font-weight: 500;
}

/* Activity List */
.activity-list {
    padding: 0;
    margin: 0;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, rgba(45, 90, 39, 0.1) 0%, rgba(74, 124, 89, 0.1) 100%);
    color: var(--secondary-bg-1);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 0.9rem;
}

.activity-details {
    flex: 1;
}

.activity-text {
    font-size: 0.9rem;
    margin-bottom: 3px;
}

.activity-time {
    font-size: 0.8rem;
    color: #888;
}

/* Empty State */
.empty-state {
    padding: 20px;
    text-align: center;
}

.empty-state-icon {
    font-size: 2rem;
    color: var(--secondary-bg-1);
    opacity: 0.3;
    margin-bottom: 10px;
}

.empty-state p {
    margin-bottom: 0;
    color: var(--text-color);
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
    
    .quick-actions {
        grid-template-columns: repeat(2, 1fr);
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
}

@media (max-width: 576px) {
    .page-header .row {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .page-header .col-auto {
        margin-top: 15px;
    }
    
    .stats-card-content {
        flex-direction: column;
        text-align: center;
    }
    
    .stats-card-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
}

/* Thêm CSS cho hiệu ứng ripple */
.ripple-effect {
    position: absolute;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.4);
    transform: scale(0);
    animation: ripple 0.6s linear;
    pointer-events: none;
    z-index: 0;
}

@keyframes ripple {
    to {
        transform: scale(1);
        opacity: 0;
    }
}

/* Thêm position relative cho các phần tử có ripple */
.dropdown-toggle, .dropdown-menu a, .stats-card {
    position: relative;
    overflow: hidden;
}

.dropdown-divider {
    height: 1px;
    background: linear-gradient(to right, 
        rgba(56, 178, 172, 0.1), 
        rgba(56, 178, 172, 0.5), 
        rgba(66, 153, 225, 0.5), 
        rgba(66, 153, 225, 0.1));
    margin: 8px 0;
    border: none;
}

.user-dropdown .dropdown-menu .logout-btn {
    color: #e53e3e;
    font-weight: 500;
}

.user-dropdown .dropdown-menu .logout-btn:hover {
    background: linear-gradient(90deg, rgba(229, 62, 62, 0.1), rgba(245, 101, 101, 0.1));
    color: #c53030;
}

.user-dropdown .dropdown-menu .logout-btn i {
    color: #e53e3e;
}

/* Thêm glow effect cho avatar khi active */
.user-dropdown.active .dropdown-toggle img.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 8px;
    border: 2px solid rgba(90, 107, 0, 0.2);
    transition: all 0.3s ease;
    transform: scale(1);
    object-fit: cover;
    background-color: #f8f9fa;
}

.user-dropdown .dropdown-toggle:hover img.user-avatar,
.user-dropdown.active .dropdown-toggle img.user-avatar {
    border-color: var(--secondary-bg-1);
    transform: scale(1.05);
}

/* Tạo màu nền gradient cho user name */
.user-dropdown .user-name {
    background: linear-gradient(90deg, #ffffff, #f0f0f0);
    -webkit-background-clip: text;
    color: white;
    font-weight: 500;
    text-shadow: 0 1px 2px rgba(0,0,0,0.1);
}
</style>
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
                <li class="active">
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
                            <h1 class="header-title">Dashboard</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
                            <h1 class="page-title">Dashboard</h1>
                            <p class="welcome-message">Chào mừng, <strong><?php echo isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; ?></strong>!</p>
                        </div>
                        <div class="col-auto">
                            <div class="date-display">
                                <i class="far fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row stats-cards">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stats-card products-card">
                            <div class="stats-card-content">
                                <div class="stats-card-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="stats-card-info">
                                    <h5>Tổng sản phẩm</h5>
                                    <h3><?php echo isset($data['productsCount']) ? $data['productsCount'] : 0; ?></h3>
                                </div>
                            </div>
                            <div class="stats-card-action">
                                <a href="<?php echo URL_ROOT; ?>/admin/products">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stats-card categories-card">
                            <div class="stats-card-content">
                                <div class="stats-card-icon">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="stats-card-info">
                                    <h5>Tổng danh mục</h5>
                                    <h3><?php echo isset($data['categoriesCount']) ? $data['categoriesCount'] : 0; ?></h3>
                                </div>
                            </div>
                            <div class="stats-card-action">
                                <a href="<?php echo URL_ROOT; ?>/admin/categories">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stats-card featured-card">
                            <div class="stats-card-content">
                                <div class="stats-card-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="stats-card-info">
                                    <h5>Sản phẩm nổi bật</h5>
                                    <h3><?php echo isset($data['featuredCount']) ? $data['featuredCount'] : 0; ?></h3>
                                </div>
                            </div>
                            <div class="stats-card-action">
                                <a href="<?php echo URL_ROOT; ?>/admin/products?featured=1">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stats-card views-card">
                            <div class="stats-card-content">
                                <div class="stats-card-icon">
                                    <i class="fas fa-eye"></i>
                                </div>
                                <div class="stats-card-info">
                                    <h5>Lượt xem</h5>
                                    <h3><?php echo isset($data['totalViews']) ? $data['totalViews'] : 0; ?></h3>
                                </div>
                            </div>
                            <div class="stats-card-action">
                                <a href="<?php echo URL_ROOT; ?>/admin/analytics">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-12 mb-4">
                        <div class="content-card">
                            <div class="card-header">
                                <h5>Hành động nhanh</h5>
                            </div>
                            <div class="card-body">
                                <div class="quick-actions">
                                    <a href="<?php echo URL_ROOT; ?>/admin/products/add" class="quick-action-btn">
                                        <div class="icon">
                                            <i class="fas fa-plus-circle"></i>
                                        </div>
                                        <span>Thêm sản phẩm</span>
                                    </a>
                                    <a href="<?php echo URL_ROOT; ?>/admin/categories/add" class="quick-action-btn">
                                        <div class="icon">
                                            <i class="fas fa-folder-plus"></i>
                                        </div>
                                        <span>Thêm danh mục</span>
                                    </a>
                                    <a href="<?php echo URL_ROOT; ?>/admin/settings" class="quick-action-btn">
                                        <div class="icon">
                                            <i class="fas fa-cog"></i>
                                        </div>
                                        <span>Cài đặt</span>
                                    </a>
                                    <a href="<?php echo URL_ROOT; ?>" class="quick-action-btn" target="_blank">
                                        <div class="icon">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <span>Xem website</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    
    // Stats card click effect
    const statsCards = document.querySelectorAll('.stats-card');
    statsCards.forEach(card => {
        const link = card.querySelector('.stats-card-action a');
        if (link) {
            card.addEventListener('click', function() {
                window.location.href = link.getAttribute('href');
            });
        }
    });
});
</script>

</body>
</html> 