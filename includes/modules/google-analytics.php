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
 * @param string $ga_linker Google Linker Domains.
 */
function ata_ga( $ga_uacct, $ga_linker ) {

	if ( '' !== $ga_uacct ) {
?>

	<!-- Start Google Analytics -->
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		ga('create', '<?php echo esc_attr( $ga_uacct ); ?>', 'auto'

	<?php if ( '' !== $ga_linker ) { ?>
		, {allowLinker: true}
	<?php } ?>
		);

	<?php
	if ( '' !== $ga_linker ) {

		$ga_domains = explode( ',', $ga_linker );

		foreach ( $ga_domains as $ga_domain ) {
			$ga_linkers[] = "'" . $ga_domain . "'";
		}

		$ga_linker = implode( ', ', $ga_linkers );

	?>
		ga('require', 'linker');
		ga('linker:autoLink', [<?php echo esc_attr( $ga_linker ); ?>]);

	<?php } ?>

		ga('send', 'pageview');

	</script>
	<!-- End Google Analytics // Added by Add to All WordPress plugin -->

<?php
	}

}

