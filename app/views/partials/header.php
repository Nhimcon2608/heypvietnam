<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<header class="site-header">
    <div class="header-container">
        <div class="logo">
            <a href="<?php echo URL_ROOT; ?>">
                <div class="logo-container">
                    <img src="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png" alt="<?php echo SITE_NAME; ?>" class="circular-logo">
                    <span class="logo-text">HEYPVIETNAM</span>
                </div>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="<?php echo URL_ROOT; ?>">Trang Chủ</a></li>
                <li><a href="<?php echo URL_ROOT; ?>/about">Giới Thiệu</a></li>
                <li class="dropdown">
                    <a href="<?php echo URL_ROOT; ?>/products" class="dropdown-toggle">Sản Phẩm</a>
                    <ul class="custom-dropdown-menu">
                        <li><a href="<?php echo URL_ROOT; ?>/products/category/1">Túi, màng bọc thực phẩm</a></li>
                        <li><a href="<?php echo URL_ROOT; ?>/products/category/2">Tắm & chăm sóc cơ thể</a></li>
                        <li><a href="<?php echo URL_ROOT; ?>/products/category/3">Đồ dùng phòng tắm</a></li>
                        <li><a href="<?php echo URL_ROOT; ?>/products/category/4">Giặt giũ & Chăm sóc nhà cửa</a></li>
                        <li><a href="<?php echo URL_ROOT; ?>/products/category/5">Đồ dùng nhà bếp và hộp đựng thực phẩm</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div class="mobile-menu-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    
    <style>
    /* Reset và Base Styles */
    .site-header {
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        top: 0;
        z-index: 1000;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .header-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        min-height: 70px;
    }
    
    /* Logo */
    .logo {
        flex-shrink: 0;
    }
    
    .logo-container {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .logo a {
        text-decoration: none;
    }
    
    .circular-logo {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #5A6B00;
        margin-right: 12px;
    }
    
    .logo-text {
        font-family: 'Arial', sans-serif;
        font-size: 20px;
        font-weight: bold;
        color: #3A1C1A;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    /* Navigation */
    .main-nav {
        flex: 1;
        display: flex;
        justify-content: center;
    }
    
    .main-nav ul {
        display: flex;
        list-style: none;
        margin: 0;
        padding: 0;
        gap: 30px;
        align-items: center;
    }
    
    .main-nav ul li {
        position: relative;
    }
    
    .main-nav ul li a {
        color: #3A1C1A;
        font-weight: 600;
        font-size: 16px;
        padding: 10px 15px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }
    
    .main-nav ul li a:hover {
        color: #5A6B00;
        background: rgba(90, 107, 0, 0.1);
        border-radius: 5px;
    }
    
    /* Dropdown */
    .dropdown {
        position: relative;
    }
    
    .dropdown-toggle {
        cursor: pointer;
    }
    
    .dropdown:hover .custom-dropdown-menu {
        display: block;
    }
    
    .custom-dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0; /* Đảm bảo sát mép trái của .dropdown-toggle */
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        min-width: 250px;
        padding: 10px 0;
        z-index: 1001;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
        transform: translateX(0); /* Loại bỏ dịch chuyển không mong muốn */
    }
    
    .custom-dropdown-menu li {
        display: block;
        margin: 0;
    }
    
    .custom-dropdown-menu li a {
        display: block;
        padding: 10px 20px;
        color: #3A1C1A;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .custom-dropdown-menu li a:hover {
        background: #5A6B00;
        color: white;
    }
    
    /* Mobile Menu Toggle */
    .mobile-menu-toggle {
        display: none;
        flex-direction: column;
        cursor: pointer;
        padding: 5px;
    }
    
    .mobile-menu-toggle span {
        width: 25px;
        height: 3px;
        background: #3A1C1A;
        margin: 3px 0;
        border-radius: 2px;
        transition: all 0.3s ease;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .mobile-menu-toggle {
            display: flex;
        }
        
        .main-nav {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            width: 100%;
            height: calc(100vh - 70px);
            background: #ffffff;
            z-index: 999;
            overflow-y: auto;
            padding: 20px 0;
        }
        
        .main-nav.active {
            display: block;
        }
        
        .main-nav ul {
            flex-direction: column;
            gap: 0;
            align-items: stretch;
        }
        
        .main-nav ul li {
            margin: 0;
            padding: 0 20px;
        }
        
        .main-nav ul li a {
            padding: 15px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .custom-dropdown-menu {
            display: none;
            position: static;
            width: 100%;
            background: #f5f5f5;
            border: none;
            box-shadow: none;
            border-radius: 0;
            max-height: calc(100vh - 150px);
            overflow-y: auto;
            padding: 0;
        }
        
        .dropdown.active .custom-dropdown-menu {
            display: block;
        }
        
        .logo-text {
            font-size: 18px;
        }
        
        .circular-logo {
            width: 45px;
            height: 45px;
        }
    }
    </style>
</header> 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        const mainNav = document.querySelector('.main-nav');
        const dropdowns = document.querySelectorAll('.dropdown');

        // Xử lý menu mobile
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenuToggle.classList.toggle('active');
            mainNav.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });

        // Xử lý dropdown trên mobile
        dropdowns.forEach(dropdown => {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            toggle.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    e.preventDefault();
                    dropdown.classList.toggle('active');
                }
            });
        });

        // Ngăn chặn đóng dropdown khi hover trên desktop
        dropdowns.forEach(dropdown => {
            dropdown.addEventListener('mouseleave', function() {
                if (window.innerWidth > 768) {
                    dropdown.querySelector('.custom-dropdown-menu').style.display = 'none';
                }
            });
            dropdown.addEventListener('mouseenter', function() {
                if (window.innerWidth > 768) {
                    dropdown.querySelector('.custom-dropdown-menu').style.display = 'block';
                }
            });
        });

        // Cập nhật khi resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                mainNav.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
                document.body.classList.remove('menu-open');
                dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
            }
        });

        // Thêm hiệu ứng scroll cho header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.site-header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });

        // CSS động cho body khi menu mobile mở
        const style = document.createElement('style');
        style.textContent = `
            body.menu-open {
                overflow: hidden;
            }
            .site-header.scrolled {
                box-shadow: 0 5px 30px rgba(0,0,0,0.15);
                backdrop-filter: blur(15px);
            }
        `;
        document.head.appendChild(style);
    });
</script>