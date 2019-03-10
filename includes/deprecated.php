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
 * @since 1.0
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
 * @since 1.0
 *
 * @return array Options array
 */
function ata_read_options() {

	_deprecated_function( __FUNCTION__, '1.2.0', 'ata_get_settings()' );

	return ata_get_settings();
}


/**
 * Function to add content to RSS feeds. Filters `the_excerpt_rss` and `the_content_feed`.
 *
 * @since 1.0.3
 *
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ald_ata_rss( $content ) {

	_deprecated_function( __FUNCTION__, '1.3.0', 'ata_rss()' );

	return ata_rss( $content );
}

