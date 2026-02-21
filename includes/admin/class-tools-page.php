<?php
/**
 * Tools page.
 *
 * @package WebberZone\Snippetz\Admin
 */

namespace WebberZone\Snippetz\Admin;

use WebberZone\Snippetz\Util\Hook_Registry;
use WebberZone\Snippetz\Snippets\Minifier;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Tools page.
 */
class Tools_Page {

	/**
	 * Parent Menu ID.
	 *
	 * @since 2.3.0
	 *
	 * @var string Parent Menu ID.
	 */
	public $parent_id;

	/**
	 * Constructor.
	 */
	public function __construct() {
		Hook_Registry::add_action( 'admin_menu', array( $this, 'add_tools_page' ) );
		Hook_Registry::add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		Hook_Registry::add_action( 'admin_post_ata_clear_assets', array( $this, 'clear_assets' ) );
		Hook_Registry::add_action( 'admin_post_ata_regenerate_combined', array( $this, 'regenerate_combined' ) );
		Hook_Registry::add_action( 'admin_post_ata_export_settings', array( $this, 'process_settings_export' ) );
		Hook_Registry::add_action( 'admin_post_ata_import_settings', array( $this, 'process_settings_import' ) );
	}

	/**
	 * Add tools page.
	 */
	public function add_tools_page() {
		if ( \WebberZone\Snippetz\Util\Helpers::is_snippets_enabled() ) {
			$parent_slug = 'edit.php?post_type=ata_snippets';
			$menu_title  = esc_html__( 'Tools', 'add-to-all' );
		} else {
			$parent_slug = 'tools.php';
			$menu_title  = esc_html__( 'Snippetz Tools', 'add-to-all' );
		}

		$this->parent_id = add_submenu_page(
			$parent_slug,
			esc_html__( 'WebberZone Snippetz Tools', 'add-to-all' ),
			$menu_title,
			'manage_options',
			'ata_tools',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the tools page.
	 *
	 * @since 2.3.0
	 *
	 * @return void
	 */
	public function render_page() {

		/* Message for successful file import */
		if ( isset( $_GET['settings_import'] ) && 'success' === $_GET['settings_import'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_settings_error( 'ata-notices', '', esc_html__( 'Settings have been imported successfully', 'add-to-all' ), 'updated' );
		}

		if ( isset( $_GET['assets_regenerated'] ) && isset( $_GET['snippet_files'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$snippet_count = absint( wp_unslash( $_GET['snippet_files'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$notice        = sprintf(
				/* translators: %d: number of snippet files regenerated. */
				_n( 'Regenerated assets and %d snippet file.', 'Regenerated assets and %d snippet files.', $snippet_count, 'add-to-all' ),
				$snippet_count
			);
			add_settings_error( 'ata-notices', '', esc_html( $notice ), 'updated' );
		}

		ob_start();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'WebberZone Snippetz Tools', 'add-to-all' ); ?></h1>
			<?php do_action( 'ata_tools_page_header' ); ?>

			<?php settings_errors(); ?>

			<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">

				<div class="postbox">
					<h2><span><?php esc_html_e( 'System Information', 'add-to-all' ); ?></span></h2>
					<div class="inside">
						<p><strong><?php esc_html_e( 'Plugin Version:', 'add-to-all' ); ?></strong> <?php echo esc_html( WZ_SNIPPETZ_VERSION ); ?></p>
						<p><strong><?php esc_html_e( 'WordPress Version:', 'add-to-all' ); ?></strong> <?php echo esc_html( get_bloginfo( 'version' ) ); ?></p>
						<p><strong><?php esc_html_e( 'PHP Version:', 'add-to-all' ); ?></strong> <?php echo esc_html( PHP_VERSION ); ?></p>
						<p><strong><?php esc_html_e( 'Snippets Enabled:', 'add-to-all' ); ?></strong> 
							<?php echo \WebberZone\Snippetz\Util\Helpers::is_snippets_enabled() ? esc_html__( 'Yes', 'add-to-all' ) : esc_html__( 'No', 'add-to-all' ); ?>
						</p>
					</div>
				</div>

				<div class="postbox">
					<h2><span><?php esc_html_e( 'Combined File Stats', 'add-to-all' ); ?></span></h2>
					<div class="inside">
						<?php
						$files = array(
							'css' => array(
								'title'    => esc_html__( 'CSS File', 'add-to-all' ),
								'filename' => 'combined.min.css',
							),
							'js'  => array(
								'title'    => esc_html__( 'JS File', 'add-to-all' ),
								'filename' => 'combined.min.js',
							),
						);

						foreach ( $files as $type => $file ) {
							$stats = Minifier::get_file_stats( $file['filename'] );
							?>
							<p><strong><?php echo esc_html( $file['title'] ); ?>:</strong>
							<?php if ( $stats ) : ?>
								<br />
								<span class="description">
									<?php esc_html_e( 'URL:', 'add-to-all' ); ?> <a href="<?php echo esc_url( $stats['url'] ); ?>" target="_blank"><?php echo esc_html( $stats['url'] ); ?></a><br />
									<?php esc_html_e( 'Path:', 'add-to-all' ); ?> <?php echo esc_html( $stats['path'] ); ?><br />
									<?php esc_html_e( 'Size:', 'add-to-all' ); ?> <?php echo esc_html( size_format( $stats['size'], 2 ) ); ?><br />
									<?php esc_html_e( 'Last Modified:', 'add-to-all' ); ?> <?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $stats['mtime'] ) ); ?>
								</span>
							<?php else : ?>
								<span class="description"><?php esc_html_e( 'File does not exist.', 'add-to-all' ); ?></span>
							<?php endif; ?>
							</p>
							<?php
						}
						?>
					</div>
				</div>

				<div class="postbox">
					<h2><span><?php esc_html_e( 'Clear Generated Assets', 'add-to-all' ); ?></span></h2>
					<div class="inside">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<?php wp_nonce_field( 'ata_clear_assets' ); ?>
							<input type="hidden" name="action" value="ata_clear_assets">
							<p>
								<?php submit_button( esc_html__( 'Clear generated assets', 'add-to-all' ), 'secondary', 'cache_clear', false ); ?>
							</p>
							<p class="description">
							<?php esc_html_e( 'Clear the WebberZone Snippetz cache. This will clear all cached CSS and JS files.', 'add-to-all' ); ?>
							</p>
						</form>
					</div>
				</div>

				<div class="postbox">
					<h2><span><?php esc_html_e( 'Regenerate Assets', 'add-to-all' ); ?></span></h2>
					<div class="inside">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<?php wp_nonce_field( 'ata_regenerate_combined' ); ?>
							<input type="hidden" name="action" value="ata_regenerate_combined">
							<p>
								<?php submit_button( esc_html__( 'Regenerate Assets', 'add-to-all' ), 'primary', 'regenerate_combined', false ); ?>
							</p>
							<p class="description">
							<?php esc_html_e( 'Regenerate combined CSS and JS files. This is useful when you have made changes to your snippets and want to force the browser to load the updated files.', 'add-to-all' ); ?>
							</p>
						</form>
					</div>
				</div>

				<div class="postbox">
					<h2><span><?php esc_html_e( 'Export/Import Settings', 'add-to-all' ); ?></span></h2>
					<div class="inside">
						<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<p class="description">
							<?php esc_html_e( 'Export the plugin settings for this site as a .json file. This allows you to easily import the configuration into another site.', 'add-to-all' ); ?>
							</p>
							<p><input type="hidden" name="action" value="ata_export_settings" /></p>
							<p>
							<?php submit_button( esc_html__( 'Export Settings', 'add-to-all' ), 'primary', 'ata_export_settings', false ); ?>
							</p>
							<?php wp_nonce_field( 'ata_export_settings_nonce', 'ata_export_settings_nonce' ); ?>
						</form>

						<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
							<p class="description">
							<?php esc_html_e( 'Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.', 'add-to-all' ); ?>
							</p>
							<p>
								<input type="file" name="import_settings_file" />
							</p>
							<p>
							<?php submit_button( esc_html__( 'Import Settings', 'add-to-all' ), 'primary', 'ata_import_settings', false ); ?>
							</p>
							<input type="hidden" name="action" value="ata_import_settings" />
							<?php wp_nonce_field( 'ata_import_settings_nonce', 'ata_import_settings_nonce' ); ?>
						</form>
					</div>
				</div>

				<?php
				/**
				 * Action hook to add additional tools page content.
				 *
				 * @since 2.3.0
				 */
				do_action( 'ata_admin_tools_page_content' );
				?>

			</div><!-- /#post-body-content -->

			<div id="postbox-container-1" class="postbox-container">

				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<?php include_once __DIR__ . '/sidebar.php'; ?>
				</div><!-- /#side-sortables -->

			</div><!-- /#postbox-container-1 -->
			</div><!-- /#post-body -->
			<br class="clear" />
			</div><!-- /#poststuff -->

		</div><!-- /.wrap -->

		<?php
		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Regenerate combined files.
	 */
	public function regenerate_combined() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'add-to-all' ) );
		}

		check_admin_referer( 'ata_regenerate_combined' );

		$snippet_files = \WebberZone\Snippetz\Snippets\Functions::regenerate_snippet_files();
		Minifier::save_combined_css();
		Minifier::save_combined_js();

		if ( \WebberZone\Snippetz\Util\Helpers::is_snippets_enabled() ) {
			$redirect_url = admin_url( 'edit.php?post_type=ata_snippets&page=ata_tools' );
		} else {
			$redirect_url = admin_url( 'tools.php?page=ata_tools' );
		}

		$redirect_url = add_query_arg(
			array(
				'assets_regenerated' => 1,
				'snippet_files'      => $snippet_files,
			),
			$redirect_url
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Process a settings export that generates a .json file of the plugin settings.
	 *
	 * @since 2.3.0
	 */
	public function process_settings_export() {

		if ( ! isset( $_POST['ata_export_settings_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['ata_export_settings_nonce'] ), 'ata_export_settings_nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = get_option( 'ata_settings' );

		ignore_user_abort( true );

		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=ata-settings-export-' . gmdate( 'm-d-Y' ) . '.json' );
		header( 'Expires: 0' );

		echo wp_json_encode( $settings );
		exit;
	}

	/**
	 * Process a settings import from a json file.
	 *
	 * @since 2.3.0
	 */
	public function process_settings_import() {

		if ( ! isset( $_POST['ata_import_settings_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['ata_import_settings_nonce'] ) ), 'ata_import_settings_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed', 'add-to-all' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'add-to-all' ) );
		}

		$filename = 'import_settings_file';

		$tmp       = isset( $_FILES[ $filename ]['name'] ) ? explode( '.', sanitize_file_name( wp_unslash( $_FILES[ $filename ]['name'] ) ) ) : array();
		$extension = end( $tmp );

		if ( 'json' !== $extension ) {
			wp_die( esc_html__( 'Please upload a valid .json file', 'add-to-all' ) );
		}

		$import_file = isset( $_FILES[ $filename ]['tmp_name'] ) ? ( wp_unslash( $_FILES[ $filename ]['tmp_name'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		if ( empty( $import_file ) ) {
			wp_die( esc_html__( 'Please upload a file to import', 'add-to-all' ) );
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$settings = (array) json_decode( file_get_contents( $import_file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		update_option( 'ata_settings', $settings );

		if ( \WebberZone\Snippetz\Util\Helpers::is_snippets_enabled() ) {
			$redirect_url = admin_url( 'edit.php?post_type=ata_snippets&page=ata_tools&settings_import=success' );
		} else {
			$redirect_url = admin_url( 'tools.php?page=ata_tools&settings_import=success' );
		}

		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since 2.3.0
	 *
	 * @param string $hook The current screen hook.
	 */
	public function admin_enqueue_scripts( $hook ) {
		$screen = get_current_screen();

		if ( $this->parent_id === $screen->id || $this->parent_id === $hook ) {
			wp_enqueue_style( 'wp-spinner' );
		}
	}

	/**
	 * Clear generated assets and cached data.
	 *
	 * @since 2.3.0
	 */
	public function clear_assets() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'add-to-all' ) );
		}

		check_admin_referer( 'ata_clear_assets' );

		// Clear all cache transients.
		$cache_keys = array(
			'ata_combined_css',
			'ata_combined_js',
			'ata_first_post_year',
		);

		foreach ( $cache_keys as $key ) {
			delete_transient( $key );
		}

		// Clear stored combined file URLs.
		delete_option( 'ata_combined_css_url' );
		delete_option( 'ata_combined_js_url' );

		// Delete all generated files.
		Minifier::delete_generated_files();
		Minifier::clear_snippet_file_meta();

		if ( \WebberZone\Snippetz\Util\Helpers::is_snippets_enabled() ) {
			$redirect_url = admin_url( 'edit.php?post_type=ata_snippets&page=ata_tools' );
		} else {
			$redirect_url = admin_url( 'tools.php?page=ata_tools' );
		}

		wp_safe_redirect( $redirect_url );
		exit;
	}
}
