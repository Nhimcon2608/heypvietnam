<?php

if (!function_exists('heypFooterDefaultContent')) {
    function heypFooterDefaultContent() {
        $siteName = defined('SITE_NAME') ? SITE_NAME : 'HeypVietNam';

        return [
            'brand_title' => $siteName,
            'description' => 'Chúng tôi cung cấp các sản phẩm thân thiện với môi trường, giúp bạn xây dựng một lối sống bền vững và an lành.',
            'phone' => '098 504 0609',
            'email' => 'Hienphuongcna@gmail.com',
            'socials' => [
                [
                    'key' => 'shopee',
                    'label' => 'Shopee HeypVietNam',
                    'url' => 'https://shopee.vn/heypvietnam',
                    'icon' => 'fas fa-shopping-bag',
                    'class' => 'shopee-link'
                ],
                [
                    'key' => 'facebook',
                    'label' => 'Facebook HeypVietNam',
                    'url' => 'https://www.facebook.com/share/19kF6DwjHV/?mibextid=wwXIfr',
                    'icon' => 'fab fa-facebook',
                    'class' => 'facebook-link'
                ],
                [
                    'key' => 'youtube',
                    'label' => 'YouTube HeypVietNam',
                    'url' => 'https://www.youtube.com/@hienphuongcna',
                    'icon' => 'fab fa-youtube',
                    'class' => 'youtube-link'
                ]
            ],
            'quick_title' => 'Liên Kết Nhanh',
            'quick_links' => [
                ['label' => 'Trang Chủ', 'url' => '/'],
                ['label' => 'Giới Thiệu', 'url' => '/about'],
                ['label' => 'Sản Phẩm', 'url' => '/products'],
                ['label' => 'Liên Hệ', 'url' => '/contact']
            ],
            'category_title' => 'Danh Mục Sản Phẩm',
            'category_links' => [
                ['label' => 'Túi, màng bọc thực phẩm', 'url' => '/products/category/1'],
                ['label' => 'Tắm & chăm sóc cơ thể', 'url' => '/products/category/2'],
                ['label' => 'Đồ dùng phòng tắm', 'url' => '/products/category/3'],
                ['label' => 'Giặt giũ & Chăm sóc nhà cửa', 'url' => '/products/category/4'],
                ['label' => 'Đồ dùng nhà bếp', 'url' => '/products/category/5']
            ],
            'copyright' => '© {year} {site_name}. Tất cả quyền được bảo lưu.'
        ];
    }
}

if (!function_exists('heypFooterNormalizeText')) {
    function heypFooterNormalizeText($value, $fallback = '') {
        $value = trim((string) $value);

        return $value !== '' ? $value : $fallback;
    }
}

if (!function_exists('heypFooterContentValue')) {
    function heypFooterContentValue(array $content, $key, $fallback = '') {
        return array_key_exists($key, $content) ? trim((string) $content[$key]) : $fallback;
    }
}

if (!function_exists('heypFooterNormalizeLinks')) {
    function heypFooterNormalizeLinks($links, array $fallbackLinks = []) {
        if (!is_array($links)) {
            return $fallbackLinks;
        }

        $normalized = [];
        foreach ($links as $link) {
            if (!is_array($link)) {
                continue;
            }

            $label = trim((string) ($link['label'] ?? ''));
            $url = trim((string) ($link['url'] ?? ''));
            if ($label === '' || $url === '') {
                continue;
            }

            $normalized[] = [
                'label' => $label,
                'url' => $url
            ];
        }

        return $normalized ?: $fallbackLinks;
    }
}

