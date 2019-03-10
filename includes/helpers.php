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

