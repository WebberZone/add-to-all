<?php
/**
 * WebberZone Snippetz Options API.
 *
 * @link  https://webberzone.com
 * @since 1.2.0
 *
 * @package WebberZone\Snippetz
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get Settings.
 *
 * Retrieves all plugin settings
 *
 * @since  1.2.0
 * @return array WebberZone Snippetz settings
 */
function ata_get_settings() {
	return \WebberZone\Snippetz\Options_API::get_settings();
}

/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since  1.2.0
 *
 * @param string $key Option to fetch.
 * @param mixed  $default_value Default option.
 * @return mixed
 */
function ata_get_option( $key = '', $default_value = null ) {
	return \WebberZone\Snippetz\Options_API::get_option( $key, $default_value );
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
	return \WebberZone\Snippetz\Options_API::update_option( $key, $value );
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
	return \WebberZone\Snippetz\Options_API::delete_option( $key );
}

/**
 * Default settings.
 *
 * @since 1.2.0
 *
 * @return array Default settings
 */
function ata_settings_defaults() {
	return \WebberZone\Snippetz\Options_API::get_settings_defaults();
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
	return \WebberZone\Snippetz\Options_API::get_default_option( $key );
}

/**
 * Reset settings.
 *
 * @since 1.2.0
 *
 * @return bool True if updated, false if not.
 */
function ata_settings_reset() {
	return \WebberZone\Snippetz\Options_API::reset_settings();
}

/**
 * Function to add an action to search for tags using Ajax.
 *
 * @since 1.7.0
 *
 * @return void
 */
function ata_tag_search() {
	\WebberZone\Snippetz\Options_API::tags_search();
}

\WebberZone\Snippetz\Util\Hook_Registry::add_action( 'wp_ajax_ata_tag_search', 'ata_tag_search' );
