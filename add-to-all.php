<?php
/**
 *
 * WebberZone Snippetz (formerly Add to All)
 *
 * WebberZone Snippetz lets you add custom text or HTML to your WordPress header, footer, sidebar, content or feed.
 *
 * @package WebberZone\Snippetz
 *
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2012-2026 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: WebberZone Snippetz - Header, Body and Footer manager
 * Version:     2.3.0
 * Plugin URI:  https://webberzone.com/plugins/add-to-all/
 * Description: A simple yet powerful plugin that allows you to insert any code snippet or script into WordPress.
 * Author:      Ajay D'Souza
 * Author URI:  https://webberzone.com/
 * Text Domain: add-to-all
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

namespace WebberZone\Snippetz;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

if ( ! defined( 'WZ_SNIPPETZ_VERSION' ) ) {
	define( 'WZ_SNIPPETZ_VERSION', '2.3.0' );
}

if ( ! defined( 'WZ_SNIPPETZ_FILE' ) ) {
	define( 'WZ_SNIPPETZ_FILE', __FILE__ );
}

if ( ! defined( 'WZ_SNIPPETZ_DIR' ) ) {
	define( 'WZ_SNIPPETZ_DIR', plugin_dir_path( WZ_SNIPPETZ_FILE ) );
}

if ( ! defined( 'WZ_SNIPPETZ_URL' ) ) {
	define( 'WZ_SNIPPETZ_URL', plugin_dir_url( WZ_SNIPPETZ_FILE ) );
}

// Load the autoloader.
require_once plugin_dir_path( __FILE__ ) . 'includes/autoloader.php';

// Load Composer dependencies if available.
$composer_autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
}

/**
 * The main function responsible for returning the one true WebberZone Snippetz instance to functions everywhere.
 */
function load() {
	\WebberZone\Snippetz\Main::get_instance();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\load' );

if ( ! function_exists( 'snippetz' ) ) {
	/**
	 * Get the main WebberZone Snippetz instance.
	 *
	 * @since 2.3.0
	 * @return Main Main instance.
	 */
	function snippetz() {
		return \WebberZone\Snippetz\Main::get_instance();
	}
}

/*
 *----------------------------------------------------------------------------
 * Include files
 *----------------------------------------------------------------------------
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/options-api.php';
