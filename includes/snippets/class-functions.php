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

use WebberZone\Snippetz\Util\Helpers;
use WebberZone\Snippetz\Util\Hook_Registry;

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
		$priority = ata_get_option( 'snippet_priority', ata_get_option( 'content_filter_priority', 10 ) );

		Hook_Registry::add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_snippets' ) );
		Hook_Registry::add_action( 'wp_head', array( $this, 'snippets_header' ), $priority );
		Hook_Registry::add_action( 'wp_footer', array( $this, 'snippets_footer' ), $priority );

		Hook_Registry::add_filter( 'the_content', array( $this, 'snippets_content' ), $priority );

		Hook_Registry::add_action( 'save_post_ata_snippets', array( $this, 'save_snippet_file' ), 10, 2 );
		Hook_Registry::add_action( 'before_delete_post', array( $this, 'delete_snippet_file' ) );
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
	 * @return \WP_Post[]|int[] Array of snippet objects or snippet IDs.
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
		 * @param \WP_Post[]|int[] $snippets Array of snippet objects or snippet IDs.
		 * @param array $parse_args Arguments passed to the get_posts function.
		 */
		return apply_filters( 'ata_get_snippets', $snippets, $parsed_args );
	}


	/**
	 * Retrieves the snippet data given a snippet ID or object.
	 *
	 * @since 2.0.0
	 *
	 * @param int|\WP_Post $snippet Snippet ID or object.
	 * @return \WP_Post|string `WP_Post` instance on success or error message on failure.
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
		 * @param \WP_Post     $_snippet `WP_Post` instance.
		 * @param int|\WP_Post $snippet  Snippet ID or object (input).
		 */
		return apply_filters( 'ata_get_snippet', $_snippet, $snippet );
	}

	/**
	 * Get Snippet Content by ID.
	 *
	 * @since 2.1.0
	 *
	 * @param int|\WP_Post $snippet Snippet ID or object.
	 * @param array        $args   Arguments passed to the get_posts function.
	 * @return string Snippet content.
	 */
	public static function get_snippet_content( $snippet, $args = array() ) {
		$classes  = array();
		$defaults = array(
			'class'        => '',
			'is_block'     => false,
			'is_shortcode' => true,
		);
		$args     = wp_parse_args( $args, $defaults );
		$args     = Helpers::sanitize_args( $args );

		$snippet = get_post( $snippet );

		if ( ! ( $snippet instanceof \WP_Post ) || 'ata_snippets' !== get_post_type( $snippet ) ) {
			return __( 'Incorrect snippet ID', 'add-to-all' );
		}

		$id = $snippet->ID;

		$classes[] = 'ata_snippet';
		$classes[] = 'ata_snippet_' . $id;
		$classes[] = $args['class'];
		$classes[] = $args['is_block'] ? 'ata_snippet_block' : '';
		$classes[] = $args['is_shortcode'] ? 'ata_snippet_shortcode' : '';
		$class     = implode( ' ', array_filter( $classes ) );

		$content = do_shortcode( $snippet->post_content );
		$type    = self::get_snippet_type( $snippet );

		if ( 'html' === $type ) {
			$content = sprintf( '<div class="%s">%s</div>', $class, $content );
		}

		$output = self::wrap_output( $content, $type, $snippet->ID );

		/**
		 * Retrieves the snippet content given a snippet ID or object.
		 *
		 * @since 2.1.0
		 *
		 * @param string   $output  Snippet content.
		 * @param \WP_Post $snippet Snippet ID or object (input).
		 * @param array    $args    Arguments.
		 */
		return apply_filters( 'ata_get_snippet_content', $output, $snippet, $args );
	}


	/**
	 * Retrieves an array of the latest snippets based on the location specified.
	 *
	 * @since 2.0.0
	 *
	 * @param string $location    Location of the snippet. Valid options are header, footer, content_before and content_after.
	 * @param int    $numberposts Optional. Number of snippets to retrieve. Default is -1.
	 * @return \WP_Post[]|int[]|false Array of snippet objects or snippet IDs on success or false on failure.
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
		 * @param \WP_Post[]|int[] $snippets    Array of snippet objects or snippet IDs on success or false on failure.
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
	 * @return \WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
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
	 * @return \WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
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
	 * @return \WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
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
	 * @return \WP_Post[]|int[] Array of snippet objects or snippet IDs on success or false on failure.
	 */
	public static function get_content_after_snippets( $numberposts = -1 ) {

		return self::get_snippets_by_location( 'content_after', $numberposts );
	}

	/**
	 * Function to enqueue snippets for the header. Filters `wp_enqueue_scripts`.
	 *
	 * @since 2.3.0
	 */
	public static function enqueue_snippets() {
		self::enqueue_combined_assets();
		self::get_snippets_content_by_location( 'header', '', '', -1, true );
		self::get_snippets_content_by_location( 'footer', '', '', -1, true );
		self::get_snippets_content_by_location( 'content_before', '', '', -1, true );
		self::get_snippets_content_by_location( 'content_after', '', '', -1, true );
	}

	/**
	 * Enqueue combined CSS/JS assets when enabled and available.
	 *
	 * @since 2.3.0
	 *
	 * @return array<string,bool> Map of asset types to enqueue status.
	 */
	public static function enqueue_combined_assets() {
		static $status = null;
		if ( null !== $status ) {
			return $status;
		}

		$status = array(
			'css' => false,
			'js'  => false,
		);

		if ( ! ata_get_option( 'enable_combination' ) ) {
			return $status;
		}

		foreach ( array( 'css', 'js' ) as $type ) {
			$stats = Minifier::get_file_stats( 'combined.min.' . $type );
			if ( ! $stats ) {
				continue;
			}

			$handle = 'ata-combined-' . $type;
			$ver    = isset( $stats['mtime'] ) ? $stats['mtime'] : false;
			if ( 'css' === $type && ! wp_style_is( $handle, 'enqueued' ) ) {
				wp_enqueue_style( $handle, esc_url( $stats['url'] ), array(), $ver );
				$status['css'] = true;
			}
			if ( 'js' === $type && ! wp_script_is( $handle, 'enqueued' ) ) {
				wp_enqueue_script( $handle, esc_url( $stats['url'] ), array(), $ver, true );
				$status['js'] = true;
			}
		}

		return $status;
	}

	/**
	 * Function to add snippets code to the header. Filters `wp_head`.
	 *
	 * @since 2.0.0
	 *
	 * @param string $location    Location of the snippet. Valid options are header, footer, content_before and content_after.
	 * @param string $before      Text to display before the output.
	 * @param string $after       Text to display after the output.
	 * @param int    $numberposts Optional. Number of snippets to retrieve. Default is -1.
	 * @param bool   $enqueue_only Optional. Whether to only enqueue scripts and styles. Default false.
	 * @return string Content of snippets for the specified location.
	 */
	public static function get_snippets_content_by_location( $location, $before = '', $after = '', $numberposts = -1, $enqueue_only = false ) {

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
			function ( $a, $b ) {
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
			$include_on_posts     = wp_parse_list( get_post_meta( $snippet->ID, '_ata_include_on_posts', true ) );
			$include_on_posts     = array_filter( $include_on_posts );
			$include_on_posttypes = wp_parse_list( get_post_meta( $snippet->ID, '_ata_include_on_posttypes', true ) );
			$include_on_posttypes = array_filter( $include_on_posttypes );

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
				$condition = array();
				$include   = array();
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
				$type = self::get_snippet_type( $snippet );
				if ( $enqueue_only && 'html' === $type ) {
					continue;
				}

				$in_footer = ( 'header' === $location ) ? false : true;
				$output[]  = self::wrap_output(
					do_shortcode( $snippet->post_content ),
					$type,
					$snippet->ID,
					$in_footer
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
	 * Function to add snippets code to the footer. Filters `the_content`.
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
	 * @param \WP_Post $snippet Snippet object.
	 * @return string Snippet type.
	 */
	public static function get_snippet_type( $snippet ) {
		$snippet_type = get_post_meta( $snippet->ID, '_ata_snippet_type', true );
		$snippet_type = ( $snippet_type ) ? $snippet_type : 'html';

		return $snippet_type;
	}

	/**
	 * Get the external file URL for a snippet if it exists.
	 *
	 * @since 2.3.0
	 *
	 * @param int    $snippet_id  Snippet ID.
	 * @param string $snippet_type Snippet type.
	 * @param bool   $update_meta Whether to update the snippet meta when the file exists.
	 * @return string External file URL or empty string.
	 */
	public static function get_snippet_file_url( $snippet_id, $snippet_type, $update_meta = true ) {
		if ( ! $snippet_id || ! in_array( $snippet_type, array( 'css', 'js' ), true ) ) {
			return '';
		}

		$file_url = get_post_meta( $snippet_id, '_ata_snippet_file', true );
		if ( $file_url ) {
			$upload_dir = wp_upload_dir();
			$file_path  = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $file_url );
			if ( ! file_exists( $file_path ) ) {
				$file_url = '';
			}
		}

		if ( ! $file_url ) {
			$file_path = Minifier::get_snippet_path( $snippet_id, $snippet_type );
			if ( file_exists( $file_path ) ) {
				$file_url = Minifier::get_snippet_url( $snippet_id, $snippet_type );
				if ( $update_meta ) {
					update_post_meta( $snippet_id, '_ata_snippet_file', $file_url );
				}
			}
		}

		return $file_url;
	}

	/**
	 * Wrap output in style or script tags depending on snippet type.
	 *
	 * @param string $output The output to be wrapped.
	 * @param string $snippet_type The type of the snippet: 'css' or 'js'.
	 * @param int    $snippet_id The snippet ID.
	 * @param bool   $in_footer Whether to enqueue the script in the footer. Default true.
	 * @return string The wrapped output.
	 */
	public static function wrap_output( $output, $snippet_type, $snippet_id = 0, $in_footer = true ) {
		// Check if the snippet type is valid.
		if ( ! in_array( $snippet_type, array( 'css', 'js' ), true ) ) {
			return $output;
		}

		if ( ata_get_option( 'enable_combination' ) ) {
			$combined_status = self::enqueue_combined_assets();
			if ( ! empty( $combined_status[ $snippet_type ] ) ) {
				return '';
			}
		}

		$output = str_replace( self::snippets_credit(), '', $output );
		$handle = 'ata-' . $snippet_type . '-' . $snippet_id;

		// Check if external file is available.
		if ( $snippet_id && ata_get_option( 'enable_external_css_js' ) ) {
			$file_url = self::get_snippet_file_url( $snippet_id, $snippet_type );
			if ( $file_url ) {
				$upload_dir = wp_upload_dir();
				$file_path  = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $file_url );
				$ver        = file_exists( $file_path ) ? filemtime( $file_path ) : false;
				if ( 'css' === $snippet_type ) {
					if ( ! wp_style_is( $handle, 'enqueued' ) ) {
						wp_enqueue_style( $handle, esc_url( $file_url ), array(), $ver );
					}
					return '';
				} elseif ( 'js' === $snippet_type ) {
					if ( ! wp_script_is( $handle, 'enqueued' ) ) {
						wp_enqueue_script( $handle, esc_url( $file_url ), array(), $ver, $in_footer );
					}
					return '';
				}
			}
		}

		// Enqueue inline styles or scripts.
		if ( 'css' === $snippet_type ) {
			if ( ! wp_style_is( $handle, 'enqueued' ) ) {
				wp_register_style( $handle, false, array(), '1.0' );
				wp_enqueue_style( $handle );
				wp_add_inline_style( $handle, $output );
			}
		} elseif ( 'js' === $snippet_type ) {
			if ( ! wp_script_is( $handle, 'enqueued' ) ) {
				wp_register_script( $handle, false, array(), '1.0', $in_footer );
				wp_enqueue_script( $handle );
				wp_add_inline_script( $handle, $output );
			}
		}

		return '';
	}

	/**
	 * Get snippet type styles.
	 *
	 * @param \WP_Post $snippet Snippet object.
	 * @return array Snippet type styles. Includes four keys: 'type', 'color', 'background', 'tag'.
	 */
	public static function get_snippet_type_styles( $snippet ) {
		$snippet_type   = self::get_snippet_type( $snippet );
		$styles['type'] = $snippet_type;

		switch ( $snippet_type ) {
			case 'html':
				$styles['color']      = '#a23114';
				$styles['background'] = '#ffffff';
				$styles['tag']        = 'html';
				break;
			case 'css':
				$styles['color']      = '#1c44e2';
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

	/**
	 * Generate or update the external file for a snippet.
	 *
	 * @since 2.3.0
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @return bool True when file written, false otherwise.
	 */
	protected static function generate_snippet_file( $post_id, $post ) {
		if ( 'ata_snippets' !== $post->post_type ) {
			return false;
		}

		if ( ! ata_get_option( 'enable_external_css_js' ) ) {
			return false;
		}

		$type = get_post_meta( $post_id, '_ata_snippet_type', true );
		if ( ! in_array( $type, array( 'css', 'js' ), true ) ) {
			return false;
		}

		$content  = get_post_field( 'post_content', $post_id );
		$content  = Helpers::process_placeholders( $content );
		$minified = ( 'css' === $type ) ? Minifier::minify_css( $content ) : Minifier::minify_js( $content );

		$dir = Minifier::get_upload_path();
		wp_mkdir_p( $dir );

		$file_path = Minifier::get_snippet_path( $post_id, $type );
		$written   = Minifier::write_snippet_file( $file_path, $minified );
		if ( ! $written ) {
			return false;
		}

		$old_url = get_post_meta( $post_id, '_ata_snippet_file', true );
		$url     = Minifier::get_snippet_url( $post_id, $type );
		if ( $old_url && $old_url !== $url ) {
			Minifier::delete_snippet_file_by_url( $old_url );
		}
		update_post_meta( $post_id, '_ata_snippet_file', $url );

		return true;
	}

	/**
	 * Regenerate external files for all CSS/JS snippets.
	 *
	 * @since 2.3.0
	 *
	 * @return int Count of updated snippets.
	 */
	public static function regenerate_snippet_files() {
		$snippets = get_posts(
			array(
				'post_type'      => 'ata_snippets',
				'posts_per_page' => -1,
				'no_found_rows'  => true,
				'fields'         => 'all',
				'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => '_ata_snippet_type',
						'value'   => array( 'css', 'js' ),
						'compare' => 'IN',
					),
				),
			)
		);

		if ( empty( $snippets ) ) {
			return 0;
		}

		$count = 0;
		foreach ( $snippets as $snippet ) {
			if ( $snippet instanceof \WP_Post && self::generate_snippet_file( $snippet->ID, $snippet ) ) {
				++$count;
			}
		}

		return $count;
	}

	/**
	 * Save snippet as external file if enabled.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 */
	public function save_snippet_file( $post_id, $post ) {
		self::generate_snippet_file( $post_id, $post );

		if ( ata_get_option( 'enable_combination' ) ) {
			Minifier::save_combined_css();
			Minifier::save_combined_js();
		}
	}

	/**
	 * Delete snippet file on post deletion.
	 *
	 * @param int $post_id Post ID.
	 */
	public function delete_snippet_file( $post_id ) {
		if ( 'ata_snippets' !== get_post_type( $post_id ) ) {
			return;
		}

		$url = get_post_meta( $post_id, '_ata_snippet_file', true );
		if ( $url ) {
			$upload_dir = wp_upload_dir();
			$path       = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url );
			if ( file_exists( $path ) ) {
				wp_delete_file( $path );
			}
		}
	}
}
