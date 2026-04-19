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

if (!function_exists('renderPage')) {
    function renderPage($page) {
        if (!is_array($page)) {
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

            echo '<p class="landing-text">' . nl2br(landingPageEscape($text)) . '</p>';
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
        }
    }
}
