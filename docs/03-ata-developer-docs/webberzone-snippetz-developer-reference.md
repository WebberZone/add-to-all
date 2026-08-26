---
slug: webberzone-snippetz-developer-reference
title: "WebberZone Snippetz Developer Reference"
products: [add-to-all]
sections: ["03-ata-developer-docs"]
tags: [add-to-all, api, developer, hooks, php]
status: publish
order: 0
toc: true
---

[toc]

This reference covers the PHP functions, filter hooks, and action hooks exposed by [WebberZone Snippetz](https://webberzone.com/plugins/add-to-all/) for use in themes and plugins.

All functions are in the `WebberZoneSnippetzSnippetsFunctions` class. All hook names are prefixed `ata_`.

## PHP functions

### `Functions::get_snippet_content( $snippet, $args )`

Returns the rendered output for a snippet.

```php
use WebberZoneSnippetzSnippetsFunctions;

$html = Functions::get_snippet_content( 42 );
echo $html;
```

**Parameters:**

- `$snippet` *(int|WP_Post)* — Snippet ID or post object.
- `$args` *(array, optional)* — Output arguments.
    - `class` *(string)* — Extra CSS class on the wrapper `<div>`. Default `''`.
    - `is_block` *(bool)* — Mark output as block-rendered. Adds `ata_snippet_block` class. Default `false`.
    - `is_shortcode` *(bool)* — Mark output as shortcode-rendered. Adds `ata_snippet_shortcode` class. Default `true`.

**Returns:** `string` — Rendered snippet HTML, or an error string if the ID is invalid.

---

### `Functions::get_snippets_by_location( $location, $numberposts )`

Returns all active snippets assigned to a given location.

```php
$snippets = Functions::get_snippets_by_location( 'header' );
foreach ( $snippets as $snippet ) {
    echo Functions::get_snippet_content( $snippet );
}
```

**Parameters:**

- `$location` *(string)* — One of `header`, `footer`, `content_before`, `content_after`.
- `$numberposts` *(int, optional)* — Maximum number to return. Default `-1` (all).

**Returns:** `WP_Post[]|int[]|false` — Array of snippet post objects, or `false` if location is invalid.

---

### `Functions::get_header_snippets( $numberposts )`

Shorthand for `get_snippets_by_location( 'header' )`.

---

### `Functions::get_footer_snippets( $numberposts )`

Shorthand for `get_snippets_by_location( 'footer' )`.

---

### `Functions::get_content_before_snippets( $numberposts )`

Shorthand for `get_snippets_by_location( 'content_before' )`.

---

### `Functions::get_content_after_snippets( $numberposts )`

Shorthand for `get_snippets_by_location( 'content_after' )`.

---

### `Functions::get_snippets( $args )`

Query snippets using `get_posts()` arguments.

```php
$snippets = Functions::get_snippets( array(
    'numberposts' => 5,
    'include'     => array( 10, 20, 30 ),
) );
```

**Parameters:**

- `$args` *(array)* — Accepts `numberposts`, `include`, `exclude`, and any `WP_Query` argument. `post_type` is always forced to `ata_snippets`.
