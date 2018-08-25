<?php
/**
 * Functions to add to the footer.
 *
 * @link  https://ajaydsouza.com
 * @since 1.2.0
 *
 * @package Add_to_All
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to add the necessary code to `wp_footer`.
 */
function ald_ata_footer() {

	$footer_other_html = ata_footer_other_html();
	$sc_project        = ata_get_option( 'sc_project', '' );
	$sc_security       = ata_get_option( 'sc_security', '' );
	$ga_uacct          = ata_get_option( 'ga_uacct', '' );
	$ga_linker         = ata_get_option( 'ga_linker', '' );
	$ga_anonymize_ip   = ata_get_option( 'ga_anonymize_ip', false );

	// Add other footer.
	if ( '' !== $footer_other_html ) {
		echo $footer_other_html; // WPCS: XSS OK.
	}

	// Add Statcounter code.
	ata_statcounter( $sc_project, $sc_security );

	// Add Google Analytics.
	ata_ga( $ga_uacct, $ga_linker, $ga_anonymize_ip );

}
add_action( 'wp_footer', 'ald_ata_footer' );


/**
 * Get the HTML to be added to the footer.
 *
 * @since 1.3.0
 */
function ata_footer_other_html() {

	$output = ata_get_option( 'footer_other_html', '' );

	/**
	 * Get the HTML to be added to the footer.
	 *
	 * @since 1.3.0
	 * @param $output HTML added to the footer
	 */
	return apply_filters( 'ata_footer_other_html', $output );
}

if ( ata_get_option( 'footer_process_shortcode' ) ) {

	add_filter( 'ata_footer_other_html', 'shortcode_unautop' );
	add_filter( 'ata_footer_other_html', 'do_shortcode' );

}
