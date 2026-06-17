---
slug: feed-customization-and-placeholders
title: "Feed Customization and Placeholders"
products: [add-to-all]
sections: [02-ata-advanced]
tags: [add-to-all, feed, rss, placeholders, copyright]
status: publish
order: 0
---

[kbtoc]

[WebberZone Snippetz](https://webberzone.com/plugins/add-to-all/) can append a copyright notice and a linked title line to every item in your site's RSS feed, and inject arbitrary HTML before or after each item's content. All text fields support placeholder tokens that expand to dynamic values at render time.

Configure these options under **Snippetz → Settings → Feed**.

## Copyright notice

Enable **Add copyright notice** to append a notice after each feed item. The default text is:

```text
© {year} "{site name}". Use of this feed is for personal non-commercial use only.
If you are not reading this article in your feed reader, then the site is guilty
of copyright infringement. Please contact me at {admin_email}
```

Edit the **Copyright text** field to customize this. You can use any valid HTML and any of the placeholder tokens below.

## Feed title line

Enable **Add post title** to prepend a line above each feed item's content. The default template is:

```text
%title% was first posted on %date% at %time%.
```

Edit **Title text** to customize the template. This field supports the feed-specific placeholder tokens listed below.

## Placeholder tokens

Tokens expand when the feed is rendered. They work in the **Copyright text** and **Title text** fields.

### General tokens

Available in the copyright notice and any other field that runs through `process_placeholders()`:

| Token | Expands to |
|---|---|
| `%year%` | Current four-digit year |
| `%first_year%` | Year of the oldest published post on the site |
| `%month%` | Current month name (January–December) |
| `%date%` | Current day of the month (1–31) |
| `%reading_time%` | Estimated reading time of the current post (e.g. "3 minutes, 12 seconds") |

### Feed title tokens

Available only in the **Title text** field:

| Token | Expands to |
|---|---|
| `%title%` | Post title linked to its permalink |
| `%date%` | Post publication date (e.g. "June 17, 2026") |
| `%time%` | Post publication time (e.g. "9:30 am") |
| `%updated_time%` | Post last-modified date |

### Adding custom tokens

Use the `ata_placeholders` filter to register additional tokens:

```php
add_filter( 'ata_placeholders', function( array $placeholders ): array {
    $placeholders['%site_name%'] = get_bloginfo( 'name' );
    $placeholders['%tagline%']   = get_bloginfo( 'description' );
    return $placeholders;
} );
```

## HTML before and after feed content

Use the **Add HTML before content** and **Add HTML after content** toggles to inject arbitrary HTML into each feed item. These fields do not support placeholder tokens.

Enable **Process shortcodes in feed** to execute shortcodes inside these HTML fields before output.

## Filters

**`ata_feed_title_text`** — Filter the rendered feed title line.

```php
add_filter( 'ata_feed_title_text', function( string $output ): string {
    return '<p>Originally published on ' . get_the_date() . '</p>';
} );
```

**`ata_creditline`** — Filter the credit link added when **Add a link to "WebberZone Snippetz"** is enabled.

```php
add_filter( 'ata_creditline', function( string $output ): string {
    return ''; // Remove credit line entirely.
} );
```

**`ata_process_placeholders`** — Filter the final output after all placeholder tokens have been replaced.

```php
add_filter( 'ata_process_placeholders', function( string $output, string $input, array $placeholders ): string {
    return $output;
}, 10, 3 );
```
