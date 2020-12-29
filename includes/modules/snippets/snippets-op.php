<?php
/**
 * Functions to perform snippet operations.
 *
 * @link  https://webberzone.com
 * @since 1.7.0
 *
 * @package Add_to_All
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Retrieves an array of the latest snippets, or snippets matching the given criteria.
 *
 * The defaults are as follows:
 *
 * @since 1.7.0
 *
 * @see WP_Query::parse_query()
 *
 * @param array $args {
 *     Optional. Arguments to retrieve posts. See WP_Query::parse_query() for all
 *     available arguments.
 *
 *     @type int        $numberposts      Total number of posts to retrieve. Is an alias of `$posts_per_page`
 *                                        in WP_Query. Accepts -1 for all. Default -1.
 *     @type int[]      $include          An array of post IDs to retrieve, sticky posts will be included.
 *                                        Is an alias of `$post__in` in WP_Query. Default empty array.
 *     @type int[]      $exclude          An array of post IDs not to retrieve. Default empty array.
 * }
 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs.
 */
function ata_get_snippets( $args = null ) {
	$defaults = array(
		'numberposts' => -1,
		'include'     => array(),
		'exclude'     => array(),
		'post_type'   => 'ata_snippets',
	);

	$parsed_args = wp_parse_args( $args, $defaults );

	/**
	 * Override arguments passed to the get_posts function.
	 *
	 * @since 1.7.0
	 *
	 * @param array $parse_args Arguments passed to the get_posts function.
	 */
	$parsed_args = apply_filters( 'ata_get_snippets_args', $parsed_args );

	$snippets = get_posts( $parsed_args );

	/**
	 * Array of the latest snippets, or snippets matching the given criteria.
	 *
	 * @since 1.7.0
	 *
	 * @param WP_Post[]|int[] Array of snippet objects or snippet IDs.
	 * @param array $parse_args Arguments passed to the get_posts function.
	 */
	return apply_filters( 'ata_get_snippets', $snippets, $parsed_args );
}


/**
 * Retrieves the snippet data given a snippet ID or object.
 *
 * @since 1.7.0
 *
 * @param int|WP_Post $snippet Snippet ID or object.
 * @return WP_Post|string `WP_Post` instance on success or error message on failure.
 */
function ata_get_snippet( $snippet ) {

	$_snippet = get_post( $snippet );

	if ( ! ( $_snippet instanceof WP_Post ) || 'ata_snippets' !== get_post_type( $_snippet ) ) {
		return __( 'Incorrect snippet ID', 'add-to-all' );
	}

	/**
	 * Retrieves the snippet data given a snippet ID or object.
	 *
	 * @since 1.7.0
	 *
	 * @param WP_Post     $_snippet `WP_Post` instance.
	 * @param int|WP_Post $snippet  Snippet ID or object (input).
	 */
	return apply_filters( 'ata_get_snippet', $_snippet, $snippet );
}


/**
 * Retrieves an array of the latest snippets based on the location specified.
 *
 * @since 1.7.0
 *
 * @param string $location    Location of the snippet. Valid options are header, footer, content_before and content_after.
 * @param int    $numberposts Optional. Number of snippets to retrieve. Default is -1.
 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
 */
function ata_get_snippets_by_location( $location, $numberposts = -1 ) {

	if ( empty( $location ) ) {
		return false;
	}

	switch ( $location ) {
		case 'header':
		case 'footer':
			$key = '_ata_add_to_' . $location;
			break;
		case 'content_before':
		case 'content_after':
			$key = '_ata_' . $location;
			break;
		default:
			return false;
	}

	$args = array(
		'numberposts' => $numberposts,
		'meta_query'  => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			array(
				'key'   => $key,
				'value' => 1,
			),
		),
	);

	$snippets = ata_get_snippets( $args );

	/**
	 * Retrieves the snippet data given a snippet ID or object.
	 *
	 * @since 1.7.0
	 *
	 * @param WP_Post[]|int[] $snippets    Array of snippet objects or snippet IDs on success or false on failure.
	 * @param string          $location    Location of the snippet. Valid options are header, footer, content_before and content_after.
	 * @param int             $numberposts Optional. Number of snippets to retrieve. Default is -1.
	 */
	return apply_filters( 'ata_get_snippets_by_location', $snippets, $location, $numberposts );
}


/**
 * Retrieves an array of the latest header snippets.
 *
 * @since 1.7.0
 *
 * @param int $numberposts Optional. Number of snippets to retrieve. Default is -1.
 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
 */
function ata_get_header_snippets( $numberposts = -1 ) {

	return ata_get_snippets_by_location( 'header', $numberposts );
}


/**
 * Retrieves an array of the latest footer snippets.
 *
 * @since 1.7.0
 *
 * @param int $numberposts Optional. Number of snippets to retrieve. Default is -1.
 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
 */
function ata_get_footer_snippets( $numberposts = -1 ) {

	return ata_get_snippets_by_location( 'footer', $numberposts );
}


/**
 * Retrieves an array of the latest content_before snippets.
 *
 * @since 1.7.0
 *
 * @param int $numberposts Optional. Number of snippets to retrieve. Default is -1.
 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
 */
function ata_get_content_before_snippets( $numberposts = -1 ) {

	return ata_get_snippets_by_location( 'content_before', $numberposts );
}


/**
 * Retrieves an array of the latest content_after snippets.
 *
 * @since 1.7.0
 *
 * @param int $numberposts Optional. Number of snippets to retrieve. Default is -1.
 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
 */