if (!function_exists('heypFooterNormalizeSocials')) {
    function heypFooterNormalizeSocials($socials, array $fallbackSocials = []) {
        $byKey = [];
        foreach ($fallbackSocials as $fallback) {
            if (is_array($fallback) && isset($fallback['key'])) {
                $byKey[$fallback['key']] = $fallback;
            }
        }

        if (is_array($socials)) {
            foreach ($socials as $social) {
                if (!is_array($social)) {
                    continue;
                }

                $key = trim((string) ($social['key'] ?? ''));
                if ($key === '') {
                    continue;
                }

                $base = $byKey[$key] ?? [
                    'key' => $key,
                    'label' => ucfirst($key),
                    'url' => '',
                    'icon' => 'fas fa-link',
                    'class' => ''
                ];

                $base['label'] = heypFooterNormalizeText($social['label'] ?? '', $base['label']);
                $base['url'] = trim((string) ($social['url'] ?? $base['url']));
                $base['icon'] = heypFooterNormalizeText($social['icon'] ?? '', $base['icon']);
                $base['class'] = heypFooterNormalizeText($social['class'] ?? '', $base['class']);
                $byKey[$key] = $base;
            }
        }

        return array_values($byKey);
    }
}

if (!function_exists('heypFooterNormalizeContent')) {
    function heypFooterNormalizeContent($content) {
        $defaults = heypFooterDefaultContent();
        $content = is_array($content) ? $content : [];

        return [
            'brand_title' => heypFooterContentValue($content, 'brand_title', $defaults['brand_title']),
            'description' => heypFooterContentValue($content, 'description', $defaults['description']),
            'phone' => heypFooterContentValue($content, 'phone', $defaults['phone']),
            'email' => heypFooterContentValue($content, 'email', $defaults['email']),
            'socials' => heypFooterNormalizeSocials($content['socials'] ?? [], $defaults['socials']),
            'quick_title' => heypFooterContentValue($content, 'quick_title', $defaults['quick_title']),
            'quick_links' => heypFooterNormalizeLinks($content['quick_links'] ?? [], $defaults['quick_links']),
            'category_title' => heypFooterContentValue($content, 'category_title', $defaults['category_title']),
            'category_links' => heypFooterNormalizeLinks($content['category_links'] ?? [], $defaults['category_links']),
            'copyright' => heypFooterContentValue($content, 'copyright', $defaults['copyright'])
        ];
    }
}

if (!function_exists('heypFooterLinksToTextarea')) {
    function heypFooterLinksToTextarea(array $links) {
        $lines = [];
        foreach ($links as $link) {
            if (!is_array($link)) {
                continue;
            }

            $lines[] = trim((string) ($link['label'] ?? '')) . '|' . trim((string) ($link['url'] ?? ''));
        }

        return implode("\n", $lines);
    }
}

if (!function_exists('heypFooterTextareaToLinks')) {
    function heypFooterTextareaToLinks($value) {
        $links = [];
        $lines = preg_split('/\R/', (string) $value);

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = array_map('trim', explode('|', $line, 2));
            $label = $parts[0] ?? '';
            $url = $parts[1] ?? '';
            if ($label === '' || $url === '') {
                continue;
            }

            $links[] = [
                'label' => $label,
                'url' => $url
            ];
        }

        return $links;
    }
}

if (!function_exists('heypFooterCanvasTextElement')) {
    function heypFooterCanvasTextElement($id, $content, $x, $y, $width, $height, $zIndex, array $style = [], $href = '') {
        return [
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
                'fontSize' => 16,
                'fontWeight' => '400',
                'color' => '#ffffff',
                'backgroundColor' => 'transparent',
                'borderRadius' => 0
            ], $style)
        ];
    }
}

if (!function_exists('heypFooterCanvasShapeElement')) {
    function heypFooterCanvasShapeElement($id, $x, $y, $width, $height, $zIndex, $backgroundColor, $borderRadius = 0, $href = '') {
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
            'href' => $href,
            'style' => [
                'fontFamily' => 'Open Sans',
                'fontSize' => 16,
                'fontWeight' => '400',
                'color' => '#ffffff',
                'backgroundColor' => $backgroundColor,
                'borderRadius' => $borderRadius
            ]
        ];
    }
}

