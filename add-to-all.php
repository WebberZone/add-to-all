<?php
/**
 * Add to Feed lets you add a copyright notice and custom text or HTML to your WordPress feed.
 *
 * @package Add_to_All
 *
 * @wordpress-plugin
 * Plugin Name: Add to All
 * Version:     1.2-beta20161022
 * Plugin URI:  http://ajaydsouza.com/wordpress/plugins/add-to-all/
 * Description: A powerful plugin that will allow you to add custom code or CSS to your header, footer, sidebar, content or feed.
 * Author:      Ajay D'Souza
 * Author URI:  http://ajaydsouza.com/
 * Text Domain:	add-to-all
 * License:		GPL-2.0+
 * License URI:	http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:	/languages
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
global $ata_settings
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
 * Function to add the necessary code to `wp_footer`.
 */
function ald_ata_footer() {
	global $ata_settings;

	$ata_other = stripslashes( $ata_settings['footer_other_html'] );
	$sc_project = stripslashes( $ata_settings['sc_project'] );
	$sc_security = stripslashes( $ata_settings['sc_security'] );
	$ga_uacct = stripslashes( $ata_settings['ga_uacct'] );

	// Add other footer.
	if ( '' !== $ata_other ) {
		echo $ata_other; // WPCS: XSS OK.
	}

	// Add Statcounter code.
	if ( '' !== $sc_project ) {
?>
	<!-- Start of StatCounter Code -->
	<script type="text/javascript">
	// <![CDATA[
		var sc_project=<?php echo esc_attr( $sc_project ); ?>;
		var sc_security="<?php echo esc_attr( $sc_security ); ?>";
		var sc_invisible=1;
		var sc_click_stat=1;
	// ]]>
	</script>
	<script type="text/javascript" src="https://secure.statcounter.com/counter/counter.js"></script>
	<noscript><div class="statcounter"><a title="WordPress hit counter" href="https://statcounter.com/wordpress.org/" class="statcounter"><img class="statcounter" src="//c.statcounter.com/<?php echo esc_attr( $sc_project ); ?>/0/<?php echo esc_attr( $sc_security ); ?>/1/" alt="WordPress hit counter" /></a></div></noscript>
	<!-- End of StatCounter Code // by Add to All WordPress Plugin -->
<?php	}

	if ( '' !== $ga_uacct ) {
?>

	<!-- Start Google Analytics -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

	  ga('create', '<?php echo esc_attr( $ga_uacct ); ?>', 'auto');
	  ga('send', 'pageview');

	</script>
	<!-- End Google Analytics // Added by Add to All WordPress plugin -->

<?php }

}
add_action( 'wp_footer', 'ald_ata_footer' );


/**
 * Function to wrap the post content with Kontera tags. Filters `the_content`.
 *
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ata_content_nofilter( $content ) {

	global $ata_settings;

	if ( $ata_settings['tp_kontera_addZT'] ) {
		$str_before = '<div class="KonaBody">';
		$str_after = '</div>';
		if ( ( is_singular() ) || ( is_home() ) || ( is_archive() ) ) {
			return $str_before . $content . $str_after;
		} else {
			return $content;
		}
	} else {
			return $content;
	}
}
add_filter( 'the_content', 'ata_content_nofilter' );


/**
 * Function to modify the_content filter priority.
 */
function ata_content_prepare_filter() {
	global $ata_settings;

	$priority = isset( $ata_settings['content_filter_priority'] ) ? $ata_settings['content_filter_priority'] : 10;

	add_filter( 'the_content', 'ata_content', $priority );
}
add_action( 'template_redirect', 'ata_content_prepare_filter' );


