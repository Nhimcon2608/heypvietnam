<?php

$root = dirname(__DIR__);

function connectDatabase() {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') ?: '';
    $name = getenv('DB_NAME') ?: 'heypvietnam';
    $ports = array_unique([getenv('DB_PORT') ?: '3307', '3306']);

    foreach ($ports as $port) {
        try {
            return new PDO(
                "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'
                ]
            );
        } catch (PDOException $exception) {
            $lastError = $exception;
        }
    }

    throw $lastError;
}

function existingAsset($path, $fallback) {
    global $root;
    $path = ltrim((string) $path, '/');

    return is_file($root . '/' . $path) ? $path : $fallback;
}

function trimText($text, $limit = 120) {
    $text = trim(preg_replace('/\s+/', ' ', (string) $text));
    if (function_exists('mb_strlen') && mb_strlen($text, 'UTF-8') > $limit) {
        return mb_substr($text, 0, $limit, 'UTF-8') . '...';
    }

    if (!function_exists('mb_strlen') && strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }

    return $text;
}

function baseStyle($overrides = []) {
    return array_merge([
        'fontFamily' => 'Open Sans',
        'fontSize' => 22,
        'fontWeight' => '400',
        'color' => '#1f2937',
        'backgroundColor' => 'transparent',
        'borderRadius' => 0
    ], $overrides);
}

function addShape(&$elements, $id, $x, $y, $width, $height, $backgroundColor, &$zIndex, $radius = 0) {
    $elements[] = [
        'id' => $id,
        'type' => 'shape',
        'x' => $x,
        'y' => $y,
        'width' => $width,
        'height' => $height,
        'zIndex' => $zIndex++,
        'content' => '',
        'src' => '',
        'style' => baseStyle([
            'backgroundColor' => $backgroundColor,
            'borderRadius' => $radius
        ])
    ];
}

function addText(&$elements, $id, $content, $x, $y, $width, $height, &$zIndex, $style = []) {
    $elements[] = [
        'id' => $id,
        'type' => 'text',
        'x' => $x,
        'y' => $y,
        'width' => $width,
        'height' => $height,
        'zIndex' => $zIndex++,
        'content' => $content,
        'src' => '',
        'style' => baseStyle($style)
    ];
}

function addImage(&$elements, $id, $src, $alt, $x, $y, $width, $height, &$zIndex, $radius = 12) {
    $elements[] = [
        'id' => $id,
        'type' => 'image',
        'x' => $x,
        'y' => $y,
        'width' => $width,
        'height' => $height,
        'zIndex' => $zIndex++,
        'content' => $alt,
        'src' => $src,
        'style' => baseStyle([
            'backgroundColor' => 'transparent',
            'borderRadius' => $radius
        ])
    ];
}

function aboutCategories(PDO $pdo) {
    try {
        $rows = $pdo->query('SELECT id, name, description, image FROM categories ORDER BY name')->fetchAll(PDO::FETCH_ASSOC);
        if ($rows) {
            return $rows;
        }
    } catch (Throwable $exception) {
        // Keep the import usable even before categories are seeded.
    }

    return [
        [
            'id' => 4,
            'name' => 'Giặt giũ & Chăm sóc nhà cửa',
            'description' => 'Các sản phẩm giặt giũ và chăm sóc nhà cửa không chứa hóa chất độc hại, an toàn cho gia đình và môi trường.',
            'image' => ''
        ],
        [
            'id' => 2,
            'name' => 'Tắm & chăm sóc cơ thể',
            'description' => 'Sản phẩm chăm sóc cơ thể và tắm rửa từ nguyên liệu tự nhiên, không hóa chất độc hại.',
            'image' => ''
        ],
        [
            'id' => 1,
            'name' => 'Túi, màng bọc thực phẩm',
            'description' => 'Các sản phẩm túi và màng bọc thực phẩm thân thiện với môi trường, có thể tái sử dụng và phân hủy sinh học.',
            'image' => ''
        ],
        [
            'id' => 5,
            'name' => 'Đồ dùng nhà bếp và hộp đựng thực phẩm',
            'description' => 'Dụng cụ nhà bếp và hộp đựng thực phẩm bền đẹp, an toàn, thân thiện với môi trường.',
            'image' => ''
        ],
        [
            'id' => 3,
            'name' => 'Đồ dùng phòng tắm',
            'description' => 'Đồ dùng phòng tắm bền đẹp, được làm từ các vật liệu thân thiện với môi trường.',
            'image' => ''
        ]
    ];
}

