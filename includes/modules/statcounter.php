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
 *
 * @since 1.2.0
 *
 * @param string $sc_project Statcounter Project ID.
 * @param string $sc_security Statcounter Security ID.
 */
function ata_statcounter( $sc_project, $sc_security ) {

	// Add Statcounter code.
	if ( '' !== $sc_project ) {
?>
	<!-- Start of StatCounter Code -->
	<script type="text/javascript">
	// <![CDATA[
		var sc_project=<?php echo esc_attr( $sc_project ); ?>;
		var sc_security="<?php echo esc_attr( $sc_security ); ?>";
		var sc_invisible=1;
		var sc_click_stat=1;
	// ]]>
	</script>
	<script type="text/javascript" src="https://secure.statcounter.com/counter/counter.js"></script>
	<noscript><div class="statcounter"><a title="WordPress hit counter" href="https://statcounter.com/wordpress.org/" class="statcounter"><img class="statcounter" src="//c.statcounter.com/<?php echo esc_attr( $sc_project ); ?>/0/<?php echo esc_attr( $sc_security ); ?>/1/" alt="WordPress hit counter" /></a></div></noscript>
	<!-- End of StatCounter Code // by Add to All WordPress Plugin -->
<?php
	}

}

