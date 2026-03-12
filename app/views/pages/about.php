<!-- Hero Section -->
<section class="about-hero">
    <div class="hero-container">
        <div class="hero-content">
            <p class="hero-tagline">VỀ HEYP - SẢN PHẨM XANH VIỆT NAM</p>
            <h1 class="hero-title">
                Khởi Nguồn Từ <span class="highlight">Tình Yêu</span><br>
                Dành Cho <span class="highlight">Thiên Nhiên</span><br>
                Và Cuộc Sống <span class="highlight">Bền Vững</span>
            </h1>
            <p class="hero-subtitle">
                Khởi Nguồn Từ Tình Yêu Dành Cho Thiên Nhiên Và Cuộc Sống Bền Vững
            </p>
            <p class="hero-subtitle">HEYP ra đời từ niềm tin rằng con người cần sự kết nối với tự nhiên. Các sản phẩm thủ công thân thiện môi trường không những đủ khả năng đáp ứng nhu cầu của con người, mang lại cho chúng ta cuộc sống mạnh khỏe, hạnh phúc mà còn góp phần bảo vệ hành tinh.</p>
        </div>
        <div class="hero-image">
            <div class="hero-image-container">
                <!-- Hiển thị logo HEYP -->
                <img src="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png" alt="HEYP Logo" class="hero-logo">
            </div>
        </div>
    </div>
</section>

<!-- Quote Section -->
<section class="quote-section">
    <div class="container">
        <p class="quote-text">
            "Mỗi sản phẩm xanh bạn chọn là một bước nhỏ hướng tới tương lai bền vững cho thế hệ mai sau."
        </p>
    </div>
</section>

<!-- Image Section 1 -->
<section class="image-section">
    <div class="container">
        <div class="image-container">
            <img src="<?php echo URL_ROOT; ?>/public/img/products/ed34bacb22af6b3972de150f2dcc2a85.webp" alt="Sản phẩm xanh Heyp">
        </div>
    </div>
</section>

<!-- Story Section -->
<section class="story-section">
    <div class="container">
        <div class="story-content">
            <div class="story-text" id="story">
                <h2>Câu Chuyện Của <em>Heyp</em></h2>
                <p>
                    Tất cả bắt đầu từ một câu hỏi đơn giản: "Làm thế nào để chúng ta có thể sống khỏe mạnh hơn
                    mà vẫn bảo vệ được môi trường?"
                </p>
                <p>
                    Heyp nhận ra rằng nhiều gia đình Việt Nam đang tìm kiếm những sản phẩm an toàn, thân thiện
                    với môi trường nhưng lại gặp khó khăn trong việc tìm được những sản phẩm chất lượng với giá cả hợp lý.
                </p>
                <p>
                    Heyp ra đời với sứ mệnh mang đến những sản phẩm xanh, sạch, an toàn cho sức khỏe và
                    thân thiện với môi trường. Heyp tin rằng mỗi lựa chọn nhỏ của bạn đều góp phần tạo nên
                    một tương lai bền vững cho thế hệ mai sau.
                </p>
                <a href="<?php echo URL_ROOT; ?>/products" class="cta-button">Khám Phá Sản Phẩm</a>
            </div>
            <div class="story-image">
                <img src="<?php echo URL_ROOT; ?>/public/img/products/e9fa5809267307de5dfe85a8e56cf6e9.webp" alt="Câu chuyện của chúng tôi">
            </div>
        </div>
    </div>
</section>

<!-- Image Section 2 -->
<section class="image-section">
    <div class="container">
        <div class="image-container">
            <img src="<?php echo URL_ROOT; ?>/public/img/products/75d255d85278de8d6f70f45f772703c7.webp" alt="Sản phẩm xanh Heyp">
        </div>
        <div class="image-container">
            <img src="<?php echo URL_ROOT; ?>/public/img/products/vn-11134210-7r98o-lqarr1ni06vm7c.webp" alt="Sản phẩm xanh Heyp">
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

<!-- Image Section 3 -->
<section class="image-section">
    <div class="container">
        <div class="image-container">
            <img src="<?php echo URL_ROOT; ?>/public/img/products/vn-11134210-7r98o-lqarr1ni1lg218.webp" alt="Cuộc sống xanh">
        </div>
        <div class="image-container">
            <img src="<?php echo URL_ROOT; ?>/public/img/products/vn-11134210-7r98o-lqarr1ni300i29.webp" alt="Cuộc sống xanh">
        </div>
        <div class="video-container">
            <div class="youtube-wrapper">
                <iframe
                    src="https://www.youtube.com/embed/1lKyby6WH-E?start=55"
                    title="Cuộc sống xanh - Heyp"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen>
                </iframe>
            </div>
        </div>
    </div>
