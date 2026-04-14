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

if (!function_exists('landingPageYoutubeEmbedUrl')) {
    function landingPageYoutubeEmbedUrl($src) {
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
            $videoId = trim($path, '/');
            $videoId = explode('/', $videoId)[0] ?? '';
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

if (!function_exists('landingPageChoice')) {
    function landingPageChoice($value, array $allowed, $fallback) {
        $value = (string) $value;
        return in_array($value, $allowed, true) ? $value : $fallback;
    }
}

if (!function_exists('landingPageNumber')) {
    function landingPageNumber($value, $min, $max, $fallback) {
        if (!is_numeric($value)) {
            return $fallback;
        }

        $value = (int) $value;
        return max($min, min($max, $value));
    }
}

if (!function_exists('landingPageColor')) {
    function landingPageColor($value, $fallback = '') {
        $value = trim((string) $value);

        if ($value === '') {
            return $fallback;
        }

        return preg_match('/^#[0-9a-fA-F]{6}$/', $value) ? $value : $fallback;
    }
}

if (!function_exists('landingPageFontFamily')) {
    function landingPageFontFamily($value) {
        $fonts = [
            'Open Sans' => "'Open Sans', Arial, sans-serif",
            'Montserrat' => "Montserrat, Arial, sans-serif",
            'Georgia' => "Georgia, serif",
            'Arial' => "Arial, sans-serif",
            'Times New Roman' => "'Times New Roman', serif"
        ];

        $value = landingPageChoice($value, array_keys($fonts), 'Open Sans');
        return $fonts[$value];
    }
}

if (!function_exists('landingPageStyleAttribute')) {
    function landingPageStyleAttribute($block, $type) {
        $style = isset($block['style']) && is_array($block['style']) ? $block['style'] : [];
        $align = landingPageChoice($style['align'] ?? 'left', ['left', 'center', 'right'], 'left');
        $width = landingPageNumber($style['width'] ?? 100, 10, 100, 100);
        $marginTop = landingPageNumber($style['marginTop'] ?? 0, 0, 160, 0);
        $marginBottom = landingPageNumber($style['marginBottom'] ?? 16, 0, 160, 16);
        $backgroundColor = landingPageColor($style['backgroundColor'] ?? '');

        $css = [
            'max-width:' . $width . '%',
            'width:100%',
            'margin-top:' . $marginTop . 'px',
            'margin-bottom:' . $marginBottom . 'px'
        ];

        if ($align === 'center') {
            $css[] = 'margin-left:auto';
            $css[] = 'margin-right:auto';
        } elseif ($align === 'right') {
            $css[] = 'margin-left:auto';
            $css[] = 'margin-right:0';
        } else {
            $css[] = 'margin-left:0';
            $css[] = 'margin-right:auto';
        }

        if ($type === 'heading' || $type === 'text') {
            $fontSize = landingPageNumber($style['fontSize'] ?? ($type === 'heading' ? 40 : 18), 10, 96, $type === 'heading' ? 40 : 18);
            $fontWeight = landingPageChoice((string) ($style['fontWeight'] ?? '400'), ['400', '500', '600', '700'], '400');
            $fontStyle = landingPageChoice($style['fontStyle'] ?? 'normal', ['normal', 'italic'], 'normal');
            $color = landingPageColor($style['color'] ?? '#243528', '#243528');
            $textAlign = landingPageChoice($style['textAlign'] ?? $align, ['left', 'center', 'right'], $align);

            $css[] = 'font-family:' . landingPageFontFamily($style['fontFamily'] ?? 'Open Sans');
            $css[] = 'font-size:' . $fontSize . 'px';
            $css[] = 'font-weight:' . $fontWeight;
            $css[] = 'font-style:' . $fontStyle;
            $css[] = 'color:' . $color;
            $css[] = 'text-align:' . $textAlign;
        }

        if ($type === 'columns') {
            $columnGap = landingPageNumber($style['columnGap'] ?? 24, 0, 80, 24);
            $firstColumnWidth = landingPageNumber($style['firstColumnWidth'] ?? 50, 20, 80, 50);

            $css[] = 'gap:' . $columnGap . 'px';
            $css[] = '--landing-first-column:' . $firstColumnWidth . '%';
            $css[] = '--landing-second-column:' . (100 - $firstColumnWidth) . '%';
        }

        if ($backgroundColor !== '') {
            $css[] = 'background-color:' . $backgroundColor;
            $css[] = 'padding:12px';
            $css[] = 'border-radius:8px';
        }

        return ' style="' . landingPageEscape(implode(';', $css)) . '"';
    }
}

if (!function_exists('landingPageImageStyleAttribute')) {
    function landingPageImageStyleAttribute($block) {
        $style = isset($block['style']) && is_array($block['style']) ? $block['style'] : [];
        $borderRadius = landingPageNumber($style['borderRadius'] ?? 8, 0, 40, 8);

        return ' style="' . landingPageEscape('border-radius:' . $borderRadius . 'px') . '"';
    }
}

if (!function_exists('landingPageCanvasColor')) {
    function landingPageCanvasColor($value, $fallback) {
        $value = trim((string) $value);

        if ($value === 'transparent') {
            return 'transparent';
        }

        return landingPageColor($value, $fallback);
    }
}

if (!function_exists('landingPageCanvasElementStyleAttribute')) {
    function landingPageCanvasElementStyleAttribute($element) {
        $style = isset($element['style']) && is_array($element['style']) ? $element['style'] : [];
        $type = landingPageChoice($element['type'] ?? 'text', ['text', 'image', 'video', 'shape'], 'text');
        $defaultBackground = $type === 'shape' ? '#d9f99d' : ($type === 'video' ? '#111827' : 'transparent');
        $backgroundColor = landingPageCanvasColor($style['backgroundColor'] ?? $defaultBackground, $defaultBackground);
        $color = landingPageCanvasColor($style['color'] ?? '#1f2937', '#1f2937');

        $css = [
            'position:absolute',
            'left:' . landingPageNumber($element['x'] ?? 0, 0, 10000, 0) . 'px',
            'top:' . landingPageNumber($element['y'] ?? 0, 0, 10000, 0) . 'px',
            'width:' . landingPageNumber($element['width'] ?? 100, 20, 2400, 100) . 'px',
            'height:' . landingPageNumber($element['height'] ?? 100, 20, 3200, 100) . 'px',
            'z-index:' . landingPageNumber($element['zIndex'] ?? 1, 1, 9999, 1),
            'font-family:' . landingPageFontFamily($style['fontFamily'] ?? 'Open Sans'),
            'font-size:' . landingPageNumber($style['fontSize'] ?? ($type === 'text' ? 24 : 18), 8, 160, $type === 'text' ? 24 : 18) . 'px',
            'font-weight:' . landingPageChoice((string) ($style['fontWeight'] ?? '400'), ['400', '500', '600', '700'], '400'),
            'color:' . $color,
            'background-color:' . $backgroundColor,
            'border-radius:' . landingPageNumber($style['borderRadius'] ?? (($type === 'image' || $type === 'video') ? 8 : 0), 0, 240, ($type === 'image' || $type === 'video') ? 8 : 0) . 'px'
        ];

        return ' style="' . landingPageEscape(implode(';', $css)) . '"';
    }
}

if (!function_exists('renderCanvasPage')) {
    function renderCanvasPage($page) {
        $canvas = isset($page['canvas']) && is_array($page['canvas']) ? $page['canvas'] : [];
        $width = landingPageNumber($canvas['width'] ?? 1200, 320, 2400, 1200);
        $height = landingPageNumber($canvas['height'] ?? 760, 320, 10000, 760);
        $backgroundColor = landingPageColor($canvas['backgroundColor'] ?? '#ffffff', '#ffffff');
        $canvasStyle = 'position:relative;width:' . $width . 'px;height:' . $height . 'px;background-color:' . $backgroundColor;

        echo '<div id="top" class="landing-page landing-page-canvas">';
        echo '<div class="landing-canvas-viewport">';
        echo '<div class="landing-canvas" style="' . landingPageEscape($canvasStyle) . '">';

        foreach ($page['elements'] ?? [] as $element) {
            if (is_array($element)) {
                renderCanvasElement($element);
            }
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}

if (!function_exists('renderCanvasElement')) {
    function renderCanvasElement($element) {
        $type = landingPageChoice($element['type'] ?? 'text', ['text', 'image', 'video', 'shape'], 'text');
        $id = landingPageEscape($element['id'] ?? '');

        echo '<div id="' . $id . '" class="landing-canvas-element landing-canvas-element-' . landingPageEscape($type) . '"' . landingPageCanvasElementStyleAttribute($element) . '>';

        if ($type === 'image') {
            $src = landingPageAssetUrl($element['src'] ?? $element['image_url'] ?? $element['url'] ?? '');
            if ($src !== '') {
                echo '<img src="' . landingPageEscape($src) . '" alt="' . landingPageEscape($element['content'] ?? '') . '" loading="lazy">';
            }
        } elseif ($type === 'video') {
            $src = landingPageYoutubeEmbedUrl($element['src'] ?? $element['url'] ?? '');
            if ($src !== '') {
                echo '<iframe src="' . landingPageEscape($src) . '" title="' . landingPageEscape($element['content'] ?? 'YouTube video') . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
            }
        } elseif ($type === 'shape') {
            echo '';
        } else {
            echo nl2br(landingPageEscape($element['content'] ?? ''));
        }

        echo '</div>';
    }
}

if (!function_exists('renderPage')) {
    function renderPage($page) {
        if (!is_array($page)) {
            return;
        }

        if (($page['layoutType'] ?? '') === 'canvas' || isset($page['elements'])) {
            renderCanvasPage($page);
            return;
        }

        echo '<div id="top" class="landing-page">';

        foreach ($page['sections'] ?? [] as $index => $section) {
            if (!is_array($section)) {
                continue;
            }

            if (empty($section['id'])) {
                $heading = isset($section['heading']) && is_array($section['heading']) ? $section['heading'] : [];
                $section['id'] = landingPageAnchorId(
                    $heading['anchorId'] ?? $heading['text'] ?? '',
                    'section-' . ($index + 1)
                );
            }

            renderSection($section);
        }

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
            $section['id'] ?? $heading['anchorId'] ?? '',
            'section'
        );

        echo '<section id="' . landingPageEscape($sectionId) . '" class="landing-section">';
        echo '<div class="container landing-section-container">';

        if (!empty($heading['text'])) {
            renderBlock([
                'type' => 'heading',
                'level' => $heading['level'] ?? 1,
                'content' => $heading['text'],
                'style' => $heading['style'] ?? []
            ]);
        }

        echo '<div class="landing-blocks">';
        foreach ($section['blocks'] ?? [] as $block) {
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

        $type = $block['type'] ?? '';

        if ($type === 'heading') {
            $level = isset($block['level']) ? (int) $block['level'] : 2;
            $level = in_array($level, [1, 2, 3], true) ? $level : 2;
            $text = $block['content'] ?? $block['text'] ?? '';

            if ($text === '') {
                return;
            }

            echo '<h' . $level . ' class="landing-heading landing-heading-' . $level . '"' . landingPageStyleAttribute($block, 'heading') . '>';
            echo landingPageEscape($text);
            echo '</h' . $level . '>';
            return;
        }

        if ($type === 'text') {
            $content = $block['content'] ?? '';
            if ($content === '') {
                return;
            }

            echo '<p class="landing-text"' . landingPageStyleAttribute($block, 'text') . '>' . nl2br(landingPageEscape($content)) . '</p>';
            return;
        }

        if ($type === 'image') {
            $src = landingPageAssetUrl($block['src'] ?? $block['url'] ?? $block['image_url'] ?? '');
            if ($src === '') {
                return;
            }

            $alt = $block['alt'] ?? $block['content'] ?? '';

            echo '<figure class="landing-image"' . landingPageStyleAttribute($block, 'image') . '>';
            echo '<img src="' . landingPageEscape($src) . '" alt="' . landingPageEscape($alt) . '" loading="lazy"' . landingPageImageStyleAttribute($block) . '>';

            if (!empty($block['caption'])) {
                echo '<figcaption>' . landingPageEscape($block['caption']) . '</figcaption>';
            }

            echo '</figure>';
            return;
        }

        if ($type === 'columns') {
            $columns = isset($block['columns']) && is_array($block['columns']) ? $block['columns'] : [];
            if (empty($columns)) {
                return;
            }

            echo '<div class="landing-columns"' . landingPageStyleAttribute($block, 'columns') . '>';
            foreach (array_slice($columns, 0, 2) as $columnIndex => $columnBlock) {
                if (!is_array($columnBlock)) {
                    continue;
                }

                echo '<div class="landing-column landing-column-' . ($columnIndex + 1) . '">';
                renderBlock($columnBlock);
                echo '</div>';
            }
            echo '</div>';
        }
    }
}
