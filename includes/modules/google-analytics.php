<?php
/**
 * Google Analytics.
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
 * Function to generate Google Analytics code.
 *
 * @since 1.2.0
 *
 * @param string $ga_uacct Google Analytics Site ID.
 * @param string $ga_linker Google Analytics Linker Domains.
 * @param string $ga_anonymize_ip Google Analytics Anonymize IP.
 */
function ata_ga( $ga_uacct, $ga_linker, $ga_anonymize_ip ) {

	if ( '' !== $ga_uacct ) {
?>

	<!-- Global site tag (gtag.js) - Google Analytics -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( $ga_uacct ); ?>"></script>
	<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', '<?php echo esc_attr( $ga_uacct ); ?>', {

		<?php
		if ( $ga_anonymize_ip ) {
		?>
		'anonymize_ip': true,
		<?php
		}

		if ( '' !== $ga_linker ) {

			$ga_domains = explode( ',', $ga_linker );

			foreach ( $ga_domains as $ga_domain ) {
				$ga_linkers[] = "'" . $ga_domain . "'";
			}

			$ga_linker = implode( ', ', $ga_linkers );

		?>

		'linker': {
			'domains': [<?php echo $ga_linker; ?>]
		}

		<?php
		}

		?>

	});
	</script>
	<!-- End Google Analytics // Added by Add to All WordPress plugin -->

<?php
	}

}

