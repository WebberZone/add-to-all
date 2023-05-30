<?php
/**
 * Register Settings.
 *
 * @link  https://webberzone.com
 * @since 1.7.0
 *
 * @package WebberZone\Snippetz\Admin
 */

namespace WebberZone\Snippetz\Admin\Settings;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

	/**
	 * ATA Settings class to register the settings.
	 *
	 * @version 1.0
	 * @since   1.7.0
	 */
class Settings {

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
	 * Prefix which is used for creating the unique filters and actions.
	 *
	 * @since 1.7.0
	 *
	 * @var string Prefix.
	 */
	public static $prefix;

	/**
	 * Settings Key.
	 *
	 * @since 1.7.0
	 *
	 * @var string Settings Key.
	 */
	public $settings_key;

	/**
	 * The slug name to refer to this menu by (should be unique for this menu).
	 *
	 * @since 1.7.0
	 *
	 * @var string Menu slug.
	 */
	public $menu_slug;

	/**
	 * Main constructor class.
	 *
	 * @since 1.7.0
	 */
	public function __construct() {
		$this->settings_key = 'ata_settings';
		self::$prefix       = 'ata';
		$this->menu_slug    = 'ata_options_page';

		$props = array(
			'default_tab'       => 'general',
			'help_sidebar'      => $this->get_help_sidebar(),
			'help_tabs'         => $this->get_help_tabs(),
			'admin_footer_text' => $this->get_admin_footer_text(),
			'menus'             => $this->get_menus(),
		);

		$args = array(
			'translation_strings' => $this->get_translation_strings(),
			'props'               => $props,
			'settings_sections'   => $this->get_settings_sections(),
			'registered_settings' => $this->get_registered_settings(),
			'upgraded_settings'   => array(),
		);

		$this->settings_api = new Settings_API( $this->settings_key, self::$prefix, $args );

		add_action( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 11, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( WZ_SNIPPETZ_FILE ), array( $this, 'plugin_actions_links' ) );
		add_action( 'ata_settings_sanitize', array( $this, 'change_settings_on_save' ), 99 );
		add_action( 'admin_menu', array( $this, 'redirect_on_save' ) );
	}

	/**
	 * Array containing the settings' sections.
	 *
	 * @since 1.8.0
	 *
	 * @return array Settings array
	 */
	public function get_translation_strings() {
		$strings = array(
			'page_header'          => esc_html__( 'WebberZone Snippetz Settings', 'add-to-all' ),
			'reset_message'        => esc_html__( 'Settings have been reset to their default values. Reload this page to view the updated settings.', 'add-to-all' ),
			'success_message'      => esc_html__( 'Settings updated.', 'add-to-all' ),
			'save_changes'         => esc_html__( 'Save Changes', 'add-to-all' ),
			'reset_settings'       => esc_html__( 'Reset all settings', 'add-to-all' ),
			'reset_button_confirm' => esc_html__( 'Do you really want to reset all these settings to their default values?', 'add-to-all' ),
			'checkbox_modified'    => esc_html__( 'Modified from default setting', 'add-to-all' ),
		);

		/**
		 * Filter the array containing the settings' sections.
		 *
		 * @since 1.8.0
		 *
		 * @param array $strings Translation strings.
		 */
		return apply_filters( self::$prefix . '_translation_strings', $strings );
	}

	/**
	 * Get the admin menus.
	 *
	 * @return array Admin menus.
	 */
	public function get_menus() {
		$menus = array();
		if ( \WebberZone\Snippetz\Util\Helpers::is_snippets_enabled() ) {
			$menus[] = array(
				'settings_page' => true,
				'type'          => 'submenu',
				'parent_slug'   => 'edit.php?post_type=ata_snippets',
				'page_title'    => esc_html__( 'WebberZone Snippetz Settings', 'add-to-all' ),
				'menu_title'    => esc_html__( 'Settings', 'add-to-all' ),
				'menu_slug'     => $this->menu_slug,
			);
		} else {
			$menus[] = array(
				'settings_page' => true,
				'type'          => 'submenu',
				'parent_slug'   => 'options-general.php',
				'page_title'    => esc_html__( 'WebberZone Snippetz Settings', 'add-to-all' ),
				'menu_title'    => esc_html__( 'WebberZone Snippetz', 'add-to-all' ),
				'menu_slug'     => $this->menu_slug,
			);
		}
		return $menus;
	}

