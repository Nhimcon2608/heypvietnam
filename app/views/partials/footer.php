<?php
require_once dirname(__DIR__, 2) . '/helpers/FooterSettings.php';

$footer = heypFooterDefaultContent();
$footerCanvas = null;

try {
    if (class_exists('\\App\\Models\\Setting')) {
        $settingModel = new \App\Models\Setting();
        $rawFooterContent = $settingModel->getSettingValue('footer_content', '');
        $decodedFooterContent = json_decode((string) $rawFooterContent, true);
        if (is_array($decodedFooterContent) && (($decodedFooterContent['layoutType'] ?? '') === 'canvas' || isset($decodedFooterContent['elements']))) {
            $footerCanvas = heypFooterNormalizeCanvasContent($decodedFooterContent);
        } else {
            $footer = heypFooterNormalizeContent(is_array($decodedFooterContent) ? $decodedFooterContent : []);
        }
    }
} catch (\Throwable $exception) {
    $footer = heypFooterDefaultContent();
    $footerCanvas = null;
}

$footerEscape = function($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

$footerUrl = function($url) {
    $url = trim((string) $url);
    if ($url === '') {
        return '#';
    }

    if (preg_match('#^(https?:)?//#', $url) || preg_match('#^(mailto|tel):#i', $url) || strpos($url, '#') === 0) {
        return $url;
    }

    $urlRoot = defined('URL_ROOT') ? URL_ROOT : '';
    if ($url === '/') {
        return $urlRoot;
    }

    return $urlRoot . '/' . ltrim($url, '/');
};

$copyright = str_replace(
    ['{year}', '{site_name}'],
    [date('Y'), defined('SITE_NAME') ? SITE_NAME : ($footer['brand_title'] ?? 'HeypVietNam')],
    $footer['copyright'] ?? ''
);
?>

<?php if (is_array($footerCanvas)): ?>
    <footer class="site-footer site-footer-canvas">
        <?php heypFooterRenderCanvas($footerCanvas); ?>
    </footer>

    <style>
    .site-footer.site-footer-canvas {
        padding: 0;
        background: transparent;
        box-shadow: none;
        overflow: hidden;
    }

    .site-footer.site-footer-canvas::before {
        display: none;
    }

    .footer-canvas-viewport {
        width: 100%;
        overflow: hidden;
        background: #6a7260;
    }

    .footer-canvas {
        position: relative;
        margin: 0 auto;
        overflow: hidden;
        transform-origin: top left;
    }

    .footer-canvas-element {
        position: absolute;
        display: block;
        overflow: hidden;
        box-sizing: border-box;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
        line-height: 1.25;
        text-decoration: none;
    }

    .footer-canvas-element-text {
        padding: 8px;
    }

    .footer-canvas-element-social {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        text-align: center;
        white-space: normal;
    }

    .footer-canvas-element-social i {
        font-size: 1.1em;
        line-height: 1;
    }

    .footer-canvas-sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .footer-canvas-element-image img,
    .footer-canvas-element-video iframe {
        display: block;
        width: 100%;
        height: 100%;
        border: 0;
        object-fit: cover;
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewport = document.querySelector('.footer-canvas-viewport');
        const canvas = document.querySelector('.footer-canvas');

        if (!viewport || !canvas) {
            return;
        }

        const designWidth = parseFloat(canvas.style.width) || canvas.offsetWidth;
        const designHeight = parseFloat(canvas.style.height) || canvas.offsetHeight;

        function fitFooterCanvas() {
            const viewportWidth = viewport.clientWidth || window.innerWidth || designWidth;
            const scale = Math.min(1, viewportWidth / designWidth);

            canvas.style.transform = 'scale(' + scale + ')';
            viewport.style.height = Math.ceil(designHeight * scale) + 'px';
        }

        fitFooterCanvas();
        window.addEventListener('resize', fitFooterCanvas);
    });
    </script>
    <?php return; ?>
<?php endif; ?>

<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section about">
                <?php if (($footer['brand_title'] ?? '') !== ''): ?>
                    <h3><?php echo $footerEscape($footer['brand_title']); ?></h3>
                <?php endif; ?>

                <?php if (($footer['description'] ?? '') !== ''): ?>
                    <p><?php echo nl2br($footerEscape($footer['description'])); ?></p>
                <?php endif; ?>

                <div class="contact">
                    <?php if (($footer['phone'] ?? '') !== ''): ?>
                        <span><i class="fas fa-phone"></i> &nbsp; <?php echo $footerEscape($footer['phone']); ?></span>
                    <?php endif; ?>

                    <?php if (($footer['email'] ?? '') !== ''): ?>
                        <span><i class="fas fa-envelope"></i> &nbsp; <?php echo $footerEscape($footer['email']); ?></span>
                    <?php endif; ?>
                </div>

                <div class="socials">
                    <?php foreach (($footer['socials'] ?? []) as $social): ?>
                        <?php
                            $socialUrl = $social['url'] ?? '';
                            if ($socialUrl === '') {
                                continue;
                            }
                        ?>
                        <a href="<?php echo $footerEscape($footerUrl($socialUrl)); ?>" target="_blank" rel="noopener" class="<?php echo $footerEscape($social['class'] ?? ''); ?>" title="<?php echo $footerEscape($social['label'] ?? ''); ?>">
                            <i class="<?php echo $footerEscape($social['icon'] ?? 'fas fa-link'); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="footer-section links">
                <?php if (($footer['quick_title'] ?? '') !== ''): ?>
                    <h3><?php echo $footerEscape($footer['quick_title']); ?></h3>
                <?php endif; ?>
                <ul>
                    <?php foreach (($footer['quick_links'] ?? []) as $link): ?>
                        <li><a href="<?php echo $footerEscape($footerUrl($link['url'] ?? '')); ?>"><?php echo $footerEscape($link['label'] ?? ''); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>
    </div>

    <?php if ($copyright !== ''): ?>
        <div class="footer-bottom">
            <p><?php echo $footerEscape($copyright); ?></p>
        </div>
    <?php endif; ?>
</footer>
