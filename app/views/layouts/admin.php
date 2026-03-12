<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Quản trị - HeypVietNam' ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    <link rel="shortcut icon" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo URL_ROOT; ?>/public/manifest.json">
    
    <!-- Theme Colors -->
    <meta name="theme-color" content="#2D5A27">
    <meta name="msapplication-TileColor" content="#2D5A27">
    <meta name="msapplication-TileImage" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo URL_ROOT; ?>/public/css/admin.css?v=<?php echo time(); ?>">
</head>
<body>
    <?php if (isset($_SESSION['admin_id']) && $_SERVER['REQUEST_URI'] != '/admin'): ?>
        <!-- Admin nav -->
        <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #2D5A27 0%, #4A7C59 100%) !important;"
            <div class="container">
                <a class="navbar-brand" href="<?php echo URL_ROOT; ?>/admin/dashboard">HeypVietNam Admin</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarAdmin">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/dashboard') !== false ? 'active' : '' ?>" href="<?php echo URL_ROOT; ?>/admin/dashboard">
                                <i class="fas fa-tachometer-alt"></i> Bảng điều khiển
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/products') !== false ? 'active' : '' ?>" href="<?php echo URL_ROOT; ?>/admin/products">
                                <i class="fas fa-box"></i> Sản phẩm
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/categories') !== false ? 'active' : '' ?>" href="<?php echo URL_ROOT; ?>/admin/categories">
                                <i class="fas fa-list"></i> Danh mục
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/admin/settings') !== false ? 'active' : '' ?>" href="<?php echo URL_ROOT; ?>/admin/settings">
                                <i class="fas fa-cog"></i> Cài đặt
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= $_SESSION['admin_name'] ?? 'Admin' ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/admin/settings"><i class="fas fa-cog"></i> Cài đặt</a></li>
                                <li><a class="dropdown-item" href="/" target="_blank"><i class="fas fa-home"></i> Xem trang chủ</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo URL_ROOT; ?>/admin/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="container py-4">
        <?php require $content; ?>
    </main>

    <footer class="py-3 mt-auto" style="background: linear-gradient(135deg, #1F2937 0%, #374151 100%); color: #FFFFFF;"
        <div class="container text-center">
            <p class="mb-0">&copy; <?= date('Y') ?> HeypVietNam Admin. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/public/js/admin.js"></script>
</body>
</html> 