	/**
	 * Array containing the settings' sections.
	 *
	 * @since 1.7.0
	 *
	 * @return array Settings array
	 */
	public static function get_settings_sections() {
		$settings_sections = array(
			'general'     => esc_html__( 'General', 'add-to-all' ),
			'third_party' => esc_html__( 'Third Party', 'add-to-all' ),
			'head'        => esc_html__( 'Header', 'add-to-all' ),
			'body'        => esc_html__( 'Body', 'add-to-all' ),
			'footer'      => esc_html__( 'Footer', 'add-to-all' ),
			'feed'        => esc_html__( 'Feed', 'add-to-all' ),
		);

		/**
		 * Filter the array containing the settings' sections.
		 *
		 * @since 1.2.0
		 *
		 * @param array $settings_sections Settings array
		 */
		return apply_filters( self::$prefix . '_settings_sections', $settings_sections );
	}


	/**
	 * Retrieve the array of plugin settings
	 *
	 * @since 1.7.0
	 *
	 * @return array Settings array
	 */
	public static function get_registered_settings() {

		$sections = self::get_settings_sections();

		foreach ( $sections as $section => $value ) {
			$method_name = 'settings_' . $section;
			if ( method_exists( __CLASS__, $method_name ) ) {
				$settings[ $section ] = self::$method_name();
			}
		}

		/**
		 * Filters the settings array
		 *
		 * @since 1.2.0
		 *
		 * @param array $ata_setings Settings array
		 */
		return apply_filters( self::$prefix . '_registered_settings', $settings );
	}

	/**
	 * Returns the Header settings.
	 *
	 * @since 1.7.0
	 *
	 * @return array Header settings.
	 */
	public static function settings_general() {

		$settings = array(
			'enable_snippets'  => array(
				'id'      => 'enable_snippets',
				'name'    => esc_html__( 'Enable Snippets Manager', 'add-to-all' ),
				'desc'    => esc_html__( 'Disabling this will turn off the Snippets manager and any of the associated functionality. This will not delete any snippets data that was created before this was turned off.', 'add-to-all' ),
				'type'    => 'checkbox',
				'options' => true,
			),
			'snippet_priority' => array(
				'id'      => 'snippet_priority',
				'name'    => esc_html__( 'Snippet content priority', 'add-to-all' ),
				'desc'    => esc_html__( 'Priority of the snippet content. Lower number means all snippets are added earlier relative to other content. Number below 10 is not recommended. At the next level, priority of each snippet is independently set from the Edit Snippets screen.', 'add-to-all' ),
				'type'    => 'text',
				'options' => 999,
			),
		);

		/**
		 * Filters the Header settings array
		 *
		 * @since 1.7.0
		 *
		 * @param array $settings Header Settings array
		 */
		return apply_filters( self::$prefix . '_settings_general', $settings );
	}

