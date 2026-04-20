<!-- Font Awesome CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<?php
$headerEscape = function($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$headerAnchorId = function($value, $fallback = 'section') {
    if (function_exists('landingPageAnchorId')) {
        return landingPageAnchorId($value, $fallback);
    }

    $id = strtolower(trim((string) $value));
    $id = preg_replace('/[^a-z0-9_-]+/', '-', $id);
    $id = trim($id, '-');

    return $id !== '' ? $id : $fallback;
};

$headerPage = isset($data['page']) && is_array($data['page']) ? $data['page'] : [];
$landingNavItems = isset($data['landingNavItems']) && is_array($data['landingNavItems']) ? $data['landingNavItems'] : [];

if (empty($landingNavItems) && !empty($headerPage['sections']) && is_array($headerPage['sections'])) {
    foreach ($headerPage['sections'] as $index => $section) {
        if (!is_array($section)) {
            continue;
        }

        $heading = isset($section['heading']) && is_array($section['heading']) ? $section['heading'] : [];
        $anchorId = $headerAnchorId(
            $section['id'] ?? $heading['anchorId'] ?? $heading['text'] ?? '',
            'section-' . ($index + 1)
        );

        if ((int) ($heading['level'] ?? 0) === 1 && trim((string) ($heading['text'] ?? '')) !== '') {
            $landingNavItems[] = [
                'label' => $heading['text'],
                'anchorId' => $anchorId
            ];
            continue;
        }

        foreach (($section['blocks'] ?? []) as $block) {
            if (
                is_array($block) &&
                ($block['type'] ?? '') === 'heading' &&
                (int) ($block['level'] ?? 0) === 1 &&
                trim((string) ($block['content'] ?? $block['text'] ?? '')) !== ''
            ) {
                $landingNavItems[] = [
                    'label' => $block['content'] ?? $block['text'],
                    'anchorId' => $anchorId
                ];
                break;
            }
        }
    }
}

$isLandingPageHeader = isset($data['landingNavItems']) || !empty($headerPage);
$headerLogo = isset($headerPage['header']['logo']) ? $headerPage['header']['logo'] : 'public/img/logoHEYP.png';
$headerLogoSrc = function_exists('landingPageAssetUrl') ? landingPageAssetUrl($headerLogo) : URL_ROOT . '/public/img/logoHEYP.png';
?>

<header class="site-header">
    <div class="header-container">
        <div class="logo">
            <a href="<?php echo URL_ROOT; ?>" data-logo-reload>
                <div class="logo-container">
                    <img src="<?php echo $headerEscape($headerLogoSrc); ?>" alt="<?php echo SITE_NAME; ?>" class="circular-logo">
                    <span class="logo-text">HEYPVIETNAM</span>
                </div>
            </a>
        </div>
        <nav class="main-nav">
            <ul>
                <?php if ($isLandingPageHeader): ?>
                    <li><a href="<?php echo URL_ROOT; ?>/#top" data-scroll-top class="active">Trang Chủ</a></li>
                    <?php foreach ($landingNavItems as $item): ?>
                        <?php
                            $anchorId = $item['anchorId'] ?? '';
                            $label = $item['label'] ?? '';
                            if ($anchorId === '' || $label === '') {
                                continue;
                            }
                        ?>
                        <li>
                            <a href="<?php echo URL_ROOT; ?>/#<?php echo $headerEscape($anchorId); ?>" data-scroll-target="<?php echo $headerEscape($anchorId); ?>">
                                <?php echo $headerEscape($label); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li><a href="<?php echo URL_ROOT; ?>">Trang Chủ</a></li>
                    <li><a href="<?php echo URL_ROOT; ?>/about">Giới Thiệu</a></li>
                    <li><a href="<?php echo URL_ROOT; ?>/contact">Liên Hệ</a></li>
                <?php endif; ?>
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

    .main-nav ul li a.active {
        color: #5A6B00;
        background: rgba(90, 107, 0, 0.14);
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
    const header = document.querySelector('.site-header');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    const dropdowns = Array.from(document.querySelectorAll('.dropdown'));
    const logoReloadLink = document.querySelector('[data-logo-reload]');
    const topLinks = Array.from(document.querySelectorAll('[data-scroll-top]'));
    const sectionLinks = Array.from(document.querySelectorAll('[data-scroll-target]'));
    const navLinks = topLinks.concat(sectionLinks);
    const sections = sectionLinks
        .map(link => document.getElementById(link.dataset.scrollTarget))
        .filter(Boolean);

    function closeMobileMenu() {
        if (!mobileMenuToggle || !mainNav) {
            return;
        }

        mainNav.classList.remove('active');
        mobileMenuToggle.classList.remove('active');
        document.body.classList.remove('menu-open');
    }

    function setActiveNav(anchorId) {
        navLinks.forEach(link => link.classList.remove('active'));

        if (!anchorId) {
            topLinks.forEach(link => link.classList.add('active'));
            return;
        }

        sectionLinks.forEach(link => {
            if (link.dataset.scrollTarget === anchorId) {
                link.classList.add('active');
            }
        });
    }

    function updateActiveSection() {
        if (header) {
            header.classList.toggle('scrolled', window.scrollY > 50);
        }

        if (!sections.length || window.scrollY <= 8) {
            setActiveNav('');
            return;
        }

        const marker = (header ? header.offsetHeight : 0) + 48;
        let activeId = '';

        sections.forEach(section => {
            if (section.getBoundingClientRect().top <= marker) {
                activeId = section.id;
            }
        });

        setActiveNav(activeId);
    }

    if (mobileMenuToggle && mainNav) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenuToggle.classList.toggle('active');
            mainNav.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
    }

    if (logoReloadLink) {
        logoReloadLink.addEventListener('click', function(e) {
            const targetUrl = new URL(this.href, window.location.href);
            const currentPath = window.location.pathname.replace(/\/$/, '');
            const targetPath = targetUrl.pathname.replace(/\/$/, '');

            if (window.location.origin === targetUrl.origin && currentPath === targetPath) {
                e.preventDefault();
                window.location.reload();
            }
        });
    }

    dropdowns.forEach(dropdown => {
        const toggle = dropdown.querySelector('.dropdown-toggle');
        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                dropdown.classList.toggle('active');
            }
        });
    });

    sectionLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const target = document.getElementById(this.dataset.scrollTarget);
            if (!target) {
                return;
            }

            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            history.replaceState(null, '', '#' + this.dataset.scrollTarget);
            setActiveNav(this.dataset.scrollTarget);
            closeMobileMenu();
        });
    });

    topLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
            history.replaceState(null, '', window.location.pathname + window.location.search);
            setActiveNav('');
            closeMobileMenu();
        });
    });

    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            closeMobileMenu();
            dropdowns.forEach(dropdown => dropdown.classList.remove('active'));
        }
    });

    let scrollFrame = null;
    window.addEventListener('scroll', function() {
        if (scrollFrame) {
            return;
        }

        scrollFrame = window.requestAnimationFrame(function() {
            updateActiveSection();
            scrollFrame = null;
        });
    });

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

    updateActiveSection();
});
</script>
