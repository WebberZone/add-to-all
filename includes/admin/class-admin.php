<?php
/**
 * Admin class.
 *
 * @since 2.3.0
 *
 * @package WebberZone\Snippetz
 */

namespace WebberZone\Snippetz\Admin;

use WebberZone\Snippetz\Util\Hook_Registry;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class to register the WebberZone Snippetz Admin Area.
 *
 * @since 2.3.0
 */
class Admin {

	/**
	 * Settings API.
	 *
	 * @since 2.3.0
	 *
	 * @var object Settings API.
	 */
	public $settings;

	/**
	 * Tools page.
	 *
	 * @since 2.3.0
	 *
	 * @var object Tools page.
	 */
	public $tools_page;

	/**
	 * Admin banner helper instance.
	 *
	 * @since 2.3.0
	 *
	 * @var Admin_Banner
	 */
	public Admin_Banner $admin_banner;

	/**
	 * Main constructor class.
	 *
	 * @since 2.3.0
	 */
	public function __construct() {
		$this->settings     = new Settings();
		$this->tools_page   = new Tools_Page();
		$this->admin_banner = new Admin_Banner( $this->get_admin_banner_config() );
	}

	/**
	 * Retrieve the configuration array for the admin banner.
	 *
	 * @since 2.3.0
	 *
	 * @return array<string, mixed>
	 */
	private function get_admin_banner_config(): array {
		$snippets_enabled = \WebberZone\Snippetz\Util\Helpers::is_snippets_enabled();
		$settings_url     = $snippets_enabled
			? admin_url( 'edit.php?post_type=ata_snippets&page=ata_options_page' )
			: admin_url( 'options-general.php?page=ata_options_page' );
		$snippets_url     = admin_url( 'edit.php?post_type=ata_snippets' );
		$tools_url        = $snippets_enabled
			? admin_url( 'edit.php?post_type=ata_snippets&page=ata_tools' )
			: admin_url( 'tools.php?page=ata_tools' );

		$sections = array(
			'settings' => array(
				'label'      => esc_html__( 'Settings', 'add-to-all' ),
				'url'        => $settings_url,
				'type'       => 'primary',
				'screen_ids' => array(
					'settings_page_ata_options_page',
				),
				'page_slugs' => array( 'ata_options_page' ),
			),
			'tools'    => array(
				'label'      => esc_html__( 'Tools', 'add-to-all' ),
				'url'        => $tools_url,
				'screen_ids' => array(
					'tools_page_ata_tools',
					'ata_snippets_page_ata_tools',
				),
				'page_slugs' => array( 'ata_tools' ),
			),
			'plugins'  => array(
				'label'  => esc_html__( 'WebberZone Plugins', 'add-to-all' ),
				'url'    => 'https://webberzone.com/plugins/',
				'type'   => 'secondary',
				'target' => '_blank',
				'rel'    => 'noopener noreferrer',
			),
		);

		if ( $snippets_enabled ) {
			$sections = array_merge(
				array(
					'snippets' => array(
						'label'      => esc_html__( 'Snippets', 'add-to-all' ),
						'url'        => $snippets_url,
						'type'       => 'primary',
						'screen_ids' => array(
							'edit-ata_snippets',
							'ata_snippets',
							'edit-ata_snippets_category',
						),
						'page_slugs' => array(),
					),
				),
				$sections
			);

			$sections['settings']['screen_ids'][] = 'ata_snippets_page_ata_options_page';
		}

		return array(
			'capability'           => 'manage_options',
			'prefix'               => 'ata',
			'exclude_screen_bases' => array(),
			'strings'              => array(
				'region_label' => esc_html__( 'WebberZone Snippetz quick links', 'add-to-all' ),
				'nav_label'    => esc_html__( 'WebberZone Snippetz admin shortcuts', 'add-to-all' ),
				'eyebrow'      => esc_html__( 'WebberZone Snippetz', 'add-to-all' ),
				'title'        => esc_html__( 'Add custom code snippets to your site with ease.', 'add-to-all' ),
				'text'         => esc_html__( 'Manage snippets, configure settings, and enhance your WordPress site.', 'add-to-all' ),
			),
			'sections'             => $sections,
		);
	}
}
