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

.landing-canvas-viewport {
    width: 100%;
    overflow: hidden;
    background: #ffffff;
}

.landing-canvas {
    position: relative;
    margin: 0;
    overflow: hidden;
    transform-origin: top left;
}

.landing-canvas-element {
    position: absolute;
    overflow: hidden;
    overflow-wrap: anywhere;
    white-space: pre-wrap;
    line-height: 1.25;
    scroll-margin-top: 90px;
}

.landing-canvas-element-image img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.landing-canvas-element-video iframe {
    display: block;
    width: 100%;
    height: 100%;
    border: 0;
}

.landing-section {
    display: flex;
    flex-direction: column;
    scroll-margin-top: 90px;
    padding: 64px 0;
    border-bottom: 1px solid #e6e6e6;
    overflow: visible;
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
    overflow: visible;
}

.landing-section-container {
    width: min(100%, 960px);
    max-width: 960px;
    gap: 24px;
    align-items: stretch;
}

.landing-blocks {
    gap: 20px;
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
}

.landing-image {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
    margin: 0;
    max-width: 720px;
    width: 100%;
    overflow: visible;
}

.landing-image img {
    display: block;
    width: 100%;
    height: auto;
    max-width: 100%;
    border-radius: 8px;
}

.landing-image figcaption {
    color: #5b5b5b;
    font-size: 0.95rem;
    line-height: 1.5;
    max-width: 100%;
    overflow-wrap: anywhere;
}

.landing-columns {
    display: flex;
    align-items: flex-start;
    max-width: 100%;
    min-width: 0;
}

.landing-column {
    display: flex;
    flex-direction: column;
    min-width: 0;
}

.landing-column-1 {
    flex: 0 1 var(--landing-first-column, 50%);
}

.landing-column-2 {
    flex: 0 1 var(--landing-second-column, 50%);
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

    .landing-columns {
        flex-direction: column;
        gap: 20px !important;
    }

    .landing-column {
        width: 100%;
        flex-basis: auto;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewport = document.querySelector('.landing-canvas-viewport');
    const canvas = document.querySelector('.landing-canvas');

    if (!viewport || !canvas) {
        return;
    }

    const designWidth = parseFloat(canvas.style.width) || canvas.offsetWidth;
    const designHeight = parseFloat(canvas.style.height) || canvas.offsetHeight;

    function fitLandingCanvas() {
        const viewportWidth = viewport.clientWidth || window.innerWidth || designWidth;
        const scale = viewportWidth / designWidth;

        canvas.style.transform = 'scale(' + scale + ')';
        viewport.style.height = Math.ceil(designHeight * scale) + 'px';
    }

    fitLandingCanvas();
    window.addEventListener('resize', fitLandingCanvas);
});
</script>
