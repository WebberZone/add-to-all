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
 * Version:     2.2.0
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
	/**
	 * Holds the version of WebberZone Snippetz.
	 *
	 * @since 1.8.0
	 *
	 * @var string $WZ_SNIPPETZ_VERSION WebberZone Snippetz version.
	 */
	define( 'WZ_SNIPPETZ_VERSION', '2.2.0' );
}


if ( ! defined( 'WZ_SNIPPETZ_FILE' ) ) {
	/**
	 * Holds the plugin file path
	 *
	 * @since 1.2.0
	 *
	 * @var string $WZ_SNIPPETZ_FILE Plugin Root File
	 */
	define( 'WZ_SNIPPETZ_FILE', __FILE__ );
}

if ( ! defined( 'WZ_SNIPPETZ_DIR' ) ) {
	/**
	 * Holds the filesystem directory path (with trailing slash)
	 *
	 * @since 1.2.0
	 *
	 * @var string $WZ_SNIPPETZ_DIR Plugin folder path
	 */
	define( 'WZ_SNIPPETZ_DIR', plugin_dir_path( WZ_SNIPPETZ_FILE ) );
}

if ( ! defined( 'WZ_SNIPPETZ_URL' ) ) {
	/**
	 * Holds the URL directory path (with trailing slash)
	 *
	 * @since 1.2.0
	 *
	 * @var string $WZ_SNIPPETZ_URL Plugin folder URL
	 */
	define( 'WZ_SNIPPETZ_URL', plugin_dir_url( WZ_SNIPPETZ_FILE ) );
}

// Load the autoloader.
require_once plugin_dir_path( __FILE__ ) . 'includes/autoloader.php';

/**
 * The main function responsible for returning the one true WebberZone Snippetz instance to functions everywhere.
 */
function load_wz_snippetz() {
	\WebberZone\Snippetz\Main::get_instance();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\load_wz_snippetz' );

/*
 *----------------------------------------------------------------------------
 * Include files
 *----------------------------------------------------------------------------
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/options-api.php';

/**
 * Declare $wz_snippetz_settings global so that it can be accessed in every function
 *
 * @since 1.0
 * @var $wz_snippetz_settings WebberZone Snippetz settings array.
 */
global $wz_snippetz_settings;
$wz_snippetz_settings = \ata_get_settings();
