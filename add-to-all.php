<?php
/*
Plugin Name: Add to All
Version:     0.9
Plugin URI:  http://ajaydsouza.com/wordpress/plugins/add-to-all/
Description: A powerful plugin that will allow you to add custom code or CSS to your header, footer, sidebar, content or feed.
Author:      Ajay D'Souza
Author URI:  http://ajaydsouza.com/
*/

if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

define('ALD_ATA_DIR', dirname(__FILE__));
define('ATA_LOCAL_NAME', 'ata');

// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

// Guess the location
$ata_path = WP_PLUGIN_DIR.'/'.plugin_basename(dirname(__FILE__));
$ata_url = WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__));

function ald_ata_init() {
	//* Begin Localization Code */
	$ata_localizationName = ATA_LOCAL_NAME;
	$ata_comments_locale = get_locale();
	$ata_comments_mofile = ALD_ATA_DIR . "/languages/" . $ata_localizationName . "-". $ata_comments_locale.".mo";
	load_textdomain($ata_localizationName, $ata_comments_mofile);
	//* End Localization Code */
}
add_action('init', 'ald_ata_init');


/*********************************************************************
*				Main Function (Do not edit)							*
********************************************************************/
// Footer function
add_action('wp_footer','ald_ata_footer');
function ald_ata_footer() {
	global $wpdb, $post, $single;

	$ata_settings = ata_read_options();

	$ata_other = stripslashes($ata_settings[ft_other]);
	$sc_project = stripslashes($ata_settings[tp_sc_project]);
	$sc_security = stripslashes($ata_settings[tp_sc_security]);
	$ga_uacct = stripslashes($ata_settings[tp_ga_uacct]);
	$ga_url = stripslashes($ata_settings[tp_ga_domain]);
	$kontera_ID = stripslashes($ata_settings[tp_kontera_ID]);
	$kontera_linkcolor = stripslashes($ata_settings[tp_kontera_linkcolor]);
	
	// Add other footer 
	if ($ata_other != '') {
		echo $ata_other;
	}

	if ($sc_project != ''){
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
	<!-- End of StatCounter Code -->
<?php	}

	if ($ga_uacct != '') {
?>
	<!-- Start Google Analytics -->
	<script type="text/javascript">
	// <![CDATA[
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '<?php echo $ga_uacct; ?>']);
	  _gaq.push(['_setDomainName', '<?php echo $ga_url; ?>']);
	  _gaq.push(['_setAllowLinker', true]);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	// ]]>
	</script>
	<!-- End Google Analytics -->
<?php	}

	if (($kontera_ID != '')&&($kontera_linkcolor != '')) {
?>
	<!-- Kontera(TM);-->
	<script type='text/javascript'>
	// <![CDATA[
	var dc_AdLinkColor = '<?php echo $kontera_linkcolor; ?>' ; 
	var dc_PublisherID = <?php echo $kontera_ID; ?> ; 
	// ]]>
	</script>
	<script type='text/javascript' src='http://kona.kontera.com/javascript/lib/KonaLibInline.js'></script>
	<!-- end Kontera(TM) --> 

<?php	}
	
}

// Content function
add_filter('the_content', 'ald_ata_content');
function ald_ata_content($content) {
	
	global $single;
	$ata_settings = ata_read_options();
	$str_before = '<div class="KonaBody">';
	$str_after = '</div>';
	
    if($ata_settings['tp_kontera_addZT']) {
		if((is_singular())||(is_home())||(is_archive())) {
			return $str_before.$content.$str_after;
		} else {
			return $content;
		}
	}
}

// Feed function
add_filter('the_content', 'ald_ata',99999999);
function ald_ata($content) {
	$ata_settings = ata_read_options();
	$creditline = '<br /><span style="font-size: 0.8em">Feed enhanced by the <a href="http://ajaydsouza.com/wordpress/plugins/add-to-feed/">Add To Feed Plugin</a> by <a href="http://ajaydsouza.com/">Ajay D\'Souza</a></span>';
	
	$str_before ='';
	$str_after ='<hr style="border-top:black solid 1px" />';
	
    if(is_feed()) {
		if($ata_settings[feed_addhtmlbefore])
		{
			$str_before .= stripslashes($ata_settings[feed_htmlbefore]);
			$str_before .= '<br />';
		}
		
		if($ata_settings[feed_addhtmlafter])
		{
			$str_after .= stripslashes($ata_settings[feed_feed_htmlafter]);
			$str_after .= '<br />';
		}
		
		if($ata_settings[feed_addtitle])
		{
			$str_after .= '<a href="'.get_permalink().'">'.the_title('','',false).'</a> was first posted on '.get_the_time('F j, Y').' at '.get_the_time('g:i a').'.';
			$str_after .= '<br />';
		}
		
		if($ata_settings[feed_addcopyright])
		{
			$str_after .= stripslashes($ata_settings[feed_copyrightnotice]);
			$str_after .= '<br />';
		}
		
		if($ata_settings[addcredit])
		{
			$str_after .= $creditline;
			$str_after .= '<br />';
		}
		
        return $str_before.$content.$str_after;
    } else {
        return $content;
    }
}

// Footer function
add_action('wp_head','ald_ata_header');
function ald_ata_header() {
	global $wpdb, $post, $single;

	$ata_settings = ata_read_options();
	$ata_other = stripslashes($ata_settings[head_other]);
	$ata_head_CSS = stripslashes($ata_settings[head_CSS]);
	
	// Add CSS to header 
	if ($ata_head_CSS != '') {
		echo '<style type="text/css">'.$ata_head_CSS.'</style>';
	}
	// Add other header 
	if ($ata_other != '') {
		echo $ata_other;
	}
}
	
