<?php
/**
 * Register ATA Custom Post Type admin columns.
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

if ( ! class_exists( 'ATA_Admin_Columns' ) ) :
	/**
	 * ATA Metabox class to add custom columns to the ata_snippets post type listing in admin.
	 *
	 * @since 1.7.0
	 */
	class ATA_Admin_Columns {

		/**
		 * Main constructor class.
		 */
		public function __construct() {
			add_filter( 'manage_ata_snippets_posts_columns', array( $this, 'manage_post_columns' ), 10 );
			add_action( 'manage_ata_snippets_posts_custom_column', array( $this, 'manage_posts_custom_column' ), 10, 2 );
			add_action( 'admin_head', array( $this, 'custom_css' ) );
		}

		/**
		 * Add columns to the custom post type ata_snippets.
		 *
		 * @param array $columns An associative array of column headings.
		 */
		public function manage_post_columns( $columns ) {
			// Create a new array so we can modify array position.
			$new_columns = array();

			// Loop through the existing columns and evaluate the column.
			foreach ( $columns as $key => $title ) {
				$new_columns[ $key ] = $title;

				if ( 'title' === $key ) {
					$new_columns['shortcode'] = __( 'Shortcode', 'add-to-all' );
				}
			}

			return $new_columns;
		}

		/**
		 * Adds the content for the custom columns.
		 *
		 * @param string $column_name The name of the column to display.
		 * @param int    $post_id     The current post ID.
		 */
		public function manage_posts_custom_column( $column_name, $post_id ) {
			switch ( $column_name ) {
				case 'shortcode':
					$shortcode = "[ata_snippet id={$post_id}]";

					$output = "\n" . '<span class="ata_shortcode"><input type="text"'
					. ' onfocus="this.select();" readonly="readonly"'
					. ' value="' . esc_attr( $shortcode ) . '"'
					. ' class="large-text code" /></span>';

					echo trim( $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					break;
			}

		}

		/**
		 * Add custom CSS to the admin head.
		 */
		public function custom_css() {
			$screen = get_current_screen();

			if ( 'edit-ata_snippets' === $screen->id ) {

				echo '<style>#shortcode{width:200px}</style>';

			}

		}

	}

	new ATA_Admin_Columns();

endif;
