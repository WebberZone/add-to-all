<?php
/**
 * Register ATA Metabox.
 *
 * @link  https://webberzone.com
 * @since 1.7.0
 *
 * @package WebberZone\Snippetz
 */

namespace WebberZone\Snippetz\Snippets;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * ATA Metabox class to register the metabox for ata_snippets post type.
 *
 * @since 1.7.0
 */
class Metabox {

	/**
	 * Settings Key.
	 *
	 * @var string Settings Key.
	 */
	public $settings_key;

	/**
	 * Prefix which is used for creating the unique filters and actions.
	 *
	 * @var string Prefix.
	 */
	public $prefix;

	/**
	 * Main constructor class.
	 */
	public function __construct() {
		$this->settings_key = 'ata_meta';
		$this->prefix       = 'ata';

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_ata_snippets', array( $this, 'save' ) );
	}

	/**
	 * Function to add the metabox.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			$this->prefix . '_metabox_id',
			__( 'WebberZone Snippetz', 'add-to-all' ),
			array( $this, 'html' ),
			$this->prefix . '_snippets'
		);
	}

	/**
	 * Function to save the metabox.
	 *
	 * @param int|string $post_id Post ID.
	 */
	public function save( $post_id ) {

		$post_meta = array();

		// Bail if we're doing an auto save.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// If our nonce isn't there, or we can't verify it, bail.
		if ( ! isset( $_POST[ $this->prefix . '_meta_box_nonce' ] ) || ! wp_verify_nonce( sanitize_key( $_POST[ $this->prefix . '_meta_box_nonce' ] ), $this->prefix . '_meta_box' ) ) {
			return;
		}

		// If our current user can't edit this post, bail.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( empty( $_POST[ $this->settings_key ] ) ) {
			return;
		}

		$posted = $_POST[ $this->settings_key ]; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash

		foreach ( $this->get_registered_settings() as $setting ) {
			$id   = $setting['id'];
			$type = isset( $setting['type'] ) ? $setting['type'] : 'text';

			/**
			 * Skip settings that are not really settings.
			 *
			 * @param array $non_setting_types Array of types which are not settings.
			 */
			$non_setting_types = apply_filters( $this->prefix . '_metabox_non_setting_types', array( 'header', 'descriptive_text' ) );

			if ( in_array( $type, $non_setting_types, true ) ) {
				continue;
			}

			if ( isset( $posted[ $id ] ) ) {
				$value             = $posted[ $id ];
				$sanitize_callback = method_exists( $this, "sanitize_{$type}" ) ? array( $this, "sanitize_{$type}" ) : array( $this, 'sanitize_missing' );
				$post_meta[ $id ]  = call_user_func( $sanitize_callback, $value );
			}
		}

		// Run the array through a generic function that allows access to all of the settings.
		$post_meta = call_user_func( array( $this, 'sanitize_post_meta' ), $post_meta );

		/**
		 * Filter the ATA post meta array which contains post-specific settings.
		 *
		 * @since 1.7.0
		 *
		 * @param array $post_meta Array of ATA metabox settings.
		 * @param int   $post_id   Post ID
		 */
		$post_meta = apply_filters( '_ata_meta_key', $post_meta, $post_id );

		// Now loop through the settings array and either save or delete the meta key.
		foreach ( $this->get_registered_settings() as $setting ) {
			if ( empty( $post_meta[ $setting['id'] ] ) ) {
				delete_post_meta( $post_id, '_ata_' . $setting['id'] );
			}
		}

