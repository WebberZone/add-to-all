<?php
/**
 * Add to Feed lets you add a copyright notice and custom text or HTML to your WordPress feed.
 *
 * @package Add_to_All
 *
 * @wordpress-plugin
 * Plugin Name: Add to All
 * Version:     1.1
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
 * Holds the filesystem directory path.
 */
define( 'ALD_ATA_DIR', dirname( __FILE__ ) );

// Set the global variables for Better Search path and URL
$ata_path = plugin_dir_path( __FILE__ );
$ata_url = plugins_url() . '/' . plugin_basename( dirname( __FILE__ ) );


/**
 * Declare $ata_settings global so that it can be accessed in every function
 */
global $ata_settings;
$ata_settings = ata_read_options();


/**
 * Function to load translation files.
 */
function ata_lang_init() {
	load_plugin_textdomain( 'add-to-all', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action('init', 'ata_lang_init');


/**
 * Function to add the necessary code to `wp_footer`.
 *
 */
function ald_ata_footer() {
	global $wpdb, $post, $single, $ata_settings;

	$ata_other = stripslashes( $ata_settings['ft_other'] );
	$sc_project = stripslashes( $ata_settings['tp_sc_project'] );
	$sc_security = stripslashes( $ata_settings['tp_sc_security'] );
	$ga_uacct = stripslashes( $ata_settings['tp_ga_uacct'] );
	$ga_domain = stripslashes( $ata_settings['tp_ga_domain'] );
	$kontera_ID = stripslashes( $ata_settings['tp_kontera_ID'] );
	$kontera_linkcolor = stripslashes( $ata_settings['tp_kontera_linkcolor'] );

	// Add other footer
	if ( '' != $ata_other ) {
		echo $ata_other;
	}

	if ( '' != $sc_project ) {
?>
	<!-- Start of StatCounter Code -->
	<script type="text/javascript">
	// <![CDATA[
		var sc_project=<?php echo $sc_project; ?>;
		var sc_security="<?php echo $sc_security; ?>";
		var sc_invisible=1;
		var sc_click_stat=1;
	// ]]>
	</script>
	<script type="text/javascript" src="http://www.statcounter.com/counter/counter_xhtml.js"></script>
	<noscript><div class="statcounter"><a title="tumblr hit counter" href="http://statcounter.com/tumblr/" class="statcounter"><img class="statcounter" src="http://c.statcounter.com/<?php echo $sc_project; ?>/0/<?php echo $sc_security; ?>/1/" alt="tumblr hit counter" /></a></div></noscript>
	<!-- End of StatCounter Code // by Add to All WordPress Plugin -->
<?php	}

	if ( '' != $ga_uacct ) {
		if ( $ata_settings['tp_ga_ua'] ) {
?>

	<!-- Start Google Analytics -->
	<script>
	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	  ga('create', '<?php echo $ga_uacct; ?>', '<?php echo $ga_domain; ?>');
	  ga('send', 'pageview');

	</script>
	<!-- End Google Analytics // Added by Add to All WordPress plugin -->

<?php } else { ?>

	<!-- Start Google Analytics -->
	<script type="text/javascript">
	// <![CDATA[
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '<?php echo $ga_uacct; ?>']);
	  _gaq.push(['_setDomainName', '<?php echo $ga_domain; ?>']);
	  _gaq.push(['_setAllowLinker', true]);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	// ]]>
	</script>
	<!-- End Google Analytics // Added by Add to All WordPress plugin -->
<?php	}
	}

	if ( ( '' != $kontera_ID ) && ( '' != $kontera_linkcolor ) ) {
?>
	<!-- Kontera(TM);-->
	<script type='text/javascript'>
	// <![CDATA[
	var dc_AdLinkColor = '<?php echo $kontera_linkcolor; ?>' ;
	var dc_PublisherID = <?php echo $kontera_ID; ?> ;
	// ]]>
	</script>
	<script type='text/javascript' src='http://kona.kontera.com/javascript/lib/KonaLibInline.js'></script>
	<!-- end Kontera(TM) // by Add to All WordPress plugin -->

<?php	}

}
add_action( 'wp_footer', 'ald_ata_footer' );


