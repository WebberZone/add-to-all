<?php
/**
 * Functions to add to the_content.
 *
 * @link  https://webberzone.com
 * @since 1.2.0
 *
 * @package Add_to_All
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to modify the_content filter priority.
 *
 * @since 1.0.3
 */
function ata_content_prepare_filter() {

	$priority = ata_get_option( 'content_filter_priority', 10 );

	add_filter( 'the_content', 'ata_content', $priority );
}
add_action( 'template_redirect', 'ata_content_prepare_filter' );


/**
 * Function to add custom HTML before and after the post content. Filters `the_content`.
 *
 * @since 1.0
 *
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ata_content( $content ) {
	global $post, $ata_settings;

	$exclude_on_post_ids = explode( ',', ata_get_option( 'exclude_on_post_ids' ) );

	if ( isset( $post ) ) {
		if ( in_array( $post->ID, $exclude_on_post_ids ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			return $content;    // Exit without adding content.
		}
	}

	if ( ( is_singular() ) || ( is_home() ) || ( is_archive() ) ) {
		$str_before = '';
		$str_after  = '';

		if ( is_singular() ) {
			if ( isset( $ata_settings['content_add_html_before'] ) && $ata_settings['content_add_html_before'] ) {
				$str_before .= ata_content_html_before();
			}

			if ( isset( $ata_settings['content_add_html_after'] ) && $ata_settings['content_add_html_after'] ) {
				$str_after .= ata_content_html_after();
			}

			if ( isset( $ata_settings['content_add_html_before_single'] ) && $ata_settings['content_add_html_before_single'] ) {
				$str_before .= ata_content_html_before_single();
			}

			if ( isset( $ata_settings['content_add_html_after_single'] ) && $ata_settings['content_add_html_after_single'] ) {
				$str_after .= ata_content_html_after_single();
			}
		}

		if ( is_single() ) {
			if ( isset( $ata_settings['content_add_html_before_post'] ) && $ata_settings['content_add_html_before_post'] ) {
				$str_before .= ata_content_html_before_post();
			}

			if ( isset( $ata_settings['content_add_html_after_post'] ) && $ata_settings['content_add_html_after_post'] ) {
				$str_after .= ata_content_html_after_post();
			}
		}

		if ( is_page() ) {
			if ( isset( $ata_settings['content_add_html_before_page'] ) && $ata_settings['content_add_html_before_page'] ) {
				$str_before .= ata_content_html_before_page();
			}

			if ( isset( $ata_settings['content_add_html_after_page'] ) && $ata_settings['content_add_html_after_page'] ) {
				$str_after .= ata_content_html_after_page();
			}
		}

		if ( ( is_home() ) || ( is_archive() ) ) {
			if ( isset( $ata_settings['content_add_html_before'] ) && $ata_settings['content_add_html_before'] ) {
				$str_before .= ata_content_html_before();
			}

			if ( isset( $ata_settings['content_add_html_after'] ) && $ata_settings['content_add_html_after'] ) {
				$str_after .= ata_content_html_after();
			}
		}

		return $str_before . $content . $str_after;
	} else {
		return $content;
	}
}


/**
 * Get the HTML to be added before the content.
 *
 * @since 1.3.0
 */
function ata_content_html_before() {

	$output = ata_get_option( 'content_html_before', '' );

	/**
	 * Get the HTML to be added before the content.
	 *
	 * @since 1.3.0
	 * @param $output HTML added before the content
	 */
	return apply_filters( 'ata_content_html_before', $output );
}


/**
 * Get the HTML to be added after the content.
 *
 * @since 1.3.0
 */
function ata_content_html_after() {

	$output = ata_get_option( 'content_html_after', '' );

	/**
	 * Get the HTML to be added after the content.
	 *
	 * @since 1.3.0
	 * @param $output HTML added after the content
	 */
	return apply_filters( 'ata_content_html_after', $output );
}


/**
 * Get the HTML to be added before the content on single pages.
 *
 * @since 1.3.0
 */
function ata_content_html_before_single() {

	$output = ata_get_option( 'content_html_before_single', '' );

	/**
	 * Get the HTML to be added before the content.
	 *
	 * @since 1.3.0
	 * @param $output HTML added before the content
	 */
	return apply_filters( 'ata_content_html_before_single', $output );
}


/**
 * Get the HTML to be added after the content on single pages.
 *
 * @since 1.3.0
 */
function ata_content_html_after_single() {

	$output = ata_get_option( 'content_html_after_single', '' );

	/**
	 * Get the HTML to be added after the content.
	 *
	 * @since 1.3.0
	 * @param $output HTML added after the content
	 */
	return apply_filters( 'ata_content_html_after_single', $output );
}


/**
 * Get the HTML to be added before the content on posts.
 *
 * @since 1.5.0
 */
function ata_content_html_before_post() {

	$output = ata_get_option( 'content_html_before_post', '' );

	/**
	 * Get the HTML to be added before the content.
	 *
	 * @since 1.5.0
	 * @param $output HTML added before the content
	 */
	return apply_filters( 'ata_content_html_before_post', $output );
}


/**
 * Get the HTML to be added after the content on posts.
 *
 * @since 1.5.0
 */
function ata_content_html_after_post() {

	$output = ata_get_option( 'content_html_after_post', '' );

	/**
	 * Get the HTML to be added after the content.
	 *
	 * @since 1.5.0
	 * @param $output HTML added after the content
	 */
	return apply_filters( 'ata_content_html_after_post', $output );
}


/**
 * Get the HTML to be added before the content on pages.
 *
 * @since 1.5.0
 */
function ata_content_html_before_page() {

	$output = ata_get_option( 'content_html_before_page', '' );

	/**
	 * Get the HTML to be added before the content.
	 *
	 * @since 1.5.0
	 * @param $output HTML added before the content
	 */
	return apply_filters( 'ata_content_html_before_page', $output );
}


/**
 * Get the HTML to be added after the content on pages.
 *
 * @since 1.5.0
 */
function ata_content_html_after_page() {

	$output = ata_get_option( 'content_html_after_page', '' );

	/**
	 * Get the HTML to be added after the content.
	 *
	 * @since 1.5.0
	 * @param $output HTML added after the content
	 */
	return apply_filters( 'ata_content_html_after_page', $output );
}


if ( ata_get_option( 'content_process_shortcode' ) ) {

	add_filter( 'ata_content_html_before', 'shortcode_unautop' );
	add_filter( 'ata_content_html_before', 'do_shortcode' );

	add_filter( 'ata_content_html_after', 'shortcode_unautop' );
	add_filter( 'ata_content_html_after', 'do_shortcode' );

	add_filter( 'ata_content_html_before_single', 'shortcode_unautop' );
	add_filter( 'ata_content_html_before_single', 'do_shortcode' );

	add_filter( 'ata_content_html_after_single', 'shortcode_unautop' );
	add_filter( 'ata_content_html_after_single', 'do_shortcode' );

	add_filter( 'ata_content_html_before_post', 'shortcode_unautop' );
	add_filter( 'ata_content_html_before_post', 'do_shortcode' );

	add_filter( 'ata_content_html_after_post', 'shortcode_unautop' );
	add_filter( 'ata_content_html_after_post', 'do_shortcode' );

	add_filter( 'ata_content_html_before_page', 'shortcode_unautop' );
	add_filter( 'ata_content_html_before_page', 'do_shortcode' );

	add_filter( 'ata_content_html_after_page', 'shortcode_unautop' );
	add_filter( 'ata_content_html_after_page', 'do_shortcode' );

}
