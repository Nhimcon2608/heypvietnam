<?php
$pageJson = $data['page_json'] ?? '{}';
$message = $data['message'] ?? '';
$error = $data['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canva Canvas Editor - HeypVietNam Admin</title>
    <link rel="icon" type="image/png" href="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="brand">
                <img src="<?php echo URL_ROOT; ?>/public/img/logoHEYP.png" alt="HeypVietNam">
                <strong>HeypVietNam</strong>
            </div>
            <nav class="admin-nav">
                <a href="<?php echo URL_ROOT; ?>/admin/pages" class="active"><i class="fas fa-object-group"></i> Canvas editor</a>
                <a href="<?php echo URL_ROOT; ?>" target="_blank"><i class="fas fa-house"></i> Xem trang</a>
                <a href="<?php echo URL_ROOT; ?>/admin/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </nav>
        </aside>

        <main class="editor-page">
            <header class="editor-header">
                <div>
                    <p class="eyebrow">Canvas-based landing page builder</p>
                    <h1>Canva-style editor</h1>
                    <p class="editor-note">Drag, resize, overlap, and reorder independent elements on a fixed design board.</p>
                </div>
                <a href="<?php echo URL_ROOT; ?>" target="_blank" class="preview-link">
                    <i class="fas fa-arrow-up-right-from-square"></i> Xem trang
                </a>
            </header>

            <?php if ($message !== ''): ?>
                <div class="alert success"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form id="canvasEditorForm" method="POST" action="<?php echo URL_ROOT; ?>/admin/pages">
                <input type="hidden" id="contentJsonInput" name="content_json">
                <textarea id="pageData" hidden><?php echo htmlspecialchars($pageJson, ENT_QUOTES, 'UTF-8'); ?></textarea>

                <section class="toolbar" aria-label="Canvas toolbar">
                    <button type="button" data-add-element="text"><i class="fas fa-font"></i> Add text</button>
                    <button type="button" data-add-element="image"><i class="fas fa-image"></i> Add image</button>
                    <button type="button" data-add-element="video"><i class="fab fa-youtube"></i> Add YouTube</button>
                    <button type="button" data-add-element="shape"><i class="fas fa-square"></i> Add shape</button>
                    <button type="button" id="bringForward"><i class="fas fa-arrow-up"></i> Bring forward</button>
                    <button type="button" id="sendBackward"><i class="fas fa-arrow-down"></i> Send backward</button>
                    <button type="button" id="deleteElement"><i class="fas fa-trash"></i> Delete</button>
                    <button type="submit" class="save-button"><i class="fas fa-save"></i> Save canvas</button>
                </section>

                <div class="workspace">
                    <section class="canvas-panel">
                        <div class="canvas-scroll">
                            <div id="canvasScaler" class="canvas-scaler">
                                <div id="designCanvas" class="canvas" aria-label="Design canvas"></div>
                            </div>
                        </div>
                    </section>

                    <aside class="inspector">
                        <h2>Element</h2>
                        <p id="emptyInspector" class="muted">Select an element to edit position, size, content, and style.</p>

                        <div id="inspectorFields" class="inspector-fields" hidden>
                            <label>Type
                                <input type="text" id="elementType" readonly>
                            </label>
                            <div class="field-grid">
                                <label>X
                                    <input type="number" id="elementX" data-field="x">
                                </label>
                                <label>Y
                                    <input type="number" id="elementY" data-field="y">
                                </label>
                                <label>Width
                                    <input type="number" id="elementWidth" min="20" data-field="width">
                                </label>
                                <label>Height
                                    <input type="number" id="elementHeight" min="20" data-field="height">
                                </label>
                                <label>Layer
                                    <input type="number" id="elementZ" min="1" data-field="zIndex">
                                </label>
                            </div>

                            <label class="text-field" data-content-field="text">Text content
                                <textarea id="elementContent" data-field="content" rows="4"></textarea>
                            </label>

                            <label class="text-field" data-content-field="media">Image path / YouTube URL
                                <input type="text" id="elementSrc" data-field="src" placeholder="public/img/logoHEYP.png or https://www.youtube.com/watch?v=...">
                            </label>
                            <label class="text-field" data-content-field="image">Upload image
                                <input type="file" id="elementFile" accept="image/*">
                            </label>

                            <div class="field-grid">
                                <label>Font
                                    <select id="elementFontFamily" data-style-field="fontFamily">
                                        <option value="Open Sans">Open Sans</option>
                                        <option value="Montserrat">Montserrat</option>
                                        <option value="Georgia">Georgia</option>
                                        <option value="Arial">Arial</option>
                                        <option value="Times New Roman">Times New Roman</option>
                                    </select>
                                </label>
                                <label>Font size
                                    <input type="number" id="elementFontSize" min="8" max="160" data-style-field="fontSize">
                                </label>
                                <label>Weight
                                    <select id="elementFontWeight" data-style-field="fontWeight">
                                        <option value="400">Regular</option>
                                        <option value="500">Medium</option>
                                        <option value="600">Semi bold</option>
                                        <option value="700">Bold</option>
                                    </select>
                                </label>
                                <label>Text color
                                    <input type="color" id="elementColor" data-style-field="color">
                                </label>
                                <label>Background
                                    <input type="color" id="elementBg" data-style-field="backgroundColor">
                                </label>
                                <label>Radius
                                    <input type="number" id="elementRadius" min="0" max="240" data-style-field="borderRadius">
                                </label>
                            </div>
                        </div>

                        <section class="json-panel">
                            <div class="json-panel-header">
                                <h2>JSON</h2>
                                <button type="button" id="refreshJson">Refresh</button>
                            </div>
                            <pre id="jsonPreview" aria-live="polite"></pre>
                        </section>
                    </aside>
                </div>
            </form>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageData = document.getElementById('pageData');
        const canvasScaler = document.getElementById('canvasScaler');
        const canvas = document.getElementById('designCanvas');
        const canvasScroll = document.querySelector('.canvas-scroll');
        const form = document.getElementById('canvasEditorForm');
        const jsonInput = document.getElementById('contentJsonInput');
        const jsonPreview = document.getElementById('jsonPreview');
        const emptyInspector = document.getElementById('emptyInspector');
        const inspectorFields = document.getElementById('inspectorFields');

        const inspector = {
            type: document.getElementById('elementType'),
            x: document.getElementById('elementX'),
            y: document.getElementById('elementY'),
            width: document.getElementById('elementWidth'),
            height: document.getElementById('elementHeight'),
            zIndex: document.getElementById('elementZ'),
            content: document.getElementById('elementContent'),
            src: document.getElementById('elementSrc'),
            file: document.getElementById('elementFile'),
            fontFamily: document.getElementById('elementFontFamily'),
            fontSize: document.getElementById('elementFontSize'),
            fontWeight: document.getElementById('elementFontWeight'),
            color: document.getElementById('elementColor'),
            backgroundColor: document.getElementById('elementBg'),
            borderRadius: document.getElementById('elementRadius')
        };

        const minCanvasHeight = 760;
        const maxCanvasHeight = 10000;
        const canvasBottomPadding = 180;
        let elements = [];
        let page = parsePage(pageData.value);
        let canvasSettings = page.canvas;
        elements = page.elements;
        let selectedId = elements[0] ? elements[0].id : null;
        let interaction = null;
        let canvasZoom = 1;

        function parsePage(json) {
            try {
                const parsed = JSON.parse(json || '{}');
                if (parsed && typeof parsed === 'object') {
                    return normalizePage(parsed);
                }
            } catch (error) {
                console.error(error);
            }

            return normalizePage({});
        }

        function normalizePage(raw) {
            const normalized = {
                header: raw.header && typeof raw.header === 'object' ? raw.header : { logo: 'public/img/logoHEYP.png' },
                layoutType: 'canvas',
                canvas: normalizeCanvas(raw.canvas),
                elements: []
            };

            if (Array.isArray(raw.elements)) {
                normalized.elements = raw.elements.map(normalizeElement).filter(Boolean);
            } else {
                normalized.elements = flattenSectionsToElements(raw.sections || []);
            }

            if (!normalized.elements.length) {
                normalized.elements = defaultElements();
            }

            normalized.elements.sort((a, b) => a.zIndex - b.zIndex);
            return normalized;
        }

        function normalizeCanvas(canvasData) {
            canvasData = canvasData && typeof canvasData === 'object' ? canvasData : {};

            return {
                width: clampNumber(canvasData.width, 320, 2400, 1200),
                height: clampNumber(canvasData.height, 320, maxCanvasHeight, minCanvasHeight),
                backgroundColor: normalizeColor(canvasData.backgroundColor, '#ffffff')
            };
        }

        function flattenSectionsToElements(sections) {
            const output = [];
            let y = 80;
            let z = 1;

            (Array.isArray(sections) ? sections : []).forEach(section => {
                if (section.heading && section.heading.text) {
                    output.push(newElement('text', {
                        x: 80,
                        y: y,
                        width: 780,
                        height: 74,
                        zIndex: z++,
                        content: section.heading.text,
                        style: {
                            fontFamily: 'Montserrat',
                            fontSize: 42,
                            fontWeight: '700',
                            color: '#243528',
                            backgroundColor: 'transparent',
                            borderRadius: 0
                        }
                    }));
                    y += 96;
                }

                (section.blocks || []).forEach(block => {
                    if (block.type === 'image') {
                        output.push(newElement('image', {
                            x: 80,
                            y: y,
                            width: 360,
                            height: 240,
                            zIndex: z++,
                            src: block.src || block.url || block.image_url || '',
                            content: block.alt || ''
                        }));
                        y += 280;
                    } else {
                        output.push(newElement('text', {
                            x: 80,
                            y: y,
                            width: 760,
                            height: 90,
                            zIndex: z++,
                            content: block.content || block.text || ''
                        }));
                        y += 120;
                    }
                });
            });

            return output;
        }

        function defaultElements() {
            return [
                newElement('text', {
                    x: 90,
                    y: 92,
                    width: 650,
                    height: 130,
                    zIndex: 1,
                    content: 'Sản phẩm xanh cho lối sống bền vững',
                    style: {
                        fontFamily: 'Montserrat',
                        fontSize: 46,
                        fontWeight: '700',
                        color: '#243528',
                        backgroundColor: 'transparent',
                        borderRadius: 0
                    }
                }),
                newElement('text', {
                    x: 92,
                    y: 250,
                    width: 520,
                    height: 120,
                    zIndex: 2,
                    content: 'Kéo thả, resize và chồng lớp các element giống Canva hoặc PowerPoint.'
                }),
                newElement('image', {
                    x: 760,
                    y: 130,
                    width: 300,
                    height: 300,
                    zIndex: 3,
                    src: 'public/img/logoHEYP.png',
                    content: 'Heyp Logo'
                })
            ];
        }

        function normalizeElement(element) {
            if (!element || typeof element !== 'object') {
                return null;
            }

            const type = ['text', 'image', 'video', 'shape'].includes(element.type) ? element.type : 'text';
            return newElement(type, element);
        }

        function newElement(type, overrides = {}) {
            const defaultWidth = type === 'text' ? 320 : (type === 'video' ? 480 : 240);
            const defaultHeight = type === 'text' ? 100 : (type === 'video' ? 270 : 180);

            const base = {
                id: overrides.id || createId(),
                type: type,
                x: clampNumber(overrides.x, 0, 2400, 120),
                y: clampNumber(overrides.y, 0, 3200, 120),
                width: clampNumber(overrides.width, 20, 2400, defaultWidth),
                height: clampNumber(overrides.height, 20, 3200, defaultHeight),
                zIndex: clampNumber(
                    overrides.zIndex !== undefined ? overrides.zIndex : nextZIndex(),
                    1,
                    9999,
                    1
                ),
                content: String(overrides.content || (type === 'text' ? 'New text' : (type === 'video' ? 'YouTube video' : ''))),
                src: String(overrides.src || (type === 'image' ? 'public/img/logoHEYP.png' : '')),
                style: normalizeStyle(overrides.style || {}, type)
            };

            return base;
        }

        function normalizeStyle(style, type) {
            style = style && typeof style === 'object' ? style : {};

            return {
                fontFamily: choice(style.fontFamily, ['Open Sans', 'Montserrat', 'Georgia', 'Arial', 'Times New Roman'], 'Open Sans'),
                fontSize: clampNumber(style.fontSize, 8, 160, type === 'text' ? 24 : 18),
                fontWeight: choice(String(style.fontWeight || '400'), ['400', '500', '600', '700'], '400'),
                color: normalizeColor(style.color, '#1f2937'),
                backgroundColor: normalizeColor(style.backgroundColor, type === 'shape' ? '#d9f99d' : (type === 'video' ? '#111827' : 'transparent')),
                borderRadius: clampNumber(style.borderRadius, 0, 240, (type === 'image' || type === 'video') ? 8 : 0)
            };
        }

        function createId() {
            if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                return window.crypto.randomUUID();
            }

            return 'el-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 10);
        }

        function choice(value, allowed, fallback) {
            return allowed.includes(value) ? value : fallback;
        }

        function clampNumber(value, min, max, fallback) {
            const number = Number(value);
            if (!Number.isFinite(number)) {
                return fallback;
            }

            return Math.max(min, Math.min(max, Math.round(number)));
        }

        function normalizeColor(value, fallback) {
            value = String(value || '').trim();
            if (value === 'transparent') {
                return value;
            }

            return /^#[0-9a-fA-F]{6}$/.test(value) ? value : fallback;
        }

        function nextZIndex() {
            return elements.length ? Math.max(...elements.map(element => element.zIndex)) + 1 : 1;
        }

        function getSelectedElement() {
            return elements.find(element => element.id === selectedId) || null;
        }

        function renderCanvas() {
            expandCanvasToElements();
            canvas.style.width = canvasSettings.width + 'px';
            canvas.style.height = canvasSettings.height + 'px';
            canvas.style.backgroundColor = canvasSettings.backgroundColor;
            updateCanvasZoom();
            canvas.innerHTML = '';

            elements
                .slice()
                .sort((a, b) => a.zIndex - b.zIndex)
                .forEach(element => canvas.appendChild(renderElement(element)));

            syncInspector();
            syncJsonPreview();
        }

        function updateCanvasZoom() {
            const availableWidth = Math.max(260, canvasScroll.clientWidth - 48);
            const widthZoom = availableWidth / canvasSettings.width;

            canvasZoom = Math.min(1, widthZoom);
            canvasScaler.style.width = Math.round(canvasSettings.width * canvasZoom) + 'px';
            canvasScaler.style.height = Math.round(canvasSettings.height * canvasZoom) + 'px';
            canvas.style.transform = 'scale(' + canvasZoom + ')';
        }

        function expandCanvasToElements() {
            const requiredHeight = elements.reduce((height, element) => {
                return Math.max(height, element.y + element.height + canvasBottomPadding);
            }, minCanvasHeight);

            canvasSettings.height = clampNumber(
                Math.max(canvasSettings.height, requiredHeight),
                320,
                maxCanvasHeight,
                minCanvasHeight
            );
        }

        function expandCanvasToElement(element) {
            const requiredHeight = element.y + element.height + canvasBottomPadding;
            if (requiredHeight <= canvasSettings.height) {
                return;
            }

            canvasSettings.height = clampNumber(requiredHeight, 320, maxCanvasHeight, canvasSettings.height);
            canvas.style.height = canvasSettings.height + 'px';
            updateCanvasZoom();
        }

        function renderElement(element) {
            const node = document.createElement('div');
            node.className = 'canvas-element canvas-element-' + element.type;
            node.dataset.id = element.id;
            node.style.left = element.x + 'px';
            node.style.top = element.y + 'px';
            node.style.width = element.width + 'px';
            node.style.height = element.height + 'px';
            node.style.zIndex = element.zIndex;
            node.style.fontFamily = fontStack(element.style.fontFamily);
            node.style.fontSize = element.style.fontSize + 'px';
            node.style.fontWeight = element.style.fontWeight;
            node.style.color = element.style.color;
            node.style.backgroundColor = element.style.backgroundColor;
            node.style.borderRadius = element.style.borderRadius + 'px';
            node.classList.toggle('selected', element.id === selectedId);

            if (element.type === 'image') {
                const image = document.createElement('img');
                image.src = resolveAssetUrl(element.src);
                image.alt = element.content || '';
                node.appendChild(image);
            } else if (element.type === 'video') {
                const embedUrl = toYouTubeEmbedUrl(element.src);
                if (embedUrl !== '') {
                    const iframe = document.createElement('iframe');
                    iframe.src = embedUrl;
                    iframe.title = element.content || 'YouTube video';
                    iframe.loading = 'lazy';
                    iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
                    iframe.allowFullscreen = true;
                    node.appendChild(iframe);
                } else {
                    const placeholder = document.createElement('div');
                    placeholder.className = 'canvas-video-placeholder';
                    placeholder.innerHTML = '<i class="fab fa-youtube"></i><span>Paste YouTube URL in the inspector</span>';
                    node.appendChild(placeholder);
                }
            } else if (element.type === 'shape') {
                node.setAttribute('aria-label', 'Shape element');
            } else {
                node.contentEditable = 'true';
                node.textContent = element.content;
                node.addEventListener('input', () => {
                    element.content = node.textContent;
                    syncInspector();
                    syncJsonPreview();
                });
            }

            node.addEventListener('pointerdown', event => startDrag(event, element.id));
            node.addEventListener('click', event => {
                event.stopPropagation();
                selectElement(element.id);
            });

            if (element.id === selectedId) {
                addMoveHandle(node, element);
                addResizeHandles(node, element);
            }

            return node;
        }

        function addMoveHandle(node, element) {
            const handle = document.createElement('span');
            handle.className = 'move-handle';
            handle.contentEditable = 'false';
            handle.innerHTML = '<i class="fas fa-arrows-up-down-left-right"></i>';
            handle.addEventListener('pointerdown', event => startDrag(event, element.id));
            node.appendChild(handle);
        }

        function addResizeHandles(node, element) {
            ['nw', 'ne', 'sw', 'se'].forEach(handle => {
                const button = document.createElement('span');
                button.className = 'resize-handle resize-' + handle;
                button.contentEditable = 'false';
                button.dataset.handle = handle;
                button.addEventListener('pointerdown', event => startResize(event, element.id, handle));
                node.appendChild(button);
            });
        }

        function startDrag(event, id) {
            if (event.target.classList.contains('resize-handle')) {
                return;
            }
            if (event.target.isContentEditable && selectedId === id) {
                return;
            }

            selectElement(id);
            const element = getSelectedElement();
            if (!element) {
                return;
            }

            interaction = {
                mode: 'drag',
                id: id,
                startX: event.clientX,
                startY: event.clientY,
                originalX: element.x,
                originalY: element.y
            };
            event.preventDefault();
        }

        function startResize(event, id, handle) {
            selectElement(id);
            const element = getSelectedElement();
            if (!element) {
                return;
            }

            interaction = {
                mode: 'resize',
                id: id,
                handle: handle,
                startX: event.clientX,
                startY: event.clientY,
                originalX: element.x,
                originalY: element.y,
                originalWidth: element.width,
                originalHeight: element.height
            };
            event.stopPropagation();
            event.preventDefault();
        }

        function handlePointerMove(event) {
            if (!interaction) {
                return;
            }

            const element = elements.find(item => item.id === interaction.id);
            if (!element) {
                return;
            }

            const dx = (event.clientX - interaction.startX) / canvasZoom;
            const dy = (event.clientY - interaction.startY) / canvasZoom;

            if (interaction.mode === 'drag') {
                element.x = clampNumber(interaction.originalX + dx, 0, canvasSettings.width - element.width, element.x);
                element.y = clampNumber(interaction.originalY + dy, 0, maxCanvasHeight - element.height, element.y);
                expandCanvasToElement(element);
            }

            if (interaction.mode === 'resize') {
                resizeElement(element, dx, dy);
                expandCanvasToElement(element);
            }

            updateElementNode(element);
            syncInspector();
            syncJsonPreview();
        }

        function resizeElement(element, dx, dy) {
            const minSize = 20;
            let x = interaction.originalX;
            let y = interaction.originalY;
            let width = interaction.originalWidth;
            let height = interaction.originalHeight;

            if (interaction.handle.includes('e')) {
                width = interaction.originalWidth + dx;
            }
            if (interaction.handle.includes('s')) {
                height = interaction.originalHeight + dy;
            }
            if (interaction.handle.includes('w')) {
                width = interaction.originalWidth - dx;
                x = interaction.originalX + dx;
            }
            if (interaction.handle.includes('n')) {
                height = interaction.originalHeight - dy;
                y = interaction.originalY + dy;
            }

            element.width = clampNumber(width, minSize, canvasSettings.width, element.width);
            element.height = clampNumber(height, minSize, maxCanvasHeight - y, element.height);
            element.x = clampNumber(x, 0, canvasSettings.width - element.width, element.x);
            element.y = clampNumber(y, 0, maxCanvasHeight - element.height, element.y);
        }

        function updateElementNode(element) {
            const node = canvas.querySelector('[data-id="' + element.id + '"]');
            if (!node) {
                return;
            }

            node.style.left = element.x + 'px';
            node.style.top = element.y + 'px';
            node.style.width = element.width + 'px';
            node.style.height = element.height + 'px';
            node.style.zIndex = element.zIndex;
        }

        function stopInteraction() {
            interaction = null;
        }

        function selectElement(id) {
            if (selectedId === id) {
                syncInspector();
                return;
            }

            selectedId = id;
            renderCanvas();
        }

        function addElement(type) {
            const element = newElement(type, {
                x: 120,
                y: 120,
                zIndex: nextZIndex()
            });

            elements.push(element);
            selectedId = element.id;
            renderCanvas();
        }

        function deleteSelectedElement() {
            if (!selectedId) {
                return;
            }

            elements = elements.filter(element => element.id !== selectedId);
            selectedId = elements[0] ? elements[0].id : null;
            renderCanvas();
        }

        function reorderLayer(direction) {
            const element = getSelectedElement();
            if (!element) {
                return;
            }

            element.zIndex = direction === 'forward'
                ? element.zIndex + 1
                : Math.max(1, element.zIndex - 1);
            normalizeZIndexes();
            renderCanvas();
        }

        function normalizeZIndexes() {
            elements
                .slice()
                .sort((a, b) => a.zIndex - b.zIndex)
                .forEach((element, index) => {
                    element.zIndex = index + 1;
                });
        }

        function updateSelectedElement(field, value) {
            const element = getSelectedElement();
            if (!element) {
                return;
            }

            if (['x', 'y', 'width', 'height', 'zIndex'].includes(field)) {
                const ranges = {
                    x: [0, Math.max(0, canvasSettings.width - element.width)],
                    y: [0, Math.max(0, canvasSettings.height - element.height)],
                    width: [20, canvasSettings.width],
                    height: [20, canvasSettings.height],
                    zIndex: [1, 9999]
                };
                element[field] = clampNumber(value, ranges[field][0], ranges[field][1], element[field]);
            } else {
                element[field] = String(value || '');
                if (field === 'src' && element.type === 'image' && toYouTubeEmbedUrl(element[field]) !== '') {
                    element.type = 'video';
                    element.style = normalizeStyle(element.style, element.type);
                }
            }

            renderCanvas();
        }

        function updateSelectedStyle(field, value) {
            const element = getSelectedElement();
            if (!element) {
                return;
            }

            element.style[field] = value;
            element.style = normalizeStyle(element.style, element.type);
            renderCanvas();
        }

        function syncInspector() {
            const element = getSelectedElement();
            emptyInspector.hidden = !!element;
            inspectorFields.hidden = !element;

            if (!element) {
                return;
            }

            inspector.type.value = element.type;
            inspector.x.value = element.x;
            inspector.y.value = element.y;
            inspector.width.value = element.width;
            inspector.height.value = element.height;
            inspector.zIndex.value = element.zIndex;
            inspector.content.value = element.content || '';
            inspector.src.value = element.src || '';
            inspector.fontFamily.value = element.style.fontFamily;
            inspector.fontSize.value = element.style.fontSize;
            inspector.fontWeight.value = element.style.fontWeight;
            inspector.color.value = element.style.color === 'transparent' ? '#000000' : element.style.color;
            inspector.backgroundColor.value = element.style.backgroundColor === 'transparent' ? '#ffffff' : element.style.backgroundColor;
            inspector.borderRadius.value = element.style.borderRadius;

            document.querySelectorAll('[data-content-field="text"]').forEach(field => {
                field.hidden = element.type !== 'text';
            });
            document.querySelectorAll('[data-content-field="image"]').forEach(field => {
                field.hidden = element.type !== 'image';
            });
            document.querySelectorAll('[data-content-field="media"]').forEach(field => {
                field.hidden = !['image', 'video'].includes(element.type);
            });
        }

        function buildPage() {
            normalizeZIndexes();
            expandCanvasToElements();

            return {
                header: page.header || { logo: 'public/img/logoHEYP.png' },
                layoutType: 'canvas',
                canvas: canvasSettings,
                elements: elements
            };
        }

        function syncJsonPreview() {
            const json = JSON.stringify(buildPage(), null, 2);
            jsonInput.value = json;
            jsonPreview.textContent = json;
        }

        function resolveAssetUrl(src) {
            src = String(src || '').trim();
            if (/^(https?:)?\/\//.test(src) || src.startsWith('data:')) {
                return src;
            }
            if (src.startsWith('/')) {
                return src;
            }
            return '<?php echo URL_ROOT; ?>/' + src.replace(/^\/+/, '');
        }

        function toYouTubeEmbedUrl(src) {
            src = String(src || '').trim();
            if (src === '') {
                return '';
            }
            if (/^(www\.)?(youtube\.com|youtu\.be)\//i.test(src)) {
                src = 'https://' + src;
            }

            try {
                const url = new URL(src, window.location.origin);
                const hostname = url.hostname.replace(/^www\./, '');
                let videoId = '';

                if (hostname === 'youtu.be') {
                    videoId = url.pathname.split('/').filter(Boolean)[0] || '';
                }

                if (hostname === 'youtube.com' || hostname === 'm.youtube.com' || hostname === 'youtube-nocookie.com') {
                    if (url.pathname === '/watch') {
                        videoId = url.searchParams.get('v') || '';
                    } else {
                        const parts = url.pathname.split('/').filter(Boolean);
                        if (['embed', 'shorts', 'live'].includes(parts[0])) {
                            videoId = parts[1] || '';
                        }
                    }
                }

                if (!/^[a-zA-Z0-9_-]{6,}$/.test(videoId)) {
                    return '';
                }

                const embedUrl = new URL('https://www.youtube.com/embed/' + videoId);
                const start = url.searchParams.get('start') || url.searchParams.get('t');
                if (start) {
                    embedUrl.searchParams.set('start', String(start).replace(/\D/g, ''));
                }

                return embedUrl.toString();
            } catch (error) {
                return '';
            }
        }

        function fontStack(fontFamily) {
            const stacks = {
                'Open Sans': "'Open Sans', Arial, sans-serif",
                'Montserrat': "Montserrat, Arial, sans-serif",
                'Georgia': 'Georgia, serif',
                'Arial': 'Arial, sans-serif',
                'Times New Roman': "'Times New Roman', serif"
            };

            return stacks[fontFamily] || stacks['Open Sans'];
        }

        document.querySelectorAll('[data-add-element]').forEach(button => {
            button.addEventListener('click', () => addElement(button.dataset.addElement));
        });

        document.querySelectorAll('[data-field]').forEach(input => {
            input.addEventListener('input', () => updateSelectedElement(input.dataset.field, input.value));
        });

        document.querySelectorAll('[data-style-field]').forEach(input => {
            input.addEventListener('input', () => updateSelectedStyle(input.dataset.styleField, input.value));
        });

        document.getElementById('bringForward').addEventListener('click', () => reorderLayer('forward'));
        document.getElementById('sendBackward').addEventListener('click', () => reorderLayer('backward'));
        document.getElementById('deleteElement').addEventListener('click', deleteSelectedElement);
        document.getElementById('refreshJson').addEventListener('click', syncJsonPreview);
        inspector.file.addEventListener('change', () => {
            const file = inspector.file.files && inspector.file.files[0];
            const element = getSelectedElement();
            if (!file || !element || element.type !== 'image') {
                return;
            }

            const reader = new FileReader();
            reader.addEventListener('load', () => {
                element.src = reader.result;
                renderCanvas();
            });
            reader.readAsDataURL(file);
        });
        canvas.addEventListener('click', () => {
            selectedId = null;
            renderCanvas();
        });
        window.addEventListener('pointermove', handlePointerMove);
        window.addEventListener('pointerup', stopInteraction);
        window.addEventListener('resize', updateCanvasZoom);
        form.addEventListener('submit', syncJsonPreview);

        renderCanvas();
    });
    </script>

    <style>
    :root {
        --bg: #f5f7f5;
        --panel: #ffffff;
        --border: #dfe5dc;
        --text: #1f2937;
        --muted: #6b7280;
        --green: #2d5a27;
        --green-soft: #e8f1e6;
        --danger: #c0392b;
    }

    * {
        box-sizing: border-box;
    }

    [hidden] {
        display: none !important;
    }

    body {
        margin: 0;
        background: var(--bg);
        color: var(--text);
        font-family: 'Open Sans', Arial, sans-serif;
    }

    .admin-shell {
        display: flex;
        min-height: 100vh;
    }

    .admin-sidebar {
        width: 250px;
        background: #1f2937;
        color: #ffffff;
        padding: 24px 18px;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .brand,
    .admin-nav,
    .editor-page,
    #canvasEditorForm,
    .inspector,
    .inspector-fields,
    .json-panel {
        display: flex;
        flex-direction: column;
    }

    .brand {
        align-items: center;
        flex-direction: row;
        gap: 12px;
        font-family: Montserrat, sans-serif;
    }

    .brand img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
    }

    .admin-nav {
        gap: 8px;
    }

    .admin-nav a {
        color: #ffffff;
        text-decoration: none;
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 12px 14px;
        border-radius: 8px;
    }

    .admin-nav a:hover,
    .admin-nav a.active {
        background: rgba(255, 255, 255, 0.12);
    }

    .editor-page {
        flex: 1;
        gap: 20px;
        padding: 28px;
        min-width: 0;
    }

    .editor-header,
    .toolbar,
    .workspace,
    .field-grid,
    .json-panel-header {
        display: flex;
    }

    .editor-header {
        justify-content: space-between;
        gap: 20px;
        align-items: flex-start;
    }

    .editor-header h1,
    .inspector h2,
    .json-panel h2 {
        margin: 0;
        font-family: Montserrat, sans-serif;
        color: #1b3b36;
    }

    .eyebrow,
    .editor-note,
    .muted {
        margin: 0 0 8px;
        color: var(--muted);
    }

    #canvasEditorForm {
        gap: 16px;
        min-width: 0;
    }

    .preview-link,
    .toolbar button,
    .json-panel button {
        border: 1px solid var(--border);
        background: #ffffff;
        color: var(--text);
        border-radius: 8px;
        padding: 10px 12px;
        text-decoration: none;
        cursor: pointer;
    }

    .toolbar {
        flex-wrap: wrap;
        gap: 10px;
        padding: 14px;
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 8px;
    }

    .toolbar .save-button {
        margin-left: auto;
        background: var(--green);
        color: #ffffff;
        border-color: var(--green);
    }

    .alert {
        padding: 12px 14px;
        border-radius: 8px;
        border: 1px solid var(--border);
        background: var(--panel);
    }

    .alert.success {
        color: var(--green);
        background: var(--green-soft);
    }

    .alert.error {
        color: var(--danger);
        background: #fff0ee;
    }

    .workspace {
        gap: 18px;
        align-items: flex-start;
        min-width: 0;
    }

    .canvas-panel {
        flex: 1;
        min-width: 0;
        background: #d8ddd4;
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 22px;
    }

    .canvas-scroll {
        overflow: auto;
        max-height: calc(100vh - 220px);
        padding: 24px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
    }

    .canvas-scaler {
        flex: 0 0 auto;
        position: relative;
    }

    .canvas {
        position: relative;
        margin: 0;
        background: #ffffff;
        border: 1px solid #cfd6cc;
        box-shadow: 0 16px 50px rgba(31, 41, 55, 0.18);
        overflow: hidden;
        transform-origin: top left;
    }

    .canvas-element {
        position: absolute;
        border: 1px solid transparent;
        min-width: 20px;
        min-height: 20px;
        overflow: hidden;
        cursor: move;
        user-select: none;
    }

    .canvas-element.selected {
        border-color: #2563eb;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.18);
        overflow: visible;
    }

    .canvas-element-text {
        padding: 8px;
        line-height: 1.25;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        user-select: text;
    }

    .canvas-element-image img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
        pointer-events: none;
    }

    .canvas-element-video iframe {
        display: block;
        width: 100%;
        height: 100%;
        border: 0;
        pointer-events: none;
    }

    .canvas-video-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #111827;
        color: #ffffff;
        text-align: center;
        padding: 16px;
    }

    .canvas-video-placeholder i {
        color: #ff0000;
        font-size: 2rem;
    }

    .resize-handle {
        position: absolute;
        width: 12px;
        height: 12px;
        background: #2563eb;
        border: 2px solid #ffffff;
        border-radius: 50%;
        z-index: 10000;
    }

    .move-handle {
        position: absolute;
        left: 50%;
        top: -32px;
        transform: translateX(-50%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: #2563eb;
        color: #ffffff;
        border-radius: 8px;
        cursor: move;
        z-index: 10001;
    }

    .resize-nw {
        top: -7px;
        left: -7px;
        cursor: nwse-resize;
    }

    .resize-ne {
        top: -7px;
        right: -7px;
        cursor: nesw-resize;
    }

    .resize-sw {
        bottom: -7px;
        left: -7px;
        cursor: nesw-resize;
    }

    .resize-se {
        right: -7px;
        bottom: -7px;
        cursor: nwse-resize;
    }

    .inspector {
        width: 330px;
        gap: 14px;
        background: var(--panel);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 18px;
    }

    .inspector-fields {
        gap: 12px;
    }

    .field-grid {
        flex-wrap: wrap;
        gap: 10px;
    }

    .field-grid label {
        flex: 1 1 130px;
    }

    label {
        display: flex;
        flex-direction: column;
        gap: 6px;
        color: var(--muted);
        font-size: 0.9rem;
    }

    input,
    select,
    textarea {
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 9px 10px;
        font: inherit;
        color: var(--text);
        background: #ffffff;
    }

    textarea {
        resize: vertical;
    }

    .json-panel {
        gap: 12px;
        margin-top: 8px;
    }

    .json-panel-header {
        justify-content: space-between;
        align-items: center;
        gap: 12px;
    }

    #jsonPreview {
        margin: 0;
        padding: 12px;
        max-height: 320px;
        overflow: auto;
        background: #111827;
        color: #e5e7eb;
        border-radius: 8px;
        line-height: 1.5;
        font-size: 0.82rem;
    }

    @media (max-width: 1100px) {
        .admin-shell,
        .workspace,
        .editor-header {
            flex-direction: column;
        }

        .admin-sidebar,
        .inspector {
            width: 100%;
        }

        .editor-page {
            padding: 18px;
        }

        .toolbar .save-button {
            margin-left: 0;
        }
    }
    </style>
</body>
</html>
