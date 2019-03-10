<?php
/**
 * Fired when the plugin is uninstalled
 *
 * @package Add_to_All
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

if ( is_multisite() ) {

	// Get all blogs in the network and activate plugin on each one.
	$blogids = $wpdb->get_col( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		"
		SELECT blog_id FROM $wpdb->blogs
		WHERE archived = '0' AND spam = '0' AND deleted = '0'
	"
	);

	foreach ( $blogids as $blogid ) {
		switch_to_blog( $blogid );
		ata_delete_data();
		restore_current_blog();
	}
} else {
	ata_delete_data();
}


/**
 * Delete Data.
 *
 * @since 1.4.0
 */
function ata_delete_data() {

	delete_option( 'ata_settings' );
	delete_option( 'ald_ata_settings' );

}

