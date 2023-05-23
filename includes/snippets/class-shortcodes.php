<?php
/**
 * Register Post Type.
 *
 * @since 1.7.0
 *
 * @package WebberZone\Snippetz
 */

namespace WebberZone\Snippetz\Snippets;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ATA Shortcode class.
 *
 * @version 1.0
 * @since   1.7.0
 */
class Shortcodes {

	/**
	 * Main constructor.
	 */
	public function __construct() {
		add_shortcode( 'ata_snippet', array( $this, 'snippet' ) );
	}

	/**
	 * Snippets shortcode. Returns the post content of the snippet for a specific ID.
	 *
	 * @param array $atts Attributes array.
	 */
	public function snippet( $atts ) {

		// Normalize attribute keys, lowercase.
		$atts = array_change_key_case( (array) $atts, CASE_LOWER );

		$atts = shortcode_atts(
			array(
				'id' => 0,
			),
			$atts,
			'ata_snippet'
		);

		$id      = absint( $atts['id'] );
		$snippet = Functions::get_snippet( $id );

		$content = is_object( $snippet ) ? $snippet->post_content : $snippet;

		$output  = sprintf( '<div class="ata_snippet ata_snippet_%s">', $id );
		$output .= do_shortcode( $content );
		$output .= '</div>';

		return $output;
	}

}
