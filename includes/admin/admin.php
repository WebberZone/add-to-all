<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://ajaydsouza.com
 * @since 1.2.0
 *
 * @package    Add_to_All
 * @subpackage Admin
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Creates the admin submenu pages under the Downloads menu and assigns their
 * links to global variables
 *
 * @since 1.2.0
 *
 * @global $ata_settings_page
 * @return void
 */
function ata_add_admin_pages_links() {
	global $ata_settings_page;

	$ata_settings_page = add_options_page( __( 'Add to All', 'add-to-all' ), __( 'Add to All', 'add-to-all' ), 'manage_options', 'ata_options_page', 'ata_options_page' );

	// Load the admin head.
	add_action( "admin_head-$ata_settings_page", 'ata_adminhead' );

	// Load the settings contextual help.
	add_action( "load-$ata_settings_page", 'ata_settings_help' );
}
add_action( 'admin_menu', 'ata_add_admin_pages_links' );


/**
 * Function to add CSS and JS to the Admin header.
 *
 * @since 1.3.0
 */
function ata_adminhead() {

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );
	wp_enqueue_script( 'jquery-ui-tabs' );
?>
	<script type="text/javascript">
	//<![CDATA[
		// Function to clear the cache.
		function clearCache() {
			jQuery.post(ajaxurl, {
				action: 'ata_clear_cache'
			}, function (response, textStatus, jqXHR) {
				alert(response.message);
			}, 'json');
		}

		// Function to add auto suggest.
		jQuery(document).ready(function($) {
			$.fn.ataTagsSuggest = function( options ) {

				var cache;
				var last;
				var $element = $( this );

				var taxonomy = $element.attr( 'data-wp-taxonomy' ) || 'category';

				function split( val ) {
					return val.split( /,\s*/ );
				}

				function extractLast( term ) {
					return split( term ).pop();
				}

				$element.on( "keydown", function( event ) {
						// Don't navigate away from the field on tab when selecting an item.
						if ( event.keyCode === $.ui.keyCode.TAB &&
						$( this ).autocomplete( 'instance' ).menu.active ) {
							event.preventDefault();
						}
					})
					.autocomplete({
						minLength: 2,
						source: function( request, response ) {
							var term;

							if ( last === request.term ) {
								response( cache );
								return;
							}

							term = extractLast( request.term );

							if ( last === request.term ) {
								response( cache );
								return;
							}

							$.ajax({
								type: 'POST',
								dataType: 'json',
								url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
								data: {
									action: 'ata_tag_search',
									tax: taxonomy,
									q: term
								},
								success: function( data ) {
									cache = data;

									response( data );
								}
							});

							last = request.term;

						},
						search: function() {
							// Custom minLength.
							var term = extractLast( this.value );

							if ( term.length < 2 ) {
								return false;
							}
						},
						focus: function( event, ui ) {
							// Prevent value inserted on focus.
							event.preventDefault();
						},
						select: function( event, ui ) {
							var terms = split( this.value );

							// Remove the last user input.
							terms.pop();

							// Add the selected item.
							terms.push( ui.item.value );

							// Add placeholder to get the comma-and-space at the end.
							terms.push( "" );
							this.value = terms.join( ", " );
							return false;
						}
					});

			};

			$( '.category_autocomplete' ).each( function ( i, element ) {
				$( element ).ataTagsSuggest();
			});

			// Prompt the user when they leave the page without saving the form.
			formmodified=0;

			$('form *').change(function(){
				formmodified=1;
			});

			window.onbeforeunload = confirmExit;

			function confirmExit() {
				if (formmodified == 1) {
					return "<?php esc_html__( 'New information not saved. Do you wish to leave the page?', 'where-did-they-go-from-here' ); ?>";
				}
			}

			$( "input[name='submit']" ).click( function() {
				formmodified = 0;
			});

			$( function() {
				$( "#post-body-content" ).tabs({
					create: function( event, ui ) {
						$( ui.tab.find("a") ).addClass( "nav-tab-active" );
					},
					activate: function( event, ui ) {
						$( ui.oldTab.find("a") ).removeClass( "nav-tab-active" );
						$( ui.newTab.find("a") ).addClass( "nav-tab-active" );
					}
				});
			});

		});

	//]]>
	</script>
<?php
}


/**
 * Add rating links to the admin dashboard
 *
 * @since 1.2.0
 *
 * @param string $footer_text The existing footer text.
 * @return string Updated Footer text
 */
function ata_admin_footer( $footer_text ) {

	global $ata_settings_page;

	if ( get_current_screen()->id === $ata_settings_page ) {

		$text = sprintf(
			/* translators: 1: Plugin page link, 2: Review link. */
			__( 'Thank you for using <a href="%1$s" target="_blank">Add to All</a>! Please <a href="%2$s" target="_blank">rate us</a> on <a href="%2$s" target="_blank">WordPress.org</a>', 'add-to-all' ),
			'https://ajaydsouza.com/wordpress/plugins/add-to-all',
			'https://wordpress.org/support/plugin/add-to-all/reviews/#new-post'
		);

		return str_replace( '</span>', '', $footer_text ) . ' | ' . $text . '</span>';

	} else {

		return $footer_text;

	}
}
add_filter( 'admin_footer_text', 'ata_admin_footer' );


/**
 * Adding WordPress plugin action links.
 *
 * @param array $links Array of links.
 * @return array
 */
function ata_plugin_actions_links( $links ) {

	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=ata_options_page' ) . '">' . esc_html__( 'Settings', 'add-to-all' ) . '</a>',
		),
		$links
	);

}
add_filter( 'plugin_action_links_' . plugin_basename( ATA_PLUGIN_FILE ), 'ata_plugin_actions_links' );


/**
 * Add meta links on Plugins page.
 *
 * @param array  $links Array of Links.
 * @param string $file Current file.
 * @return array
 */
function ata_plugin_actions( $links, $file ) {

	if ( false !== strpos( $file, 'add-to-all.php' ) ) {
		$links[] = '<a href="http://wordpress.org/support/plugin/add-to-all">' . esc_html__( 'Support', 'add-to-all' ) . '</a>';
		$links[] = '<a href="https://ajaydsouza.com/donate/">' . esc_html__( 'Donate', 'add-to-all' ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'ata_plugin_actions', 10, 2 );

