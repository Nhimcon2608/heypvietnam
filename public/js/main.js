document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            mainNav.style.display = mainNav.style.display === 'block' ? 'none' : 'block';
        });
    }
    
    // Xử lý dropdown menu
    const dropdowns = document.querySelectorAll('.dropdown');
    
    if (window.innerWidth < 768) {
        // Mobile: Khi click vào menu hiển thị dropdown
        dropdowns.forEach(dropdown => {
            const link = dropdown.querySelector('a');
            const menu = dropdown.querySelector('.custom-dropdown-menu');
            
            if (link && menu) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdown.classList.toggle('active');
                });
            }
        });
    }
    
    // Thêm sự kiện resize để xử lý khi chuyển đổi kích thước màn hình
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            // Trên desktop, reset tất cả style inline để CSS xử lý hover
            dropdowns.forEach(dropdown => {
                dropdown.classList.remove('active');
            });
        }
    });
    
    // Back to top button
    const backToTopBtn = document.createElement('button');
    backToTopBtn.classList.add('back-to-top');
    backToTopBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    document.body.appendChild(backToTopBtn);
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
    
    // Add styles for back to top button
    const style = document.createElement('style');
    style.textContent = `
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            background-color: var(--secondary-bg-1);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: none;
            z-index: 99;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        .back-to-top:hover {
            background-color: var(--secondary-bg-2);
        }
    `;
    document.head.appendChild(style);

    // Product Quantity Selector
    const productQuantityInput = document.getElementById('productQuantity');
    const increaseBtn = document.getElementById('increaseQuantity');
    const decreaseBtn = document.getElementById('decreaseQuantity');
    
    if (productQuantityInput && increaseBtn && decreaseBtn) {
        increaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(productQuantityInput.value);
            productQuantityInput.value = currentValue + 1;
        });
        
        decreaseBtn.addEventListener('click', function() {
            const currentValue = parseInt(productQuantityInput.value);
            if (currentValue > 1) {
                productQuantityInput.value = currentValue - 1;
            }
        });
        
        // Prevent manual entry of non-numeric values
        productQuantityInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value === '' || parseInt(this.value) < 1) {
                this.value = 1;
            }
        });
    }

    // Xử lý tất cả các liên kết trong sidebar
    const sidebarLinks = document.querySelectorAll('.sidebar a');
    if (sidebarLinks) {
        sidebarLinks.forEach(link => {
            // Đảm bảo mỗi liên kết có thể nhấn vào
            link.style.pointerEvents = 'auto';
            
            // Thêm sự kiện click rõ ràng
            link.addEventListener('click', function(e) {
                // Đảm bảo sự kiện click hoạt động
                const href = this.getAttribute('href');
                if (href) {
                    window.location.href = href;
                }
            });
        });
    }
    
    // Xử lý nút tìm kiếm
    const searchButtons = document.querySelectorAll('.sidebar form button');
    if (searchButtons) {
        searchButtons.forEach(button => {
            button.style.pointerEvents = 'auto';
            button.style.cursor = 'pointer';
        });
    }
    
    // Xử lý input tìm kiếm
    const searchInputs = document.querySelectorAll('.sidebar form input');
    if (searchInputs) {
        searchInputs.forEach(input => {
            input.style.pointerEvents = 'auto';
        });
    }
}); 