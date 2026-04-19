<?php
$pageJson = $data['page_json'] ?? '{}';
$message = $data['message'] ?? '';
$error = $data['error'] ?? '';
$formAction = $data['formAction'] ?? (URL_ROOT . '/admin/pages');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Block Landing Page Editor - HeypVietNam Admin</title>
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
                <a href="<?php echo URL_ROOT; ?>/admin/pages" class="active"><i class="fas fa-file-lines"></i> Block editor</a>
                <a href="<?php echo URL_ROOT; ?>/admin/footer"><i class="fas fa-window-restore"></i> Footer</a>
                <a href="<?php echo URL_ROOT; ?>" target="_blank"><i class="fas fa-house"></i> Xem trang</a>
                <a href="<?php echo URL_ROOT; ?>/admin/logout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
            </nav>
        </aside>

        <main class="editor-page">
            <?php if ($message !== ''): ?>
                <div class="alert success" role="status"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form id="blockEditorForm" method="POST" action="<?php echo htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" id="contentJsonInput" name="content_json">
                <textarea id="pageData" hidden><?php echo htmlspecialchars($pageJson, ENT_QUOTES, 'UTF-8'); ?></textarea>

                <header class="editor-header">
                    <div>
                        <p class="eyebrow">Landing page builder</p>
                        <h1>Word-like block editor</h1>
                        <p class="note">Each row is one block. H1 rows become header menu anchors.</p>
                    </div>
                    <div class="header-actions">
                        <a class="secondary-link" href="<?php echo URL_ROOT; ?>" target="_blank">View page</a>
                        <button type="submit" class="save-button"><i class="fas fa-save"></i> Save landing page</button>
                    </div>
                </header>

                <section class="toolbar" aria-label="Add blocks">
                    <button type="button" data-add-block="heading" data-level="1"><i class="fas fa-heading"></i> H1</button>
                    <button type="button" data-add-block="heading" data-level="2"><i class="fas fa-heading"></i> H2</button>
                    <button type="button" data-add-block="heading" data-level="3"><i class="fas fa-heading"></i> H3</button>
                    <button type="button" data-add-block="text"><i class="fas fa-align-left"></i> Text</button>
                    <button type="button" data-add-block="image"><i class="fas fa-image"></i> Image</button>
                </section>

                <div class="workspace">
                    <section class="document-panel" aria-label="Editable landing page blocks">
                        <div id="blockList" class="block-list"></div>
                        <button type="button" id="appendTextBlock" class="append-block">+ Add text block</button>
                    </section>

                    <aside class="json-panel">
                        <h2>Data structure used</h2>
                        <p>Saved as JSON in <code>pages.content</code>.</p>
                        <pre id="jsonPreview" aria-live="polite"></pre>
                    </aside>
                </div>
            </form>
        </main>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pageData = document.getElementById('pageData');
        const form = document.getElementById('blockEditorForm');
        const jsonInput = document.getElementById('contentJsonInput');
        const blockList = document.getElementById('blockList');
        const jsonPreview = document.getElementById('jsonPreview');
        const appendTextBlock = document.getElementById('appendTextBlock');
        const defaultHeader = { logo: 'public/img/logoHEYP.png' };

        let page = parsePage(pageData.value);
        let blocks = pageToBlocks(page);
        let selectedId = blocks[0] ? blocks[0].id : null;
        let draggedId = null;

        function parsePage(json) {
            try {
                const raw = JSON.parse(json || '{}');
                if (raw && typeof raw === 'object') {
                    return normalizePage(raw);
                }
            } catch (error) {
                console.error(error);
            }

            return normalizePage({});
        }

        function normalizePage(raw) {
            const normalized = {
                header: raw.header && typeof raw.header === 'object' ? raw.header : defaultHeader,
                sections: Array.isArray(raw.sections) ? raw.sections : []
            };

            if (!normalized.sections.length && Array.isArray(raw.elements)) {
                normalized.sections = canvasElementsToSections(raw.elements, normalized.header);
            }

            return normalized;
        }

        function canvasElementsToSections(elements) {
            const sorted = elements
                .filter(element => element && typeof element === 'object')
                .slice()
                .sort((a, b) => Number(a.y || 0) - Number(b.y || 0) || Number(a.x || 0) - Number(b.x || 0));
            const sections = [];
            let current = null;

            sorted.forEach(element => {
                const content = String(element.content || '').trim();
                const style = element.style && typeof element.style === 'object' ? element.style : {};
                const fontSize = Number(style.fontSize || 0);
                const level = Number(element.headingLevel || element.level || 0);
                const isH1 = element.type !== 'image' && content && (level === 1 || fontSize >= 32);

                if (isH1) {
                    const anchorId = slugify(element.id || content, 'section-' + (sections.length + 1));
                    current = {
                        id: anchorId,
                        heading: { level: 1, text: content, anchorId: anchorId },
                        blocks: []
                    };
                    sections.push(current);
                    return;
                }

                if (!current) {
                    current = {
                        id: 'home',
                        heading: { level: 1, text: 'Trang Chủ', anchorId: 'home' },
                        blocks: []
                    };
                    sections.push(current);
                }

                if (element.type === 'image') {
                    const src = String(element.src || element.image_url || element.url || '').trim();
                    if (src) {
                        current.blocks.push({ type: 'image', src: src, alt: content, caption: '' });
                    }
                    return;
                }

                if (content) {
                    const contentLevel = level === 2 || level === 3 ? level : (fontSize >= 28 ? 2 : 0);
                    current.blocks.push(contentLevel
                        ? { type: 'heading', level: contentLevel, content: content }
                        : { type: 'text', content: content });
                }
            });

            return sections;
        }

        function pageToBlocks(currentPage) {
            const output = [];

            (currentPage.sections || []).forEach((section, index) => {
                const heading = section.heading && typeof section.heading === 'object' ? section.heading : {};
                const anchorId = slugify(section.id || heading.anchorId || heading.text, 'section-' + (index + 1));

                if (String(heading.text || '').trim()) {
                    output.push(newBlock('heading', {
                        level: clampHeadingLevel(heading.level || 1),
                        content: heading.text,
                        anchorId: anchorId
                    }));
                }

                (Array.isArray(section.blocks) ? section.blocks : []).forEach(block => {
                    const normalized = normalizeBlock(block);
                    if (normalized) {
                        output.push(normalized);
                    }
                });
            });

            if (!output.length) {
                output.push(newBlock('heading', {
                    level: 1,
                    content: 'Trang Chủ',
                    anchorId: 'home'
                }));
                output.push(newBlock('text', {
                    content: 'Start writing your landing page content.'
                }));
            }

            return output;
        }

        function normalizeBlock(block) {
            if (!block || typeof block !== 'object') {
                return null;
            }

            if (block.type === 'heading') {
                return newBlock('heading', {
                    level: clampHeadingLevel(block.level || 2),
                    content: block.content || block.text || '',
                    anchorId: block.anchorId || ''
                });
            }

            if (block.type === 'text') {
                return newBlock('text', { content: block.content || '' });
            }

            if (block.type === 'image') {
                return newBlock('image', {
                    src: block.src || block.url || block.image_url || '',
                    alt: block.alt || block.content || '',
                    caption: block.caption || ''
                });
            }

            return null;
        }

        function newBlock(type, overrides = {}) {
            return {
                id: overrides.id || createId(),
                type: type,
                level: clampHeadingLevel(overrides.level || 2),
                content: String(overrides.content || ''),
                anchorId: String(overrides.anchorId || ''),
                src: String(overrides.src || ''),
                alt: String(overrides.alt || ''),
                caption: String(overrides.caption || '')
            };
        }

        function createId() {
            if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                return window.crypto.randomUUID();
            }

            return 'block-' + Date.now().toString(36) + '-' + Math.random().toString(36).slice(2, 9);
        }

        function clampHeadingLevel(level) {
            level = Number(level);
            return [1, 2, 3].includes(level) ? level : 2;
        }

        function slugify(value, fallback) {
            const slug = String(value || '')
                .toLowerCase()
                .trim()
                .replace(/[^a-z0-9_-]+/g, '-')
                .replace(/^-+|-+$/g, '');

            return slug || fallback;
        }

        function uniqueAnchorId(value, used, fallback) {
            const base = slugify(value, fallback);
            let id = base;
            let counter = 2;

            while (used.has(id)) {
                id = base + '-' + counter;
                counter++;
            }

            used.add(id);
            return id;
        }

        function blockLabel(block) {
            if (block.type === 'heading') {
                return 'H' + block.level;
            }

            return block.type === 'image' ? 'Image' : 'Text';
        }

        function render() {
            blockList.innerHTML = '';

            blocks.forEach((block, index) => {
                blockList.appendChild(renderBlockRow(block, index));
            });

            updateJson();
        }

        function renderBlockRow(block, index) {
            const row = document.createElement('article');
            row.className = 'block-row block-row-' + block.type + (block.id === selectedId ? ' selected' : '');
            row.dataset.id = block.id;
            row.draggable = true;

            const controls = document.createElement('div');
            controls.className = 'block-controls';

            const handle = document.createElement('button');
            handle.type = 'button';
            handle.className = 'drag-handle';
            handle.title = 'Drag to reorder';
            handle.innerHTML = '<i class="fas fa-grip-vertical"></i>';
            controls.appendChild(handle);

            const label = document.createElement('span');
            label.className = 'block-label';
            label.textContent = blockLabel(block);
            controls.appendChild(label);

            if (block.type === 'heading') {
                const level = document.createElement('select');
                level.className = 'level-select';
                [1, 2, 3].forEach(value => {
                    const option = document.createElement('option');
                    option.value = String(value);
                    option.textContent = 'H' + value;
                    option.selected = block.level === value;
                    level.appendChild(option);
                });
                level.addEventListener('change', function() {
                    updateBlockField(index, 'level', clampHeadingLevel(this.value), true);
                    if (blocks[index] && blocks[index].level !== 1) {
                        blocks[index].anchorId = '';
                    }
                    render();
                });
                controls.appendChild(level);
            }

            controls.appendChild(controlButton('up', 'Move up', 'fas fa-arrow-up', () => moveBlock(index, -1)));
            controls.appendChild(controlButton('down', 'Move down', 'fas fa-arrow-down', () => moveBlock(index, 1)));
            controls.appendChild(controlButton('delete', 'Delete', 'fas fa-trash', () => deleteBlock(index)));

            const body = document.createElement('div');
            body.className = 'block-body';

            if (block.type === 'heading' || block.type === 'text') {
                const editable = document.createElement('div');
                editable.className = 'editable editable-' + block.type;
                editable.contentEditable = 'true';
                editable.dataset.placeholder = block.type === 'heading' ? 'Heading text' : 'Write text';
                editable.textContent = block.content;
                editable.addEventListener('focus', () => selectedId = block.id);
                editable.addEventListener('input', function() {
                    updateBlock(index, this.textContent);
                });
                editable.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        addBlockAfter(block.id, 'text');
                    }
                });
                body.appendChild(editable);

                if (block.type === 'heading' && block.level === 1) {
                    const anchor = document.createElement('input');
                    anchor.className = 'anchor-input';
                    anchor.type = 'text';
                    anchor.placeholder = 'anchorId, e.g. contact';
                    anchor.value = block.anchorId || slugify(block.content, '');
                    anchor.addEventListener('focus', () => selectedId = block.id);
                    anchor.addEventListener('input', function() {
                        updateBlockField(index, 'anchorId', this.value);
                    });
                    anchor.addEventListener('keydown', enterCreatesText(block.id));
                    body.appendChild(anchor);
                }
            }

            if (block.type === 'image') {
                body.appendChild(textInput('Image path', block.src, value => updateBlockField(index, 'src', value), block.id, 'public/img/logoHEYP.png'));
                body.appendChild(textInput('Alt text', block.alt, value => updateBlockField(index, 'alt', value), block.id, 'Image description'));
                body.appendChild(textInput('Caption', block.caption, value => updateBlockField(index, 'caption', value), block.id, 'Optional caption'));

                if (block.src.trim()) {
                    const preview = document.createElement('img');
                    preview.className = 'image-preview';
                    preview.src = resolvePreviewUrl(block.src);
                    preview.alt = block.alt || '';
                    body.appendChild(preview);
                }
            }

            row.appendChild(controls);
            row.appendChild(body);

            row.addEventListener('click', function() {
                selectedId = block.id;
                blockList.querySelectorAll('.block-row').forEach(item => item.classList.remove('selected'));
                row.classList.add('selected');
            });
            row.addEventListener('dragstart', function(event) {
                draggedId = block.id;
                event.dataTransfer.effectAllowed = 'move';
                row.classList.add('dragging');
            });
            row.addEventListener('dragend', function() {
                draggedId = null;
                row.classList.remove('dragging');
                blockList.querySelectorAll('.drag-over').forEach(item => item.classList.remove('drag-over'));
            });
            row.addEventListener('dragover', function(event) {
                event.preventDefault();
                row.classList.add('drag-over');
            });
            row.addEventListener('dragleave', function() {
                row.classList.remove('drag-over');
            });
            row.addEventListener('drop', function(event) {
                event.preventDefault();
                row.classList.remove('drag-over');
                reorderDraggedBlock(block.id);
            });

            return row;
        }

        function controlButton(name, title, icon, handler) {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'icon-button ' + name;
            button.title = title;
            button.innerHTML = '<i class="' + icon + '"></i>';
            button.addEventListener('click', function(event) {
                event.stopPropagation();
                handler();
            });
            return button;
        }

        function textInput(labelText, value, onChange, blockId, placeholder) {
            const label = document.createElement('label');
            label.className = 'inline-field';
            label.textContent = labelText;

            const input = document.createElement('input');
            input.type = 'text';
            input.value = value;
            input.placeholder = placeholder || '';
            input.addEventListener('focus', () => selectedId = blockId);
            input.addEventListener('input', function() {
                onChange(this.value);
            });
            input.addEventListener('keydown', enterCreatesText(blockId));
            label.appendChild(input);

            return label;
        }

        function enterCreatesText(blockId) {
            return function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    addBlockAfter(blockId, 'text');
                }
            };
        }

        function addBlockAfter(afterId, type, overrides = {}) {
            const block = newBlock(type, overrides);
            const index = blocks.findIndex(item => item.id === afterId);

            if (index === -1) {
                blocks.push(block);
            } else {
                blocks.splice(index + 1, 0, block);
            }

            selectedId = block.id;
            render();
            focusBlock(block.id);
        }

        function addBlock(type, overrides = {}) {
            addBlockAfter(selectedId, type, overrides);
        }

        function deleteBlock(index) {
            if (blocks.length <= 1) {
                blocks = [newBlock('heading', { level: 1, content: 'Trang Chủ', anchorId: 'home' })];
                selectedId = blocks[0].id;
                render();
                return;
            }

            if (index < 0 || index >= blocks.length) {
                return;
            }

            blocks.splice(index, 1);
            selectedId = blocks[Math.min(index, blocks.length - 1)].id;
            render();
        }

        function updateBlock(index, content) {
            updateBlockField(index, 'content', content);
        }

        function updateBlockField(index, field, value, skipJsonUpdate = false) {
            if (!blocks[index]) {
                return;
            }

            blocks[index][field] = value;
            selectedId = blocks[index].id;

            if (!skipJsonUpdate) {
                updateJson();
            }
        }

        function moveBlock(index, direction) {
            const nextIndex = index + direction;
            if (nextIndex < 0 || nextIndex >= blocks.length) {
                return;
            }

            const moved = blocks.splice(index, 1)[0];
            blocks.splice(nextIndex, 0, moved);
            selectedId = moved.id;
            render();
        }

        function reorderDraggedBlock(targetId) {
            if (!draggedId || draggedId === targetId) {
                return;
            }

            const from = blocks.findIndex(block => block.id === draggedId);
            const to = blocks.findIndex(block => block.id === targetId);
            if (from === -1 || to === -1) {
                return;
            }

            const moved = blocks.splice(from, 1)[0];
            blocks.splice(to, 0, moved);
            selectedId = moved.id;
            render();
        }

        function focusBlock(id) {
            const row = blockList.querySelector('[data-id="' + id + '"]');
            if (!row) {
                return;
            }

            const editable = row.querySelector('[contenteditable="true"], input');
            if (editable) {
                editable.focus();
            }
        }

        function resolvePreviewUrl(src) {
            src = String(src || '').trim();
            if (/^(https?:)?\/\//.test(src) || src.startsWith('data:') || src.startsWith('/')) {
                return src;
            }

            if (src.startsWith('public/')) {
                return <?php echo json_encode(URL_ROOT); ?> + '/' + src;
            }

            return <?php echo json_encode(URL_ROOT); ?> + '/public/img/' + src;
        }

        function buildPage() {
            const sections = [];
            const usedAnchors = new Set();
            let currentSection = null;

            blocks.forEach((block, index) => {
                if (block.type === 'heading' && block.level === 1) {
                    const anchorId = uniqueAnchorId(block.anchorId || block.content, usedAnchors, 'section-' + (sections.length + 1));
                    currentSection = {
                        id: anchorId,
                        heading: {
                            level: 1,
                            text: block.content || 'Untitled',
                            anchorId: anchorId
                        },
                        blocks: []
                    };
                    sections.push(currentSection);
                    return;
                }

                if (!currentSection) {
                    const anchorId = uniqueAnchorId('home', usedAnchors, 'home');
                    currentSection = {
                        id: anchorId,
                        heading: {
                            level: 1,
                            text: 'Trang Chủ',
                            anchorId: anchorId
                        },
                        blocks: []
                    };
                    sections.push(currentSection);
                }

                if (block.type === 'heading') {
                    if (block.content.trim()) {
                        currentSection.blocks.push({
                            type: 'heading',
                            level: clampHeadingLevel(block.level),
                            content: block.content
                        });
                    }
                    return;
                }

                if (block.type === 'text') {
                    if (block.content.trim()) {
                        currentSection.blocks.push({
                            type: 'text',
                            content: block.content
                        });
                    }
                    return;
                }

                if (block.type === 'image' && block.src.trim()) {
                    currentSection.blocks.push({
                        type: 'image',
                        src: block.src,
                        alt: block.alt,
                        caption: block.caption
                    });
                }
            });

            if (!sections.length) {
                sections.push({
                    id: 'home',
                    heading: { level: 1, text: 'Trang Chủ', anchorId: 'home' },
                    blocks: []
                });
            }

            return {
                header: page.header || defaultHeader,
                sections: sections
            };
        }

        function updateJson() {
            const output = JSON.stringify(buildPage(), null, 2);
            jsonPreview.textContent = output;
            jsonInput.value = output;
        }

        document.querySelectorAll('[data-add-block]').forEach(button => {
            button.addEventListener('click', function() {
                const type = this.dataset.addBlock;
                const level = Number(this.dataset.level || 2);
                const defaults = type === 'heading'
                    ? { level: level, content: level === 1 ? 'New section' : 'New heading' }
                    : (type === 'image' ? { src: 'public/img/logoHEYP.png', alt: 'Image' } : { content: '' });
                addBlock(type, defaults);
            });
        });

        appendTextBlock.addEventListener('click', () => addBlock('text', { content: '' }));

        form.addEventListener('submit', function() {
            jsonInput.value = JSON.stringify(buildPage(), null, 2);
        });

        render();
    });
    </script>

    <style>
    :root {
        --sidebar: #243528;
        --accent: #5A6B00;
        --text: #243528;
        --muted: #667085;
        --line: #d9e0d5;
        --surface: #f7f9f4;
    }

    * {
        box-sizing: border-box;
    }

    body {
        margin: 0;
        background: #eef3ea;
        color: var(--text);
        font-family: 'Open Sans', Arial, sans-serif;
    }

    button,
    input,
    select {
        font: inherit;
    }

    .admin-shell {
        display: flex;
        align-items: stretch;
        min-height: 100vh;
    }

    .admin-sidebar {
        flex: 0 0 260px;
        background: var(--sidebar);
        color: #ffffff;
        padding: 24px 18px;
    }

    .brand {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 32px;
    }

    .brand img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        background: #ffffff;
    }

    .admin-nav {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .admin-nav a {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
        text-decoration: none;
        padding: 12px;
        border-radius: 8px;
    }

    .admin-nav a.active,
    .admin-nav a:hover {
        background: rgba(255, 255, 255, 0.14);
    }

    .editor-page {
        flex: 1 1 auto;
        padding: 28px;
        min-width: 0;
    }

    .alert {
        padding: 12px 14px;
        border-radius: 8px;
        margin-bottom: 16px;
        border: 1px solid var(--line);
        background: #ffffff;
    }

    .alert.success {
        border-color: #9bc27d;
        color: #30551d;
    }

    .alert.error {
        border-color: #e08b8b;
        color: #8a1f1f;
    }

    #blockEditorForm {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .editor-header,
    .toolbar,
    .document-panel,
    .json-panel {
        background: #ffffff;
        border: 1px solid var(--line);
        border-radius: 8px;
    }

    .editor-header {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding: 22px;
    }

    .eyebrow,
    .note {
        margin: 0;
        color: var(--muted);
    }

    .editor-header h1 {
        margin: 4px 0 8px;
        font-family: Montserrat, Arial, sans-serif;
        font-size: 28px;
    }

    .header-actions,
    .toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .toolbar {
        padding: 12px;
    }

    .toolbar button,
    .save-button,
    .secondary-link,
    .append-block,
    .icon-button,
    .drag-handle {
        border: 1px solid var(--line);
        background: #ffffff;
        color: var(--text);
        border-radius: 8px;
        padding: 9px 12px;
        text-decoration: none;
        cursor: pointer;
    }

    .save-button {
        background: var(--accent);
        border-color: var(--accent);
        color: #ffffff;
    }

    .workspace {
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }

    .document-panel {
        flex: 1 1 auto;
        min-width: 0;
        padding: 18px;
    }

    .block-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .block-row {
        display: flex;
        flex-direction: column;
        gap: 14px;
        padding: 14px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: var(--surface);
        position: static;
        overflow: visible;
    }

    .block-row.selected {
        border-color: var(--accent);
        box-shadow: 0 0 0 2px rgba(90, 107, 0, 0.12);
    }

    .block-row.dragging {
        opacity: 0.55;
    }

    .block-row.drag-over {
        border-color: var(--accent);
    }

    .block-controls {
        display: flex;
        align-items: center;
        align-content: flex-start;
        gap: 8px;
        flex-wrap: wrap;
    }

    .block-label {
        min-width: 44px;
        font-weight: 700;
        color: var(--accent);
    }

    .level-select {
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 8px;
        background: #ffffff;
    }

    .icon-button,
    .drag-handle {
        width: 36px;
        height: 36px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .icon-button.delete {
        color: #9b1c1c;
    }

    .block-body {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 0;
    }

    .editable {
        display: block;
        min-height: 44px;
        padding: 10px 12px;
        border: 1px solid var(--line);
        border-radius: 8px;
        background: #ffffff;
        line-height: 1.6;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
        outline: none;
    }

    .editable-heading {
        font-family: Montserrat, Arial, sans-serif;
        font-size: 22px;
        font-weight: 700;
    }

    .editable:empty::before {
        content: attr(data-placeholder);
        color: #98a28f;
    }

    .anchor-input,
    .inline-field input {
        width: 100%;
        border: 1px solid var(--line);
        border-radius: 8px;
        padding: 10px 12px;
        background: #ffffff;
    }

    .inline-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
        color: var(--muted);
        font-size: 13px;
    }

    .image-preview {
        display: block;
        width: min(100%, 420px);
        height: auto;
        border-radius: 8px;
        border: 1px solid var(--line);
        background: #ffffff;
    }

    .append-block {
        width: 100%;
        margin-top: 14px;
        color: var(--accent);
        font-weight: 700;
    }

    .json-panel {
        flex: 0 0 360px;
        padding: 18px;
    }

    .json-panel h2 {
        margin: 0 0 8px;
        font-size: 18px;
    }

    .json-panel p {
        margin: 0 0 14px;
        color: var(--muted);
    }

    .json-panel pre {
        margin: 0;
        padding: 14px;
        max-height: 70vh;
        overflow: auto;
        border-radius: 8px;
        background: #18231a;
        color: #eaf2e4;
        font-size: 12px;
        line-height: 1.5;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    @media (max-width: 1000px) {
        .admin-shell {
            display: block;
        }

        .admin-sidebar {
            display: none;
        }

        .workspace {
            flex-direction: column;
        }

        .json-panel {
            flex-basis: auto;
            width: 100%;
        }
    }

    @media (max-width: 720px) {
        .editor-page {
            padding: 14px;
        }

        .editor-header {
            flex-direction: column;
        }
    }
    </style>
</body>
</html>
