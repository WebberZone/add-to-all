# AGENTS.md

This file provides guidance to Codex (Codex.ai/code) when working with code in this repository.

## Plugin Overview

**WebberZone Snippetz** (plugin slug: `add-to-all`) is a WordPress plugin (v2.3.0) that lets users insert arbitrary code snippets (HTML, CSS, JS) into the header, body open, footer, content, or feed of a WordPress site. It also manages a `ata_snippets` custom post type for reusable named snippets. Namespace: `WebberZone\Snippetz`. Requires WordPress 6.3+, PHP 7.4+. No Freemius.

Constants defined in `add-to-all.php`: `WZ_SNIPPETZ_VERSION`, `WZ_SNIPPETZ_FILE`, `WZ_SNIPPETZ_DIR`, `WZ_SNIPPETZ_URL`.

Settings prefix/key: `ata` / `ata_settings` (wp_options). Access via `ata_get_option($key)` / `ata_get_settings()` (thin wrappers around `Options_API`).

## Commands

### PHP
```bash
composer phpcs          # Lint PHP (WordPress coding standards)
composer phpcbf         # Auto-fix PHP code style
composer phpstan        # Static analysis
composer phpcompat      # Check PHP 7.4-8.5 compatibility
composer test           # Run all checks (phpcs + phpcompat + phpstan)
composer build:vendor   # Install production deps only
```

### JavaScript/CSS
```bash
npm run build           # Build the snippetz Gutenberg block (wp-scripts)
npm start               # Watch block source files
npm run build:assets    # Minify CSS/JS, generate RTL CSS (node build-assets.js)
npm run lint:js         # ESLint
npm run lint:css        # Stylelint
npm run zip             # Create distribution zip (wp-scripts plugin-zip)
```

## Architecture

### Entry Point
`add-to-all.php` defines constants, loads `includes/autoloader.php` (a function-based PSR-4 autoloader registered via `spl_autoload_register`), loads `vendor/autoload.php` if present (Composer dependencies — notably `matthiasmullie/minify` for CSS/JS minification), requires `includes/options-api.php` (procedural function wrappers), then calls `\WebberZone\Snippetz\load()` on `plugins_loaded`.

### Autoloader note
Unlike the class-based autoloader used in autoclose, this plugin uses a plain function `WebberZone\Snippetz\autoload()` in `includes/autoloader.php`. The mapping convention is otherwise identical: namespace segments become path segments under `includes/`, underscores become hyphens, lowercase, final segment prefixed with `class-`.

### Main class (`includes/class-main.php`)
Singleton (`Main::get_instance()`). On `init` it instantiates:
- `Frontend\Shortcodes`, `Frontend\Site_Verification`, `Frontend\Third_Party`, `Frontend\Blocks\Blocks` — always
- `Admin\Admin` — only when `is_admin()`
- `Snippets\Snippets` — only when `Util\Helpers::is_snippets_enabled()`

Hooks for content injection (`wp_head`, `wp_body_open`, `wp_footer`, `the_content`, `the_excerpt_rss`) are registered directly in `Main::hooks()`.

### Snippets subsystem (`includes/snippets/`)
- **`Snippets`** — Registers the `ata_snippets` custom post type (non-public, admin-only, block editor disabled) and `ata_snippets_category` taxonomy. Uses CodeMirror for the editor. REST base: `webberzone/v1/snippets`.
- **`Functions`** — Injects snippet content into `wp_head`, `wp_footer`, and `the_content` based on snippet meta and settings.
- **`Minifier`** — Wraps `matthiasmullie/minify` to minify HTML/CSS/JS snippets when the option is enabled; minified output is cached as flat files under `wp-content/uploads/`.
- **`Shortcodes`** — `[ata_snippet id=""]` shortcode for embedding snippets inline.
- **`Metabox`** — Post meta for snippet type (`_ata_snippet_type`: `html`, `css`, `js`) and display conditions.
- **`Admin_Columns`** — Custom admin list columns for the snippets CPT.

### Frontend (`includes/frontend/`)
- **`Blocks\Blocks`** — Registers the `webberzone/snippetz` Gutenberg block; source at `includes/frontend/blocks/src/snippetz/`, built output at `includes/frontend/blocks/build/snippetz/`.
- **`Shortcodes`** — `[add-to-all]` legacy shortcode.
- **`Site_Verification`** — Outputs site verification meta tags (Google, Bing, etc.) in `wp_head`.
- **`Third_Party`** — Handles integrations with third-party services configured via settings.

### Admin (`includes/admin/`)
- **`Settings`** — Settings page under Settings menu (`ata_options_page`). Tabs: General, Third Party, Header, Body, Footer, Feed.
- **`Tools_Page`** — Tools page for one-time actions.

### Settings access
Always use `ata_get_option($key, $default)` rather than reading `ata_settings` directly.
