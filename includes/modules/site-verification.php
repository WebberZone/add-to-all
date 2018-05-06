<?php
/**
 * Site Verification functions.
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
 * Site Verification.
 *
 * @since 1.2.0
 */
function ata_site_verification() {

	ata_site_verification_google();
	ata_site_verification_bing();
	ata_site_verification_pinterest();

	/**
	 * Site Verification action.
	 *
	 * @since 1.2.0
	 */
	do_action( 'ata_site_verification' );
}
add_action( 'wp_head', 'ata_site_verification' );

/**
 * Google Site Verification.
 *
 * @since 1.2.0
 */
function ata_site_verification_google() {

	$verification_code = ata_get_option( 'google_verification', '' );

	if ( '' !== $verification_code ) {
?>
		<meta name="google-site-verification" content="<?php echo esc_attr( $verification_code ); ?>" />
<?php
	}

}

/**
 * Bing Site Verification.
 *
 * @since 1.2.0
 */
function ata_site_verification_bing() {

	$verification_code = ata_get_option( 'bing_verification', '' );

	if ( '' !== $verification_code ) {
?>
		<meta name="msvalidate.01" content="<?php echo esc_attr( $verification_code ); ?>" />
<?php
	}

}

/**
 * Pinterest Site Verification.
 *
 * @since 1.2.0
 */
function ata_site_verification_pinterest() {

	$verification_code = ata_get_option( 'pinterest_verification', '' );

	if ( '' !== $verification_code ) {
?>
		<meta name="p:domain_verify" content="<?php echo esc_attr( $verification_code ); ?>" />
<?php
	}

}
