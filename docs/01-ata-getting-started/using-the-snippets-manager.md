---
slug: using-the-snippets-manager
title: "Using the Snippets Manager"
products: [add-to-all]
sections: [01-ata-getting-started]
tags: [add-to-all, snippets, snippetz, conditions]
status: publish
order: 0
---

[kbtoc]

The [WebberZone Snippetz](https://webberzone.com/plugins/add-to-all/) Snippets Manager lets you create named, reusable code snippets — HTML, CSS, or JavaScript — and control exactly where each one appears on your site. Each snippet is a custom post type entry with its own location settings, display conditions, and priority.

The Snippets Manager is enabled by default. If it is turned off, go to **Settings → Snippetz → General** and enable **Enable Snippets Manager**.

## Creating a snippet

1. Navigate to **Snippetz → Add New Snippet** in your WordPress admin.
2. Enter a title to identify the snippet (not shown publicly).
3. Paste your code into the editor.
4. Set the **Snippet Type** in the metabox (HTML, JavaScript, or CSS).
5. Configure location, conditions, and priority (see sections below).
6. Click **Publish**.

## Snippet types

| Type | Output method |
|---|---|
| **HTML** | Wrapped in `<div class="ata_snippet ata_snippet_{id}">` and output inline |
| **CSS** | Enqueued via `wp_add_inline_style` (or as an external file when external files are enabled) |
| **JavaScript** | Enqueued via `wp_add_inline_script` in the footer (or as an external file when external files are enabled) |

Changing the snippet type after saving updates the editor's syntax highlighting mode. Save the post once after changing the type to apply the new mode.

## Step 1: Where to display this

Each snippet can be injected into one or more locations independently:

| Option | Where it appears |
|---|---|
| **Add to Header** | `wp_head()` — inside `<head>` |
| **Add to Footer** | `wp_footer()` — just before `</body>` |
| **Add before Content** | Prepended to `the_content` on posts matching the conditions below |
| **Add after Content** | Appended to `the_content` on posts matching the conditions below |

A snippet can be assigned to multiple locations simultaneously.

## Step 2: Conditions

Conditions control which posts or pages a snippet's content-area output (before/after content) applies to. Header and footer output ignores conditions — those locations always inject globally.

Leave all conditions blank to suppress content-area output entirely. Set at least one condition to restrict where the snippet appears.

### Logical relationship

Choose **OR** (show when any one condition matches) or **AND** (show only when all conditions match).

**Default:** OR

### Available conditions

| Condition | Description |
|---|---|
| **Include on these post types** | Checkboxes for registered public post types |
| **Include on these Post IDs** | Comma-separated post, page, or custom post type IDs |
| **Include on these Categories** | Comma-separated category slugs (autocomplete supported) |
| **Include on these Tags** | Comma-separated tag slugs (autocomplete supported) |

Custom taxonomies are not supported by the category and tag condition fields.

## Step 3: Priority

Controls the order in which snippets are injected relative to each other. Lower numbers inject earlier. At the same priority, snippets are ordered by post ID (ascending).

**Default:** `10`

The global **Snippet content priority** setting in **Settings → General** sets the WordPress hook priority for all snippet injection. Individual snippet priorities operate within that hook at a finer level.

## Disabling a snippet

Check **Disable Snippet** in the metabox to temporarily suppress a snippet without deleting it. The snippet data is preserved and the setting can be reversed at any time.

## Embedding snippets inline

Use the `[ata_snippet id=""]` shortcode or the **WebberZone Snippetz** block to embed a snippet directly inside post or page content. See [Snippetz Shortcode and Block](https://webberzone.com/support/knowledgebase/snippetz-shortcode-and-block/) for details.

## Organizing snippets

Snippets support a **Categories** taxonomy (`ata_snippets_category`) for grouping related snippets. Categories are shown as a column in the snippets list and can be used to filter the list view.