function ata_get_content_after_snippets( $numberposts = -1 ) {

	return ata_get_snippets_by_location( 'content_after', $numberposts );
}


/**
 * Function to add snippets code to the header. Filters `wp_head`.
 *
 * @since 1.7.0
 *
 * @param string $location Location of the snippet. Valid options are header, footer, content_before and content_after.
 * @param string $before   Text to display before the output.
 * @param string $after    Text to display after the output.
 * @return string Content of snippets for the specified location.
 */
function ata_get_snippets_content_by_location( $location, $before = '', $after = '' ) {

	global $post;

	switch ( $location ) {
		case 'header':
		case 'footer':
		case 'content_before':
		case 'content_after':
			$snippets = call_user_func( "ata_get_{$location}_snippets" );
			break;
		default:
			return '';
	}

	$output    = $before;
	$all_terms = array();

	// Get taxonomies for the current post.
	$taxes = get_object_taxonomies( $post );

	foreach ( $taxes as $tax ) {
		$terms = get_the_terms( $post->ID, $tax );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$term_taxonomy_id = wp_list_pluck( $terms, 'term_taxonomy_id' );
		} else {
			$term_taxonomy_id = array();
		}
		$all_terms = array_merge( $all_terms, $term_taxonomy_id );
	}

	foreach ( $snippets as $snippet ) {
		$include_on_terms = array();

		// Process post IDs and post types.
		$include_relation     = get_post_meta( $snippet->ID, '_ata_include_relation', true );
		$include_relation     = ! empty( $include_relation ) ? $include_relation : 'or';
		$include_on_posts     = get_post_meta( $snippet->ID, '_ata_include_on_posts', true );
		$include_on_posts     = $include_on_posts ? explode( ',', $include_on_posts ) : array();
		$include_on_posttypes = get_post_meta( $snippet->ID, '_ata_include_on_posttypes', true );
		$include_on_posttypes = $include_on_posttypes ? explode( ',', $include_on_posttypes ) : array();

		// Process taxonomies.
		foreach ( $taxes as $tax ) {
			$include_on       = get_post_meta( $snippet->ID, '_ata_include_on_' . $tax . '_ids', true );
			$include_on       = $include_on ? explode( ',', $include_on ) : array();
			$include_on_terms = array_merge( $include_on_terms, $include_on );
		}

		if ( empty( $include_on_posts ) && empty( $include_on_posttypes ) && empty( $include_on_terms ) ) {
			$include_code = true;
		}
		if ( 'or' === $include_relation ) {
			if ( ( ! empty( $include_on_posts ) && in_array( $post->ID, $include_on_posts ) ) // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			|| ( ! empty( $include_on_posttypes ) && in_array( $post->post_type, $include_on_posttypes ) ) // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			|| ( ! empty( $include_on_terms ) && 0 !== count( array_intersect( $all_terms, $include_on_terms ) ) ) // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			) {
				$include_code = true;
			} else {
				$include_code = false;
			}
		} else {
			if ( ! empty( $include_on_posts ) ) {
				$condition[] = 1;
				if ( in_array( $post->ID, $include_on_posts ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					$include_code[] = 1;
				} else {
					$include_code[] = 0;
				}
			}
			if ( ! empty( $include_on_posttypes ) ) {
				$condition[] = 1;
				if ( in_array( $post->post_type, $include_on_posttypes ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
					$include_code[] = 1;
				} else {
					$include_code[] = 0;
				}
			}
			if ( ! empty( $include_on_terms ) ) {
				$condition[] = 1;
				if ( count( array_intersect( $all_terms, $include_on_terms ) ) ) {
					$include_code[] = 1;
				} else {
					$include_code[] = 0;
				}
			}
			$include_code = ( array_sum( $condition ) === array_sum( $include_code ) ) ? true : false;
		}
		if ( $include_code ) {
			$output .= do_shortcode( $snippet->post_content );
		}
	}

	$output .= $after;

	return $output;
}


/**
 * Function to add snippets code to the header. Filters `wp_head`.
 *
 * @since 1.7.0
 */
function ata_snippets_header() {
	echo ata_get_snippets_content_by_location( 'header', ata_snippets_credit() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'ata_snippets_header' );


/**
 * Function to add snippets code to the footer. Filters `wp_footer`.
 *
 * @since 1.7.0
 */
function ata_snippets_footer() {
	echo ata_get_snippets_content_by_location( 'footer', ata_snippets_credit() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_footer', 'ata_snippets_footer' );


/**
 * Function to add snippets code to the footer. Filters `wp_footer`.
 *
 * @since 1.7.0
 *
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ata_snippets_content( $content ) {

	$before = ata_snippets_credit() . '<div class="ata_snippets">';
	$after  = '</div>';

	$str_before = ata_get_snippets_content_by_location( 'content_before' );
	if ( $str_before ) {
		$str_before = $before . $str_before . $after;
	}

	$str_after = ata_get_snippets_content_by_location( 'content_after' );
	if ( $str_after ) {
		$str_after = $before . $str_after . $after;
	}

	return $str_before . $content . $str_after;
}
add_filter( 'the_content', 'ata_snippets_content', ata_get_option( 'content_filter_priority', 10 ) );


/**
 * Function to add snippets credit line.
 *
 * @since 1.7.0
 *
 * @return string Snippets credit line.
 */
function ata_snippets_credit() {

	/**
	 * Filter the snippets credit line.
	 *
	 * @since 1.7.0
	 *
	 * @param string $text Snippets credit line.
	 */
	return apply_filters( 'ata_snippets_credit', '<!-- Snippets by Add to All -->' );
}

