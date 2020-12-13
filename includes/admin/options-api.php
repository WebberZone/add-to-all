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
 * Reset settings.
 *
 * @since 1.2.0
 *
 * @return void
 */
function ata_settings_reset() {
	delete_option( 'ata_settings' );
}


/**
 * Function to add an action to search for tags using Ajax.
 *
 * @since 2.6.0
 *
 * @return void
 */
function ata_tag_search() {

	if ( ! isset( $_REQUEST['tax'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_die( 0 );
	}

	$taxonomy = sanitize_key( $_REQUEST['tax'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$tax      = get_taxonomy( $taxonomy );
	if ( ! $tax ) {
		wp_die( 0 );
	}

	if ( ! current_user_can( $tax->cap->assign_terms ) ) {
		wp_die( -1 );
	}

	$s = isset( $_REQUEST['q'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$comma = _x( ',', 'tag delimiter' );
	if ( ',' !== $comma ) {
		$s = str_replace( $comma, ',', $s );
	}
	if ( false !== strpos( $s, ',' ) ) {
		$s = explode( ',', $s );
		$s = $s[ count( $s ) - 1 ];
	}
	$s = trim( $s );

	/** This filter has been defined in /wp-admin/includes/ajax-actions.php */
	$term_search_min_chars = (int) apply_filters( 'term_search_min_chars', 2, $tax, $s );

	/*
	 * Require $term_search_min_chars chars for matching (default: 2)
	 * ensure it's a non-negative, non-zero integer.
	 */
	if ( ( 0 === $term_search_min_chars ) || ( strlen( $s ) < $term_search_min_chars ) ) {
		wp_die();
	}

	$results = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'name__like' => $s,
			'fields'     => 'names',
			'hide_empty' => false,
		)
	);

	echo wp_json_encode( $results );
	wp_die();

}
add_action( 'wp_ajax_ata_tag_search', 'ata_tag_search' );
