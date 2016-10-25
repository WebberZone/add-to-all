<?php
/**
 * Renders the settings page.
 * Portions of this code have been inspired by Easy Digital Downloads, WordPress Settings Sandbox, etc.
 *
 * @link https://webberzone.com
 * @since 1.2.0
 *
 * @package Add_to_All
 * @subpackage Admin/Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Render the settings page.
 *
 * @since 1.2.0
 *
 * @return void
 */
function ata_options_page() {
	$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], ata_get_settings_sections() ) ? $_GET['tab'] : 'third_party';

	ob_start();
	?>
	<div class="wrap">
		<h1><?php _e( 'Add to All Settings', 'add-to-all' ); // WPCS: XSS OK. ?></h1>

		<?php //settings_errors(); ?>

		<h2 class="nav-tab-wrapper">
			<?php
			foreach ( ata_get_settings_sections() as $tab_id => $tab_name ) {

				$tab_url = add_query_arg(
					array(
					'settings-updated' => false,
					'tab' => $tab_id,
					)
				);

				$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

				echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
							echo esc_html( $tab_name );
				echo '</a>';

			}
			?>
		</h2>

		<div id="tab_container">
			<form method="post" action="options.php">
				<table class="form-table">
				<?php
					settings_fields( 'ata_settings' );
					do_settings_fields( 'ata_settings_' . $active_tab, 'ata_settings_' . $active_tab );
				?>
				</table>

				<p>
				<?php
					// Default submit button
					submit_button(
						__( 'Submit', 'add-to-all' ),
						'primary',
						'submit',
						false
					);

					echo '&nbsp;&nbsp;';

					// Reset button.
					$confirm = esc_js( __( 'Do you really want to reset all these settings to their default values?', 'add-to-all' ) );
					submit_button(
						__( 'Reset', 'add-to-all' ),
						'secondary',
						'settings_reset',
						false,
						array(
							'onclick' => "return confirm('{$confirm}');",
						)
					);
				?>
				</p>
			</form>
		</div><!-- /#tab_container-->

		<div id="dashboard-widgets" class="metabox-holder">

			<?php include_once( 'footer.php' ); ?>

		</div><!-- /#dashboard-widgets -->

	</div><!-- /.wrap -->

	<?php
	echo ob_get_clean();
}

/**
 * Array containing the settings' sections.
 *
 * @since 1.2.0
 *
 * @return array Settings array
 */
function ata_get_settings_sections() {
	$ata_settings_sections = array(
		'third_party' => esc_html__( 'Third Party', 'add-to-all' ),
		'head'        => esc_html__( 'Header', 'add-to-all' ),
		'content'     => esc_html__( 'Content', 'add-to-all' ),
		'footer'      => esc_html__( 'Footer', 'add-to-all' ),
		'feed'        => esc_html__( 'Feed', 'add-to-all' ),
	);

	/**
	 * Filter the array containing the settings' sections.
	 *
	 * @since 1.2.0
	 *
	 * @param array $ata_settings_sections Settings array
	 */
	return apply_filters( 'ata_settings_sections', $ata_settings_sections );

}


/**
 * Miscellaneous callback funcion
 *
 * @since 1.2.0
 *
 * @return void
 */
function ata_missing_callback( $args ) {
	printf( esc_html__( 'The callback function used for the %1$s setting is missing.', 'add-to-all' ), '<strong>' . esc_attr( $args['id'] ) . '</strong>' );
}


/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since 1.2.0
 * @param array $args Arguments passed by the setting
 * @return void
 */
function ata_header_callback( $args ) {

	/**
	 * After Settings Output filter
	 *
	 * @since 1.2.0
	 * @param string $html HTML string.
	 * @param array Arguments array.
	 */
	echo apply_filters( 'ata_after_setting_output', '', $args );
}


/**
 * Display text fields.
 *
 * @since 1.2.0
 *
 * @param array $args Array of arguments
 * @return void
 */
