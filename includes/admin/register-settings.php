<?php
/**
 * Register settings.
 *
 * Functions to register, read, write and update settings.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @link  https://ajaydsouza.com
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
 * Register settings function
 *
 * @since 1.2.0
 *
 * @return void
 */
function ata_register_settings() {

	if ( false === get_option( 'ata_settings' ) ) {
		add_option( 'ata_settings', ata_settings_defaults() );
	}

	foreach ( ata_get_registered_settings() as $section => $settings ) {

		add_settings_section(
			'ata_settings_' . $section, // ID used to identify this section and with which to register options, e.g. ata_settings_general.
			__return_null(), // No title, we will handle this via a separate function.
			'__return_false', // No callback function needed. We'll process this separately.
			'ata_settings_' . $section  // Page on which these options will be added.
		);

		foreach ( $settings as $setting ) {

			$args = wp_parse_args(
				$setting, array(
					'section'          => $section,
					'id'               => null,
					'name'             => '',
					'desc'             => '',
					'type'             => null,
					'options'          => '',
					'max'              => null,
					'min'              => null,
					'step'             => null,
					'size'             => null,
					'field_class'      => '',
					'field_attributes' => '',
					'placeholder'      => '',
				)
			);

			add_settings_field(
				'ata_settings[' . $args['id'] . ']', // ID of the settings field. We save it within the ata_settings array.
				$args['name'],     // Label of the setting.
				function_exists( 'ata_' . $args['type'] . '_callback' ) ? 'ata_' . $args['type'] . '_callback' : 'ata_missing_callback', // Function to handle the setting.
				'ata_settings_' . $section,    // Page to display the setting. In our case it is the section as defined above.
				'ata_settings_' . $section,    // Name of the section.
				$args
			);
		}
	}

	// Register the settings into the options table.
	register_setting( 'ata_settings', 'ata_settings', 'ata_settings_sanitize' );
}
add_action( 'admin_init', 'ata_register_settings' );


/**
 * Flattens ata_get_registered_settings() into $setting[id] => $setting[type] format.
 *
 * @since 1.3.0
 *
 * @return array Default settings
 */
function ata_get_registered_settings_types() {

	$options = array();

	// Populate some default values.
	foreach ( ata_get_registered_settings() as $tab => $settings ) {
		foreach ( $settings as $option ) {
			$options[ $option['id'] ] = $option['type'];
		}
	}

	/**
	 * Filters the settings array.
	 *
	 * @since 1.3.0
	 *
	 * @param array   $options Default settings.
	 */
	return apply_filters( 'ata_get_settings_types', $options );
}


/**
 * Default settings.
 *
 * @since 1.2.0
 *
 * @return array Default settings
 */
function ata_settings_defaults() {

	$options = array();

	// Populate some default values.
	foreach ( ata_get_registered_settings() as $tab => $settings ) {
		foreach ( $settings as $option ) {
			// When checkbox is set to true, set this to 1.
			if ( 'checkbox' === $option['type'] && ! empty( $option['options'] ) ) {
				$options[ $option['id'] ] = 1;
			} else {
				$options[ $option['id'] ] = 0;
			}
			// If an option is set.
			if ( in_array( $option['type'], array( 'textarea', 'text', 'csv', 'numbercsv', 'posttypes', 'number' ), true ) && isset( $option['options'] ) ) {
				$options[ $option['id'] ] = $option['options'];
			}
			if ( in_array( $option['type'], array( 'multicheck', 'radio', 'select', 'radiodesc', 'thumbsizes' ), true ) && isset( $option['default'] ) ) {
				$options[ $option['id'] ] = $option['default'];
			}
		}
	}

	$upgraded_settings = ata_upgrade_settings();

	if ( false !== $upgraded_settings ) {
		$options = array_merge( $options, $upgraded_settings );
	}

	/**
	 * Filters the default settings array.
	 *
	 * @since 1.2.0
	 *
	 * @param array $options Default settings.
	 */
	return apply_filters( 'ata_settings_defaults', $options );
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

