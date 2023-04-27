<?php
/**
 *
 * WebberZone Snippetz (formerly Add to All)
 *
 * WebberZone Snippetz lets you add custom text or HTML to your WordPress header, footer, sidebar, content or feed.
 *
 * @package Add_to_All
 *
 * @author    Ajay D'Souza
 * @license   GPL-2.0+
 * @link      https://webberzone.com
 * @copyright 2012-2022 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: WebberZone Snippetz
 * Version:     1.8.0
 * Plugin URI:  https://webberzone.com/plugins/add-to-all/
 * Description: A powerful plugin that will allow you to add custom code or CSS to your header, footer, sidebar, content or feed.
 * Author:      Ajay D'Souza
 * Author URI:  https://webberzone.com/
 * Text Domain: add-to-all
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, then abort execution.
if ( ! defined( 'WPINC' ) ) {
	die( "Aren't you supposed to come here via WP-Admin?" );
}

/**
 * Holds the version of WebberZone Snippetz.
 *
 * @since 1.8.0
 *
 * @var string WebberZone Snippetz version.
 */
if ( ! defined( 'ADD_TO_ALL_VERSION' ) ) {
	define( 'ADD_TO_ALL_VERSION', '1.8.0' );
}


/**
 * Holds the plugin file path
 *
 * @since 1.2.0
 *
 * @var string Plugin Root File
 */
if ( ! defined( 'ATA_PLUGIN_FILE' ) ) {
	define( 'ATA_PLUGIN_FILE', __FILE__ );
}


/**
 * Holds the filesystem directory path (with trailing slash)
 *
 * @since 1.2.0
 *
 * @var string Plugin folder path
 */
if ( ! defined( 'ATA_PLUGIN_DIR' ) ) {
	define( 'ATA_PLUGIN_DIR', plugin_dir_path( ATA_PLUGIN_FILE ) );
}

/**
 * Holds the URL directory path (with trailing slash)
 *
 * @since 1.2.0
 *
 * @var string Plugin folder URL
 */
if ( ! defined( 'ATA_PLUGIN_URL' ) ) {
	define( 'ATA_PLUGIN_URL', plugin_dir_url( ATA_PLUGIN_FILE ) );
}

/*
 *----------------------------------------------------------------------------
 * Include files
 *----------------------------------------------------------------------------
 */

require_once ATA_PLUGIN_DIR . 'includes/admin/class-settings-api.php';
require_once ATA_PLUGIN_DIR . 'includes/admin/class-ata-settings.php';
require_once ATA_PLUGIN_DIR . 'includes/admin/options-api.php';
require_once ATA_PLUGIN_DIR . 'includes/i10n.php';
require_once ATA_PLUGIN_DIR . 'includes/helpers.php';
require_once ATA_PLUGIN_DIR . 'includes/content.php';
require_once ATA_PLUGIN_DIR . 'includes/header.php';
require_once ATA_PLUGIN_DIR . 'includes/footer.php';
require_once ATA_PLUGIN_DIR . 'includes/feed.php';
require_once ATA_PLUGIN_DIR . 'includes/modules/statcounter.php';
require_once ATA_PLUGIN_DIR . 'includes/modules/google-analytics.php';
require_once ATA_PLUGIN_DIR . 'includes/modules/tynt.php';
require_once ATA_PLUGIN_DIR . 'includes/modules/site-verification.php';
require_once ATA_PLUGIN_DIR . 'includes/modules/class-ata-shortcodes.php';

/**
 * Declare $ata_settings global so that it can be accessed in every function
 *
 * @since 1.0
 * @var $ata_settings WebberZone Snippetz settings array.
 */
global $ata_settings;
$ata_settings = ata_get_settings();


/**
 * Get Settings.
 *
 * Retrieves all plugin settings
 *
 * @since  1.2.0
 * @return array WebberZone Snippetz settings
 */
function ata_get_settings() {

	$settings = get_option( 'ata_settings' );

	/**
	 * Settings array
	 *
	 * Retrieves all plugin settings
	 *
	 * @since 1.2.0
	 * @param array $settings Settings array
	 */
	return apply_filters( 'ata_get_settings', $settings );
}

/*
 *----------------------------------------------------------------------------
 * Snippets Manager
 *----------------------------------------------------------------------------
 */
$ata_disable_snippets = ( defined( 'ATA_DISABLE_SNIPPETS' ) && ATA_DISABLE_SNIPPETS ) ? true : false;
if ( ata_get_option( 'enable_snippets' ) && ! $ata_disable_snippets ) {
	require_once ATA_PLUGIN_DIR . 'includes/modules/snippets/class-ata-snippets.php';
	require_once ATA_PLUGIN_DIR . 'includes/modules/snippets/snippets-op.php';
	require_once ATA_PLUGIN_DIR . 'includes/modules/snippets/class-ata-snippets-shortcode.php';
	require_once ATA_PLUGIN_DIR . 'includes/admin/class-ata-metabox.php';
	require_once ATA_PLUGIN_DIR . 'includes/admin/class-ata-admin-columns.php';
}


/*
 *----------------------------------------------------------------------------
 * Deprecated functions, variables and constants
 *----------------------------------------------------------------------------
 */

require_once ATA_PLUGIN_DIR . '/includes/deprecated.php';