if (!function_exists('heypFooterDefaultCanvasContent')) {
    function heypFooterDefaultCanvasContent($content = null) {
        $footer = heypFooterNormalizeContent(is_array($content) ? $content : heypFooterDefaultContent());
        $elements = [];
        $zIndex = 1;

        $elements[] = heypFooterCanvasTextElement('footer-brand-title', $footer['brand_title'], 70, 44, 370, 42, $zIndex++, [
            'fontFamily' => 'Montserrat',
            'fontSize' => 26,
            'fontWeight' => '700'
        ]);
        $elements[] = heypFooterCanvasTextElement('footer-description', $footer['description'], 70, 96, 390, 96, $zIndex++, [
            'fontSize' => 16,
            'color' => '#f8fafc'
        ]);
        $elements[] = heypFooterCanvasTextElement('footer-phone', '☎ ' . $footer['phone'], 70, 214, 330, 30, $zIndex++, [
            'fontSize' => 15,
            'color' => '#eef2e8'
        ]);
        $elements[] = heypFooterCanvasTextElement('footer-email', '✉ ' . $footer['email'], 70, 250, 360, 30, $zIndex++, [
            'fontSize' => 15,
            'color' => '#eef2e8'
        ]);

        $socialX = 70;
        $socialColors = [
            'shopee' => '#ee4d2d',
            'facebook' => '#3b5998',
            'youtube' => '#ff0000'
        ];
        foreach ($footer['socials'] as $social) {
            $key = $social['key'] ?? '';
            $label = $social['label'] ?? ucfirst($key);
            $url = $social['url'] ?? '';
            if ($url === '') {
                continue;
            }

            $elements[] = heypFooterCanvasTextElement('footer-social-' . $key, $label, $socialX, 300, 44, 44, $zIndex++, [
                'fontFamily' => 'Montserrat',
                'fontSize' => 22,
                'fontWeight' => '700',
                'color' => '#ffffff',
                'backgroundColor' => $socialColors[$key] ?? '#5a6b00',
                'borderRadius' => 8
            ], $url);
            $socialX += 56;
        }

        $elements[] = heypFooterCanvasTextElement('footer-quick-title', $footer['quick_title'], 520, 48, 230, 36, $zIndex++, [
            'fontFamily' => 'Montserrat',
            'fontSize' => 21,
            'fontWeight' => '700'
        ]);
        foreach ($footer['quick_links'] as $index => $link) {
            $elements[] = heypFooterCanvasTextElement('footer-quick-link-' . ($index + 1), $link['label'], 520, 98 + ($index * 36), 230, 30, $zIndex++, [
                'fontSize' => 16,
                'color' => '#f8fafc'
            ], $link['url']);
        }

        $elements[] = heypFooterCanvasTextElement('footer-category-title', $footer['category_title'], 790, 48, 330, 36, $zIndex++, [
            'fontFamily' => 'Montserrat',
            'fontSize' => 21,
            'fontWeight' => '700'
        ]);
        foreach ($footer['category_links'] as $index => $link) {
            $elements[] = heypFooterCanvasTextElement('footer-category-link-' . ($index + 1), $link['label'], 790, 98 + ($index * 36), 340, 30, $zIndex++, [
                'fontSize' => 15,
                'color' => '#f8fafc'
            ], $link['url']);
        }

        $elements[] = heypFooterCanvasShapeElement('footer-bottom-bg', 0, 360, 1200, 64, $zIndex++, '#5f6759', 0);
        $elements[] = heypFooterCanvasTextElement('footer-copyright', $footer['copyright'], 0, 381, 1200, 26, $zIndex++, [
            'fontSize' => 15,
            'color' => '#ffffff',
            'backgroundColor' => 'transparent'
        ]);

        return [
            'layoutType' => 'canvas',
            'canvas' => [
                'width' => 1200,
                'height' => heypFooterCanvasContentHeight($elements),
                'backgroundColor' => '#6a7260'
            ],
            'elements' => $elements
        ];
    }
}

if (!function_exists('heypFooterCanvasChoice')) {
    function heypFooterCanvasChoice($value, array $allowed, $fallback) {
        $value = (string) $value;
        return in_array($value, $allowed, true) ? $value : $fallback;
    }
}

if (!function_exists('heypFooterCanvasNumber')) {
    function heypFooterCanvasNumber($value, $min, $max, $fallback) {
        if (!is_numeric($value)) {
            return $fallback;
        }

        $value = (int) $value;
        return max($min, min($max, $value));
    }
}