</section>

<!-- Custom Styles for About Page -->
<style>
    .about-hero {
        background: white;
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .hero-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 60px;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .hero-content {
        z-index: 2;
        position: relative;
    }

    .hero-image {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .hero-image-container {
        width: 500px;
        height: 500px;
        border-radius: 50%;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        position: relative;
        background: #e9ecef;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .hero-image-placeholder {
        color: #6c757d;
        text-align: center;
        padding: 20px;
    }

    .hero-image-placeholder i {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
    }

    .hero-tagline {
        font-size: 0.9rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .hero-title {
        font-size: 2.5rem.2rem;
        font-weight: 150;
        line-height: 1.2;
        margin-bottom: 30px;
        color: #2c3e50;
        font-family: 'Georgia', serif;
    }

    .hero-title .highlight {
        font-style: italic;
        color: #7d8471;
    }

    .hero-subtitle {
        font-size: 1.3rem;
        color: #6c757d;
        margin-bottom: 40px;
        line-height: 1.6;
    }

    .cta-button {
        background: #7d8471;
        color: white;
        padding: 15px 35px;
        border: none;
        border-radius: 50px;
        font-size: 1.1rem;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .cta-button:hover {
        background: #6a7063;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(125, 132, 113, 0.3);
    }

    .quote-section {
        background: #f8f9fa;
        padding: 80px 0;
        text-align: center;
    }

    .quote-text {
        font-size: 1.8rem;
        font-style: italic;
        color: #495057;
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.5;
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
        background: linear-gradient(90deg, transparent, #5A6B00, transparent);
    }

    /* Add specific styling for the title wrapper to center the title */
    .section-title-wrapper {
        text-align: center;
        width: 100%;
        display: block;
        margin-bottom: 30px;
    }

    .section-title {
        text-align: center;
        margin-bottom: 2rem;
        font-size: 2rem;
        color: #2c3e50;
        display: inline-block;
    }

    /* Container để căn giữa grid */
    .category-container {
        display: flex;
        justify-content: center;
        width: 100%;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr) !important;
        gap: 15px;
        margin-top: 20px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
        width: 100%;
        min-width: 0;
    }

    .category-card {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        background: #fff;
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
        color: #2c3e50;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .category-desc {
        margin-bottom: 12px;
        color: #6c757d;
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
        color: #5A6B00;
        border: 2px solid #5A6B00;
        padding: 8px 12px;
        font-size: 0.9rem;
        text-align: center;
        text-decoration: none;
        border-radius: 4px;
        transition: all 0.3s ease;
    }

    .eco-btn:hover {
        background: #5A6B00;
        color: #fff;
        text-decoration: none;
    }

    .story-section {
        padding: 100px 0;
        background: white;
    }

    .story-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        align-items: flex-start;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        width: 100%;
    }

    /* Đảm bảo text ở bên trái, image ở bên phải */
    .story-text {
        grid-column: 1;
        order: 1;
        width: 100%;
    }

    .story-image {
        grid-column: 2;
        order: 2;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .story-text {
        padding-right: 40px;
        position: relative;
        text-align: left;
        display: block;
        width: 100%;
    }

    .story-text::after {
        content: '';
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 1px;
        height: 60%;
        background: linear-gradient(to bottom, transparent, #e9ecef, transparent);
    }

    .story-text h2 {
        font-size: 2.5rem;
        font-weight: 300;
        margin-bottom: 30px;
        color: #2c3e50;
        font-family: 'Georgia', serif;
    }

    .story-text p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #6c757d;
        margin-bottom: 20px;
        text-align: justify;
    }

    .story-image {
        padding-left: 20px;
        width: 100%;
    }

    .story-image img {
        width: 100%;
        height: auto;
        max-width: 100%;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        display: block;
    }

    .story-image img:hover {
        transform: scale(1.02);
    }

    /* Đảm bảo layout 2 cột chỉ áp dụng trên desktop */
    @media (min-width: 769px) {
        .story-content {
            display: grid !important;
            grid-template-columns: 1fr 1fr !important;
            gap: 80px !important;
        }
        
        .story-text {
            grid-column: 1 !important;
            order: 1 !important;
        }
        
        .story-image {
            grid-column: 2 !important;
            order: 2 !important;
        }
    }

    .image-section {
        padding: 100px 0;
        background: #f8f9fa;
    }

    .image-section .container {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .image-container {
        text-align: center;
    }

    .image-container img {
        width: 100%;
        height: auto;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        display: block;
        margin: 0 auto;
    }

    .video-container {
        text-align: center;
        position: relative;
    }

    .youtube-wrapper {
        position: relative;
        width: 100%;
        height: 0;
        padding-bottom: 56.25%; /* 16:9 aspect ratio */
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    }

    .youtube-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
    }

    @media (max-width: 1200px) {
        .category-grid {
            grid-template-columns: repeat(4, 1fr) !important;
        }
    }

    @media (max-width: 992px) {
        .category-grid {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }

    @media (max-width: 1024px) and (min-width: 769px) {
        .hero-image-container {
            width: 450px;
            height: 450px;
        }
    }

    @media (max-width: 1024px) {
        .hero-container {
            gap: 50px;
            padding: 0 30px;
        }
        
        .hero-title {
            font-size: 2.3rem;
        }
        
        .hero-image-container {
            width: 400px;
            height: 400px;
        }
    }

    @media (max-width: 768px) {
        .hero-container {
            grid-template-columns: 1fr;
            gap: 30px;
            text-align: center;
            padding: 0 20px;
        }

        .hero-title {
            font-size: 2rem;
            line-height: 1.1;
        }
        
        .hero-subtitle {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .hero-image-container {
            width: 320px;
            height: 320px;
            margin: 0 auto;
        }
        
        .about-hero {
            padding: 60px 0;
        }
        
        .cta-button {
            padding: 12px 25px;
            font-size: 1rem;
            min-width: 180px;
        }
        
        .quote-section {
            padding: 60px 0;
        }
        
        .quote-text {
            font-size: 1.4rem;
            padding: 0 20px;
        }
        
        .category-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

        .story-content {
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .story-text {
            padding-right: 0;
            text-align: center;
            order: 1;
            grid-column: 1;
        }

        .story-text::after {
            display: none;
        }

        .story-image {
            padding-left: 0;
            order: 2;
            grid-column: 1;
        }

        .feature-categories {
            padding: 60px 0;
        }

        .category-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .section-title {
            font-size: 2rem;
            font-family: 'Georgia', serif;
        }

        .story-section {
            padding: 60px 0;
        }

        .image-section {
            padding: 60px 0;
        }

        .image-section .container {
            max-width: 98%;
            gap: 20px;
        }

        .youtube-wrapper {
            border-radius: 10px;
        }
    }

    @media (max-width: 576px) {
        .category-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }
        
        .hero-title {
            font-size: 1.7rem;
        }
        
        .hero-subtitle {
            font-size: 1rem;
        }
        
        .hero-image-container {
            width: 280px;
            height: 280px;
        }
        
        .about-hero {
            padding: 40px 0;
        }
        
        .quote-text {
            font-size: 1.2rem;
            line-height: 1.4;
        }
        
        .story-section,
        .image-section,
        .feature-categories {
            padding: 50px 0;
        }
        
        .story-text h2 {
            font-size: 2rem;
        }
        
        .story-text p {
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .section-title {
            font-size: 1.8rem;
        }
        
        .category-img-container {
            height: 150px;
        }
        
        .category-info {
            padding: 12px;
        }
        
        .category-title {
            font-size: 1rem;
        }
        
        .category-desc {
            font-size: 0.85rem;
            line-height: 1.4;
        }
        
        .eco-btn {
            padding: 8px 12px;
            font-size: 0.8rem;
        }
        
        .cta-button {
            display: block;
            width: 90%;
            margin: 0 auto;
            text-align: center;
        }
    }
    
    @media (max-width: 480px) {
        .hero-container {
            padding: 0 15px;
        }
        
        .hero-title {
            font-size: 1.5rem;
        }
        
        .hero-subtitle {
            font-size: 0.95rem;
            margin-bottom: 25px;
        }
        
        .hero-image-container {
            width: 250px;
            height: 250px;
        }
        
        .quote-text {
            font-size: 1.1rem;
            padding: 0 15px;
        }
        
        .story-text h2 {
            font-size: 1.8rem;
        }
        
        .image-section .container {
            max-width: 95%;
            gap: 15px;
        }
        
        .youtube-wrapper {
            border-radius: 8px;
        }
        
        .section-title {
            font-size: 1.6rem;
        }
        
        .category-img-container {
            height: 140px;
        }
    }
    
    @media (max-width: 400px) {
        .category-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }
        
        .hero-title {
            font-size: 1.4rem;
        }
        
        .hero-image-container {
            width: 220px;
            height: 220px;
        }
        
        .about-hero {
            padding: 30px 0;
        }
        
        .quote-section {
            padding: 40px 0;
        }
        
        .quote-text {
            font-size: 1rem;
        }
    }
    
    @media (max-width: 390px) {
        .about-hero {
            padding: 25px 0;
        }
        
        .hero-container {
            padding: 0 10px;
            gap: 25px;
        }
        
        .hero-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 0.9rem;
            margin-bottom: 20px;
            line-height: 1.5;
        }
        
        .hero-tagline {
            font-size: 0.8rem;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        
        .hero-image-container {
            width: 200px;
            height: 200px;
        }
        
        .cta-button {
            padding: 12px 20px;
            font-size: 0.95rem;
            width: 85%;
            display: block;
            margin: 0 auto;
        }
        
        .quote-section {
            padding: 35px 0;
        }
        
        .quote-text {
            font-size: 0.95rem;
            padding: 0 10px;
            line-height: 1.4;
        }
        
        .section-title {
            font-size: 1.5rem;
            margin-bottom: 25px;
        }
        
        .story-section,
        .image-section,
        .feature-categories {
            padding: 40px 0;
        }
        
        .story-content {
            gap: 30px;
        }
        
        .story-text h2 {
            font-size: 1.6rem;
            margin-bottom: 20px;
        }
        
        .story-text p {
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 15px;
        }
        
        .category-img-container {
            height: 120px;
        }
        
        .category-info {
            padding: 10px;
        }
        
        .category-title {
            font-size: 0.9rem;
            margin-bottom: 6px;
        }
        
        .category-desc {
            font-size: 0.8rem;
            line-height: 1.3;
        }
        
        .eco-btn {
            padding: 6px 10px;
            font-size: 0.75rem;
        }
        
        .image-section .container {
            gap: 15px;
            padding: 0 10px;
        }
        
        .youtube-wrapper {
            border-radius: 8px;
        }
    }
    
    @media (max-width: 280px) {
        .about-hero {
            padding: 20px 0;
        }
        
        .hero-container {
            padding: 0 8px;
            gap: 20px;
        }
        
        .hero-title {
            font-size: 1.1rem;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .hero-subtitle {
            font-size: 0.85rem;
            margin-bottom: 18px;
            line-height: 1.4;
        }
        
        .hero-tagline {
            font-size: 0.7rem;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }
        
        .hero-image-container {
            width: 170px;
            height: 170px;
        }
        
        .cta-button {
            padding: 10px 18px;
            font-size: 0.85rem;
            width: 90%;
            border-radius: 25px;
        }
        
        .quote-section {
            padding: 30px 0;
        }
        
        .quote-text {
            font-size: 0.9rem;
            padding: 0 8px;
            line-height: 1.3;
        }
        
        .section-title {
            font-size: 1.3rem;
            margin-bottom: 20px;
        }
        
        .story-section,
        .image-section,
        .feature-categories {
            padding: 30px 0;
        }
        
        .story-content {
            gap: 25px;
        }
        
        .story-text h2 {
            font-size: 1.4rem;
            margin-bottom: 18px;
        }
        
        .story-text p {
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 12px;
        }
        
        .category-img-container {
            height: 100px;
        }
        
        .category-info {
            padding: 8px;
        }
        
        .category-title {
            font-size: 0.85rem;
            margin-bottom: 4px;
        }
        
        .category-desc {
            font-size: 0.75rem;
            line-height: 1.2;
        }
        
        .eco-btn {
            padding: 5px 8px;
            font-size: 0.7rem;
        }
        
        .image-section .container {
            gap: 12px;
            padding: 0 5px;
            max-width: 98%;
        }
        
        .image-container img {
            border-radius: 10px;
        }
        
        .youtube-wrapper {
            border-radius: 6px;
        }
    }
</style>
