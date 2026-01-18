<?php
/**
 * Minifier class for CSS/JS minification and combination.
 *
 * @package WebberZone\Snippetz\Snippets
 */

namespace WebberZone\Snippetz\Snippets;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use WebberZone\Snippetz\Util\Helpers;

/**
 * Minifier class.
 */
class Minifier {

	/**
	 * Upload subdirectory under uploads.
	 */
	public const UPLOAD_SUBDIR = 'snippetz';

	/**
	 * Get upload directory info for generated assets.
	 *
	 * @since 2.3.0
	 *
	 * @return array Upload directory information.
	 */
	public static function get_upload_dir() {
		return wp_upload_dir();
	}

	/**
	 * Get base upload path or URL for generated assets.
	 *
	 * @since 2.3.0
	 *
	 * @param string $key Upload directory key.
	 * @return string Base path or URL.
	 */
	private static function get_upload_base( $key ) {
		$upload_dir = self::get_upload_dir();
		$base       = $upload_dir[ $key ] ?? '';

		return trailingslashit( $base ) . self::UPLOAD_SUBDIR . '/';
	}

	/**
	 * Get upload directory path for generated assets.
	 *
	 * @since 2.3.0
	 *
	 * @param string $filename Optional filename.
	 * @return string Path to the upload directory or file.
	 */
	public static function get_upload_path( $filename = '' ) {
		$dir = self::get_upload_base( 'basedir' );

		return $filename ? $dir . $filename : $dir;
	}

	/**
	 * Get upload directory URL for generated assets.
	 *
	 * @since 2.3.0
	 *
	 * @param string $filename Optional filename.
	 * @return string URL to the upload directory or file.
	 */
	public static function get_upload_url( $filename = '' ) {
		$dir = self::get_upload_base( 'baseurl' );

		return $filename ? $dir . $filename : $dir;
	}

	/**
	 * Get snippet filename for a given snippet and type.
	 *
	 * @since 2.3.0
	 *
	 * @param int    $snippet_id Snippet ID.
	 * @param string $type Snippet type.
	 * @return string Filename.
	 */
	public static function get_snippet_filename( $snippet_id, $type ) {
		return sprintf( 'snippet-%d.min.%s', $snippet_id, $type );
	}

	/**
	 * Get snippet file path.
	 *
	 * @since 2.3.0
	 *
	 * @param int    $snippet_id Snippet ID.
	 * @param string $type Snippet type.
	 * @return string File path.
	 */
	public static function get_snippet_path( $snippet_id, $type ) {
		return self::get_upload_path( self::get_snippet_filename( $snippet_id, $type ) );
	}

	/**
	 * Get snippet file URL.
	 *
	 * @since 2.3.0
	 *
	 * @param int    $snippet_id Snippet ID.
	 * @param string $type Snippet type.
	 * @return string File URL.
	 */
	public static function get_snippet_url( $snippet_id, $type ) {
		return self::get_upload_url( self::get_snippet_filename( $snippet_id, $type ) );
	}

	/**
	 * Delete snippet file by URL.
	 *
	 * @since 2.3.0
	 *
	 * @param string $file_url File URL.
	 * @return void
	 */
	public static function delete_snippet_file_by_url( $file_url ) {
		if ( empty( $file_url ) ) {
			return;
		}

		$file_path = str_replace( self::get_upload_url(), self::get_upload_path(), $file_url );
		if ( file_exists( $file_path ) ) {
			wp_delete_file( $file_path );
		}
	}

	/**
	 * Write snippet file content using WP_Filesystem.
	 *
	 * @since 2.3.0
	 *
	 * @param string $file_path File path.
	 * @param string $content File content.
	 * @return bool True on success.
	 */
	public static function write_snippet_file( $file_path, $content ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();
		global $wp_filesystem;
		if ( ! $wp_filesystem ) {
			return false;
		}

		return (bool) $wp_filesystem->put_contents( $file_path, $content, FS_CHMOD_FILE );
	}

	/**
	 * Delete generated files.
	 *
	 * @since 2.3.0
	 *
	 * @return void
	 */
	public static function delete_generated_files() {
		$dir = self::get_upload_path();

		if ( ! file_exists( $dir ) ) {
			return;
		}

		$files = glob( $dir . '*' );
		if ( ! is_array( $files ) ) {
			return;
		}

		foreach ( $files as $file ) {
			if ( is_file( $file ) ) {
				wp_delete_file( $file );
			}
		}
	}

