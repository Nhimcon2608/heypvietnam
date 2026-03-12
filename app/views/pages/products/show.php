<!-- Product Detail -->
<section class="product-detail-section">
    <div class="container">
        <h1 class="main-product-title">CHI TIẾT SẢN PHẨM</h1>
        <nav class="breadcrumb">
            <a href="<?php echo URL_ROOT; ?>">Trang Chủ</a>
            <span class="breadcrumb-separator">&#8594;</span>
            <a href="<?php echo URL_ROOT; ?>/products">Sản Phẩm</a>
            <span class="breadcrumb-separator">&#8594;</span>
            <a href="<?php echo URL_ROOT; ?>/products/category/<?php echo $data['product']['category_id']; ?>"><?php echo $data['product']['category_name']; ?></a>
            <span class="breadcrumb-separator">&#8594;</span>
            <span class="breadcrumb-current"><?php echo $data['product']['name']; ?></span>
        </nav>
        
        <!-- Get product sizes from database -->
        <?php
        // Kết nối database để lấy thông tin kích thước sản phẩm
        // Sử dụng config từ config.php
        try {
            $db = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Lấy kích thước sản phẩm
            $stmt = $db->prepare("SELECT * FROM product_sizes WHERE product_id = ? ORDER BY display_order, id");
            $stmt->execute([$data['product']['id']]);
            $product_sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            $product_sizes = [];
        }
        ?>
        
        <!-- Main Product Section - Similar to Shopee layout -->
        <div class="product-main-section">
            <!-- Left: Product Images Gallery -->
            <div class="product-gallery">
                <div class="main-image" style="position: relative;">
                    <?php
                    // Check if product has video
                    $has_video = isset($data['productVideos']) && !empty($data['productVideos']);
                    $video = $has_video ? $data['productVideos'][0] : null;

                    // Get main image URL from database (supports DB/file fallback)
                    $main_image_url = URL_ROOT . "/image.php?type=main&id=" . $data['product']['id'];
                    ?>

                    <?php if ($has_video): ?>
                    <div id="productVideo" style="display: none;">
                        <video id="mainProductVideo" controls autoplay muted loop style="width: 100%; height: auto; max-height: 500px; object-fit: contain;">
                            <source src="<?php echo URL_ROOT; ?>/public/videos/products/<?php echo $video['video_filename']; ?>" type="video/<?php echo $video['video_type']; ?>">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                    <div id="productImage" style="display: block;">
                        <img src="<?php echo $main_image_url; ?>" alt="<?php echo $data['product']['name']; ?>" id="mainProductImage">
                    </div>
                    <?php else: ?>
                    <img src="<?php echo $main_image_url; ?>" alt="<?php echo $data['product']['name']; ?>" id="mainProductImage">
                    <?php endif; ?>

                    <?php if(isset($data['product']['out_of_stock']) && $data['product']['out_of_stock']): ?>
                    <div class="out-of-stock-overlay">
                        <div class="out-of-stock-badge" style="width: 120px; height: 120px; font-size: 0.9rem;">
                            <i class="fas fa-times-circle"></i>
                            <span>HẾT HÀNG</span>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="thumbnails-container">
                    <button class="thumbnail-nav prev" onclick="scrollThumbnails('prev')" style="display: none;">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="thumbnails" id="thumbnails">
                        <?php if ($has_video): ?>
                        <div class="thumbnail video-thumbnail" onclick="showVideo(this)" data-type="video">
                            <div style="position: relative; width: 100%; height: 100%; overflow: hidden;">
                                <video style="width: 100%; height: 100%; object-fit: cover;" muted>
                                    <source src="<?php echo URL_ROOT; ?>/public/videos/products/<?php echo $video['video_filename']; ?>" type="video/<?php echo $video['video_type']; ?>">
                                </video>
                                <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); pointer-events: none;">
                                    <i class="fas fa-play-circle" style="font-size: 2rem; color: rgba(255,255,255,0.9); text-shadow: 0 2px 4px rgba(0,0,0,0.5);"></i>
                                </div>
                                <span style="position: absolute; bottom: 2px; right: 2px; background: rgba(0,0,0,0.7); color: white; padding: 1px 4px; font-size: 10px; border-radius: 2px;">VIDEO</span>
                            </div>
                        </div>
                        <div class="thumbnail active" onclick="showImage(this, '<?php echo $main_image_url; ?>')" data-type="image"
                             data-lightbox="<?php echo $main_image_url; ?>">
                            <img src="<?php echo $main_image_url; ?>" alt="<?php echo $data['product']['name']; ?>">
                        </div>
                        <?php else: ?>
                        <div class="thumbnail active" onclick="handleThumbnailClick(this, '<?php echo $main_image_url; ?>')"
                             data-lightbox="<?php echo $main_image_url; ?>">
                            <img src="<?php echo $main_image_url; ?>" alt="<?php echo $data['product']['name']; ?>">
                        </div>
                        <?php endif; ?>
                        <?php if (isset($data['additionalImages']) && count($data['additionalImages']) > 0): ?>
                            <?php foreach ($data['additionalImages'] as $image): ?>
                                <?php
                                // Get additional image URL from database
                                $add_image_url = URL_ROOT . "/image.php?type=additional&id=" . $image['id'];
                                ?>
                                <div class="thumbnail" onclick="<?php echo $has_video ? "showImage(this, '$add_image_url')" : "handleThumbnailClick(this, '$add_image_url')"; ?>"
                                     data-lightbox="<?php echo $add_image_url; ?>" data-type="image">
                                    <img src="<?php echo $add_image_url; ?>" alt="<?php echo $data['product']['name']; ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Size Images -->
                        <?php if (!empty($product_sizes)): ?>
                            <?php foreach($product_sizes as $size): ?>
                                <?php if (!empty($size['image'])): ?>
                                    <?php
                                    // Get size image URL from database
                                    $size_image_url = URL_ROOT . "/image.php?type=size&id=" . $size['id'];
                                    ?>
                                    <div class="thumbnail size-thumbnail"
                                         data-size="<?php echo htmlspecialchars($size['size_name']); ?>"
                                         onclick="<?php echo $has_video ? "showImage(this, '$size_image_url')" : "handleThumbnailClick(this, '$size_image_url')"; ?>"
                                         data-lightbox="<?php echo $size_image_url; ?>" data-type="image">
                                        <img src="<?php echo $size_image_url; ?>" alt="<?php echo htmlspecialchars($size['size_name']); ?>">
                                        <div class="size-label"><?php echo htmlspecialchars($size['size_name']); ?></div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <button class="thumbnail-nav next" onclick="scrollThumbnails('next')" style="display: none;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="social-share">
                    <span>Chia sẻ:</span>
                    <a href="https://shopee.vn/heypvietnam" target="_blank" class="shopee-link" title="Shopee HeypVietNam"><i class="fas fa-shopping-bag"></i></a>
                    <a href="https://www.facebook.com/share/19kF6DwjHV/?mibextid=wwXIfr" target="_blank" class="facebook-link" title="Facebook HeypVietNam"><i class="fab fa-facebook"></i></a>
                    <a href="https://www.youtube.com/@hienphuongcna" target="_blank" class="youtube-link" title="YouTube HeypVietNam"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            
            <!-- Right: Product Info -->
            <div class="product-info">
                <h1 class="product-title"><?php echo $data['product']['name']; ?> - <?php echo $data['product']['category_name']; ?></h1>
                
                <div class="product-price">
                    <span class="current-price" id="displayPrice"><?php echo number_format($data['product']['price'], 0, ',', '.'); ?>đ</span>
                </div>
                
                <!-- Product Options -->
                <div class="product-options">
                    <?php if (!empty($product_sizes)): ?>
                    <!-- Size Options -->
                    <div class="option-row">
                        <div class="option-label">Kích Thước</div>
                        <div class="option-values size-options" style="display: flex; flex-wrap: wrap; gap: 8px;">
                            <?php foreach($product_sizes as $index => $size): ?>
                            <?php
                            // Check if size has image
                            $size_image_url = null;
                            if (!empty($size['image'])) {
                                $size_image_url = URL_ROOT . "/image.php?type=size&id=" . $size['id'];
                            }
                            ?>
                            <div class="option-value size-option-with-image"
                                 data-value="<?php echo htmlspecialchars($size['size_name']); ?>"
                                 data-price="<?php echo $size['price']; ?>"
                                 data-image="<?php echo $size_image_url; ?>"
                                 <?php echo $index === 0 ? 'data-default="true"' : ''; ?>>
                                <?php if ($size_image_url): ?>
                                    <div class="size-image-preview">
                                        <img src="<?php echo $size_image_url; ?>" alt="<?php echo htmlspecialchars($size['size_name']); ?>">
                                    </div>
                                <?php endif; ?>
                                <span class="size-name"><?php echo htmlspecialchars($size['size_name']); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Buy Now Button -->
                <div class="product-actions" style="margin-top: 30px;">
                    <?php if (!empty($data['product']['shopee_link'])): ?>
                        <a href="<?php echo htmlspecialchars($data['product']['shopee_link']); ?>" target="_blank" class="buy-now-btn">
                            MUA NGAY
                        </a>
                    <?php else: ?>
                        <a href="https://shopee.vn/heypvietnam" target="_blank" class="buy-now-btn">
                            MUA NGAY
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Product Details - Similar to tabbed content in Shopee -->
        <div class="product-details-section">
            <h2 class="details-heading">CHI TIẾT SẢN PHẨM</h2>
            
            <div class="product-specs">
                <div class="specs-row">
                    <div class="specs-label">Danh Mục</div>
                    <div class="specs-value"><?php echo $data['product']['category_name']; ?></div>
                </div>

                <?php if(!empty($data['product']['origin'])): ?>
                <div class="specs-row">
                    <div class="specs-label">Xuất xứ</div>
                    <div class="specs-value"><?php echo $data['product']['origin']; ?></div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($data['product']['material'])): ?>
                <div class="specs-row">
                    <div class="specs-label">Chất liệu</div>
                    <div class="specs-value"><?php echo $data['product']['material']; ?></div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($data['product']['warranty_type'])): ?>
                <div class="specs-row">
                    <div class="specs-label">Loại bảo hành</div>
                    <div class="specs-value"><?php echo $data['product']['warranty_type']; ?></div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($data['product']['manufacturer_name'])): ?>
                <div class="specs-row">
                    <div class="specs-label">Tên tổ chức chịu trách nhiệm sản xuất</div>
                    <div class="specs-value"><?php echo $data['product']['manufacturer_name']; ?></div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($data['product']['manufacturer_address'])): ?>
                <div class="specs-row">
                    <div class="specs-label">Địa chỉ tổ chức chịu trách nhiệm sản xuất</div>
                    <div class="specs-value"><?php echo $data['product']['manufacturer_address']; ?></div>
                </div>
                <?php endif; ?>
                
                <?php if(!empty($data['product']['shipping_from'])): ?>
                <div class="specs-row">
                    <div class="specs-label">Gửi từ</div>
                    <div class="specs-value"><?php echo $data['product']['shipping_from']; ?></div>
                </div>
                <?php endif; ?>
                
                <!-- Custom Fields trong Chi tiết sản phẩm -->
                <?php if(!empty($data['customFields'])): ?>
                    <?php foreach($data['customFields'] as $customField): ?>
                    <div class="specs-row">
                        <div class="specs-label"><?php echo htmlspecialchars($customField['field_title']); ?></div>
                        <div class="specs-value"><?php echo nl2br(htmlspecialchars($customField['field_content'])); ?></div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <h2 class="details-heading">MÔ TẢ SẢN PHẨM</h2>
            <div class="product-description">
                <p><?php echo nl2br(htmlspecialchars($data['product']['description'])); ?></p>
                
                <?php if(!empty($data['product']['usage_instructions'])): ?>
                <div class="usage-instructions">
                    <p><strong>Cách sử dụng:</strong></p>
                    <?php echo nl2br($data['product']['usage_instructions']); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Related Products -->
        <div class="product-details-section">
            <h2 class="details-heading">SẢN PHẨM LIÊN QUAN</h2>
            <div class="products-grid">
                <?php foreach($data['relatedProducts'] as $relatedProduct) : ?>
                    <?php if($relatedProduct['id'] != $data['product']['id']) : ?>
                        <a href="<?php echo URL_ROOT; ?>/products/show/<?php echo $relatedProduct['id']; ?>" class="product-card related-product-card">
                            <div class="product-img-container">
                                <?php
                                // Get main image from database for related product
                                $related_image_url = URL_ROOT . "/image.php?type=main&id=" . $relatedProduct['id'];
                                ?>
                                <img src="<?php echo $related_image_url; ?>" alt="<?php echo $relatedProduct['name']; ?>" class="product-img">
                                <?php if(isset($relatedProduct['out_of_stock']) && $relatedProduct['out_of_stock']): ?>
                                <div class="out-of-stock-overlay">
                                    <div class="out-of-stock-badge">
                                        <i class="fas fa-times-circle"></i>
                                        <span>HẾT HÀNG</span>
                                    </div>
                                </div>
                                <?php elseif(isset($relatedProduct['discount']) && $relatedProduct['discount'] > 0): ?>
                                <div class="product-badge">
                                    <span class="discount"><?php echo $relatedProduct['discount']; ?>%</span>
                                    <span class="badge-label">GIẢM</span>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="product-info">
                                <h3 class="product-title"><?php echo $relatedProduct['name']; ?></h3>
                                <div class="product-price"><?php echo number_format($relatedProduct['price'], 0, ',', '.'); ?>đ</div>
                            </div>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<style>
