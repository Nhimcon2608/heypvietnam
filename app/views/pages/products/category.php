<!-- Products Content -->
<section class="products-section">
    <div class="container">
        <div class="products-layout">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="search-box">
                    <h3>Tìm Kiếm</h3>
                    <form action="<?php echo URL_ROOT; ?>/products/search" method="get">
                        <div class="search-input-group">
                            <input type="text" name="term" placeholder="Tìm kiếm sản phẩm..." class="search-input">
                            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                        </div>
                    </form>
                </div>

                <div class="categories-box">
                    <h3>Danh Mục</h3>
                    <ul class="categories-list">
                        <li><a href="<?php echo URL_ROOT; ?>/products" class="category-link">Tất Cả Sản Phẩm</a></li>
                        <?php foreach($data['categories'] as $category) : ?>
                            <li><a href="<?php echo URL_ROOT; ?>/products/category/<?php echo $category['id']; ?>"
                                   class="category-link <?php echo ($category['id'] == $data['category']['id']) ? 'active' : ''; ?>">
                                    <?php echo $category['name']; ?>
                                </a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="products-content">
                <div class="products-header">
                    <h2 class="products-title"><?php echo $data['category']['name']; ?></h2>
                    <p class="products-subtitle">Tìm thấy <?php echo count($data['products']); ?> sản phẩm</p>
                </div>

                <?php if(empty($data['products'])) : ?>
                    <div class="no-products">
                        <i class="fas fa-leaf"></i>
                        <h3>Không có sản phẩm nào</h3>
                        <p>Danh mục này hiện chưa có sản phẩm nào. Hãy thử chọn danh mục khác.</p>
                    </div>
                <?php else : ?>
                    <div class="products-grid">
                        <?php foreach($data['products'] as $product) : ?>
                            <a href="<?php echo URL_ROOT; ?>/products/show/<?php echo $product['id']; ?>" class="product-card related-product-card">
                                <div class="product-img-container">
                                    <?php
                                    // Get main image from database for product
                                    $product_image_url = URL_ROOT . "/image.php?type=main&id=" . $product['id'];
                                    ?>
                                    <img src="<?php echo $product_image_url; ?>" alt="<?php echo $product['name']; ?>" class="product-img">
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
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.hero-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    text-align: center;
}