/**
 * Function to add custom HTML before and after the post content. Filters `the_content`.
 *
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ata_content( $content ) {
	global $ata_settings;

	if ( ( is_singular() ) || ( is_home() ) || ( is_archive() ) ) {
		$str_before = '';
		$str_after = '';

		if ( is_singular() ) {
			if ( $ata_settings['content_add_html_before'] ) {
				$str_before .= stripslashes( $ata_settings['content_html_before'] );
			}

			if ( $ata_settings['content_add_html_after'] ) {
				$str_after .= stripslashes( $ata_settings['content_html_after'] );
			}

			if ( $ata_settings['content_add_html_before_single'] ) {
				$str_before .= stripslashes( $ata_settings['content_html_before_single'] );
			}

			if ( $ata_settings['content_add_html_after_single'] ) {
				$str_after .= stripslashes( $ata_settings['content_html_after_single'] );
			}
		} elseif ( ( is_home() ) || ( is_archive() ) ) {
			if ( $ata_settings['content_add_html_before'] ) {
				$str_before .= stripslashes( $ata_settings['content_html_before'] );
			}

			if ( $ata_settings['content_add_html_after'] ) {
				$str_after .= stripslashes( $ata_settings['content_html_after'] );
			}
		}

	    return $str_before . $content . $str_after;
	} else {
		return $content;
	}

}


/**
 * Function to add content to RSS feeds. Filters `the_excerpt_rss` and `the_content_feed`.
 *
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ald_ata_rss( $content ) {
	global $ata_settings;

	if ( ( $ata_settings['feed_add_html_before'] ) || ( $ata_settings['feed_add_html_after'] ) || ( $ata_settings['feed_add_title'] ) || ( $ata_settings['feed_add_copyright'] ) || ( $ata_settings['add_credit'] ) ) {
		$str_before = '';
		$str_after = '<hr style="border-top:black solid 1px" />';

		if ( $ata_settings['feed_add_html_before'] ) {
			$str_before .= stripslashes( $ata_settings['feed_html_before'] );
			$str_before .= '<br />';
		}

		if ( $ata_settings['feed_add_html_after'] ) {
			$str_after .= stripslashes( $ata_settings['feed_html_after'] );
			$str_after .= '<br />';
		}

		if ( $ata_settings['feed_add_title'] ) {
			$title = '<a href="' . get_permalink() . '">' . the_title( '', '', false ) . '</a>';
			$search_array = array(
				'%title%',
				'%date%',
				'%time%',
			);
			$replace_array = array(
				$title,
				get_the_time( 'F j, Y' ),
				get_the_time( 'g:i a' ),
			);
			$str_after .= str_replace( $search_array, $replace_array, $ata_settings['feed_title_text'] );

			$str_after .= '<br />';
		}

		if ( $ata_settings['feed_add_copyright'] ) {
			$str_after .= stripslashes( $ata_settings['feed_copyrightnotice'] );
			$str_after .= '<br />';
		}

		if ( $ata_settings['add_credit'] ) {
			$creditline = '<br /><span style="font-size: 0.8em">';
			$creditline .= __( 'Feed enhanced by ', 'add-to-all' );
			$creditline .= '<a href="http://ajaydsouza.com/wordpress/plugins/add-to-all/" rel="nofollow">Add To All</a>';

			$str_after .= $creditline;
			$str_after .= '<br />';
		}

		return $str_before . $content . $str_after;
	} else {
		return $content;
	}

}
add_filter( 'the_excerpt_rss', 'ald_ata_rss', 99999999 );
add_filter( 'the_content_feed', 'ald_ata_rss', 99999999 );


/**
 * Function to add custom code to the header. Filters `wp_head`.
 */
function ald_ata_header() {

	global $ata_settings;

	$ata_other = stripslashes( $ata_settings['head_other_html'] );
	$ata_head_css = stripslashes( $ata_settings['head_css'] );
	$ata_tynt_id = stripslashes( $ata_settings['tynt_id'] );

	// Add CSS to header.
	if ( '' !== $ata_head_css ) {
		echo '<style type="text/css">' . $ata_head_css . '</style>'; // WPCS: XSS OK.
	}

	// Add Tynt code to Header.
	if ( '' !== $ata_tynt_id ) {
	?>

	<!-- Begin 33Across SiteCTRL - Inserted by Add to All WordPress Plugin -->
	<script>
	var Tynt=Tynt||[];Tynt.push('<?php	echo esc_attr( $ata_tynt_id ); ?>');
	(function(){var h,s=document.createElement('script');
	s.src=(window.location.protocol==='https:'?
	'https':'http')+'://cdn.tynt.com/ti.js';
	h=document.getElementsByTagName('script')[0];
	h.parentNode.insertBefore(s,h);})();
	</script>
	<!-- End 33Across SiteCTRL - Inserted by Add to All WordPress Plugin -->

	<?php
	}

	// Add other header.
	if ( '' !== $ata_other ) {
		echo $ata_other; // WPCS: XSS OK.
	}

}
add_action( 'wp_head', 'ald_ata_header' );


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
	$title = get_the_title( $postid );

	if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $result->ID ) ) {
		$output .= get_the_post_thumbnail(
			$result->ID,
			array(
				$ata_settings['thumb_width'],
				$ata_settings['thumb_height'],
			),
			array(
				'title' => $title,
				'alt' => $title,
				'class' => 'ata_thumb',
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
		}		if ( $ata_settings['thumb_default_show'] && ! $postimage ) {
			// If no thumb found and settings permit, use default thumb.
			$postimage = $ata_settings['thumb_default'];
		}		if ( $postimage ) {
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
 ----------------------------------------------------------------------------*
 * Include files
 *----------------------------------------------------------------------------*/

	require_once ATA_PLUGIN_DIR . 'includes/admin/register-settings.php';


/*
 ----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	require_once ATA_PLUGIN_DIR . 'includes/admin/admin.php';
	require_once ATA_PLUGIN_DIR . 'includes/admin/settings-page.php';
	require_once ATA_PLUGIN_DIR . 'includes/admin/save-settings.php';
	require_once ATA_PLUGIN_DIR . 'includes/admin/help-tab.php';

}

/*
 ----------------------------------------------------------------------------*
 * Deprecated functions, variables and constants
 *----------------------------------------------------------------------------*/

	require_once ATA_PLUGIN_DIR . '/includes/deprecated.php';