if (!function_exists('heypFooterCanvasColor')) {
    function heypFooterCanvasColor($value, $fallback) {
        $value = trim((string) $value);
        if ($value === 'transparent') {
            return 'transparent';
        }

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $fallback;
    }
}

if (!function_exists('heypFooterCanvasContentHeight')) {
    function heypFooterCanvasContentHeight(array $elements, $minimumHeight = 160, $bottomPadding = 8) {
        $bottom = 0;
        foreach ($elements as $element) {
            if (!is_array($element)) {
                continue;
            }

            $bottom = max($bottom, (int) ($element['y'] ?? 0) + (int) ($element['height'] ?? 0));
        }

        return heypFooterCanvasNumber($bottom + $bottomPadding, $minimumHeight, 2400, $minimumHeight);
    }
}

if (!function_exists('heypFooterCanvasSocialKey')) {
    function heypFooterCanvasSocialKey(array $element) {
        $id = strtolower((string) ($element['id'] ?? ''));
        if (strpos($id, 'footer-social-') !== 0) {
            return '';
        }

        $content = strtolower((string) ($element['content'] ?? ''));
        $href = strtolower((string) ($element['href'] ?? ''));
        $haystack = $id . ' ' . $content . ' ' . $href;

        foreach (['shopee', 'facebook', 'youtube'] as $key) {
            if (strpos($haystack, $key) !== false) {
                return $key;
            }
        }

        return '';
    }
}

if (!function_exists('heypFooterCanvasSocialIconClass')) {
    function heypFooterCanvasSocialIconClass($key) {
        $icons = [
            'shopee' => 'fas fa-shopping-bag',
            'facebook' => 'fab fa-facebook-f',
            'youtube' => 'fab fa-youtube'
        ];

        return $icons[$key] ?? '';
    }
}

if (!function_exists('heypFooterCanvasSocialLabel')) {
    function heypFooterCanvasSocialLabel($key, $fallback = '') {
        $labels = [
            'shopee' => 'Shopee HeypVietNam',
            'facebook' => 'Facebook HeypVietNam',
            'youtube' => 'YouTube HeypVietNam'
        ];

        $fallback = trim((string) $fallback);
        return $labels[$key] ?? ($fallback !== '' ? $fallback : ucfirst((string) $key));
    }
}

if (!function_exists('heypFooterNormalizeSocialCanvasElement')) {
    function heypFooterNormalizeSocialCanvasElement(array $element) {
        $key = heypFooterCanvasSocialKey($element);
        if ($key === '') {
            return $element;
        }

        $element['content'] = heypFooterCanvasSocialLabel($key, $element['content'] ?? '');
        $element['width'] = 44;
        $element['height'] = 44;
        $element['style']['fontSize'] = max(20, (int) ($element['style']['fontSize'] ?? 20));
        $element['style']['borderRadius'] = (int) ($element['style']['borderRadius'] ?? 8) > 8 ? 8 : (int) ($element['style']['borderRadius'] ?? 8);

        return $element;
    }
}

if (!function_exists('heypFooterNormalizeCanvasContent')) {
    function heypFooterNormalizeCanvasContent($content) {
        if (!is_array($content)) {
            return heypFooterDefaultCanvasContent();
        }

        if (($content['layoutType'] ?? '') !== 'canvas' && !isset($content['elements'])) {
            return heypFooterDefaultCanvasContent($content);
        }

        $canvas = isset($content['canvas']) && is_array($content['canvas']) ? $content['canvas'] : [];
        $elements = [];
        foreach (($content['elements'] ?? []) as $index => $element) {
            if (is_array($element)) {
                $elements[] = heypFooterNormalizeCanvasElement($element, $index + 1);
            }
        }

        if (empty($elements)) {
            return heypFooterDefaultCanvasContent();
        }

        usort($elements, function($a, $b) {
            return $a['zIndex'] <=> $b['zIndex'];
        });
        foreach ($elements as $index => &$element) {
            $element['zIndex'] = $index + 1;
        }
        unset($element);

        $canvasHeight = heypFooterCanvasContentHeight($elements);

        return [
            'layoutType' => 'canvas',
            'canvas' => [
                'width' => heypFooterCanvasNumber($canvas['width'] ?? 1200, 320, 2400, 1200),
                'height' => $canvasHeight,
                'backgroundColor' => heypFooterCanvasColor($canvas['backgroundColor'] ?? '#6a7260', '#6a7260')
            ],
            'elements' => $elements
        ];
    }
}