	/**
	 * Clear snippet file URL meta for all snippets.
	 *
	 * @since 2.3.0
	 *
	 * @return void
	 */
	public static function clear_snippet_file_meta() {
		$snippets = get_posts(
			array(
				'post_type'      => 'ata_snippets',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
			)
		);

		if ( empty( $snippets ) ) {
			return;
		}

		foreach ( $snippets as $snippet_id ) {
			delete_post_meta( $snippet_id, '_ata_snippet_file' );
		}
	}

	/**
	 * Minify CSS content.
	 *
	 * @param string $css CSS content.
	 * @return string Minified CSS.
	 */
	public static function minify_css( $css ) {
		if ( class_exists( '\MatthiasMullie\Minify\CSS' ) ) {
			$minifier = new \MatthiasMullie\Minify\CSS( $css );
			return $minifier->minify();
		}
		// Fallback to basic minification.
		$css = preg_replace( '/\/\*.*?\*\//s', '', $css );
		$css = preg_replace( '/\s+/', ' ', $css );
		$css = preg_replace( '/\s*([{}:;,])\s*/', '$1', $css );
		return trim( $css );
	}

	/**
	 * Minify JS content.
	 *
	 * @param string $js JS content.
	 * @return string Minified JS.
	 */
	public static function minify_js( $js ) {
		if ( class_exists( '\MatthiasMullie\Minify\JS' ) ) {
			$minifier = new \MatthiasMullie\Minify\JS( $js );
			return $minifier->minify();
		}
		// Fallback to basic minification.
		$js = preg_replace( '/\/\*.*?\*\//s', '', $js );
		$js = preg_replace( '/\/\/.*$/m', '', $js );
		$js = preg_replace( '/\s+/', ' ', $js );
		return trim( $js );
	}

	/**
	 * Combine and minify all CSS snippets.
	 *
	 * @return string Combined minified CSS.
	 */
	public static function combine_css() {
		$css = self::get_combined_content( 'css' );
		return self::minify_css( $css );
	}

	/**
	 * Combine and minify all JS snippets.
	 *
	 * @return string Combined minified JS.
	 */
	public static function combine_js() {
		$js = self::get_combined_content( 'js' );
		return self::minify_js( $js );
	}

	/**
	 * Get combined content for snippets.
	 *
	 * @param string $type Snippet type (css or js).
	 * @return string Combined content.
	 */
	private static function get_combined_content( $type ) {
		$locations = array( 'header', 'footer', 'content_before', 'content_after' );
		$snippets  = array();

		foreach ( $locations as $location ) {
			$location_snippets = Functions::get_snippets_by_location( $location );
			if ( is_array( $location_snippets ) ) {
				$snippets = array_merge( $snippets, $location_snippets );
			}
		}

		$combined_content = '';
		foreach ( $snippets as $snippet ) {
			if ( ! $snippet instanceof \WP_Post ) {
				continue;
			}
			if ( Functions::get_snippet_type( $snippet ) === $type ) {
				$content           = get_post_field( 'post_content', $snippet->ID );
				$content           = Helpers::process_placeholders( $content );
				$combined_content .= $content . "\n";
			}
		}
		return $combined_content;
	}

	/**
	 * Save content to uploads and store the URL in an option.
	 *
	 * @param string $content     File contents.
	 * @param string $filename    Filename.
	 * @param string $option_name Option name to store URL.
	 * @return bool True on success.
	 */
	private static function save_combined_file( $content, $filename, $option_name ) {
		$dir = self::get_upload_path();
		wp_mkdir_p( $dir );
		$file_path = self::get_upload_path( $filename );
		$written   = self::write_snippet_file( $file_path, $content );
		if ( ! $written ) {
			return false;
		}

		return update_option( $option_name, self::get_upload_url( $filename ) );
	}

	/**
	 * Save combined CSS to file.
	 */
	public static function save_combined_css() {
		$content = self::combine_css();
		if ( empty( $content ) ) {
			return;
		}

		self::save_combined_file( $content, 'combined.css', 'ata_combined_css_url' );
	}

	/**
	 * Save combined JS to file.
	 */
	public static function save_combined_js() {
		$content = self::combine_js();
		if ( empty( $content ) ) {
			return;
		}

		self::save_combined_file( $content, 'combined.js', 'ata_combined_js_url' );
	}

	/**
	 * Get file stats.
	 *
	 * @since 2.3.0
	 *
	 * @param string $filename Filename.
	 * @return array|bool Array of file stats or false if file does not exist.
	 */
	public static function get_file_stats( $filename ) {
		$file_path = self::get_upload_path( $filename );

		if ( ! file_exists( $file_path ) ) {
			return false;
		}

		return array(
			'url'   => self::get_upload_url( $filename ),
			'path'  => $file_path,
			'size'  => filesize( $file_path ),
			'mtime' => filemtime( $file_path ),
		);
	}
}
