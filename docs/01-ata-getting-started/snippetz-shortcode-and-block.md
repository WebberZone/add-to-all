---
slug: snippetz-shortcode-and-block
title: "Snippetz Shortcode and Block"
products: [add-to-all]
sections: [01-ata-getting-started]
tags: [add-to-all, shortcode, block, snippetz]
status: publish
order: 0
---

[WebberZone Snippetz](https://webberzone.com/plugins/add-to-all/) provides three ways to embed a named snippet inline inside post or page content: a shortcode, a Gutenberg block, and an Insert Snippet button for the Classic Editor.

All three require the Snippets Manager to be enabled (**Snippetz → Settings → General → Enable Snippets Manager**) and a published snippet to exist.

## `[ata_snippet]` shortcode

```text
[ata_snippet id="123"]
```

Outputs the content of the snippet with ID `123` at the shortcode location.

**Parameters:**

| Parameter | Type | Description |
|---|---|---|
| `id` | number | ID of the snippet post to embed. Required. |
| `class` | string | Extra CSS class added to the wrapper `<div>`. Default empty. |

HTML snippets are wrapped in `<div class="ata_snippet ata_snippet_{id} ata_snippet_shortcode {class}">`. CSS and JS snippets output their content without the wrapper div.

```text
[ata_snippet id="42" class="my-custom-class"]
```

## WebberZone Snippetz block

The **WebberZone Snippetz** block (`webberzone/snippetz`) is available in the block inserter under **Widgets**.

Select the snippet to embed from the dropdown in the block sidebar. The block renders the snippet content in the editor preview and on the front end.

**Block attributes:**

| Attribute | Type | Description |
|---|---|---|
| `snippetId` | number | ID of the snippet post to embed. Default `0`. |
| `className` | string | Extra CSS class on the block wrapper. |

CSS and JS snippets embedded via the block are enqueued using the standard WordPress enqueue system — the block itself outputs nothing visible for those types.

## Insert Snippet button (Classic Editor)

When the Classic Editor is active, an **Insert Snippet** button appears in the editor toolbar on post and page edit screens. The button is not shown on the snippet editing screen itself, and it is not available in the block editor.

Clicking the button opens a modal where you can choose from any published snippet. After selecting a snippet and clicking **Insert Snippet**, the shortcode `[ata_snippet id="..."]` is inserted at the cursor position in the editor.

This requires at least one published snippet in the Snippets Manager. If no snippets exist, the dropdown will be empty.

## `[ata_reading_time]` shortcode

Outputs the estimated reading time of a post.

```text
[ata_reading_time]
[ata_reading_time wpm="250" before="Reading time: " after=" min"]
```

**Parameters:**

| Parameter | Type | Description |
|---|---|---|
| `post` | number | Post ID to calculate reading time for. Defaults to the current post. |
| `wpm` | number | Words per minute reading speed. Default `200`. |
| `before` | string | Text prepended to the reading time string. Default empty. |
| `after` | string | Text appended to the reading time string. Default empty. |

Output is wrapped in `<span class="ata_reading_time ata_reading_time_{post_id}">`. Reading time is calculated after stripping HTML tags and shortcodes.