if (!function_exists('heypFooterNormalizeCanvasElement')) {
    function heypFooterNormalizeCanvasElement(array $element, $fallbackIndex) {
        $type = heypFooterCanvasChoice($element['type'] ?? 'text', ['text', 'image', 'video', 'shape'], 'text');
        $defaultWidth = $type === 'text' ? 320 : ($type === 'video' ? 480 : 240);
        $defaultHeight = $type === 'text' ? 100 : ($type === 'video' ? 270 : 180);
        $id = preg_replace('/[^a-zA-Z0-9_-]+/', '-', trim((string) ($element['id'] ?? '')));
        $id = trim($id, '-');

        $normalized = [
            'id' => $id !== '' ? $id : 'footer-el-' . $fallbackIndex,
            'type' => $type,
            'x' => heypFooterCanvasNumber($element['x'] ?? 0, 0, 10000, 0),
            'y' => heypFooterCanvasNumber($element['y'] ?? 0, 0, 2400, 0),
            'width' => heypFooterCanvasNumber($element['width'] ?? $defaultWidth, 20, 2400, $defaultWidth),
            'height' => heypFooterCanvasNumber($element['height'] ?? $defaultHeight, 20, 2400, $defaultHeight),
            'zIndex' => heypFooterCanvasNumber($element['zIndex'] ?? $fallbackIndex, 1, 9999, $fallbackIndex),
            'content' => (string) ($element['content'] ?? ''),
            'src' => (string) ($element['src'] ?? $element['image_url'] ?? $element['url'] ?? ''),
            'href' => (string) ($element['href'] ?? ''),
            'style' => heypFooterNormalizeCanvasStyle($element['style'] ?? [], $type)
        ];

        return heypFooterNormalizeSocialCanvasElement($normalized);
    }
}

if (!function_exists('heypFooterNormalizeCanvasStyle')) {
    function heypFooterNormalizeCanvasStyle($style, $type) {
        $style = is_array($style) ? $style : [];
        $defaultBackground = $type === 'shape' ? '#d9f99d' : ($type === 'video' ? '#111827' : 'transparent');

        return [
            'fontFamily' => heypFooterCanvasChoice($style['fontFamily'] ?? 'Open Sans', ['Open Sans', 'Montserrat', 'Georgia', 'Arial', 'Times New Roman'], 'Open Sans'),
            'fontSize' => heypFooterCanvasNumber($style['fontSize'] ?? ($type === 'text' ? 16 : 18), 8, 160, $type === 'text' ? 16 : 18),
            'fontWeight' => heypFooterCanvasChoice((string) ($style['fontWeight'] ?? '400'), ['400', '500', '600', '700'], '400'),
            'color' => heypFooterCanvasColor($style['color'] ?? '#ffffff', '#ffffff'),
            'backgroundColor' => heypFooterCanvasColor($style['backgroundColor'] ?? $defaultBackground, $defaultBackground),
            'borderRadius' => heypFooterCanvasNumber($style['borderRadius'] ?? (($type === 'image' || $type === 'video') ? 8 : 0), 0, 240, ($type === 'image' || $type === 'video') ? 8 : 0)
        ];
    }
}

if (!function_exists('heypFooterCanvasFontFamily')) {
    function heypFooterCanvasFontFamily($value) {
        $fonts = [
            'Open Sans' => "'Open Sans', Arial, sans-serif",
            'Montserrat' => "Montserrat, Arial, sans-serif",
            'Georgia' => "Georgia, serif",
            'Arial' => "Arial, sans-serif",
            'Times New Roman' => "'Times New Roman', serif"
        ];

        $value = heypFooterCanvasChoice($value, array_keys($fonts), 'Open Sans');
        return $fonts[$value];
    }
}

