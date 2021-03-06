<?php
/**
 * Helper functions
 *
 * @since 1.4.0
 *
 * @package Add_to_All
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Function to get the post thumbnail.
 *
 * @since 1.0
 *
 * @param integer $postid Post ID.
 * @return string Image tag with the post thumbnail
 */
function ata_get_the_post_thumbnail( $postid ) {

	$result = get_post( $postid );
	global $ata_settings;
	$output = '';
	$title  = get_the_title( $postid );

	if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $result->ID ) ) {
		$output .= get_the_post_thumbnail(
			$result->ID,
			array(
				$ata_settings['thumb_width'],
				$ata_settings['thumb_height'],
			),
			array(
				'title'  => $title,
				'alt'    => $title,
				'class'  => 'ata_thumb',
				'border' => '0',
			)
		);
	} else {
		$postimage = get_post_meta( $result->ID, $ata_settings['thumb_meta'], true );
		if ( ! $postimage && $ata_settings['scan_images'] ) {
			preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $result->post_content, $matches );

			if ( isset( $matches ) && $matches[1][0] ) {
				$postimage = $matches[1][0]; // Get the first one only.
			}
		}
		if ( ! $postimage ) {
			// If no other thumbnail set, try to get the custom video thumbnail set by the Video Thumbnails plugin.
			$postimage = get_post_meta( $result->ID, '_video_thumbnail', true );
		}       if ( $ata_settings['thumb_default_show'] && ! $postimage ) {
			// If no thumb found and settings permit, use default thumb.
			$postimage = $ata_settings['thumb_default'];
		}       if ( $postimage ) {
			$output .= '<img src="' . $postimage . '" alt="' . $title . '" title="' . $title . '" style="max-width:' . $ata_settings['thumb_width'] . 'px;max-height:' . $ata_settings['thumb_height'] . 'px; border:0;" class="ata_thumb" />';
		}
	}

	return apply_filters( 'ata_get_the_post_thumbnail', $output );
}


/**
 * Function to create an excerpt for the post.
 *
 * @since 1.0
 *
 * @param integer $id Post ID.
 * @param mixed   $excerpt_length Length of the excerpt in words.
 * @param bool    $use_excerpt Use excerpt.
 * @return string The excerpt
 */
function ata_excerpt( $id, $excerpt_length = 0, $use_excerpt = true ) {
	$content = '';
	if ( $use_excerpt ) {
		$content = get_post( $id )->post_excerpt;
	}
	if ( '' === $content ) {
		$content = get_post( $id )->post_content;
	}

	$output = wp_strip_all_tags( strip_shortcodes( $content ) );

	if ( $excerpt_length > 0 ) {
		$output = wp_trim_words( $output, $excerpt_length );
	}

	return apply_filters( 'ata_excerpt', $output, $id, $excerpt_length, $use_excerpt );
}


/**
 * Replace placeholders with their content.
 *
 * @since 1.4.0
 *
 * @param string $input Content with placeholders to be replaced.
 * @return string Content with placeholders replaced.
 */
