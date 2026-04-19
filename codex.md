# Codex Project Guide

## 1. Project Overview

- Repository: https://github.com/Nhimcon2608/heypvietnam
- Tech stack: PHP server-rendered pages, MySQL on XAMPP, HTML/CSS/JS.
- Current: PHP website with static sections and hardcoded HTML content.
- Problem:
  - Content is not dynamic.
  - Layout/content cannot be edited without code changes.

## 2. Refactor Goal

- Keep the existing PHP architecture and deployed hosting model.
- Convert static content into dynamic JSON-driven content stored in MySQL.
- Build a Word-like landing page builder, similar to WordPress editor behavior:
  - Vertical document flow.
  - No overlapping elements.
  - Content pushes following content down as it grows.
- Generate header navigation dynamically from H1 headings.

## 3. Core Concepts

- Page: a full landing page, including header settings and ordered sections.
- Section: a grouped vertical area of content blocks.
- Block: the smallest renderable unit, such as text, heading, or image.
- Heading:
  - H1: main section heading and source for menu navigation.
  - H2/H3: content structure only.

## 4. Data Structure (IMPORTANT)

Use a MySQL `JSON` column for page content.

```json
{
  "header": {
    "logo": "logo.png"
  },
  "sections": [
    {
      "id": "contact",
      "heading": {
        "level": 1,
        "text": "Contact",
        "anchorId": "contact"
      },
      "blocks": [
        {
          "type": "text",
          "content": "Email: example@gmail.com"
        }
      ]
    }
  ]
}
```

## 5. Rendering Architecture (PHP)

PHP reads JSON from MySQL, decodes it, then renders sections and blocks.

```php
$page = json_decode($row['content'], true);
```

Render rules:

- Loop sections.
- Loop blocks inside each section.
- Render each block type with a small PHP template/helper.
- Escape user-editable output before echoing.

```php
foreach ($page['sections'] as $section) {
    echo "<section id='{$section['id']}'>";

    foreach ($section['blocks'] as $block) {
        if ($block['type'] === 'text') {
            echo "<p>{$block['content']}</p>";
        }
    }

    echo "</section>";
}
```

## 6. Layout Rules (CRITICAL)

- Use vertical flow only:
  - `display: block`
  - `display: flex`
  - `flex-direction: column`
- Do not use:
  - `position: absolute` for content layout.
  - overlapping elements.
  - free drag/pixel-based positioning.
- Content must behave like Microsoft Word:
  - More content increases block height.
  - Later blocks automatically move down.
  - No collisions between blocks or sections.

## 7. Editor Behavior (Admin Page)

- Each block is one row.
- Press Enter to create a new block.
- Supported blocks:
  - heading: H1, H2, H3
  - text
  - image
- Allow:
  - add block
  - delete block
  - reorder block
- Optional:
  - drag and drop reordering with JavaScript.

## 8. Header Navigation

- Header is fixed/sticky.
- Logo reloads the page.
- Home button scrolls to top.
- Menu is generated from H1 headings.
- H1 `anchorId` values become section navigation targets.

```js
document.getElementById("contact").scrollIntoView({
  behavior: "smooth"
});
```

## 9. Database Design

Table: `pages`

```sql
CREATE TABLE pages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255),
  slug VARCHAR(255),
  content JSON
);
```

## 10. What to Avoid

- Hardcoded HTML sections.
- Inline content in PHP files.
- Absolute positioning layouts.
- Free drag or pixel-based positioning.
- Rewriting the project from scratch.
- Changing the PHP framework or hosting model.

## 11. Development Priority

1. Convert homepage into JSON structure.
2. Build PHP renderer from JSON.
3. Implement header navigation.
4. Build admin editor.
5. Save/load JSON from MySQL.

## Constraints

- Do not change the PHP framework.
- Do not rewrite the project from scratch.
- Reuse existing UI/CSS if possible.
- Keep code simple and maintainable.
- Optimize for minimal token usage.
