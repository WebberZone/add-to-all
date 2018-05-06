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
			__return_null(),    // No title, we will handle this via a separate function.
			'__return_false',   // No callback function needed. We'll process this separately.
			'ata_settings_' . $section  // Page on which these options will be added.
		);

		foreach ( $settings as $setting ) {

			add_settings_field(
				'ata_settings[' . $setting['id'] . ']', // ID of the settings field. We save it within the ata_settings array.
				$setting['name'],      // Label of the setting.
				function_exists( 'ata_' . $setting['type'] . '_callback' ) ? 'ata_' . $setting['type'] . '_callback' : 'ata_missing_callback', // Function to handle the setting.
				'ata_settings_' . $section, // Page to display the setting. In our case it is the section as defined above.
				'ata_settings_' . $section, // Name of the section.
				array(        // Array of arguments to pass to the callback function.
					'section' => $section,
					'id'      => isset( $setting['id'] ) ? $setting['id'] : null,
					'name'    => isset( $setting['name'] ) ? $setting['name'] : '',
					'desc'    => isset( $setting['desc'] ) ? $setting['desc'] : '',
					'type'    => isset( $setting['type'] ) ? $setting['type'] : null,
					'options' => isset( $setting['options'] ) ? $setting['options'] : '',
					'max'     => isset( $setting['max'] ) ? $setting['max'] : 999999,
					'min'     => isset( $setting['min'] ) ? $setting['min'] : 0,
					'step'    => isset( $setting['step'] ) ? $setting['step'] : 1,
				)
			);
		}
	}

	// Register the settings into the options table.
	register_setting( 'ata_settings', 'ata_settings', 'ata_settings_sanitize' );
}
add_action( 'admin_init', 'ata_register_settings' );


/**
 * Retrieve the array of plugin settings
 *
 * @since 1.2.0
 *
 * @return array Settings array
 */
