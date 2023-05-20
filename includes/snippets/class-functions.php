<?php
/**
 * Functions to perform snippet operations.
 *
 * @link  https://webberzone.com
 * @since 2.0.0
 *
 * @package WebberZone\Snippetz
 */

namespace WebberZone\Snippetz\Snippets;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Functions to perform snippet operations.
 *
 * @version 1.0
 * @since   2.0.0
 */
class Functions {

	/**
	 * Constructor function.
	 */
	public function __construct() {
		add_action( 'wp_head', array( $this, 'snippets_header' ) );
		add_action( 'wp_footer', array( $this, 'snippets_footer' ) );
		$priority = ata_get_option( 'snippet_priority', ata_get_option( 'content_filter_priority', 10 ), 10 );
		add_filter( 'the_content', array( $this, 'snippets_content' ), $priority );
	}

	/**
	 * Retrieves an array of the latest snippets, or snippets matching the given criteria.
	 *
	 * The defaults are as follows:
	 *
	 * @since 2.0.0
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
	public static function get_snippets( $args = array() ) {
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
		 * @since 2.0.0
		 *
		 * @param array $parse_args Arguments passed to the get_posts function.
		 */
		$parsed_args = apply_filters( 'ata_get_snippets_args', $parsed_args );

		$snippets = get_posts( $parsed_args );

