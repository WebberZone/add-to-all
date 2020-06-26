<?php
/**
 * Add to All Options API.
 *
 * @link  https://webberzone.com
 * @since 1.2.0
 *
 * @package Add_to_All
 * @subpackage Admin/Register_Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since  1.2.0
 *
 * @param string $key Option to fetch.
 * @param mixed  $default Default option.
 * @return mixed
 */
function ata_get_option( $key = '', $default = false ) {

	global $ata_settings;

	$value = ! empty( $ata_settings[ $key ] ) ? $ata_settings[ $key ] : $default;

	/**
	 * Filter the value for the option being fetched.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed $value  Value of the option
	 * @param mixed $key  Name of the option
	 * @param mixed $default Default value
	 */
	$value = apply_filters( 'ata_get_option', $value, $key, $default );

	/**
	 * Key specific filter for the value of the option being fetched.
	 *
	 * @since 1.2.0
	 *
	 * @param mixed $value  Value of the option
	 * @param mixed $key  Name of the option
	 * @param mixed $default Default value
	 */
	return apply_filters( 'ata_get_option_' . $key, $value, $key, $default );
}


/**
 * Update an option
 *
 * Updates an ata setting value in both the db and the global variable.
 * Warning: Passing in an empty, false or null string value will remove
 *        the key from the ata_options array.
 *
 * @since 1.2.0
 *
 * @param  string          $key   The Key to update.
 * @param  string|bool|int $value The value to set the key to.
 * @return boolean   True if updated, false if not.
 */
function ata_update_option( $key = '', $value = false ) {

	// If no key, exit.
	if ( empty( $key ) ) {
		return false;
	}

	// If no value, delete.
	if ( empty( $value ) ) {
		$remove_option = ata_delete_option( $key );
		return $remove_option;
	}

	// First let's grab the current settings.
	$options = get_option( 'ata_settings' );

	// Let's let devs alter that value coming in.
	$value = apply_filters( 'ata_update_option', $value, $key );

	// Next let's try to update the value.
	$options[ $key ] = $value;
	$did_update      = update_option( 'ata_settings', $options );

	// If it updated, let's update the global variable.
	if ( $did_update ) {
		global $ata_settings;
		$ata_settings[ $key ] = $value;
	}
	return $did_update;
}


/**
 * Remove an option
 *
 * Removes an ata setting value in both the db and the global variable.
 *
 * @since 1.2.0
 *
 * @param  string $key The Key to update.
 * @return boolean   True if updated, false if not.
 */
function ata_delete_option( $key = '' ) {

	// If no key, exit.
	if ( empty( $key ) ) {
		return false;
	}

	// First let's grab the current settings.
	$options = get_option( 'ata_settings' );

	// Next let's try to update the value.
	if ( isset( $options[ $key ] ) ) {
		unset( $options[ $key ] );
	}

	$did_update = update_option( 'ata_settings', $options );

	// If it updated, let's update the global variable.
	if ( $did_update ) {
		global $ata_settings;
		$ata_settings = $options;
	}
	return $did_update;
}


/**
 * Get the default option for a specific key
 *
 * @since 1.3.0
 *
 * @param string $key Key of the option to fetch.
 * @return mixed
 */
function ata_get_default_option( $key = '' ) {

	$default_settings = ata_settings_defaults();

	if ( array_key_exists( $key, $default_settings ) ) {
		return $default_settings[ $key ];
	} else {
		return false;
	}

}


/**
 * Reset settings.
 *
 * @since 1.2.0
 *
 * @return void
 */
function ata_settings_reset() {
	delete_option( 'ata_settings' );
}

