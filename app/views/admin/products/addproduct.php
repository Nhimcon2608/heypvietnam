<?php
// app/views/admin/products/addproduct.php

// Prevent direct access
if (!defined('URL_ROOT')) {
    die('Direct access not permitted');
}

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . URL_ROOT . '/admin');
    exit;
}

// Use data passed from controller
$product = $data['product'] ?? [];
$categories = $data['categories'] ?? [];
$error = $data['error'] ?? '';
$success = $data['success'] ?? '';
$sizes = $data['sizes'] ?? [];
$size_prices = $data['size_prices'] ?? [];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm mới - HeypVietNam Admin</title>
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
                <li class="active">
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
                            <h1 class="page-title">Thêm sản phẩm mới</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/products">Sản phẩm</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Thêm sản phẩm</li>
                                </ol>
                            </nav>
                        </div>
                        <div class="col-auto">
                            <a href="<?php echo URL_ROOT; ?>/admin/products" class="btn btn-secondary" style="padding: 10px 20px; font-size: 1rem; font-weight: 500; display: inline-block; text-decoration: none; cursor: pointer;">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <div class="card">
                        <div class="card-body">
                            <?php if (!empty($data['error'])): ?>
                                <div class="alert alert-danger"><?php echo $data['error']; ?></div>
                            <?php endif; ?>

                            <?php if (!empty($data['success'])): ?>
                                <div class="alert alert-success"><?php echo $data['success']; ?></div>
                            <?php endif; ?>
                            
                            <form action="<?php echo URL_ROOT; ?>/admin/products/add" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($data['product']['name']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Giá cơ bản <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="price" name="price" value="<?php echo $data['product']['price']; ?>" min="0" step="1000" required>
                                                <span class="input-group-text">VND</span>
                                            </div>
                                            <small class="form-text text-muted">Giá hiển thị mặc định khi không chọn kích thước</small>
                                        </div>

                                        <div class="mb-3">
                                            <label for="shopee_link" class="form-label">Link Shopee <span class="text-danger">*</span></label>
                                            <input type="url" class="form-control" id="shopee_link" name="shopee_link" value="<?php echo isset($data['product']['shopee_link']) ? htmlspecialchars($data['product']['shopee_link']) : ''; ?>" placeholder="https://shopee.vn/..." required>
                                            <small class="form-text text-muted">Link sản phẩm trên Shopee để khách hàng có thể mua hàng</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <option value="">-- Chọn danh mục --</option>
                                                <?php foreach ($data['categories'] as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo ($data['product']['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="featured" name="featured" <?php echo $data['product']['featured'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="featured">Sản phẩm nổi bật</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Hình ảnh chính <span class="text-danger">*</span></label>
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                            <div id="image-preview-container" class="mt-2" style="display: none;">
                                                <img id="image-preview" src="#" alt="Xem trước" style="max-height: 200px; max-width: 100%;">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="additional_images" class="form-label">Hình ảnh bổ sung</label>
                                            <input type="file" class="form-control" id="additional_images" name="additional_images[]" accept="image/*" multiple>
                                            <small class="form-text text-muted">Bạn có thể chọn nhiều hình ảnh. Mỗi hình ảnh tối đa 2MB.</small>
                                            <div id="additional-images-preview" class="row mt-2">
                                                <!-- Preview images will be shown here -->
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="video" class="form-label">Video sản phẩm (tùy chọn)</label>
                                            <input type="file" class="form-control" id="video" name="video" accept="video/*">
                                            <div id="video-preview-container" class="mt-2" style="display: none;">
                                                <video id="video-preview" controls style="max-height: 200px; max-width: 100%;">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả sản phẩm</label>
                                    <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($data['product']['description']); ?></textarea>
                                </div>
                                

                                
                                <!-- Custom Fields Section -->
                                <div class="mb-4">
                                    <h5 class="mb-3">Thông tin tùy chỉnh</h5>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Bạn có thể thêm các thông tin tùy chỉnh khác cho sản phẩm.
                                    </div>
                                    
                                    <div id="custom-fields-container">
                                        <div class="custom-field-row">
                                            <div class="row mb-3 align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control" name="custom_field_title[]" placeholder="Tiêu đề (ví dụ: Hạn sử dụng)">
                                                </div>
                                                <div class="col-md-4">
                                                    <textarea class="form-control" name="custom_field_content[]" rows="2" placeholder="Nội dung (ví dụ: 12 tháng)"></textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="d-flex">
                                                        <button type="button" class="btn btn-outline-secondary move-custom-field-up me-1" title="Di chuyển lên" style="display: none;"><i class="fas fa-arrow-up"></i></button>
                                                        <button type="button" class="btn btn-outline-secondary move-custom-field-down me-2" title="Di chuyển xuống" style="display: none;"><i class="fas fa-arrow-down"></i></button>
                                                        <button type="button" class="btn btn-danger remove-custom-field" style="display: none;">
                                                            <i class="fas fa-times"></i> Xóa
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-start mt-2">
                                        <button type="button" id="add-custom-field" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Thêm thông tin tùy chỉnh
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Size Options -->
                                <div class="mb-4">
                                    <label class="form-label">Kích thước và giá <span class="text-danger">*</span></label>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Bạn có thể thêm các kích thước khác nhau với giá riêng. Nếu không thêm kích thước, sản phẩm sẽ hiển thị giá cơ bản.
                                    </div>
                                    
                                    <div id="size-container">
                                        <div class="size-row">
                                            <div class="row mb-3 align-items-start">
                                                <div class="col-md-3 size-column">
                                                    <label class="form-label small">Tên kích thước</label>
                                                    <input type="hidden" name="display_order[]" value="0">
                                                    <input type="text" class="form-control" name="size[]" placeholder="Tên kích thước hoặc tên sản phẩm">
                                                </div>
                                                <div class="col-md-2 price-column">
                                                    <label class="form-label small">Giá</label>
                                                    <div class="input-group">
                                                        <input type="number" class="form-control" name="size_price[]" placeholder="Giá" min="0" step="1000">
                                                        <span class="input-group-text">VND</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">Ảnh kích thước (tùy chọn)</label>
                                                    <input type="file" class="form-control size-image-input" name="size_images[]" accept="image/*">
                                                    <div class="size-image-preview mt-2" style="display: none;">
                                                        <img src="" alt="Preview" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small">&nbsp;</label>
                                                    <div class="d-flex">
                                                        <button type="button" class="btn btn-outline-secondary move-up me-1" title="Di chuyển lên"><i class="fas fa-arrow-up"></i></button>
                                                        <button type="button" class="btn btn-outline-secondary move-down me-2" title="Di chuyển xuống"><i class="fas fa-arrow-down"></i></button>
                                                        <button type="button" class="btn btn-danger remove-size" style="display: none;"><i class="fas fa-times"></i> Xóa</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-start mt-2">
                                    <button type="button" id="add-size" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Thêm kích thước khác
                                    </button>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                                    <i class="fas fa-save"></i> Lưu sản phẩm
                                </button>
                            </div>
                            </form>
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
    // Toggle sidebar
    const toggleBtn = document.getElementById('toggle-sidebar');
    const dashboard = document.querySelector('.admin-dashboard');
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            dashboard.classList.toggle('sidebar-collapsed');
        });
    }
    
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
    
    // WebP to JPEG conversion function
    function convertWebPToJPEG(file, callback) {
        if (file.type !== 'image/webp') {
            callback(file);
            return;
        }

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();

        img.onload = function() {
            canvas.width = img.width;
            canvas.height = img.height;
            ctx.drawImage(img, 0, 0);

            canvas.toBlob(function(blob) {
                // Create new File object with JPEG type
                const convertedFile = new File([blob], file.name.replace(/\.webp$/i, '.jpg'), {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });
                callback(convertedFile);
            }, 'image/jpeg', 0.9);
        };

        img.src = URL.createObjectURL(file);
    }

    // Hiển thị ảnh xem trước với WebP conversion
    const imageInput = document.getElementById('image');
    const previewContainer = document.getElementById('image-preview-container');
    const preview = document.getElementById('image-preview');

    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Convert WebP to JPEG if needed
                convertWebPToJPEG(file, function(convertedFile) {
                    // Update the input with converted file
                    if (convertedFile !== file) {
                        const dt = new DataTransfer();
                        dt.items.add(convertedFile);
                        imageInput.files = dt.files;

                        // Show conversion notice
                        const notice = document.createElement('div');
                        notice.className = 'alert alert-info alert-sm mt-2';
                        notice.innerHTML = '<i class="fas fa-info-circle"></i> File WebP đã được tự động chuyển đổi sang JPEG.';
                        previewContainer.appendChild(notice);
                        setTimeout(() => notice.remove(), 3000);
                    }

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        previewContainer.style.display = 'block';
                    }
                    reader.readAsDataURL(convertedFile);
                });
            } else {
                previewContainer.style.display = 'none';
            }
        });
    }
    
    // Hiển thị xem trước nhiều hình ảnh bổ sung
    const additionalImagesInput = document.getElementById('additional_images');
    const additionalImagesPreview = document.getElementById('additional-images-preview');
    
    if (additionalImagesInput) {
        additionalImagesInput.addEventListener('change', function() {
            // Xóa tất cả xem trước hiện tại
            additionalImagesPreview.innerHTML = '';

            const files = Array.from(this.files);
            const convertedFiles = [];
            let processedCount = 0;

            // Process each file
            files.forEach((file, index) => {
                convertWebPToJPEG(file, function(convertedFile) {
                    convertedFiles[index] = convertedFile;
                    processedCount++;

                    // Create preview
                    const reader = new FileReader();
                    const imgCol = document.createElement('div');
                    imgCol.className = 'col-md-4 mb-2';

                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'additional-img-container position-relative';
                    imgContainer.style.height = '150px';
                    imgContainer.style.border = '1px solid #ddd';
                    imgContainer.style.borderRadius = '4px';
                    imgContainer.style.padding = '5px';
                    imgContainer.style.overflow = 'hidden';

                    const img = document.createElement('img');
                    img.className = 'w-100 h-100';
                    img.style.objectFit = 'contain';

                    // Add conversion notice if WebP was converted
                    if (convertedFile !== file) {
                        const notice = document.createElement('div');
                        notice.className = 'badge bg-info position-absolute top-0 start-0 m-1';
                        notice.style.fontSize = '10px';
                        notice.textContent = 'WebP→JPG';
                        imgContainer.appendChild(notice);
                    }

                    reader.onload = function(e) {
                        img.src = e.target.result;
                    }
                    reader.readAsDataURL(convertedFile);

                    imgContainer.appendChild(img);
                    imgCol.appendChild(imgContainer);
                    additionalImagesPreview.appendChild(imgCol);

                    // Update input files when all are processed
                    if (processedCount === files.length) {
                        const dt = new DataTransfer();
                        convertedFiles.forEach(file => dt.items.add(file));
                        additionalImagesInput.files = dt.files;
                    }
                });
            });
        });
    }

    // Hiển thị xem trước video
    const videoInput = document.getElementById('video');
    const videoPreviewContainer = document.getElementById('video-preview-container');
    const videoPreview = document.getElementById('video-preview');

    if (videoInput) {
        videoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Check file size (50MB = 50 * 1024 * 1024 bytes)
                const maxSize = 50 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File video quá lớn! Kích thước tối đa là 50MB.');
                    this.value = '';
                    videoPreviewContainer.style.display = 'none';
                    return;
                }

                // Check file type
                const allowedTypes = ['video/mp4', 'video/webm', 'video/avi', 'video/mov', 'video/quicktime'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Định dạng video không được hỗ trợ! Chỉ hỗ trợ: MP4, WEBM, AVI, MOV.');
                    this.value = '';
                    videoPreviewContainer.style.display = 'none';
                    return;
                }

                const url = URL.createObjectURL(file);
                videoPreview.src = url;
                videoPreviewContainer.style.display = 'block';
            } else {
                videoPreviewContainer.style.display = 'none';
            }
        });
    }

    // Size management
    const sizeContainer = document.getElementById('size-container');
    const addSizeBtn = document.getElementById('add-size');
    
    // Add size row
    addSizeBtn.addEventListener('click', function() {
        const sizeRow = document.createElement('div');
        sizeRow.classList.add('size-row');
        sizeRow.innerHTML = `
            <div class="row mb-3 align-items-start">
                <div class="col-md-3 size-column">
                    <label class="form-label small">Tên kích thước</label>
                    <input type="hidden" name="display_order[]" value="${document.querySelectorAll('.size-row').length}">
                    <input type="text" class="form-control" name="size[]" placeholder="Tên kích thước hoặc tên sản phẩm">
                </div>
                <div class="col-md-2 price-column">
                    <label class="form-label small">Giá</label>
                    <div class="input-group">
                        <input type="number" class="form-control" name="size_price[]" placeholder="Giá" min="0" step="1000">
                        <span class="input-group-text">VND</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Ảnh kích thước (tùy chọn)</label>
                    <input type="file" class="form-control size-image-input" name="size_images[]" accept="image/*">
                    <div class="size-image-preview mt-2" style="display: none;">
                        <img src="" alt="Preview" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">&nbsp;</label>
                    <div class="d-flex">
                        <button type="button" class="btn btn-outline-secondary move-up me-1" title="Di chuyển lên"><i class="fas fa-arrow-up"></i></button>
                        <button type="button" class="btn btn-outline-secondary move-down me-2" title="Di chuyển xuống"><i class="fas fa-arrow-down"></i></button>
                        <button type="button" class="btn btn-danger remove-size" style="display: none;"><i class="fas fa-times"></i> Xóa</button>
                    </div>
                </div>
            </div>
        `;
        sizeContainer.appendChild(sizeRow);
        updateRemoveButtons();
        updateMoveButtons();
    });
    
    // Remove size row
    sizeContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-size') || e.target.parentElement.classList.contains('remove-size')) {
            const button = e.target.classList.contains('remove-size') ? e.target : e.target.parentElement;
            const sizeRow = button.closest('.size-row');
            sizeRow.remove();
            updateRemoveButtons();
            updateMoveButtons();
        }
    });
    
    // Move size row up or down
    sizeContainer.addEventListener('click', function(e) {
        const isUpButton = e.target.classList.contains('move-up') || e.target.parentElement.classList.contains('move-up');
        const isDownButton = e.target.classList.contains('move-down') || e.target.parentElement.classList.contains('move-down');
        
        if (isUpButton || isDownButton) {
            const button = e.target.classList.contains('move-up') || e.target.classList.contains('move-down') 
                         ? e.target 
                         : e.target.parentElement;
            const sizeRow = button.closest('.size-row');
            
            if (isUpButton) {
                const prevSizeRow = sizeRow.previousElementSibling;
                if (prevSizeRow) {
                    sizeContainer.insertBefore(sizeRow, prevSizeRow);
                }
            } else if (isDownButton) {
                const nextSizeRow = sizeRow.nextElementSibling;
                if (nextSizeRow) {
                    sizeContainer.insertBefore(nextSizeRow, sizeRow);
                }
            }
            
            updateRemoveButtons();
            updateMoveButtons();
        }
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.remove-size');
        // Cho phép xóa tất cả sizes vì sizes không còn bắt buộc
        removeButtons.forEach(button => button.style.display = 'block');
    }
    
    // Update move buttons visibility
    function updateMoveButtons() {
        const sizeRows = document.querySelectorAll('.size-row');
        
        sizeRows.forEach((row, index) => {
            const upBtn = row.querySelector('.move-up');
            const downBtn = row.querySelector('.move-down');
            
            // Disable up button for first row
            if (upBtn) {
                upBtn.disabled = index === 0;
            }
            
            // Disable down button for last row
            if (downBtn) {
                downBtn.disabled = index === sizeRows.length - 1;
            }
        });
    }
    
    updateRemoveButtons();
    updateMoveButtons();
    
    // Chức năng đổi chỗ cột kích thước và giá
    const swapColumnsBtn = document.getElementById('swap-columns');
    if (swapColumnsBtn) {
        swapColumnsBtn.addEventListener('click', function() {
            const sizeColumns = document.querySelectorAll('.size-column');
            const priceColumns = document.querySelectorAll('.price-column');
            
            // Swap the columns in the DOM
            sizeColumns.forEach((sizeCol, index) => {
                const priceCol = priceColumns[index];
                const parent = sizeCol.parentNode;
                
                if (sizeCol.nextElementSibling === priceCol) {
                    // If size is before price, move size after price
                    parent.insertBefore(priceCol, sizeCol);
                } else {
                    // If price is before size, move price after size
                    parent.insertBefore(sizeCol, priceCol.nextElementSibling);
                }
            });
        });
    }
    
    // Function to update display_order hidden fields when rows are moved
    function updateDisplayOrder() {
        const sizeRows = document.querySelectorAll('.size-row');
        sizeRows.forEach((row, index) => {
            const orderInput = row.querySelector('input[name="display_order[]"]');
            if (orderInput) {
                orderInput.value = index;
            }
        });
    }

    // Size image preview functionality
    sizeContainer.addEventListener('change', function(e) {
        if (e.target.classList.contains('size-image-input')) {
            const input = e.target;
            const previewContainer = input.parentElement.querySelector('.size-image-preview');
            const previewImg = previewContainer.querySelector('img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    });

    // Initial setup
    updateRemoveButtons();
    updateMoveButtons();

    // Custom Fields management
    const customFieldsContainer = document.getElementById('custom-fields-container');
    const addCustomFieldBtn = document.getElementById('add-custom-field');
    
    // Add custom field row
    addCustomFieldBtn.addEventListener('click', function() {
        const customFieldRow = document.createElement('div');
        customFieldRow.classList.add('custom-field-row');
        customFieldRow.innerHTML = `
            <div class="row mb-3 align-items-center">
                <div class="col-md-4">
                    <input type="text" class="form-control" name="custom_field_title[]" placeholder="Tiêu đề (ví dụ: Hạn sử dụng)" required>
                </div>
                <div class="col-md-4">
                    <textarea class="form-control" name="custom_field_content[]" rows="2" placeholder="Nội dung (ví dụ: 12 tháng)" required></textarea>
                </div>
                <div class="col-md-4">
                    <div class="d-flex">
                        <button type="button" class="btn btn-outline-secondary move-custom-field-up me-1" title="Di chuyển lên"><i class="fas fa-arrow-up"></i></button>
                        <button type="button" class="btn btn-outline-secondary move-custom-field-down me-2" title="Di chuyển xuống"><i class="fas fa-arrow-down"></i></button>
                        <button type="button" class="btn btn-danger remove-custom-field">
                            <i class="fas fa-times"></i> Xóa
                        </button>
                    </div>
                </div>
            </div>
        `;
        customFieldsContainer.appendChild(customFieldRow);
        updateCustomFieldRemoveButtons();
        updateCustomFieldMoveButtons();

        // Focus vào input đầu tiên của field mới
        const newTitleInput = customFieldRow.querySelector('input[name="custom_field_title[]"]');
        if (newTitleInput) {
            newTitleInput.focus();
        }
    });
    
    // Remove custom field row and handle move buttons
    customFieldsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-custom-field') || e.target.parentElement.classList.contains('remove-custom-field')) {
            const button = e.target.classList.contains('remove-custom-field') ? e.target : e.target.parentElement;
            const customFieldRow = button.closest('.custom-field-row');
            customFieldRow.remove();
            updateCustomFieldRemoveButtons();
            updateCustomFieldMoveButtons();
        }
        
        // Handle move up/down buttons
        const isUpButton = e.target.classList.contains('move-custom-field-up') || e.target.parentElement.classList.contains('move-custom-field-up');
        const isDownButton = e.target.classList.contains('move-custom-field-down') || e.target.parentElement.classList.contains('move-custom-field-down');
        
        if (isUpButton || isDownButton) {
            const button = e.target.classList.contains('move-custom-field-up') || e.target.classList.contains('move-custom-field-down') 
                         ? e.target 
                         : e.target.parentElement;
            const customFieldRow = button.closest('.custom-field-row');
            
            if (isUpButton) {
                const prevCustomFieldRow = customFieldRow.previousElementSibling;
                if (prevCustomFieldRow) {
                    customFieldsContainer.insertBefore(customFieldRow, prevCustomFieldRow);
                }
            } else if (isDownButton) {
                const nextCustomFieldRow = customFieldRow.nextElementSibling;
                if (nextCustomFieldRow) {
                    customFieldsContainer.insertBefore(nextCustomFieldRow, customFieldRow);
                }
            }
            
            updateCustomFieldMoveButtons();
        }
    });
    
    // Update remove buttons visibility for custom fields
    function updateCustomFieldRemoveButtons() {
        const removeButtons = document.querySelectorAll('.remove-custom-field');
        const customFieldRows = document.querySelectorAll('.custom-field-row');
        
        removeButtons.forEach((button, index) => {
            // Show remove button if there's more than one row
            button.style.display = customFieldRows.length > 1 ? 'block' : 'none';
        });
    }
    
    // Update move buttons visibility for custom fields
    function updateCustomFieldMoveButtons() {
        const customFieldRows = document.querySelectorAll('.custom-field-row');
        
        customFieldRows.forEach((row, index) => {
            const upBtn = row.querySelector('.move-custom-field-up');
            const downBtn = row.querySelector('.move-custom-field-down');
            
            // Hide up button for first row
            if (upBtn) {
                upBtn.style.display = index === 0 ? 'none' : 'inline-block';
            }
            
            // Hide down button for last row
            if (downBtn) {
                downBtn.style.display = index === customFieldRows.length - 1 ? 'none' : 'inline-block';
            }
        });
    }
    
    updateCustomFieldRemoveButtons();
    updateCustomFieldMoveButtons();

    // Debug form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form is being submitted');

            // Log all custom field data
            const titles = document.querySelectorAll('input[name="custom_field_title[]"]');
            const contents = document.querySelectorAll('textarea[name="custom_field_content[]"]');

            console.log('Custom field titles:', Array.from(titles).map(input => input.value));
            console.log('Custom field contents:', Array.from(contents).map(textarea => textarea.value));

            // Check if any custom fields are empty
            let hasEmptyFields = false;
            titles.forEach((title, index) => {
                const content = contents[index];
                if (title.value.trim() === '' && content.value.trim() !== '') {
                    console.warn(`Custom field ${index + 1} has content but no title`);
                    hasEmptyFields = true;
                } else if (title.value.trim() !== '' && content.value.trim() === '') {
                    console.warn(`Custom field ${index + 1} has title but no content`);
                    hasEmptyFields = true;
                }
            });

            if (hasEmptyFields) {
                console.warn('Some custom fields are incomplete');
            }
        });
    }
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
    padding: 20px;
}

.card {
    border: none;
    box-shadow: none;
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