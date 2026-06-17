---
slug: webberzone-snippetz-settings
title: "WebberZone Snippetz Settings"
products: [add-to-all]
sections: [01-ata-getting-started]
tags: [add-to-all, settings, snippetz]
status: publish
order: 0
---

[kbtoc]

This document covers all settings in [WebberZone Snippetz](https://webberzone.com/plugins/add-to-all/). Access settings via **Snippetz → Settings** when the Snippets Manager is enabled, or via **Settings → Snippetz** when it is disabled.

## General

### Enable Snippets Manager

Enables the Snippets Manager, which registers the `ata_snippets` custom post type and lets you create named, reusable snippets with per-snippet conditions and location controls. Disabling this hides the Snippets menu but does not delete any existing snippet data.

**Default:** Enabled

### Enable external CSS/JS files

Save CSS and JS snippets as minified external files under `wp-content/uploads/snippetz/` and serve them via `wp_enqueue_style` / `wp_enqueue_script` instead of inline output. See [External CSS and JS Files](https://webberzone.com/support/knowledgebase/external-css-js-files/) for details.

**Default:** Disabled

### Enable file combination

Combine all CSS snippets into one file and all JS snippets into one file, then enqueue the combined files. When enabled, per-snippet display conditions are ignored — the combined file loads on every page. Works independently of **Enable external CSS/JS files**.

**Default:** Disabled

### Snippet content priority

WordPress hook priority used when injecting snippet content. A lower number injects earlier relative to other `the_content` filters. Values below 10 are not recommended. Individual snippets can override this with their own **Priority** field.

**Default:** `999`

## Third Party

### StatCounter

Enter your StatCounter **Project ID** and **Security ID** to add the StatCounter tracking script to your site header. Find both values in your StatCounter account under the tracking code for your project.

### Google Analytics

Enter your **Tracking ID** (Google Tag ID, e.g. `G-XXXXXXXXXX`) to inject the Google Analytics tracking snippet into your site header.

### Site verification

Add site verification meta tags to `<head>` for each service:

| Field | Service |
|---|---|
| **Google** | Value of the `content` attribute in the Google Search Console HTML tag method |
| **Bing** | Value of the `content` attribute in the Bing Webmaster Tools HTML tag method |
| **Meta** | Value of the `content` attribute in the Meta (Facebook) domain verification tag |
| **Pinterest** | Value of the `content` attribute in the Pinterest meta tag method |

Leave a field empty to omit that tag.

## Header

### Custom CSS

CSS added here is injected into `<head>` via `wp_head()`. Enter rules without `<style>` tags.

### HTML to add to the header

Raw HTML or JavaScript injected into `<head>` via `wp_head()`. Enter valid HTML; JavaScript must be wrapped in `<script>` tags.

## Body

### Opening Body Tag

**HTML to add to `wp_body_open()`** — HTML injected immediately after the opening `<body>` tag via `wp_body_open()`. Requires your theme to call `wp_body_open()`. Useful for tag manager noscripts and other body-open snippets.

### Content settings

**Content filter priority** — Priority of the `the_content` filter that injects HTML before and after post content. Default: `999`.

**Exclude display on these post IDs** — Comma-separated list of post or page IDs. Content-area HTML from this tab is suppressed on these IDs.

**Process shortcodes in content** — When enabled, shortcodes in the HTML fields below are executed before output.

### Home and other views

Displays on home, category, tag, and other archive views as well as single posts.

**Add HTML before content** / **HTML to add before the content** — Toggle and HTML field. Content is prepended to `the_content`.

**Add HTML after content** / **HTML to add after the content** — Toggle and HTML field. Content is appended to `the_content`.

### Single posts views

Displays on single posts, pages, and custom post types only.

Same before/after toggle and HTML field pair as above, scoped to `is_singular()`.

### Post only views

Displays only on standard posts (`post_type = post`).

Same before/after toggle and HTML field pair, scoped to `is_single()`.

### Page only views

Displays only on standard pages (`post_type = page`).

Same before/after toggle and HTML field pair, scoped to `is_page()`.

## Footer

**Process shortcodes in footer** — When enabled, shortcodes in the footer HTML field are executed before output.

**HTML to add to the footer** — HTML injected just before `</body>` via `wp_footer()`. JavaScript must be wrapped in `<script>` tags.

## Feed

**Add copyright notice** — Appends a copyright notice to every feed item. Enabled by default.

**Copyright text** — HTML added as the last item in your feed. Supports placeholder tokens: `%year%`, `%first_year%`. Default text includes your site name, home URL, and admin email.

**Add post title** — Prepends a linked title line to each feed item. Enabled by default.

**Title text** — Template for the title line. Supports `%title%` (linked post title), `%date%`, `%time%`, `%updated_time%`.

**Process shortcodes in feed** — When enabled, shortcodes in the feed HTML fields are executed before output.

**Add HTML before content** / **HTML to add before the content** — Toggle and HTML field injected before each feed item's content.

**Add HTML after content** / **HTML to add after the content** — Toggle and HTML field injected after each feed item's content.

**Add a link to "WebberZone Snippetz" plugin page** — Adds a small credit link in the feed. Disabled by default.