	/**
	 * Returns the Third party settings.
	 *
	 * @since 1.5.0
	 *
	 * @return array Third party settings.
	 */
	public static function settings_third_party() {

		$settings = array(
			'statcounter_header'           => array(
				'id'   => 'statcounter_header',
				'name' => '<h3>' . esc_html__( 'StatCounter', 'add-to-all' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'sc_project'                   => array(
				'id'      => 'sc_project',
				'name'    => esc_html__( 'Project ID', 'add-to-all' ),
				'desc'    => esc_html__( 'This is the value of sc_project in your StatCounter code.', 'add-to-all' ),
				'type'    => 'text',
				'options' => '',
			),
			'sc_security'                  => array(
				'id'      => 'sc_security',
				'name'    => esc_html__( 'Security ID', 'add-to-all' ),
				'desc'    => esc_html__( 'This is the value of sc_security in your StatCounter code.', 'add-to-all' ),
				'type'    => 'text',
				'options' => '',
			),
			'google_analytics_header'      => array(
				'id'   => 'google_analytics_header',
				'name' => '<h3>' . esc_html__( 'Google Analytics', 'add-to-all' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'ga_uacct'                     => array(
				'id'      => 'ga_uacct',
				'name'    => esc_html__( 'Tracking ID', 'add-to-all' ),
				/* translators: 1: Google Tag ID link. */
				'desc'    => sprintf( esc_html__( 'Find your %s', 'add-to-all' ), '<a href="https://www.google.com/webmasters/verification/verification" target="_blank">' . esc_html__( 'Google Tag ID', 'add-to-all' ) . '</a>' ),
				'type'    => 'text',
				'options' => '',
			),
			'verification_header'          => array(
				'id'   => 'verification_header',
				'name' => '<h3>' . esc_html__( 'Site verification', 'add-to-all' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'google_verification'          => array(
				'id'      => 'google_verification',
				'name'    => esc_html__( 'Google', 'add-to-all' ),
				/* translators: 1: Google verification details page. */
				'desc'    => sprintf( esc_html__( 'Value of the content portion of the HTML tag method on the %s', 'add-to-all' ), '<a href="https://www.google.com/webmasters/verification/verification" target="_blank">' . esc_html__( 'verification details page', 'add-to-all' ) . '</a>' ),
				'type'    => 'text',
				'options' => '',
			),
			'bing_verification'            => array(
				'id'      => 'bing_verification',
				'name'    => esc_html__( 'Bing', 'add-to-all' ),
				/* translators: 1: Bing verification details page. */
				'desc'    => sprintf( esc_html__( 'Value of the content portion of the HTML tag method on the %s', 'add-to-all' ), '<a href="https://www.bing.com/webmaster/" target="_blank">' . esc_html__( 'verification details page', 'add-to-all' ) . '</a>' ),
				'type'    => 'text',
				'options' => '',
			),
			'facebook_domain_verification' => array(
				'id'      => 'facebook_domain_verification',
				'name'    => esc_html__( 'Meta', 'add-to-all' ),
				/* translators: 1: Meta tag details page. */
				'desc'    => sprintf( esc_html__( 'Value of the content portion of the Meta tag method. Read how to verify your domain in the %s', 'add-to-all' ), '<a href="https://www.facebook.com/business/help/321167023127050" target="_blank">' . esc_html__( 'Meta Business Help Centre', 'add-to-all' ) . '</a>' ),
				'type'    => 'text',
				'options' => '',
			),
			'pinterest_verification'       => array(
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
		return apply_filters( self::$prefix . '_settings_third_party', $settings );
	}

	/**
	 * Returns the Header settings.
	 *
	 * @since 1.5.0
	 *
	 * @return array Header settings.
	 */
	public static function settings_head() {

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
		return apply_filters( self::$prefix . '_settings_head', $settings );
	}

	/**
	 * Returns the Content settings.
	 *
	 * @since 1.5.0
	 *
	 * @return array Content settings.
	 */
	public static function settings_body() {

		$settings = array(
			'wp_body_open_header'            => array(
				'id'   => 'wp_body_open_header',
				'name' => '<h3>' . esc_html__( 'Opening Body Tag', 'add-to-all' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'wp_body_open'                   => array(
				'id'          => 'wp_body_open',
				'name'        => esc_html__( 'HTML to add to wp_body_open()', 'add-to-all' ),
				'desc'        => esc_html__( 'wp_body_open() is called after the opening body tag. Please ensure that you enter valid HTML or JavaScript. This might not work if your theme does not include the tag.', 'add-to-all' ),
				'type'        => 'html',
				'options'     => '',
				'field_class' => 'codemirror_html',
			),
			'content_header'                 => array(
				'id'   => 'content_header',
				'name' => '<h3>' . esc_html__( 'Content settings', 'add-to-all' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'content_filter_priority'        => array(
				'id'      => 'content_filter_priority',
				'name'    => esc_html__( 'Content filter priority', 'add-to-all' ),
				'desc'    => esc_html__( 'A higher number will cause the WebberZone Snippetz output to be processed after other filters. Number below 10 is not recommended.', 'add-to-all' ),
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
		return apply_filters( self::$prefix . '_settings_body', $settings );
	}

	/**
	 * Returns the Footer settings.
	 *
	 * @since 1.5.0
	 *
	 * @return array Footer settings.
	 */
	public static function settings_footer() {

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
		return apply_filters( self::$prefix . '_settings_footer', $settings );
	}

	/**
	 * Returns the Feed settings.
	 *
	 * @since 1.5.0
	 *
	 * @return array Feed settings.
	 */
	public static function settings_feed() {

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
				'options'     => self::get_copyright_text(),
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
				'name'    => esc_html__( 'Add a link to "WebberZone Snippetz" plugin page', 'add-to-all' ),
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
		return apply_filters( self::$prefix . '_settings_feed', $settings );
	}

	/**
	 * Copyright notice text.
	 *
	 * @since 1.7.0
	 * @return string Copyright notice
	 */
	public static function get_copyright_text() {

		$copyrightnotice  = '&copy;' . gmdate( 'Y' ) . ' &quot;<a href="' . get_option( 'home' ) . '">' . get_option( 'blogname' ) . '</a>&quot;. ';
		$copyrightnotice .= esc_html__( 'Use of this feed is for personal non-commercial use only. If you are not reading this article in your feed reader, then the site is guilty of copyright infringement. Please contact me at ', 'ald_ata_plugin' );
		$copyrightnotice .= '<!--email_off-->' . get_option( 'admin_email' ) . '<!--/email_off-->';

		/**
		 * Copyright notice text.
		 *
		 * @since 1.2.0
		 * @param string $copyrightnotice Copyright notice
		 */
		return apply_filters( self::$prefix . '_copyright_text', $copyrightnotice );
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
	 * Adding WordPress plugin action links.
	 *
	 * @since 1.7.0
	 *
	 * @param array $links Array of links.
	 * @return array
	 */
	public function plugin_actions_links( $links ) {

		$location = $this->get_settings_location();
		return array_merge(
			array(
				'settings' => '<a href="' . $location . '">' . esc_html__( 'Settings', 'add-to-all' ) . '</a>',
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
			$new_links = array(
				'support'    => '<a href = "https://wordpress.org/support/plugin/add-to-all">' . esc_html__( 'Support', 'add-to-all' ) . '</a>',
				'donate'     => '<a href = "https://ajaydsouza.com/donate/">' . esc_html__( 'Donate', 'add-to-all' ) . '</a>',
				'contribute' => '<a href = "https://github.com/WebberZone/add-to-all">' . esc_html__( 'Contribute', 'add-to-all' ) . '</a>',
			);

			$links = array_merge( $links, $new_links );
		}
		return $links;
	}

	/**
	 * Get the help sidebar content to display on the plugin settings page.
	 *
	 * @since 1.8.0
	 */
	public function get_help_sidebar() {

		$help_sidebar =
		/* translators: 1: Plugin support site link. */
		'<p>' . sprintf( __( 'For more information or how to get support visit the <a href="%s">support site</a>.', 'add-to-all' ), esc_url( 'https://webberzone.com/support/' ) ) . '</p>' .
		/* translators: 1: WordPress.org support forums link. */
			'<p>' . sprintf( __( 'Support queries should be posted in the <a href="%s">WordPress.org support forums</a>.', 'add-to-all' ), esc_url( 'https://wordpress.org/support/plugin/add-to-all' ) ) . '</p>' .
		'<p>' . sprintf(
			/* translators: 1: Github issues link, 2: Github plugin page link. */
			__( '<a href="%1$s">Post an issue</a> on <a href="%2$s">GitHub</a> (bug reports only).', 'add-to-all' ),
			esc_url( 'https://github.com/ajaydsouza/add-to-all/issues' ),
			esc_url( 'https://github.com/ajaydsouza/add-to-all' )
		) . '</p>';

		/**
		 * Filter to modify the help sidebar content.
		 *
		 * @since 1.8.0
		 *
		 * @param array $help_sidebar Help sidebar content.
		 */
		return apply_filters( self::$prefix . '_settings_help', $help_sidebar );
	}

	/**
	 * Get the help tabs to display on the plugin settings page.
	 *
	 * @since 1.8.0
	 */
	public function get_help_tabs() {

		$help_tabs = array(
			array(
				'id'      => 'ata-settings-general-help',
				'title'   => esc_html__( 'General', 'add-to-all' ),
				'content' =>
					'<p><strong>' . esc_html__( 'This screen provides general settings. Enable/disable the Snippets Manager and set the global priority of snippets.', 'add-to-all' ) . '</strong></p>' .
					'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
			),
			array(
				'id'      => 'ata-settings-third-party-help',
				'title'   => esc_html__( 'Third Party', 'add-to-all' ),
				'content' =>
					'<p><strong>' . esc_html__( 'This screen provides the settings for configuring the integration with third party scripts.', 'add-to-all' ) . '</strong></p>' .
					'<p>' . sprintf(
						/* translators: 1: Google Analystics help article. */
						esc_html__( 'Google Analytics tracking can be found by visiting this %s', 'add-to-all' ),
						'<a href="https://support.google.com/analytics/topic/9303319" target="_blank">' . esc_html__( 'article', 'add-to-all' ) . '</a>.'
					) .
					'</p>' .
					'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
			),
			array(
				'id'      => 'ata-settings-header-help',
				'title'   => esc_html__( 'Header', 'add-to-all' ),
				'content' =>
					'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the header of your site.', 'add-to-all' ) . '</strong></p>' .
					'<p>' . esc_html__( 'You can add custom CSS or HTML code. Useful for adding meta tags for site verification, etc.', 'add-to-all' ) . '</p>' .
					'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
			),
			array(
				'id'      => 'ata-settings-body-help',
				'title'   => esc_html__( 'Body', 'add-to-all' ),
				'content' =>
					'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the content of posts, pages and custom post types.', 'add-to-all' ) . '</strong></p>' .
					'<p>' . esc_html__( 'You can set the priority of the filter and choose if you want this to be displayed on either all content (including archives) or just single posts/pages.', 'add-to-all' ) . '</p>' .
					'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
			),
			array(
				'id'      => 'ata-settings-footer-help',
				'title'   => esc_html__( 'Footer', 'add-to-all' ),
				'content' =>
					'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the footer of your site.', 'add-to-all' ) . '</strong></p>' .
					'<p>' . esc_html__( 'You can add custom HTML code. Useful for adding tracking code for analytics, etc.', 'add-to-all' ) . '</p>' .
					'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
			),
			array(
				'id'      => 'ata-settings-feed-help',
				'title'   => esc_html__( 'Feed', 'add-to-all' ),
				'content' =>
					'<p><strong>' . esc_html__( 'This screen allows you to control what content is added to the feed of your site.', 'add-to-all' ) . '</strong></p>' .
					'<p>' . esc_html__( 'You can add copyright text, a link to the title and date of the post, and HTML before and after the content', 'add-to-all' ) . '</p>' .
					'<p>' . esc_html__( 'You must click the Save Changes button at the bottom of the screen for new settings to take effect.', 'add-to-all' ) . '</p>',
			),
		);

		/**
		 * Filter to add more help tabs.
		 *
		 * @since 1.8.0
		 *
		 * @param array $help_tabs Associative array of help tabs.
		 */
		return apply_filters( self::$prefix . '_settings_help', $help_tabs );
	}

	/**
	 * Add footer text on the plugin page.
	 *
	 * @since 2.0.0
	 */
	public static function get_admin_footer_text() {
		return sprintf(
			/* translators: 1: Opening achor tag with Plugin page link, 2: Closing anchor tag, 3: Opening anchor tag with review link. */
			__( 'Thank you for using %1$sWebberZone Snippetz%2$s! Please %3$srate us%2$s on %3$sWordPress.org%2$s', 'knowledgebase' ),
			'<a href="https://webberzone.com/plugins/add-to-all/" target="_blank">',
			'</a>',
			'<a href="https://wordpress.org/support/plugin/add-to-all/reviews/#new-post" target="_blank">'
		);
	}

	/**
	 * Modify settings when they are being saved.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $settings Settings array.
	 * @return string  $settings  Sanitized settings array.
	 */
	public function change_settings_on_save( $settings ) {

		return $settings;
	}

	/**
	 * Redirect to the correct settings page on save.
	 *
	 * @since 2.0.0
	 */
	public function redirect_on_save() {
		if ( isset( $_GET['page'] ) && $_GET['page'] === $this->menu_slug && isset( $_GET['settings-updated'] ) && true === (bool) $_GET['settings-updated'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended

			$location = $this->get_settings_location();
			wp_safe_redirect( $location );
			exit;
		}
	}

	/**
	 * Get link of the Settings page.
	 *
	 * @since 2.0.1
	 */
	public function get_settings_location() {
		if ( \WebberZone\Snippetz\Util\Helpers::is_snippets_enabled() ) {
			$location = admin_url( "/edit.php?post_type=ata_snippets&page={$this->menu_slug}" );
		} else {
			$location = admin_url( "/options-general.php?page={$this->menu_slug}" );
		}
		return $location;
	}
}
