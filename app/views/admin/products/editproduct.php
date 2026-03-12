<?php
// Increase upload limits
ini_set('upload_max_filesize', '50M');
ini_set('post_max_size', '60M');
ini_set('max_execution_time', '300');
ini_set('max_input_time', '300');
ini_set('memory_limit', '256M');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm - HeypVietNam Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .image-container {
        position: relative;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        height: 150px;
        overflow: hidden;
    }

    .image-container img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        transition: all 0.3s ease;
    }

    .delete-image-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        z-index: 10;
        background-color: rgba(255, 77, 77, 0.8);
        color: white;
        border: none;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        opacity: 0.7;
        transition: all 0.2s ease;
        font-size: 12px;
    }

    .image-container:hover .delete-image-btn {
        opacity: 1;
        transform: scale(1.1);
    }

    .delete-image-btn:hover {
        background-color: #ff3333;
        box-shadow: 0 0 5px rgba(0,0,0,0.3);
    }
    </style>
</head>
<body>

<?php
// Sử dụng dữ liệu từ controller
$product = $data['product'] ?? null;
$categories = $data['categories'] ?? [];
$sizes = $data['sizes'] ?? [];
$error = $data['error'] ?? '';

// Lấy thông báo thành công từ URL parameter
$success = isset($_GET['success']) ? $_GET['success'] : '';

if (!$product) {
    die('Không tìm thấy sản phẩm');
}

$product_id = $product['id'];

// Khởi tạo biến để lưu video của sản phẩm
$product_video = null;

// Kết nối database để xử lý AJAX requests - sử dụng cài đặt từ config.php
try {
    $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Kiểm tra và cập nhật cột size_value để có default value
    try {
        $db->exec("ALTER TABLE product_sizes MODIFY size_value VARCHAR(100) DEFAULT ''");
    } catch (PDOException $e) {
        // Bỏ qua lỗi nếu có
    }

    // Kiểm tra và thêm cột price nếu chưa tồn tại
    try {
        $check_price_column = $db->query("SHOW COLUMNS FROM product_sizes LIKE 'price'");
        if ($check_price_column->rowCount() == 0) {
            $db->exec("ALTER TABLE product_sizes ADD price DECIMAL(10,2) DEFAULT 0 AFTER size_value");
        }
    } catch (PDOException $e) {
        // Bỏ qua lỗi nếu có
    }

    // Kiểm tra và thêm cột display_order nếu chưa tồn tại
    try {
        $check_display_order_column = $db->query("SHOW COLUMNS FROM product_sizes LIKE 'display_order'");
        if ($check_display_order_column->rowCount() == 0) {
            $db->exec("ALTER TABLE product_sizes ADD display_order INT DEFAULT 0 AFTER price");
        }
    } catch (PDOException $e) {
        // Bỏ qua lỗi nếu có
    }

    // Kiểm tra và thêm cột image nếu chưa tồn tại
    try {
        $check_image_column = $db->query("SHOW COLUMNS FROM product_sizes LIKE 'image'");
        if ($check_image_column->rowCount() == 0) {
            $db->exec("ALTER TABLE product_sizes ADD image VARCHAR(255) NULL AFTER display_order");
        }
    } catch (PDOException $e) {
        // Bỏ qua lỗi nếu có
    }
    // Lấy video hiện tại của sản phẩm
    try {
        $video_stmt = $db->prepare("SELECT * FROM product_videos WHERE product_id = ? ORDER BY display_order ASC, created_at ASC LIMIT 1");
        $video_stmt->execute([$product_id]);
        $product_video = $video_stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Bỏ qua lỗi nếu bảng chưa tồn tại
    }
} catch(PDOException $e) {
    die("Lỗi kết nối database: " . $e->getMessage());
}

// Xử lý AJAX request xóa hình ảnh
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_image') {
    header('Content-Type: application/json');

    if (!isset($_POST['image_id']) || !is_numeric($_POST['image_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID hình ảnh không hợp lệ']);
        exit;
    }

    $image_id = (int)$_POST['image_id'];

    try {
        // Lấy thông tin hình ảnh trước khi xóa
        $stmt = $db->prepare("SELECT image_filename FROM product_images WHERE id = ?");
        $stmt->execute([$image_id]);
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$image) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy hình ảnh']);
            exit;
        }

        // Xóa file vật lý
        $image_path = getPublicPath('img/products/' . $image['image_filename']);
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        // Xóa record trong database
        $delete_stmt = $db->prepare("DELETE FROM product_images WHERE id = ?");
        $delete_stmt->execute([$image_id]);

        echo json_encode(['success' => true, 'message' => 'Xóa hình ảnh thành công']);
        exit;

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        exit;
    }
}

