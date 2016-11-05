<?php
/**
 * Site Verification functions.
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
 * Google Site Verification.
 *
 * @since 1.2.0
 */
function ata_site_verification_google() {

	$verification_code = ata_get_option( 'google_verification', '' );

	if ( '' !== $verification_code ) {
?>
		<meta name="google-site-verification" content="<?php esc_attr_e( $verification_code ); ?>" />
<?php
	}

}
add_action( 'wp_head', 'ata_site_verification_google' );
