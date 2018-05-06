<?php
/**
 * Help tab.
 *
 * Functions to generated the help tab on the Settings page.
 *
 * @link  https://ajaydsouza.com
 * @since 1.2.0
 *
 * @package Add_to_All
 * @subpackage Admin/Help
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Function to add the content of the help tab.
 *
 * @since 1.2.0
 */
function ata_settings_help() {
	global $ata_settings_page;

	$screen = get_current_screen();

	if ( $screen->id !== $ata_settings_page ) {
		return;
	}

	// Set the text in the help sidebar.
	$screen->set_help_sidebar(
		/* translators: 1: Plugin support site link. */
		'<p>' . sprintf( __( 'For more information or how to get support visit the <a href="%s">support site</a>.', 'add-to-all' ), esc_url( 'https://ajaydsouza.com/support/' ) ) . '</p>' .
		/* translators: 1: WordPress.org support forums link. */
			'<p>' . sprintf( __( 'Support queries should be posted in the <a href="%s">WordPress.org support forums</a>.', 'add-to-all' ), esc_url( 'https://wordpress.org/support/plugin/add-to-all' ) ) . '</p>' .
		'<p>' . sprintf(
			/* translators: 1: Github issues link, 2: Github plugin page link. */
			__( '<a href="%1$s">Post an issue</a> on <a href="%2$s">GitHub</a> (bug reports only).', 'add-to-all' ),
			esc_url( 'https://github.com/ajaydsouza/add-to-all/issues' ),
			esc_url( 'https://github.com/ajaydsouza/add-to-all' )
		) . '</p>'
	);

	// Add third party help tab.
	$screen->add_help_tab(
		array(
			'id'      => 'ata-settings-third-party-help',
			'title'   => esc_html__( 'Third Party', 'add-to-all' ),
			'content' =>
				'<p><strong>' . esc_html__( 'This screen provides the settings for configuring the integration with third party scripts.', 'add-to-all' ) . '</strong></p>' .
				'<p>' . sprintf(
					/* translators: 1: Google Analystics help article. */
					esc_html__( 'Google Analytics tracking can be found by visiting this %s', 'add-to-all' ),
					'<a href="https://support.google.com/analytics/answer/1008080?hl=en" target="_blank">' . esc_html__( 'article', 'add-to-all' ) . '</a>.'
				) .
				'</p>',
			'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
		)
	);

	// Add Header help tab.
	$screen->add_help_tab(
		array(
			'id'      => 'ata-settings-header-help',
			'title'   => esc_html__( 'Header', 'add-to-all' ),
			'content' =>
				'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the header of your site.', 'add-to-all' ) . '</strong></p>' .
				'<p>' . esc_html__( 'You can add custom CSS or HTML code. Useful for adding meta tags for site verification, etc.', 'add-to-all' ) . '</p>' .
				'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
		)
	);

	// Add Content help tab.
	$screen->add_help_tab(
		array(
			'id'      => 'ata-settings-content-help',
			'title'   => esc_html__( 'Content', 'add-to-all' ),
			'content' =>
				'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the content of posts, pages and custom post types.', 'add-to-all' ) . '</strong></p>' .
				'<p>' . esc_html__( 'You can set the priority of the filter and choose if you want this to be displayed on either all content (including archives) or just single posts/pages.', 'add-to-all' ) . '</p>' .
				'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
		)
	);

	// Add Footer help tab.
	$screen->add_help_tab(
		array(
			'id'      => 'ata-settings-footer-help',
			'title'   => esc_html__( 'Footer', 'add-to-all' ),
			'content' =>
				'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the footer of your site.', 'add-to-all' ) . '</strong></p>' .
				'<p>' . esc_html__( 'You can add custom HTML code. Useful for adding tracking code for analytics, etc.', 'add-to-all' ) . '</p>' .
				'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
		)
	);

	// Add Feed help tab.
	$screen->add_help_tab(
		array(
			'id'      => 'ata-settings-feed-help',
			'title'   => esc_html__( 'Feed', 'add-to-all' ),
			'content' =>
				'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the feed of your site.', 'add-to-all' ) . '</strong></p>' .
				'<p>' . esc_html__( 'You can add copyright text, a link to the title and date of the post, and HTML before and after the content', 'add-to-all' ) . '</p>' .
				'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
		)
	);

	/**
	 * Action to add more help settings.
	 *
	 * @since 1.2.0
	 */
	do_action( 'ata_settings_help', $screen );
}