.product-detail-section {
    padding: 30px 0;
    background-color: #f5f5f5;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
    box-sizing: border-box;
    width: 100%;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    margin-bottom: 25px;
    padding: 12px 16px;
    background: rgba(248, 249, 250, 0.8);
    border-radius: 8px;
    border-left: 3px solid var(--secondary-bg-1);
}

.breadcrumb a {
    color: #6c757d;
    text-decoration: none;
    padding: 4px 8px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.breadcrumb a:hover {
    color: var(--secondary-bg-1);
    background: rgba(139, 115, 85, 0.1);
}

.breadcrumb-separator {
    color: #adb5bd;
    font-weight: 300;
    user-select: none;
}

.breadcrumb-current {
    color: var(--heading-color);
    font-weight: 500;
    padding: 4px 8px;
    background: rgba(139, 115, 85, 0.1);
    border-radius: 4px;
}

/* Main product section */
.product-main-section {
    display: grid;
    grid-template-columns: 5fr 7fr;
    gap: 30px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 20px;
}

/* Product Gallery */
.product-gallery {
    display: flex;
    flex-direction: column;
}

.main-image {
    width: 100%;
    height: 400px;
    overflow: hidden;
    border: 1px solid #eee;
    margin-bottom: 10px;
    border-radius: 4px;
}

.main-image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background-color: #f9f9f9;
}

