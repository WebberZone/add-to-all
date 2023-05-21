<?php
/**
 * Helper functions
 *
 * @since 1.4.0
 *
 * @package WebberZone\Snippetz
 */

namespace WebberZone\Snippetz\Util;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Helpers class - contains utility functions.
 *
 * @since 2.0.0
 */
class Helpers {

	/**
	 * Replace placeholders with their content.
	 *
	 * @since 1.4.0
	 *
	 * @param string $input Content with placeholders to be replaced.
	 * @return string Content with placeholders replaced.
	 */
	public static function process_placeholders( $input ) {
		$content = get_post_field( 'post_content' );

		$placeholders = array(
			'%year%'         => gmdate( 'Y' ), // A full numeric representation of a year, 4 digits.
			'%month%'        => gmdate( 'F' ), // January through December.
			'%date%'         => gmdate( 'j' ), // Date - 01 to 31.
			'%first_year%'   => self::get_first_post_year(),
			'%home_url%'     => get_home_url(),
			'%reading_time%' => self::get_reading_time( $content ),
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

	/**
	 * Get the year of the first post.
	 *
	 * @since 1.4.0
	 *
	 * @return string|bool Year fo the first post or false if error.
	 */
	public static function get_first_post_year() {
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
	 * @param array  $input      Input string.
	 * @param string $delimiter  Delimiter.
	 * @param string $enclosure  Enclosure.
	 * @param string $terminator Terminating string.
	 * @return string CSV string.
	 */
	public static function str_putcsv( $input, $delimiter = ',', $enclosure = '"', $terminator = "\n" ) {
		// First convert associative array to numeric indexed array.
		$work_array = array();
		foreach ( $input as $key => $value ) {
			$work_array[] = $value;
		}

		$string     = '';
		$array_size = count( $work_array );

		for ( $i = 0; $i < $array_size; $i++ ) {
			// Nested array, process nest item.
			if ( is_array( $work_array[ $i ] ) ) {
				$string .= self::str_putcsv( $work_array[ $i ], $delimiter, $enclosure, $terminator );
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

	/**
	 * Calculate the estimated reading time for a given piece of $content.
	 *
	 * @since 1.8.0
	 *
	 * @param string $content Content use to determine reading time.
	 * @param array  $args {
	 *      Optional. Arguments to configure the reading time.
	 *
	 *     @type int        $wpm      Words per minute.
	 *     @type string     $before   Text to display before.
	 *     @type string     $after    Text to display after.
	 *     @type bool       $echo     Whether to echo or return.
	 * }
	 * @return string Estimated reading time if $args['echo'] set to false, void if true.
	 */
	public static function get_reading_time( $content = '', $args = array() ) {
		$defaults = array(
			'wpm'    => 200,
			'before' => '',
			'after'  => '',
			'echo'   => false,
		);

		$parsed_args = wp_parse_args( $args, $defaults );

		$content = wp_strip_all_tags( strip_shortcodes( $content ) );
		$words   = str_word_count( $content );
		$minutes = floor( $words / $parsed_args['wpm'] );
		$seconds = floor( ( $words % $parsed_args['wpm'] ) / ( $parsed_args['wpm'] / 60 ) );

		$minutes_text = sprintf( _n( '%s minute', '%s minutes', $minutes, 'add-to-all' ), number_format_i18n( $minutes ) );
		$seconds_text = sprintf( _n( '%s second', '%s seconds', $seconds, 'add-to-all' ), number_format_i18n( $seconds ) );
		$time         = $minutes_text . ', ' . $seconds_text;
		$time         = $parsed_args['before'] . $time . $parsed_args['after'];

		/**
		 * Filters the reading time.
		 *
		 * @since 1.8.0
		 *
		 * @param string $time Estimated reading time.
		 * @param string $content Content.
		 * @param array $parse_args Parsed arguments array.
		 */
		$time = apply_filters( 'ata_get_reading_time', $time, $content, $parsed_args );

		if ( $parsed_args['echo'] ) {
			echo esc_html( $time );
		} else {
			return $time;
		}
	}

	/**
	 * Is snippets enabled?
	 *
	 * @return boolean True if snippets are enabled. False if not.
	 */
	public static function is_snippets_enabled() {
		return ( ata_get_option( 'enable_snippets' ) && ! ( defined( '\ATA_DISABLE_SNIPPETS' ) && \ATA_DISABLE_SNIPPETS ) );
	}

}