// Xử lý AJAX request xóa video
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_video') {
    header('Content-Type: application/json');

    if (!isset($_POST['video_id']) || !is_numeric($_POST['video_id'])) {
        echo json_encode(['success' => false, 'message' => 'ID video không hợp lệ']);
        exit;
    }

    $video_id = (int)$_POST['video_id'];

    try {
        // Lấy thông tin video để xóa file
        $video_stmt = $db->prepare("SELECT * FROM product_videos WHERE id = ?");
        $video_stmt->execute([$video_id]);
        $video = $video_stmt->fetch(PDO::FETCH_ASSOC);

        if ($video) {
            // Xóa file video
            $video_path = getPublicPath('videos/products/' . $video['video_filename']);
            if (file_exists($video_path)) {
                unlink($video_path);
            }

            // Xóa record trong database
            $delete_stmt = $db->prepare("DELETE FROM product_videos WHERE id = ?");
            $result = $delete_stmt->execute([$video_id]);

            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa video']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy video']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Lỗi database: ' . $e->getMessage()]);
    }
    exit;
}

// Form sẽ được xử lý bởi controller
?>

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
                            <h1 class="page-title">Chỉnh sửa sản phẩm</h1>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="<?php echo URL_ROOT; ?>/admin/products">Sản phẩm</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa sản phẩm</li>
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
                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <?php if (!empty($success)): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>
                            
                            <form action="<?php echo URL_ROOT; ?>/admin/products/edit/<?php echo $product['id']; ?>" method="post" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="price" class="form-label">Giá cơ bản <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="price" name="price" value="<?php echo $product['price']; ?>" min="0" step="1000" required>
                                                <span class="input-group-text">VND</span>
                                            </div>
                                            <small class="form-text text-muted">Giá hiển thị mặc định khi không chọn kích thước</small>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                                            <select class="form-select" id="category_id" name="category_id" required>
                                                <option value="">-- Chọn danh mục --</option>
                                                <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo ($product['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="shopee_link" class="form-label">Link Shopee <span class="text-danger">*</span></label>
                                            <input type="url" class="form-control" id="shopee_link" name="shopee_link" value="<?php echo isset($product['shopee_link']) ? htmlspecialchars($product['shopee_link']) : ''; ?>" placeholder="https://shopee.vn/..." required>
                                            <small class="form-text text-muted">Link sản phẩm trên Shopee để khách hàng có thể mua hàng</small>
                                        </div>

                                        <div class="mb-3 form-check">
                                            <input type="checkbox" class="form-check-input" id="featured" name="featured" <?php echo ($product['featured'] == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="featured">Sản phẩm nổi bật</label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Hình ảnh</label>
                                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                                            <small class="form-text text-muted">Kích thước tối đa: 2MB. Định dạng hỗ trợ: JPG, PNG, GIF. Để trống nếu không muốn thay đổi ảnh.</small>
                                            
                                            <?php if (!empty($product['image'])): ?>
                                            <div class="mt-2">
                                                <p>Ảnh hiện tại:</p>
                                                <img src="<?php echo URL_ROOT; ?>/image.php?type=product&id=<?php echo $product['id']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-height: 200px; max-width: 100%;">
                                            </div>
                                            <?php endif; ?>
                                            
                                            <div id="image-preview-container" class="mt-2" style="display: none;">
                                                <p>Ảnh mới:</p>
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
                                            
                                            <?php
                                            // Get existing additional images
                                            try {
                                                $additional_images_stmt = $db->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order ASC, created_at ASC");
                                                $additional_images_stmt->execute([$product_id]);
                                                $additional_images = $additional_images_stmt->fetchAll(PDO::FETCH_ASSOC);
                                                
                                                if (!empty($additional_images)) {
                                                    echo '<div class="mt-3"><p>Hình ảnh bổ sung hiện tại:</p>';
                                                    echo '<div class="row">';
                                                    
                                                    foreach ($additional_images as $img) {
                                                        echo '<div class="col-md-4 mb-3">';
                                                        echo '<div class="image-container">';
                                                        echo '<img src="' . URL_ROOT . '/image.php?type=additional&id=' . $img['id'] . '">';
                                                        echo '<button type="button" class="delete-image-btn" data-id="' . $img['id'] . '"><i class="fas fa-times"></i></button>';
                                                        echo '</div>';
                                                        echo '</div>';
                                                    }
                                                    
                                                    echo '</div></div>';
                                                }
                                            } catch(PDOException $e) {
                                                // Silently fail, just don't show additional images
                                            }
                                            ?>
                                        </div>

                                        <div class="mb-3">
                                            <label for="video" class="form-label">Video sản phẩm (tùy chọn)</label>
                                            <input type="file" class="form-control" id="video" name="video" accept="video/*">
                                            <small class="form-text text-muted">
                                                Kích thước tối đa: <?php echo min(ini_get('upload_max_filesize'), ini_get('post_max_size'), '50MB'); ?>.
                                                Định dạng hỗ trợ: MP4, WEBM, AVI, MOV. Để trống nếu không muốn thay đổi video.
                                            </small>

                                            <?php if (!empty($product_video)): ?>
                                            <div class="mt-2">
                                                <p>Video hiện tại:</p>
                                                <video controls style="max-height: 200px; max-width: 100%;">
                                                    <source src="<?php echo URL_ROOT; ?>/public/videos/products/<?php echo $product_video['video_filename']; ?>" type="video/<?php echo $product_video['video_type']; ?>">
                                                    Your browser does not support the video tag.
                                                </video>
                                                <div class="mt-2">
                                                    <button type="button" class="btn btn-sm btn-danger" id="delete-video-btn" data-video-id="<?php echo $product_video['id']; ?>">
                                                        <i class="fas fa-trash"></i> Xóa video
                                                    </button>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                            <div id="video-preview-container" class="mt-2" style="display: none;">
                                                <p>Video mới:</p>
                                                <video id="video-preview" controls style="max-height: 200px; max-width: 100%;">
                                                    Your browser does not support the video tag.
                                                </video>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả sản phẩm</label>
                                    <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                </div>
                                
                                <!-- Product Details Section -->
                                <div class="mb-4">
                                    <h5 class="mb-3">Chi tiết sản phẩm</h5>

                                </div>
                                
                                <!-- Custom Fields Section -->
                                <div class="mb-4">
                                    <h5 class="mb-3">Thông tin tùy chỉnh</h5>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i> Bạn có thể thêm các thông tin tùy chỉnh khác cho sản phẩm.
                                    </div>
                                    
                                    <div id="custom-fields-container">
                                        <?php
                                        // Hiển thị custom fields nếu có
                                        if (isset($customFields) && is_array($customFields) && count($customFields) > 0): ?>
                                            <?php foreach ($customFields as $customField): ?>
                                                <div class="custom-field-row">
                                                    <div class="row mb-3 align-items-center">
                                                        <input type="hidden" name="custom_field_id[]" value="<?php echo $customField['id']; ?>">
                                                        <input type="hidden" name="custom_field_display_order[]" value="<?php echo $customField['display_order']; ?>">
                                                        <div class="col-md-4">
                                                            <input type="text" class="form-control" name="custom_field_title[]" placeholder="Tiêu đề" value="<?php echo htmlspecialchars($customField['field_title']); ?>">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <textarea class="form-control" name="custom_field_content[]" rows="2" placeholder="Nội dung"><?php echo htmlspecialchars($customField['field_content']); ?></textarea>
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
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                        
                                        <!-- Chỉ hiển thị field trống nếu chưa có custom fields nào -->
                                        <?php if (!isset($customFields) || empty($customFields)): ?>
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
                                        <?php endif; ?>
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
                                        <?php if (!empty($sizes)): ?>
                                            <?php foreach ($sizes as $loop_index => $size): ?>
                                                <div class="size-row">
                                                    <div class="row mb-3 align-items-start">
                                                        <input type="hidden" name="size_id[]" value="<?php echo $size['id']; ?>">
                                                        <input type="hidden" name="display_order[]" value="<?php echo $size['display_order']; ?>">
                                                        <div class="col-md-3 size-column">
                                                            <label class="form-label small">Tên kích thước</label>
                                                            <input type="text" class="form-control" name="size[]" placeholder="Tên kích thước (VD: S, M, L)" value="<?php echo htmlspecialchars($size['size_name']); ?>">
                                                        </div>
                                                        <div class="col-md-2 price-column">
                                                            <label class="form-label small">Giá</label>
                                                            <div class="input-group">
                                                                <input type="number" class="form-control" name="size_price[]" placeholder="Giá" min="0" step="1000" value="<?php echo $size['price']; ?>">
                                                                <span class="input-group-text">VND</span>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small">Ảnh kích thước (tùy chọn)</label>
                                                            <input type="file" class="form-control size-image-input" name="size_images[]" accept="image/*">
                                                            <input type="hidden" name="existing_size_images[]" value="<?php echo isset($size['image']) ? $size['image'] : ''; ?>">
                                                            <div class="size-image-preview mt-2" <?php echo !empty($size['image']) ? '' : 'style="display: none;"'; ?>>
                                                                <img src="<?php echo !empty($size['image']) ? URL_ROOT . '/image.php?type=size&id=' . $size['id'] : ''; ?>" alt="Preview" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                                                                <?php if (!empty($size['image'])): ?>
                                                                    <button type="button" class="btn btn-sm btn-danger mt-1 remove-size-image" data-size-id="<?php echo $size['id']; ?>">
                                                                        <i class="fas fa-trash"></i> Xóa ảnh
                                                                    </button>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small">&nbsp;</label>
                                                            <div class="d-flex">
                                                                <button type="button" class="btn btn-outline-secondary move-up me-1" title="Di chuyển lên"><i class="fas fa-arrow-up"></i></button>
                                                                <button type="button" class="btn btn-outline-secondary move-down me-2" title="Di chuyển xuống"><i class="fas fa-arrow-down"></i></button>
                                                            <button type="button" class="btn btn-danger remove-size"><i class="fas fa-times"></i> Xóa</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="size-row">
                                                <div class="row mb-3 align-items-start">
                                                    <input type="hidden" name="size_id[]" value="0">
                                                    <input type="hidden" name="display_order[]" value="<?php echo count($sizes); ?>">
                                                    <div class="col-md-3 size-column">
                                                        <label class="form-label small">Tên kích thước</label>
                                                        <input type="text" class="form-control" name="size[]" placeholder="Tên kích thước (VD: S, M, L)" required>
                                                    </div>
                                                    <div class="col-md-2 price-column">
                                                        <label class="form-label small">Giá</label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" name="size_price[]" placeholder="Giá" min="0" step="1000" required>
                                                            <span class="input-group-text">VND</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small">Ảnh kích thước (tùy chọn)</label>
                                                        <input type="file" class="form-control size-image-input" name="size_images[]" accept="image/*">
                                                        <input type="hidden" name="existing_size_images[]" value="">
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
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="text-start mt-2">
                                    <button type="button" id="add-size" class="btn btn-success">
                                        <i class="fas fa-plus"></i> Thêm kích thước khác
                                    </button>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" style="padding: 10px 24px;">
                                    <i class="fas fa-save"></i> Lưu thay đổi
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

            // Process each file with WebP conversion
            files.forEach((file, index) => {
                convertWebPToJPEG(file, function(convertedFile) {
                    convertedFiles[index] = convertedFile;
                    processedCount++;

                    // Create preview
                    const reader = new FileReader();
                    const imgCol = document.createElement('div');
                    imgCol.className = 'col-md-4 mb-2';

                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'image-container position-relative';
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

    // Xử lý xóa video
    const deleteVideoBtn = document.getElementById('delete-video-btn');
    if (deleteVideoBtn) {
        deleteVideoBtn.addEventListener('click', function() {
            if (confirm('Bạn có chắc chắn muốn xóa video này?')) {
                const videoId = this.getAttribute('data-video-id');

                // Gửi AJAX request để xóa video
                const xhr = new XMLHttpRequest();
                xhr.open('POST', window.location.href, true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                // Xóa thành công, reload trang
                                window.location.reload();
                            } else {
                                alert('Lỗi: ' + response.message);
                            }
                        } catch (e) {
                            alert('Lỗi xử lý phản hồi từ máy chủ');
                        }
                    } else {
                        alert('Lỗi kết nối đến máy chủ');
                    }
                };
                xhr.send('action=delete_video&video_id=' + videoId);
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
                <input type="hidden" name="size_id[]" value="0">
                <input type="hidden" name="display_order[]" value="0">
                <div class="col-md-3 size-column">
                    <label class="form-label small">Tên kích thước</label>
                    <input type="text" class="form-control" name="size[]" placeholder="Tên kích thước (VD: S, M, L)">
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
                    <input type="hidden" name="existing_size_images[]" value="">
                    <div class="size-image-preview mt-2" style="display: none;">
                        <img src="" alt="Preview" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">&nbsp;</label>
                    <div class="d-flex">
                        <button type="button" class="btn btn-outline-secondary move-up me-1" title="Di chuyển lên"><i class="fas fa-arrow-up"></i></button>
                        <button type="button" class="btn btn-outline-secondary move-down me-2" title="Di chuyển xuống"><i class="fas fa-arrow-down"></i></button>
                    <button type="button" class="btn btn-danger remove-size"><i class="fas fa-times"></i> Xóa</button>
                    </div>
                </div>
            </div>
        `;
        sizeContainer.appendChild(sizeRow);
        updateRemoveButtons();
    });
    
    // Remove size row
    sizeContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-size') || e.target.parentElement.classList.contains('remove-size')) {
            const button = e.target.classList.contains('remove-size') ? e.target : e.target.parentElement;
            const sizeRow = button.closest('.size-row');
            sizeRow.remove();
            updateRemoveButtons();
        }
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const removeButtons = document.querySelectorAll('.remove-size');
        // Cho phép xóa tất cả sizes vì sizes không còn bắt buộc
        removeButtons.forEach(button => button.style.display = 'block');
    }
    
    updateRemoveButtons();
    
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
            updateDisplayOrder();
        }
    });
    
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

    // Remove size image functionality
    sizeContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-size-image') || e.target.parentElement.classList.contains('remove-size-image')) {
            const button = e.target.classList.contains('remove-size-image') ? e.target : e.target.parentElement;
            const sizeId = button.getAttribute('data-size-id');
            const previewContainer = button.closest('.size-image-preview');
            const hiddenInput = previewContainer.parentElement.querySelector('input[name="existing_size_images[]"]');
            const sizeRow = button.closest('.size-row');

            // Hide preview and clear hidden input
            previewContainer.style.display = 'none';
            hiddenInput.value = '';

            // Add a hidden input to mark this size image for deletion
            let deleteInput = sizeRow.querySelector('input[name="delete_size_images[]"]');
            if (!deleteInput) {
                deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_size_images[]';
                deleteInput.value = String(sizeId);
                sizeRow.appendChild(deleteInput);
            } else {
                deleteInput.value = String(sizeId);
            }

            console.log('Marked size image for deletion, size ID:', sizeId);

            // Debug: Show all delete inputs
            const allDeleteInputs = document.querySelectorAll('input[name="delete_size_images[]"]');
            console.log('All delete inputs:', Array.from(allDeleteInputs).map(input => input.value));
        }
    });

    // Initial setup
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
    
    // Xóa hình ảnh bổ sung
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-image-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.delete-image-btn');
            const imageId = btn.getAttribute('data-id');
            const imageContainer = btn.closest('.col-md-4');
            
            // Xóa container hình ảnh khỏi DOM ngay lập tức
            imageContainer.style.transition = 'opacity 0.3s ease';
            imageContainer.style.opacity = '0';
            
            setTimeout(() => {
                imageContainer.remove();
            }, 300);
            
            // Gửi request xóa ảnh trong background
            const formData = new FormData();
            formData.append('action', 'delete_image');
            formData.append('image_id', imageId);
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    console.error('Lỗi khi xóa ảnh:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
    
    // Custom Fields management
    const customFieldsContainer = document.getElementById('custom-fields-container');
    const addCustomFieldBtn = document.getElementById('add-custom-field');
    
    // Add new custom field
    addCustomFieldBtn.addEventListener('click', function() {
        const currentFieldCount = document.querySelectorAll('.custom-field-row').length;
        const newCustomFieldRow = document.createElement('div');
        newCustomFieldRow.className = 'custom-field-row';
        newCustomFieldRow.innerHTML = `
            <div class="row mb-3 align-items-center">
                <input type="hidden" name="custom_field_id[]" value="0">
                <input type="hidden" name="custom_field_display_order[]" value="${currentFieldCount}">
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
        customFieldsContainer.appendChild(newCustomFieldRow);
        updateCustomFieldRemoveButtons();
        updateCustomFieldMoveButtons();

        // Focus vào input đầu tiên của field mới
        const newTitleInput = newCustomFieldRow.querySelector('input[name="custom_field_title[]"]');
        if (newTitleInput) {
            newTitleInput.focus();
        }
    });
    
    // Remove custom field and handle move buttons
    customFieldsContainer.addEventListener('click', function(e) {
        // Handle remove custom field
        if (e.target.classList.contains('remove-custom-field') || e.target.parentElement.classList.contains('remove-custom-field')) {
            const button = e.target.classList.contains('remove-custom-field') ? e.target : e.target.parentElement;
            const customFieldRow = button.closest('.custom-field-row');
            customFieldRow.remove();
            updateCustomFieldRemoveButtons();
            updateCustomFieldMoveButtons();
        }
        
        // Handle move up/down for custom fields
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
        if (removeButtons.length === 1) {
            removeButtons[0].style.display = 'none';
        } else {
            removeButtons.forEach(button => button.style.display = 'block');
        }
    }
    
    // Update move buttons visibility for custom fields
    function updateCustomFieldMoveButtons() {
        const customFieldRows = document.querySelectorAll('.custom-field-row');
        customFieldRows.forEach((row, index) => {
            const moveUpBtn = row.querySelector('.move-custom-field-up');
            const moveDownBtn = row.querySelector('.move-custom-field-down');
            const displayOrderInput = row.querySelector('input[name="custom_field_display_order[]"]');
            
            // Update display order
            if (displayOrderInput) {
                displayOrderInput.value = index;
            }
            
            if (moveUpBtn) {
                moveUpBtn.style.display = index === 0 ? 'none' : 'inline-block';
            }
            if (moveDownBtn) {
                moveDownBtn.style.display = index === customFieldRows.length - 1 ? 'none' : 'inline-block';
            }
        });
    }
    
    // Initialize custom field buttons
    updateCustomFieldRemoveButtons();
    updateCustomFieldMoveButtons();

    // Debug form submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form is being submitted');

            // Debug delete size images
            const deleteInputs = document.querySelectorAll('input[name="delete_size_images[]"]');
            console.log('Delete size images inputs:', Array.from(deleteInputs).map(input => input.value));

            if (deleteInputs.length > 0) {
                alert('Found ' + deleteInputs.length + ' size images to delete: ' + Array.from(deleteInputs).map(input => input.value).join(', '));
            }

            // Log all custom field data
            const titles = document.querySelectorAll('input[name="custom_field_title[]"]');
            const contents = document.querySelectorAll('textarea[name="custom_field_content[]"]');

            console.log('Custom field titles:', Array.from(titles).map(input => input.value));
            console.log('Custom field contents:', Array.from(contents).map(textarea => textarea.value));

            // Check if any custom fields are empty
            let hasEmptyFields = false;
            let validFields = 0;
            titles.forEach((title, index) => {
                const content = contents[index];
                const titleValue = title.value.trim();
                const contentValue = content.value.trim();

                console.log(`Field ${index + 1}: Title="${titleValue}", Content="${contentValue}"`);

                if (titleValue === '' && contentValue !== '') {
                    console.warn(`Custom field ${index + 1} has content but no title`);
                    hasEmptyFields = true;
                } else if (titleValue !== '' && contentValue === '') {
                    console.warn(`Custom field ${index + 1} has title but no content`);
                    hasEmptyFields = true;
                } else if (titleValue !== '' && contentValue !== '') {
                    validFields++;
                    console.log(`Custom field ${index + 1} is valid`);
                }
            });

            console.log(`Total valid custom fields: ${validFields}`);

            if (hasEmptyFields) {
                console.warn('Some custom fields are incomplete');
            }

            // Log form data
            const formData = new FormData(form);
            console.log('Form data entries:');
            for (let [key, value] of formData.entries()) {
                if (key.includes('custom_field')) {
                    console.log(`${key}: ${value}`);
                }
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