if (!function_exists('heypFooterEscape')) {
    function heypFooterEscape($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('heypFooterAssetUrl')) {
    function heypFooterAssetUrl($src) {
        $src = trim((string) $src);
        if ($src === '') {
            return '';
        }

        if (preg_match('#^(https?:)?//#', $src) || strpos($src, 'data:') === 0) {
            return $src;
        }

        $urlRoot = defined('URL_ROOT') ? URL_ROOT : '';
        if ($src[0] === '/') {
            return $urlRoot . $src;
        }

        return $urlRoot . '/' . ltrim($src, '/');
    }
}

if (!function_exists('heypFooterLinkUrl')) {
    function heypFooterLinkUrl($url) {
        $url = trim((string) $url);
        if ($url === '') {
            return '';
        }

        if (preg_match('#^(https?:)?//#', $url) || preg_match('#^(mailto|tel):#i', $url) || strpos($url, '#') === 0) {
            return $url;
        }

        $urlRoot = defined('URL_ROOT') ? URL_ROOT : '';
        if ($url === '/') {
            return $urlRoot;
        }

        return $urlRoot . '/' . ltrim($url, '/');
    }
}

if (!function_exists('heypFooterLinkTargetAttribute')) {
    function heypFooterLinkTargetAttribute($url) {
        $urlRoot = defined('URL_ROOT') ? URL_ROOT : '';
        if ($urlRoot !== '' && strpos((string) $url, $urlRoot) === 0) {
            return '';
        }

        return preg_match('#^https?://#i', (string) $url) ? ' target="_blank" rel="noopener"' : '';
    }
}

if (!function_exists('heypFooterYoutubeEmbedUrl')) {
    function heypFooterYoutubeEmbedUrl($src) {
        $src = trim((string) $src);
        if ($src === '') {
            return '';
        }
        if (preg_match('#^(www\.)?(youtube\.com|youtu\.be)/#i', $src)) {
            $src = 'https://' . $src;
        }

        $parts = parse_url($src);
        if (!is_array($parts) || empty($parts['host'])) {
            return '';
        }

        $host = strtolower(preg_replace('/^www\./', '', $parts['host']));
        $path = $parts['path'] ?? '';
        $query = [];
        if (!empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $videoId = '';
        if ($host === 'youtu.be') {
            $videoId = explode('/', trim($path, '/'))[0] ?? '';
        } elseif (in_array($host, ['youtube.com', 'm.youtube.com', 'youtube-nocookie.com'], true)) {
            if ($path === '/watch') {
                $videoId = (string) ($query['v'] ?? '');
            } else {
                $segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));
                if (isset($segments[0], $segments[1]) && in_array($segments[0], ['embed', 'shorts', 'live'], true)) {
                    $videoId = $segments[1];
                }
            }
        }

        if (!preg_match('/^[a-zA-Z0-9_-]{6,}$/', $videoId)) {
            return '';
        }

        $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
        $start = $query['start'] ?? $query['t'] ?? '';
        $start = preg_replace('/\D+/', '', (string) $start);
        if ($start !== '') {
            $embedUrl .= '?start=' . $start;
        }

        return $embedUrl;
    }
}

if (!function_exists('heypFooterCanvasElementStyleAttribute')) {
    function heypFooterCanvasElementStyleAttribute(array $element) {
        $style = isset($element['style']) && is_array($element['style']) ? $element['style'] : [];
        $css = [
            'position:absolute',
            'left:' . heypFooterCanvasNumber($element['x'] ?? 0, 0, 10000, 0) . 'px',
            'top:' . heypFooterCanvasNumber($element['y'] ?? 0, 0, 2400, 0) . 'px',
            'width:' . heypFooterCanvasNumber($element['width'] ?? 100, 20, 2400, 100) . 'px',
            'height:' . heypFooterCanvasNumber($element['height'] ?? 100, 20, 2400, 100) . 'px',
            'z-index:' . heypFooterCanvasNumber($element['zIndex'] ?? 1, 1, 9999, 1),
            'font-family:' . heypFooterCanvasFontFamily($style['fontFamily'] ?? 'Open Sans'),
            'font-size:' . heypFooterCanvasNumber($style['fontSize'] ?? 16, 8, 160, 16) . 'px',
            'font-weight:' . heypFooterCanvasChoice((string) ($style['fontWeight'] ?? '400'), ['400', '500', '600', '700'], '400'),
            'color:' . heypFooterCanvasColor($style['color'] ?? '#ffffff', '#ffffff'),
            'background-color:' . heypFooterCanvasColor($style['backgroundColor'] ?? 'transparent', 'transparent'),
            'border-radius:' . heypFooterCanvasNumber($style['borderRadius'] ?? 0, 0, 240, 0) . 'px'
        ];

        return ' style="' . heypFooterEscape(implode(';', $css)) . '"';
    }
}

