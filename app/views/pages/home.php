<?php
$page = isset($data['page']) && is_array($data['page']) ? $data['page'] : ['sections' => []];
renderPage($page);
?>

<style>
.landing-page {
    display: flex;
    flex-direction: column;
    width: 100%;
    background: #ffffff;
    overflow: visible;
}

.landing-page-canvas {
    display: block;
}

.landing-page,
.landing-page * {
    box-sizing: border-box;
}

.landing-section {
    display: flex;
    flex-direction: column;
    scroll-margin-top: 90px;
    padding: 64px 0;
    border-bottom: 1px solid #e6e6e6;
    overflow: visible;
    position: static;
}

.landing-section:first-child {
    padding-top: 80px;
}

.landing-section-container,
.landing-blocks,
.landing-block {
    display: flex;
    flex-direction: column;
    min-width: 0;
    width: 100%;
    overflow: visible;
    position: static;
}

.landing-section-container {
    width: min(100%, 960px);
    max-width: 960px;
    margin: 0 auto;
    padding: 0 20px;
    gap: 24px;
    align-items: stretch;
}

.landing-blocks {
    gap: 20px;
}

.landing-block {
    flex: 0 0 auto;
}

.landing-block:empty {
    display: none;
}

.landing-heading {
    color: #243528;
    font-family: Georgia, serif;
    line-height: 1.2;
    margin: 0;
    max-width: 100%;
    overflow-wrap: anywhere;
}

.landing-heading-1 {
    font-size: 2.7rem;
    font-weight: 400;
}

.landing-heading-2 {
    font-size: 2rem;
    font-weight: 500;
}

.landing-heading-3 {
    font-size: 1.5rem;
    font-weight: 600;
}

.landing-text {
    color: var(--text-color);
    font-size: 1.05rem;
    line-height: 1.75;
    margin: 0;
    max-width: 760px;
    overflow-wrap: anywhere;
    white-space: pre-wrap;
}

.landing-image {
    display: flex;
    flex-direction: column;
    align-items: stretch;
    gap: 10px;
    margin: 0;
    max-width: 720px;
    width: 100%;
    overflow: visible;
    position: static;
}

.landing-image img {
    display: block;
    width: 100%;
    height: auto;
    max-width: 100%;
    border-radius: 8px;
    object-fit: contain;
}

.landing-image figcaption {
    color: #5b5b5b;
    font-size: 0.95rem;
    line-height: 1.5;
    max-width: 100%;
    overflow-wrap: anywhere;
}

.landing-video {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin: 0;
    max-width: 860px;
    width: 100%;
    overflow: visible;
    position: static;
}

.landing-video-frame {
    aspect-ratio: 16 / 9;
    width: 100%;
    border-radius: 8px;
    overflow: hidden;
    background: #111827;
}

.landing-video iframe {
    display: block;
    width: 100%;
    height: 100%;
    border: 0;
}

.landing-video figcaption {
    color: #5b5b5b;
    font-size: 0.95rem;
    line-height: 1.5;
    max-width: 100%;
    overflow-wrap: anywhere;
}

.landing-canvas-viewport {
    width: 100%;
    overflow: hidden;
    background: #ffffff;
}

.landing-canvas {
    position: relative;
    margin: 0 auto;
    overflow: hidden;
    transform-origin: top left;
}

.landing-canvas-element {
    position: absolute;
    display: block;
    overflow: hidden;
    box-sizing: border-box;
    overflow-wrap: anywhere;
    white-space: pre-wrap;
    line-height: 1.25;
    text-decoration: none;
    scroll-margin-top: 90px;
}

.landing-canvas-element-text {
    padding: 8px;
}

.landing-canvas-element-image img,
.landing-canvas-element-video iframe {
    display: block;
    width: 100%;
    height: 100%;
    border: 0;
    object-fit: cover;
}

@media (max-width: 768px) {
    .landing-section {
        padding: 44px 0;
        scroll-margin-top: 82px;
    }

    .landing-section:first-child {
        padding-top: 56px;
    }

    .landing-section-container {
        gap: 20px;
    }

    .landing-heading-1 {
        font-size: 2.1rem;
    }

    .landing-heading-2 {
        font-size: 1.7rem;
    }

    .landing-heading-3 {
        font-size: 1.3rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.landing-canvas-viewport').forEach(function(viewport) {
        const canvas = viewport.querySelector('.landing-canvas');
        if (!canvas) {
            return;
        }

        const designWidth = parseFloat(canvas.style.width) || canvas.offsetWidth;
        const designHeight = parseFloat(canvas.style.height) || canvas.offsetHeight;

        function fitLandingCanvas() {
            const viewportWidth = viewport.clientWidth || window.innerWidth || designWidth;
            const scale = Math.min(1, viewportWidth / designWidth);

            canvas.style.transform = 'scale(' + scale + ')';
            viewport.style.height = Math.ceil(designHeight * scale) + 'px';
        }

        fitLandingCanvas();
        window.addEventListener('resize', fitLandingCanvas);
    });
});
</script>