/**
 * Function to wrap the post content with Kontera tags. Filters `the_content`.
 *
 * @param string $content Post content
 * @return string Filtered post content
 */
function ata_content_nofilter( $content ) {

	global $single, $ata_settings;

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
 *
 */
function ata_content_prepare_filter() {
	global $ata_settings;

    $priority = isset ( $ata_settings['content_filter_priority'] ) ? $ata_settings['content_filter_priority'] : 10;

    add_filter( 'the_content', 'ata_content', $priority );
}
add_action( 'template_redirect', 'ata_content_prepare_filter' );


/**
 * Function to add custom HTML before and after the post content. Filters `the_content`.
 *
 * @param string $content Post content
 * @return string Filtered post content
 */
function ata_content( $content ) {
	global $ata_settings;

	if ( ( is_singular() ) || ( is_home() ) || ( is_archive() ) ) {
		$str_before = '';
		$str_after = '';

		if ( is_singular() ) {
			if ( $ata_settings['content_addhtmlbefore'] ) {
				$str_before .= stripslashes( $ata_settings['content_htmlbefore'] );
			}

			if ( $ata_settings['content_addhtmlafter'] ) {
				$str_after .= stripslashes( $ata_settings['content_htmlafter'] );
			}


			if ( $ata_settings['content_addhtmlbeforeS'] ) {
				$str_before .= stripslashes( $ata_settings['content_htmlbeforeS'] );
			}

			if ( $ata_settings['content_addhtmlafterS'] ) {
				$str_after .= stripslashes( $ata_settings['content_htmlafterS'] );
			}
		} elseif ( ( is_home() ) || ( is_archive() ) ) {
			if ( $ata_settings['content_addhtmlbefore'] ) {
				$str_before .= stripslashes( $ata_settings['content_htmlbefore'] );
			}

			if ( $ata_settings['content_addhtmlafter'] ) {
				$str_after .= stripslashes( $ata_settings['content_htmlafter'] );
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
 * @param string $content Post content
 * @return string Filtered post content
 */
function ald_ata_rss( $content ) {
	global $ata_settings;

	if ( ( $ata_settings[ 'feed_addhtmlbefore' ] ) || ( $ata_settings[ 'feed_addhtmlafter' ] ) || ( $ata_settings[ 'feed_addtitle' ] ) || ( $ata_settings[ 'feed_addcopyright' ] ) || ( $ata_settings[ 'addcredit' ] ) ) {
		$str_before = '';
		$str_after = '<hr style="border-top:black solid 1px" />';

		if ( $ata_settings[ 'feed_addhtmlbefore' ] ) {
			$str_before .= stripslashes( $ata_settings[ 'feed_htmlbefore' ] );
			$str_before .= '<br />';
		}

		if ( $ata_settings[ 'feed_addhtmlafter' ] ) {
			$str_after .= stripslashes( $ata_settings[ 'feed_htmlafter' ] );
			$str_after .= '<br />';
		}

		if ( $ata_settings[ 'feed_addtitle' ] ) {
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
			$str_after .= str_replace( $search_array, $replace_array, $ata_settings['feed_titletext'] );

			$str_after .= '<br />';
		}

		if ( $ata_settings[ 'feed_addcopyright' ] ) {
			$str_after .= stripslashes( $ata_settings[ 'feed_copyrightnotice' ] );
			$str_after .= '<br />';
		}

		if ( $ata_settings[ 'addcredit' ] ) {
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
 *
 */
function ald_ata_header() {

	global $wpdb, $post, $single, $ata_settings;

	$ata_other = stripslashes( $ata_settings[ 'head_other' ] );
	$ata_head_CSS = stripslashes( $ata_settings[ 'head_CSS' ] );
	$ata_tp_tynt_id = stripslashes( $ata_settings[ 'tp_tynt_id' ] );

	// Add CSS to header
	if ( $ata_head_CSS != '' ) {
		echo '<style type="text/css">' . $ata_head_CSS . '</style>';
	}

	// Add Tynt code to Header
	if ( $ata_tp_tynt_id != '' ) {
	?>
		<!-- BEGIN Tynt Script - Inserted by Add to All WordPress Plugin -->
		<script type="text/javascript">
		if(document.location.protocol=='http:'){
		 var Tynt=Tynt||[];Tynt.push('<?php	echo $ata_tp_tynt_id; ?>');
		 (function(){var s=document.createElement('script');s.async="async";s.type="text/javascript";s.src='http://tcr.tynt.com/ti.js';var h=document.getElementsByTagName('script')[0];h.parentNode.insertBefore(s,h);})();
		}
		</script>
		<!-- END Tynt Script -->
	<?php
	}

	// Add other header
	if ( $ata_other != '' ) {
		echo $ata_other;
	}

}
add_action( 'wp_head', 'ald_ata_header' );


/**
 * Default options.
 *
 * @return return Array with default options
 */
function ata_default_options() {
	global $ata_url;
	$copyrightnotice = '&copy;' . date( "Y" ) . ' &quot;<a href="' . get_option( 'home' ) . '">' . get_option( 'blogname' ) . '</a>&quot;. ';
	$copyrightnotice .= __( 'Use of this feed is for personal non-commercial use only. If you are not reading this article in your feed reader, then the site is guilty of copyright infringement. Please contact me at ', 'ald_ata_plugin' );
	$copyrightnotice .= get_option( 'admin_email' );
	$ga_url = parse_url( get_option( 'home' ), PHP_URL_HOST );

	$titletext = __( '%title% was first posted on %date% at %time%.', 'add-to-all' );

	$ata_settings = array(
		 'addcredit' => false, // Show credits?

		// Content options
		'content_htmlbefore' => '', // HTML you want added to the content
		'content_htmlafter' => '', // HTML you want added to the content
		'content_addhtmlbefore' => false, // Add HTML to content?
		'content_addhtmlafter' => false, // Add HTML to content?
		'content_htmlbeforeS' => '', // HTML you want added to the content on single pages only
		'content_htmlafterS' => '', // HTML you want added to the content on single pages only
		'content_addhtmlbeforeS' => false, // Add HTML to content on single pages?
		'content_addhtmlafterS' => false, // Add HTML to content on single pages?
		'content_filter_priority' => 999, // Content priority

		// Feed options
		'feed_htmlbefore' => '', // HTML you want added to the feed
		'feed_htmlafter' => '', // HTML you want added to the feed
		'feed_copyrightnotice' => $copyrightnotice, // Copyright Notice
		'feed_emailaddress' => get_option( 'admin_email' ), // Admin Email
		'feed_addhtmlbefore' => false, // Add HTML to Feed?
		'feed_addhtmlafter' => false, // Add HTML to Feed?
		'feed_addtitle' => true, // Add title of the post?
		'feed_titletext' => $titletext,	// Custom text when adding a link to the post title
		'feed_addcopyright' => true, // Add copyright notice?

		// 3rd party options
		'tp_sc_project' => '', // StatCounter Project ID
		'tp_sc_security' => '', // StatCounter Security String
		'tp_ga_uacct' => '', // Google Analytics Web Property ID
		'tp_ga_domain' => $ga_url, // Google Analytics _setDomainName value
		'tp_ga_ua' => false, // Use Google Universal Analytics code
		'tp_kontera_ID' => '', // Kontera Publisher ID
		'tp_kontera_linkcolor' => '', // Kontera link color
		'tp_kontera_addZT' => '', // Kontera Add zone tags
		'tp_tynt_id' => '', // Tynt ID

		// Footer options
		'ft_other' => '', // For any other code

		// Header options
		'head_CSS' => '', // CSS to add to header (do not wrap with <style> tags)
		'head_other' => '' // For any other code

	);
	return apply_filters( 'ata_default_options', $ata_settings );
}


/**
 * Function to read options from the database and add any new ones.
 *
 * @return array Options array
 */
function ata_read_options() {
	$ata_settings_changed = false;

	$defaults = ata_default_options();

	$ata_settings = array_map('stripslashes',(array)get_option('ald_ata_settings'));
	unset($ata_settings[0]); // produced by the (array) casting when there's nothing in the DB

	foreach ($defaults as $k=>$v) {
		if (!isset($ata_settings[$k]))
			$ata_settings[$k] = $v;
		$ata_settings_changed = true;
	}
	if ($ata_settings_changed == true)
		update_option('ald_ata_settings', $ata_settings);

	return apply_filters( 'ata_read_options', $ata_settings );
}


/**
 * Function to get the post thumbnail.
 *
 * @param integer $postid Post ID
 * @return string Image tag with the post thumbnail
 */
function ata_get_the_post_thumbnail( $postid ) {

	$result = get_post( $postid );
	global $ata_settings;
	$output = '';
	$title = get_the_title( $postid );

	if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $result->ID ) ) {
		$output .= get_the_post_thumbnail( $result->ID, array(
			 $ata_settings[ 'thumb_width' ],
			$ata_settings[ 'thumb_height' ]
		), array(
			 'title' => $title,
			'alt' => $title,
			'class' => 'ata_thumb',
			'border' => '0'
		) );
	} else {
		$postimage = get_post_meta( $result->ID, $ata_settings[ 'thumb_meta' ], true ); // Check
		if ( !$postimage && $ata_settings[ 'scan_images' ] ) {
			preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $result->post_content, $matches );
			// any image there?
			if ( isset( $matches ) && $matches[ 1 ][ 0 ] ) {
				$postimage = $matches[ 1 ][ 0 ]; // we need the first one only!
			}
		}
		if ( !$postimage )
			$postimage = get_post_meta( $result->ID, '_video_thumbnail', true ); // If no other thumbnail set, try to get the custom video thumbnail set by the Video Thumbnails plugin
		if ( $ata_settings[ 'thumb_default_show' ] && !$postimage )
			$postimage = $ata_settings[ 'thumb_default' ]; // If no thumb found and settings permit, use default thumb
		if ( $postimage ) {
			$output .= '<img src="' . $postimage . '" alt="' . $title . '" title="' . $title . '" style="max-width:' . $ata_settings[ 'thumb_width' ] . 'px;max-height:' . $ata_settings[ 'thumb_height' ] . 'px; border:0;" class="ata_thumb" />';
		}
	}

	return apply_filters( 'ata_get_the_post_thumbnail', $output );
}



/**
 * Function to create an excerpt for the post.
 *
 * @param integer $id Post ID
 * @param mixed $excerpt_length Length of the excerpt in words
 * @return string The excerpt
 */
function ata_excerpt( $id, $excerpt_length = 0, $use_excerpt = true ) {
	$content = $excerpt = '';
	if ( $use_excerpt ) {
		$content = get_post( $id )->post_excerpt;
	}
	if ( '' == $content ) {
		$content = get_post( $id )->post_content;
	}

	$output = strip_tags( strip_shortcodes( $content ) );

	if ( $excerpt_length > 0 ) {
		$output = wp_trim_words( $output, $excerpt_length );
	}

	return apply_filters( 'ata_excerpt', $output, $id, $excerpt_length, $use_excerpt );
}


/**
 *  Admin options
 *
 */
if ( is_admin() || strstr( $_SERVER['PHP_SELF'], 'wp-admin/' ) ) {

	/**
	 *  Load the admin pages if we're in the Admin.
	 *
	 */
	require_once( ALD_ATA_DIR . "/admin.inc.php" );

	/**
	 * Adding WordPress plugin action links.
	 *
	 * @param array $links
	 * @return array
	 */
	function ata_plugin_actions_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=ata_options' ) . '">' . __( 'Settings', 'add-to-all' ) . '</a>'
			),
			$links
		);

	}
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'ata_plugin_actions_links' );

	/**
	 * Add meta links on Plugins page.
	 *
	 * @param array $links
	 * @param string $file
	 * @return array
	 */
	function ata_plugin_actions( $links, $file ) {
		static $plugin;
		if ( ! $plugin ) {
			$plugin = plugin_basename( __FILE__ );
		}

		// create link
		if ( $file == $plugin ) {
			$links[] = '<a href="http://wordpress.org/support/plugin/add-to-all">' . __( 'Support', 'add-to-all' ) . '</a>';
			$links[] = '<a href="http://ajaydsouza.com/donate/">' . __( 'Donate', 'add-to-all' ) . '</a>';
		}
		return $links;
	}
	add_filter( 'plugin_row_meta', 'ata_plugin_actions', 10, 2 ); // only 2.8 and higher

} // End admin.inc

?>