<?php
/**
 * WebberZone Snippetz Options API.
 *
 * @since 2.1.0
 *
 * @package WebberZone\Snippetz
 */

namespace WebberZone\Snippetz;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Options API Class.
 *
 * @since 2.3.0
 */
class Options_API {

	/**
	 * Settings option name.
	 *
	 * @since 2.3.0
	 * @var string
	 */
	const SETTINGS_OPTION = 'ata_settings';

	/**
	 * Filter prefix.
	 *
	 * @since 2.3.0
	 * @var string
	 */
	const FILTER_PREFIX = 'ata';

	/**
	 * Settings array.
	 *
	 * @since 2.3.0
	 * @var array
	 */
	private static $settings;

	/**
	 * Initialize hooks for AJAX functionality.
	 *
	 * @since 2.3.0
	 */
	public static function init() {
	}

	/**
	 * Get Settings.
	 *
	 * Retrieves all plugin settings
	 *
	 * @since 2.3.0
	 * @return array Glue Link settings
	 */
	public static function get_settings() {
		$settings = get_option( self::SETTINGS_OPTION );

		/**
		 * Settings array
		 *
		 * Retrieves all plugin settings
		 *
		 * @since 2.3.0
		 * @param array $settings Settings array
		 */
		return apply_filters( self::FILTER_PREFIX . '_get_settings', $settings );
	}

	/**
	 * Get an option
	 *
	 * Looks to see if the specified setting exists, returns default if not
	 *
	 * @since 2.3.0
	 *
	 * @param string $key           Option to fetch.
	 * @param mixed  $default_value Default option.
	 * @return mixed
	 */
	public static function get_option( $key = '', $default_value = null ) {
		if ( empty( self::$settings ) ) {
			self::$settings = self::get_settings();
		}

		$value = isset( self::$settings[ $key ] ) ? self::$settings[ $key ] : null;

		if ( is_null( $value ) ) {
			if ( is_null( $default_value ) ) {
				$default_value = self::get_default_option( $key );
			}
			$value = $default_value;
		}

		/**
		 * Filter the value for the option being fetched.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed $value         Value of the option.
		 * @param mixed $key           Name of the option.
		 * @param mixed $default_value Default value.
		 */
		$value = apply_filters( self::FILTER_PREFIX . '_get_option', $value, $key, $default_value );

		/**
		 * Key specific filter for the value of the option being fetched.
		 *
		 * @since 2.3.0
		 *
		 * @param mixed $value         Value of the option.
		 * @param mixed $key           Name of the option.
		 * @param mixed $default_value Default value.
		 */
		return apply_filters( self::FILTER_PREFIX . "_get_option_{$key}", $value, $key, $default_value );
	}

	/**
	 * Update an option
	 *
	 * Updates a setting value in both the db and the global variable.
	 * Warning: Passing in an empty, false or null string value will remove
	 *        the key from the settings array.
	 *
	 * @since 2.3.0
	 *
	 * @param  string          $key   The Key to update.
	 * @param  string|bool|int $value The value to set the key to.
	 * @return boolean True if updated, false if not.
	 */
	public static function update_option( $key = '', $value = false ) {
		// If no key, exit.
		if ( empty( $key ) ) {
			return false;
		}

		// First let's grab the current settings.
		$options = get_option( self::SETTINGS_OPTION, array() );

		// Let's let devs alter that value coming in.
		$value = apply_filters( self::FILTER_PREFIX . '_update_option', $value, $key );

		// Next let's try to update the value.
		$options[ $key ] = $value;
		$did_update      = update_option( self::SETTINGS_OPTION, $options );

		// If it updated, let's update the static variable.
		if ( $did_update ) {
			self::$settings = $options;
		}

		return $did_update;
	}

	/**
	 * Update all settings at once.
	 *
	 * @since 2.3.0
	 *
	 * @param array $settings  Settings array to save.
	 * @param bool  $merge     Whether to merge with existing settings. Default true.
	 * @param bool  $autoload  Whether to autoload the option. Default true.
	 * @return bool True if updated, false otherwise.
	 */
	public static function update_settings( array $settings, bool $merge = true, bool $autoload = true ): bool {
		// Merge incoming array into existing settings if requested.
		if ( $merge ) {
			$existing = (array) self::get_settings();
			$settings = array_merge( $existing, $settings );
		}
		$did_update = update_option( self::SETTINGS_OPTION, $settings, $autoload );
		if ( $did_update ) {
			self::$settings = $settings;
		}
		return $did_update;
	}

	/**
	 * Remove an option
	 *
	 * Removes a Glue Link setting value in both the db and the static variable.
	 *
	 * @since 2.3.0
	 *
	 * @param  string $key The Key to delete.
	 * @return boolean True if updated, false if not.
	 */
	public static function delete_option( $key = '' ) {
		// If no key, exit.
		if ( empty( $key ) ) {
			return false;
		}

		// First let's grab the current settings.
		$options = get_option( self::SETTINGS_OPTION, array() );

		// Next let's try to update the value.
		if ( isset( $options[ $key ] ) ) {
			unset( $options[ $key ] );
		}

		$did_update = update_option( self::SETTINGS_OPTION, $options );

		// If it updated, let's update the static variable.
		if ( $did_update ) {
			self::$settings = $options;
		}

		return $did_update;
	}

	/**
	 * Default settings.
	 *
	 * @since 2.3.0
	 *
	 * @return array Default settings
	 */
	public static function get_settings_defaults() {
		return Admin\Settings::settings_defaults();
	}

	/**
	 * Get the default option for a specific key
	 *
	 * @since 2.3.0
	 *
	 * @param string $key Key of the option to fetch.
	 * @return mixed
	 */
	public static function get_default_option( $key = '' ) {
		$default_settings = self::get_settings_defaults();

		if ( array_key_exists( $key, $default_settings ) ) {
			return $default_settings[ $key ];
		}

		return false;
	}

	/**
	 * Reset settings.
	 *
	 * @since 2.3.0
	 *
	 * @return bool True if updated, false if not.
	 */
	public static function reset_settings(): bool {
		$did_update = update_option( self::SETTINGS_OPTION, self::get_settings_defaults() );

		// If it updated, let's update the static variable.
		if ( $did_update ) {
			self::$settings = self::get_settings_defaults();
		}

		return $did_update;
	}

}

// Initialize hooks.
Options_API::init();
