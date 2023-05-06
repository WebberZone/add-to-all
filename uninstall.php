<?php
/**
 * Fired when the plugin is uninstalled
 *
 * @package WebberZone\Snippetz
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

if ( is_multisite() ) {
	$sites = get_sites(
		array(
			'archived' => 0,
			'spam'     => 0,
			'deleted'  => 0,
		)
	);

	foreach ( $sites as $site ) {
		switch_to_blog( $site->blog_id );
		ata_delete_data();
		restore_current_blog();
	}
} else {
	ata_delete_data();
}

/**
 * Delete Data.
 *
 * @since 1.4.0
 */
function ata_delete_data() {
	global $wpdb;

	delete_option( 'ata_settings' );
	delete_option( 'ald_ata_settings' );
	delete_transient( 'ata_first_post_year' );

	// Delete custom post type data for Snippets only if the constant is set.
	if ( defined( 'ATA_REMOVE_SNIPPETS_DATA' ) && ATA_REMOVE_SNIPPETS_DATA ) {
		$snippets = get_posts(
			array(
				'post_type'      => 'ata_snippets',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
			)
		);

		if ( $snippets ) {
			foreach ( $snippets as $snippet ) {
				wp_delete_post( $snippet->ID, false );
			}
		}

		/** Delete All the Taxonomies */
		foreach ( array( 'ata_snippets_category' ) as $taxonomy ) {

			// Delete Terms.
			$terms = $wpdb->get_results( $wpdb->prepare( "SELECT t.*, tt.* FROM $wpdb->terms AS t INNER JOIN $wpdb->term_taxonomy AS tt ON t.term_id = tt.term_id WHERE tt.taxonomy IN (%s) ORDER BY t.name ASC", $taxonomy ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

			if ( $terms ) {
				foreach ( $terms as $term ) {
					$wpdb->delete( $wpdb->term_taxonomy, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->delete( $wpdb->term_relationships, array( 'term_taxonomy_id' => $term->term_taxonomy_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->delete( $wpdb->terms, array( 'term_id' => $term->term_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				}
			}

			// Delete Taxonomy.
			$wpdb->delete( $wpdb->term_taxonomy, array( 'taxonomy' => $taxonomy ), array( '%s' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		}
	}
}
