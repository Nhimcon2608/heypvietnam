<?php

if (!function_exists('heypHomeCanvasTextElement')) {
    function heypHomeCanvasTextElement($id, $content, $x, $y, $width, $height, $zIndex, array $style = [], $href = '', $headingLevel = 0, $navLabel = '') {
        $element = [
            'id' => $id,
            'type' => 'text',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'zIndex' => $zIndex,
            'content' => $content,
            'src' => '',
            'href' => $href,
            'style' => array_merge([
                'fontFamily' => 'Open Sans',
                'fontSize' => 20,
                'fontWeight' => '400',
                'fontStyle' => 'normal',
                'textAlign' => 'left',
                'color' => '#6c757d',
                'backgroundColor' => 'transparent',
                'borderRadius' => 0
            ], $style)
        ];

        if ($headingLevel > 0) {
            $element['headingLevel'] = $headingLevel;
        }

        if ($navLabel !== '') {
            $element['navLabel'] = $navLabel;
        }

        return $element;
    }
}

if (!function_exists('heypHomeCanvasImageElement')) {
    function heypHomeCanvasImageElement($id, $src, $alt, $x, $y, $width, $height, $zIndex, array $style = []) {
        return [
            'id' => $id,
            'type' => 'image',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'zIndex' => $zIndex,
            'content' => $alt,
            'src' => $src,
            'href' => '',
            'style' => array_merge([
                'fontFamily' => 'Open Sans',
                'fontSize' => 18,
                'fontWeight' => '400',
                'fontStyle' => 'normal',
                'textAlign' => 'left',
                'color' => '#1f2937',
                'backgroundColor' => 'transparent',
                'borderRadius' => 8
            ], $style)
        ];
    }
}

if (!function_exists('heypHomeCanvasShapeElement')) {
    function heypHomeCanvasShapeElement($id, $x, $y, $width, $height, $zIndex, $backgroundColor, $borderRadius = 0) {
        return [
            'id' => $id,
            'type' => 'shape',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'zIndex' => $zIndex,
            'content' => '',
            'src' => '',
            'href' => '',
            'style' => [
                'fontFamily' => 'Open Sans',
                'fontSize' => 18,
                'fontWeight' => '400',
                'fontStyle' => 'normal',
                'textAlign' => 'left',
                'color' => '#1f2937',
                'backgroundColor' => $backgroundColor,
                'borderRadius' => $borderRadius
            ]
        ];
    }
}

if (!function_exists('heypHomeCanvasVideoElement')) {
    function heypHomeCanvasVideoElement($id, $src, $title, $x, $y, $width, $height, $zIndex, array $style = []) {
        return [
            'id' => $id,
            'type' => 'video',
            'x' => $x,
            'y' => $y,
            'width' => $width,
            'height' => $height,
            'zIndex' => $zIndex,
            'content' => $title,
            'src' => $src,
            'href' => '',
            'style' => array_merge([
                'fontFamily' => 'Open Sans',
                'fontSize' => 18,
                'fontWeight' => '400',
                'fontStyle' => 'normal',
                'textAlign' => 'left',
                'color' => '#ffffff',
                'backgroundColor' => '#111827',
                'borderRadius' => 15
            ], $style)
        ];
    }
}

