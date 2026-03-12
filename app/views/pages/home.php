
<!-- Hero Section -->
<section class="home-hero">
    <div class="hero-container">
        <div class="hero-content">
            <p class="hero-tagline">SẢN PHẨM XANH CHO LỐI SỐNG BỀN VỮNG</p>
            <h1 class="hero-title">
                Sản Phẩm Xanh Cho Lối Sống Bền Vững
            </h1>
            <p class="hero-subtitle">
                HEYP cung cấp các sản phẩm làm sạch trong gia đình từ xà phòng truyền thống, thân thiện môi trường, giúp bạn xây dựng lối sống bền vững, an lành và hạnh phúc. 
            </p>
            <a href="<?php echo URL_ROOT; ?>/products" class="cta-button">Khám Phá Sản Phẩm</a>
        </div>
        <div class="hero-image">
            <div class="hero-image-container">
                <?php if(defined('APP_ROOT') && file_exists(APP_ROOT . '/public/img/hero-home.jpg')): ?>
                    <img src="<?php echo URL_ROOT; ?>/public/img/hero-home.jpg" alt="Sản phẩm xanh Heyp">
                <?php else: ?>
                    <img src="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png" alt="Heyp Logo">
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="feature-categories">
    <div class="container">
        <div class="section-title-wrapper">
            <h2 class="section-title">Danh Mục Sản Phẩm</h2>
        </div>
        <div class="category-container">
        <div class="category-grid">
            <?php foreach($data['categories'] as $category) : ?>
                <div class="category-card">
                    <div class="category-img-container">
                        <img src="<?php echo URL_ROOT; ?>/public/img/categories/<?php echo $category['image']; ?>" alt="<?php echo $category['name']; ?>" class="category-img">
                    </div>
                    <div class="category-info">
                        <h3 class="category-title"><?php echo $category['name']; ?></h3>
                        <p class="category-desc"><?php echo nl2br(htmlspecialchars(substr($category['description'], 0, 80))); ?>...</p>
                        <a href="<?php echo URL_ROOT; ?>/products/category/<?php echo $category['id']; ?>" class="btn eco-btn">Xem Sản Phẩm</a>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <div class="container">
        <div class="section-title-wrapper">
            <h2 class="section-title"><?php echo $data['featured_title']; ?></h2>
            <?php if(!empty($data['featured_description'])): ?>
            <p class="section-description"><?php echo nl2br(htmlspecialchars($data['featured_description'])); ?></p>
            <?php endif; ?>
        </div>
        <div class="products-grid">
            <?php foreach($data['featuredProducts'] as $product) : ?>
                <a href="<?php echo URL_ROOT; ?>/products/show/<?php echo $product['id']; ?>" class="product-card">
                    <div class="product-img-container">
                        <img src="<?php echo URL_ROOT; ?>/image.php?type=main&id=<?php echo $product['id']; ?>" alt="<?php echo $product['name']; ?>" class="product-img">
                        <?php if(isset($product['out_of_stock']) && $product['out_of_stock']): ?>
                        <div class="out-of-stock-overlay">
                            <div class="out-of-stock-badge">
                                <i class="fas fa-times-circle"></i>
                                <span>HẾT HÀNG</span>
                            </div>
                        </div>
                        <?php elseif(isset($product['discount']) && $product['discount'] > 0): ?>
                        <div class="product-badge">
                            <span class="discount"><?php echo $product['discount']; ?>%</span>
                            <span class="badge-label">GIẢM</span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3 class="product-title"><?php echo $product['name']; ?></h3>
                        <div class="product-price"><?php echo number_format($product['price'], 0, ',', '.'); ?>đ</div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <div class="section-title-wrapper">
                    <h2 class="section-title">Về HEYP</h2>
                </div>
                <p>HEYP là thương hiệu địa phương tại Daklak, HEYP hướng tới cung cấp các sản phẩm làm sạch, bảo vệ cho gia đình bạn, được làm từ nguyên liệu thiên nhiên, không phụ gia. HEYP cũng vô cùng tự hào vì đi theo con đường ủng hộ bảo vệ môi trường bằng cách hạn chế tối đa bao bì nhựa trong đóng gói và vận chuyển.</p>
                <a href="<?php echo URL_ROOT; ?>/about" class="btn"><?php echo isset($data['home_about_button_text']) ? $data['home_about_button_text'] : 'Tìm Hiểu Thêm'; ?></a>
            </div>
            <div class="about-img">
                <img src="<?php echo URL_ROOT; ?>/public/img/products/vn-11134210-7r98o-lqarr1ni06vm7c.webp" alt="About Heyp">
            </div>
        </div>
    </div>
