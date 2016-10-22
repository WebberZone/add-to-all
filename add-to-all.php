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
global $ata_settings;
$ata_settings = ata_read_options();


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

	$ata_other = stripslashes( $ata_settings['ft_other'] );
	$sc_project = stripslashes( $ata_settings['tp_sc_project'] );
	$sc_security = stripslashes( $ata_settings['tp_sc_security'] );
	$ga_uacct = stripslashes( $ata_settings['tp_ga_uacct'] );
	$ga_domain = stripslashes( $ata_settings['tp_ga_domain'] );
	$kontera_id = stripslashes( $ata_settings['tp_kontera_ID'] );
	$kontera_linkcolor = stripslashes( $ata_settings['tp_kontera_linkcolor'] );

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

	if ( ( '' !== $kontera_id ) && ( '' !== $kontera_linkcolor ) ) {
?>
	<!-- Kontera(TM);-->
	<script type='text/javascript'>
	// <![CDATA[
	var dc_AdLinkColor = '<?php echo esc_attr( $kontera_linkcolor ); ?>' ;
	var dc_PublisherID = <?php echo esc_attr( $kontera_id ); ?> ;
	// ]]>
	</script>
	<script type='text/javascript' src='///kona.kontera.com/javascript/lib/KonaLibInline.js'></script>
	<!-- end Kontera(TM) // by Add to All WordPress plugin -->

<?php	}

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
 * @param string $content Post content.
 * @return string Filtered post content
 */
function ald_ata_rss( $content ) {
	global $ata_settings;

	if ( ( $ata_settings['feed_addhtmlbefore'] ) || ( $ata_settings['feed_addhtmlafter'] ) || ( $ata_settings['feed_addtitle'] ) || ( $ata_settings['feed_addcopyright'] ) || ( $ata_settings['addcredit'] ) ) {
		$str_before = '';
		$str_after = '<hr style="border-top:black solid 1px" />';

		if ( $ata_settings['feed_addhtmlbefore'] ) {
			$str_before .= stripslashes( $ata_settings['feed_htmlbefore'] );
			$str_before .= '<br />';
		}

		if ( $ata_settings['feed_addhtmlafter'] ) {
			$str_after .= stripslashes( $ata_settings['feed_htmlafter'] );
			$str_after .= '<br />';
		}

		if ( $ata_settings['feed_addtitle'] ) {
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

		if ( $ata_settings['feed_addcopyright'] ) {
			$str_after .= stripslashes( $ata_settings['feed_copyrightnotice'] );
			$str_after .= '<br />';
		}

		if ( $ata_settings['addcredit'] ) {
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

	$ata_other = stripslashes( $ata_settings['head_other'] );
	$ata_head_css = stripslashes( $ata_settings['head_CSS'] );
	$ata_tp_tynt_id = stripslashes( $ata_settings['tp_tynt_id'] );

	// Add CSS to header.
	if ( '' !== $ata_head_css ) {
		echo '<style type="text/css">' . $ata_head_css . '</style>'; // WPCS: XSS OK.
	}

	// Add Tynt code to Header.
	if ( '' !== $ata_tp_tynt_id ) {
	?>

	<!-- Begin 33Across SiteCTRL - Inserted by Add to All WordPress Plugin -->
	<script>
	var Tynt=Tynt||[];Tynt.push('<?php	echo esc_attr( $ata_tp_tynt_id ); ?>');
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
 * Default options.
 *
 * @return return Array with default options
 */
function ata_default_options() {

	$copyrightnotice = '&copy;' . date( 'Y' ) . ' &quot;<a href="' . get_option( 'home' ) . '">' . get_option( 'blogname' ) . '</a>&quot;. ';
	$copyrightnotice .= __( 'Use of this feed is for personal non-commercial use only. If you are not reading this article in your feed reader, then the site is guilty of copyright infringement. Please contact me at ', 'ald_ata_plugin' );
	$copyrightnotice .= get_option( 'admin_email' );
	$ga_url = wp_parse_url( get_option( 'home' ), PHP_URL_HOST );

	$titletext = __( '%title% was first posted on %date% at %time%.', 'add-to-all' );

	$ata_settings = array(
		 'addcredit' => false, // Show credits?

		// Content options.
		'content_htmlbefore' => '', // HTML you want added to the content.
		'content_htmlafter' => '', // HTML you want added to the content.
		'content_addhtmlbefore' => false, // Add HTML to content?
		'content_addhtmlafter' => false, // Add HTML to content?
		'content_htmlbeforeS' => '', // HTML you want added to the content on single pages only.
		'content_htmlafterS' => '', // HTML you want added to the content on single pages only.
		'content_addhtmlbeforeS' => false, // Add HTML to content on single pages?
		'content_addhtmlafterS' => false, // Add HTML to content on single pages?
		'content_filter_priority' => 999, // Content priority.

		// Feed options.
		'feed_htmlbefore' => '', // HTML you want added to the feed.
		'feed_htmlafter' => '', // HTML you want added to the feed.
		'feed_copyrightnotice' => $copyrightnotice, // Copyright Notice.
		'feed_emailaddress' => get_option( 'admin_email' ), // Admin Email.
		'feed_addhtmlbefore' => false, // Add HTML to Feed?
		'feed_addhtmlafter' => false, // Add HTML to Feed?
		'feed_addtitle' => true, // Add title of the post?
		'feed_titletext' => $titletext,	// Custom text when adding a link to the post title.
		'feed_addcopyright' => true, // Add copyright notice?

		// 3rd party options.
		'tp_sc_project' => '', // StatCounter Project ID.
		'tp_sc_security' => '', // StatCounter Security String.
		'tp_ga_uacct' => '', // Google Analytics Web Property ID.
		'tp_ga_domain' => $ga_url, // Google Analytics _setDomainName value.
		'tp_ga_ua' => false, // Use Google Universal Analytics code.
		'tp_kontera_ID' => '', // Kontera Publisher ID.
		'tp_kontera_linkcolor' => '', // Kontera link color.
		'tp_kontera_addZT' => '', // Kontera Add zone tags.
		'tp_tynt_id' => '', // Tynt ID.

		// Footer options.
		'ft_other' => '', // For any other code.

		// Header options.
		'head_CSS' => '', // CSS to add to header (do not wrap with <style> tags).
		'head_other' => '',// For any other code.

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

	$ata_settings = array_map( 'stripslashes', (array) get_option( 'ald_ata_settings' ) );
	unset( $ata_settings[0] ); // Produced by the (array) casting when there's nothing in the DB.

	foreach ( $defaults as $k => $v ) {
		if ( ! isset( $ata_settings[ $k ] ) ) {
			$ata_settings[ $k ] = $v; }
		$ata_settings_changed = true;
	}
	if ( true === $ata_settings_changed ) {
		update_option( 'ald_ata_settings', $ata_settings ); }

	return apply_filters( 'ata_read_options', $ata_settings );
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


/**
 *  Admin options
 */
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	/**
	 *  Load the admin pages if we're in the Admin.
	 */
	require_once( ATA_PLUGIN_DIR . '/admin.inc.php' );

}