.thumbnails {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.thumbnail {
    width: 70px;
    height: 70px;
    border: 1px solid #ddd;
    overflow: hidden;
    cursor: pointer;
    border-radius: 3px;
}

.thumbnail.active {
    border-color: var(--secondary-bg-1);
}

.thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background-color: #f9f9f9;
}

.social-share {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: 20px;
    padding: 15px 0;
    border-top: 1px solid #eee;
}

.social-share span {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

.social-share a {
    display: inline-block;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    text-align: center;
    line-height: 36px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    text-decoration: none;
}

.social-share a:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

/* Shopee Link Styling */
.social-share a.shopee-link {
    background-color: #ee4d2d;
    color: white;
}

.social-share a.shopee-link:hover {
    background-color: #d73211;
    color: white;
    box-shadow: 0 4px 8px rgba(238, 77, 45, 0.4);
}

/* Facebook Link Styling */
.social-share a.facebook-link {
    background-color: #3b5998;
    color: white;
}

.social-share a.facebook-link:hover {
    background-color: #0066ffff;
    color: white;
    box-shadow: 0 4px 8px rgba(0, 85, 255, 0.4);
}

/* YouTube Link Styling */
.social-share a.youtube-link {
    background-color: #ff0000;
    color: white;
}

.social-share a.youtube-link:hover {
    background-color: #cc0000;
    color: white;
    box-shadow: 0 4px 8px rgba(255, 0, 0, 0.4);
}

.social-btn {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    transition: all 0.3s;
}

.social-btn.facebook {
    background-color: #3b5998;
}

.social-btn.pinterest {
    background-color: #e60023;
}

.social-btn.twitter {
    background-color: #1da1f2;
}

/* Product Info */
.product-info {
    display: flex;
    flex-direction: column;
}

.product-title {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #333;
    line-height: 1.4;
}

.product-meta {
    display: flex;
    align-items: center;
    gap: 30px;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.rating {
    display: flex;
    align-items: center;
    gap: 10px;
}

.rating-score {
    font-size: 18px;
    font-weight: bold;
    color: #ee4d2d;
}

.stars {
    color: #ee4d2d;
}

.reviews-count {
    color: #666;
    font-size: 14px;
}

.sales-count {
    color: #666;
    font-size: 14px;
}

.product-price {
    background-color: #fafafa;
    padding: 15px;
    margin-bottom: 20px;
}

.current-price {
    font-size: 1.8rem;
    color: #ee4d2d;
    font-weight: bold;
}

.product-shipping {
    display: flex;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
    margin-bottom: 20px;
}

.shipping-label {
    width: 110px;
    color: #757575;
}

.shipping-info {
    flex: 1;
}

.shipping-note {
    font-size: 13px;
    color: #666;
    margin-top: 5px;
}

/* Product Options */
.product-options {
    margin-bottom: 25px;
}

.option-row {
    display: flex;
    margin-bottom: 20px;
}

.option-label {
    width: 110px;
    padding-top: 8px;
    color: #757575;
}

.option-values {
    flex: 1;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.option-value {
    padding: 8px 15px;
    border: 1px solid #ddd;
    border-radius: 3px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.2s;
    position: relative;
}

.option-value:hover, .option-value.selected {
    color: var(--secondary-bg-1);
    border-color: var(--secondary-bg-1);
}

/* Product Details Section */
.product-details-section {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 20px;
    margin-bottom: 30px;
    text-align: left;
}

.details-heading {
    font-size: 18px;
    text-transform: uppercase;
    color: #333;
    padding-bottom: 10px;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    font-weight: bold;
}

.product-specs {
    margin-bottom: 30px;
}

.specs-row {
    display: flex;
    padding: 8px 0;
    font-size: 14px;
    border-bottom: 1px solid #f5f5f5;
}

.specs-row:last-child {
    border-bottom: none;
}

.specs-label {
    width: 200px;
    color: #757575;
    padding-right: 20px;
}

.specs-value {
    flex: 1;
    color: #333;
}

.product-description {
    font-size: 15px;
    line-height: 1.6;
    color: #333;
    text-align: left;
}

.product-description p {
    margin-bottom: 15px;
}

.product-description ul {
    padding-left: 20px;
    margin-bottom: 15px;
}

.product-description li {
    margin-bottom: 5px;
}

/* Related Products */
.related-products {
    margin: 40px auto;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
    max-width: 1200px;
}

.section-title {
    font-size: 24px;
    text-transform: uppercase;
    color: #333;
    margin: 0 auto 30px;
    text-align: center;
    position: relative;
    padding-bottom: 15px;
    font-weight: bold;
    display: inline-block;
}

.section-title:after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background-color: #5a6b00;
    display: block;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 25px;
    margin: 20px auto 0;
    width: 100%;
    justify-items: center;
    text-align: center;
}

.products-grid .product-card {
    text-decoration: none;
    color: inherit;
    background-color: var(--accent-color);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
    text-align: center;
    align-items: center;
}

.products-grid .product-img-container {
    height: 200px;
    overflow: hidden;
    position: relative;
    background-color: #f9f9f9;
    width: 100%;
    border-bottom: 1px solid #eee;
}

.products-grid .product-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background-color: #f9f9f9;
}

.products-grid .product-badge {
    position: absolute;
    bottom: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    background-color: #f00;
    color: white;
    width: 50px;
    height: 50px;
    justify-content: center;
    align-items: center;
    text-align: center;
    font-weight: bold;
}

.products-grid .product-badge .discount {
    font-size: 1.2rem;
    line-height: 1;
}

.products-grid .product-badge .badge-label {
    font-size: 0.7rem;
    text-transform: uppercase;
}

.products-grid .product-info {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    text-align: center;
    align-items: center;
    justify-content: center;
    margin: 0;
    width: 100%;
    min-height: 100px;
}

.products-grid .product-info h3 {
    width: 100%;
    text-align: center !important;
    margin: 0 auto 12px auto;
}

.products-grid .product-title {
    width: 100%;
    text-align: center;
    margin: 0 0 12px 0;
    padding: 0 5px;
    font-size: 0.95rem;
    color: #333;
    height: auto;
    min-height: auto;
    line-height: 1.5;
    display: block;
    overflow: visible;
    white-space: normal;
    word-wrap: break-word;
    text-align: center !important;
    margin-left: auto;
    margin-right: auto;
}

.products-grid .product-price {
    font-weight: bold;
    color: #f05123;
    margin: 0 auto;
    font-size: 1.1rem;
    text-align: center !important;
    width: 100%;
}

/* Override variables to make sure colors work */
:root {
    --secondary-bg-1-rgb: 90, 107, 0;
}

/* Responsive */
@media (max-width: 1200px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
}

@media (max-width: 992px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
}

.related-products .product-card .product-info {
    text-align: center;
}

.related-products .product-card .product-title {
    text-align: center;
}

.product-details-section .product-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;
    position: relative;
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
    text-decoration: none;
    color: inherit;
}

