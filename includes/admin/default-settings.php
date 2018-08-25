<?php
/**
 * Register settings.
 *
 * Functions to register, read, write and update settings.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @link  https://ajaydsouza.com
 * @since 1.3.0
 *
 * @package Add_to_All
 * @subpackage Admin/Default_Registered_Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


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
				'ga_anonymize_ip'         => array(
					'id'      => 'ga_anonymize_ip',
					'name'    => esc_html__( 'Anonymize IP', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this box to anonymize IPs before they are sent to Google Analytics.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'ga_linker'               => array(
					'id'      => 'ga_linker',
					'name'    => esc_html__( 'Linker domains', 'add-to-all' ),
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
				'exclude_on_post_ids'            => array(
					'id'      => 'exclude_on_post_ids',
					'name'    => esc_html__( 'Exclude display on these post IDs', 'add-to-all' ),
					'desc'    => esc_html__( 'Comma-separated list of post or page IDs to exclude displaying the above content. e.g. 188,320,500', 'add-to-all' ),
					'type'    => 'numbercsv',
					'options' => '',
				),
				'content_process_shortcode'      => array(
					'id'      => 'content_process_shortcode',
					'name'    => esc_html__( 'Process shortcodes in content', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this box to execute any shortcodes that you enter in the options below.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_header_all'             => array(
					'id'   => 'content_header_all',
					'name' => '<h3>' . esc_html__( 'Home and other views', 'easy-digital-downloads' ) . '</h3>',
					'desc' => esc_html__( 'Displays when viewing single posts, home, category, tag and other archives.', 'add-to-all' ),
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
					'name' => '<h3>' . esc_html__( 'Single posts views', 'easy-digital-downloads' ) . '</h3>',
					'desc' => esc_html__( 'Displays when viewing single views including posts, pages, custom-post-types.', 'add-to-all' ),
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
				'footer_process_shortcode' => array(
					'id'      => 'footer_process_shortcode',
					'name'    => esc_html__( 'Process shortcodes in footer', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this box to execute any shortcodes that you enter in the option below.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'footer_other_html'        => array(
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
				'feed_add_copyright'     => array(
					'id'      => 'feed_add_copyright',
					'name'    => esc_html__( 'Add copyright notice?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the below copyright notice to your feed.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => true,
				),
				'feed_copyrightnotice'   => array(
					'id'      => 'feed_copyrightnotice',
					'name'    => esc_html__( 'Coyright text', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML only. This copyright notice is added as the last item of your feed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => ata_get_copyright_text(),
				),
				'feed_add_title'         => array(
					'id'      => 'feed_add_title',
					'name'    => esc_html__( 'Add post title?', 'add-to-all' ),
					'desc'    => esc_html__( 'Add a link to the title of the post in the feed.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => true,
				),
				'feed_title_text'        => array(
					'id'      => 'feed_title_text',
					'name'    => esc_html__( 'Title text', 'add-to-all' ),
					/* translators: No strings here. */
					'desc'    => esc_html__( 'The above text will be added to the feed. You can use %title% to add a link to the post, %date% and %time% to display the date and time of the post respectively.', 'add-to-all' ),
					'type'    => 'textarea',
					/* translators: No strings here. */
					'options' => esc_html__( '%title% was first posted on %date% at %time%.', 'add-to-all' ),
				),
				'feed_process_shortcode' => array(
					'id'      => 'feed_process_shortcode',
					'name'    => esc_html__( 'Process shortcodes in feed', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this box to execute any shortcodes that you enter in the options below.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'feed_add_html_before'   => array(
					'id'      => 'feed_add_html_before',
					'name'    => esc_html__( 'Add HTML before content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'feed_html_before'       => array(
					'id'      => 'feed_html_before',
					'name'    => esc_html__( 'HTML to add before the content', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => '',
				),
				'feed_add_html_after'    => array(
					'id'      => 'feed_add_html_after',
					'name'    => esc_html__( 'Add HTML after content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'feed_html_after'        => array(
					'id'      => 'feed_html_after',
					'name'    => esc_html__( 'HTML to add after the content', 'add-to-all' ),
					'desc'    => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'    => 'textarea',
					'options' => '',
				),
				'add_credit'             => array(
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

