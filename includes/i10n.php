<?php
/**
 * Language functions
 *
 * @since 1.4.0
 *
 * @package Add_to_All
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to load translation files.
 *
 * @since 1.2.0
 */
function ata_lang_init() {
	load_plugin_textdomain( 'add-to-all', false, dirname( plugin_basename( ATA_PLUGIN_FILE ) ) . '/languages/' );
}
add_action( 'init', 'ata_lang_init' );

