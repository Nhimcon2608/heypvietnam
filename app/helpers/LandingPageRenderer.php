<?php

if (!function_exists('landingPageEscape')) {
    function landingPageEscape($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('landingPageAnchorId')) {
    function landingPageAnchorId($value, $fallback = 'section') {
        $id = strtolower(trim((string) $value));
        $id = preg_replace('/[^a-z0-9_-]+/', '-', $id);
        $id = trim($id, '-');

        return $id !== '' ? $id : $fallback;
    }
}

if (!function_exists('landingPageAssetUrl')) {
    function landingPageAssetUrl($src) {
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

        if (strpos($src, 'public/') === 0 || strpos($src, 'image.php') === 0) {
            return $urlRoot . '/' . $src;
        }

        return $urlRoot . '/public/img/' . $src;
    }
}

if (!function_exists('landingPageAttributes')) {
    function landingPageAttributes(array $attributes) {
        $html = '';

        foreach ($attributes as $name => $value) {
            if ($value === null || $value === false || $value === '') {
                continue;
            }

            if ($value === true) {
                $html .= ' ' . landingPageEscape($name);
                continue;
            }

            $html .= ' ' . landingPageEscape($name) . '="' . landingPageEscape($value) . '"';
        }

        return $html;
    }
}

if (!function_exists('landingPageBlockText')) {
    function landingPageBlockText(array $block) {
        return trim((string) ($block['content'] ?? $block['text'] ?? ''));
    }
}

if (!function_exists('landingPageYouTubeEmbedUrl')) {
    function landingPageYouTubeEmbedUrl($src) {
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
            $segments = array_values(array_filter(explode('/', $path)));
            $videoId = $segments[0] ?? '';
        } elseif (in_array($host, ['youtube.com', 'm.youtube.com', 'youtube-nocookie.com'], true)) {
            if ($path === '/watch') {
                $videoId = (string) ($query['v'] ?? '');
            } else {
                $segments = array_values(array_filter(explode('/', $path)));
                if (in_array($segments[0] ?? '', ['embed', 'shorts', 'live'], true)) {
                    $videoId = $segments[1] ?? '';
                }
            }
        }

        if (!preg_match('/^[a-zA-Z0-9_-]{6,}$/', $videoId)) {
            return '';
        }

        $embedUrl = 'https://www.youtube.com/embed/' . rawurlencode($videoId);
        $start = $query['start'] ?? $query['t'] ?? '';
        $start = preg_replace('/\D+/', '', (string) $start);
        if ($start !== '') {
            $embedUrl .= '?start=' . $start;
        }

        return $embedUrl;
    }
}

if (!function_exists('renderPage')) {
    function renderPage($page) {
        if (!is_array($page)) {
            return;
        }

        if (landingPageIsCanvasContent($page)) {
            echo '<div' . landingPageAttributes([
                'id' => 'top',
                'class' => 'landing-page landing-page-canvas'
            ]) . '>';
            renderLandingCanvas($page);
            echo '</div>';
            return;
        }

        echo '<div' . landingPageAttributes([
            'id' => 'top',
            'class' => 'landing-page'
        ]) . '>';

        foreach (($page['sections'] ?? []) as $index => $section) {
            if (!is_array($section)) {
                continue;
            }

            $heading = isset($section['heading']) && is_array($section['heading']) ? $section['heading'] : [];
            $section['id'] = landingPageAnchorId(
                $section['id'] ?? $heading['anchorId'] ?? $heading['text'] ?? '',
                'section-' . ($index + 1)
            );

            renderSection($section);
        }

        echo '</div>';
    }
}

if (!function_exists('landingPageIsCanvasContent')) {
    function landingPageIsCanvasContent($page) {
        return is_array($page) && (($page['layoutType'] ?? '') === 'canvas' || isset($page['elements']));
    }
}

if (!function_exists('landingPageCanvasChoice')) {
    function landingPageCanvasChoice($value, array $allowed, $fallback) {
        $value = (string) $value;
        return in_array($value, $allowed, true) ? $value : $fallback;
    }
}

if (!function_exists('landingPageCanvasNumber')) {
    function landingPageCanvasNumber($value, $min, $max, $fallback) {
        if (!is_numeric($value)) {
            return $fallback;
        }

        $value = (int) $value;
        return max($min, min($max, $value));
    }
}

if (!function_exists('landingPageCanvasColor')) {
    function landingPageCanvasColor($value, $fallback) {
        $value = trim((string) $value);
        if ($value === 'transparent') {
            return 'transparent';
        }

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $fallback;
    }
}

