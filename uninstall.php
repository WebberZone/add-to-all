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

delete_option( 'ald_ata_settings' );
delete_option( 'ata_settings' );

