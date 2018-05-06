<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://ajaydsouza.com
 * @since 1.2.0
 *
 * @package    Add_to_All
 * @subpackage Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Creates the admin submenu pages under the Downloads menu and assigns their
 * links to global variables
 *
 * @since 1.2.0
 *
 * @global $ata_settings_page
 * @return void
 */
function ata_add_admin_pages_links() {
	global $ata_settings_page;

	$ata_settings_page = add_options_page( __( 'Add to All', 'add-to-all' ), __( 'Add to All', 'add-to-all' ), 'manage_options', 'ata_options_page', 'ata_options_page' );

	// Load the settings contextual help.
	add_action( "load-$ata_settings_page", 'ata_settings_help' );
}
add_action( 'admin_menu', 'ata_add_admin_pages_links' );


/**
 * Add rating links to the admin dashboard
 *
 * @since 1.2.0
 *
 * @param string $footer_text The existing footer text.
 * @return string Updated Footer text
 */
function ata_admin_footer( $footer_text ) {

	global $ata_settings_page;

	if ( get_current_screen()->id === $ata_settings_page ) {

		$text = sprintf(
			__( 'Thank you for using <a href="%1$s" target="_blank">Add to All</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'add-to-all' ),
			'https://ajaydsouza.com/wordpress/plugins/add-to-all',
			'https://wordpress.org/support/plugin/add-to-all/reviews/#new-post'
		);

		return str_replace( '</span>', '', $footer_text ) . ' | ' . $text . '</span>';

	} else {

		return $footer_text;

	}
}
add_filter( 'admin_footer_text', 'ata_admin_footer' );


/**
 * Adding WordPress plugin action links.
 *
 * @param array $links Array of links.
 * @return array
 */
function ata_plugin_actions_links( $links ) {

	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=ata_options_page' ) . '">' . esc_html__( 'Settings', 'add-to-all' ) . '</a>',
		),
		$links
	);

}
add_filter( 'plugin_action_links_' . plugin_basename( ATA_PLUGIN_FILE ), 'ata_plugin_actions_links' );


/**
 * Add meta links on Plugins page.
 *
 * @param array  $links Array of Links.
 * @param string $file Current file.
 * @return array
 */
function ata_plugin_actions( $links, $file ) {

	if ( false !== strpos( $file, 'add-to-all.php' ) ) {
		$links[] = '<a href="http://wordpress.org/support/plugin/add-to-all">' . esc_html__( 'Support', 'add-to-all' ) . '</a>';
		$links[] = '<a href="https://ajaydsouza.com/donate/">' . esc_html__( 'Donate', 'add-to-all' ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'ata_plugin_actions', 10, 2 );

