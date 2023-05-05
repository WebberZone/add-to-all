<?php
/**
 * WebberZone Snippetz Shortcodes.
 *
 * @since 1.8.0
 *
 * @package WebberZone\Snippetz
 */

namespace WebberZone\Snippetz\Frontend;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ATA Shortcodes class.
 *
 * @version 1.0
 * @since   1.8.0
 */
class Shortcodes {

	/**
	 * Main constructor.
	 */
	public function __construct() {
		add_shortcode( 'ata_reading_time', array( $this, 'reading_time' ) );
	}

	/**
	 * Estimaged reading time shortcode.
	 *
	 * @param array  $atts Attributes array.
	 * @param string $content Shortcode Content.
	 */
	public function reading_time( $atts, $content = null ) {

		$atts = shortcode_atts(
			array(
				'post'   => get_post(),
				'wpm'    => 200,
				'before' => '',
				'after'  => '',
			),
			$atts,
			'ata_reading_time'
		);

		$post = get_post( $atts['post'] );
		if ( ! $post ) {
			return '';
		}

		if ( empty( $content ) ) {
			$content = get_post_field( 'post_content', $post );
		}

		$output  = sprintf( '<span class="ata_reading_time ata_reading_time_%s">', $post->ID );
		$output .= \WebberZone\Snippetz\Util\Helpers::get_reading_time( $content, $atts );
		$output .= '</span>';

		return $output;
	}

}