</section>

<style>
/* Home Hero Section */
.home-hero {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 40px 0 80px 0;
    min-height: 70vh;
    display: flex;
    align-items: center;
    margin-top: 20px;
}

.hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
}

.hero-content {
    max-width: 500px;
}

.hero-tagline {
    color: #6c757d;
    font-size: 0.9rem;
    font-weight: 500;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 20px;
}

.hero-title {
    font-size: 3.2rem;
    font-weight: 300;
    color: #2c3e50;
    line-height: 1.2;
    margin-bottom: 30px;
    font-family: 'Georgia', serif;
}

.hero-subtitle {
    font-size: 1.1rem;
    color: var(--text-color);
    line-height: 1.6;
    margin-bottom: 35px;
    opacity: 0.9;
}

.cta-button {
    display: inline-block;
    background: var(--secondary-bg-1);
    color: white;
    padding: 15px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(90, 107, 0, 0.3);
}

.cta-button:hover {
    background: #4a5700;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(90, 107, 0, 0.4);
    color: white;
    text-decoration: none;
}

.hero-image {
    display: flex;
    justify-content: center;
    align-items: center;
}

.hero-image-container {
    width: 100%;
    max-width: 450px;
    height: 450px;
    border-radius: 50%;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.hero-image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.hero-image-container:hover img {
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero-container {
        gap: 40px;
    }

    .hero-title {
        font-size: 2.8rem;
    }

    .hero-image-container {
        max-width: 380px;
        height: 380px;
    }
}

@media (max-width: 768px) {
    .hero-container {
        grid-template-columns: 1fr;
        gap: 30px;
        text-align: center;
        padding: 0 15px;
    }

    .hero-title {
        font-size: 2.2rem;
        line-height: 1.1;
    }

    .hero-subtitle {
        font-size: 1rem;
        margin-bottom: 25px;
    }

    .hero-image-container {
        max-width: 280px;
        height: 280px;
        margin: 0 auto;
    }

    .home-hero {
        padding: 40px 0 60px 0;
        min-height: auto;
    }

    .cta-button {
        padding: 12px 25px;
        font-size: 0.9rem;
        width: auto;
        min-width: 200px;
    }
}

@media (max-width: 480px) {
    .hero-container {
        padding: 0 10px;
    }

    .hero-title {
        font-size: 1.8rem;
    }

    .hero-subtitle {
        font-size: 0.95rem;
    }

    .hero-image-container {
        max-width: 240px;
        height: 240px;
    }

    .home-hero {
        padding: 30px 0 40px 0;
    }

    .cta-button {
        display: block;
        width: 90%;
        margin: 0 auto;
        text-align: center;
    }
}

.section-title {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2rem;
    color: var(--heading-color);
    display: inline-block;
}

/* .section-title:after đã bị xóa để loại bỏ dấu gạch chân */

/* Responsive cho section-title - đồng bộ với hero-title */
@media (max-width: 1024px) {
    .section-title {
        font-size: 2.4rem;
        margin-bottom: 1.8rem;
    }
}

@media (max-width: 768px) {
    .section-title {
        font-size: 2rem;
        margin-bottom: 1.5rem;
    }
}

@media (max-width: 480px) {
    .section-title {
        font-size: 1.6rem;
        margin-bottom: 1.2rem;
    }
}

@media (max-width: 400px) {
    .section-title {
        font-size: 1.4rem;
        margin-bottom: 1rem;
    }
}

.section-description {
    text-align: center;
    max-width: 800px;
    margin: 0 auto 30px;
}

/* Feature Categories Section */
.feature-categories {
    padding: 80px 0;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    position: relative;
}

.feature-categories::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--secondary-bg-1), transparent);
}

