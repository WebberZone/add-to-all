<?php
/**
 * Add to All lets you add custom text or HTML to your WordPress header, footer, sidebar, content or feed.
 *
 * @package Add_to_All
 *
 * @wordpress-plugin
 * Plugin Name: Add to All
 * Version:     1.3.0
 * Plugin URI:  https://ajaydsouza.com/wordpress/plugins/add-to-all/
 * Description: A powerful plugin that will allow you to add custom code or CSS to your header, footer, sidebar, content or feed.
 * Author:      Ajay D'Souza
 * Author URI:  https://ajaydsouza.com/
 * Text Domain: add-to-all
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, then abort execution.
if ( ! defined( 'WPINC' ) ) {
	die( "Aren't you supposed to come here via WP-Admin?" );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Top 10
 *
 * @since 1.2.0
 *
 * @var string Plugin folder path
 */
if ( ! defined( 'ATA_PLUGIN_DIR' ) ) {
	define( 'ATA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Top 10
 *
 * @since 1.2.0
 *
 * @var string Plugin folder URL
 */
if ( ! defined( 'ATA_PLUGIN_URL' ) ) {
	define( 'ATA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Top 10
 *
 * @since 1.2.0
 *
 * @var string Plugin Root File
 */
if ( ! defined( 'ATA_PLUGIN_FILE' ) ) {
	define( 'ATA_PLUGIN_FILE', __FILE__ );
}


/**
 * Declare $ata_settings global so that it can be accessed in every function
 */
global $ata_settings;
$ata_settings = ata_get_settings();


/**
 * Get Settings.
 *
 * Retrieves all plugin settings
 *
 * @since  1.2.0
 * @return array Add to All settings
 */
function ata_get_settings() {

	$settings = get_option( 'ata_settings' );

	/**
	 * Settings array
	 *
	 * Retrieves all plugin settings
	 *
	 * @since 1.2.0
	 * @param array $settings Settings array
	 */
	return apply_filters( 'ata_get_settings', $settings );
}


/**
 * Function to load translation files.
 */
function ata_lang_init() {
	load_plugin_textdomain( 'add-to-all', false, dirname( plugin_basename( ATA_PLUGIN_FILE ) ) . '/languages/' );
}
add_action( 'init', 'ata_lang_init' );


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

	$output = strip_tags( strip_shortcodes( $content ) );

	if ( $excerpt_length > 0 ) {
		$output = wp_trim_words( $output, $excerpt_length );
	}

	return apply_filters( 'ata_excerpt', $output, $id, $excerpt_length, $use_excerpt );
}


/*
 *----------------------------------------------------------------------------
 * Include files
 *----------------------------------------------------------------------------
 */

	require_once ATA_PLUGIN_DIR . 'includes/admin/default-settings.php';
	require_once ATA_PLUGIN_DIR . 'includes/admin/register-settings.php';
	require_once ATA_PLUGIN_DIR . 'includes/content.php';
	require_once ATA_PLUGIN_DIR . 'includes/header.php';
	require_once ATA_PLUGIN_DIR . 'includes/footer.php';
	require_once ATA_PLUGIN_DIR . 'includes/feed.php';
	require_once ATA_PLUGIN_DIR . 'includes/modules/statcounter.php';
	require_once ATA_PLUGIN_DIR . 'includes/modules/google-analytics.php';
	require_once ATA_PLUGIN_DIR . 'includes/modules/tynt.php';
	require_once ATA_PLUGIN_DIR . 'includes/modules/site-verification.php';


/*
 *----------------------------------------------------------------------------
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
 */

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	require_once ATA_PLUGIN_DIR . 'includes/admin/admin.php';
	require_once ATA_PLUGIN_DIR . 'includes/admin/settings-page.php';
	require_once ATA_PLUGIN_DIR . 'includes/admin/save-settings.php';
	require_once ATA_PLUGIN_DIR . 'includes/admin/help-tab.php';

}

/*
 *----------------------------------------------------------------------------
 * Deprecated functions, variables and constants
 *----------------------------------------------------------------------------
 */

	require_once ATA_PLUGIN_DIR . '/includes/deprecated.php';