if (!function_exists('heypFooterRenderCanvasElement')) {
    function heypFooterRenderCanvasElement(array $element) {
        $type = heypFooterCanvasChoice($element['type'] ?? 'text', ['text', 'image', 'video', 'shape'], 'text');
        $href = $type === 'video' ? '' : heypFooterLinkUrl($element['href'] ?? '');
        $tag = $href !== '' ? 'a' : 'div';
        $socialKey = $type === 'text' ? heypFooterCanvasSocialKey($element) : '';
        $socialIconClass = $socialKey !== '' ? heypFooterCanvasSocialIconClass($socialKey) : '';
        $socialLabel = $socialIconClass !== '' ? heypFooterCanvasSocialLabel($socialKey, $element['content'] ?? '') : '';
        $classes = [
            'footer-canvas-element',
            'footer-canvas-element-' . $type
        ];

        if ($socialIconClass !== '') {
            $classes[] = 'footer-canvas-element-social';
            $classes[] = 'footer-canvas-social-' . $socialKey;
        }

        $linkAttribute = $href !== '' ? ' href="' . heypFooterEscape($href) . '"' . heypFooterLinkTargetAttribute($href) : '';
        if ($socialIconClass !== '') {
            $linkAttribute .= ' aria-label="' . heypFooterEscape($socialLabel) . '" title="' . heypFooterEscape($socialLabel) . '"';
        }

        echo '<' . $tag . $linkAttribute . ' id="' . heypFooterEscape($element['id'] ?? '') . '" class="' . heypFooterEscape(implode(' ', $classes)) . '"' . heypFooterCanvasElementStyleAttribute($element) . '>';

        if ($type === 'image') {
            $src = heypFooterAssetUrl($element['src'] ?? '');
            if ($src !== '') {
                echo '<img src="' . heypFooterEscape($src) . '" alt="' . heypFooterEscape($element['content'] ?? '') . '" loading="lazy">';
            }
        } elseif ($type === 'video') {
            $src = heypFooterYoutubeEmbedUrl($element['src'] ?? '');
            if ($src !== '') {
                echo '<iframe src="' . heypFooterEscape($src) . '" title="' . heypFooterEscape($element['content'] ?? 'YouTube video') . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
            }
        } elseif ($type === 'shape') {
            echo '';
        } elseif ($socialIconClass !== '') {
            echo '<i class="' . heypFooterEscape($socialIconClass) . '" aria-hidden="true"></i>';
            echo '<span class="footer-canvas-sr-only">' . heypFooterEscape($socialLabel) . '</span>';
        } else {
            $content = str_replace(
                ['{year}', '{site_name}'],
                [date('Y'), defined('SITE_NAME') ? SITE_NAME : 'HeypVietNam'],
                (string) ($element['content'] ?? '')
            );
            echo nl2br(heypFooterEscape($content));
        }

        echo '</' . $tag . '>';
    }
}

if (!function_exists('heypFooterRenderCanvas')) {
    function heypFooterRenderCanvas($content) {
        $footer = heypFooterNormalizeCanvasContent($content);
        $canvas = $footer['canvas'];
        $canvasStyle = 'position:relative;width:' . $canvas['width'] . 'px;height:' . $canvas['height'] . 'px;background-color:' . $canvas['backgroundColor'];

        echo '<div class="footer-canvas-viewport">';
        echo '<div class="footer-canvas" style="' . heypFooterEscape($canvasStyle) . '">';
        foreach ($footer['elements'] as $element) {
            heypFooterRenderCanvasElement($element);
        }
        echo '</div>';
        echo '</div>';
    }
}