.product-details-section .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.main-product-title {
    font-size: 24px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
    text-transform: uppercase;
    padding-bottom: 10px;
}

.quantity-selector,
.quantity-controls,
.quantity-btn,
#productQuantity {
    display: none !important;
}

/* Buy Now Button Styles */
.buy-now-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #ee4d2d, #ff6b35);
    color: white;
    padding: 18px 40px;
    border: none;
    border-radius: 8px;
    font-size: 20px;
    font-weight: bold;
    text-decoration: none;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(238, 77, 45, 0.3);
    cursor: pointer;
    min-width: 300px;
}

.buy-now-btn:hover {
    background: linear-gradient(135deg, #d73211, #ee4d2d);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(238, 77, 45, 0.4);
    color: white;
    text-decoration: none;
}

.buy-now-btn:active {
    transform: translateY(0);
    box-shadow: 0 2px 10px rgba(238, 77, 45, 0.3);
}

.buy-now-btn i {
    font-size: 18px;
}

.related-product-card {
    text-decoration: none;
    color: inherit;
    background-color: var(--accent-color);
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
    height: 100%;
    width: 100%;
    text-align: center;
    align-items: center;
}

.related-product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.related-product-card .product-title {
    width: 100%;
    text-align: center;
    margin: 0 0 12px 0;
    padding: 0 5px;
    font-size: 0.95rem;
    color: #333;
    height: auto;
    min-height: auto;
    line-height: 1.5;
    display: block;
    overflow: visible;
    white-space: normal;
    word-wrap: break-word;
    text-align: center !important;
    margin-left: auto;
    margin-right: auto;
}

.related-product-card .product-price {
    font-weight: bold;
    color: #f05123;
    margin: 0 auto;
    font-size: 1.1rem;
    text-align: center !important;
    width: 100%;
}

/* Size Options with Images */
.size-option-with-image {
    display: flex;
    flex-direction: row;
    align-items: center;
    padding: 8px 12px;
    border: 2px solid #ddd;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 80px;
    margin: 0 5px 5px 0;
    background: white;
    gap: 8px;
}

.size-option-with-image:hover {
    border-color: #5A6B00;
}

.size-option-with-image.selected {
    border-color: #5A6B00;
    border-width: 3px;
}

.size-image-preview {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    overflow: hidden;
    border: 1px solid #eee;
    flex-shrink: 0;
}

.size-image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.size-name {
    font-size: 1rem;
    font-weight: 500;
    text-align: left;
    line-height: 24px;
}

/* Size option without image - center text */
.size-option-with-image:not(:has(.size-image-preview)) .size-name {
    text-align: center;
}

/* Size Thumbnails */
.size-thumbnail {
    position: relative;
}

.size-thumbnail .size-label {
    position: absolute;
    bottom: 2px;
    left: 2px;
    right: 2px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    font-size: 0.7rem;
    text-align: center;
    padding: 2px;
    border-radius: 2px;
}

/* Thumbnail carousel */
.thumbnails-container {
    position: relative;
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.thumbnails {
    display: flex;
    gap: 10px;
    overflow-x: auto;
    scroll-behavior: smooth;
    scrollbar-width: none;
    -ms-overflow-style: none;
    max-width: 100%;
}

.thumbnails::-webkit-scrollbar {
    display: none;
}

.thumbnail-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: background-color 0.3s ease;
}

.thumbnail-nav:hover {
    background: rgba(0, 0, 0, 0.7);
}

.thumbnail-nav.prev {
    left: -15px;
}

.thumbnail-nav.next {
    right: -15px;
}

.thumbnail {
    cursor: pointer;
    transition: transform 0.3s ease;
    flex-shrink: 0;
}

.thumbnail:hover {
    transform: scale(1.05);
}

/* Main product image clickable */
#mainProductImage {
    cursor: pointer;
    transition: transform 0.3s ease;
}