		foreach ( $post_meta as $setting => $value ) {
			if ( empty( $post_meta[ $setting ] ) ) {
				delete_post_meta( $post_id, '_ata_' . $setting );
			} else {
				update_post_meta( $post_id, '_ata_' . $setting, $value );
			}
		}
	}

	/**
	 * Function to display the metabox.
	 *
	 * @param object $post Post object.
	 */
	public function html( $post ) {
		// Add an nonce field so we can check for it later.
		wp_nonce_field( $this->prefix . '_meta_box', $this->prefix . '_meta_box_nonce' );

		foreach ( $this->get_registered_settings() as $setting ) {

			$args = wp_parse_args(
				$setting,
				array(
					'id'               => null,
					'name'             => '',
					'desc'             => '',
					'type'             => null,
					'default'          => '',
					'options'          => '',
					'max'              => null,
					'min'              => null,
					'step'             => null,
					'size'             => null,
					'field_class'      => '',
					'field_attributes' => '',
					'placeholder'      => '',
				)
			);

			$id            = $args['id'];
			$value         = get_post_meta( $post->ID, '_ata_' . $id, true );
			$args['value'] = ! empty( $value ) ? $value : ( isset( $args['default'] ) ? $args['default'] : $args['options'] );
			$type          = isset( $args['type'] ) ? $args['type'] : 'text';
			$callback      = method_exists( $this, "callback_{$type}" ) ? array( $this, "callback_{$type}" ) : array( $this, 'callback_missing' );

			// Output the HTML for the setting.
			echo '<p>';
			call_user_func( $callback, $args );
			echo '</p>';

		}

		/**
		 * Action triggered when displaying WebberZone Snippetz meta box
		 *
		 * @since 1.7.0
		 *
		 * @param object $post  Post object.
		 * @param array  $value Value of `_ata_meta_key`.
		 */
		do_action( $this->prefix . '_meta_box', $post, $value );
	}

	/**
	 * Get registered settings for metabox.
	 *
	 * @return array Registered settings.
	 */
	public function get_registered_settings() {

		$settings = array(
			'step1_header'         => array(
				'id'   => 'step1_header',
				'name' => '<h3>' . esc_html__( 'Step 1: Select where should this be displayed', 'add-to-all' ) . '</h3>',
				'desc' => '',
				'type' => 'header',
			),
			'add_to_header'        => array(
				'id'      => 'add_to_header',
				'name'    => __( 'Add to Header', 'add-to-all' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'add_to_footer'        => array(
				'id'      => 'add_to_footer',
				'name'    => __( 'Add to Footer', 'add-to-all' ),
				'desc'    => '',
				'type'    => 'checkbox',
				'options' => false,
			),
			'content_before'       => array(
				'id'      => 'content_before',
				'name'    => __( 'Add before Content', 'add-to-all' ),
				'desc'    => __( 'When enabled the contents of this snippet are automatically added before the content of posts based on the selection below.', 'add-to-all' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'content_after'        => array(
				'id'      => 'content_after',
				'name'    => esc_html__( 'Add after Content', 'add-to-all' ),
				'desc'    => esc_html__( 'When enabled the contents of this snippet are automatically added after the content of posts based on the selection below.', 'add-to-all' ),
				'type'    => 'checkbox',
				'options' => false,
			),
			'step2_header'         => array(
				'id'   => 'step2_header',
				'name' => '<h3>' . esc_html__( 'Step 2: Select the conditions where this will be displayed', 'add-to-all' ) . '</h3>',
				'desc' => esc_html__( 'Leaving any option blank will not apply the filter', 'add-to-all' ),
				'type' => 'header',
			),
			'include_relation'     => array(
				'id'      => 'include_relation',
				'name'    => esc_html__( 'The logical relationship between each condition below', 'add-to-all' ),
				'desc'    => esc_html__( 'Selecting OR would match any of the condition below and selecting AND would match all the conditions below.', 'add-to-all' ),
				'type'    => 'radio',
				'default' => 'or',
				'options' => array(
					'or'  => esc_html__( 'OR', 'add-to-all' ),
					'and' => esc_html__( 'AND', 'add-to-all' ),
				),
			),
			'include_on_posttypes' => array(
				'id'      => 'include_on_posttypes',
				'name'    => esc_html__( 'Include on these post types', 'add-to-all' ),
				'desc'    => esc_html__( 'Select on which post types to display the contents of this snippet.', 'add-to-all' ),
				'type'    => 'posttypes',
				'options' => '',
			),
			'include_on_posts'     => array(
				'id'      => 'include_on_posts',
				'name'    => esc_html__( 'Include on these Post IDs', 'add-to-all' ),
				'desc'    => esc_html__( 'Enter a comma-separated list of post, page or custom post type IDs on which to include the code. Any incorrect ids will be removed when saving.', 'add-to-all' ),
				'size'    => 'large',
				'type'    => 'postids',
				'options' => '',
			),
			'include_on_category'  => array(
				'id'               => 'include_on_category',
				'name'             => esc_html__( 'Include on these Categories', 'add-to-all' ),
				'desc'             => esc_html__( 'Comma separated list of category slugs. The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. Does not support custom taxonomies.', 'add-to-all' ),
				'type'             => 'csv',
				'options'          => '',
				'size'             => 'large',
				'field_class'      => 'category_autocomplete',
				'field_attributes' => array(
					'data-wp-taxonomy' => 'category',
				),
			),
			'include_on_post_tag'  => array(
				'id'               => 'include_on_post_tag',
				'name'             => esc_html__( 'Include on these Tags', 'add-to-all' ),
				'desc'             => esc_html__( 'Comma separated list of tag slugs. The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. Does not support custom taxonomies.', 'add-to-all' ),
				'type'             => 'csv',
				'options'          => '',
				'size'             => 'large',
				'field_class'      => 'category_autocomplete',
				'field_attributes' => array(
					'data-wp-taxonomy' => 'post_tag',
				),
			),
		);

		/**
		 * Filter array of registered settings for metabox.
		 *
		 * @param array Registered settings.
		 */
		$settings = apply_filters( $this->prefix . '_metabox_settings', $settings );

		return $settings;
	}

	/**
	 * Miscellaneous callback function
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_missing( $args ) {
		/* translators: 1: Code. */
		printf( esc_html__( 'The callback function used for the %1$s setting is missing.' ), '<strong>' . esc_attr( $args['id'] ) . '</strong>' );
	}

	/**
	 * Header callback function
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_header( $args ) {
		$html  = $args['name'];
		$html .= $this->get_field_description( $args['desc'] );

		/** This filter has been defined in class-ata-metabox.php */
		echo apply_filters( $this->prefix . '_after_metabox_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Display checkbox
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_checkbox( $args ) {

		$id    = $args['id'];
		$value = $args['value'];

		$checked = checked( 1, $value, false );

		$html  = sprintf( '<label for="%1$s[%2$s]"><strong>%3$s:</strong></label> ', $this->settings_key, sanitize_key( $id ), $args['name'] );
		$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="-1" />', $this->settings_key, sanitize_key( $id ) );
		$html .= sprintf( '<input type="checkbox" id="%1$s[%2$s]" name="%1$s[%2$s]" %3$s style="margin:0 20px 0 10px;" /><br />', $this->settings_key, sanitize_key( $id ), $checked );
		$html .= $this->get_field_description( $args['desc'] );

		/** This filter has been defined in class-ata-metabox.php */
		echo apply_filters( $this->prefix . '_after_metabox_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_multicheck( $args ) {
		$html  = '';
		$id    = $args['id'];
		$value = $args['value'];

		if ( ! empty( $args['options'] ) ) {
			$html .= sprintf( '<input type="hidden" name="%1$s[%2$s]" value="-1" />', $this->settings_key, $id );

			foreach ( $args['options'] as $key => $option ) {
				if ( isset( $value[ $key ] ) ) {
					$enabled = $key;
				} else {
					$enabled = null;
				}

				$html .= sprintf( '<input name="%1$s[%2$s][%3$s]" id="%1$s[%2$s][%3$s]" type="checkbox" value="%4$s" %5$s /> ', $this->settings_key, sanitize_key( $id ), sanitize_key( $key ), esc_attr( $key ), checked( $key, $enabled, false ) );
				$html .= sprintf( '<label for="%1$s[%2$s][%3$s]">%4$s</label> <br />', $this->settings_key, sanitize_key( $id ), sanitize_key( $key ), $option );
			}

			$html .= $this->get_field_description( $args['desc'] );
		}

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @param array $args Array of arguments.
	 * @return void
	 */
	public function callback_radio( $args ) {
		$html  = '';
		$value = $args['value'];

		foreach ( $args['options'] as $key => $option ) {
			$html .= sprintf( '<input name="%1$s[%2$s]" id="%1$s[%2$s][%3$s]" type="radio" value="%3$s" %4$s /> ', $this->settings_key, sanitize_key( $args['id'] ), $key, checked( $value, $key, false ) );
			$html .= sprintf( '<label for="%1$s[%2$s][%3$s]">%4$s</label> <br />', $this->settings_key, sanitize_key( $args['id'] ), $key, $option );
		}

		$html .= $this->get_field_description( $args['desc'] );

		/** This filter has been defined in class-settings-api.php */
		echo apply_filters( $this->prefix . '_after_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Display text fields.
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_text( $args ) {

		$id          = $args['id'];
		$value       = $args['value'];
		$size        = sanitize_html_class( ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular' );
		$class       = sanitize_html_class( $args['field_class'] );
		$placeholder = empty( $args['placeholder'] ) ? '' : ' placeholder="' . $args['placeholder'] . '"';
		$disabled    = ! empty( $args['disabled'] ) ? ' disabled="disabled"' : '';
		$readonly    = ( isset( $args['readonly'] ) && true === $args['readonly'] ) ? ' readonly="readonly"' : '';
		$attributes  = $disabled . $readonly;

		foreach ( (array) $args['field_attributes'] as $attribute => $val ) {
			$attributes .= sprintf( ' %1$s="%2$s"', $attribute, esc_attr( $val ) );
		}

		$html  = sprintf( '<label for="%1$s[%2$s]"><strong>%3$s:</strong></label> ', $this->settings_key, sanitize_key( $id ), $args['name'] );
		$html .= sprintf( '<input type="text" id="%1$s[%2$s]" name="%1$s[%2$s]" class="%3$s" value="%4$s" %5$s %6$s /><br />', $this->settings_key, sanitize_key( $id ), $class . ' ' . $size . '-text', esc_attr( stripslashes( $value ) ), $attributes, $placeholder );
		$html .= $this->get_field_description( $args['desc'] );

		/** This filter has been defined in class-ata-metabox.php */
		echo apply_filters( $this->prefix . '_after_metabox_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Display csv fields.
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_csv( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Display numbercsv fields.
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_numbercsv( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Display post IDs fields.
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_postids( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Display url fields.
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_url( $args ) {
		$this->callback_text( $args );
	}

	/**
	 * Display posttypes fields.
	 *
	 * @param array $args Arguments array.
	 * @return void
	 */
	public function callback_posttypes( $args ) {
		$html  = '';
		$value = $args['value'];

		// If post_types is empty or contains a query string then use parse_str else consider it comma-separated.
		if ( is_array( $value ) ) {
			$post_types = $value;
		} elseif ( ! is_array( $value ) && false === strpos( $value, '=' ) ) {
			$post_types = explode( ',', $value );
		} else {
			parse_str( $value, $post_types );
		}

		$html = sprintf( '<label for="%1$s[%2$s]"><strong>%3$s:</strong></label><br />', $this->settings_key, sanitize_key( $args['id'] ), $args['name'] );

		$wp_post_types   = get_post_types(
			array(
				'public' => true,
			)
		);
		$posts_types_inc = array_intersect( $wp_post_types, $post_types );

		foreach ( $wp_post_types as $wp_post_type ) {

			$html .= sprintf( '<input name="%4$s[%1$s][%2$s]" id="%4$s[%1$s][%2$s]" type="checkbox" value="%2$s" %3$s /> ', sanitize_key( $args['id'] ), esc_attr( $wp_post_type ), checked( true, in_array( $wp_post_type, $posts_types_inc, true ), false ), $this->settings_key );
			$html .= sprintf( '<label for="%3$s[%1$s][%2$s]">%2$s</label> <br />', sanitize_key( $args['id'] ), $wp_post_type, $this->settings_key );

		}

		$html .= $this->get_field_description( $args['desc'] );

		/** This filter has been defined in class-ata-metabox.php */
		echo apply_filters( $this->prefix . '_after_metabox_setting_output', $html, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Get field description for display.
	 *
	 * @param string $desc Description.
	 */
	public function get_field_description( $desc ) {
		if ( ! empty( $desc ) ) {
			$desc = '<em class="description">' . wp_kses_post( $desc ) . '</em>';
		} else {
			$desc = '';
		}

		return $desc;
	}

	/**
	 * Miscellaneous sanitize function
	 *
	 * @param mixed $value Setting Value.
	 * @return string Sanitized value.
	 */
	public function sanitize_missing( $value ) {
		return $value;
	}

	/**
	 * Sanitize checkbox function.
	 *
	 * @param mixed $value Setting Value.
	 * @return string Sanitized value.
	 */
	public function sanitize_checkbox( $value ) {
		$value = ( -1 === (int) $value ) ? 0 : 1;
		return $value;
	}

	/**
	 * Sanitize text field.
	 *
	 * @param string $value Setting Value.
	 * @return string Sanitized value.
	 */
	public function sanitize_text( $value ) {
		return sanitize_text_field( $value );
	}

	/**
	 * Sanitize csv field.
	 *
	 * @param string $value Setting Value.
	 * @return string Sanitized value.
	 */
	public function sanitize_csv( $value ) {
		return implode( ',', array_map( 'trim', explode( ',', sanitize_text_field( wp_unslash( $value ) ) ) ) );
	}

	/**
	 * Sanitize numbercsv field.
	 *
	 * @param string $value Setting Value.
	 * @return string Sanitized value.
	 */
	public function sanitize_numbercsv( $value ) {
		return implode( ',', array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $value ) ) ) ) ) );
	}

	/**
	 * Sanitize postids field.
	 *
	 * @param string $value Setting Value.
	 * @return string Sanitized value.
	 */
	public function sanitize_postids( $value ) {
		$ids = array_filter( array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $value ) ) ) ) );

		foreach ( $ids as $key => $value ) {
			if ( false === get_post_status( $value ) ) {
				unset( $ids[ $key ] );
			}
		}

		return implode( ',', $ids );
	}

	/**
	 * Sanitize posttypes field.
	 *
	 * @param string $value Setting Value.
	 * @return string Sanitized value.
	 */
	public function sanitize_posttypes( $value ) {
		$post_types = is_array( $value ) ? array_map( 'sanitize_text_field', wp_unslash( $value ) ) : array();
		return implode( ',', $post_types );
	}

	/**
	 * Sanitize Post Meta array.
	 *
	 * @param array $settings Post meta settings array.
	 * @return string Sanitized value.
	 */
	public function sanitize_post_meta( $settings ) {

		// This array holds a list of keys that will be passed through our category/tags loop to determine the ids.
		$keys = array(
			'include_on_category' => array(
				'tax'       => 'category',
				'ids_field' => 'include_on_category_ids',
			),
			'include_on_post_tag' => array(
				'tax'       => 'post_tag',
				'ids_field' => 'include_on_post_tag_ids',
			),
		);

		foreach ( $keys as $key => $fields ) {
			if ( isset( $settings[ $key ] ) ) {
				$ids   = array();
				$names = array();

				$taxes = array_unique( str_getcsv( $settings[ $key ] ) );

				foreach ( $taxes as $tax ) {
					$tax_name = get_term_by( 'name', $tax, $fields['tax'] );

					if ( isset( $tax_name->term_taxonomy_id ) ) {
						$ids[]   = $tax_name->term_taxonomy_id;
						$names[] = $tax_name->name;
					}
				}
				$settings[ $fields['ids_field'] ] = isset( $ids ) ? join( ',', $ids ) : '';
				$settings[ $key ]                 = isset( $names ) ? \WebberZone\Snippetz\Util\Helpers::str_putcsv( $names ) : '';
			} else {
				$settings[ $fields['ids_field'] ] = '';
			}
		}

		return $settings;
	}
}
