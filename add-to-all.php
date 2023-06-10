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
 * @copyright 2012-2023 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: WebberZone Snippetz
 * Version:     2.0.1
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

/**
 * Holds the version of WebberZone Snippetz.
 *
 * @since 1.8.0
 *
 * @var string WebberZone Snippetz version.
 */
if ( ! defined( 'WZ_SNIPPETZ_VERSION' ) ) {
	define( 'WZ_SNIPPETZ_VERSION', '2.0.0' );
}


/**
 * Holds the plugin file path
 *
 * @since 1.2.0
 *
 * @var string Plugin Root File
 */
if ( ! defined( 'WZ_SNIPPETZ_FILE' ) ) {
	define( 'WZ_SNIPPETZ_FILE', __FILE__ );
}


/**
 * Holds the filesystem directory path (with trailing slash)
 *
 * @since 1.2.0
 *
 * @var string Plugin folder path
 */
if ( ! defined( 'WZ_SNIPPETZ_DIR' ) ) {
	define( 'WZ_SNIPPETZ_DIR', plugin_dir_path( WZ_SNIPPETZ_FILE ) );
}

/**
 * Holds the URL directory path (with trailing slash)
 *
 * @since 1.2.0
 *
 * @var string Plugin folder URL
 */
if ( ! defined( 'WZ_SNIPPETZ_URL' ) ) {
	define( 'WZ_SNIPPETZ_URL', plugin_dir_url( WZ_SNIPPETZ_FILE ) );
}

// Load the autoloader.
require_once WZ_SNIPPETZ_DIR . 'includes/autoloader.php';

/**
 * The main function responsible for returning the one true WebberZone Snippetz instance to functions everywhere.
 *
 * @return Add_To_All The one true WebberZone Snippetz instance.
 */
function load_wz_snippetz() {
	return \WebberZone\Snippetz\Main::get_instance();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\load_wz_snippetz' );

/*
 *----------------------------------------------------------------------------
 * Include files
 *----------------------------------------------------------------------------
 */
require_once WZ_SNIPPETZ_DIR . 'includes/options-api.php';

/**
 * Declare $ata_settings global so that it can be accessed in every function
 *
 * @since 1.0
 * @var $ata_settings WebberZone Snippetz settings array.
 */
global $ata_settings;
$ata_settings = ata_get_settings();
