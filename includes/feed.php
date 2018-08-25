<?php
/**
 * Functions to add to the feed.
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
 * Function to add content to RSS feeds. Filters `the_excerpt_rss` and `the_content_feed`.
 *
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ald_ata_rss( $content ) {
	global $ata_settings;

	if ( isset( $ata_settings['feed_add_html_before'] ) || isset( $ata_settings['feed_add_html_after'] ) || isset( $ata_settings['feed_add_title'] ) || isset( $ata_settings['feed_add_copyright'] ) || isset( $ata_settings['add_credit'] ) ) {
		$str_before = '';
		$str_after  = '<hr style="border-top:black solid 1px" />';

		if ( isset( $ata_settings['feed_add_html_before'] ) && $ata_settings['feed_add_html_before'] ) {
			$str_before .= ata_feed_html_before();
			$str_before .= '<br />';
		}

		if ( isset( $ata_settings['feed_add_html_after'] ) && $ata_settings['feed_add_html_after'] ) {
			$str_after .= ata_feed_html_after();
			$str_after .= '<br />';
		}

		if ( isset( $ata_settings['feed_add_title'] ) && $ata_settings['feed_add_title'] ) {
			$str_after .= ata_feed_title_text();
			$str_after .= '<br />';
		}

		if ( isset( $ata_settings['feed_add_copyright'] ) && $ata_settings['feed_add_copyright'] ) {
			$str_after .= ata_get_option( 'feed_copyrightnotice', '' );
			$str_after .= '<br />';
		}

		if ( isset( $ata_settings['add_credit'] ) && $ata_settings['add_credit'] ) {
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


/**
 * Get the HTML to be added before the content in the feed.
 *
 * @since 1.3.0
 */
function ata_feed_html_before() {

	$output = ata_get_option( 'feed_html_before', '' );

	/**
	 * Filters the HTML to be added before the content in the feed.
	 *
	 * @since 1.3.0
	 * @param $output HTML added before the feed
	 */
	return apply_filters( 'ata_feed_html_before', $output );
}


/**
 * Get the HTML to be added after the content in the feed.
 *
 * @since 1.3.0
 */
function ata_feed_html_after() {

	$output = ata_get_option( 'feed_html_after', '' );

	/**
	 * Filters the HTML to be added after the content in the feed.
	 *
	 * @since 1.3.0
	 * @param $output HTML added after the feed
	 */
	return apply_filters( 'ata_feed_html_after', $output );
}


/**
 * Get title text to be added after the content in the feed.
 *
 * @since 1.3.0
 */
function ata_feed_title_text() {

	$title         = '<a href="' . get_permalink() . '">' . the_title( '', '', false ) . '</a>';
	$search_array  = array(
		'%title%',
		'%date%',
		'%time%',
	);
	$replace_array = array(
		$title,
		get_the_time( 'F j, Y' ),
		get_the_time( 'g:i a' ),
	);

	$output = str_replace( $search_array, $replace_array, ata_get_option( 'feed_title_text', '' ) );

	/**
	 * Filters title text to be added after the content in the feed.
	 *
	 * @since 1.3.0
	 * @param $output HTML added after the feed
	 */
	return apply_filters( 'ata_feed_title_text', $output );
}

/**
 * Get the credit line - link to Add to All plugin page.
 *
 * @since 1.3.0
 */
function ata_creditline() {

	$output  = '<br /><span style="font-size: 0.8em">';
	$output .= __( 'Feed enhanced by ', 'add-to-all' );
	$output .= '<a href="https://ajaydsouza.com/wordpress/plugins/add-to-all/" rel="nofollow">Add To All</a>';
	$output .= '</span>';

	/**
	 * Filters the credit line.
	 *
	 * @since 1.3.0
	 * @param $output HTML added after the feed
	 */
	return apply_filters( 'ata_creditline', $output );
}

if ( ata_get_option( 'feed_process_shortcode' ) ) {

	add_filter( 'ata_feed_html_before', 'shortcode_unautop' );
	add_filter( 'ata_feed_html_before', 'do_shortcode' );

	add_filter( 'ata_feed_html_after', 'shortcode_unautop' );
	add_filter( 'ata_feed_html_after', 'do_shortcode' );

}