if (!function_exists('landingPageRichTextStyleValue')) {
    function landingPageRichTextStyleValue($property, $value) {
        $value = trim((string) $value);

        if ($property === 'font-family') {
            $family = trim(explode(',', str_replace(['"', "'"], '', $value))[0]);
            return in_array($family, ['Open Sans', 'Montserrat', 'Georgia', 'Arial', 'Times New Roman'], true) ? $family : '';
        }

        if ($property === 'font-size') {
            $size = (int) preg_replace('/[^0-9]/', '', $value);
            return $size >= 8 && $size <= 160 ? $size . 'px' : '';
        }

        if ($property === 'font-weight') {
            $weight = $value === 'bold' ? '700' : $value;
            return in_array($weight, ['400', '500', '600', '700'], true) ? $weight : '';
        }

        if ($property === 'font-style') {
            return $value === 'italic' ? 'italic' : '';
        }

        if ($property === 'text-decoration') {
            return strpos($value, 'underline') !== false ? 'underline' : '';
        }

        if ($property === 'color' || $property === 'background-color') {
            return landingPageCanvasColor($value, '');
        }

        return '';
    }
}

if (!function_exists('landingPageSanitizeRichTextStyle')) {
    function landingPageSanitizeRichTextStyle($style) {
        $rules = [];
        foreach (explode(';', (string) $style) as $rule) {
            $parts = explode(':', $rule, 2);
            if (count($parts) !== 2) {
                continue;
            }

            $property = strtolower(trim($parts[0]));
            $value = landingPageRichTextStyleValue($property, $parts[1]);
            if ($value !== '') {
                $rules[] = $property . ': ' . $value;
            }
        }

        return implode('; ', $rules);
    }
}

if (!function_exists('landingPageSanitizeRichTextHtml')) {
    function landingPageSanitizeRichTextHtml($html) {
        $html = strip_tags((string) $html, '<span><strong><b><em><i><u><br>');
        $html = preg_replace_callback('/<span\b([^>]*)>/i', function($matches) {
            $style = '';
            if (preg_match('/style\s*=\s*(["\'])(.*?)\1/is', $matches[1] ?? '', $styleMatch)) {
                $style = landingPageSanitizeRichTextStyle($styleMatch[2]);
            }

            return $style !== '' ? '<span style="' . landingPageEscape($style) . '">' : '<span>';
        }, $html);
        $html = preg_replace('/<\s*(strong|b)\b[^>]*>/i', '<strong>', $html);
        $html = preg_replace('/<\s*\/\s*(strong|b)\s*>/i', '</strong>', $html);
        $html = preg_replace('/<\s*(em|i)\b[^>]*>/i', '<em>', $html);
        $html = preg_replace('/<\s*\/\s*(em|i)\s*>/i', '</em>', $html);
        $html = preg_replace('/<\s*u\b[^>]*>/i', '<u>', $html);
        $html = preg_replace('/<\s*\/\s*u\s*>/i', '</u>', $html);
        $html = preg_replace('/<br\s*\/?>/i', '<br>', $html);

        return preg_replace('/(?:<br>){2,}$/', '<br>', $html);
    }
}

if (!function_exists('landingPageCanvasContentHeight')) {
    function landingPageCanvasContentHeight(array $elements, $minimumHeight = 760, $bottomPadding = 180) {
        $bottom = 0;
        foreach ($elements as $element) {
            if (!is_array($element)) {
                continue;
            }

            $bottom = max($bottom, (int) ($element['y'] ?? 0) + (int) ($element['height'] ?? 0));
        }

        return landingPageCanvasNumber($bottom + $bottomPadding, $minimumHeight, 10000, $minimumHeight);
    }
}

