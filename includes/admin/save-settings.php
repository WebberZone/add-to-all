<?php
/**
 * Save settings.
 *
 * Functions to register, read, write and update settings.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @link  https://ajaydsouza.com
 * @since 1.2.0
 *
 * @package    Add_to_All
 * @subpackage Admin/Save_Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Sanitize the form data being submitted.
 *
 * @since  1.2.0
 * @param  array $input Input unclean array
 * @return array Sanitized array
 */
function ata_settings_sanitize( $input = array() ) {

	// First, we read the options collection.
	global $ata_settings;

	// This should be set if a form is submitted, so let's save it in the $referrer variable
	if ( empty( $_POST['_wp_http_referer'] ) ) {
		return $input;
	}

	parse_str( $_POST['_wp_http_referer'], $referrer );

	// Get the various settings we've registered.
	$settings = ata_get_registered_settings();

	// Check if we need to set to defaults
	$reset = isset( $_POST['settings_reset'] );

	if ( $reset ) {
		ata_settings_reset();
		$ata_settings = get_option( 'ata_settings' );

		add_settings_error( 'ata-notices', '', __( 'Settings have been reset to their default values. Reload this page to view the updated settings', 'add-to-all' ), 'error' );

		return $ata_settings;
	}

	// Get the tab. This is also our settings' section
	$tab = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';

	$input = $input ? $input : array();

	$input = apply_filters( 'ata_settings_' . $tab . '_sanitize', $input );

	// Loop through each setting being saved and pass it through a sanitization filter.
	foreach ( $input as $key => $value ) {

		// Get the setting type (checkbox, select, etc).
		$type = isset( $settings[ $tab ][ $key ]['type'] ) ? $settings[ $tab ][ $key ]['type'] : false;

		if ( $type ) {
			// Field type specific filter
			$input[ $key ] = apply_filters( 'ata_settings_sanitize_' . $type, $value, $key );
		}

		// General filter
		$input[ $key ] = apply_filters( 'ata_settings_sanitize', $input[ $key ], $key );
	}

	// Loop through the whitelist and unset any that are empty for the tab being saved.
	if ( ! empty( $settings[ $tab ] ) ) {
		foreach ( $settings[ $tab ] as $key => $value ) {
			if ( empty( $input[ $key ] ) && ! empty( $ata_settings[ $key ] ) ) {
				unset( $ata_settings[ $key ] );
			}
		}
	}

	// Merge our new settings with the existing. Force (array) in case it is empty.
	$ata_settings = array_merge( (array) $ata_settings, $input );

	add_settings_error( 'ata-notices', '', __( 'Settings updated.', 'add-to-all' ), 'updated' );

	return $ata_settings;

}


/**
 * Sanitize text fields
 *
 * @since 1.2.0
 *
 * @param  array $input The field value
 * @return string  $input  Sanitizied value
 */
function ata_sanitize_text_field( $input ) {
	return sanitize_text_field( $input );
}
add_filter( 'ata_settings_sanitize_text', 'ata_sanitize_text_field' );