/* Add specific styling for the title wrapper to center the title */
.section-title-wrapper {
    text-align: center;
    width: 100%;
    display: block;
    margin-bottom: 30px;
}

/* Container để căn giữa grid */
.category-container {
    display: flex;
    justify-content: center;
    width: 100%;
}

.category-grid {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 15px;
    margin-top: 20px;
    max-width: 1200px;
    margin-left: auto;
    margin-right: auto;
}

.category-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: var(--accent-color);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.category-img-container {
    height: 150px;
    overflow: hidden;
}

.category-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.category-card:hover .category-img {
    transform: scale(1.05);
}

.category-info {
    padding: 12px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.category-title {
    margin-top: 0;
    margin-bottom: 8px;
    font-size: 1rem;
    color: var(--heading-color);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.category-desc {
    margin-bottom: 12px;
    color: var(--text-color);
    flex-grow: 1;
    font-size: 0.85rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.eco-btn {
    background: transparent;
    color: var(--secondary-bg-1);
    border: 2px solid var(--secondary-bg-1);
    padding: 8px 12px;
    font-size: 0.9rem;
    text-align: center;
}

.eco-btn:hover {
    background: var(--secondary-bg-1);
    color: var(--accent-color);
}

/* Products Section */
.products-section {
    padding: 80px 0;
    background: linear-gradient(135deg, var(--primary-bg) 0%, #f5f0d0 100%);
    position: relative;
}

.products-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--secondary-bg-2), transparent);
}

.products-section .products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 20px;
    justify-items: center;
    text-align: center;
}

.product-card {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background: var(--accent-color, #fff);
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

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
}

.product-img-container {
    height: 250px;
    overflow: hidden;
    position: relative;
    background-color: #f9f9f9;
    border-bottom: 1px solid #eee;
}

.product-badge {
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

.product-badge .discount {
    font-size: 1.2rem;
    line-height: 1;
}

.product-badge .badge-label {
    font-size: 0.7rem;
    text-transform: uppercase;
}

.product-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.5s ease;
}

.product-card:hover .product-img {
    transform: scale(1.05);
}

/* Thêm đường viền cho hình ảnh sản phẩm */
.product-img-container::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border: 1px solid rgba(0,0,0,0.05);
    pointer-events: none;
}

.product-info {
    padding: 16px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    text-align: center;
    width: 100%;
    margin: 0 auto;
    align-items: center;
}

.product-title {
    margin: 0 0 12px 0;
    padding: 0 5px;
    font-size: 1.1rem;
    color: var(--heading-color, #333);
    text-align: center;
    min-height: auto;
    height: auto;
    line-height: 1.5;
    display: block;
    overflow: visible;
    white-space: normal;
    width: 100%;
    word-wrap: break-word;
}

.product-desc {
    margin-bottom: 16px;
    color: var(--text-color);
    flex-grow: 1;
    font-size: 0.95rem;
    line-height: 1.5;
    text-align: center;
    display: block;
    overflow: visible;
    -webkit-line-clamp: initial;
    -webkit-box-orient: initial;
    height: auto;
    min-height: 4.5em;
}

.product-price {
    font-weight: bold;
    color: #f05123;
    margin-bottom: 0;
    font-size: 1.1rem;
    text-align: center;
}

/* Responsive Adjustments */
@media (max-width: 1200px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
    .category-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 992px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    }
    .category-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
    .category-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .products-section .products-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    .category-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
    
    .feature-categories,
    .products-section,
    .about-section {
        padding: 50px 0;
    }
    
    /* section-title đã được responsive ở trên */
    
    .container {
        padding: 0 15px;
    }
    
    .category-img-container {
        height: 120px;
    }
    
    .category-info {
        padding: 10px;
    }
    
    .category-title {
        font-size: 0.9rem;
        white-space: normal;
    }
    
    .category-desc {
        font-size: 0.8rem;
        -webkit-line-clamp: 2;
    }
    
    .eco-btn {
        padding: 6px 10px;
        font-size: 0.8rem;
    }
    
    .product-img-container {
        height: 220px;
    }
    
    .product-info {
        padding: 12px;
    }
    
    .product-title {
        font-size: 1rem;
    }
    
    .about-text p {
        font-size: 1rem;
        line-height: 1.6;
    }
    
    .about-text .btn {
        display: block;
        text-align: center;
        max-width: 200px;
        margin: 20px auto 0;
    }
}