function ata_process_placeholders( $input ) {

	$placeholders = array(
		'%year%'       => gmdate( 'Y' ), // A full numeric representation of a year, 4 digits.
		'%month%'      => gmdate( 'F' ), // January through December.
		'%date%'       => gmdate( 'j' ), // Date - 01 to 31.
		'%first_year%' => ata_get_first_post_year(),
		'%home_url%'   => get_home_url(),
	);

	/**
	 * Filters array of placeholders in the format 'placeholder' => 'replaced_text'.
	 *
	 * @since 1.4.0
	 *
	 * @param array $placeholders Array of placeholders.
	 */
	$placeholders = apply_filters( 'ata_placeholders', $placeholders );

	$search  = array_keys( $placeholders );
	$replace = array_values( $placeholders );

	$output = str_replace( $search, $replace, $input );

	/**
	 * Filters content with placeholders replaced.
	 *
	 * @since 1.4.0
	 *
	 * @param string $output Content with placeholders replaced.
	 * @param string $input Content with placeholders to be replaced.
	 * @param array  $placeholders Placeholders.
	 */
	return apply_filters( 'ata_process_placeholders', $output, $input, $placeholders );
}
add_filter( 'ata_content_html_before', 'ata_process_placeholders', 99 );
add_filter( 'ata_content_html_after', 'ata_process_placeholders', 99 );
add_filter( 'ata_content_html_before_single', 'ata_process_placeholders', 99 );
add_filter( 'ata_content_html_after_single', 'ata_process_placeholders', 99 );
add_filter( 'ata_content_html_before_post', 'ata_process_placeholders', 99 );
add_filter( 'ata_content_html_after_post', 'ata_process_placeholders', 99 );
add_filter( 'ata_content_html_before_page', 'ata_process_placeholders', 99 );
add_filter( 'ata_content_html_after_page', 'ata_process_placeholders', 99 );
add_filter( 'ata_feed_html_before', 'ata_process_placeholders', 99 );
add_filter( 'ata_feed_html_after', 'ata_process_placeholders', 99 );
add_filter( 'ata_footer_other_html', 'ata_process_placeholders', 99 );


/**
 * Get the year of the first post.
 *
 * @since 1.4.0
 *
 * @return string|bool Year fo the first post or false if error.
 */
function ata_get_first_post_year() {
	global $wpdb;

	$year = get_transient( 'ata_first_post_year' );

	if ( false === $year ) {

		$year = $wpdb->get_results( " SELECT YEAR(min(post_date_gmt)) AS firstyear FROM {$wpdb->posts} WHERE post_status = 'publish' " ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching

		set_transient( 'ata_first_post_year', $year, WEEK_IN_SECONDS );
	}

	if ( $year ) {
		return $year[0]->firstyear;
	}

	return false;
}


/**
 * Convert a string to CSV.
 *
 * @since 2.9.0
 *
 * @param array  $array Input string.
 * @param string $delimiter Delimiter.
 * @param string $enclosure Enclosure.
 * @param string $terminator Terminating string.
 * @return string CSV string.
 */
function ata_str_putcsv( $array, $delimiter = ',', $enclosure = '"', $terminator = "\n" ) {
	// First convert associative array to numeric indexed array.
	$work_array = array();
	foreach ( $array as $key => $value ) {
		$work_array[] = $value;
	}

	$string     = '';
	$array_size = count( $work_array );

	for ( $i = 0; $i < $array_size; $i++ ) {
		// Nested array, process nest item.
		if ( is_array( $work_array[ $i ] ) ) {
			$string .= ata_str_putcsv( $work_array[ $i ], $delimiter, $enclosure, $terminator );
		} else {
			switch ( gettype( $work_array[ $i ] ) ) {
				// Manually set some strings.
				case 'NULL':
					$sp_format = '';
					break;
				case 'boolean':
					$sp_format = ( true === $work_array[ $i ] ) ? 'true' : 'false';
					break;
				// Make sure sprintf has a good datatype to work with.
				case 'integer':
					$sp_format = '%i';
					break;
				case 'double':
					$sp_format = '%0.2f';
					break;
				case 'string':
					$sp_format        = '%s';
					$work_array[ $i ] = str_replace( "$enclosure", "$enclosure$enclosure", $work_array[ $i ] );
					break;
				// Unknown or invalid items for a csv - note: the datatype of array is already handled above, assuming the data is nested.
				case 'object':
				case 'resource':
				default:
					$sp_format = '';
					break;
			}
			$string .= sprintf( '%2$s' . $sp_format . '%2$s', $work_array[ $i ], $enclosure );
			$string .= ( $i < ( $array_size - 1 ) ) ? $delimiter : $terminator;
		}
	}

	return $string;
}

