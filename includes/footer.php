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

	$ata_other = ata_get_option( 'footer_other_html', '' );
	$sc_project = ata_get_option( 'sc_project', '' );
	$sc_security = ata_get_option( 'sc_security', '' );
	$ga_uacct = ata_get_option( 'ga_uacct', '' );
	$ga_linker = ata_get_option( 'ga_linker', '' );

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


