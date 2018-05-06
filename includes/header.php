<?php
/**
 * Functions to add to the header.
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
 * Function to add custom code to the header. Filters `wp_head`.
 */
function ald_ata_header() {

	$ata_head_other_html = ata_head_other_html();
	$ata_head_css        = ata_get_option( 'head_css', '' );
	$ata_tynt_id         = ata_get_option( 'tynt_id', '' );

	// Add CSS to header.
	if ( '' !== $ata_head_css ) {
		echo '<style type="text/css">' . $ata_head_css . '</style>'; // WPCS: XSS OK.
	}

	// Add Tynt.
	ata_tynt( $ata_tynt_id );

	// Add other header.
	if ( '' !== $ata_head_other_html ) {
		echo $ata_head_other_html; // WPCS: XSS OK.
	}

}
add_action( 'wp_head', 'ald_ata_header' );


/**
 * Get the HTML to be added to the header.
 *
 * @since 1.3.0
 */
function ata_head_other_html() {

	$output = ata_get_option( 'head_other_html', '' );

	/**
	 * Get the HTML to be added to the header.
	 *
	 * @since 1.3.0
	 * @param $output HTML added to the header
	 */
	return apply_filters( 'ata_head_other_html', $output );
}