if (!function_exists('heypDefaultHomePageContent')) {
    function heypDefaultHomePageContent() {
        $elements = [];
        $z = 1;

        $elements[] = heypHomeCanvasShapeElement('hero-bg', 0, 0, 1200, 640, $z++, '#ffffff', 0);
        $elements[] = heypHomeCanvasShapeElement('quote-bg', 0, 640, 1200, 250, $z++, '#f8f9fa', 0);
        $elements[] = heypHomeCanvasShapeElement('image-intro-bg', 0, 890, 1200, 620, $z++, '#f8f9fa', 0);
        $elements[] = heypHomeCanvasShapeElement('story-bg', 0, 1510, 1200, 760, $z++, '#ffffff', 0);
        $elements[] = heypHomeCanvasShapeElement('gallery-bg', 0, 2270, 1200, 1880, $z++, '#f8f9fa', 0);

        $elements[] = heypHomeCanvasTextElement('ve-heyp-tagline', 'VỀ HEYP - SẢN PHẨM XANH VIỆT NAM', 70, 88, 520, 34, $z++, [
            'fontSize' => 14,
            'fontWeight' => '500',
            'color' => '#6c757d'
        ]);
        $elements[] = heypHomeCanvasTextElement('ve-heyp', "Khởi Nguồn Từ Tình Yêu\nDành Cho Thiên Nhiên\nVà Cuộc Sống Bền Vững", 70, 132, 570, 190, $z++, [
            'fontFamily' => 'Georgia',
            'fontSize' => 43,
            'fontWeight' => '400',
            'color' => '#2c3e50'
        ], '', 1, 'Về HEYP');
        $elements[] = heypHomeCanvasTextElement('ve-heyp-subtitle', 'Khởi Nguồn Từ Tình Yêu Dành Cho Thiên Nhiên Và Cuộc Sống Bền Vững', 70, 328, 570, 70, $z++, [
            'fontSize' => 22,
            'color' => '#6c757d'
        ]);
        $elements[] = heypHomeCanvasTextElement('ve-heyp-intro', 'HEYP ra đời từ niềm tin rằng con người cần sự kết nối với tự nhiên. Các sản phẩm thủ công thân thiện môi trường không những đủ khả năng đáp ứng nhu cầu của con người, mang lại cho chúng ta cuộc sống mạnh khỏe, hạnh phúc mà còn góp phần bảo vệ hành tinh.', 70, 405, 570, 150, $z++, [
            'fontSize' => 20,
            'color' => '#6c757d'
        ]);
        $elements[] = heypHomeCanvasShapeElement('hero-logo-circle-bg', 725, 80, 430, 430, $z++, '#e9ecef', 215);
        $elements[] = heypHomeCanvasImageElement('hero-logo', 'public/img/logoHEYP.png', 'HEYP Logo', 725, 80, 430, 430, $z++, [
            'borderRadius' => 215
        ]);

        $elements[] = heypHomeCanvasTextElement('lua-chon-xanh', '"Mỗi sản phẩm xanh bạn chọn là một bước nhỏ hướng tới tương lai bền vững cho thế hệ mai sau."', 200, 718, 800, 96, $z++, [
            'fontFamily' => 'Georgia',
            'fontSize' => 30,
            'fontStyle' => 'italic',
            'textAlign' => 'center',
            'color' => '#495057'
        ]);

        $elements[] = heypHomeCanvasImageElement('intro-image', 'public/img/about/heyp-green-living.webp', 'Sản phẩm xanh Heyp', 70, 965, 1060, 460, $z++, [
            'borderRadius' => 15
        ]);

        $elements[] = heypHomeCanvasTextElement('cau-chuyen-cua-heyp', 'Câu Chuyện Của Heyp', 70, 1590, 500, 72, $z++, [
            'fontFamily' => 'Georgia',
            'fontSize' => 40,
            'fontWeight' => '400',
            'color' => '#2c3e50'
        ], '', 1, 'Câu Chuyện');
        $elements[] = heypHomeCanvasShapeElement('story-divider', 595, 1638, 1, 430, $z++, '#e9ecef', 0);
        $elements[] = heypHomeCanvasTextElement('story-p1', 'Tất cả bắt đầu từ một câu hỏi đơn giản: "Làm thế nào để chúng ta có thể sống khỏe mạnh hơn mà vẫn bảo vệ được môi trường?"', 70, 1688, 500, 112, $z++, [
            'fontSize' => 19,
            'color' => '#6c757d'
        ]);
        $elements[] = heypHomeCanvasTextElement('story-p2', 'Heyp nhận ra rằng nhiều gia đình Việt Nam đang tìm kiếm những sản phẩm an toàn, thân thiện với môi trường nhưng lại gặp khó khăn trong việc tìm được những sản phẩm chất lượng với giá cả hợp lý.', 70, 1818, 500, 132, $z++, [
            'fontSize' => 19,
            'color' => '#6c757d'
        ]);
        $elements[] = heypHomeCanvasTextElement('story-p3', 'Heyp ra đời với sứ mệnh mang đến những sản phẩm xanh, sạch, an toàn cho sức khỏe và thân thiện với môi trường. Heyp tin rằng mỗi lựa chọn nhỏ của bạn đều góp phần tạo nên một tương lai bền vững cho thế hệ mai sau.', 70, 1970, 500, 154, $z++, [
            'fontSize' => 19,
            'color' => '#6c757d'
        ]);
        $elements[] = heypHomeCanvasTextElement('story-button', 'Tìm Hiểu Thêm', 70, 2145, 220, 54, $z++, [
            'fontSize' => 16,
            'fontWeight' => '600',
            'textAlign' => 'center',
            'color' => '#ffffff',
            'backgroundColor' => '#7d8471',
            'borderRadius' => 27
        ], '#cuoc-song-xanh');
        $elements[] = heypHomeCanvasImageElement('story-image', 'public/img/about/heyp-green-story.webp', 'Câu chuyện của chúng tôi', 665, 1580, 455, 560, $z++, [
            'borderRadius' => 20
        ]);

        $elements[] = heypHomeCanvasTextElement('hinh-anh-heyp', 'Hình Ảnh HEYP', 70, 2350, 520, 70, $z++, [
            'fontFamily' => 'Georgia',
            'fontSize' => 40,
            'fontWeight' => '400',
            'color' => '#2c3e50'
        ], '', 1, 'Hình Ảnh');
        $elements[] = heypHomeCanvasImageElement('gallery-image-one', 'public/img/about/heyp-green-story.webp', 'Sản phẩm xanh Heyp', 70, 2440, 1060, 460, $z++, [
            'borderRadius' => 15
        ]);
        $elements[] = heypHomeCanvasImageElement('gallery-image-two', 'public/img/about/heyp-green-living.webp', 'Cuộc sống xanh', 70, 2940, 1060, 460, $z++, [
            'borderRadius' => 15
        ]);
        $elements[] = heypHomeCanvasTextElement('cuoc-song-xanh', 'Cuộc Sống Xanh', 70, 3485, 520, 70, $z++, [
            'fontFamily' => 'Georgia',
            'fontSize' => 40,
            'fontWeight' => '400',
            'color' => '#2c3e50'
        ], '', 1, 'Cuộc Sống Xanh');
        $elements[] = heypHomeCanvasVideoElement('green-life-video', 'https://www.youtube.com/embed/1lKyby6WH-E?start=55', 'Cuộc sống xanh - Heyp', 70, 3575, 1060, 596, $z++);

        return [
            'header' => [
                'logo' => 'public/img/logoHEYP.png'
            ],
            'layoutType' => 'canvas',
            'canvas' => [
                'width' => 1200,
                'height' => 4250,
                'backgroundColor' => '#ffffff'
            ],
            'elements' => $elements
        ];
    }
}
