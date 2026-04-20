<?php
$pageJson = $data['page_json'] ?? '{}';
$defaultElementsJson = $data['default_elements_json'] ?? '[]';
$editorTitle = $data['editorTitle'] ?? 'Canva Canvas Editor - HeypVietNam Admin';
$editorEyebrow = $data['editorEyebrow'] ?? 'Canvas-based landing page builder';
$editorHeading = $data['editorHeading'] ?? 'Canva-style editor';
$editorNote = $data['editorNote'] ?? 'Drag, resize, overlap, and reorder independent elements on a fixed design board.';
$formAction = $data['formAction'] ?? (URL_ROOT . '/admin/pages');
$activeEditor = $data['activeEditor'] ?? 'pages';
$saveButtonLabel = $data['saveButtonLabel'] ?? 'Save canvas';
$canvasDefaults = $data['canvasDefaults'] ?? [];
$canvasDefaultWidth = isset($canvasDefaults['width']) ? (int) $canvasDefaults['width'] : 1200;
$canvasDefaultHeight = isset($canvasDefaults['height']) ? (int) $canvasDefaults['height'] : 760;
$canvasDefaultMinHeight = isset($canvasDefaults['minHeight']) ? (int) $canvasDefaults['minHeight'] : 760;
$canvasDefaultMaxHeight = isset($canvasDefaults['maxHeight']) ? (int) $canvasDefaults['maxHeight'] : 10000;
$canvasDefaultBottomPadding = isset($canvasDefaults['bottomPadding']) ? (int) $canvasDefaults['bottomPadding'] : 180;
$canvasDefaultBackgroundColor = $canvasDefaults['backgroundColor'] ?? '#ffffff';
$canvasDefaultAutoFitHeight = !empty($canvasDefaults['autoFitHeight']);
$message = $data['message'] ?? '';
$error = $data['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($editorTitle, ENT_QUOTES, 'UTF-8'); ?></title>
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
                <a href="<?php echo URL_ROOT; ?>/admin/pages" class="<?php echo $activeEditor === 'pages' ? 'active' : ''; ?>"><i class="fas fa-object-group"></i> Canvas editor</a>
                <a href="<?php echo URL_ROOT; ?>/admin/footer" class="<?php echo $activeEditor === 'footer' ? 'active' : ''; ?>"><i class="fas fa-window-restore"></i> Footer</a>
                <a href="<?php echo URL_ROOT; ?>" target="_blank"><i class="fas fa-house"></i> Xem trang</a>
                <a href="<?php echo URL_ROOT; ?>/admin/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </nav>
        </aside>

        <main class="editor-page">
            <?php if ($message !== ''): ?>
                <div class="alert success toast-alert" data-auto-dismiss="3000" role="status">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form id="canvasEditorForm" method="POST" action="<?php echo htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" id="contentJsonInput" name="content_json">
                <textarea id="pageData" hidden><?php echo htmlspecialchars($pageJson, ENT_QUOTES, 'UTF-8'); ?></textarea>
                <textarea id="defaultElementsData" hidden><?php echo htmlspecialchars($defaultElementsJson, ENT_QUOTES, 'UTF-8'); ?></textarea>

                <section class="toolbar" aria-label="Canvas toolbar">
                    <button type="button" data-add-element="text"><i class="fas fa-font"></i> Add text</button>
                    <button type="button" data-add-element="image"><i class="fas fa-image"></i> Add image</button>
                    <button type="button" data-add-element="video"><i class="fab fa-youtube"></i> Add YouTube</button>
                    <button type="button" data-add-element="shape"><i class="fas fa-square"></i> Add shape</button>
                    <button type="button" id="bringForward"><i class="fas fa-arrow-up"></i> Bring forward</button>
                    <button type="button" id="sendBackward"><i class="fas fa-arrow-down"></i> Send backward</button>
                    <button type="button" id="deleteElement"><i class="fas fa-trash"></i> Delete</button>
                    <button type="submit" class="save-button"><i class="fas fa-save"></i> <?php echo htmlspecialchars($saveButtonLabel, ENT_QUOTES, 'UTF-8'); ?></button>
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
                            <label data-heading-control>Heading / header
                                <select id="elementHeadingLevel" data-field="headingLevel">
                                    <option value="0">Auto</option>
                                    <option value="1">H1 - show on header</option>
                                    <option value="2">H2 - content only</option>
                                    <option value="3">H3 - content only</option>
                                </select>
                                <span id="headerNavHint" class="header-nav-hint"></span>
                            </label>
                            <label data-header-label-control>Header text
                                <input type="text" id="elementNavLabel" data-field="navLabel" placeholder="Leave blank to use text content">
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

                            <div class="inline-style-toolbar" data-content-field="text" aria-label="Inline text style">
                                <button type="button" id="textBoldButton" data-inline-style="bold" title="Bold (Ctrl/Cmd+B)"><strong>B</strong></button>
                                <button type="button" id="textItalicButton" data-inline-style="italic" title="Italic (Ctrl/Cmd+I)"><em>I</em></button>
                                <button type="button" id="textUnderlineButton" data-inline-style="underline" title="Underline (Ctrl/Cmd+U)"><u>U</u></button>
                                <span>Highlight text first to style only that part.</span>
                            </div>

                            <label class="text-field" data-content-field="media">Image path / YouTube URL
                                <input type="text" id="elementSrc" data-field="src" placeholder="public/img/logoHEYP.png or https://www.youtube.com/watch?v=...">
                            </label>
                            <label class="text-field">Link URL
                                <input type="text" id="elementHref" data-field="href" placeholder="/contact or https://...">
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
        const defaultElementsData = document.getElementById('defaultElementsData');
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
            headingLevel: document.getElementById('elementHeadingLevel'),
            headerNavHint: document.getElementById('headerNavHint'),
            navLabel: document.getElementById('elementNavLabel'),
            x: document.getElementById('elementX'),
            y: document.getElementById('elementY'),
            width: document.getElementById('elementWidth'),
            height: document.getElementById('elementHeight'),
            zIndex: document.getElementById('elementZ'),
            content: document.getElementById('elementContent'),
            src: document.getElementById('elementSrc'),
            href: document.getElementById('elementHref'),
            file: document.getElementById('elementFile'),
            fontFamily: document.getElementById('elementFontFamily'),
            fontSize: document.getElementById('elementFontSize'),
            fontWeight: document.getElementById('elementFontWeight'),
            boldButton: document.getElementById('textBoldButton'),
            italicButton: document.getElementById('textItalicButton'),
            underlineButton: document.getElementById('textUnderlineButton'),
            color: document.getElementById('elementColor'),
            backgroundColor: document.getElementById('elementBg'),
            borderRadius: document.getElementById('elementRadius')
        };

        const editorCanvasDefaults = {
            width: <?php echo $canvasDefaultWidth; ?>,
            height: <?php echo $canvasDefaultHeight; ?>,
            minHeight: <?php echo $canvasDefaultMinHeight; ?>,
            maxHeight: <?php echo $canvasDefaultMaxHeight; ?>,
            bottomPadding: <?php echo $canvasDefaultBottomPadding; ?>,
            backgroundColor: <?php echo json_encode($canvasDefaultBackgroundColor); ?>,
            autoFitHeight: <?php echo $canvasDefaultAutoFitHeight ? 'true' : 'false'; ?>
        };
        const minCanvasHeight = editorCanvasDefaults.minHeight;
        const maxCanvasHeight = editorCanvasDefaults.maxHeight;
        const canvasBottomPadding = editorCanvasDefaults.bottomPadding;
        let elements = [];
        let page = parsePage(pageData.value);
        let canvasSettings = page.canvas;
        elements = page.elements;
        let selectedId = elements[0] ? elements[0].id : null;
        let selectedIds = selectedId ? [selectedId] : [];
        let interaction = null;
        let suppressCanvasClick = false;
        let canvasZoom = 1;
        let elementClipboard = [];
        let historyStack = [];
        let redoStack = [];
        let historyTimer = null;
        let isRestoringHistory = false;
        let savedTextRange = null;
        let savedTextElementId = null;
        let lastPointerPosition = null;
        let autoScrollFrame = null;
        const autoScrollEdgeSize = 72;
        const autoScrollMaxSpeed = 28;

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
                width: clampNumber(canvasData.width, 320, 2400, editorCanvasDefaults.width),
                height: clampNumber(canvasData.height, minCanvasHeight, maxCanvasHeight, editorCanvasDefaults.height),
                backgroundColor: normalizeColor(canvasData.backgroundColor, editorCanvasDefaults.backgroundColor)
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
                        headingLevel: clampNumber(section.heading.level, 0, 3, 1),
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
                    } else if (block.type === 'video') {
                        output.push(newElement('video', {
                            x: 80,
                            y: y,
                            width: 640,
                            height: 360,
                            zIndex: z++,
                            src: block.src || block.url || block.video_url || '',
                            content: block.title || block.content || ''
                        }));
                        y += 400;
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
            const configuredElements = parseDefaultElements();
            if (configuredElements.length) {
                return configuredElements;
            }

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

        function parseDefaultElements() {
            try {
                const parsed = JSON.parse(defaultElementsData.value || '[]');
                if (!Array.isArray(parsed)) {
                    return [];
                }

                return parsed.map(normalizeElement).filter(Boolean);
            } catch (error) {
                console.error(error);
                return [];
            }
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
                y: clampNumber(overrides.y, 0, maxCanvasHeight, 120),
                width: clampNumber(overrides.width, 20, 2400, defaultWidth),
                height: clampNumber(overrides.height, 20, 3200, defaultHeight),
                zIndex: clampNumber(
                    overrides.zIndex !== undefined ? overrides.zIndex : nextZIndex(),
                    1,
                    9999,
                    1
                ),
                headingLevel: clampNumber(
                    overrides.headingLevel !== undefined ? overrides.headingLevel : (overrides.level !== undefined ? overrides.level : 0),
                    0,
                    3,
                    0
                ),
                navLabel: String(overrides.navLabel || ''),
                content: String(overrides.content || (type === 'text' ? 'New text' : (type === 'video' ? 'YouTube video' : ''))),
                contentHtml: sanitizeRichTextHtml(overrides.contentHtml || overrides.html || ''),
                src: String(overrides.src || (type === 'image' ? 'public/img/logoHEYP.png' : '')),
                href: String(overrides.href || ''),
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
                fontStyle: choice(String(style.fontStyle || 'normal'), ['normal', 'italic'], 'normal'),
                textAlign: choice(String(style.textAlign || 'left'), ['left', 'center', 'right'], 'left'),
                textDecoration: choice(String(style.textDecoration || 'none'), ['none', 'underline'], 'none'),
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

        function getSelectedElements() {
            const selectedSet = new Set(selectedIds);
            return elements.filter(element => selectedSet.has(element.id));
        }

        function getOrderedSelectedElements() {
            return getSelectedElements().sort((a, b) => a.zIndex - b.zIndex);
        }

        function setSelection(ids, primaryId = null, shouldRender = true) {
            const seen = new Set();
            selectedIds = ids.filter(id => {
                if (seen.has(id) || !elements.some(element => element.id === id)) {
                    return false;
                }

                seen.add(id);
                return true;
            });

            selectedId = primaryId && selectedIds.includes(primaryId)
                ? primaryId
                : (selectedIds[selectedIds.length - 1] || null);

            if (shouldRender) {
                renderCanvas();
            }
        }

        function syncSelectionVisuals() {
            const selectedSet = new Set(selectedIds);
            canvas.querySelectorAll('.canvas-element').forEach(node => {
                const id = node.dataset.id;
                node.classList.toggle('selected', selectedSet.has(id));
                node.classList.toggle('primary-selected', id === selectedId);
            });
        }

        function setLiveSelection(ids, primaryId = null) {
            setSelection(ids, primaryId, false);
            syncSelectionVisuals();
            syncInspector();
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

        function getCanvasPoint(event, shouldClamp = true) {
            const rect = canvas.getBoundingClientRect();
            const zoom = canvasZoom || 1;
            const point = {
                x: (event.clientX - rect.left) / zoom,
                y: (event.clientY - rect.top) / zoom
            };

            if (!shouldClamp) {
                return point;
            }

            return {
                x: clampNumber(point.x, 0, canvasSettings.width, 0),
                y: clampNumber(point.y, 0, canvasSettings.height, 0)
            };
        }

        function getVisibleCanvasRect() {
            const canvasRect = canvas.getBoundingClientRect();
            const scrollRect = canvasScroll.getBoundingClientRect();
            const zoom = canvasZoom || 1;
            const left = Math.max(canvasRect.left, scrollRect.left);
            const top = Math.max(canvasRect.top, scrollRect.top);
            const right = Math.min(canvasRect.right, scrollRect.right);
            const bottom = Math.min(canvasRect.bottom, scrollRect.bottom);

            if (right <= left || bottom <= top) {
                return {
                    x: 0,
                    y: clampNumber(canvasScroll.scrollTop / zoom, 0, canvasSettings.height, 0),
                    width: canvasSettings.width,
                    height: Math.min(canvasSettings.height, canvasScroll.clientHeight / zoom)
                };
            }

            return {
                x: clampNumber((left - canvasRect.left) / zoom, 0, canvasSettings.width, 0),
                y: clampNumber((top - canvasRect.top) / zoom, 0, canvasSettings.height, 0),
                width: Math.max(0, (right - left) / zoom),
                height: Math.max(0, (bottom - top) / zoom)
            };
        }

        function getVisibleInsertionPoint(element) {
            const visible = getVisibleCanvasRect();
            const fallbackX = Math.min(120, Math.max(0, canvasSettings.width - element.width));
            const fallbackY = Math.min(120, Math.max(0, maxCanvasHeight - element.height));
            const x = visible.x + Math.max(24, (visible.width - element.width) / 2);
            const y = visible.y + Math.max(24, (visible.height - element.height) / 2);

            return {
                x: clampNumber(x, 0, Math.max(0, canvasSettings.width - element.width), fallbackX),
                y: clampNumber(y, 0, Math.max(0, maxCanvasHeight - element.height), fallbackY)
            };
        }

        function normalizeCanvasRect(start, end) {
            const x = Math.min(start.x, end.x);
            const y = Math.min(start.y, end.y);
            const width = Math.abs(end.x - start.x);
            const height = Math.abs(end.y - start.y);

            return {
                x: x,
                y: y,
                width: width,
                height: height,
                right: x + width,
                bottom: y + height
            };
        }

        function updateMarqueeBox(activeInteraction, rect) {
            if (!activeInteraction.box) {
                return;
            }

            activeInteraction.box.style.left = rect.x + 'px';
            activeInteraction.box.style.top = rect.y + 'px';
            activeInteraction.box.style.width = rect.width + 'px';
            activeInteraction.box.style.height = rect.height + 'px';
        }

        function elementIntersectsRect(element, rect) {
            return element.x < rect.right
                && element.x + element.width > rect.x
                && element.y < rect.bottom
                && element.y + element.height > rect.y;
        }

        function mergeSelectedIds(baseIds, extraIds) {
            const merged = [];
            baseIds.concat(extraIds).forEach(id => {
                if (!merged.includes(id)) {
                    merged.push(id);
                }
            });

            return merged;
        }

        function expandCanvasToElements() {
            const requiredHeight = elements.reduce((height, element) => {
                return Math.max(height, element.y + element.height + canvasBottomPadding);
            }, minCanvasHeight);
            const nextHeight = editorCanvasDefaults.autoFitHeight
                ? requiredHeight
                : Math.max(canvasSettings.height, requiredHeight);

            canvasSettings.height = clampNumber(
                nextHeight,
                minCanvasHeight,
                maxCanvasHeight,
                minCanvasHeight
            );
        }

        function expandCanvasToElement(element) {
            const requiredHeight = element.y + element.height + canvasBottomPadding;
            if (requiredHeight <= canvasSettings.height) {
                return;
            }

            canvasSettings.height = clampNumber(requiredHeight, minCanvasHeight, maxCanvasHeight, canvasSettings.height);
            canvas.style.height = canvasSettings.height + 'px';
            updateCanvasZoom();
        }

        function getSocialIconClass(element) {
            const id = String(element.id || '').toLowerCase();
            if (!id.startsWith('footer-social-')) {
                return '';
            }

            const text = [id, element.content || '', element.href || ''].join(' ').toLowerCase();
            if (text.includes('shopee')) {
                return 'fas fa-shopping-bag';
            }
            if (text.includes('facebook')) {
                return 'fab fa-facebook-f';
            }
            if (text.includes('youtube')) {
                return 'fab fa-youtube';
            }

            return '';
        }

        function getElementHeadingLevel(element) {
            if (!element || typeof element !== 'object') {
                return 0;
            }

            return clampNumber(
                element.headingLevel !== undefined ? element.headingLevel : (element.level !== undefined ? element.level : 0),
                0,
                3,
                0
            );
        }

        function normalizeNavLabel(value) {
            return String(value || '').trim().replace(/\s+/g, ' ');
        }

        function isHeaderNavElement(element) {
            if (!element || !['text', 'heading'].includes(element.type)) {
                return false;
            }

            if (getElementHeadingLevel(element) !== 1) {
                return false;
            }

            const label = normalizeNavLabel(element.navLabel || element.content);
            return label !== '' && !/^https?:\/\//i.test(label);
        }

        function canEditHeaderNavLabel(element) {
            return element && ['text', 'heading'].includes(element.type) && getElementHeadingLevel(element) === 1;
        }

        function headerNavBadgeText(element) {
            return 'Header H1';
        }

        function headerNavStatusText(element) {
            if (isHeaderNavElement(element)) {
                return 'This text is H1 and will show on the header.';
            }

            if (!['text', 'heading'].includes(element.type)) {
                return 'Only text elements can become header items.';
            }

            const headingLevel = getElementHeadingLevel(element);
            if (headingLevel === 2 || headingLevel === 3) {
                return 'This heading is content only and will not show on the header.';
            }

            return 'Set Heading / header to H1 to show this text on the header.';
        }

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = String(value || '');
            return div.innerHTML;
        }

        function escapeAttribute(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }

        function normalizeInlineStyleValue(property, value) {
            value = String(value || '').trim();

            if (property === 'font-family') {
                const cleanFamily = value.replace(/["']/g, '').split(',')[0].trim();
                return ['Open Sans', 'Montserrat', 'Georgia', 'Arial', 'Times New Roman'].includes(cleanFamily)
                    ? cleanFamily
                    : '';
            }

            if (property === 'font-size') {
                const size = clampNumber(String(value).replace('px', ''), 8, 160, 0);
                return size > 0 ? size + 'px' : '';
            }

            if (property === 'font-weight') {
                const weight = value === 'bold' ? '700' : value;
                return ['400', '500', '600', '700'].includes(weight) ? weight : '';
            }

            if (property === 'font-style') {
                return value === 'italic' ? 'italic' : '';
            }

            if (property === 'text-decoration') {
                return value.includes('underline') ? 'underline' : '';
            }

            if (property === 'color' || property === 'background-color') {
                return normalizeColor(value, '') || '';
            }

            return '';
        }

        function sanitizeInlineCss(styleValue) {
            const output = [];
            String(styleValue || '').split(';').forEach(rule => {
                const parts = rule.split(':');
                if (parts.length < 2) {
                    return;
                }

                const property = parts.shift().trim().toLowerCase();
                const value = normalizeInlineStyleValue(property, parts.join(':'));
                if (value !== '') {
                    output.push(property + ': ' + value);
                }
            });

            return output.join('; ');
        }

        function serializeRichNode(node) {
            if (node.nodeType === Node.TEXT_NODE) {
                return escapeHtml(node.textContent || '');
            }

            if (node.nodeType !== Node.ELEMENT_NODE) {
                return '';
            }

            const tag = node.tagName.toLowerCase();
            const children = Array.from(node.childNodes).map(serializeRichNode).join('');

            if (tag === 'br') {
                return '<br>';
            }

            if (tag === 'div' || tag === 'p') {
                return children + '<br>';
            }

            if (tag === 'strong' || tag === 'b') {
                return '<strong>' + children + '</strong>';
            }

            if (tag === 'em' || tag === 'i') {
                return '<em>' + children + '</em>';
            }

            if (tag === 'u') {
                return '<u>' + children + '</u>';
            }

            if (tag === 'span') {
                const style = sanitizeInlineCss(node.getAttribute('style') || '');
                return style !== ''
                    ? '<span style="' + escapeAttribute(style) + '">' + children + '</span>'
                    : children;
            }

            return children;
        }

        function sanitizeRichTextHtml(html) {
            const template = document.createElement('template');
            template.innerHTML = String(html || '');
            return Array.from(template.content.childNodes).map(serializeRichNode).join('').replace(/(?:<br>){2,}$/g, '<br>');
        }

        function richTextToPlainText(node) {
            return String(node.innerText || '')
                .replace(/\u00a0/g, ' ')
                .replace(/\n$/g, '');
        }

        function getCanvasElementNode(id) {
            return Array.from(canvas.querySelectorAll('.canvas-element')).find(node => node.dataset.id === id) || null;
        }

        function getTextContentNode(id) {
            const node = getCanvasElementNode(id);
            return node ? node.querySelector('.canvas-text-content') : null;
        }

        function getTextRangeOwner(range) {
            if (!range) {
                return null;
            }

            const container = range.commonAncestorContainer.nodeType === Node.ELEMENT_NODE
                ? range.commonAncestorContainer
                : range.commonAncestorContainer.parentElement;

            return container ? container.closest('.canvas-text-content') : null;
        }

        function saveCurrentTextSelection() {
            const selection = window.getSelection();
            if (!selection || selection.rangeCount === 0 || selection.isCollapsed) {
                return false;
            }

            const range = selection.getRangeAt(0);
            const owner = getTextRangeOwner(range);
            if (!owner || !canvas.contains(owner)) {
                return false;
            }

            const elementNode = owner.closest('.canvas-element');
            if (!elementNode) {
                return false;
            }

            savedTextRange = range.cloneRange();
            savedTextElementId = elementNode.dataset.id || null;
            return true;
        }

        function getUsableTextRange(element, textNode) {
            const selection = window.getSelection();
            if (selection && selection.rangeCount > 0 && !selection.isCollapsed) {
                const activeRange = selection.getRangeAt(0);
                if (getTextRangeOwner(activeRange) === textNode) {
                    return activeRange;
                }
            }

            if (savedTextRange && savedTextElementId === element.id && getTextRangeOwner(savedTextRange) === textNode) {
                selection.removeAllRanges();
                selection.addRange(savedTextRange);
                return savedTextRange;
            }

            return null;
        }

        function syncRichTextElementFromNode(element, textNode) {
            element.content = richTextToPlainText(textNode);
            element.contentHtml = sanitizeRichTextHtml(textNode.innerHTML);
            inspector.content.value = element.content;
            syncJsonPreview();
            scheduleHistory();
        }

        function applyInlineStylesToRange(element, styles) {
            const textNode = getTextContentNode(element.id);
            if (!textNode) {
                return false;
            }

            const range = getUsableTextRange(element, textNode);
            if (!range || range.collapsed) {
                return false;
            }

            const span = document.createElement('span');
            Object.entries(styles).forEach(([field, value]) => {
                if (field === 'fontFamily') {
                    span.style.fontFamily = fontStack(value);
                } else if (field === 'fontSize') {
                    span.style.fontSize = clampNumber(value, 8, 160, element.style.fontSize) + 'px';
                } else if (field === 'fontWeight') {
                    span.style.fontWeight = choice(String(value), ['400', '500', '600', '700'], element.style.fontWeight);
                } else if (field === 'fontStyle') {
                    span.style.fontStyle = value === 'italic' ? 'italic' : 'normal';
                } else if (field === 'textDecoration') {
                    span.style.textDecoration = value === 'underline' ? 'underline' : 'none';
                } else if (field === 'color') {
                    span.style.color = normalizeColor(value, element.style.color);
                }
            });

            span.appendChild(range.extractContents());
            range.insertNode(span);

            const nextRange = document.createRange();
            nextRange.selectNodeContents(span);
            const selection = window.getSelection();
            selection.removeAllRanges();
            selection.addRange(nextRange);
            saveCurrentTextSelection();
            syncRichTextElementFromNode(element, textNode);
            return true;
        }

        function applyTextStyle(field, value) {
            const element = getSelectedElement();
            if (!element || element.type !== 'text') {
                return false;
            }

            if (['fontFamily', 'fontSize', 'fontWeight', 'color'].includes(field) && applyInlineStylesToRange(element, { [field]: value })) {
                return true;
            }

            element.style[field] = value;
            element.style = normalizeStyle(element.style, element.type);
            renderCanvas();
            scheduleHistory();
            return true;
        }

        function rangeStartComputedStyle(element) {
            const textNode = getTextContentNode(element.id);
            const range = textNode ? getUsableTextRange(element, textNode) : null;
            if (!range) {
                return null;
            }

            const source = range.startContainer.nodeType === Node.ELEMENT_NODE
                ? range.startContainer
                : range.startContainer.parentElement;

            return source ? window.getComputedStyle(source) : null;
        }

        function toggleTextStyle(command) {
            const element = getSelectedElement();
            if (!element || element.type !== 'text') {
                return;
            }

            const computed = rangeStartComputedStyle(element);
            if (command === 'bold') {
                const currentWeight = computed ? parseInt(computed.fontWeight, 10) : parseInt(element.style.fontWeight, 10);
                const nextWeight = currentWeight >= 600 ? '400' : '700';
                if (!applyInlineStylesToRange(element, { fontWeight: nextWeight })) {
                    applyTextStyle('fontWeight', nextWeight);
                }
                return;
            }

            if (command === 'italic') {
                const nextStyle = (computed ? computed.fontStyle : element.style.fontStyle) === 'italic' ? 'normal' : 'italic';
                if (!applyInlineStylesToRange(element, { fontStyle: nextStyle })) {
                    applyTextStyle('fontStyle', nextStyle);
                }
                return;
            }

            if (command === 'underline') {
                const currentDecoration = computed ? computed.textDecorationLine : element.style.textDecoration;
                const nextDecoration = String(currentDecoration || '').includes('underline') ? 'none' : 'underline';
                if (!applyInlineStylesToRange(element, { textDecoration: nextDecoration })) {
                    applyTextStyle('textDecoration', nextDecoration);
                }
            }
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
            node.style.fontStyle = element.style.fontStyle;
            node.style.textAlign = element.style.textAlign;
            node.style.textDecoration = element.style.textDecoration;
            node.style.color = element.style.color;
            node.style.backgroundColor = element.style.backgroundColor;
            node.style.borderRadius = element.style.borderRadius + 'px';
            node.classList.toggle('selected', selectedIds.includes(element.id));
            node.classList.toggle('primary-selected', element.id === selectedId);
            if (isHeaderNavElement(element)) {
                node.classList.add('is-header-nav-item');
                node.dataset.headerNav = headerNavBadgeText(element);
            }

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
                const socialIconClass = getSocialIconClass(element);
                if (socialIconClass !== '') {
                    node.classList.add('canvas-element-social');
                    node.contentEditable = 'false';
                    node.title = element.content || '';
                    const icon = document.createElement('i');
                    icon.className = socialIconClass;
                    node.appendChild(icon);
                } else {
                    const textContent = document.createElement('div');
                    textContent.className = 'canvas-text-content';
                    textContent.contentEditable = 'true';
                    textContent.spellcheck = false;
                    textContent.innerHTML = element.contentHtml !== ''
                        ? sanitizeRichTextHtml(element.contentHtml)
                        : escapeHtml(element.content);
                    textContent.addEventListener('input', () => {
                        syncRichTextElementFromNode(element, textContent);
                        syncInspector();
                    });
                    textContent.addEventListener('keydown', event => {
                        if (event.key === 'Enter') {
                            event.preventDefault();
                            document.execCommand('insertLineBreak');
                        }
                    });
                    textContent.addEventListener('paste', event => {
                        event.preventDefault();
                        const text = event.clipboardData ? event.clipboardData.getData('text/plain') : '';
                        document.execCommand('insertText', false, text);
                    });
                    ['mouseup', 'keyup', 'focus'].forEach(eventName => {
                        textContent.addEventListener(eventName, saveCurrentTextSelection);
                    });
                    node.appendChild(textContent);
                }
            }

            node.addEventListener('pointerdown', event => startDrag(event, element.id));
            node.addEventListener('click', event => {
                event.stopPropagation();
                selectElement(element.id, event.shiftKey);
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

        function startMarqueeSelection(event) {
            if (event.target !== canvas || (event.button !== undefined && event.button !== 0)) {
                return;
            }

            const origin = getCanvasPoint(event);
            const box = document.createElement('div');
            box.className = 'selection-marquee';
            canvas.appendChild(box);

            interaction = {
                mode: 'marquee',
                startX: event.clientX,
                startY: event.clientY,
                origin: origin,
                additive: event.shiftKey,
                originalSelectedId: selectedId,
                originalSelectedIds: selectedIds.slice(),
                box: box,
                changed: false
            };

            if (!event.shiftKey && selectedIds.length) {
                setLiveSelection([], null);
            }

            canvas.classList.add('is-marquee-selecting');
            updateMarqueeBox(interaction, normalizeCanvasRect(origin, origin));
            event.preventDefault();
        }

        function startDrag(event, id) {
            if (event.target.classList.contains('resize-handle')) {
                return;
            }
            if (event.shiftKey) {
                return;
            }
            if (event.target.closest('.canvas-text-content') && selectedId === id) {
                return;
            }

            let selectionChanged = false;
            if (!selectedIds.includes(id)) {
                selectElement(id, false, false);
                syncSelectionVisuals();
                selectionChanged = true;
            }
            const element = getSelectedElement();
            if (!element) {
                return;
            }

            const startPoint = getCanvasPoint(event, false);
            const dragIds = selectedIds.includes(id) ? selectedIds.slice() : [id];
            const originalPositions = {};
            elements.forEach(item => {
                if (dragIds.includes(item.id)) {
                    originalPositions[item.id] = {
                        x: item.x,
                        y: item.y
                    };
                }
            });

            interaction = {
                mode: 'drag',
                id: id,
                ids: dragIds,
                startX: event.clientX,
                startY: event.clientY,
                startCanvasX: startPoint.x,
                startCanvasY: startPoint.y,
                originalX: element.x,
                originalY: element.y,
                originalPositions: originalPositions,
                renderOnStop: selectionChanged,
                changed: false
            };
            event.preventDefault();
        }

        function startResize(event, id, handle) {
            selectElement(id, false, false);
            const element = getSelectedElement();
            if (!element) {
                return;
            }

            const startPoint = getCanvasPoint(event, false);
            interaction = {
                mode: 'resize',
                id: id,
                handle: handle,
                startX: event.clientX,
                startY: event.clientY,
                startCanvasX: startPoint.x,
                startCanvasY: startPoint.y,
                originalX: element.x,
                originalY: element.y,
                originalWidth: element.width,
                originalHeight: element.height,
                changed: false
            };
            event.stopPropagation();
            event.preventDefault();
        }

        function handleMarqueeMove(event) {
            event.preventDefault();
            const point = getCanvasPoint(event);
            const rect = normalizeCanvasRect(interaction.origin, point);
            updateMarqueeBox(interaction, rect);

            if (
                Math.abs(event.clientX - interaction.startX) < 4
                && Math.abs(event.clientY - interaction.startY) < 4
            ) {
                return;
            }

            interaction.changed = true;
            const hitIds = elements
                .slice()
                .sort((a, b) => a.zIndex - b.zIndex)
                .filter(element => elementIntersectsRect(element, rect))
                .map(element => element.id);
            const ids = interaction.additive
                ? mergeSelectedIds(interaction.originalSelectedIds, hitIds)
                : hitIds;
            const primaryId = hitIds.length
                ? hitIds[hitIds.length - 1]
                : (interaction.additive ? interaction.originalSelectedId : null);

            setLiveSelection(ids, primaryId);
        }

        function pointerSnapshot(event) {
            event.preventDefault();

            return {
                clientX: event.clientX,
                clientY: event.clientY,
                preventDefault: () => {}
            };
        }

        function edgeScrollAmount(position, start, end) {
            if (position < start + autoScrollEdgeSize) {
                const ratio = (start + autoScrollEdgeSize - position) / autoScrollEdgeSize;
                return -Math.ceil(Math.min(1, ratio) * autoScrollMaxSpeed);
            }

            if (position > end - autoScrollEdgeSize) {
                const ratio = (position - (end - autoScrollEdgeSize)) / autoScrollEdgeSize;
                return Math.ceil(Math.min(1, ratio) * autoScrollMaxSpeed);
            }

            return 0;
        }

        function getAutoScrollDelta(pointer) {
            const rect = canvasScroll.getBoundingClientRect();

            return {
                x: edgeScrollAmount(pointer.clientX, rect.left, rect.right),
                y: edgeScrollAmount(pointer.clientY, rect.top, rect.bottom)
            };
        }

        function applyAutoScroll(delta) {
            let scrolled = false;

            if (delta.x !== 0) {
                const previousLeft = canvasScroll.scrollLeft;
                canvasScroll.scrollLeft += delta.x;
                scrolled = scrolled || canvasScroll.scrollLeft !== previousLeft;
            }

            if (delta.y !== 0) {
                const previousTop = canvasScroll.scrollTop;
                canvasScroll.scrollTop += delta.y;
                scrolled = scrolled || canvasScroll.scrollTop !== previousTop;

                if (canvasScroll.scrollTop === previousTop) {
                    const previousWindowY = window.scrollY;
                    window.scrollBy(0, delta.y);
                    scrolled = scrolled || window.scrollY !== previousWindowY;
                }
            }

            return scrolled;
        }

        function runAutoScroll() {
            autoScrollFrame = null;

            if (!interaction || !lastPointerPosition) {
                return;
            }

            const delta = getAutoScrollDelta(lastPointerPosition);
            if (delta.x === 0 && delta.y === 0) {
                return;
            }

            applyAutoScroll(delta);
            updateInteractionFromPointer(lastPointerPosition);
            startAutoScroll();
        }

        function startAutoScroll() {
            if (autoScrollFrame !== null) {
                return;
            }

            autoScrollFrame = window.requestAnimationFrame(runAutoScroll);
        }

        function updateAutoScroll(pointer) {
            const delta = getAutoScrollDelta(pointer);

            if (delta.x !== 0 || delta.y !== 0) {
                startAutoScroll();
                return;
            }

            stopAutoScroll();
        }

        function stopAutoScroll() {
            if (autoScrollFrame === null) {
                return;
            }

            window.cancelAnimationFrame(autoScrollFrame);
            autoScrollFrame = null;
        }

        function finishMarqueeSelection(activeInteraction) {
            canvas.classList.remove('is-marquee-selecting');
            suppressCanvasClick = true;

            if (activeInteraction.box && activeInteraction.box.parentNode) {
                activeInteraction.box.remove();
            }

            if (!activeInteraction.changed && !activeInteraction.additive) {
                setSelection([], null, false);
            }

            renderCanvas();
        }

        function handlePointerMove(event) {
            if (!interaction) {
                return;
            }

            lastPointerPosition = pointerSnapshot(event);
            updateInteractionFromPointer(lastPointerPosition);
            updateAutoScroll(lastPointerPosition);
        }

        function updateInteractionFromPointer(pointer) {
            if (!interaction) {
                return;
            }

            if (interaction.mode === 'marquee') {
                handleMarqueeMove(pointer);
                return;
            }

            const element = elements.find(item => item.id === interaction.id);
            if (!element) {
                return;
            }

            const point = getCanvasPoint(pointer, false);
            const dx = point.x - interaction.startCanvasX;
            const dy = point.y - interaction.startCanvasY;

            if (interaction.mode === 'drag') {
                (interaction.ids || [interaction.id]).forEach(id => {
                    const item = elements.find(entry => entry.id === id);
                    const original = interaction.originalPositions && interaction.originalPositions[id];
                    if (!item || !original) {
                        return;
                    }

                    item.x = clampNumber(original.x + dx, 0, canvasSettings.width - item.width, item.x);
                    item.y = clampNumber(original.y + dy, 0, maxCanvasHeight - item.height, item.y);
                    expandCanvasToElement(item);
                    updateElementNode(item);
                });
            }

            if (interaction.mode === 'resize') {
                resizeElement(element, dx, dy);
                expandCanvasToElement(element);
                updateElementNode(element);
            }

            interaction.changed = true;
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
            if (!interaction) {
                return;
            }

            stopAutoScroll();
            lastPointerPosition = null;

            if (interaction.mode === 'marquee') {
                finishMarqueeSelection(interaction);
                interaction = null;
                return;
            }

            const shouldRenderAfterInteraction = interaction.renderOnStop
                || (interaction.changed && editorCanvasDefaults.autoFitHeight);
            if (shouldRenderAfterInteraction) {
                renderCanvas();
            }
            if (interaction.changed) {
                recordHistory();
            }
            interaction = null;
        }

        function selectElement(id, additive = false, shouldRender = true) {
            if (additive) {
                if (selectedIds.includes(id)) {
                    setSelection(selectedIds.filter(selected => selected !== id), selectedId === id ? null : selectedId, shouldRender);
                    return;
                }

                setSelection(selectedIds.concat(id), id, shouldRender);
                return;
            }

            if (selectedId === id && selectedIds.length === 1) {
                syncInspector();
                return;
            }

            setSelection([id], id, shouldRender);
        }

        function addElement(type) {
            const element = newElement(type, {
                zIndex: nextZIndex()
            });
            const position = getVisibleInsertionPoint(element);
            element.x = position.x;
            element.y = position.y;

            elements.push(element);
            expandCanvasToElement(element);
            setSelection([element.id], element.id, false);
            renderCanvas();
            recordHistory();
        }

        function deleteSelectedElement() {
            if (!selectedIds.length) {
                return;
            }

            const selectedSet = new Set(selectedIds);
            elements = elements.filter(element => !selectedSet.has(element.id));
            selectedIds = elements[0] ? [elements[0].id] : [];
            selectedId = selectedIds[0] || null;
            renderCanvas();
            recordHistory();
        }

        function reorderLayer(direction) {
            const selectedElements = getSelectedElements();
            if (!selectedElements.length) {
                return;
            }

            selectedElements.forEach(element => {
                element.zIndex = direction === 'forward'
                    ? element.zIndex + 1
                    : Math.max(1, element.zIndex - 1);
            });
            normalizeZIndexes();
            renderCanvas();
            recordHistory();
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
            } else if (field === 'headingLevel') {
                element.headingLevel = clampNumber(value, 0, 3, 0);
                if (element.headingLevel !== 1) {
                    element.navLabel = '';
                }
            } else if (field === 'content') {
                element.content = String(value || '');
                element.contentHtml = '';
            } else {
                element[field] = String(value || '');
                if (field === 'src' && element.type === 'image' && toYouTubeEmbedUrl(element[field]) !== '') {
                    element.type = 'video';
                    element.style = normalizeStyle(element.style, element.type);
                }
            }

            renderCanvas();
            scheduleHistory();
        }

        function updateSelectedStyle(field, value) {
            const element = getSelectedElement();
            if (!element) {
                return;
            }

            if (element.type === 'text' && applyTextStyle(field, value)) {
                return;
            }

            element.style[field] = value;
            element.style = normalizeStyle(element.style, element.type);
            renderCanvas();
            scheduleHistory();
        }

        function syncInspector() {
            const element = getSelectedElement();
            emptyInspector.hidden = !!element && selectedIds.length <= 1;
            inspectorFields.hidden = !element;

            if (!element) {
                emptyInspector.textContent = 'Select an element to edit position, size, content, and style.';
                return;
            }

            emptyInspector.textContent = selectedIds.length > 1
                ? selectedIds.length + ' elements selected. Inspector edits the primary element.'
                : 'Select an element to edit position, size, content, and style.';

            inspector.type.value = element.type;
            inspector.x.value = element.x;
            inspector.y.value = element.y;
            inspector.width.value = element.width;
            inspector.height.value = element.height;
            inspector.zIndex.value = element.zIndex;
            inspector.headingLevel.value = getElementHeadingLevel(element);
            inspector.headerNavHint.textContent = headerNavStatusText(element);
            inspector.navLabel.value = element.navLabel || '';
            inspector.content.value = element.content || '';
            inspector.src.value = element.src || '';
            inspector.href.value = element.href || '';
            inspector.fontFamily.value = element.style.fontFamily;
            inspector.fontSize.value = element.style.fontSize;
            inspector.fontWeight.value = element.style.fontWeight;
            inspector.boldButton.classList.toggle('active', element.style.fontWeight === '700');
            inspector.italicButton.classList.toggle('active', element.style.fontStyle === 'italic');
            inspector.underlineButton.classList.toggle('active', element.style.textDecoration === 'underline');
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
            document.querySelectorAll('[data-heading-control]').forEach(field => {
                field.hidden = !['text', 'heading'].includes(element.type);
            });
            document.querySelectorAll('[data-header-label-control]').forEach(field => {
                field.hidden = !canEditHeaderNavLabel(element);
            });
        }

        function buildPage() {
            normalizeZIndexes();
            expandCanvasToElements();
            const normalizedElements = elements.map(element => {
                const normalized = cloneElement(element);
                if (!isHeaderNavElement(normalized)) {
                    normalized.navLabel = '';
                }
                if (normalized.type === 'text') {
                    normalized.contentHtml = sanitizeRichTextHtml(normalized.contentHtml || '');
                    if (normalized.contentHtml === '') {
                        delete normalized.contentHtml;
                    }
                } else {
                    delete normalized.contentHtml;
                }
                return normalized;
            });

            return {
                header: page.header || { logo: 'public/img/logoHEYP.png' },
                layoutType: 'canvas',
                canvas: canvasSettings,
                elements: normalizedElements
            };
        }

        function cloneElement(element) {
            return JSON.parse(JSON.stringify(element));
        }

        function copySelectedElements() {
            const selectedElements = getOrderedSelectedElements();
            if (!selectedElements.length) {
                return false;
            }

            elementClipboard = selectedElements.map(cloneElement);
            return true;
        }

        function pasteElements() {
            if (!elementClipboard.length) {
                return;
            }

            const newIds = [];
            let nextLayer = nextZIndex();
            elementClipboard.forEach(element => {
                const pasted = cloneElement(element);
                pasted.id = createId();
                pasted.x = clampNumber(pasted.x + 24, 0, canvasSettings.width - pasted.width, 120);
                pasted.y = clampNumber(pasted.y + 24, 0, maxCanvasHeight - pasted.height, 120);
                pasted.zIndex = nextLayer++;
                elements.push(pasted);
                newIds.push(pasted.id);
                expandCanvasToElement(pasted);
            });

            setSelection(newIds, newIds[newIds.length - 1] || null, false);
            renderCanvas();
            recordHistory();
        }

        function duplicateSelectedElements() {
            if (!copySelectedElements()) {
                return;
            }

            pasteElements();
        }

        function selectAllElements() {
            setSelection(elements.map(element => element.id), elements.length ? elements[elements.length - 1].id : null);
        }

        function clearSelection() {
            setSelection([], null);
        }

        function moveSelectedElements(deltaX, deltaY) {
            const selectedElements = getSelectedElements();
            if (!selectedElements.length) {
                return;
            }

            selectedElements.forEach(element => {
                element.x = clampNumber(element.x + deltaX, 0, canvasSettings.width - element.width, element.x);
                element.y = clampNumber(element.y + deltaY, 0, maxCanvasHeight - element.height, element.y);
                expandCanvasToElement(element);
            });

            renderCanvas();
            recordHistory();
        }

        function serializeEditorState() {
            return JSON.stringify({
                canvas: canvasSettings,
                elements: elements,
                selectedIds: selectedIds
            });
        }

        function resetHistory() {
            historyStack = [serializeEditorState()];
            redoStack = [];
        }

        function recordHistory() {
            if (isRestoringHistory) {
                return;
            }

            const state = serializeEditorState();
            if (historyStack[historyStack.length - 1] === state) {
                return;
            }

            historyStack.push(state);
            if (historyStack.length > 100) {
                historyStack.shift();
            }
            redoStack = [];
        }

        function scheduleHistory() {
            window.clearTimeout(historyTimer);
            historyTimer = window.setTimeout(() => {
                historyTimer = null;
                recordHistory();
            }, 350);
        }

        function flushPendingHistory() {
            if (!historyTimer) {
                return;
            }

            window.clearTimeout(historyTimer);
            historyTimer = null;
            recordHistory();
        }

        function restoreEditorState(state) {
            isRestoringHistory = true;
            try {
                const parsed = JSON.parse(state);
                canvasSettings = normalizeCanvas(parsed.canvas || {});
                elements = Array.isArray(parsed.elements)
                    ? parsed.elements.map(normalizeElement).filter(Boolean)
                    : [];
                const restoredIds = Array.isArray(parsed.selectedIds) ? parsed.selectedIds : [];
                selectedIds = restoredIds.filter(id => elements.some(element => element.id === id));
                selectedId = selectedIds[selectedIds.length - 1] || null;
                renderCanvas();
            } finally {
                isRestoringHistory = false;
            }
        }

        function undo() {
            flushPendingHistory();
            if (historyStack.length <= 1) {
                return;
            }

            redoStack.push(historyStack.pop());
            restoreEditorState(historyStack[historyStack.length - 1]);
        }

        function redo() {
            flushPendingHistory();
            if (!redoStack.length) {
                return;
            }

            const state = redoStack.pop();
            historyStack.push(state);
            restoreEditorState(state);
        }

        function isInputTarget(target) {
            return target && (
                target.matches('input, textarea, select') ||
                target.closest('input, textarea, select')
            );
        }

        function hasEditableTextSelection(target) {
            if (!target || !target.isContentEditable) {
                return false;
            }

            const selection = window.getSelection();
            return selection && !selection.isCollapsed && target.contains(selection.anchorNode);
        }

        function isCanvasTextEditorTarget(target) {
            return !!(target && target.closest && target.closest('.canvas-text-content'));
        }

        function saveEditor() {
            flushPendingHistory();
            syncJsonPreview();
            if (typeof form.requestSubmit === 'function') {
                form.requestSubmit();
            } else {
                form.submit();
            }
        }

        function shouldIgnoreShortcut(event, key, usesModifier) {
            const target = event.target;
            if (isInputTarget(target)) {
                return !(usesModifier && key === 's');
            }

            return false;
        }

        function handleKeyboardShortcut(event) {
            const key = event.key.toLowerCase();
            const usesModifier = event.metaKey || event.ctrlKey;
            const editingCanvasText = event.target && event.target.isContentEditable;

            if (shouldIgnoreShortcut(event, key, usesModifier)) {
                return;
            }

            if (editingCanvasText && !usesModifier && key !== 'escape') {
                return;
            }

            if (usesModifier && key === 's') {
                event.preventDefault();
                saveEditor();
                return;
            }

            if (usesModifier && key === 'z') {
                event.preventDefault();
                if (event.shiftKey) {
                    redo();
                } else {
                    undo();
                }
                return;
            }

            if (usesModifier && key === 'y') {
                event.preventDefault();
                redo();
                return;
            }

            if (usesModifier && ['b', 'i', 'u'].includes(key)) {
                event.preventDefault();
                const command = key === 'b' ? 'bold' : (key === 'i' ? 'italic' : 'underline');
                toggleTextStyle(command);
                return;
            }

            if (usesModifier && key === 'c') {
                if (isCanvasTextEditorTarget(event.target) && hasEditableTextSelection(event.target)) {
                    return;
                }
                if (copySelectedElements()) {
                    event.preventDefault();
                }
                return;
            }

            if (usesModifier && key === 'v') {
                if (isCanvasTextEditorTarget(event.target)) {
                    return;
                }
                if (elementClipboard.length) {
                    event.preventDefault();
                    pasteElements();
                }
                return;
            }

            if (usesModifier && key === 'd') {
                event.preventDefault();
                duplicateSelectedElements();
                return;
            }

            if (usesModifier && key === 'a') {
                if (isCanvasTextEditorTarget(event.target)) {
                    return;
                }
                event.preventDefault();
                selectAllElements();
                return;
            }

            if (key === 'delete' || key === 'backspace') {
                if (editingCanvasText) {
                    return;
                }

                if (selectedIds.length) {
                    event.preventDefault();
                    deleteSelectedElement();
                }
                return;
            }

            if (key === 'escape') {
                if (selectedIds.length) {
                    event.preventDefault();
                    clearSelection();
                }
                return;
            }

            const arrowMoves = {
                arrowleft: [-1, 0],
                arrowright: [1, 0],
                arrowup: [0, -1],
                arrowdown: [0, 1]
            };

            if (arrowMoves[key]) {
                if (editingCanvasText) {
                    return;
                }

                const [deltaX, deltaY] = arrowMoves[key];
                const multiplier = event.shiftKey ? 10 : 1;
                event.preventDefault();
                moveSelectedElements(deltaX * multiplier, deltaY * multiplier);
            }
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
            input.addEventListener('mousedown', saveCurrentTextSelection);
            input.addEventListener('focus', saveCurrentTextSelection);
            input.addEventListener('input', () => updateSelectedStyle(input.dataset.styleField, input.value));
        });

        document.querySelectorAll('[data-inline-style]').forEach(button => {
            button.addEventListener('mousedown', saveCurrentTextSelection);
            button.addEventListener('click', () => toggleTextStyle(button.dataset.inlineStyle));
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
                recordHistory();
            });
            reader.readAsDataURL(file);
        });
        canvas.addEventListener('pointerdown', startMarqueeSelection);
        canvas.addEventListener('click', event => {
            if (suppressCanvasClick) {
                suppressCanvasClick = false;
                return;
            }

            if (event.target !== canvas || event.shiftKey) {
                return;
            }

            clearSelection();
        });
        window.addEventListener('pointermove', handlePointerMove);
        window.addEventListener('pointerup', stopInteraction);
        window.addEventListener('resize', updateCanvasZoom);
        window.addEventListener('keydown', handleKeyboardShortcut);
        document.addEventListener('selectionchange', saveCurrentTextSelection);
        form.addEventListener('submit', syncJsonPreview);
        document.querySelectorAll('[data-auto-dismiss]').forEach(alert => {
            const delay = clampNumber(alert.dataset.autoDismiss, 500, 10000, 3000);
            window.setTimeout(() => {
                alert.classList.add('is-hiding');
                window.setTimeout(() => alert.remove(), 260);
            }, delay);
        });

        renderCanvas();
        resetHistory();
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
    .muted,
    .shortcut-hints {
        margin: 0 0 8px;
        color: var(--muted);
    }

    .shortcut-hints {
        font-size: 0.9rem;
        line-height: 1.5;
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

    .toast-alert {
        position: fixed;
        top: 18px;
        right: 18px;
        z-index: 30000;
        max-width: 320px;
        min-width: 220px;
        padding: 10px 14px;
        box-shadow: 0 10px 28px rgba(31, 41, 55, 0.18);
        transition: opacity 0.22s ease, transform 0.22s ease;
    }

    .toast-alert.is-hiding {
        opacity: 0;
        transform: translateY(-8px);
        pointer-events: none;
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

    .canvas.is-marquee-selecting {
        cursor: crosshair;
    }

    .selection-marquee {
        position: absolute;
        border: 1px solid #2563eb;
        background: rgba(37, 99, 235, 0.12);
        box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.18);
        pointer-events: none;
        z-index: 20000;
    }

    .canvas.is-marquee-selecting .move-handle,
    .canvas.is-marquee-selecting .resize-handle {
        display: none;
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

    .canvas-element.selected:not(.primary-selected) {
        border-color: #14b8a6;
        box-shadow: 0 0 0 2px rgba(20, 184, 166, 0.18);
    }

    .canvas-element.is-header-nav-item {
        outline: 2px solid rgba(45, 90, 39, 0.55);
        outline-offset: 2px;
    }

    .canvas-element.is-header-nav-item::after {
        content: attr(data-header-nav);
        position: absolute;
        top: 6px;
        right: 6px;
        padding: 3px 7px;
        border-radius: 6px;
        background: #2d5a27;
        color: #ffffff;
        font-family: Montserrat, Arial, sans-serif;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.2;
        pointer-events: none;
        white-space: nowrap;
        z-index: 10002;
    }

    .canvas-element-text {
        padding: 8px;
        line-height: 1.25;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        user-select: text;
    }

    .canvas-text-content {
        width: 100%;
        height: 100%;
        outline: none;
        white-space: inherit;
        overflow-wrap: inherit;
        cursor: text;
    }

    .canvas-element-social {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        text-align: center;
        user-select: none;
    }

    .canvas-element-social i {
        font-size: 1.2em;
        line-height: 1;
        pointer-events: none;
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
        position: sticky;
        top: 18px;
        max-height: calc(100vh - 36px);
        overflow-y: auto;
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

    .inline-style-toolbar {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .inline-style-toolbar button {
        width: 38px;
        height: 34px;
        padding: 0;
        justify-content: center;
    }

    .inline-style-toolbar button.active {
        border-color: #2563eb;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .inline-style-toolbar span {
        color: var(--muted);
        font-size: 12px;
        line-height: 1.4;
    }

    textarea {
        resize: vertical;
    }

    .header-nav-hint {
        color: #2d5a27;
        font-size: 0.82rem;
        line-height: 1.4;
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

        .inspector {
            position: static;
            max-height: none;
            overflow-y: visible;
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