function ata_get_registered_settings() {

	$ata_settings = array(
		/*** Third Party settings */
		'third_party' => apply_filters(
			'ata_settings_third_party',
			array(
				'statcounter_header'      => array(
					'id'   => 'statcounter_header',
					'name' => '<h3>' . esc_html__( 'StatCounter', 'easy-digital-downloads' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'sc_project'              => array(
					'id'      => 'sc_project',
					'name'    => esc_html__( 'Project ID', 'add-to-all' ),
					'desc'    => esc_html__( 'This is the value of sc_project in your StatCounter code.', 'add-to-all' ),
					'type'    => 'text',
					'options' => '',
				),
				'sc_security'             => array(
					'id'      => 'sc_security',
					'name'    => esc_html__( 'Security ID', 'add-to-all' ),
					'desc'    => esc_html__( 'This is the value of sc_security in your StatCounter code.', 'add-to-all' ),
					'type'    => 'text',
					'options' => '',
				),
				'google_analytics_header' => array(
					'id'   => 'google_analytics_header',
					'name' => '<h3>' . esc_html__( 'Google Analytics', 'easy-digital-downloads' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'ga_uacct'                => array(
					'id'      => 'ga_uacct',
					'name'    => esc_html__( 'Tracking ID', 'add-to-all' ),
					'desc'    => esc_html__( 'You can find this under Admin &raquo; Tracking Info &raquo; Tracking Code when viewing your project settings and is of the form UA-XXXX-Y.', 'add-to-all' ),
					'type'    => 'text',
					'options' => '',
				),
				'ga_linker'               => array(
					'id'      => 'ga_linker',
					'name'    => esc_html__( 'Linker autoLink domains', 'add-to-all' ),
					'desc'    => esc_html__( "If you'd like to implement cross-domain tracking, enter a comma-separated list of domains, e.g. ajaydsouza.com,webberzone.com", 'add-to-all' ),
					'type'    => 'csv',
					'options' => '',
				),
				'tynt_header'             => array(
					'id'   => 'tynt_header',
					'name' => '<h3>' . esc_html__( '33 Across (Tynt)', 'easy-digital-downloads' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'tynt_id'                 => array(
					'id'      => 'tynt_id',
					'name'    => esc_html__( 'Tynt ID', 'add-to-all' ),
					/* translators: 1: Code. */
					'desc'    => sprintf( esc_html__( 'This is the text between the brackets in %1$s  in the SiteCTRL client script', 'add-to-all' ), "<code>Tynt.push('ID HERE')</code>" ),
					'type'    => 'text',
					'options' => '',
				),
				'verification_header'     => array(
					'id'   => 'verification_header',
					'name' => '<h3>' . esc_html__( 'Site verification', 'easy-digital-downloads' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'google_verification'     => array(
					'id'      => 'google_verification',
					'name'    => esc_html__( 'Google', 'add-to-all' ),
					/* translators: 1: Google verification details page. */
					'desc'    => sprintf( esc_html__( 'Value of the content portion of the HTML tag method on the %s', 'add-to-all' ), '<a href="https://www.google.com/webmasters/verification/verification" target="_blank">' . esc_html__( 'verification details page', 'add-to-all' ) . '</a>' ),
					'type'    => 'text',
					'options' => '',
				),
				'bing_verification'       => array(
					'id'      => 'bing_verification',
					'name'    => esc_html__( 'Bing', 'add-to-all' ),
					/* translators: 1: Bing verification details page. */
					'desc'    => sprintf( esc_html__( 'Value of the content portion of the HTML tag method on the %s', 'add-to-all' ), '<a href="https://www.bing.com/webmaster/" target="_blank">' . esc_html__( 'verification details page', 'add-to-all' ) . '</a>' ),
					'type'    => 'text',
					'options' => '',
				),
				'pinterest_verification'  => array(
					'id'      => 'pinterest_verification',
					'name'    => esc_html__( 'Pinterest', 'add-to-all' ),
					/* translators: 1: Pinterest meta tag details page. */
					'desc'    => sprintf( esc_html__( 'Read how to get the Meta Tag from the %s', 'add-to-all' ), '<a href="https://help.pinterest.com/en/articles/confirm-your-website" target="_blank">' . esc_html__( 'Pinterest help page', 'add-to-all' ) . '</a>' ),
					'type'    => 'text',
					'options' => '',
				),
			)
		),
		/*** Header settings */
		'head'        => apply_filters(
			'ata_settings_head',
			array(
				'head_css'        => array(
					'id'      => 'head_css',
					'name'    => esc_html__( 'Custom CSS', 'add-to-all' ),
					'desc'    => esc_html__( 'Add the CSS code without the <style></style> tags.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => '',
				),
				'head_other_html' => array(
					'id'      => 'head_other_html',
					'name'    => esc_html__( 'HTML to add to the header', 'add-to-all' ),
					/* translators: 1: Code. */
					'desc'    => sprintf( esc_html__( 'The code entered here is added to %1$s. Please ensure that you enter valid HTML or JavaScript.', 'add-to-all' ), '<code>wp_head()</code>' ),
					'type'    => 'textarea',
					'options' => '',
				),
			)
		),
		/*** Content settings */
		'content'     => apply_filters(
			'ata_settings_content',
			array(
				'content_filter_priority'        => array(
					'id'      => 'content_filter_priority',
					'name'    => esc_html__( 'Content filter priority', 'add-to-all' ),
					'desc'    => esc_html__( 'A higher number will cause the Add to All output to be processed after other filters. Number below 10 is not recommended.', 'add-to-all' ),
					'type'    => 'text',
					'options' => 999,
				),
				'content_header_all'             => array(
					'id'   => 'content_header_all',
					'name' => '<h3>' . esc_html__( 'All posts and pages', 'easy-digital-downloads' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'content_add_html_before'        => array(
					'id'      => 'content_add_html_before',
					'name'    => esc_html__( 'Add HTML before content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_before'            => array(
					'id'      => 'content_html_before',
					'name'    => esc_html__( 'HTML to add before the content', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => '',
				),
				'content_add_html_after'         => array(
					'id'      => 'content_add_html_after',
					'name'    => esc_html__( 'Add HTML after content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_after'             => array(
					'id'      => 'content_html_after',
					'name'    => esc_html__( 'HTML to add after the content', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => '',
				),
				'content_header_single'          => array(
					'id'   => 'content_header_single',
					'name' => '<h3>' . esc_html__( 'Single posts and pages', 'easy-digital-downloads' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'content_add_html_before_single' => array(
					'id'      => 'content_add_html_before_single',
					'name'    => esc_html__( 'Add HTML before content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_before_single'     => array(
					'id'      => 'content_html_before_single',
					'name'    => esc_html__( 'HTML to add before the content', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => '',
				),
				'content_add_html_after_single'  => array(
					'id'      => 'content_add_html_after_single',
					'name'    => esc_html__( 'Add HTML after content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_after_single'      => array(
					'id'      => 'content_html_after_single',
					'name'    => esc_html__( 'HTML to add after the content', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => '',
				),
			)
		),
		/*** Footer settings */
		'footer'      => apply_filters(
			'ata_settings_footer',
			array(
				'footer_other_html' => array(
					'id'      => 'footer_other_html',
					'name'    => esc_html__( 'HTML to add to the footer', 'add-to-all' ),
					/* translators: 1: Code. */
					'desc'    => sprintf( esc_html__( 'The code entered here is added to %1$s. Please ensure that you enter valid HTML or JavaScript.', 'add-to-all' ), '<code>wp_footer()</code>' ),
					'type'    => 'textarea',
					'options' => '',
				),
			)
		),
		/*** Feed settings */
		'feed'        => apply_filters(
			'ata_settings_feed',
			array(
				'feed_add_copyright'   => array(
					'id'      => 'feed_add_copyright',
					'name'    => esc_html__( 'Add copyright notice?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the below copyright notice to your feed.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => true,
				),
				'feed_copyrightnotice' => array(
					'id'      => 'feed_copyrightnotice',
					'name'    => esc_html__( 'Coyright text', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML only. This copyright notice is added as the last item of your feed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => ata_get_copyright_text(),
				),
				'feed_add_title'       => array(
					'id'      => 'feed_add_title',
					'name'    => esc_html__( 'Add post title?', 'add-to-all' ),
					'desc'    => esc_html__( 'Add a link to the title of the post in the feed.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => true,
				),
				'feed_title_text'      => array(
					'id'      => 'feed_title_text',
					'name'    => esc_html__( 'Title text', 'add-to-all' ),
					/* translators: No strings here. */
					'desc'    => esc_html__( 'The above text will be added to the feed. You can use %title% to add a link to the post, %date% and %time% to display the date and time of the post respectively.', 'add-to-all' ),
					'type'    => 'textarea',
					/* translators: No strings here. */
					'options' => esc_html__( '%title% was first posted on %date% at %time%.', 'add-to-all' ),
				),
				'feed_add_html_before' => array(
					'id'      => 'feed_add_html_before',
					'name'    => esc_html__( 'Add HTML before content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'feed_html_before'     => array(
					'id'      => 'feed_html_before',
					'name'    => esc_html__( 'HTML to add before the content', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => '',
				),
				'feed_add_html_after'  => array(
					'id'      => 'feed_add_html_after',
					'name'    => esc_html__( 'Add HTML after content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'feed_html_after'      => array(
					'id'      => 'feed_html_after',
					'name'    => esc_html__( 'HTML to add after the content', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => '',
				),
				'add_credit'           => array(
					'id'      => 'add_credit',
					'name'    => esc_html__( 'Add a link to "Add to All" plugin page', 'add-to-all' ),
					'desc'    => '',
					'type'    => 'checkbox',
					'options' => false,
				),
			)
		),
	);

	/**
	 * Filters the settings array
	 *
	 * @since 1.2.0
	 *
	 * @param array $ata_setings Settings array
	 */
	return apply_filters( 'ata_registered_settings', $ata_settings );

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
				$options[ $option['id'] ] = '1';
			}
			// If an option is set.
			if ( in_array( $option['type'], array( 'textarea', 'text', 'csv' ), true ) && ! empty( $option['options'] ) ) {
				$options[ $option['id'] ] = $option['options'];
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
 * Copyright notice text.
 *
 * @since 1.2.0
 * @return string Copyright notice
 */
function ata_get_copyright_text() {

	$copyrightnotice  = '&copy;' . date( 'Y' ) . ' &quot;<a href="' . get_option( 'home' ) . '">' . get_option( 'blogname' ) . '</a>&quot;. ';
	$copyrightnotice .= esc_html__( 'Use of this feed is for personal non-commercial use only. If you are not reading this article in your feed reader, then the site is guilty of copyright infringement. Please contact me at ', 'ald_ata_plugin' );
	$copyrightnotice .= '<!--email_off-->' . get_option( 'admin_email' ) . '<!--/email_off-->';

	/**
	 * Copyright notice text.
	 *
	 * @since 1.2.0
	 * @param string $copyrightnotice Copyright notice
	 */
	return apply_filters( 'ata_copyright_text', $copyrightnotice );
}


/**
 * Upgrade v1.1.0 settings to v1.2.0.
 *
 * @since v1.2.0
 * @return array Settings array
 */
function ata_upgrade_settings() {
	$old_settings = get_option( 'ald_ata_settings' );

	if ( empty( $old_settings ) ) {
		return false;
	} else {
		$map = array(
			'add_credit'                     => 'addcredit',

			// Content options.
			'content_html_before'            => 'content_htmlbefore',
			'content_html_after'             => 'content_htmlafter',
			'content_add_html_before'        => 'content_addhtmlbefore',
			'content_add_html_after'         => 'content_addhtmlafter',
			'content_html_before_single'     => 'content_htmlbeforeS',
			'content_html_after_single'      => 'content_htmlafterS',
			'content_add_html_before_single' => 'content_addhtmlbeforeS',
			'content_add_html_after_single'  => 'content_addhtmlafterS',
			'content_filter_priority'        => 'content_filter_priority',

			// Feed options.
			'feed_html_before'               => 'feed_htmlbefore',
			'feed_html_after'                => 'feed_htmlafter',
			'feed_add_html_before'           => 'feed_addhtmlbefore',
			'feed_add_html_after'            => 'feed_addhtmlafter',
			'feed_copyrightnotice'           => 'feed_copyrightnotice',
			'feed_add_title'                 => 'feed_addtitle',
			'feed_title_text'                => 'feed_titletext',
			'feed_add_copyright'             => 'feed_addcopyright',

			// 3rd party options.
			'sc_project'                     => 'tp_sc_project',
			'sc_security'                    => 'tp_sc_security',
			'ga_uacct'                       => 'tp_ga_uacct',
			'tynt_id'                        => 'tp_tynt_id',

			// Footer options.
			'footer_other_html'              => 'ft_other',

			// Header options.
			'head_css'                       => 'head_CSS',
			'head_other_html'                => 'head_other',
		);

		foreach ( $map as $key => $value ) {
			$settings[ $key ] = strval( $old_settings[ $value ] );
		}

		delete_option( 'ald_ata_settings' );

		return $settings;
	}

}