if (!function_exists('landingPageNormalizeCanvasStyle')) {
    function landingPageNormalizeCanvasStyle($style, $type) {
        $style = is_array($style) ? $style : [];
        $defaultBackground = $type === 'shape' ? '#d9f99d' : ($type === 'video' ? '#111827' : 'transparent');

        return [
            'fontFamily' => landingPageCanvasChoice(
                $style['fontFamily'] ?? 'Open Sans',
                ['Open Sans', 'Montserrat', 'Georgia', 'Arial', 'Times New Roman'],
                'Open Sans'
            ),
            'fontSize' => landingPageCanvasNumber($style['fontSize'] ?? ($type === 'text' ? 24 : 18), 8, 160, $type === 'text' ? 24 : 18),
            'fontWeight' => landingPageCanvasChoice((string) ($style['fontWeight'] ?? '400'), ['400', '500', '600', '700'], '400'),
            'fontStyle' => landingPageCanvasChoice((string) ($style['fontStyle'] ?? 'normal'), ['normal', 'italic'], 'normal'),
            'textAlign' => landingPageCanvasChoice((string) ($style['textAlign'] ?? 'left'), ['left', 'center', 'right'], 'left'),
            'textDecoration' => landingPageCanvasChoice((string) ($style['textDecoration'] ?? 'none'), ['none', 'underline'], 'none'),
            'color' => landingPageCanvasColor($style['color'] ?? '#1f2937', '#1f2937'),
            'backgroundColor' => landingPageCanvasColor($style['backgroundColor'] ?? $defaultBackground, $defaultBackground),
            'borderRadius' => landingPageCanvasNumber($style['borderRadius'] ?? (($type === 'image' || $type === 'video') ? 8 : 0), 0, 240, ($type === 'image' || $type === 'video') ? 8 : 0)
        ];
    }
}

if (!function_exists('landingPageNormalizeCanvasElement')) {
    function landingPageNormalizeCanvasElement(array $element, $fallbackIndex) {
        $type = landingPageCanvasChoice($element['type'] ?? 'text', ['text', 'image', 'video', 'shape'], 'text');
        $defaultWidth = $type === 'text' ? 320 : ($type === 'video' ? 480 : 240);
        $defaultHeight = $type === 'text' ? 100 : ($type === 'video' ? 270 : 180);
        $id = preg_replace('/[^a-zA-Z0-9_-]+/', '-', trim((string) ($element['id'] ?? '')));
        $id = trim($id, '-');
        $headingLevel = landingPageCanvasNumber($element['headingLevel'] ?? $element['level'] ?? 0, 0, 3, 0);

        $normalized = [
            'id' => $id !== '' ? $id : 'landing-el-' . $fallbackIndex,
            'type' => $type,
            'x' => landingPageCanvasNumber($element['x'] ?? 120, 0, 10000, 120),
            'y' => landingPageCanvasNumber($element['y'] ?? 120, 0, 10000, 120),
            'width' => landingPageCanvasNumber($element['width'] ?? $defaultWidth, 20, 2400, $defaultWidth),
            'height' => landingPageCanvasNumber($element['height'] ?? $defaultHeight, 20, 3200, $defaultHeight),
            'zIndex' => landingPageCanvasNumber($element['zIndex'] ?? $fallbackIndex, 1, 9999, $fallbackIndex),
            'content' => (string) ($element['content'] ?? ''),
            'src' => (string) ($element['src'] ?? $element['image_url'] ?? $element['url'] ?? ''),
            'href' => (string) ($element['href'] ?? ''),
            'style' => landingPageNormalizeCanvasStyle($element['style'] ?? [], $type)
        ];

        if ($type === 'text' && $headingLevel > 0) {
            $normalized['headingLevel'] = $headingLevel;
        }
        if ($type === 'text' && trim((string) ($element['contentHtml'] ?? '')) !== '') {
            $normalized['contentHtml'] = landingPageSanitizeRichTextHtml($element['contentHtml']);
        }
        if ($type === 'text' && trim((string) ($element['navLabel'] ?? '')) !== '') {
            $normalized['navLabel'] = trim((string) $element['navLabel']);
        }

        return $normalized;
    }
}

if (!function_exists('landingPageNormalizeCanvasContent')) {
    function landingPageNormalizeCanvasContent($content) {
        $content = is_array($content) ? $content : [];
        $canvas = isset($content['canvas']) && is_array($content['canvas']) ? $content['canvas'] : [];
        $elements = [];

        foreach (($content['elements'] ?? []) as $index => $element) {
            if (is_array($element)) {
                $elements[] = landingPageNormalizeCanvasElement($element, $index + 1);
            }
        }

        usort($elements, function($a, $b) {
            return $a['zIndex'] <=> $b['zIndex'];
        });
        foreach ($elements as $index => &$element) {
            $element['zIndex'] = $index + 1;
        }
        unset($element);

        return [
            'header' => isset($content['header']) && is_array($content['header']) ? $content['header'] : ['logo' => 'public/img/logoHEYP.png'],
            'layoutType' => 'canvas',
            'canvas' => [
                'width' => landingPageCanvasNumber($canvas['width'] ?? 1200, 320, 2400, 1200),
                'height' => landingPageCanvasContentHeight($elements),
                'backgroundColor' => landingPageCanvasColor($canvas['backgroundColor'] ?? '#ffffff', '#ffffff')
            ],
            'elements' => $elements
        ];
    }
}

