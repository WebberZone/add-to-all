<?php
/**
 * Functions to add to the_content.
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
 * Function to modify the_content filter priority.
 */
function ata_content_prepare_filter() {
	global $ata_settings;

	$priority = isset( $ata_settings['content_filter_priority'] ) ? $ata_settings['content_filter_priority'] : 10;

	add_filter( 'the_content', 'ata_content', $priority );
}
add_action( 'template_redirect', 'ata_content_prepare_filter' );


/**
 * Function to add custom HTML before and after the post content. Filters `the_content`.
 *
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ata_content( $content ) {
	global $ata_settings;

	if ( ( is_singular() ) || ( is_home() ) || ( is_archive() ) ) {
		$str_before = '';
		$str_after = '';

		if ( is_singular() ) {
			if ( isset( $ata_settings['content_add_html_before'] ) ) {
				$str_before .= stripslashes( $ata_settings['content_html_before'] );
			}

			if ( isset( $ata_settings['content_add_html_after'] ) ) {
				$str_after .= stripslashes( $ata_settings['content_html_after'] );
			}

			if ( isset( $ata_settings['content_add_html_before_single'] ) ) {
				$str_before .= stripslashes( $ata_settings['content_html_before_single'] );
			}

			if ( isset( $ata_settings['content_add_html_after_single'] ) ) {
				$str_after .= stripslashes( $ata_settings['content_html_after_single'] );
			}
		} elseif ( ( is_home() ) || ( is_archive() ) ) {
			if ( isset( $ata_settings['content_add_html_before'] ) ) {
				$str_before .= stripslashes( $ata_settings['content_html_before'] );
			}

			if ( isset( $ata_settings['content_add_html_after'] ) ) {
				$str_after .= stripslashes( $ata_settings['content_html_after'] );
			}
		}

	    return $str_before . $content . $str_after;
	} else {
		return $content;
	}

}