		/**
		 * Array of the latest snippets, or snippets matching the given criteria.
		 *
		 * @since 2.0.0
		 *
		 * @param WP_Post[]|int[] Array of snippet objects or snippet IDs.
		 * @param array $parse_args Arguments passed to the get_posts function.
		 */
		return apply_filters( 'ata_get_snippets', $snippets, $parsed_args );
	}


	/**
	 * Retrieves the snippet data given a snippet ID or object.
	 *
	 * @since 2.0.0
	 *
	 * @param int|WP_Post $snippet Snippet ID or object.
	 * @return WP_Post|string `WP_Post` instance on success or error message on failure.
	 */
	public static function get_snippet( $snippet ) {

		$_snippet = get_post( $snippet );

		if ( ! ( $_snippet instanceof \WP_Post ) || 'ata_snippets' !== get_post_type( $_snippet ) ) {
			return __( 'Incorrect snippet ID', 'add-to-all' );
		}

		/**
		 * Retrieves the snippet data given a snippet ID or object.
		 *
		 * @since 2.0.0
		 *
		 * @param WP_Post     $_snippet `WP_Post` instance.
		 * @param int|WP_Post $snippet  Snippet ID or object (input).
		 */
		return apply_filters( 'ata_get_snippet', $_snippet, $snippet );
	}


	/**
	 * Retrieves an array of the latest snippets based on the location specified.
	 *
	 * @since 2.0.0
	 *
	 * @param string $location    Location of the snippet. Valid options are header, footer, content_before and content_after.
	 * @param int    $numberposts Optional. Number of snippets to retrieve. Default is -1.
	 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
	 */
	public static function get_snippets_by_location( $location, $numberposts = -1 ) {

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

		$snippets = self::get_snippets( $args );

		/**
		 * Retrieves the snippet data given a snippet ID or object.
		 *
		 * @since 2.0.0
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
	 * @since 2.0.0
	 *
	 * @param int $numberposts Optional. Number of snippets to retrieve. Default is -1.
	 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
	 */
	public static function get_header_snippets( $numberposts = -1 ) {

		return self::get_snippets_by_location( 'header', $numberposts );
	}


	/**
	 * Retrieves an array of the latest footer snippets.
	 *
	 * @since 2.0.0
	 *
	 * @param int $numberposts Optional. Number of snippets to retrieve. Default is -1.
	 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
	 */
	public static function get_footer_snippets( $numberposts = -1 ) {

		return self::get_snippets_by_location( 'footer', $numberposts );
	}


	/**
	 * Retrieves an array of the latest content_before snippets.
	 *
	 * @since 2.0.0
	 *
	 * @param int $numberposts Optional. Number of snippets to retrieve. Default is -1.
	 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
	 */
	public static function get_content_before_snippets( $numberposts = -1 ) {

		return self::get_snippets_by_location( 'content_before', $numberposts );
	}


	/**
	 * Retrieves an array of the latest content_after snippets.
	 *
	 * @since 2.0.0
	 *
	 * @param int $numberposts Optional. Number of snippets to retrieve. Default is -1.
	 * @return WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
	 */
	public static function get_content_after_snippets( $numberposts = -1 ) {

		return self::get_snippets_by_location( 'content_after', $numberposts );
	}


	/**
	 * Function to add snippets code to the header. Filters `wp_head`.
	 *
	 * @since 2.0.0
	 *
	 * @param string $location    Location of the snippet. Valid options are header, footer, content_before and content_after.
	 * @param string $before      Text to display before the output.
	 * @param string $after       Text to display after the output.
	 * @param string $numberposts Optional. Number of snippets to retrieve. Default is -1.
	 * @return string Content of snippets for the specified location.
	 */
	public static function get_snippets_content_by_location( $location, $before = '', $after = '', $numberposts = -1 ) {

		global $post;

		switch ( $location ) {
			case 'header':
			case 'footer':
			case 'content_before':
			case 'content_after':
				$method_name = "get_{$location}_snippets";
				if ( method_exists( __CLASS__, $method_name ) ) {
					$snippets = self::$method_name( $numberposts );
				} else {
					return '';
				}
				break;
			default:
				return '';
		}

		if ( empty( $snippets ) ) {
			return '';
		}

		$snippets_with_priority = array();
		foreach ( $snippets as $snippet ) {
			$priority                 = get_post_meta( $snippet->ID, '_ata_include_priority', true );
			$snippets_with_priority[] = array(
				'snippet'  => $snippet,
				'priority' => $priority,
			);
		}

		// Sort the snippets by priority.
		usort(
			$snippets_with_priority,
			function( $a, $b ) {
				$priority_a = ! empty( $a['priority'] ) ? $a['priority'] : 10;
				$priority_b = ! empty( $b['priority'] ) ? $b['priority'] : 10;

				// Higher priority comes later.
				return $priority_a - $priority_b;
			}
		);

		$output[]  = $before;
		$all_terms = array();

		// Get taxonomies for the current post.
		$taxes = get_object_taxonomies( $post );

		foreach ( $taxes as $tax ) {
			$terms = get_the_terms( $post->ID, $tax );
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				$term_taxonomy_ids = wp_list_pluck( $terms, 'term_taxonomy_id' );
				$all_terms         = array_merge( $all_terms, $term_taxonomy_ids );
			}
		}

		foreach ( $snippets_with_priority  as $item ) {
			$snippet          = $item['snippet'];
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
				$include_on       = get_post_meta( $snippet->ID, "_ata_include_on_{$tax}_ids", true );
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
					$include[]   = in_array( $post->ID, $include_on_posts ) ? 1 : 0; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				}
				if ( ! empty( $include_on_posttypes ) ) {
					$condition[] = 1;
					$include[]   = in_array( $post->post_type, $include_on_posttypes ) ? 1 : 0; // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				}
				if ( ! empty( $include_on_terms ) ) {
					$condition[] = 1;
					$include[]   = count( array_intersect( $all_terms, $include_on_terms ) ) ? 1 : 0;
				}
				$include_code = ( array_sum( $condition ) === array_sum( $include ) ) ? true : false;
			}
			if ( $include_code ) {
				$output[] = self::wrap_output(
					do_shortcode( $snippet->post_content ),
					self::get_snippet_type( $snippet )
				);
			}
		}

		$output[] = $after;

		return implode( '', $output );
	}


	/**
	 * Function to add snippets code to the header. Filters `wp_head`.
	 *
	 * @since 2.0.0
	 */
	public static function snippets_header() {
		echo self::get_snippets_content_by_location( 'header', self::snippets_credit() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}



	/**
	 * Function to add snippets code to the footer. Filters `wp_footer`.
	 *
	 * @since 2.0.0
	 */
	public static function snippets_footer() {
		echo self::get_snippets_content_by_location( 'footer', self::snippets_credit() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}



	/**
	 * Function to add snippets code to the footer. Filters `wp_footer`.
	 *
	 * @since 2.0.0
	 *
	 * @param string $content Post content.
	 * @return string Filtered post content
	 */
	public static function snippets_content( $content ) {

		$before = self::snippets_credit() . '<div class="ata_snippets">';
		$after  = '</div>';

		$str_before = self::get_snippets_content_by_location( 'content_before' );
		if ( $str_before ) {
			$str_before = $before . $str_before . $after;
		}

		$str_after = self::get_snippets_content_by_location( 'content_after' );
		if ( $str_after ) {
			$str_after = $before . $str_after . $after;
		}

		return $str_before . $content . $str_after;
	}


	/**
	 * Function to add snippets credit line.
	 *
	 * @since 2.0.0
	 *
	 * @return string Snippets credit line.
	 */
	public static function snippets_credit() {

		/**
		 * Filter the snippets credit line.
		 *
		 * @since 2.0.0
		 *
		 * @param string $text Snippets credit line.
		 */
		return apply_filters( 'ata_snippets_credit', '<!-- Snippets by WebberZone Snippetz -->' );
	}

	/**
	 * Get snippet type.
	 *
	 * @since 2.0.0
	 *
	 * @param WP_Post $snippet Snippet object.
	 * @return string Snippet type.
	 */
	public static function get_snippet_type( $snippet ) {
		$snippet_type = get_post_meta( $snippet->ID, '_ata_snippet_type', true );
		$snippet_type = ( $snippet_type ) ? $snippet_type : 'html';

		return $snippet_type;
	}

	/**
	 * Wrap output in style or script tags depending on snippet type.
	 *
	 * @param string $output The output to be wrapped.
	 * @param string $snippet_type The type of the snippet: 'css' or 'js'.
	 * @return string The wrapped output.
	 */
	public static function wrap_output( $output, $snippet_type ) {
		// Check if the snippet type is valid.
		if ( ! in_array( $snippet_type, array( 'css', 'js' ), true ) ) {
			return $output;
		}

		$output = str_replace( self::snippets_credit(), '', $output );

		// Wrap the output in style or script tags accordingly using a switch statement.
		switch ( $snippet_type ) {
			case 'css':
				return '<style type="text/css">' . $output . '</style>';
			case 'js':
				return '<script type="text/javascript">' . $output . '</script>';
			default:
				return $output;
		}
	}

	/**
	 * Get snippet type styles.
	 *
	 * @param WP_Post $snippet Snippet object.
	 * @return array Snippet type styles. Includes four keys: 'type', 'color', 'background', 'tag'.
	 */
	public static function get_snippet_type_styles( $snippet ) {
		$snippet_type   = self::get_snippet_type( $snippet );
		$styles['type'] = $snippet_type;

		switch ( $snippet_type ) {
			case 'html':
				$styles['color']      = '#e34c26';
				$styles['background'] = '#ffffff';
				$styles['tag']        = 'html';
				break;
			case 'css':
				$styles['color']      = '#264de4';
				$styles['background'] = '#ffffff';
				$styles['tag']        = 'style';
				break;
			case 'js':
				$styles['background'] = '#F0DB4F';
				$styles['color']      = '#323330';
				$styles['tag']        = 'script';
				break;
		}
		return $styles;
	}

}
