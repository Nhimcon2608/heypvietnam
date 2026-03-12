<?php
// Include config file to get URL_ROOT and other constants
require_once dirname(__FILE__) . '/../../../../config.php';

// Include necessary models
require_once APP_ROOT . '/app/core/Database.php';
require_once APP_ROOT . '/app/models/Product.php';
require_once APP_ROOT . '/app/models/Category.php';

// Initialize models
$productModel = new \App\Models\Product();
$categoryModel = new \App\Models\Category();

// Get data for the page
$selectedCategory = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$categories = $categoryModel->getAll();

if ($selectedCategory && $selectedCategory != 'all') {
    $products = $productModel->getByCategory($selectedCategory);
} else {
    $products = $productModel->getAllWithCategory();
}

// Prepare data array for compatibility with existing template
$data = [
    'products' => $products,
    'categories' => $categories,
    'selectedCategory' => $selectedCategory
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm - HeypVietNam Admin</title>
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
                    <div class="header-left">
                        <div class="page-title-wrapper">
                            <h1 class="header-title">Quản lý sản phẩm</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Sản phẩm</li>
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
                        <div class="col text-center">
                            <h1 class="page-title">Quản lý sản phẩm</h1>
                            <p class="page-subtitle">Quản lý tất cả sản phẩm trong hệ thống</p>
                            <div class="mt-3">
                            <a href="<?php echo URL_ROOT; ?>/admin/products/add" class="btn btn-primary add-new-btn" style="padding: 10px 20px; font-size: 1rem; font-weight: 500; display: inline-block; text-decoration: none; cursor: pointer;">
                                    <i class="fas fa-plus"></i> Thêm sản phẩm mới
                            </a>
                            <button id="deleteSelectedBtn" class="btn btn-danger ms-2" style="padding: 10px 20px; font-size: 1rem; font-weight: 500; display: none;">
                                <i class="fas fa-trash"></i> Xóa đã chọn (<span id="selectedCount">0</span>)
                            </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thêm bộ lọc danh mục -->
                <div class="filter-section mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center justify-content-center">
                                <div class="col-md-3 text-center">
                                    <h5 class="card-title"><i class="fas fa-filter"></i> Lọc sản phẩm</h5>
                                </div>
                                <div class="col-md-6">
                                    <form id="categoryFilterForm" method="GET" action="<?= URL_ROOT ?>/admin/products">
                                        <div class="input-group">
                                            <select class="form-control" id="categorySelect" name="category_id">
                                                <option value="all">Tất cả danh mục</option>
                                                <?php foreach ($data['categories'] as $category): ?>
                                                    <option value="<?= $category['id'] ?>" <?= isset($data['selectedCategory']) && $data['selectedCategory'] == $category['id'] ? 'selected' : '' ?>>
                                                        <?= $category['name'] ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="input-group-append">
                                                <button class="btn btn-primary" type="submit">
                                                    <i class="fas fa-search"></i> Lọc
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-3 text-center">
                                    <?php if (isset($data['selectedCategory']) && $data['selectedCategory'] && $data['selectedCategory'] != 'all'): ?>
                                        <a href="<?= URL_ROOT ?>/admin/products" class="btn btn-outline-secondary">
                                            <i class="fas fa-times"></i> Xóa bộ lọc
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <span class="badge badge-light">
                                        <i class="fas fa-cube"></i> Tổng số: <?= count($data['products']) ?> sản phẩm
                                    </span>
                                    <?php if (isset($data['selectedCategory']) && $data['selectedCategory'] && $data['selectedCategory'] != 'all'): ?>
                                        <span class="badge badge-info">
                                            <i class="fas fa-filter"></i> Đang lọc theo danh mục
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="content-card">
                    <?php if (empty($data['products'])): ?>
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-box-open"></i>
                            </div>
                            <h3>Chưa có sản phẩm nào</h3>
                            <p>Bắt đầu bằng cách thêm sản phẩm mới vào hệ thống.</p>
                            <a href="<?php echo URL_ROOT; ?>/app/views/admin/products/addproduct.php" class="btn btn-primary" style="padding: 10px 20px; font-size: 1rem; font-weight: 500; display: inline-block; text-decoration: none; cursor: pointer;">
                                <i class="fas fa-plus"></i> Thêm sản phẩm mới
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-container">
                            <div class="table-responsive mx-auto" style="max-width: 95%;">
                                <table class="table table-hover product-table">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" title="Chọn tất cả">
                                            </th>
                                            <th>ID</th>
                                            <th>Ảnh</th>
                                            <th>Tên sản phẩm</th>
                                            <th>Giá</th>
                                            <th>Danh mục</th>
                                            <th>Shopee Link</th>
                                            <th>Nổi bật</th>
                                            <th>Trạng thái hàng</th>
                                            <th>Ngày tạo</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data['products'] as $product): ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" class="product-checkbox" value="<?php echo $product['id']; ?>" data-name="<?php echo htmlspecialchars($product['name']); ?>">
                                                </td>
                                                <td><span class="id-badge"><?php echo $product['id']; ?></span></td>
                                                <td>
                                                    <div class="product-image">
                                                        <?php if (!empty($product['image'])): ?>
                                                            <img src="<?php echo URL_ROOT . '/image.php?type=product&id=' . $product['id']; ?>"
                                                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                                 loading="lazy"
                                                                 style="display: block;">
                                                            <div class="no-image" style="display: none;">
                                                                <i class="fas fa-image"></i>
                                                                <span style="font-size: 0.7rem; margin-top: 2px;">Lỗi ảnh</span>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="no-image">
                                                                <i class="fas fa-image"></i>
                                                                <span style="font-size: 0.7rem; margin-top: 2px;">Không có ảnh</span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="product-name"><?php echo $product['name']; ?></div>
                                                </td>
                                                <td><span class="price-tag"><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</span></td>
                                                <td><span class="category-badge"><?php echo $product['category_name']; ?></span></td>
                                                <td>
                                                    <?php if (!empty($product['shopee_link'])): ?>
                                                        <a href="<?php echo htmlspecialchars($product['shopee_link']); ?>" target="_blank" class="shopee-link-btn" title="Xem trên Shopee">
                                                            <i class="fas fa-shopping-bag"></i> Shopee
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">Chưa có link</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="featured-status">
                                                        <?php if ($product['featured']): ?>
                                                            <span class="featured-badge"><i class="fas fa-star"></i> Nổi bật</span>
                                                        <?php else: ?>
                                                            <span class="normal-badge">Thường</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="stock-toggle">
                                                        <label class="switch">
                                                            <input type="checkbox" <?php echo (isset($product['out_of_stock']) && $product['out_of_stock']) ? 'checked' : ''; ?>
                                                                   onchange="toggleOutOfStock(<?php echo $product['id']; ?>, this)">
                                                            <span class="slider round"></span>
                                                        </label>
                                                        <span class="stock-label <?php echo (isset($product['out_of_stock']) && $product['out_of_stock']) ? 'out-of-stock' : 'in-stock'; ?>">
                                                            <?php echo (isset($product['out_of_stock']) && $product['out_of_stock']) ? 'Hết hàng' : 'Còn hàng'; ?>
                                                        </span>
                                                    </div>
                                                </td>
                                                <td><span class="date-display"><?php echo date('d/m/Y', strtotime($product['created_at'])); ?></span></td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <a href="<?php echo URL_ROOT; ?>/admin/products/edit/<?= $product['id'] ?>" class="btn-edit" title="Chỉnh sửa">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="javascript:void(0)" class="btn-delete" title="Xóa" onclick="confirmDelete(<?= $product['id'] ?>, '<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>')">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                        <a href="<?php echo URL_ROOT; ?>/products/show/<?php echo $product['id']; ?>" class="btn-view" title="Xem" target="_blank">
                                                            <i class="fas fa-eye"></i>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xóa sản phẩm <strong id="productName"></strong>?</p>
                <p class="text-danger">Hành động này không thể hoàn tác!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">Xóa sản phẩm</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for success message in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('success') === '1') {
        Swal.fire({
            title: 'Thành công!',
            text: 'Sản phẩm đã được thêm thành công!',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            // Remove success parameter from URL
            const url = new URL(window.location);
            url.searchParams.delete('success');
            window.history.replaceState({}, document.title, url.pathname + url.search);
        });