#mainProductImage:hover {
    transform: scale(1.02);
}

/* Lightbox styles */
.lightbox {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.9);
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.lightbox.show {
    opacity: 1;
}

.lightbox-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lightbox-close {
    position: absolute;
    top: -40px;
    right: 0;
    color: white;
    font-size: 35px;
    font-weight: bold;
    cursor: pointer;
    z-index: 1001;
}

.lightbox-close:hover {
    color: #ccc;
}

#lightbox-image {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 100%;
    display: flex;
    justify-content: space-between;
    pointer-events: none;
}

.lightbox-prev,
.lightbox-next {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 20px;
    transition: background-color 0.3s ease;
    pointer-events: all;
}

.lightbox-prev:hover,
.lightbox-next:hover {
    background: rgba(255, 255, 255, 0.4);
}

.lightbox-prev {
    margin-left: -60px;
}

.lightbox-next {
    margin-right: -60px;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .product-main-section {
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        padding: 15px;
    }
    
    .main-image {
        height: 350px;
    }
    
    .container {
        padding: 0 15px;
    }
}

@media (max-width: 768px) {
    .product-main-section {
        grid-template-columns: 1fr;
        gap: 20px;
        padding: 15px;
    }
    
    .main-image {
        height: 300px;
        margin-bottom: 15px;
    }
    
    .thumbnails {
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .thumbnail {
        width: 60px;
        height: 60px;
    }
    
    .product-title {
        font-size: 1.3rem;
        margin-bottom: 12px;
    }
    
    .current-price {
        font-size: 1.5rem;
    }
    
    .buy-now-btn {
        padding: 15px 30px;
        font-size: 18px;
        min-width: 250px;
    }
    
    .size-option-with-image {
        min-width: 70px;
        padding: 6px 10px;
        gap: 6px;
    }

    .size-image-preview {
        width: 20px;
        height: 20px;
    }

    .size-name {
        font-size: 0.9rem;
        line-height: 20px;
    }

    .thumbnail-nav {
        width: 25px;
        height: 25px;
        font-size: 12px;
    }

    .lightbox-prev,
    .lightbox-next {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    .lightbox-prev {
        margin-left: -50px;
    }
    
    .breadcrumb {
        font-size: 12px;
        padding: 10px 12px;
        margin-bottom: 20px;
    }
    
    .main-product-title {
        font-size: 20px;
        margin-bottom: 12px;
    }
    
    .product-details-section {
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .details-heading {
        font-size: 16px;
        margin-bottom: 12px;
    }
    
    .specs-row {
        font-size: 13px;
    }
    
    .specs-label {
        width: 150px;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 15px;
    }

    .lightbox-next {
        margin-right: -60px;
    }
}

@media (max-width: 576px) {
    .main-image {
        height: 250px;
    }
    
    .thumbnail {
        width: 50px;
        height: 50px;
    }
    
    .thumbnails {
        gap: 6px;
    }
    
    .product-title {
        font-size: 1.1rem;
    }
    
    .current-price {
        font-size: 1.3rem;
    }
    
    .buy-now-btn {
        padding: 12px 25px;
        font-size: 16px;
        min-width: 200px;
        width: 100%;
    }
    
    .option-label {
        width: 80px;
        font-size: 0.9rem;
    }
    
    .size-option-with-image {
        min-width: 60px;
        padding: 5px 8px;
        gap: 4px;
    }
    
    .size-image-preview {
        width: 18px;
        height: 18px;
    }
    
    .size-name {
        font-size: 0.8rem;
        line-height: 18px;
    }
    
    .breadcrumb {
        font-size: 11px;
        padding: 8px 10px;
    }
    
    .main-product-title {
        font-size: 18px;
    }
    
    .details-heading {
        font-size: 14px;
    }
    
    .specs-row {
        font-size: 12px;
        flex-direction: column;
        gap: 5px;
    }
    
    .specs-label {
        width: 100%;
        font-weight: 600;
        color: #333;
    }
    
    .specs-value {
        margin-left: 10px;
    }
    
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .related-product-card .product-title {
        font-size: 0.8rem;
        height: 2.2em;
    }
    
    .related-product-card .product-price {
        font-size: 0.9rem;
    }
    
    .product-img-container {
        height: 140px;
    }
    
    .social-share {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .social-share span {
        width: 100%;
        margin-bottom: 5px;
    }
}

@media (max-width: 400px) {
    .main-image {
        height: 220px;
    }
    
    .product-title {
        font-size: 1rem;
    }
    
    .current-price {
        font-size: 1.2rem;
    }
    
    .buy-now-btn {
        padding: 10px 20px;
        font-size: 14px;
    }
    
    .products-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .related-product-card .product-title {
        font-size: 0.9rem;
        height: auto;
        line-height: 1.3;
    }
    
    .product-img-container {
        height: 180px;
    }
    
    .container {
        padding: 0 10px;
    }
    
    .product-main-section,
    .product-details-section {
        padding: 12px;
    }
}
    .lightbox-close {
        font-size: 30px;
        top: -35px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Price formatting function
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
    }
    
    // Get elements
    const sizeOptions = document.querySelectorAll('.size-options .option-value');
    const displayPrice = document.getElementById('displayPrice');
    
    // Current selected size data
    let currentSize = null;
    let currentPrice = <?php echo $data['product']['price']; ?>;
    
    // Size selection
    sizeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Update selection
            sizeOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');

            // Update price
            currentSize = this.dataset.value;
            currentPrice = parseFloat(this.dataset.price);

            // Update display
            displayPrice.textContent = formatPrice(currentPrice);

            // Change main image if size has image
            if (this.dataset.image && this.dataset.image !== 'null') {
                const mainImage = document.getElementById('mainProductImage');
                mainImage.src = this.dataset.image;

                // Update thumbnail selection
                document.querySelectorAll('.thumbnail').forEach(thumb => {
                    thumb.classList.remove('active');
                });

                // Find and activate corresponding size thumbnail
                const sizeThumbnail = document.querySelector(`.size-thumbnail[data-size="${currentSize}"]`);
                if (sizeThumbnail) {
                    sizeThumbnail.classList.add('active');
                }
            }
        });
    });

    // Select first size by default if sizes exist
    const defaultSize = document.querySelector('.size-options .option-value[data-default="true"]') ||
                       document.querySelector('.size-options .option-value');

    if (defaultSize) {
        defaultSize.click();
    } else {
        // No sizes available, display base price
        displayPrice.textContent = formatPrice(currentPrice);
    }

    // Image gallery
    window.changeImage = function(thumbnail, imageUrl) {
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        thumbnail.classList.add('active');
        document.getElementById('mainProductImage').src = imageUrl;

        // Update size option selection based on thumbnail
        updateSizeOptionFromThumbnail(thumbnail);
    };

    // Handle thumbnail click - change main image and open lightbox on double click
    window.handleThumbnailClick = function(thumbnail, imageUrl) {
        changeImage(thumbnail, imageUrl);
    };

    // Show video in main display
    window.showVideo = function(thumbnail) {
        // Hide image, show video
        const productVideo = document.getElementById('productVideo');
        const productImage = document.getElementById('productImage');
        const mainVideo = document.getElementById('mainProductVideo');

        if (productVideo && productImage) {
            productVideo.style.display = 'block';
            productImage.style.display = 'none';

            // Auto play video when shown
            if (mainVideo) {
                mainVideo.currentTime = 0; // Reset to beginning
                mainVideo.play().catch(function(error) {
                    console.log('Auto-play was prevented:', error);
                });
            }
        }

        // Update thumbnail active state
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        thumbnail.classList.add('active');
    };

    // Show image in main display
    window.showImage = function(thumbnail, imageUrl) {
        // Hide video, show image
        const productVideo = document.getElementById('productVideo');
        const productImage = document.getElementById('productImage');
        const mainImage = document.getElementById('mainProductImage');
        const mainVideo = document.getElementById('mainProductVideo');

        if (productVideo && productImage) {
            productVideo.style.display = 'none';
            productImage.style.display = 'block';

            // Pause video when switching to image
            if (mainVideo) {
                mainVideo.pause();
            }
        }

        // Update main image source
        if (mainImage) {
            mainImage.src = imageUrl;
        }

        // Update thumbnail active state
        document.querySelectorAll('.thumbnail').forEach(thumb => {
            thumb.classList.remove('active');
        });
        thumbnail.classList.add('active');

        // Update size option selection based on thumbnail
        updateSizeOptionFromThumbnail(thumbnail);
    };

    // Update size option selection when thumbnail changes
    function updateSizeOptionFromThumbnail(thumbnail) {
        // If it's a size thumbnail, select the corresponding size option
        if (thumbnail.classList.contains('size-thumbnail')) {
            const sizeName = thumbnail.getAttribute('data-size');
            if (sizeName) {
                // Find and select the corresponding size option using data-value
                const sizeOption = document.querySelector(`.size-options .option-value[data-value="${sizeName}"]`);
                if (sizeOption) {
                    // Remove selected class from all size options
                    document.querySelectorAll('.size-options .option-value').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    // Add selected class to the matching size option
                    sizeOption.classList.add('selected');

                    // Update current size and price
                    currentSize = sizeName;
                    currentPrice = parseFloat(sizeOption.getAttribute('data-price'));

                    // Update price display
                    displayPrice.textContent = formatPrice(currentPrice);
                }
            }
        }
        // If it's not a size thumbnail (main image or additional images),
        // keep the current size selection unchanged
    }

    // Lightbox functionality
    let currentLightboxIndex = 0;
    let lightboxImages = [];

    // Initialize lightbox images array
    function initLightbox() {
        lightboxImages = [];
        document.querySelectorAll('.thumbnail[data-lightbox]').forEach(thumb => {
            lightboxImages.push(thumb.getAttribute('data-lightbox'));
        });
    }

    // Open lightbox
    window.openLightbox = function(imageUrl) {
        currentLightboxIndex = lightboxImages.indexOf(imageUrl);
        document.getElementById('lightbox-image').src = imageUrl;
        const lightbox = document.getElementById('lightbox');
        lightbox.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        setTimeout(() => {
            lightbox.classList.add('show');
        }, 10);
    };

    // Close lightbox
    window.closeLightbox = function() {
        const lightbox = document.getElementById('lightbox');
        lightbox.classList.remove('show');
        setTimeout(() => {
            lightbox.style.display = 'none';
            document.body.style.overflow = 'auto';
        }, 300);
    };

    // Change lightbox image
    window.changeLightboxImage = function(direction) {
        currentLightboxIndex += direction;
        if (currentLightboxIndex >= lightboxImages.length) {
            currentLightboxIndex = 0;
        } else if (currentLightboxIndex < 0) {
            currentLightboxIndex = lightboxImages.length - 1;
        }
        document.getElementById('lightbox-image').src = lightboxImages[currentLightboxIndex];
    };

    // Thumbnail carousel
    window.scrollThumbnails = function(direction) {
        const container = document.getElementById('thumbnails');
        const scrollAmount = 120;
        if (direction === 'next') {
            container.scrollLeft += scrollAmount;
        } else {
            container.scrollLeft -= scrollAmount;
        }
        updateThumbnailNavigation();
    };

    // Update thumbnail navigation visibility
    function updateThumbnailNavigation() {
        const container = document.getElementById('thumbnails');
        const prevBtn = document.querySelector('.thumbnail-nav.prev');
        const nextBtn = document.querySelector('.thumbnail-nav.next');

        if (container.scrollWidth > container.clientWidth) {
            prevBtn.style.display = container.scrollLeft > 0 ? 'flex' : 'none';
            nextBtn.style.display = container.scrollLeft < (container.scrollWidth - container.clientWidth) ? 'flex' : 'none';
        } else {
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        }
    }

    // Add click event to main product image for lightbox
    document.getElementById('mainProductImage').addEventListener('click', function() {
        const activeThumb = document.querySelector('.thumbnail.active');
        if (activeThumb) {
            const imageUrl = activeThumb.getAttribute('data-lightbox');
            openLightbox(imageUrl);
        }
    });

    // Initialize
    setTimeout(() => {
        initLightbox();
        updateThumbnailNavigation();
    }, 100);

    // Update navigation on scroll
    document.getElementById('thumbnails').addEventListener('scroll', updateThumbnailNavigation);

    // Update navigation on window resize
    window.addEventListener('resize', updateThumbnailNavigation);

    // Keyboard navigation for lightbox
    document.addEventListener('keydown', function(e) {
        if (document.getElementById('lightbox').style.display === 'flex') {
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                changeLightboxImage(-1);
            } else if (e.key === 'ArrowRight') {
                changeLightboxImage(1);
            }
        }
    });

    // Initialize video thumbnail
    const videoThumbnail = document.querySelector('.video-thumbnail video');
    if (videoThumbnail) {
        // Set video to first frame for thumbnail
        videoThumbnail.addEventListener('loadeddata', function() {
            this.currentTime = 1; // Show frame at 1 second
        });

        // Prevent video from playing in thumbnail
        videoThumbnail.addEventListener('play', function() {
            this.pause();
        });
    }
});
</script>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox" onclick="closeLightbox()">
    <div class="lightbox-content" onclick="event.stopPropagation()">
        <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
        <img id="lightbox-image" src="" alt="">
        <div class="lightbox-nav">
            <button class="lightbox-prev" onclick="changeLightboxImage(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="lightbox-next" onclick="changeLightboxImage(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>