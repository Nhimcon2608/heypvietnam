# Codex Project Guide

## 1. Project Overview

- Repository: https://github.com/Nhimcon2608/heypvietnam
- Stack: PHP server-rendered pages, MySQL on XAMPP, HTML/CSS/JS.
- Current state: static PHP website with hardcoded HTML sections and inline content.
- Problem: content is not dynamic, and layout/content changes require code edits.

## 2. Refactor Goal

- Keep the existing PHP architecture and deployed hosting assumptions.
- Convert static content into dynamic JSON-driven content stored in MySQL.
- Build a Word-like landing page builder, similar to WordPress editor behavior:
  - Vertical document flow.
  - No overlapping elements.
  - Content pushes following content down as it grows.
- Generate header navigation dynamically from H1 headings.

## 3. Core Concepts

- Page: a full landing page, including header settings and ordered sections.
- Section: a grouped vertical area of content blocks.
- Block: the smallest renderable content unit, such as heading, text, or image.
- Heading:
  - H1: main section heading and menu navigation source.
  - H2/H3: content structure only.

## 4. Data Structure

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

## 5. Rendering Architecture

PHP reads JSON from MySQL, decodes it, then renders sections and blocks.

```php
$page = json_decode($row['content'], true);
```

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

Renderer rules:

- Loop through `sections`.
- Inside each section, loop through `blocks`.
- Render block types with simple PHP templates or small helper functions.
- Escape output before rendering user-editable content.

## 6. Layout Rules

- Use vertical flow only: `display: block`, `display: flex`, `flex-direction: column`.
- Do not use `position: absolute` for content layout.
- Do not allow overlapping elements.
- Content must behave like Microsoft Word:
  - More text increases block height.
  - Later blocks automatically move down.
  - No collisions between sections or blocks.

## 7. Editor Behavior

- Admin editor treats each block as one row.
- Press Enter to create a new block.
- Supported block types:
  - heading: H1, H2, H3
  - text
  - image
- Required actions:
  - Add block
  - Delete block
  - Reorder block
- Optional: drag and drop reordering with JavaScript.

## 8. Header Navigation

- Header is sticky/fixed.
- Logo reloads the page.
- Home button scrolls to top.
- Menu items are generated from H1 headings.
- H1 heading `anchorId` values become section navigation targets.

```js
document.getElementById("contact").scrollIntoView({
  behavior: "smooth"
});
```

## 9. Database Design

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

1. Convert homepage content into JSON structure.
2. Build PHP renderer from JSON.
3. Implement dynamic header navigation.
4. Build admin editor.
5. Save and load JSON from MySQL.

## Constraints

- Do not change the PHP framework.
- Do not rewrite the project from scratch.
- Reuse existing UI/CSS where practical.
- Keep code simple and maintainable.
- Optimize future agent context for minimal token usage.