function ata_text_callback( $args ) {

	// First, we read the options collection
	global $ata_settings;

	if ( isset( $ata_settings[ $args['id'] ] ) ) {
		$value = $ata_settings[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$html = '<input type="text" id="ata_settings[' . $args['id'] . ']" name="ata_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />';
	$html .= '<p class="description">' . $args['desc'] . '</p>';

	/** This filter has been defined in settings-page.php **/
	echo apply_filters( 'ata_after_setting_output', $html, $args );

}


/**
 * Display textarea.
 *
 * @since 1.2.0
 *
 * @param array $args Array of arguments
 * @return void
 */
function ata_textarea_callback( $args ) {

	// First, we read the options collection
	global $ata_settings;

	if ( isset( $ata_settings[ $args['id'] ] ) ) {
		$value = $ata_settings[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$html = '<textarea class="large-text" cols="50" rows="5" id="ata_settings[' . $args['id'] . ']" name="ata_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
	$html .= '<p class="description">' . $args['desc'] . '</p>';

	/** This filter has been defined in settings-page.php **/
	echo apply_filters( 'ata_after_setting_output', $html, $args );
}


/**
 * Display checboxes.
 *
 * @since 1.2.0
 *
 * @param array $args Array of arguments
 * @return void
 */
function ata_checkbox_callback( $args ) {

	// First, we read the options collection
	global $ata_settings;

	$checked = isset( $ata_settings[ $args['id'] ] ) ? checked( 1, $ata_settings[ $args['id'] ], false ) : '';

	$html = '<input type="checkbox" id="ata_settings[' . $args['id'] . ']" name="ata_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
	$html .= '<p class="description">' . $args['desc'] . '</p>';

	/** This filter has been defined in settings-page.php **/
	echo apply_filters( 'ata_after_setting_output', $html, $args );

}


/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since 1.2.0
 *
 * @param array $args Array of arguments
 * @return void
 */
function ata_multicheck_callback( $args ) {
	global $ata_settings;
	$html = '';

	if ( ! empty( $args['options'] ) ) {
		foreach ( $args['options'] as $key => $option ) {
			if ( isset( $ata_settings[ $args['id'] ][ $key ] ) ) {
				$enabled = $option;
			} else {
				$enabled = null;
			}

			$html .= '<input name="ata_settings[' . $args['id'] . '][' . $key . ']" id="ata_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked( $option, $enabled, false ) . '/> <br />';

			$html .= '<label for="ata_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
		}

		$html .= '<p class="description">' . $args['desc'] . '</p>';
	}

	/** This filter has been defined in settings-page.php **/
	echo apply_filters( 'ata_after_setting_output', $html, $args );

}


/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since 1.2.0
 *
 * @param array $args Array of arguments
 * @return void
 */
function ata_radio_callback( $args ) {
	global $ata_settings;
	$html = '';

	foreach ( $args['options'] as $key => $option ) {
		$checked = false;

		if ( isset( $ata_settings[ $args['id'] ] ) && $ata_settings[ $args['id'] ] == $key ) {
			$checked = true;
		} elseif ( isset( $args['options'] ) && $args['options'] == $key && ! isset( $ata_settings[ $args['id'] ] ) ) {
			$checked = true;
		}

		$html .= '<input name="ata_settings[' . $args['id'] . ']"" id="ata_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/> <br />';
		$html .= '<label for="ata_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
	}

	$html .= '<p class="description">' . $args['desc'] . '</p>';

	/** This filter has been defined in settings-page.php **/
	echo apply_filters( 'ata_after_setting_output', $html, $args );

}


/**
 * Number Callback
 *
 * Renders number fields.
 *
 * @since 1.2.0
 *
 * @param array $args Array of arguments
 * @return void
 */
function ata_number_callback( $args ) {
	global $ata_settings;

	if ( isset( $ata_settings[ $args['id'] ] ) ) {
		$value = $ata_settings[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	$max  = isset( $args['max'] ) ? $args['max'] : 999999;
	$min  = isset( $args['min'] ) ? $args['min'] : 0;
	$step = isset( $args['step'] ) ? $args['step'] : 1;

	$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
	$html = '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . $size . '-text" id="ata_settings[' . $args['id'] . ']" name="ata_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
	$html .= '<p class="description">' . $args['desc'] . '</p>';

	/** This filter has been defined in settings-page.php **/
	echo apply_filters( 'ata_after_setting_output', $html, $args );

}


/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since 1.2.0
 *
 * @param array $args Array of arguments
 * @return void
 */
function ata_select_callback( $args ) {
	global $ata_settings;

	if ( isset( $ata_settings[ $args['id'] ] ) ) {
		$value = $ata_settings[ $args['id'] ];
	} else {
		$value = isset( $args['options'] ) ? $args['options'] : '';
	}

	if ( isset( $args['chosen'] ) ) {
		$chosen = 'class="ata-chosen"';
	} else {
		$chosen = '';
	}

	$html = '<select id="ata_settings[' . $args['id'] . ']" name="ata_settings[' . $args['id'] . ']" ' . $chosen . ' />';

	foreach ( $args['options'] as $option => $name ) {
		$selected = selected( $option, $value, false );
		$html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
	}

	$html .= '</select>';
	$html .= '<p class="description">' . $args['desc'] . '</p>';

	/** This filter has been defined in settings-page.php **/
	echo apply_filters( 'ata_after_setting_output', $html, $args );

}


