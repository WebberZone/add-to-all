<?php
/**
 * Tynt.
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
 * @param string $ata_tynt_id Tynt ID.
 */
function ata_tynt( $ata_tynt_id ) {

	// Add Tynt code to Header.
	if ( '' !== $ata_tynt_id ) {
	?>

	<!-- Begin 33Across SiteCTRL - Inserted by Add to All WordPress Plugin -->
	<script>
	var Tynt=Tynt||[];Tynt.push('<?php	echo esc_attr( $ata_tynt_id ); ?>');
	(function(){var h,s=document.createElement('script');
	s.src=(window.location.protocol==='https:'?
	'https':'http')+'://cdn.tynt.com/ti.js';
	h=document.getElementsByTagName('script')[0];
	h.parentNode.insertBefore(s,h);})();
	</script>
	<!-- End 33Across SiteCTRL - Inserted by Add to All WordPress Plugin -->

	<?php
	}

}

