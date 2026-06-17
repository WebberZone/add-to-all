---
slug: external-css-js-files
title: "External CSS and JS Files"
products: [add-to-all]
sections: [02-ata-advanced]
tags: [add-to-all, performance, css, javascript, minification]
status: publish
order: 0
---

[kbtoc]

By default, [WebberZone Snippetz](https://webberzone.com/plugins/add-to-all/) injects CSS and JavaScript snippets as inline styles and scripts using the WordPress enqueue system (`wp_add_inline_style` / `wp_add_inline_script`). The **External CSS/JS files** feature saves those snippets as minified flat files and serves them from the uploads directory instead.

## Enabling external files

1. Go to **Snippetz → Settings → General**.
2. Enable **Enable external CSS/JS files**.
3. Click **Save Changes**.

From this point, each CSS or JS snippet is saved as a minified file in `wp-content/uploads/snippetz/` the next time it is published or updated. Existing snippets are converted on their next save.

## How it works

When a CSS or JS snippet is saved, Snippetz minifies the content using the `matthiasmullie/minify` library and writes the output to:

```text
wp-content/uploads/snippetz/snippet-{id}.min.{css|js}
```

On the front end, the file is registered and enqueued with a version derived from the file's modification timestamp, so browsers cache the file until it changes.

The snippet metabox displays the generated file URL and size after the file has been created.

## File combination

The **Enable file combination** option merges all CSS snippets into one file and all JS snippets into one file:

```text
wp-content/uploads/snippetz/combined.min.css
wp-content/uploads/snippetz/combined.min.js
```

The combined files are enqueued on every page load.

**Important:** When file combination is active, per-snippet display conditions are ignored. A snippet set to show only on specific post types or IDs will appear on all pages when combination is enabled. File combination works independently — enabling it does not require **Enable external CSS/JS files** to also be on. Use combination only when all your CSS or JS snippets should load globally.

## When to use external files

Use external files when:

- Your CSS or JS snippets are large enough that inline output adds measurable weight to each page response.
- You want browser caching to apply to snippet output.
- Your caching plugin or CDN handles external assets better than inline output.

Keep the default inline mode when:

- Snippets are small and load order relative to other inline styles matters.
- You use per-snippet conditions to target specific pages and need those conditions to apply to CSS/JS output.

## Regenerating files

Files are regenerated automatically when a snippet is saved. To force regeneration for all snippets at once, go to **Snippetz → Tools** (or **Tools → Snippetz Tools** when the Snippets Manager is disabled) and click **Regenerate Assets**. Deleting a snippet also deletes its generated file.