@media (max-width: 400px) {
    .category-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .hero-title {
        font-size: 1.6rem;
    }
    
    .hero-subtitle {
        font-size: 0.9rem;
    }
    
    .hero-image-container {
        max-width: 200px;
        height: 200px;
    }
}

@media (max-width: 390px) {
    .home-hero {
        padding: 25px 0 35px 0;
    }
    
    .hero-container {
        padding: 0 8px;
        gap: 20px;
    }
    
    .hero-title {
        font-size: 1.5rem;
        margin-bottom: 15px;
    }
    
    .hero-subtitle {
        font-size: 0.85rem;
        margin-bottom: 20px;
    }
    
    .hero-tagline {
        font-size: 0.8rem;
        letter-spacing: 1px;
        margin-bottom: 15px;
    }
    
    .hero-image-container {
        max-width: 180px;
        height: 180px;
    }
    
    .cta-button {
        padding: 10px 20px;
        font-size: 0.85rem;
        width: 85%;
    }
    
    .section-title {
        font-size: 1.3rem;
        margin-bottom: 1rem;
    }
    
    .feature-categories,
    .products-section,
    .about-section {
        padding: 40px 0;
    }
    
    .container {
        padding: 0 10px;
    }
    
    .category-grid {
        gap: 10px;
    }
    
    .category-img-container {
        height: 100px;
    }
    
    .category-info {
        padding: 8px;
    }
    
    .category-title {
        font-size: 0.85rem;
        margin-bottom: 6px;
    }
    
    .category-desc {
        font-size: 0.75rem;
        line-height: 1.3;
        -webkit-line-clamp: 2;
    }
    
    .eco-btn {
        padding: 5px 8px;
        font-size: 0.75rem;
    }
    
    .product-img-container {
        height: 200px;
    }
    
    .product-info {
        padding: 10px;
    }
    
    .product-title {
        font-size: 0.95rem;
        line-height: 1.4;
    }
    
    .product-price {
        font-size: 1rem;
    }
    
    .about-text .section-title {
        font-size: 1.8rem;
    }
    
    .about-text p {
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .about-text .btn {
        padding: 12px 25px;
        font-size: 0.9rem;
        max-width: 180px;
    }
    
    .about-img img {
        max-width: 300px;
    }
}

@media (max-width: 280px) {
    .home-hero {
        padding: 20px 0 30px 0;
        min-height: auto;
    }
    
    .hero-container {
        padding: 0 5px;
        gap: 15px;
    }
    
    .hero-title {
        font-size: 1.2rem;
        margin-bottom: 12px;
        line-height: 1.3;
    }
    
    .hero-subtitle {
        font-size: 0.8rem;
        margin-bottom: 18px;
        line-height: 1.4;
    }
    
    .hero-tagline {
        font-size: 0.7rem;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
    }
    
    .hero-image-container {
        max-width: 150px;
        height: 150px;
    }
    
    .cta-button {
        padding: 8px 15px;
        font-size: 0.8rem;
        width: 90%;
        border-radius: 25px;
    }
    
    .section-title {
        font-size: 1.1rem;
        margin-bottom: 0.8rem;
    }
    
    .feature-categories,
    .products-section,
    .about-section {
        padding: 30px 0;
    }
    
    .container {
        padding: 0 5px;
    }
    
    .category-grid {
        gap: 8px;
    }
    
    .category-img-container {
        height: 80px;
    }
    
    .category-info {
        padding: 6px;
    }
    
    .category-title {
        font-size: 0.8rem;
        margin-bottom: 4px;
    }
    
    .category-desc {
        font-size: 0.7rem;
        line-height: 1.2;
        -webkit-line-clamp: 1;
    }
    
    .eco-btn {
        padding: 4px 6px;
        font-size: 0.7rem;
    }
    
    .product-img-container {
        height: 180px;
    }
    
    .product-info {
        padding: 8px;
    }
    
    .product-title {
        font-size: 0.9rem;
        line-height: 1.3;
        margin-bottom: 8px;
    }
    
    .product-price {
        font-size: 0.95rem;
    }
    
    .about-content {
        gap: 25px;
    }
    
    .about-text .section-title {
        font-size: 1.5rem;
        margin-bottom: 20px;
    }
    
    .about-text p {
        font-size: 0.9rem;
        line-height: 1.4;
        margin-bottom: 15px;
    }
    
    .about-text .btn {
        padding: 10px 20px;
        font-size: 0.85rem;
        max-width: 160px;
        margin: 15px auto 0;
    }
    
    .about-img img {
        max-width: 250px;
        border-radius: 15px;
    }
    
    .about-img::before {
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        border-radius: 20px;
    }
}

/* About Section */
.about-section {
    padding: 80px 0;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    position: relative;
    overflow: hidden;
}

.about-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(125, 132, 113, 0.3), transparent);
}