if (!function_exists('landingPageCanvasFontFamily')) {
    function landingPageCanvasFontFamily($value) {
        $fonts = [
            'Open Sans' => "'Open Sans', Arial, sans-serif",
            'Montserrat' => "Montserrat, Arial, sans-serif",
            'Georgia' => "Georgia, serif",
            'Arial' => "Arial, sans-serif",
            'Times New Roman' => "'Times New Roman', serif"
        ];

        $value = landingPageCanvasChoice($value, array_keys($fonts), 'Open Sans');
        return $fonts[$value];
    }
}

if (!function_exists('landingPageLinkUrl')) {
    function landingPageLinkUrl($url) {
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

if (!function_exists('landingPageLinkTargetAttribute')) {
    function landingPageLinkTargetAttribute($url) {
        $urlRoot = defined('URL_ROOT') ? URL_ROOT : '';
        if ($urlRoot !== '' && strpos((string) $url, $urlRoot) === 0) {
            return '';
        }

        return preg_match('#^https?://#i', (string) $url) ? ' target="_blank" rel="noopener"' : '';
    }
}

if (!function_exists('landingPageCanvasElementStyleAttribute')) {
    function landingPageCanvasElementStyleAttribute(array $element) {
        $style = isset($element['style']) && is_array($element['style']) ? $element['style'] : [];
        $css = [
            'position:absolute',
            'left:' . landingPageCanvasNumber($element['x'] ?? 0, 0, 10000, 0) . 'px',
            'top:' . landingPageCanvasNumber($element['y'] ?? 0, 0, 10000, 0) . 'px',
            'width:' . landingPageCanvasNumber($element['width'] ?? 100, 20, 2400, 100) . 'px',
            'height:' . landingPageCanvasNumber($element['height'] ?? 100, 20, 3200, 100) . 'px',
            'z-index:' . landingPageCanvasNumber($element['zIndex'] ?? 1, 1, 9999, 1),
            'font-family:' . landingPageCanvasFontFamily($style['fontFamily'] ?? 'Open Sans'),
            'font-size:' . landingPageCanvasNumber($style['fontSize'] ?? 24, 8, 160, 24) . 'px',
            'font-weight:' . landingPageCanvasChoice((string) ($style['fontWeight'] ?? '400'), ['400', '500', '600', '700'], '400'),
            'font-style:' . landingPageCanvasChoice((string) ($style['fontStyle'] ?? 'normal'), ['normal', 'italic'], 'normal'),
            'text-align:' . landingPageCanvasChoice((string) ($style['textAlign'] ?? 'left'), ['left', 'center', 'right'], 'left'),
            'text-decoration:' . landingPageCanvasChoice((string) ($style['textDecoration'] ?? 'none'), ['none', 'underline'], 'none'),
            'color:' . landingPageCanvasColor($style['color'] ?? '#1f2937', '#1f2937'),
            'background-color:' . landingPageCanvasColor($style['backgroundColor'] ?? 'transparent', 'transparent'),
            'border-radius:' . landingPageCanvasNumber($style['borderRadius'] ?? 0, 0, 240, 0) . 'px'
        ];

        return ' style="' . landingPageEscape(implode(';', $css)) . '"';
    }
}

if (!function_exists('renderLandingCanvasElement')) {
    function renderLandingCanvasElement(array $element) {
        $type = landingPageCanvasChoice($element['type'] ?? 'text', ['text', 'image', 'video', 'shape'], 'text');
        $href = $type === 'video' ? '' : landingPageLinkUrl($element['href'] ?? '');
        $tag = $href !== '' ? 'a' : 'div';
        $classes = [
            'landing-canvas-element',
            'landing-canvas-element-' . $type
        ];

        $linkAttribute = $href !== '' ? ' href="' . landingPageEscape($href) . '"' . landingPageLinkTargetAttribute($href) : '';
        echo '<' . $tag . $linkAttribute . ' id="' . landingPageEscape($element['id'] ?? '') . '" class="' . landingPageEscape(implode(' ', $classes)) . '"' . landingPageCanvasElementStyleAttribute($element) . '>';

        if ($type === 'image') {
            $src = landingPageAssetUrl($element['src'] ?? '');
            if ($src !== '') {
                echo '<img src="' . landingPageEscape($src) . '" alt="' . landingPageEscape($element['content'] ?? '') . '" loading="lazy">';
            }
        } elseif ($type === 'video') {
            $src = landingPageYouTubeEmbedUrl($element['src'] ?? '');
            if ($src !== '') {
                echo '<iframe src="' . landingPageEscape($src) . '" title="' . landingPageEscape($element['content'] ?? 'YouTube video') . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
            }
        } elseif ($type === 'shape') {
            echo '';
        } else {
            $contentHtml = trim((string) ($element['contentHtml'] ?? ''));
            echo $contentHtml !== ''
                ? landingPageSanitizeRichTextHtml($contentHtml)
                : landingPageEscape($element['content'] ?? '');
        }

        echo '</' . $tag . '>';
    }
}

if (!function_exists('renderLandingCanvas')) {
    function renderLandingCanvas($content) {
        $page = landingPageNormalizeCanvasContent($content);
        $canvas = $page['canvas'];
        $canvasStyle = 'position:relative;width:' . $canvas['width'] . 'px;height:' . $canvas['height'] . 'px;background-color:' . $canvas['backgroundColor'];

        echo '<div class="landing-canvas-viewport">';
        echo '<div class="landing-canvas" style="' . landingPageEscape($canvasStyle) . '">';
        foreach ($page['elements'] as $element) {
            renderLandingCanvasElement($element);
        }
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('renderSection')) {
    function renderSection($section) {
        if (!is_array($section)) {
            return;
        }

        $heading = isset($section['heading']) && is_array($section['heading']) ? $section['heading'] : [];
        $sectionId = landingPageAnchorId(
            $section['id'] ?? $heading['anchorId'] ?? $heading['text'] ?? '',
            'section'
        );

        echo '<section' . landingPageAttributes([
            'id' => $sectionId,
            'class' => 'landing-section'
        ]) . '>';
        echo '<div class="container landing-section-container">';

        if (trim((string) ($heading['text'] ?? '')) !== '') {
            renderBlock([
                'type' => 'heading',
                'level' => $heading['level'] ?? 1,
                'content' => $heading['text']
            ]);
        }

        echo '<div class="landing-blocks">';
        foreach (($section['blocks'] ?? []) as $block) {
            if (!is_array($block)) {
                continue;
            }

            echo '<div class="landing-block">';
            renderBlock($block);
            echo '</div>';
        }
        echo '</div>';

        echo '</div>';
        echo '</section>';
    }
}

if (!function_exists('renderBlock')) {
    function renderBlock($block) {
        if (!is_array($block)) {
            return;
        }

        $type = (string) ($block['type'] ?? '');

        if ($type === 'heading') {
            $level = (int) ($block['level'] ?? 2);
            $level = in_array($level, [1, 2, 3], true) ? $level : 2;
            $text = landingPageBlockText($block);

            if ($text === '') {
                return;
            }

            echo '<h' . $level . landingPageAttributes([
                'class' => 'landing-heading landing-heading-' . $level
            ]) . '>' . landingPageEscape($text) . '</h' . $level . '>';
            return;
        }

        if ($type === 'text') {
            $text = landingPageBlockText($block);
            if ($text === '') {
                return;
            }

            echo '<p class="landing-text">' . landingPageEscape($text) . '</p>';
            return;
        }

        if ($type === 'image') {
            $src = landingPageAssetUrl($block['src'] ?? $block['url'] ?? $block['image_url'] ?? '');
            if ($src === '') {
                return;
            }

            $alt = (string) ($block['alt'] ?? $block['content'] ?? '');
            $caption = trim((string) ($block['caption'] ?? ''));

            echo '<figure class="landing-image">';
            echo '<img' . landingPageAttributes([
                'src' => $src,
                'alt' => $alt,
                'loading' => 'lazy'
            ]) . '>';

            if ($caption !== '') {
                echo '<figcaption>' . landingPageEscape($caption) . '</figcaption>';
            }

            echo '</figure>';
            return;
        }

        if ($type === 'video') {
            $src = landingPageYouTubeEmbedUrl($block['src'] ?? $block['url'] ?? $block['video_url'] ?? '');
            if ($src === '') {
                return;
            }

            $title = trim((string) ($block['title'] ?? $block['content'] ?? 'YouTube video'));
            $caption = trim((string) ($block['caption'] ?? ''));

            echo '<figure class="landing-video">';
            echo '<div class="landing-video-frame">';
            echo '<iframe' . landingPageAttributes([
                'src' => $src,
                'title' => $title,
                'loading' => 'lazy',
                'allow' => 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
                'allowfullscreen' => true
            ]) . '></iframe>';
            echo '</div>';

            if ($caption !== '') {
                echo '<figcaption>' . landingPageEscape($caption) . '</figcaption>';
            }

            echo '</figure>';
        }
    }
}
