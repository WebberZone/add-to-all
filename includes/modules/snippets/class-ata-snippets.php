<?php
/**
 * Register Snippets Post Type.
 *
 * @since 1.7.0
 *
 * @package Add_to_All
 * @subpackage Post_Types
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'ATA_Snippets' ) ) :
	/**
	 * ATA Settings class to register the settings.
	 *
	 * @version 1.0
	 * @since   1.7.0
	 */
	class ATA_Snippets {

		/**
		 * Holds the name of the post type.
		 *
		 * @var string Post type.
		 */
		protected $post_type;

		/**
		 * Constructor function.
		 */
		public function __construct() {
			$this->post_type = 'ata_snippets';

			add_action( 'init', array( $this, 'register_post_type' ), 0 );
			add_action( 'init', array( $this, 'register_taxonomy' ), 0 );

			add_filter( 'wp_editor_settings', array( $this, 'wp_editor_settings' ), 10, 2 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
			add_filter( 'the_content', array( $this, 'remove_wpautop' ), 0 );
			add_action( 'edit_form_after_title', array( $this, 'media_buttons' ) );
			add_filter( 'media_view_strings', array( $this, 'media_view_strings' ), 10, 2 );
		}

		/**
		 * Register Snippets Post Type.
		 *
		 * @return void
		 */
		public function register_post_type() {

			$labels  = array(
				'name'                  => _x( 'Snippets', 'Post Type General Name', 'add-to-all' ),
				'singular_name'         => _x( 'Snippet', 'Post Type Singular Name', 'add-to-all' ),
				'menu_name'             => __( 'Snippets', 'add-to-all' ),
				'name_admin_bar'        => __( 'Snippet', 'add-to-all' ),
				'archives'              => __( 'Snippet Archives', 'add-to-all' ),
				'attributes'            => __( 'Snippet Attributes', 'add-to-all' ),
				'parent_item_colon'     => __( 'Parent Snippet:', 'add-to-all' ),
				'all_items'             => __( 'All Snippets', 'add-to-all' ),
				'add_new_item'          => __( 'Add New Snippet', 'add-to-all' ),
				'add_new'               => __( 'Add New Snippet', 'add-to-all' ),
				'new_item'              => __( 'New Snippet', 'add-to-all' ),
				'edit_item'             => __( 'Edit Snippet', 'add-to-all' ),
				'update_item'           => __( 'Update Snippet', 'add-to-all' ),
				'view_item'             => __( 'View Snippet', 'add-to-all' ),
				'view_items'            => __( 'View Snippets', 'add-to-all' ),
				'search_items'          => __( 'Search Snippet', 'add-to-all' ),
				'not_found'             => __( 'Not found', 'add-to-all' ),
				'not_found_in_trash'    => __( 'Not found in Trash', 'add-to-all' ),
				'featured_image'        => __( 'Featured Image', 'add-to-all' ),
				'set_featured_image'    => __( 'Set featured image', 'add-to-all' ),
				'remove_featured_image' => __( 'Remove featured image', 'add-to-all' ),
				'use_featured_image'    => __( 'Use as featured image', 'add-to-all' ),
				'insert_into_item'      => __( 'Insert into snippet', 'add-to-all' ),
				'uploaded_to_this_item' => __( 'Uploaded to this snippet', 'add-to-all' ),
				'items_list'            => __( 'Snippets list', 'add-to-all' ),
				'items_list_navigation' => __( 'Snippets list navigation', 'add-to-all' ),
				'filter_items_list'     => __( 'Filter snippets list', 'add-to-all' ),
			);
			$rewrite = array(
				'slug'       => 'snippet',
				'with_front' => false,
				'pages'      => true,
				'feeds'      => false,
			);
			$args    = array(
				'label'               => __( 'Snippet', 'add-to-all' ),
				'description'         => __( 'WebberZone Snippetz Snippets', 'add-to-all' ),
				'labels'              => $labels,
				'supports'            => array( 'title', 'editor', 'revisions', 'custom-fields' ),
				'taxonomies'          => array( 'ata_snippets_category' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => 5,
				'menu_icon'           => 'dashicons-format-aside',
				'show_in_admin_bar'   => true,
				'show_in_nav_menus'   => true,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => true,
				'rewrite'             => $rewrite,
				'capabilities'        => array(
					'publish_posts'       => 'manage_options',
					'edit_posts'          => 'manage_options',
					'edit_others_posts'   => 'manage_options',
					'delete_posts'        => 'manage_options',
					'delete_others_posts' => 'manage_options',
					'read_private_posts'  => 'manage_options',
					'edit_post'           => 'manage_options',
					'delete_post'         => 'manage_options',
					'read_post'           => 'manage_options',
				),
				'show_in_rest'        => false,
			);

			/**
			 * Filter the arguments passed to register the Snippets post type.
			 *
			 * @since 1.7.0
			 *
			 * @param array $args Register Post type arguments.
			 */
			$args = apply_filters( $this->post_type . '_args', $args );

			register_post_type( $this->post_type, $args );
		}

		/**
		 * Register WebberZone Snippetz Snippet Category taxonomy
		 *
		 * @return void
		 */
		public function register_taxonomy() {

			$labels  = array(
				'name'                       => _x( 'Snippet Categories', 'Taxonomy General Name', 'add-to-all' ),
				'singular_name'              => _x( 'Snippet Category', 'Taxonomy Singular Name', 'add-to-all' ),
				'menu_name'                  => __( 'Snippet Category', 'add-to-all' ),
				'all_items'                  => __( 'All Categories', 'add-to-all' ),
				'parent_item'                => __( 'Parent Category', 'add-to-all' ),
				'parent_item_colon'          => __( 'Parent Category:', 'add-to-all' ),
				'new_item_name'              => __( 'New Snippet Category', 'add-to-all' ),
				'add_new_item'               => __( 'Add New Snippet Category', 'add-to-all' ),
				'edit_item'                  => __( 'Edit Snippet Category', 'add-to-all' ),
				'update_item'                => __( 'Update Snippet Category', 'add-to-all' ),
				'view_item'                  => __( 'View Snippet Category', 'add-to-all' ),
				'separate_items_with_commas' => __( 'Separate snippet categories with commas', 'add-to-all' ),
				'add_or_remove_items'        => __( 'Add or remove snippet categories', 'add-to-all' ),
				'choose_from_most_used'      => __( 'Choose from the most used', 'add-to-all' ),
				'popular_items'              => __( 'Popular Snippet Categories', 'add-to-all' ),
				'search_items'               => __( 'Search Snippet Categories', 'add-to-all' ),
				'not_found'                  => __( 'Not Found', 'add-to-all' ),
				'no_terms'                   => __( 'No categories', 'add-to-all' ),
				'items_list'                 => __( 'Categories list', 'add-to-all' ),
				'items_list_navigation'      => __( 'Categories list navigation', 'add-to-all' ),
			);
			$rewrite = array(
				'slug'         => 'snippet-category',
				'with_front'   => true,
				'hierarchical' => false,
			);
			$args    = array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_nav_menus' => true,
				'show_tagcloud'     => true,
				'rewrite'           => $rewrite,
				'show_in_rest'      => false,
			);

			/**
			 * Filter the arguments passed to register the Snippet categories.
			 *
			 * @since 1.7.0
			 *
			 * @param array $args Register Taxonomy arguments array.
			 */
			$args = apply_filters( 'ata_snippets_category_args', $args );

			register_taxonomy( 'ata_snippets_category', array( $this->post_type ), $args );
		}

		/**
		 * Update Editor settings for $this->post_type custom post type
		 *
		 * @param array  $settings Array of editor arguments.
		 * @param string $editor_id Unique editor identifier.
		 * @return array Updated settings array.
		 */
		public function wp_editor_settings( $settings, $editor_id ) {
			if ( 'content' === $editor_id && get_current_screen()->post_type === $this->post_type ) {
				$settings['wpautop']       = false;
				$settings['tinymce']       = false;
				$settings['quicktags']     = false;
				$settings['media_buttons'] = false;
				$settings['editor_class']  = 'codemirror_html';
			}

			return $settings;
		}

		/**
		 * Enqueue scripts and styles.
		 *
		 * @param string $hook The current admin page.
		 */
		public function admin_enqueue_scripts( $hook ) {

			$minimize = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

			if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || get_current_screen()->post_type !== $this->post_type ) {
				return;
			}

			wp_enqueue_style( 'wp-color-picker' );

			wp_enqueue_media();
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );

			wp_enqueue_code_editor(
				array(
					'type'       => 'text/html',
					'codemirror' => array(
						'indentUnit' => 2,
						'tabSize'    => 2,
					),
				)
			);

			wp_enqueue_script( 'ata-codemirror-js', ATA_PLUGIN_URL . 'includes/admin/js/apply-codemirror' . $minimize . '.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'ata-media-js', ATA_PLUGIN_URL . 'includes/admin/js/media-selector' . $minimize . '.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'ata-taxonomy-suggest-js', ATA_PLUGIN_URL . 'includes/admin/js/taxonomy-suggest' . $minimize . '.js', array( 'jquery' ), '1.0', true );
		}

		/**
		 * Remove wpautop when viewing the custom post type.
		 *
		 * @param string $content Post content.
		 * @return string Updated post content.
		 */
		public function remove_wpautop( $content ) {

			( get_post_type() === $this->post_type ) && remove_filter( 'the_content', 'wpautop' );
			return $content;
		}

		/**
		 * Add media buttons.
		 *
		 * @param WP_Post $post Post object.
		 */
		public function media_buttons( $post ) {
			if ( get_post_type( $post ) === $this->post_type ) {
				printf(
					'<br /><button type="button" class="button insert-codemirror-media add_media" data-editor="content">%1$s</button><br /><br />',
					esc_html__( 'Add Media', 'add-to-all' )
				);
			}
		}

		/**
		 * Edit media strings.
		 *
		 * @param string[] $strings Array of media view strings keyed by the name they'll be referenced by in JavaScript.
		 * @param  WP_Post  $post    Post object.
		 * @return string[] Updated strings array.
		 */
		public function media_view_strings( $strings, $post ) {
			if ( get_post_type( $post ) === $this->post_type ) {
				$strings['createGalleryTitle']       = '';
				$strings['setFeaturedImageTitle']    = '';
				$strings['insertFromUrlTitle']       = '';
				$strings['createPlaylistTitle']      = '';
				$strings['createVideoPlaylistTitle'] = '';
				$strings['insertIntoPost']           = __( 'Insert into editor', 'add-to-all' );
			}
			return $strings;
		}
	}

	new ATA_Snippets();

endif;