function buildAboutCanvasPage(PDO $pdo) {
    $elements = [];
    $zIndex = 1;
    $logo = existingAsset('public/img/logoHEYP.png', 'public/img/logoHEYP.png');
    $productA = existingAsset('public/img/products/ed34bacb22af6b3972de150f2dcc2a85.webp', 'public/img/products/e9fa5809267307de5dfe85a8e56cf6e9.webp');
    $productB = existingAsset('public/img/products/e9fa5809267307de5dfe85a8e56cf6e9.webp', 'public/img/products/e9fa5809267307de5dfe85a8e56cf6e9.webp');
    $productC = existingAsset('public/img/products/75d255d85278de8d6f70f45f772703c7.webp', 'public/img/products/vn-11134210-7r98o-lqarr1ni06vm7c.webp');
    $productD = existingAsset('public/img/products/vn-11134210-7r98o-lqarr1ni06vm7c.webp', 'public/img/products/vn-11134210-7r98o-lqarr1ni06vm7c.webp');
    $productE = existingAsset('public/img/products/vn-11134210-7r98o-lqarr1ni1lg218.webp', 'public/img/products/sizes/1758546165_size_0_image (5).webp');
    $productF = existingAsset('public/img/products/vn-11134210-7r98o-lqarr1ni300i29.webp', 'public/img/products/sizes/1758546165_size_1_image (6).webp');

    addShape($elements, 'about-hero-bg', 0, 0, 1200, 680, '#ffffff', $zIndex);
    addText($elements, 'about-hero-tagline', 'VỀ HEYP - SẢN PHẨM XANH VIỆT NAM', 80, 90, 560, 40, $zIndex, [
        'fontSize' => 15,
        'fontWeight' => '600',
        'color' => '#6c757d',
        'fontFamily' => 'Montserrat'
    ]);
    addText($elements, 'about-hero-title', "Khởi Nguồn Từ Tình Yêu\nDành Cho Thiên Nhiên\nVà Cuộc Sống Bền Vững", 80, 138, 610, 210, $zIndex, [
        'fontFamily' => 'Georgia',
        'fontSize' => 43,
        'fontWeight' => '400',
        'color' => '#2c3e50'
    ]);
    addText($elements, 'about-hero-subtitle', 'Khởi Nguồn Từ Tình Yêu Dành Cho Thiên Nhiên Và Cuộc Sống Bền Vững', 80, 370, 570, 70, $zIndex, [
        'fontSize' => 22,
        'color' => '#6c757d'
    ]);
    addText($elements, 'about-hero-description', 'HEYP ra đời từ niềm tin rằng con người cần sự kết nối với tự nhiên. Các sản phẩm thủ công thân thiện môi trường không những đủ khả năng đáp ứng nhu cầu của con người, mang lại cho chúng ta cuộc sống mạnh khỏe, hạnh phúc mà còn góp phần bảo vệ hành tinh.', 80, 460, 590, 150, $zIndex, [
        'fontSize' => 19,
        'color' => '#6c757d'
    ]);
    addImage($elements, 'about-hero-logo', $logo, 'HEYP Logo', 735, 115, 360, 360, $zIndex, 180);

    addShape($elements, 'about-quote-bg', 0, 680, 1200, 260, '#f8f9fa', $zIndex);
    addText($elements, 'about-quote', '"Mỗi sản phẩm xanh bạn chọn là một bước nhỏ hướng tới tương lai bền vững cho thế hệ mai sau."', 200, 765, 800, 110, $zIndex, [
        'fontFamily' => 'Georgia',
        'fontSize' => 30,
        'fontWeight' => '400',
        'color' => '#495057'
    ]);

    addShape($elements, 'about-image-1-bg', 0, 940, 1200, 580, '#f8f9fa', $zIndex);
    addImage($elements, 'about-image-1', $productA, 'Sản phẩm xanh Heyp', 90, 1030, 1020, 400, $zIndex, 15);

    addShape($elements, 'about-story-bg', 0, 1520, 1200, 760, '#ffffff', $zIndex);
    addText($elements, 'about-story-title', 'Câu Chuyện Của Heyp', 80, 1630, 500, 70, $zIndex, [
        'fontFamily' => 'Georgia',
        'fontSize' => 42,
        'fontWeight' => '400',
        'color' => '#2c3e50'
    ]);
    addText($elements, 'about-story-p1', 'Tất cả bắt đầu từ một câu hỏi đơn giản: "Làm thế nào để chúng ta có thể sống khỏe mạnh hơn mà vẫn bảo vệ được môi trường?"', 80, 1725, 520, 95, $zIndex, [
        'fontSize' => 20,
        'color' => '#6c757d'
    ]);
    addText($elements, 'about-story-p2', 'Heyp nhận ra rằng nhiều gia đình Việt Nam đang tìm kiếm những sản phẩm an toàn, thân thiện với môi trường nhưng lại gặp khó khăn trong việc tìm được những sản phẩm chất lượng với giá cả hợp lý.', 80, 1840, 520, 120, $zIndex, [
        'fontSize' => 20,
        'color' => '#6c757d'
    ]);
    addText($elements, 'about-story-p3', 'Heyp ra đời với sứ mệnh mang đến những sản phẩm xanh, sạch, an toàn cho sức khỏe và thân thiện với môi trường. Heyp tin rằng mỗi lựa chọn nhỏ của bạn đều góp phần tạo nên một tương lai bền vững cho thế hệ mai sau.', 80, 1980, 520, 150, $zIndex, [
        'fontSize' => 20,
        'color' => '#6c757d'
    ]);
    addText($elements, 'about-story-button', 'Khám Phá Sản Phẩm', 80, 2160, 250, 58, $zIndex, [
        'fontFamily' => 'Montserrat',
        'fontSize' => 18,
        'fontWeight' => '600',
        'color' => '#ffffff',
        'backgroundColor' => '#7d8471',
        'borderRadius' => 28
    ]);
    addImage($elements, 'about-story-image', $productB, 'Câu chuyện của chúng tôi', 675, 1630, 440, 440, $zIndex, 20);

    addShape($elements, 'about-image-2-bg', 0, 2280, 1200, 560, '#f8f9fa', $zIndex);
    addImage($elements, 'about-image-2-left', $productC, 'Sản phẩm xanh Heyp', 80, 2370, 500, 330, $zIndex, 15);
    addImage($elements, 'about-image-2-right', $productD, 'Sản phẩm xanh Heyp', 620, 2370, 500, 330, $zIndex, 15);

    addShape($elements, 'about-categories-bg', 0, 2840, 1200, 850, '#fbfcfb', $zIndex);
    addText($elements, 'about-categories-title', 'Danh Mục Sản Phẩm', 360, 2930, 480, 70, $zIndex, [
        'fontFamily' => 'Georgia',
        'fontSize' => 38,
        'fontWeight' => '400',
        'color' => '#2c3e50'
    ]);

    $fallbackImages = [$logo, $productA, $productB, $productC, $productD];
    foreach (aboutCategories($pdo) as $index => $category) {
        $x = 60 + ($index * 220);
        $y = 3040;
        $categorySrc = existingAsset(
            'public/img/categories/' . ($category['image'] ?? ''),
            $fallbackImages[$index % count($fallbackImages)]
        );

        addShape($elements, 'about-category-card-' . ($index + 1), $x, $y, 205, 430, '#ffffff', $zIndex, 8);
        addImage($elements, 'about-category-image-' . ($index + 1), $categorySrc, $category['name'], $x + 12, $y + 12, 181, 135, $zIndex, 8);
        addText($elements, 'about-category-title-' . ($index + 1), $category['name'], $x + 14, $y + 165, 177, 70, $zIndex, [
            'fontFamily' => 'Montserrat',
            'fontSize' => 18,
            'fontWeight' => '700',
            'color' => '#2c3e50'
        ]);
        addText($elements, 'about-category-desc-' . ($index + 1), trimText($category['description'], 130), $x + 14, $y + 245, 177, 100, $zIndex, [
            'fontSize' => 15,
            'color' => '#6c757d'
        ]);
        addText($elements, 'about-category-button-' . ($index + 1), 'Xem Sản Phẩm', $x + 14, $y + 365, 177, 44, $zIndex, [
            'fontFamily' => 'Montserrat',
            'fontSize' => 14,
            'fontWeight' => '600',
            'color' => '#5A6B00',
            'backgroundColor' => '#ffffff',
            'borderRadius' => 4
        ]);
    }

    addShape($elements, 'about-media-bg', 0, 3690, 1200, 700, '#f8f9fa', $zIndex);
    addImage($elements, 'about-media-left', $productE, 'Cuộc sống xanh', 60, 3790, 340, 250, $zIndex, 15);
    addImage($elements, 'about-media-center', $productF, 'Cuộc sống xanh', 430, 3790, 340, 250, $zIndex, 15);
    addShape($elements, 'about-video-card', 800, 3790, 340, 250, '#111827', $zIndex, 15);
    addText($elements, 'about-video-title', "Video YouTube\nCuộc sống xanh - Heyp", 830, 3865, 280, 100, $zIndex, [
        'fontFamily' => 'Montserrat',
        'fontSize' => 24,
        'fontWeight' => '700',
        'color' => '#ffffff'
    ]);
    addText($elements, 'about-video-url', 'https://www.youtube.com/embed/1lKyby6WH-E?start=55', 830, 3970, 280, 40, $zIndex, [
        'fontSize' => 13,
        'color' => '#d1d5db'
    ]);

    return [
        'header' => [
            'logo' => $logo
        ],
        'layoutType' => 'canvas',
        'canvas' => [
            'width' => 1200,
            'height' => 4390,
            'backgroundColor' => '#ffffff'
        ],
        'elements' => $elements
    ];
}

$pdo = connectDatabase();
$page = buildAboutCanvasPage($pdo);
$content = json_encode($page, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

if ($content === false) {
    throw new RuntimeException('JSON encode failed: ' . json_last_error_msg());
}

$pdo->exec("CREATE TABLE IF NOT EXISTS pages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    content JSON NOT NULL,
    UNIQUE KEY pages_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

$statement = $pdo->prepare("INSERT INTO pages (title, slug, content)
    VALUES (:title, :slug, :content)
    ON DUPLICATE KEY UPDATE title = VALUES(title), content = VALUES(content)");
$statement->execute([
    ':title' => 'Trang Chủ',
    ':slug' => 'home',
    ':content' => $content
]);

echo 'Imported about page canvas into pages.slug=home with ' . count($page['elements']) . " elements.\n";
