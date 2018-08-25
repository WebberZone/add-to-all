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
 * @param  array $input Input unclean array.
 * @return array Sanitized array
 */
function ata_settings_sanitize( $input = array() ) {

	// First, we read the options collection.
	global $ata_settings;

	// This should be set if a form is submitted, so let's save it in the $referrer variable.
	if ( empty( $_POST['_wp_http_referer'] ) ) { // WPCS: CSRF ok.
		return $input;
	}

	parse_str( sanitize_text_field( wp_unslash( $_POST['_wp_http_referer'] ) ), $referrer ); // WPCS: CSRF ok.

	// Get the various settings we've registered.
	$settings       = ata_get_registered_settings();
	$settings_types = ata_get_registered_settings_types();

	// Check if we need to set to defaults.
	$reset = isset( $_POST['settings_reset'] ); // WPCS: CSRF ok.

	if ( $reset ) {
		ata_settings_reset();
		$ata_settings = ata_get_settings();

		add_settings_error( 'ata-notices', '', __( 'Settings have been reset to their default values. Reload this page to view the updated settings', 'add-to-all' ), 'error' );

		return $ata_settings;
	}

	// Get the tab. This is also our settings' section.
	$tab = isset( $referrer['tab'] ) ? $referrer['tab'] : 'third_party';

	$input = $input ? $input : array();

	/**
	 * Filter the settings for the tab. e.g. ata_settings_third_party_sanitize.
	 *
	 * @since  1.2.0
	 * @param  array $input Input unclean array
	 */
	$input = apply_filters( 'ata_settings_' . $tab . '_sanitize', $input );

	// Create out output array by merging the existing settings with the ones submitted.
	$output = array_merge( $ata_settings, $input );

	// Loop through each setting being saved and pass it through a sanitization filter.
	foreach ( $settings_types as $key => $type ) {

		/**
		 * Skip settings that are not really settings.
		 *
		 * @since 1.3.0
		 * @param  array $non_setting_types Array of types which are not settings.
		 */
		$non_setting_types = apply_filters( 'ata_non_setting_types', array( 'header', 'descriptive_text' ) );

		if ( in_array( $type, $non_setting_types, true ) ) {
			continue;
		}

		if ( array_key_exists( $key, $output ) ) {

			/**
			 * Field type filter.
			 *
			 * @since 1.2.0
			 * @param array $output[$key] Setting value.
			 * @param array $key Setting key.
			 */
			$output[ $key ] = apply_filters( 'ata_settings_sanitize_' . $type, $output[ $key ], $key );
		}

		/**
		 * Field type filter for a specific key.
		 *
		 * @since 1.3.0
		 * @param array $output[$key] Setting value.
		 * @param array $key Setting key.
		 */
		$output[ $key ] = apply_filters( 'ata_settings_sanitize' . $key, $output[ $key ], $key );

		// Delete any key that is not present when we submit the input array.
		if ( ! isset( $input[ $key ] ) ) {
			unset( $output[ $key ] );
		}
	}

	// Delete any settings that are no longer part of our registered settings.
	if ( array_key_exists( $key, $output ) && ! array_key_exists( $key, $settings_types ) ) {
		unset( $output[ $key ] );
	}

	add_settings_error( 'ata-notices', '', __( 'Settings updated.', 'add-to-all' ), 'updated' );

	/**
	 * Filter the settings array before it is returned.
	 *
	 * @since 1.3.0
	 * @param array $output Settings array.
	 * @param array $input Input settings array.
	 */
	return apply_filters( 'ata_settings_sanitize', $output, $input );

}


/**
 * Sanitize text fields
 *
 * @since 1.2.0
 *
 * @param  array $input The field value.
 * @return string  $input  Sanitizied value
 */
function ata_sanitize_text_field( $input ) {
	return sanitize_text_field( $input );
}
add_filter( 'ata_settings_sanitize_text', 'ata_sanitize_text_field' );


/**
 * Sanitize number fields
 *
 * @since 1.3.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function ata_sanitize_number_field( $value ) {
	return filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
}
add_filter( 'ata_settings_sanitize_number', 'ata_sanitize_number_field' );


/**
 * Sanitize CSV fields
 *
 * @since 1.2.0
 *
 * @param  array $input The field value.
 * @return string  $input  Sanitizied value
 */
function ata_sanitize_csv_field( $input ) {

	return implode( ',', array_map( 'trim', explode( ',', sanitize_text_field( wp_unslash( $input ) ) ) ) );
}
add_filter( 'ata_settings_sanitize_csv', 'ata_sanitize_csv_field' );


/**
 * Sanitize CSV fields which hold numbers e.g. IDs
 *
 * @since 1.3.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function ata_sanitize_numbercsv_field( $value ) {
	return implode( ',', array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $value ) ) ) ) ) );
}
add_filter( 'ata_settings_sanitize_numbercsv', 'ata_sanitize_numbercsv_field' );


/**
 * Sanitize textarea fields
 *
 * @since 1.2.0
 *
 * @param  array $input The field value.
 * @return string  $input  Sanitizied value
 */
function ata_sanitize_textarea_field( $input ) {

	global $allowedposttags;

	// We need more tags to allow for script and style.
	$moretags = array(
		'script' => array(
			'type'    => true,
			'src'     => true,
			'async'   => true,
			'defer'   => true,
			'charset' => true,
		),
		'style'  => array(
			'type'   => true,
			'media'  => true,
			'scoped' => true,
		),
		'link'   => array(
			'rel'      => true,
			'type'     => true,
			'href'     => true,
			'media'    => true,
			'sizes'    => true,
			'hreflang' => true,
		),
	);

	$allowedtags = array_merge( $allowedposttags, $moretags );

	/**
	 * Filter allowed tags allowed when sanitizing text and textarea fields.
	 *
	 * @since 1.3.0
	 *
	 * @param array $allowedtags Allowed tags array.
	 * @param array $value The field value.
	 */
	$allowedtags = apply_filters( 'ata_sanitize_allowed_tags', $allowedtags, $value );

	return wp_kses( wp_unslash( $input ), $allowedtags );

}
/* add_filter( 'ata_settings_sanitize_textarea', 'ata_sanitize_textarea_field' ); */


/**
 * Sanitize checkbox fields
 *
 * @since 1.3.0
 *
 * @param  array $value The field value.
 * @return string|int  $value  Sanitized value
 */
function ata_sanitize_checkbox_field( $value ) {

	$value = ( -1 === (int) $value ) ? 0 : 1;

	return $value;
}
add_filter( 'ata_settings_sanitize_checkbox', 'ata_sanitize_checkbox_field' );


/**
 * Sanitize post_types fields
 *
 * @since 1.3.0
 *
 * @param  array $value The field value.
 * @return string  $value  Sanitized value
 */
function ata_sanitize_posttypes_field( $value ) {

	$post_types = is_array( $value ) ? array_map( 'sanitize_text_field', wp_unslash( $value ) ) : array( 'post', 'page' );

	return implode( ',', $post_types );
}
add_filter( 'ata_settings_sanitize_posttypes', 'ata_sanitize_posttypes_field' );


