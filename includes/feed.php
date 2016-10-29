<?php
/**
 * Functions to add to the feed.
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
 * Function to add content to RSS feeds. Filters `the_excerpt_rss` and `the_content_feed`.
 *
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ald_ata_rss( $content ) {
	global $ata_settings;

	if ( isset( $ata_settings['feed_add_html_before'] ) || isset( $ata_settings['feed_add_html_after'] ) || isset( $ata_settings['feed_add_title'] ) || isset( $ata_settings['feed_add_copyright'] ) || isset( $ata_settings['add_credit'] ) ) {
		$str_before = '';
		$str_after = '<hr style="border-top:black solid 1px" />';

		if ( isset( $ata_settings['feed_add_html_before'] ) ) {
			$str_before .= stripslashes( $ata_settings['feed_html_before'] );
			$str_before .= '<br />';
		}

		if ( isset( $ata_settings['feed_add_html_after'] ) ) {
			$str_after .= stripslashes( $ata_settings['feed_html_after'] );
			$str_after .= '<br />';
		}

		if ( isset( $ata_settings['feed_add_title'] ) ) {
			$title = '<a href="' . get_permalink() . '">' . the_title( '', '', false ) . '</a>';
			$search_array = array(
				'%title%',
				'%date%',
				'%time%',
			);
			$replace_array = array(
				$title,
				get_the_time( 'F j, Y' ),
				get_the_time( 'g:i a' ),
			);
			$str_after .= str_replace( $search_array, $replace_array, $ata_settings['feed_title_text'] );

			$str_after .= '<br />';
		}

		if ( isset( $ata_settings['feed_add_copyright'] ) ) {
			$str_after .= stripslashes( $ata_settings['feed_copyrightnotice'] );
			$str_after .= '<br />';
		}

		if ( isset( $ata_settings['add_credit'] ) ) {
			$creditline = '<br /><span style="font-size: 0.8em">';
			$creditline .= __( 'Feed enhanced by ', 'add-to-all' );
			$creditline .= '<a href="http://ajaydsouza.com/wordpress/plugins/add-to-all/" rel="nofollow">Add To All</a>';

			$str_after .= $creditline;
			$str_after .= '<br />';
		}

		return $str_before . $content . $str_after;
	} else {
		return $content;
	}

}
add_filter( 'the_excerpt_rss', 'ald_ata_rss', 99999999 );
add_filter( 'the_content_feed', 'ald_ata_rss', 99999999 );

