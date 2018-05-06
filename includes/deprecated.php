<?php
/**
 * Deprecated functions, constants, variables, etc.
 *
 * @link  https://ajaydsouza.com
 * @since 1.2.0
 *
 * @package Add_to_All
 * @subpackage Deprecated
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Default options.
 *
 * @return return Array with default options
 */
function ata_default_options() {

	_deprecated_function( __FUNCTION__, '1.2.0', 'ata_settings_defaults()' );

	return ata_settings_defaults();
}


/**
 * Function to read options from the database and add any new ones.
 *
 * @return array Options array
 */
function ata_read_options() {

	_deprecated_function( __FUNCTION__, '1.2.0', 'ata_get_settings()' );

	global $ata_settings;

	return $ata_settings;
}

