<?php
/**
 * Register ATA Metabox.
 *
 * @link  https://webberzone.com
 * @since 1.7.0
 *
 * @package WebberZone\Snippetz
 */

namespace WebberZone\Snippetz\Snippets;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ATA Metabox class to register the metabox for ata_snippets post type.
 *
 * @since 1.7.0
 */
class Metabox {

	/**
	 * Metabox API.
	 *
	 * @var object Metabox API.
	 */
	public $metabox_api;

	/**
	 * Settings Key.
	 *
	 * @var string Settings Key.
	 */
	public $settings_key;

	/**
	 * Prefix which is used for creating the unique filters and actions.
	 *
	 * @var string Prefix.
	 */
	public $prefix;

	/**
	 * Main constructor class.
	 */
	public function __construct() {
		$this->settings_key = 'ata_meta';
		$this->prefix       = 'ata';

		$this->metabox_api = new \WebberZone\Snippetz\Admin\Settings\Metabox_API(
			array(
				'settings_key'           => $this->settings_key,
				'prefix'                 => $this->prefix,
				'post_type'              => 'ata_snippets',
				'title'                  => __( 'WebberZone Snippetz', 'add-to-all' ),
				'registered_settings'    => $this->get_registered_settings(),
				'checkbox_modified_text' => __( 'Modified from default', 'add-to-all' ),
			)
		);
	}

	/**
	 * Get registered settings for metabox.
	 *
	 * @return array Registered settings.
	 */
	public function get_registered_settings() {

		$settings = array(
			'step1_header'         => array(
				'id'   => 'step1_header',
				'name' => '<h3>' . esc_html__( 'Step 1: Select where should this be displayed', 'add-to-all' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'add_to_header'        => array(
				'id'      => 'add_to_header',
				'name'    => __( 'Add to Header', 'add-to-all' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'add_to_footer'        => array(
				'id'      => 'add_to_footer',
				'name'    => __( 'Add to Footer', 'add-to-all' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'content_before'       => array(
				'id'      => 'content_before',
				'name'    => __( 'Add before Content', 'add-to-all' ),
				'desc'    => __( 'When enabled the contents of this snippet are automatically added before the content of posts based on the selection below.', 'add-to-all' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'content_after'        => array(
				'id'      => 'content_after',
				'name'    => esc_html__( 'Add after Content', 'add-to-all' ),
				'desc'    => esc_html__( 'When enabled the contents of this snippet are automatically added after the content of posts based on the selection below.', 'add-to-all' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'step2_header'         => array(
				'id'   => 'step2_header',
				'name' => '<h3>' . esc_html__( 'Step 2: Select the conditions where this will be displayed', 'add-to-all' ) . '</h3>',
				'desc' => esc_html__( 'Leaving any option blank will not apply the filter', 'add-to-all' ),
				'type' => 'header',
			),
			'include_relation'     => array(
				'id'      => 'include_relation',
				'name'    => esc_html__( 'The logical relationship between each condition below', 'add-to-all' ),
				'desc'    => esc_html__( 'Selecting OR would match any of the condition below and selecting AND would match all the conditions below.', 'add-to-all' ),
				'type'    => 'radio',
				'default' => 'or',
				'options' => array(
					'or'  => esc_html__( 'OR', 'add-to-all' ),
					'and' => esc_html__( 'AND', 'add-to-all' ),
				),
			),
			'include_on_posttypes' => array(
				'id'      => 'include_on_posttypes',
				'name'    => esc_html__( 'Include on these post types', 'add-to-all' ),
				'desc'    => esc_html__( 'Select on which post types to display the contents of this snippet.', 'add-to-all' ),
				'type'    => 'posttypes',
				'options' => '',
			),
			'include_on_posts'     => array(
				'id'      => 'include_on_posts',
				'name'    => esc_html__( 'Include on these Post IDs', 'add-to-all' ),
				'desc'    => esc_html__( 'Enter a comma-separated list of post, page or custom post type IDs on which to include the code. Any incorrect ids will be removed when saving.', 'add-to-all' ),
				'size'    => 'large',
				'type'    => 'postids',
				'options' => '',
			),
			'include_on_category'  => array(
				'id'               => 'include_on_category',
				'name'             => esc_html__( 'Include on these Categories', 'add-to-all' ),
				'desc'             => esc_html__( 'Comma separated list of category slugs. The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. Does not support custom taxonomies.', 'add-to-all' ),
				'type'             => 'csv',
				'options'          => '',
				'size'             => 'large',
				'field_class'      => 'category_autocomplete',
				'field_attributes' => array(
					'data-wp-taxonomy' => 'category',
				),
			),
			'include_on_post_tag'  => array(
				'id'               => 'include_on_post_tag',
				'name'             => esc_html__( 'Include on these Tags', 'add-to-all' ),
				'desc'             => esc_html__( 'Comma separated list of tag slugs. The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. Does not support custom taxonomies.', 'add-to-all' ),
				'type'             => 'csv',
				'options'          => '',
				'size'             => 'large',
				'field_class'      => 'category_autocomplete',
				'field_attributes' => array(
					'data-wp-taxonomy' => 'post_tag',
				),
			),
		);

		/**
		 * Filter array of registered settings for metabox.
		 *
		 * @param array Registered settings.
		 */
		$settings = apply_filters( $this->prefix . '_metabox_settings', $settings );

		return $settings;
	}

}
