<?php
/**
 * Register ATA Settings.
 *
 * @link  https://webberzone.com
 * @since 1.7.0
 *
 * @package Add_to_All
 * @subpackage Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'ATA_Settings' ) ) :
	/**
	 * ATA Settings class to register the settings.
	 *
	 * @version 1.0
	 * @since   1.7.0
	 */
	class ATA_Settings {

		/**
		 * Class instance.
		 *
		 * @var class Class instance.
		 */
		public static $instance;

		/**
		 * Settings API.
		 *
		 * @since 1.7.0
		 *
		 * @var object Settings API.
		 */
		public $settings_api;

		/**
		 * Settings Page in Admin area.
		 *
		 * @since 1.7.0
		 *
		 * @var string Settings Page.
		 */
		public $settings_page;

		/**
		 * Main constructor class.
		 *
		 * @since 1.7.0
		 */
		protected function __construct() {
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_footer_text', array( $this, 'admin_footer_text' ) );
			add_action( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
			add_filter( 'plugin_action_links_' . plugin_basename( ATA_PLUGIN_FILE ), array( $this, 'plugin_actions_links' ) );
		}

		/**
		 * Singleton instance
		 *
		 * @since 1.7.0
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Init function.
		 *
		 * @since 1.7.0
		 */
		public function admin_init() {

			$args = array(
				'reset_message'   => __( 'Settings have been reset to their default values. Reload this page to view the updated settings.', 'add-to-all' ),
				'success_message' => __( 'Settings updated.', 'add-to-all' ),
				'default_tab'     => 'third_party',
				'settings_page'   => $this->settings_page,
			);

			$this->settings_api = new ATA_Admin\Settings_API( 'ata_settings', 'ata', $args );
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_registered_settings( $this->get_registered_settings() );
			$this->settings_api->set_upgraded_settings( $this->get_upgrade_settings() );

			$this->settings_api->admin_init();
		}

		/**
		 * Add admin menu.
		 *
		 * @since 1.7.0
		 */
		public function admin_menu() {
			$this->settings_page = add_options_page( __( 'Add to All', 'add-to-all' ), __( 'Add to All', 'add-to-all' ), 'manage_options', 'ata_options_page', array( $this, 'plugin_settings' ) );

			// Load the settings contextual help.
			add_action( 'load-' . $this->settings_page, array( $this, 'settings_help' ) );
		}

		/**
		 * Array containing the settings' sections.
		 *
		 * @since 1.7.0
		 *
		 * @return array Settings array
		 */
		public function get_settings_sections() {
			$ata_settings_sections = array(
				'third_party' => esc_html__( 'Third Party', 'add-to-all' ),
				'head'        => esc_html__( 'Header', 'add-to-all' ),
				'content'     => esc_html__( 'Content', 'add-to-all' ),
				'footer'      => esc_html__( 'Footer', 'add-to-all' ),
				'feed'        => esc_html__( 'Feed', 'add-to-all' ),
			);

			/**
			 * Filter the array containing the settings' sections.
			 *
			 * @since 1.2.0
			 *
			 * @param array $ata_settings_sections Settings array
			 */
			return apply_filters( 'ata_settings_sections', $ata_settings_sections );

		}


		/**
		 * Retrieve the array of plugin settings
		 *
		 * @since 1.7.0
		 *
		 * @return array Settings array
		 */
		public function get_registered_settings() {

			$ata_settings = array(
				'third_party' => $this->settings_third_party(),
				'head'        => $this->settings_head(),
				'content'     => $this->settings_content(),
				'footer'      => $this->settings_footer(),
				'feed'        => $this->settings_feed(),
			);

			/**
			 * Filters the settings array
			 *
			 * @since 1.2.0
			 *
			 * @param array $ata_setings Settings array
			 */
			return apply_filters( 'ata_registered_settings', $ata_settings );

		}

		/**
		 * Returns the Third party settings.
		 *
		 * @since 1.5.0
		 *
		 * @return array Third party settings.
		 */
		public function settings_third_party() {

			$settings = array(
				'statcounter_header'      => array(
					'id'   => 'statcounter_header',
					'name' => '<h3>' . esc_html__( 'StatCounter', 'add-to-all' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'sc_project'              => array(
					'id'      => 'sc_project',
					'name'    => esc_html__( 'Project ID', 'add-to-all' ),
					'desc'    => esc_html__( 'This is the value of sc_project in your StatCounter code.', 'add-to-all' ),
					'type'    => 'text',
					'options' => '',
				),
				'sc_security'             => array(
					'id'      => 'sc_security',
					'name'    => esc_html__( 'Security ID', 'add-to-all' ),
					'desc'    => esc_html__( 'This is the value of sc_security in your StatCounter code.', 'add-to-all' ),
					'type'    => 'text',
					'options' => '',
				),
				'google_analytics_header' => array(
					'id'   => 'google_analytics_header',
					'name' => '<h3>' . esc_html__( 'Google Analytics', 'add-to-all' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'ga_uacct'                => array(
					'id'      => 'ga_uacct',
					'name'    => esc_html__( 'Tracking ID', 'add-to-all' ),
					'desc'    => esc_html__( 'You can find this under Admin &raquo; Tracking Info &raquo; Tracking Code when viewing your project settings and is of the form UA-XXXX-Y.', 'add-to-all' ),
					'type'    => 'text',
					'options' => '',
				),
				'ga_anonymize_ip'         => array(
					'id'      => 'ga_anonymize_ip',
					'name'    => esc_html__( 'Anonymize IP', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this box to anonymize IPs before they are sent to Google Analytics.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'ga_linker'               => array(
					'id'      => 'ga_linker',
					'name'    => esc_html__( 'Linker domains', 'add-to-all' ),
					'desc'    => esc_html__( "If you'd like to implement cross-domain tracking, enter a comma-separated list of domains, e.g. ajaydsouza.com,webberzone.com", 'add-to-all' ),
					'type'    => 'csv',
					'options' => '',
				),
				'tynt_header'             => array(
					'id'   => 'tynt_header',
					'name' => '<h3>' . esc_html__( '33 Across (Tynt)', 'add-to-all' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'tynt_id'                 => array(
					'id'      => 'tynt_id',
					'name'    => esc_html__( 'Tynt ID', 'add-to-all' ),
					/* translators: 1: Code. */
					'desc'    => sprintf( esc_html__( 'This is the text between the brackets in %1$s  in the SiteCTRL client script', 'add-to-all' ), "<code>Tynt.push('ID HERE')</code>" ),
					'type'    => 'text',
					'options' => '',
				),
				'verification_header'     => array(
					'id'   => 'verification_header',
					'name' => '<h3>' . esc_html__( 'Site verification', 'add-to-all' ) . '</h3>',
					'desc' => '',
					'type' => 'header',
				),
				'google_verification'     => array(
					'id'      => 'google_verification',
					'name'    => esc_html__( 'Google', 'add-to-all' ),
					/* translators: 1: Google verification details page. */
					'desc'    => sprintf( esc_html__( 'Value of the content portion of the HTML tag method on the %s', 'add-to-all' ), '<a href="https://www.google.com/webmasters/verification/verification" target="_blank">' . esc_html__( 'verification details page', 'add-to-all' ) . '</a>' ),
					'type'    => 'text',
					'options' => '',
				),
				'bing_verification'       => array(
					'id'      => 'bing_verification',
					'name'    => esc_html__( 'Bing', 'add-to-all' ),
					/* translators: 1: Bing verification details page. */
					'desc'    => sprintf( esc_html__( 'Value of the content portion of the HTML tag method on the %s', 'add-to-all' ), '<a href="https://www.bing.com/webmaster/" target="_blank">' . esc_html__( 'verification details page', 'add-to-all' ) . '</a>' ),
					'type'    => 'text',
					'options' => '',
				),
				'pinterest_verification'  => array(
					'id'      => 'pinterest_verification',
					'name'    => esc_html__( 'Pinterest', 'add-to-all' ),
					/* translators: 1: Pinterest meta tag details page. */
					'desc'    => sprintf( esc_html__( 'Read how to get the Meta Tag from the %s', 'add-to-all' ), '<a href="https://help.pinterest.com/en/articles/confirm-your-website" target="_blank">' . esc_html__( 'Pinterest help page', 'add-to-all' ) . '</a>' ),
					'type'    => 'text',
					'options' => '',
				),
			);

			/**
			 * Filters the Third party settings array
			 *
			 * @since 1.5.0
			 *
			 * @param array $settings Third party Settings array
			 */
			return apply_filters( 'ata_settings_third_party', $settings );
		}

		/**
		 * Returns the Header settings.
		 *
		 * @since 1.5.0
		 *
		 * @return array Header settings.
		 */
		public function settings_head() {

			$settings = array(
				'head_css'        => array(
					'id'          => 'head_css',
					'name'        => esc_html__( 'Custom CSS', 'add-to-all' ),
					'desc'        => esc_html__( 'Add the CSS code without the <style></style> tags.', 'add-to-all' ),
					'type'        => 'css',
					'options'     => '',
					'field_class' => 'codemirror_css',
				),
				'head_other_html' => array(
					'id'          => 'head_other_html',
					'name'        => esc_html__( 'HTML to add to the header', 'add-to-all' ),
					/* translators: 1: Code. */
					'desc'        => sprintf( esc_html__( 'The code entered here is added to %1$s. Please ensure that you enter valid HTML or JavaScript.', 'add-to-all' ), '<code>wp_head()</code>' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
			);

			/**
			 * Filters the Header settings array
			 *
			 * @since 1.5.0
			 *
			 * @param array $settings Header Settings array
			 */
			return apply_filters( 'ata_settings_head', $settings );
		}

		/**
		 * Returns the Content settings.
		 *
		 * @since 1.5.0
		 *
		 * @return array Content settings.
		 */
		public function settings_content() {

			$settings = array(
				'content_filter_priority'        => array(
					'id'      => 'content_filter_priority',
					'name'    => esc_html__( 'Content filter priority', 'add-to-all' ),
					'desc'    => esc_html__( 'A higher number will cause the Add to All output to be processed after other filters. Number below 10 is not recommended.', 'add-to-all' ),
					'type'    => 'text',
					'options' => 999,
				),
				'exclude_on_post_ids'            => array(
					'id'      => 'exclude_on_post_ids',
					'name'    => esc_html__( 'Exclude display on these post IDs', 'add-to-all' ),
					'desc'    => esc_html__( 'Comma-separated list of post or page IDs to exclude displaying the above content. e.g. 188,320,500', 'add-to-all' ),
					'type'    => 'postids',
					'options' => '',
				),
				'content_process_shortcode'      => array(
					'id'      => 'content_process_shortcode',
					'name'    => esc_html__( 'Process shortcodes in content', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this box to execute any shortcodes that you enter in the options below.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_header_all'             => array(
					'id'   => 'content_header_all',
					'name' => '<h3>' . esc_html__( 'Home and other views', 'add-to-all' ) . '</h3>',
					'desc' => esc_html__( 'Displays when viewing single posts, home, category, tag and other archives.', 'add-to-all' ),
					'type' => 'header',
				),
				'content_add_html_before'        => array(
					'id'      => 'content_add_html_before',
					'name'    => esc_html__( 'Add HTML before content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_before'            => array(
					'id'          => 'content_html_before',
					'name'        => esc_html__( 'HTML to add before the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
				'content_add_html_after'         => array(
					'id'      => 'content_add_html_after',
					'name'    => esc_html__( 'Add HTML after content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_after'             => array(
					'id'          => 'content_html_after',
					'name'        => esc_html__( 'HTML to add after the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
				'content_header_single'          => array(
					'id'   => 'content_header_single',
					'name' => '<h3>' . esc_html__( 'Single posts views', 'add-to-all' ) . '</h3>',
					'desc' => esc_html__( 'Displays when viewing single views including posts, pages, custom-post-types.', 'add-to-all' ),
					'type' => 'header',
				),
				'content_add_html_before_single' => array(
					'id'      => 'content_add_html_before_single',
					'name'    => esc_html__( 'Add HTML before content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_before_single'     => array(
					'id'          => 'content_html_before_single',
					'name'        => esc_html__( 'HTML to add before the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
				'content_add_html_after_single'  => array(
					'id'      => 'content_add_html_after_single',
					'name'    => esc_html__( 'Add HTML after content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_after_single'      => array(
					'id'          => 'content_html_after_single',
					'name'        => esc_html__( 'HTML to add after the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
				'content_header_post'            => array(
					'id'   => 'content_header_post',
					'name' => '<h3>' . esc_html__( 'Post only views', 'add-to-all' ) . '</h3>',
					'desc' => esc_html__( 'Displays only on posts', 'add-to-all' ),
					'type' => 'header',
				),
				'content_add_html_before_post'   => array(
					'id'      => 'content_add_html_before_post',
					'name'    => esc_html__( 'Add HTML before content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_before_post'       => array(
					'id'          => 'content_html_before_post',
					'name'        => esc_html__( 'HTML to add before the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
				'content_add_html_after_post'    => array(
					'id'      => 'content_add_html_after_post',
					'name'    => esc_html__( 'Add HTML after content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_after_post'        => array(
					'id'          => 'content_html_after_post',
					'name'        => esc_html__( 'HTML to add after the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
				'content_header_page'            => array(
					'id'   => 'content_header_page',
					'name' => '<h3>' . esc_html__( 'Page only views', 'add-to-all' ) . '</h3>',
					'desc' => esc_html__( 'Displays only on pages', 'add-to-all' ),
					'type' => 'header',
				),
				'content_add_html_before_page'   => array(
					'id'      => 'content_add_html_before_page',
					'name'    => esc_html__( 'Add HTML before content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your page.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_before_page'       => array(
					'id'          => 'content_html_before_page',
					'name'        => esc_html__( 'HTML to add before the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
				'content_add_html_after_page'    => array(
					'id'      => 'content_add_html_after_page',
					'name'    => esc_html__( 'Add HTML after content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your page.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'content_html_after_page'        => array(
					'id'          => 'content_html_after_page',
					'name'        => esc_html__( 'HTML to add after the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
			);

			/**
			 * Filters the Content settings array
			 *
			 * @since 1.5.0
			 *
			 * @param array $settings Content Settings array
			 */
			return apply_filters( 'ata_settings_content', $settings );
		}

		/**
		 * Returns the Footer settings.
		 *
		 * @since 1.5.0
		 *
		 * @return array Footer settings.
		 */
		public function settings_footer() {

			$settings = array(
				'footer_process_shortcode' => array(
					'id'      => 'footer_process_shortcode',
					'name'    => esc_html__( 'Process shortcodes in footer', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this box to execute any shortcodes that you enter in the option below.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'footer_other_html'        => array(
					'id'          => 'footer_other_html',
					'name'        => esc_html__( 'HTML to add to the footer', 'add-to-all' ),
					/* translators: 1: Code. */
					'desc'        => sprintf( esc_html__( 'The code entered here is added to %1$s. Please ensure that you enter valid HTML or JavaScript.', 'add-to-all' ), '<code>wp_footer()</code>' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
			);

			/**
			 * Filters the Footer settings array
			 *
			 * @since 1.5.0
			 *
			 * @param array $settings Footer Settings array
			 */
			return apply_filters( 'ata_settings_footer', $settings );
		}

		/**
		 * Returns the Feed settings.
		 *
		 * @since 1.5.0
		 *
		 * @return array Feed settings.
		 */
		public function settings_feed() {

			$settings = array(
				'feed_add_copyright'     => array(
					'id'      => 'feed_add_copyright',
					'name'    => esc_html__( 'Add copyright notice?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the below copyright notice to your feed.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => true,
				),
				'feed_copyrightnotice'   => array(
					'id'          => 'feed_copyrightnotice',
					'name'        => esc_html__( 'Coyright text', 'add-to-all' ),
					/* translators: No strings here. */
					'desc'        => esc_html__( 'Enter valid HTML only. This copyright notice is added as the last item of your feed. You can also use %year% for the year or %first_year% for the year of the first post,', 'add-to-all' ),
					'type'        => 'html',
					'options'     => $this->get_copyright_text(),
					'field_class' => 'codemirror_html',
				),
				'feed_add_title'         => array(
					'id'      => 'feed_add_title',
					'name'    => esc_html__( 'Add post title?', 'add-to-all' ),
					'desc'    => esc_html__( 'Add a link to the title of the post in the feed.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => true,
				),
				'feed_title_text'        => array(
					'id'      => 'feed_title_text',
					'name'    => esc_html__( 'Title text', 'add-to-all' ),
					/* translators: No strings here. */
					'desc'    => esc_html__( 'The above text will be added to the feed. You can use %title% to add a link to the post, %date% and %time% to display the date and time of the post respectively.', 'add-to-all' ),
					'type'    => 'textarea',
					/* translators: No strings here. */
					'options' => esc_html__( '%title% was first posted on %date% at %time%.', 'add-to-all' ),
				),
				'feed_process_shortcode' => array(
					'id'      => 'feed_process_shortcode',
					'name'    => esc_html__( 'Process shortcodes in feed', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this box to execute any shortcodes that you enter in the options below.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'feed_add_html_before'   => array(
					'id'      => 'feed_add_html_before',
					'name'    => esc_html__( 'Add HTML before content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'feed_html_before'       => array(
					'id'          => 'feed_html_before',
					'name'        => esc_html__( 'HTML to add before the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
				'feed_add_html_after'    => array(
					'id'      => 'feed_add_html_after',
					'name'    => esc_html__( 'Add HTML after content?', 'add-to-all' ),
					'desc'    => esc_html__( 'Check this to add the HTML below before the content of your post.', 'add-to-all' ),
					'type'    => 'checkbox',
					'options' => false,
				),
				'feed_html_after'        => array(
					'id'          => 'feed_html_after',
					'name'        => esc_html__( 'HTML to add after the content', 'add-to-all' ),
					'desc'        => esc_html__( 'Enter valid HTML or JavaScript (wrapped in script tags). No PHP allowed.', 'add-to-all' ),
					'type'        => 'html',
					'options'     => '',
					'field_class' => 'codemirror_html',
				),
				'add_credit'             => array(
					'id'      => 'add_credit',
					'name'    => esc_html__( 'Add a link to "Add to All" plugin page', 'add-to-all' ),
					'desc'    => '',
					'type'    => 'checkbox',
					'options' => false,
				),
			);

			/**
			 * Filters the Feed settings array
			 *
			 * @since 1.5.0
			 *
			 * @param array $settings Feed Settings array
			 */
			return apply_filters( 'ata_settings_feed', $settings );
		}

		/**
		 * Copyright notice text.
		 *
		 * @since 1.7.0
		 * @return string Copyright notice
		 */
		public function get_copyright_text() {

			$copyrightnotice  = '&copy;' . gmdate( 'Y' ) . ' &quot;<a href="' . get_option( 'home' ) . '">' . get_option( 'blogname' ) . '</a>&quot;. ';
			$copyrightnotice .= esc_html__( 'Use of this feed is for personal non-commercial use only. If you are not reading this article in your feed reader, then the site is guilty of copyright infringement. Please contact me at ', 'ald_ata_plugin' );
			$copyrightnotice .= '<!--email_off-->' . get_option( 'admin_email' ) . '<!--/email_off-->';

			/**
			 * Copyright notice text.
			 *
			 * @since 1.2.0
			 * @param string $copyrightnotice Copyright notice
			 */
			return apply_filters( 'ata_copyright_text', $copyrightnotice );
		}


		/**
		 * Upgrade v1.1.0 settings to v1.2.0.
		 *
		 * @since 1.7.0
		 * @return array Settings array
		 */
		public function get_upgrade_settings() {
			$old_settings = get_option( 'ald_ata_settings' );

			if ( empty( $old_settings ) ) {
				return false;
			} else {
				$map = array(
					'add_credit'                     => 'addcredit',

					// Content options.
					'content_html_before'            => 'content_htmlbefore',
					'content_html_after'             => 'content_htmlafter',
					'content_add_html_before'        => 'content_addhtmlbefore',
					'content_add_html_after'         => 'content_addhtmlafter',
					'content_html_before_single'     => 'content_htmlbeforeS',
					'content_html_after_single'      => 'content_htmlafterS',
					'content_add_html_before_single' => 'content_addhtmlbeforeS',
					'content_add_html_after_single'  => 'content_addhtmlafterS',
					'content_filter_priority'        => 'content_filter_priority',

					// Feed options.
					'feed_html_before'               => 'feed_htmlbefore',
					'feed_html_after'                => 'feed_htmlafter',
					'feed_add_html_before'           => 'feed_addhtmlbefore',
					'feed_add_html_after'            => 'feed_addhtmlafter',
					'feed_copyrightnotice'           => 'feed_copyrightnotice',
					'feed_add_title'                 => 'feed_addtitle',
					'feed_title_text'                => 'feed_titletext',
					'feed_add_copyright'             => 'feed_addcopyright',

					// 3rd party options.
					'sc_project'                     => 'tp_sc_project',
					'sc_security'                    => 'tp_sc_security',
					'ga_uacct'                       => 'tp_ga_uacct',
					'tynt_id'                        => 'tp_tynt_id',

					// Footer options.
					'footer_other_html'              => 'ft_other',

					// Header options.
					'head_css'                       => 'head_CSS',
					'head_other_html'                => 'head_other',
				);

				foreach ( $map as $key => $value ) {
					$settings[ $key ] = strval( $old_settings[ $value ] );
				}

				delete_option( 'ald_ata_settings' );

				return $settings;
			}

		}

		/**
		 * Render the settings page.
		 *
		 * @since 1.7.0
		 *
		 * @return void
		 */
		public function plugin_settings() {
			$active_tab = isset( $_GET['tab'] ) && array_key_exists( sanitize_key( wp_unslash( $_GET['tab'] ) ), $this->get_settings_sections() ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'general'; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended

			ob_start();
			?>
			<div class="wrap">
				<h1><?php esc_html_e( 'Add to All Settings', 'add-to-all' ); ?></h1>

				<div id="poststuff">
				<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">

					<?php $this->settings_api->show_navigation(); ?>
					<?php $this->settings_api->show_form(); ?>

				</div><!-- /#post-body-content -->

				<div id="postbox-container-1" class="postbox-container">

					<div id="side-sortables" class="meta-box-sortables ui-sortable">
						<?php include_once 'sidebar.php'; ?>
					</div><!-- /#side-sortables -->

				</div><!-- /#postbox-container-1 -->
				</div><!-- /#post-body -->
				<br class="clear" />
				</div><!-- /#poststuff -->

			</div><!-- /.wrap -->

			<?php
			echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Add rating links to the admin dashboard
		 *
		 * @since 1.7.0
		 *
		 * @param string $footer_text The existing footer text.
		 * @return string Updated Footer text
		 */
		public function admin_footer_text( $footer_text ) {

			if ( get_current_screen()->id === $this->settings_page ) {

				$text = sprintf(
					/* translators: 1: Plugin page link, 2: Review link. */
					__( 'Thank you for using <a href="%1$s" target="_blank">Add to All</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'add-to-all' ),
					'https://webberzone.com/plugins/add-to-all',
					'https://wordpress.org/support/plugin/add-to-all/reviews/#new-post'
				);

				return str_replace( '</span>', '', $footer_text ) . ' | ' . $text . '</span>';

			} else {

				return $footer_text;

			}
		}

		/**
		 * Adding WordPress plugin action links.
		 *
		 * @since 1.7.0
		 *
		 * @param array $links Array of links.
		 * @return array
		 */
		public function plugin_actions_links( $links ) {

			return array_merge(
				array(
					'settings' => '<a href="' . admin_url( 'options-general.php?page=ata_options_page' ) . '">' . esc_html__( 'Settings', 'add-to-all' ) . '</a>',
				),
				$links
			);

		}

		/**
		 * Add meta links on Plugins page.
		 *
		 * @since 1.7.0
		 *
		 * @param array  $links Array of Links.
		 * @param string $file Current file.
		 * @return array
		 */
		public function plugin_row_meta( $links, $file ) {

			if ( false !== strpos( $file, 'add-to-all.php' ) ) {
				$links[] = '<a href="http://wordpress.org/support/plugin/add-to-all">' . esc_html__( 'Support', 'add-to-all' ) . '</a>';
				$links[] = '<a href="https://webberzone.com/donate/">' . esc_html__( 'Donate', 'add-to-all' ) . '</a>';
			}
			return $links;
		}



		/**
		 * Function to add the content of the help tab.
		 *
		 * @since 1.7.0
		 */
		public function settings_help() {
			$screen = get_current_screen();

			if ( $screen->id !== $this->settings_page ) {
				return;
			}

			// Set the text in the help sidebar.
			$screen->set_help_sidebar(
				/* translators: 1: Plugin support site link. */
				'<p>' . sprintf( __( 'For more information or how to get support visit the <a href="%s">support site</a>.', 'add-to-all' ), esc_url( 'https://webberzone.com/support/' ) ) . '</p>' .
				/* translators: 1: WordPress.org support forums link. */
					'<p>' . sprintf( __( 'Support queries should be posted in the <a href="%s">WordPress.org support forums</a>.', 'add-to-all' ), esc_url( 'https://wordpress.org/support/plugin/add-to-all' ) ) . '</p>' .
				'<p>' . sprintf(
					/* translators: 1: Github issues link, 2: Github plugin page link. */
					__( '<a href="%1$s">Post an issue</a> on <a href="%2$s">GitHub</a> (bug reports only).', 'add-to-all' ),
					esc_url( 'https://github.com/ajaydsouza/add-to-all/issues' ),
					esc_url( 'https://github.com/ajaydsouza/add-to-all' )
				) . '</p>'
			);

			// Add third party help tab.
			$screen->add_help_tab(
				array(
					'id'      => 'ata-settings-third-party-help',
					'title'   => esc_html__( 'Third Party', 'add-to-all' ),
					'content' =>
						'<p><strong>' . esc_html__( 'This screen provides the settings for configuring the integration with third party scripts.', 'add-to-all' ) . '</strong></p>' .
						'<p>' . sprintf(
							/* translators: 1: Google Analystics help article. */
							esc_html__( 'Google Analytics tracking can be found by visiting this %s', 'add-to-all' ),
							'<a href="https://support.google.com/analytics/answer/1008080?hl=en" target="_blank">' . esc_html__( 'article', 'add-to-all' ) . '</a>.'
						) .
						'</p>',
					'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
				)
			);

			// Add Header help tab.
			$screen->add_help_tab(
				array(
					'id'      => 'ata-settings-header-help',
					'title'   => esc_html__( 'Header', 'add-to-all' ),
					'content' =>
						'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the header of your site.', 'add-to-all' ) . '</strong></p>' .
						'<p>' . esc_html__( 'You can add custom CSS or HTML code. Useful for adding meta tags for site verification, etc.', 'add-to-all' ) . '</p>' .
						'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
				)
			);

			// Add Content help tab.
			$screen->add_help_tab(
				array(
					'id'      => 'ata-settings-content-help',
					'title'   => esc_html__( 'Content', 'add-to-all' ),
					'content' =>
						'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the content of posts, pages and custom post types.', 'add-to-all' ) . '</strong></p>' .
						'<p>' . esc_html__( 'You can set the priority of the filter and choose if you want this to be displayed on either all content (including archives) or just single posts/pages.', 'add-to-all' ) . '</p>' .
						'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
				)
			);

			// Add Footer help tab.
			$screen->add_help_tab(
				array(
					'id'      => 'ata-settings-footer-help',
					'title'   => esc_html__( 'Footer', 'add-to-all' ),
					'content' =>
						'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the footer of your site.', 'add-to-all' ) . '</strong></p>' .
						'<p>' . esc_html__( 'You can add custom HTML code. Useful for adding tracking code for analytics, etc.', 'add-to-all' ) . '</p>' .
						'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
				)
			);

			// Add Feed help tab.
			$screen->add_help_tab(
				array(
					'id'      => 'ata-settings-feed-help',
					'title'   => esc_html__( 'Feed', 'add-to-all' ),
					'content' =>
						'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the feed of your site.', 'add-to-all' ) . '</strong></p>' .
						'<p>' . esc_html__( 'You can add copyright text, a link to the title and date of the post, and HTML before and after the content', 'add-to-all' ) . '</p>' .
						'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
				)
			);

			/**
			 * Action to add more help settings.
			 *
			 * @since 1.2.0
			 */
			do_action( 'ata_settings_help', $screen );
		}
	}

	ATA_Settings::get_instance();

endif;