// Global functions for image handling
window.handleImageError = function(img) {
    console.log('Image failed to load:', img.src);
    
    // Get fallback paths from data attribute
    const fallbackPaths = img.getAttribute('data-fallback-paths');
    if (fallbackPaths) {
        try {
            const paths = JSON.parse(fallbackPaths);
            const currentSrc = img.src;
            const currentIndex = paths.indexOf(currentSrc);
            
            // Try next path if available
            if (currentIndex < paths.length - 1) {
                const nextPath = paths[currentIndex + 1];
                console.log('Trying fallback path:', nextPath);
                img.src = nextPath;
                return;
            }
        } catch (e) {
            console.error('Error parsing fallback paths:', e);
        }
    }
    
    // If no more fallback paths, try placeholder
    if (!img.src.includes('placeholder.svg')) {
        img.src = '<?php echo URL_ROOT; ?>/public/img/placeholder.svg';
        return;
    }
    
    // If placeholder also fails, show no-image div
    img.style.display = 'none';
    if (img.nextElementSibling && img.nextElementSibling.classList.contains('no-image')) {
        img.nextElementSibling.style.display = 'flex';
    }
}

window.handleImageLoad = function(img) {
    console.log('Image loaded successfully:', img.src);
    img.style.display = 'block';
    if (img.nextElementSibling && img.nextElementSibling.classList.contains('no-image')) {
        img.nextElementSibling.style.display = 'none';
    }
};
    }
    
    // Redirect function for add product button
    window.addProduct = function() {
        window.location.href = '<?php echo URL_ROOT; ?>/admin/products/add';
    };
    
    // Make all buttons and links more clickable
    const allButtons = document.querySelectorAll('.btn, button, a.add-new-btn');
    allButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Add this line for debugging
            console.log('Button clicked:', this.textContent, 'href:', this.getAttribute('href'));
        });
    });
    
    // Special handling for add product button in header
    const addNewBtn = document.querySelector('.add-new-btn');
    if (addNewBtn) {
        addNewBtn.addEventListener('click', function(e) {
            console.log('Add new button clicked, redirecting to:', this.getAttribute('href'));
        });
    }

    // Bulk delete functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');
    const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
    const selectedCountSpan = document.getElementById('selectedCount');

    // Select all functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateDeleteButton();
        });
    }

    // Individual checkbox change
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateDeleteButton();
        });
    });

    // Update select all checkbox state
    function updateSelectAllState() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        const totalBoxes = productCheckboxes.length;

        if (checkedBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedBoxes.length === totalBoxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
            selectAllCheckbox.checked = false;
        }
    }

    // Update delete button visibility and count
    function updateDeleteButton() {
        const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
        const count = checkedBoxes.length;

        if (count > 0) {
            deleteSelectedBtn.style.display = 'inline-block';
            selectedCountSpan.textContent = count;
        } else {
            deleteSelectedBtn.style.display = 'none';
        }
    }

    // Delete selected products
    if (deleteSelectedBtn) {
        deleteSelectedBtn.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.product-checkbox:checked');
            const selectedIds = Array.from(checkedBoxes).map(cb => cb.value);
            const selectedNames = Array.from(checkedBoxes).map(cb => cb.dataset.name);

            if (selectedIds.length === 0) {
                alert('Vui lòng chọn ít nhất một sản phẩm để xóa.');
                return;
            }

            const confirmMessage = selectedIds.length === 1
                ? `Bạn có chắc chắn muốn xóa sản phẩm "${selectedNames[0]}"?`
                : `Bạn có chắc chắn muốn xóa ${selectedIds.length} sản phẩm đã chọn?\n\nDanh sách sản phẩm:\n${selectedNames.join('\n')}`;

            if (confirm(confirmMessage)) {
                deleteMultipleProducts(selectedIds);
            }
        });
    }

    // Function to delete multiple products
    function deleteMultipleProducts(productIds) {
        // Show loading state
        deleteSelectedBtn.disabled = true;
        deleteSelectedBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xóa...';

        // Send AJAX request
        fetch('<?php echo URL_ROOT; ?>/admin/products/delete-multiple', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                productIds: productIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Reload page to show updated list
                window.location.reload();
            } else {
                alert('Lỗi: ' + data.message);
                // Reset button state
                deleteSelectedBtn.disabled = false;
                deleteSelectedBtn.innerHTML = '<i class="fas fa-trash"></i> Xóa đã chọn (<span id="selectedCount">' + productIds.length + '</span>)';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa sản phẩm.');
            // Reset button state
            deleteSelectedBtn.disabled = false;
            deleteSelectedBtn.innerHTML = '<i class="fas fa-trash"></i> Xóa đã chọn (<span id="selectedCount">' + productIds.length + '</span>)';
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
    
    // Delete confirmation
    window.confirmDelete = function(id, name) {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: `Bạn có chắc chắn muốn xóa sản phẩm "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Đang xóa...',
                    text: 'Vui lòng đợi',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Redirect to delete
                window.location.href = '<?php echo URL_ROOT; ?>/admin/products/delete/' + id;
            }
        });
    };

    // Toggle out of stock status
    window.toggleOutOfStock = function(productId, checkbox) {
        const isOutOfStock = checkbox.checked;
        const label = checkbox.closest('.stock-toggle').querySelector('.stock-label');

        // Update UI immediately
        if (isOutOfStock) {
            label.textContent = 'Hết hàng';
            label.className = 'stock-label out-of-stock';
        } else {
            label.textContent = 'Còn hàng';
            label.className = 'stock-label in-stock';
        }

        // Send AJAX request
        fetch('<?php echo URL_ROOT; ?>/admin/products/toggle-stock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                product_id: productId,
                out_of_stock: isOutOfStock ? 1 : 0
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                // Revert UI changes if request failed
                checkbox.checked = !isOutOfStock;
                if (!isOutOfStock) {
                    label.textContent = 'Hết hàng';
                    label.className = 'stock-label out-of-stock';
                } else {
                    label.textContent = 'Còn hàng';
                    label.className = 'stock-label in-stock';
                }
                alert('Lỗi: ' + (data.message || 'Không thể cập nhật trạng thái hàng'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert UI changes if request failed
            checkbox.checked = !isOutOfStock;
            if (!isOutOfStock) {
                label.textContent = 'Hết hàng';
                label.className = 'stock-label out-of-stock';
            } else {
                label.textContent = 'Còn hàng';
                label.className = 'stock-label in-stock';
            }
            alert('Có lỗi xảy ra khi cập nhật trạng thái hàng');
        });
    };

    // Auto-submit form khi thay đổi danh mục
    const categorySelect = document.getElementById('categorySelect');
    if (categorySelect) {
        categorySelect.addEventListener('change', function() {
            document.getElementById('categoryFilterForm').submit();
        });
    }

    // Fade in content
    const productTable = document.querySelector('.table-responsive');
    if (productTable) {
        productTable.style.opacity = '0';
        setTimeout(function() {
            productTable.style.transition = 'opacity 0.5s ease';
            productTable.style.opacity = '1';
        }, 100);
    }
    
    // Add tooltip to buttons
    const buttons = document.querySelectorAll('[data-toggle="tooltip"]');
    buttons.forEach(button => {
        button.setAttribute('title', button.getAttribute('data-original-title') || button.getAttribute('aria-label'));
    });
    
    // Handle image loading errors with multiple fallback paths
    const productImages = document.querySelectorAll('.product-image img');
    productImages.forEach(img => {
        // Check if image is already loaded and has error
        if (img.complete && img.naturalHeight === 0) {
            handleImageError(img);
        }
    });
    
    // Lazy loading for better performance
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });
        
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
});

// This function is now handled by the window.confirmDelete above
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

.logo img {
    height: 40px;
    margin-right: 10px;
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

.header-btn .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background-color: #f44336;
    color: white;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    font-size: 0.7rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.view-site-btn {
    background-color: var(--secondary-bg-1);
    color: white;
}

.view-site-btn:hover {
    background-color: var(--plant-color);
    color: white;
}

.notification-btn {
    font-size: 1rem;
}

.btn-toggle-sidebar {
    background: none;
    border: none;
    color: var(--heading-color);
    font-size: 1.2rem;
    cursor: pointer;
    padding: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    border-radius: 6px;
    background-color: rgba(0,0,0,0.03);
}

.btn-toggle-sidebar:hover {
    background-color: rgba(0,0,0,0.07);
    transform: translateY(-2px);
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

/* Page header */
.page-header {
    margin-bottom: 20px;
    background: none;
    padding-top: 20px;
}

.page-title {
    font-size: 1.8rem;
    margin-bottom: 5px;
    font-weight: 700;
    color: var(--heading-color);
}

.page-subtitle {
    color: #666;
    margin: 0;
}

/* Content */
.content-wrapper {
    padding: 20px;
}

/* Rest of the CSS remains the same */
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
    text-align: center;
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
    text-align: center;
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
    transition: opacity 0.3s ease;
    border-radius: 4px;
}

.product-image img[src*="placeholder.svg"] {
    object-fit: contain;
    opacity: 0.7;
}

.product-image img:hover {
    opacity: 0.9;
    transform: scale(1.05);
}

/* Support for different image formats */
.product-image img[src$=".webp"],
.product-image img[src$=".jpg"],
.product-image img[src$=".jpeg"],
.product-image img[src$=".png"],
.product-image img[src$=".gif"],
.product-image img[src$=".bmp"],
.product-image img[src$=".tiff"],
.product-image img[src$=".svg"] {
    object-fit: cover;
    width: 100%;
    height: 100%;
}

/* Special handling for SVG images */
.product-image img[src$=".svg"] {
    object-fit: contain;
    padding: 2px;
}

/* Loading state */
.product-image img[src=""] {
    display: none;
}

/* Error state styling */
.product-image .no-image {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px dashed #dee2e6;
    color: #6c757d;
    font-size: 0.8rem;
    transition: all 0.3s ease;
}

.product-image .no-image:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    border-color: #adb5bd;
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #aaa;
    font-size: 1.2rem;
    text-align: center;
    background-color: #f8f9fa;
    border: 1px dashed #ddd;
}

.no-image span {
    font-size: 0.7rem;
    margin-top: 2px;
    color: #999;
}

.product-name {
    font-weight: 600;
    color: var(--heading-color);
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.price-tag {
    font-weight: 600;
    color: var(--secondary-bg-2);
}

.category-badge {
    background-color: rgba(90, 107, 0, 0.1);
    color: var(--secondary-bg-1);
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
}

.featured-badge {
    display: inline-flex;
    align-items: center;
    background-color: rgba(255, 193, 7, 0.1);
    color: #f1a600;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
}

.featured-badge i {
    margin-right: 4px;
    font-size: 0.8rem;
}

.normal-badge {
    background-color: #f0f0f0;
    color: #666;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.85rem;
}

.date-display {
    color: #666;
    font-size: 0.9rem;
}

/* Stock Toggle Switch */
.stock-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 22px;
    flex-shrink: 0;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #28a745;
    transition: .3s;
    border-radius: 22px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 16px;
    width: 16px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

input:checked + .slider {
    background-color: #dc3545;
}

input:checked + .slider:before {
    transform: translateX(22px);
}

.stock-label {
    font-size: 0.8rem;
    font-weight: 500;
    white-space: nowrap;
    min-width: 60px;
}

.stock-label.in-stock {
    color: #28a745;
}

.stock-label.out-of-stock {
    color: #dc3545;
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

.btn-view {
    background-color: var(--secondary-bg-1);
}

.btn-view:hover {
    background-color: var(--plant-color);
    transform: translateY(-2px);
}

/* Shopee Link Button */
.shopee-link-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    background-color: #ee4d2d;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.shopee-link-btn:hover {
    background-color: #d73211;
    color: white;
    text-decoration: none;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(238, 77, 45, 0.3);
}

.shopee-link-btn i {
    font-size: 0.8rem;
}

/* Modal styles */
.modal-content {
    border: none;
    border-radius: 8px;
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #eee;
}

.modal-footer {
    border-top: 1px solid #eee;
}

/* Filter section styles - enhancement */
.filter-section .card {
    border: none;
    box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
    border-radius: 10px;
    transition: all 0.3s;
    border-left: 4px solid var(--secondary-bg-1);
}

.filter-section .card:hover {
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
}

.filter-section .card-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0;
    color: var(--secondary-bg-1);
}

.filter-section .form-control {
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    padding: 10px 15px;
    font-size: 0.95rem;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
    transition: all 0.2s;
}

.filter-section .form-control:focus {
    border-color: var(--secondary-bg-1);
    box-shadow: 0 0 0 0.2rem rgba(90, 107, 0, 0.15);
}

.filter-section .input-group {
    border-radius: 8px;
    overflow: hidden;
}

.filter-section .input-group-append .btn {
    padding: 10px 20px;
    font-weight: 500;
}

.filter-section .btn-primary {
    background-color: var(--secondary-bg-1);
    border-color: var(--secondary-bg-1);
    transition: all 0.3s;
}

.filter-section .btn-primary:hover {
    background-color: var(--plant-color);
    border-color: var(--plant-color);
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

.filter-section .btn-outline-secondary {
    color: #666;
    border-color: #ddd;
    background-color: transparent;
    transition: all 0.3s;
}

.filter-section .btn-outline-secondary:hover {
    color: var(--secondary-bg-1);
    border-color: var(--secondary-bg-1);
    background-color: rgba(90, 107, 0, 0.05);
}

.filter-section .badge {
    font-size: 0.85rem;
    font-weight: 500;
    padding: 5px 12px;
    margin-right: 8px;
    border-radius: 20px;
    transition: all 0.2s;
}

.filter-section .badge-light {
    background-color: #f5f5f5;
    color: #555;
}

.filter-section .badge-info {
    background-color: rgba(23, 162, 184, 0.1);
    color: #17a2b8;
}

.filter-section .badge i {
    margin-right: 5px;
}

/* Add button styles */
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
    
    .product-name {
        max-width: 150px;
    }
    
    .header-action-buttons .btn-text {
        display: none;
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
    
    .filter-section .col-md-4 {
        margin-top: 15px;
        text-align: left !important;
    }
    
    .header-left {
        max-width: 60%;
    }
    
    .header-title {
        font-size: 1rem;
    }
    
    .page-title-wrapper .breadcrumb {
        display: none;
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
    
    .product-name {
        max-width: 100px;
    }
    
    .header-action-buttons {
        display: none;
    }
}

/* Bulk selection styles */
.product-checkbox, #selectAll {
    width: 18px;
    height: 18px;
    cursor: pointer;
    accent-color: #007bff;
}

#deleteSelectedBtn {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
}

#deleteSelectedBtn:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
}

#deleteSelectedBtn:disabled {
    background: #6c757d !important;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

.table th:first-child,
.table td:first-child {
    width: 50px;
    text-align: center;
}

/* Indeterminate checkbox styling */
#selectAll:indeterminate {
    opacity: 0.8;
}
</style>

</body>
</html>