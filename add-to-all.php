<?php
/**
 * Add to All lets you add custom text or HTML to your WordPress header, footer, sidebar, content or feed.
 *
 * @package Add_to_All
 *
 * @wordpress-plugin
 * Plugin Name: Add to All
 * Version:     1.7.0-beta1
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
 * Holds the filesystem directory path (with trailing slash) for Top 10
 *
 * @since 1.2.0
 *
 * @var string Plugin Root File
 */
if ( ! defined( 'ATA_PLUGIN_FILE' ) ) {
	define( 'ATA_PLUGIN_FILE', __FILE__ );
}


/**
 * Holds the filesystem directory path (with trailing slash) for Top 10
 *
 * @since 1.2.0
 *
 * @var string Plugin folder path
 */
if ( ! defined( 'ATA_PLUGIN_DIR' ) ) {
	define( 'ATA_PLUGIN_DIR', plugin_dir_path( ATA_PLUGIN_FILE ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Top 10
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


/**
 * Declare $ata_settings global so that it can be accessed in every function
 *
 * @since 1.0
 * @var $ata_settings Add to All settings array.
 */
global $ata_settings;
$ata_settings = ata_get_settings();


/**
 * Get Settings.
 *
 * Retrieves all plugin settings
 *
 * @since  1.2.0
 * @return array Add to All settings
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
 * Deprecated functions, variables and constants
 *----------------------------------------------------------------------------
 */

	require_once ATA_PLUGIN_DIR . '/includes/deprecated.php';

