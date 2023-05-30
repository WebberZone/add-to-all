<?php
/**
 * Main plugin class.
 *
 * @package WebberZone\Snippetz
 */

namespace WebberZone\Snippetz;

if ( ! defined( 'WPINC' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * @since 2.0.0
 */
final class Main {
	/**
	 * The single instance of the class.
	 *
	 * @var Main
	 */
	private static $instance;

	/**
	 * Settings.
	 *
	 * @since 2.0.0
	 *
	 * @var object Settings API.
	 */
	public $settings;

	/**
	 * Shortcodes.
	 *
	 * @since 2.0.0
	 *
	 * @var object Shortcodes.
	 */
	public $shortcodes;

	/**
	 * Snippets.
	 *
	 * @since 2.0.0
	 *
	 * @var object Snippets.
	 */
	public $snippets;

	/**
	 * Site verification.
	 *
	 * @since 2.0.0
	 *
	 * @var object Site verification.
	 */
	public $site_verification;

	/**
	 * Third party functions.
	 *
	 * @since 2.0.0
	 *
	 * @var object Third party functions.
	 */
	public $third_party;

	/**
	 * Gets the instance of the class.
	 *
	 * @since 2.0.0
	 *
	 * @return Main
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Main ) ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * A dummy constructor.
	 *
	 * @since 2.0.0
	 */
	private function __construct() {
		// Do nothing.
	}

	/**
	 * Initializes the plugin.
	 *
	 * @since 2.0.0
	 */
	private function init() {
		$this->settings          = new \WebberZone\Snippetz\Admin\Settings\Settings();
		$this->shortcodes        = new \WebberZone\Snippetz\Frontend\Shortcodes();
		$this->site_verification = new \WebberZone\Snippetz\Frontend\Site_Verification();
		$this->third_party       = new \WebberZone\Snippetz\Frontend\Third_Party();

		if ( \WebberZone\Snippetz\Util\Helpers::is_snippets_enabled() ) {
			$this->snippets = new \WebberZone\Snippetz\Snippets\Snippets();
		}

		$this->hooks();
	}

	/**
	 * Run the hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_action( 'wp_body_open', array( $this, 'wp_body_open' ) );
		add_action( 'wp_footer', array( $this, 'wp_footer' ) );
		add_action( 'the_excerpt_rss', array( $this, 'the_excerpt_rss' ), 99999999 );
		add_action( 'the_content_feed', array( $this, 'the_excerpt_rss' ), 99999999 );

		$priority = ata_get_option( 'content_filter_priority', 10 );
		add_filter( 'the_content', array( $this, 'the_content' ), $priority );

		$footer_process_shortcode  = ata_get_option( 'footer_process_shortcode' );
		$feed_process_shortcode    = ata_get_option( 'feed_process_shortcode' );
		$content_process_shortcode = ata_get_option( 'content_process_shortcode' );

		$filters = array(
			'ata_footer_other_html'          => $footer_process_shortcode,
			'ata_feed_html_before'           => $feed_process_shortcode,
			'ata_feed_html_after'            => $feed_process_shortcode,
			'ata_content_html_before'        => $content_process_shortcode,
			'ata_content_html_after'         => $content_process_shortcode,
			'ata_content_html_before_single' => $content_process_shortcode,
			'ata_content_html_after_single'  => $content_process_shortcode,
			'ata_content_html_before_post'   => $content_process_shortcode,
			'ata_content_html_after_post'    => $content_process_shortcode,
			'ata_content_html_before_page'   => $content_process_shortcode,
			'ata_content_html_after_page'    => $content_process_shortcode,
			'ata_feed_copyrightnotice'       => 1,
		);

		foreach ( $filters as $filter => $process_shortcode ) {
			if ( $process_shortcode ) {
				add_filter( $filter, 'shortcode_unautop' );
				add_filter( $filter, 'do_shortcode' );
				add_filter( $filter, array( $this, 'process_placeholders' ), 99 );
			}
		}
	}

	/**
	 * Load the plugin translations.
	 *
	 * @since 2.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'add-to-all', false, dirname( plugin_basename( WZ_SNIPPETZ_FILE ) ) . '/languages/' );
	}

	/**
	 * Get an option and apply an `ata_{$option}` filter.
	 *
	 * @param string $option Option name.
	 * @return string Option value after filtering.
	 */
	public function get_option_and_filter( $option ) {

		$output = ata_get_option( $option, '' );

		/**
		 * Get the HTML to be added to the footer.
		 *
		 * @since 1.3.0
		 * @param $output HTML added to the footer
		 */
		return apply_filters( "ata_$option", $output );
	}

	/**
	 * Function to add custom code to the header. Filters `wp_head`.
	 *
	 * @since 2.0.0
	 */
	public function wp_head() {

		$head_other_html = $this->get_option_and_filter( 'head_other_html' );
		$head_css        = $this->get_option_and_filter( 'head_css' );
		$tynt_id         = ata_get_option( 'tynt_id', '' );

		// Add CSS to header.
		if ( '' !== $head_css ) {
			echo '<style type="text/css">' . $head_css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		// Add other header.
		if ( '' !== $head_other_html ) {
			echo $head_other_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Function to add custom code to `wp_body_open()`.
	 *
	 * @since 2.0.0
	 */
	public function wp_body_open() {

		$html = $this->get_option_and_filter( 'wp_body_open' );

		// Add other header.
		if ( '' !== $html ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Function to add the necessary code to `wp_footer`.
	 *
	 * @since 2.0.0
	 */
	public function wp_footer() {

		$footer_other_html = $this->get_option_and_filter( 'footer_other_html' );
		if ( '' !== $footer_other_html ) {
			echo $footer_other_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Function to add custom HTML before and after the post content. Filters `the_content`.
	 *
	 * @since 1.0
	 *
	 * @param string $content Post content.
	 * @return string Filtered post content
	 */
	public function the_content( $content ) {
		global $post;

		$exclude_on_post_ids = array_map( 'absint', explode( ',', ata_get_option( 'exclude_on_post_ids' ) ) );

		if ( isset( $post ) ) {
			if ( in_array( (int) $post->ID, $exclude_on_post_ids, true ) ) {
				return $content; // Exit without adding content.
			}
		}

		if ( ! is_singular() && ! is_home() && ! is_archive() ) {
			return $content;
		}

		$str_before = '';
		$str_after  = '';

		if ( is_singular() ) {
			if ( ata_get_option( 'content_add_html_before' ) ) {
				$str_before .= $this->get_option_and_filter( 'content_html_before' );
			}

			if ( ata_get_option( 'content_add_html_after' ) ) {
				$str_after .= $this->get_option_and_filter( 'content_html_after' );
			}

			if ( ata_get_option( 'content_add_html_before_single' ) ) {
				$str_before .= $this->get_option_and_filter( 'content_html_before_single' );
			}

			if ( ata_get_option( 'content_add_html_after_single' ) ) {
				$str_after .= $this->get_option_and_filter( 'content_html_after_single' );
			}
		}
		if ( is_single() ) {
			if ( ata_get_option( 'content_add_html_before_post' ) ) {
				$str_before .= $this->get_option_and_filter( 'content_html_before_post' );
			}

			if ( ata_get_option( 'content_add_html_after_post' ) ) {
				$str_after .= $this->get_option_and_filter( 'content_html_after_post' );
			}
		}
		if ( is_page() ) {
			if ( ata_get_option( 'content_add_html_before_page' ) ) {
				$str_before .= $this->get_option_and_filter( 'content_html_before_page' );
			}

			if ( ata_get_option( 'content_add_html_after_page' ) ) {
				$str_after .= $this->get_option_and_filter( 'content_html_after_page' );
			}
		}
		if ( is_home() || is_archive() ) {
			if ( ata_get_option( 'content_add_html_before' ) ) {
				$str_before .= $this->get_option_and_filter( 'content_html_before' );
			}

			if ( ata_get_option( 'content_add_html_after' ) ) {
				$str_after .= $this->get_option_and_filter( 'content_html_after' );
			}
		}

		return $str_before . $content . $str_after;
	}

	/**
	 * Function to add content to RSS feeds. Filters `the_excerpt_rss` and `the_content_feed`.
	 *
	 * @since 2.0.0
	 *
	 * @param string $content Post content.
	 * @return string Filtered post content.
	 */
	public function the_excerpt_rss( $content ) {
		$str_before = '';
		$str_after  = '';

		if ( ! empty( ata_get_option( 'feed_add_html_before' ) ) ) {
			$str_before .= $this->get_option_and_filter( 'feed_html_before' );
			$str_before .= '<br />';
		}

		if ( ! empty( ata_get_option( 'feed_add_html_after' ) ) ) {
			$str_after .= $this->get_option_and_filter( 'feed_html_after' );
			$str_after .= '<br />';
		}

		if ( ! empty( ata_get_option( 'feed_add_title' ) ) ) {
			$str_after .= $this->feed_title_text();
			$str_after .= '<br />';
		}

		if ( ! empty( ata_get_option( 'feed_add_copyright' ) ) ) {
			$str_after .= $this->get_option_and_filter( 'feed_copyrightnotice' );
			$str_after .= '<br />';
		}

		if ( ! empty( ata_get_option( 'add_credit' ) ) ) {
			$str_after .= $this->creditline();
			$str_after .= '<br />';
		}

		if ( empty( $str_before ) && empty( $str_after ) ) {
			return $content;
		}
		if ( ! empty( $str_after ) ) {
			$str_after = '<hr style="border-top: black solid 1px" />' . $str_after;
		}

		return $str_before . $content . $str_after;
	}

	/**
	 * Get title text to be added after the content in the feed.
	 *
	 * @since 2.0.0
	 */
	public function feed_title_text() {

		$title         = '<a href="' . get_permalink() . '">' . the_title( '', '', false ) . '</a>';
		$search_array  = array(
			'%title%',
			'%date%',
			'%time%',
			'%updated_time%',
		);
		$replace_array = array(
			$title,
			get_the_time( 'F j, Y' ),
			get_the_time( 'g:i a' ),
			get_the_modified_date(),
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
	 * Get the credit line - link to WebberZone Snippetz plugin page.
	 *
	 * @since 2.0.0
	 */
	public function creditline() {

		$output  = '<br /><span style="font-size: 0.8em">';
		$output .= __( 'Feed enhanced by ', 'add-to-all' );
		$output .= '<a href="https://webberzone.com/plugins/add-to-all/" rel="nofollow">WebberZone Snippetz</a>';
		$output .= '</span>';

		/**
		 * Filters the credit line.
		 *
		 * @since 1.3.0
		 * @param $output HTML added after the feed
		 */
		return apply_filters( 'ata_creditline', $output );
	}

	/**
	 * Process placeholders.
	 *
	 * @since 2.0.0
	 *
	 * @param string $input Input string.
	 * @return string $output Output string.
	 */
	public function process_placeholders( $input ) {
		$output = \WebberZone\Snippetz\Util\Helpers::process_placeholders( $input );
		return $output;
	}
}