// Default Options
function ata_default_options() {
	global $ata_url;
	$copyrightnotice = '&copy;'. date("Y").' &quot;<a href="'.get_option('home').'">'.get_option('blogname').'</a>&quot;. ';
	$copyrightnotice .= __('Use of this feed is for personal non-commercial use only. If you are not reading this article in your feed reader, then the site is guilty of copyright infringement. Please contact me at ','ald_ata_plugin');
	$copyrightnotice .= get_option('admin_email');
	$ga_url = parse_url(get_option('home'),PHP_URL_HOST);

	$ata_settings = Array (
						addcredit => false,		// Show credits?
						
						// Feed options
						feed_htmlbefore => '',		// HTML you want added to the feed
						feed_htmlafter => '',		// HTML you want added to the feed
						feed_copyrightnotice => $copyrightnotice,		// Copyright Notice
						feed_emailaddress => get_option('admin_email'),		// Admin Email
						feed_addhtmlbefore => false,		// Add HTML to Feed?
						feed_addhtmlafter => false,		// Add HTML to Feed?
						feed_addtitle => true,		// Add title of the post?
						feed_addcopyright => true,		// Add copyright notice?
						
						// 3rd party options
						tp_sc_project => '',		// StatCounter Project ID
						tp_sc_security => '',		// StatCounter Security String
						tp_ga_uacct => '',			// Google Analytics Web Property ID
						tp_ga_domain => $ga_url,		// Google Analytics _setDomainName value
						tp_kontera_ID => '',		// Kontera Publisher ID
						tp_kontera_linkcolor => '',		// Kontera link color
						tp_kontera_addZT => '',		// Kontera Add zone tags

						// Footer options
						ft_other => '',				// For any other code

						// Header options
						head_CSS => '',				// CSS to add to header (do not wrap with <style> tags)
						head_other => '',			// For any other code

					);
	return $ata_settings;
}

// Function to read options from the database
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
	
	return $ata_settings;

}

// Create full text index
function ald_ata_activate() {
	global $wpdb;

    $wpdb->hide_errors();

    $wpdb->show_errors();
}
if (function_exists('register_activation_hook')) {
	register_activation_hook(__FILE__,'ald_ata_activate');
}

// Function to get the post thumbnail
function ata_get_the_post_thumbnail($postid) {

	$result = get_post($postid);
	$ata_settings = ata_read_options();
	$output = '';
	$title = get_the_title($postid);
	
	if (function_exists('has_post_thumbnail') && has_post_thumbnail($result->ID)) {
		$output .= get_the_post_thumbnail($result->ID, array($ata_settings[thumb_width],$ata_settings[thumb_height]), array('title' => $title,'alt' => $title, 'class' => 'ata_thumb', 'border' => '0'));
	} else {
		$postimage = get_post_meta($result->ID, $ata_settings[thumb_meta], true);	// Check
		if (!$postimage && $ata_settings['scan_images']) {
			preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $result->post_content, $matches );
			// any image there?
			if (isset($matches) && $matches[1][0]) {
				$postimage = $matches[1][0]; // we need the first one only!
			}
		}
		if (!$postimage) $postimage = get_post_meta($result->ID, '_video_thumbnail', true); // If no other thumbnail set, try to get the custom video thumbnail set by the Video Thumbnails plugin
		if ($ata_settings['thumb_default_show'] && !$postimage) $postimage = $ata_settings[thumb_default]; // If no thumb found and settings permit, use default thumb
		if ($postimage) {
		  $output .= '<img src="'.$postimage.'" alt="'.$title.'" title="'.$title.'" style="max-width:'.$ata_settings[thumb_width].'px;max-height:'.$ata_settings[thumb_height].'px;" border="0" class="ata_thumb" />';
		}
	}
	
	return $output;
}

// Function to create an excerpt for the post
function ata_excerpt($id,$excerpt_length){
	$content = get_post($id)->post_content;
	$out = strip_tags($content);
	$blah = explode(' ',$out);
	if (!$excerpt_length) $excerpt_length = 10;
	if(count($blah) > $excerpt_length){
		$k = $excerpt_length;
		$use_dotdotdot = 1;
	}else{
		$k = count($blah);
		$use_dotdotdot = 0;
	}
	$excerpt = '';
	for($i=0; $i<$k; $i++){
		$excerpt .= $blah[$i].' ';
	}
	$excerpt .= ($use_dotdotdot) ? '...' : '';
	$out = $excerpt;
	return $out;
}

// This function adds an Options page in WP Admin
if (is_admin() || strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
	require_once(ALD_ATA_DIR . "/admin.inc.php");

// Add meta links
function ata_plugin_actions( $links, $file ) {
	static $plugin;
	if (!$plugin) $plugin = plugin_basename(__FILE__);
 
	// create link
	if ($file == $plugin) {
		$links[] = '<a href="' . admin_url( 'options-general.php?page=ata_options' ) . '">' . __('Settings', ATA_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://ajaydsouza.com/support/">' . __('Support', ATA_LOCAL_NAME ) . '</a>';
		$links[] = '<a href="http://ajaydsouza.com/donate/">' . __('Donate', ATA_LOCAL_NAME ) . '</a>';
	}
	return $links;
}
global $wp_version;
if ( version_compare( $wp_version, '2.8alpha', '>' ) )
	add_filter( 'plugin_row_meta', 'ata_plugin_actions', 10, 2 ); // only 2.8 and higher
else add_filter( 'plugin_action_links', 'ata_plugin_actions', 10, 2 );


} // End admin.inc

?>