<?php
/**
 * Functions to add to the footer.
 *
 * @link  https://ajaydsouza.com
 * @since 1.2.0
 *
 * @package	Add_to_All
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to add the necessary code to `wp_footer`.
 */
function ald_ata_footer() {
	global $ata_settings;

	$ata_other = stripslashes( $ata_settings['footer_other_html'] );
	$sc_project = stripslashes( $ata_settings['sc_project'] );
	$sc_security = stripslashes( $ata_settings['sc_security'] );
	$ga_uacct = stripslashes( $ata_settings['ga_uacct'] );
	$ga_linker = stripslashes( $ata_settings['ga_linker'] );

	// Add other footer.
	if ( '' !== $ata_other ) {
		echo $ata_other; // WPCS: XSS OK.
	}

	// Add Statcounter code.
	ata_statcounter( $sc_project, $sc_security );

	// Add Google Analytics.
	ata_ga( $ga_uacct, $ga_linker );

}
add_action( 'wp_footer', 'ald_ata_footer' );