.hero-content {
    max-width: 800px;
    margin: 0 auto;
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
    font-size: 3.2rem;
    font-weight: 300;
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

/* Products Section */
.products-section {
    background: white;
    padding: 80px 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.products-layout {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 40px;
    align-items: start;
}

/* Sidebar Styles */
.sidebar {
    position: sticky;
    top: 20px;
}

.search-box, .categories-box {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.search-box h3, .categories-box h3 {
    color: #2c3e50;
    font-size: 1.2rem;
    margin-bottom: 20px;
    font-weight: 600;
}

.search-input-group {
    display: flex;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: border-color 0.3s ease;
}

.search-input-group:focus-within {
    border-color: #7d8471;
}

.search-input {
    flex: 1;
    padding: 12px 15px;
    border: none;
    outline: none;
    font-size: 1rem;
}

.search-btn {
    background: #7d8471;
    color: white;
    border: none;
    padding: 12px 18px;
    cursor: pointer;
    transition: background 0.3s ease;
}

.search-btn:hover {
    background: #6a7063;
}

.categories-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.categories-list li {
    margin-bottom: 8px;
}

.category-link {
    display: block;
    padding: 12px 15px;
    color: #495057;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
    font-weight: 500;
}

.category-link:hover, .category-link.active {
    background: #7d8471;
    color: white;
    transform: translateX(5px);
}

/* Products Content */
.products-content {
    min-height: 500px;
    width: 100%;
}

.products-header {
    margin-bottom: 40px;
    text-align: center !important;
    width: 100%;
    display: block;
}

.products-title {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 10px;
    font-weight: 300;
    text-align: center !important;
    width: 100%;
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.products-subtitle {
    font-size: 1.1rem;
    margin-bottom: 10px;
    color: #495057;
    text-align: center !important;
    width: 100%;
    display: block;
}

/* Products Grid */
.products-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin: 40px auto;
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
    background: #fff;
    max-width: 280px;
    margin: 0 auto;
    text-decoration: none;
    color: inherit;
}

.product-details-section .product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
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

.product-img-container {
    height: 220px;
    overflow: hidden;
    position: relative;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.product-img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.3s ease;
}

.product-card:hover .product-img {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: #e74c3c;
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Out of Stock Overlay */
.out-of-stock-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 3;
    border-radius: 15px;
}

.out-of-stock-badge {
    width: 100px;
    height: 100px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    border-radius: 50%;
    font-size: 0.75rem;
    font-weight: bold;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 4px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.out-of-stock-badge i {
    font-size: 1.2rem;
    opacity: 0.9;
}

.out-of-stock-badge span {
    letter-spacing: 0.5px;
    line-height: 1;
}

.product-info {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 140px;
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

/* No Products */
.no-products {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-color);
}

.no-products i {
    font-size: 4rem;
    color: var(--secondary-bg-1);
    margin-bottom: 20px;
    opacity: 0.6;
}

.no-products h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: var(--heading-color);
}

.no-products p {
    font-size: 1rem;
    opacity: 0.8;
    max-width: 400px;
    margin: 0 auto;
    line-height: 1.6;
}

/* No Products */
.no-products {
    text-align: center;
    padding: 60px 20px;
    color: #495057;
}

.no-products i {
    font-size: 4rem;
    color: #7d8471;
    margin-bottom: 20px;
    opacity: 0.6;
}

.no-products h3 {
    font-size: 1.5rem;
    margin-bottom: 15px;
    color: #2c3e50;
}

.no-products p {
    font-size: 1rem;
    opacity: 0.8;
    max-width: 400px;
    margin: 0 auto;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .products-layout {
        grid-template-columns: 250px 1fr;
        gap: 25px;
    }
    
    .search-box, .categories-box {
        padding: 18px;
    }
}

@media (max-width: 992px) {
    .products-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .sidebar {
        order: 2;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .products-content {
        order: 1;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
        gap: 18px;
    }
}

@media (max-width: 768px) {
    .hero-title {
        font-size: 2.2rem;
    }

    .hero-subtitle {
        font-size: 1rem;
    }

    .products-title {
        font-size: 2rem;
    }
    
    .products-subtitle {
        font-size: 0.95rem;
    }

    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .product-title {
        font-size: 0.9rem;
        line-height: 1.3;
        margin-bottom: 10px;
        min-height: 2.4em;
    }

    .product-price {
        font-size: 1rem;
        text-align: center;
    }

    .product-info {
        padding: 12px;
        min-height: 90px;
    }
    
    .product-img-container {
        height: 180px;
    }

    .product-card:hover {
        transform: translateY(-2px);
    }

    .search-box, .categories-box {
        padding: 16px;
        margin-bottom: 15px;
    }
    
    .search-box h3, .categories-box h3 {
        font-size: 1.1rem;
        margin-bottom: 15px;
    }
    
    .category-link {
        padding: 10px 12px;
        font-size: 0.9rem;
    }
    
    .search-input {
        padding: 10px 12px;
        font-size: 0.9rem;
    }
    
    .search-btn {
        padding: 10px 15px;
    }
}

@media (max-width: 576px) {
    .sidebar {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .products-title {
        font-size: 1.8rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        gap: 12px;
    }
    
    .product-img-container {
        height: 160px;
    }
    
    .no-products {
        padding: 40px 15px;
    }
    
    .no-products i {
        font-size: 3rem;
    }
    
    .no-products h3 {
        font-size: 1.3rem;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 1.8rem;
    }

    .products-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .products-section {
        padding: 40px 0;
    }
    
    .products-title {
        font-size: 1.6rem;
    }
    
    .products-subtitle {
        font-size: 0.9rem;
    }

    .sidebar {
        grid-template-columns: 1fr;
    }

    .search-box, .categories-box {
        padding: 15px;
    }
    
    .search-box h3, .categories-box h3 {
        font-size: 1rem;
        margin-bottom: 12px;
    }

    .product-img-container {
        height: 140px;
    }

    .product-title {
        font-size: 0.8rem;
        margin-bottom: 8px;
        min-height: 2.4em;
        line-height: 1.2;
    }

    .product-price {
        font-size: 0.9rem;
        text-align: center;
    }

    .product-info {
        padding: 10px 8px;
        min-height: auto;
    }
    
    .container {
        padding: 0 15px;
    }
    
    .category-link {
        padding: 8px 10px;
        font-size: 0.85rem;
    }
    
    .search-input {
        padding: 8px 10px;
        font-size: 0.85rem;
    }
    
    .search-btn {
        padding: 8px 12px;
    }
}

@media (max-width: 400px) {
    .products-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .hero-title {
        font-size: 1.6rem;
    }
    
    .products-title {
        font-size: 1.4rem;
    }
    
    .product-img-container {
        height: 200px;
    }
    
    .product-title {
        font-size: 0.9rem;
        height: auto;
        line-height: 1.3;
    }
    
    .product-price {
        font-size: 1rem;
    }
    
    .product-info {
        padding: 12px;
    }
    
    .search-box, .categories-box {
        padding: 12px;
    }
}
</style>