.about-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
}

.about-text {
    color: #2c3e50;
}

.about-tagline {
    font-size: 0.9rem;
    font-weight: 600;
    color: #7d8471;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 15px;
    position: relative;
    display: inline-block;
}

.about-text .section-title {
    color: #2c3e50;
    text-align: left;
    margin-bottom: 30px;
    font-family: 'Georgia', serif;
    font-size: 2.5rem;
    font-weight: 300;
}

.about-text .section-title::after {
    left: 0;
    transform: none;
    background-color: #7d8471;
}

.about-text p {
    font-size: 1.1rem;
    line-height: 1.7;
    margin-bottom: 20px;
    color: #6c757d;
}

.about-text .btn {
    display: inline-block;
    background: #7d8471;
    color: white;
    padding: 15px 30px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    margin-top: 20px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(125, 132, 113, 0.3);
}

.about-text .btn:hover {
    background: #6a7260;
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
    box-shadow: 0 6px 20px rgba(125, 132, 113, 0.4);
}

.about-img {
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.about-img::before {
    content: '';
    position: absolute;
    top: -20px;
    left: -20px;
    right: -20px;
    bottom: -20px;
    background: linear-gradient(135deg, #7d8471, #a8b196);
    border-radius: 30px;
    opacity: 0.1;
    z-index: 0;
}

.about-img img {
    width: 100%;
    max-width: 500px;
    height: auto;
    object-fit: cover;
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(125, 132, 113, 0.2);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.about-img img:hover {
    transform: translateY(-10px);
    box-shadow: 0 30px 60px rgba(125, 132, 113, 0.3);
}

@media (max-width: 768px) {
    .about-section {
        padding: 60px 0;
    }

    .about-content {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }

    .about-text .section-title {
        text-align: center;
        font-size: 2rem;
    }

    .about-text .section-title::after {
        left: 50%;
        transform: translateX(-50%);
    }

    .about-tagline {
        text-align: center;
    }

    .about-img::before {
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
    }

    .about-img img {
        max-width: 400px;
    }
}
</style>
