---
slug: webberzone-snippetz-developer-reference
title: "WebberZone Snippetz Developer Reference"
products: [add-to-all]
sections: [03-ata-developer-docs]
tags: [add-to-all, developer, hooks, php, api]
status: publish
order: 0
---

[kbtoc]

This reference covers the PHP functions, filter hooks, and action hooks exposed by [WebberZone Snippetz](https://webberzone.com/plugins/add-to-all/) for use in themes and plugins.

All functions are in the `WebberZone\Snippetz\Snippets\Functions` class. All hook names are prefixed `ata_`.

## PHP functions

### `Functions::get_snippet_content( $snippet, $args )`

Returns the rendered output for a snippet.

```php
use WebberZone\Snippetz\Snippets\Functions;

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

**Returns:** `WP_Post[]|int[]`

## Filter hooks

### `ata_get_snippet_content`

Filter the rendered output of a single snippet.

```php
add_filter( 'ata_get_snippet_content', function( string $output, \WP_Post $snippet, array $args ): string {
    return $output;
}, 10, 3 );
```

**Parameters:**

- `$output` *(string)* — Rendered snippet HTML.
- `$snippet` *(WP_Post)* — Snippet post object.
- `$args` *(array)* — Arguments passed to `get_snippet_content()`.

---

### `ata_get_snippets_by_location`

Filter the array of snippets returned for a location.

```php
add_filter( 'ata_get_snippets_by_location', function( array $snippets, string $location, int $numberposts ): array {
    return $snippets;
}, 10, 3 );
```

---

### `ata_get_snippets_args`

Filter the `get_posts()` arguments before snippets are queried.

```php
add_filter( 'ata_get_snippets_args', function( array $args ): array {
    $args['orderby'] = 'title';
    return $args;
} );
```

---

### `ata_get_snippets`

Filter the raw array of snippets returned by `get_posts()`.

```php
add_filter( 'ata_get_snippets', function( array $snippets, array $args ): array {
    return $snippets;
}, 10, 2 );
```

---

### `ata_get_snippet`

Filter an individual snippet post object.

```php
add_filter( 'ata_get_snippet', function( \WP_Post $snippet, $input ): \WP_Post {
    return $snippet;
}, 10, 2 );
```

---

### `ata_placeholders`

Add or modify placeholder tokens used in copyright notices and other text fields. See [Feed Customization and Placeholders](https://webberzone.com/support/knowledgebase/feed-customization-and-placeholders/) for the full token list.

```php
add_filter( 'ata_placeholders', function( array $placeholders ): array {
    $placeholders['%author%'] = get_the_author();
    return $placeholders;
} );
```

---

### `ata_process_placeholders`

Filter the final string after all placeholder tokens have been replaced.

```php
add_filter( 'ata_process_placeholders', function( string $output, string $input, array $placeholders ): string {
    return $output;
}, 10, 3 );
```

---

### `ata_get_reading_time`

Filter the calculated reading time string.

```php
add_filter( 'ata_get_reading_time', function( string $time, string $content, array $args ): string {
    // Return only minutes, no seconds.
    return explode( ',', $time )[0];
}, 10, 3 );
```

---

### `ata_feed_title_text`

Filter the rendered feed title line after placeholder substitution.

```php
add_filter( 'ata_feed_title_text', function( string $output ): string {
    return $output;
} );
```

---

### `ata_creditline`

Filter the credit link appended to the feed.

```php
add_filter( 'ata_creditline', function( string $output ): string {
    return ''; // Remove credit line.
} );
```

---

### `ata_snippets_credit`

Filter the HTML comment credit marker (`<!-- Snippets by WebberZone Snippetz -->`) injected into snippet output.

```php
add_filter( 'ata_snippets_credit', function( string $credit ): string {
    return '';
} );
```

---

### `ata_{$option}`

Filter the HTML output for a specific settings field just before it is injected into the page. `$option` is the settings key. Applies to HTML and CSS output fields: `head_css`, `head_other_html`, `wp_body_open`, `footer_other_html`, `content_html_before`, `content_html_after`, `content_html_before_single`, `content_html_after_single`, `content_html_before_post`, `content_html_after_post`, `content_html_before_page`, `content_html_after_page`.

```php
// Append a comment to the custom CSS before it is output.
add_filter( 'ata_head_css', function( string $css ): string {
    return $css . "\n/* added by theme */";
} );
```

## Action hooks

### `ata_site_verification`

Fires inside `wp_head()` after the built-in site verification meta tags have been output. Use this to add additional verification tags.

```php
add_action( 'ata_site_verification', function(): void {
    echo '<meta name="custom-verify" content="abc123" />' . "\n";
} );
```

---

### `ata_tools_page_header`

Fires at the top of the Snippetz Tools page, inside the page wrapper.

---

### `ata_admin_tools_page_content`

Fires in the main content area of the Snippetz Tools page, below the built-in tool cards. Use this to register custom tool cards or notices.

```php
add_action( 'ata_admin_tools_page_content', function(): void {
    echo '<div class="card"><h2>My Custom Tool</h2><p>...</p></div>';
} );
```
