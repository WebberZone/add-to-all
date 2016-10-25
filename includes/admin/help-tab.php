<?php
/**
 * Help tab.
 *
 * Functions to generated the help tab on the Settings page.
 *
 * @link  https://ajaydsouza.com
 * @since 1.2.0
 *
 * @package	Add_to_All
 * @subpackage Admin/Help
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


function ata_settings_help() {
	global $ata_settings_page;

	$screen = get_current_screen();

	if ( $screen->id !== $ata_settings_page ) {
		return;
	}

	$screen->set_help_sidebar(
		'<p>' . sprintf( __( 'For more information or how to get support visit the <a href="%s">support site</a>.', 'add-to-all' ), esc_url( 'https://ajaydsouza.com/support/' ) ) . '</p>' .
		'<p>' . sprintf( __( 'Support queries should be posted in the <a href="%s">WordPress.org support forums</a>.', 'add-to-all' ), esc_url( 'https://wordpress.org/support/plugin/knowledgebase' ) ) . '</p>' .
		'<p>' . sprintf(
			__( '<a href="%s">Post an issue</a> on <a href="%s">GitHub</a> (bug reports only).', 'add-to-all' ),
			esc_url( 'https://github.com/WebberZone/knowledgebase/issues' ),
			esc_url( 'https://github.com/WebberZone/knowledgebase' )
		) . '</p>'
	);

	$screen->add_help_tab( array(
		'id'	    => 'ata-settings-third-party',
		'title'	    => __( 'Third Party', 'add-to-all' ),
		'content'	=>
			'<p><strong>' . __( 'This screen provides the settings for configuring the integration with third party scripts.', 'add-to-all' ) . '</strong></p>' .
			'<p>' . sprintf( esc_html__( 'Google Analytics tracking can be founds by visiting this help article: %s', 'add-to-all' ), esc_url( 'https://support.google.com/analytics/answer/1008080?hl=en' ) )  . '</p>',
	) );


	do_action( 'ata_settings_help', $screen );

}
