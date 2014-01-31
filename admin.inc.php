<?php
/**********************************************************************
*					Admin Page										*
*********************************************************************/
if (!defined('ABSPATH')) die("Aren't you supposed to come here via WP-Admin?");

function ata_options() {
	
	global $wpdb;
    $poststable = $wpdb->posts;

	$ata_settings = ata_read_options();

	if (isset($_POST['ata_save'])) {

		$ata_settings['addcredit'] = ((isset($_POST['addcredit'])) ? true : false);

		// Save Header related options
		$ata_settings['head_CSS'] = ($_POST['head_CSS']);
		$ata_settings['head_other'] = ($_POST['head_other']);

		// Save Content related options
		$ata_settings['content_htmlbefore'] = ($_POST['content_htmlbefore']);
		$ata_settings['content_htmlafter'] = ($_POST['content_htmlafter']);
		$ata_settings['content_addhtmlbefore'] = ((isset($_POST['content_addhtmlbefore'])) ? true : false);
		$ata_settings['content_addhtmlafter'] = ((isset($_POST['content_addhtmlafter'])) ? true : false);
		$ata_settings['content_htmlbeforeS'] = ($_POST['content_htmlbeforeS']);
		$ata_settings['content_htmlafterS'] = ($_POST['content_htmlafterS']);
		$ata_settings['content_addhtmlbeforeS'] = ((isset($_POST['content_addhtmlbeforeS'])) ? true : false);
		$ata_settings['content_addhtmlafterS'] = ((isset($_POST['content_addhtmlafterS'])) ? true : false);
		$ata_settings['content_filter_priority'] = intval($_POST['content_filter_priority']);

		// Save Feed related options
		$ata_settings['feed_htmlbefore'] = ($_POST['feed_htmlbefore']);
		$ata_settings['feed_htmlafter'] = ($_POST['feed_htmlafter']);
		$ata_settings['feed_copyrightnotice'] = ($_POST['feed_copyrightnotice']);
		$ata_settings['feed_addhtmlbefore'] = ((isset($_POST['feed_addhtmlbefore'])) ? true : false);
		$ata_settings['feed_addhtmlafter'] = ((isset($_POST['feed_addhtmlafter'])) ? true : false);
		$ata_settings['feed_addtitle'] = ((isset($_POST['feed_addtitle'])) ? true : false);
		$ata_settings['feed_addcopyright'] = ((isset($_POST['feed_addcopyright'])) ? true : false);

		// Save Footer related options
		$ata_settings['ft_other'] = ($_POST['ft_other']);

		// 3rd party options
		$ata_settings['tp_sc_project'] = ($_POST['tp_sc_project']);
		$ata_settings['tp_sc_security'] = ($_POST['tp_sc_security']);
		$ata_settings['tp_ga_uacct'] = ($_POST['tp_ga_uacct']);
		$ata_settings['tp_ga_domain'] = ($_POST['tp_ga_domain']);
		$ata_settings['tp_kontera_ID'] = ($_POST['tp_kontera_ID']);
		$ata_settings['tp_kontera_linkcolor'] = ($_POST['tp_kontera_linkcolor']);
<<<<<<< HEAD
		$ata_settings['tp_kontera_addZT'] = (($_POST['tp_kontera_addZT']) ? true : false);
=======
		$ata_settings['tp_kontera_addZT'] = ((isset($_POST['tp_kontera_addZT'])) ? true : false);
>>>>>>> work

		update_option('ald_ata_settings', $ata_settings);
		
		$str = '<div id="message" class="updated fade"><p>'. __('Options saved successfully.',ATA_LOCAL_NAME) .'</p></div>';
		echo $str;
	}
	
	if (isset($_POST['ata_default'])) {
		delete_option('ald_ata_settings');
		$ata_settings = ata_default_options();
		update_option('ald_ata_settings', $ata_settings);
		
		$str = '<div id="message" class="updated fade"><p>'. __('Options set to Default.',ATA_LOCAL_NAME) .'</p></div>';
		echo $str;
	}

?>

<div class="wrap">
	<div id="page-wrap">
	<div id="inside">
		<div id="header">
		<h2>Add to All</h2>
		</div>
	  <div id="side">
		<div class="side-widget">
			<span class="title"><?php _e('Support the development',ATA_LOCAL_NAME) ?></span>
			<div id="donate-form">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="donate@ajaydsouza.com">
				<input type="hidden" name="lc" value="IN">
				<input type="hidden" name="item_name" value="Donation for Add to All">
				<input type="hidden" name="item_number" value="ata">
				<strong><?php _e('Enter amount in USD: ',ATA_LOCAL_NAME) ?></strong> <input name="amount" value="10.00" size="6" type="text"><br />
				<input type="hidden" name="currency_code" value="USD">
				<input type="hidden" name="button_subtype" value="services">
				<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php _e('Send your donation to the author of',ATA_LOCAL_NAME) ?> Add to All?">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
		</div>
		<div class="side-widget">
		<span class="title"><?php _e('Quick links') ?></span>				
		<ul>
			<li><a href="http://ajaydsouza.com/wordpress/plugins/add-to-all/"><?php _e('Add to All ');_e('plugin page',ATA_LOCAL_NAME) ?></a></li>
			<li><a href="http://ajaydsouza.com/wordpress/plugins/"><?php _e('Other plugins',ATA_LOCAL_NAME) ?></a></li>
			<li><a href="http://ajaydsouza.com/"><?php _e('Ajay\'s blog',ATA_LOCAL_NAME) ?></a></li>
			<li><a href="http://wordpress.org/support/plugin/add-to-all"><?php _e('Support',ATA_LOCAL_NAME) ?></a></li>
			<li><a href="http://twitter.com/ajaydsouza"><?php _e('Follow @ajaydsouza on Twitter',ATA_LOCAL_NAME) ?></a></li>
		</ul>
		</div>
		<div class="side-widget">
		<span class="title"><?php _e('Recent developments',ATA_LOCAL_NAME) ?></span>				
		<?php require_once(ABSPATH . WPINC . '/class-simplepie.php'); wp_widget_rss_output('http://ajaydsouza.com/archives/category/wordpress/plugins/feed/', array('items' => 5, 'show_author' => 0, 'show_date' => 1));
		?>
		</div>
	  </div>

	  <div id="options-div">
	  <form method="post" id="ata_options" name="ata_options" onsubmit="return checkForm()">
		<fieldset class="options">
		<div class="tabber">
			<div class="tabbertab">
			<h3>
			  <?php _e('Options for 3rd party services',ATA_LOCAL_NAME); ?>
			</h3>
			  <table class="form-table">
				<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e('Statcounter Options:',ATA_LOCAL_NAME); ?></th>
				</tr>
				<tr style="vertical-align: top;"><th scope="row"><label for="tp_sc_project"><?php _e('StatCounter Project ID (Value of sc_project):',ATA_LOCAL_NAME); ?></label></th>
				<td><input type="textbox" name="tp_sc_project" id="tp_sc_project" value="<?php echo esc_attr(stripslashes($ata_settings['tp_sc_project'])); ?>" style="width:250px" /></td>
				</tr>
				<tr style="vertical-align: top;"><th scope="row"><label for="tp_sc_security"><?php _e('StatCounter Security ID (Value of sc_security):',ATA_LOCAL_NAME); ?></label></th>
				<td><input type="textbox" name="tp_sc_security" id="tp_sc_security" value="<?php echo esc_attr(stripslashes($ata_settings['tp_sc_security'])); ?>" style="width:250px" /></td>
				</tr>
				<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e('Google Analytics Options:',ATA_LOCAL_NAME); ?></th>
				</tr>
				<tr style="vertical-align: top;"><th scope="row"><label for="tp_ga_uacct"><?php _e('Tracking ID:',ATA_LOCAL_NAME); ?></label></th>
				<td><input type="textbox" name="tp_ga_uacct" id="tp_ga_uacct" value="<?php echo esc_attr(stripslashes($ata_settings['tp_ga_uacct'])); ?>" style="width:250px" /></td>
				</tr>
				<tr style="vertical-align: top;"><th scope="row"><label for="tp_ga_domain"><?php _e('Multiple sub-domain and top-level domain support (Value of _setDomainName):',ATA_LOCAL_NAME); ?></label></th>
				<td><input type="textbox" name="tp_ga_domain" id="tp_ga_domain" value="<?php echo esc_attr(stripslashes($ata_settings['tp_ga_domain'])); ?>" style="width:250px" /> 
				    <a href="https://support.google.com/analytics/bin/answer.py?hl=en-GB&utm_id=ad&answer=1034342" target="_blank"><?php _e('View Explanation',ATA_LOCAL_NAME); ?></a></td>
				</tr>
				<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e('Kontera Options:',ATA_LOCAL_NAME); ?></th>
				</tr>
				<tr style="vertical-align: top;"><th scope="row"><label for="tp_kontera_ID"><?php _e('Kontera ID (Value of dc_PublisherID):',ATA_LOCAL_NAME); ?></label></th>
				<td><input type="textbox" name="tp_kontera_ID" id="tp_kontera_ID" value="<?php echo esc_attr(stripslashes($ata_settings['tp_kontera_ID'])); ?>" style="width:250px" /></td>
				</tr>
				<tr style="vertical-align: top;"><th scope="row"><label for="tp_kontera_linkcolor"><?php _e('Kontera link colour (Value of dc_AdLinkColor):',ATA_LOCAL_NAME); ?></label></th>
				<td><input type="textbox" name="tp_kontera_linkcolor" id="tp_kontera_linkcolor" value="<?php echo esc_attr(stripslashes($ata_settings['tp_kontera_linkcolor'])); ?>" style="width:250px" /></td>
				</tr>
				<tr style="vertical-align: top;"><th scope="row"><label for="tp_kontera_addZT"><?php _e('Wrap post content with Kontera Zone Tags:',ATA_LOCAL_NAME); ?></label></th>
				<td><input type="checkbox" name="tp_kontera_addZT" id="tp_kontera_addZT" <?php if ($ata_settings['tp_kontera_addZT']) echo 'checked="checked"' ?> /></td>
				</tr>
			  </table>
			</div>
			<div class="tabbertab">
			<h3>
			  <?php _e('Header options',ATA_LOCAL_NAME); ?>
			</h3>
			  <table class="form-table">
				<tr style="vertical-align: top; "><th scope="row" colspan="2"><?php _e('Custom CSS to add to header:',ATA_LOCAL_NAME); ?></th>
				</tr>
				<tr style="vertical-align: top; "><td scope="row" colspan="2"><textarea name="head_CSS" id="head_CSS" rows="15" cols="80"><?php echo stripslashes($ata_settings['head_CSS']); ?></textarea></td>
				<br /><em><?php _e('Do not include <code>style</code> tags',ATA_LOCAL_NAME); ?></em></tr>
				<tr style="vertical-align: top; "><th scope="row" colspan="2"><?php _e('Any other HTML (no PHP) to add to <code>wp_head</code>:',ATA_LOCAL_NAME); ?></th>
				</tr>
				<tr style="vertical-align: top; "><td scope="row" colspan="2"><textarea name="head_other" id="head_other" rows="15" cols="80"><?php echo stripslashes($ata_settings['head_other']); ?></textarea></td>
				</tr>
			  </table>		
			</div>
			<div class="tabbertab">
			<h3>
			  <?php _e('Content options',ATA_LOCAL_NAME); ?>
			</h3>
			  <table class="form-table">
				<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e('Content to add to all posts before and/or after post content:',ATA_LOCAL_NAME); ?></th>
				</tr>
				<tr style="vertical-align: top; "><td scope="row" colspan="2">
					<label><input type="checkbox" name="content_addhtmlbefore" id="content_addhtmlbefore" <?php if ($ata_settings['content_addhtmlbefore']) echo 'checked="checked"' ?> /> <?php _e('Add the following before the content. (You can use HTML):',ATA_LOCAL_NAME); ?></label>
					<br /><textarea name="content_htmlbefore" id="content_htmlbefore" rows="15" cols="80"><?php echo stripslashes($ata_settings['content_htmlbefore']); ?></textarea></td>
				</tr>
				<tr style="vertical-align: top; "><td scope="row" colspan="2">
					<label><input type="checkbox" name="content_addhtmlafter" id="content_addhtmlafter" <?php if ($ata_settings['content_addhtmlafter']) echo 'checked="checked"' ?> /> <?php _e('Add the following after the content. (You can use HTML):',ATA_LOCAL_NAME); ?></label>
					<br /><textarea name="content_htmlafter" id="content_htmlafter" rows="15" cols="80"><?php echo stripslashes($ata_settings['content_htmlafter']); ?></textarea></td>
				</tr>

				<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e('Content to add to single posts before and/or after post content:',ATA_LOCAL_NAME); ?></th>
				</tr>
				<tr style="vertical-align: top; "><td scope="row" colspan="2">
					<label><input type="checkbox" name="content_addhtmlbeforeS" id="content_addhtmlbeforeS" <?php if ($ata_settings['content_addhtmlbeforeS']) echo 'checked="checked"' ?> /> <?php _e('Add the following before the content. (You can use HTML):',ATA_LOCAL_NAME); ?></label>
					<br /><textarea name="content_htmlbeforeS" id="content_htmlbeforeS" rows="15" cols="80"><?php echo stripslashes($ata_settings['content_htmlbeforeS']); ?></textarea></td>
				</tr>
				<tr style="vertical-align: top; "><td scope="row" colspan="2">
					<label><input type="checkbox" name="content_addhtmlafterS" id="content_addhtmlafterS" <?php if ($ata_settings['content_addhtmlafterS']) echo 'checked="checked"' ?> /> <?php _e('Add the following after the content. (You can use HTML):',ATA_LOCAL_NAME); ?></label>
					<br /><textarea name="content_htmlafterS" id="content_htmlafterS" rows="15" cols="80"><?php echo stripslashes($ata_settings['content_htmlafterS']); ?></textarea></td>
				</tr>

				<tr style="vertical-align: top; background: #eee"><th scope="row" colspan="2"><?php _e('Adjust content filter priority:',ATA_LOCAL_NAME); ?></th>
				</tr>
				<tr>
				<td colspan="2">
					<input type="textbox" name="content_filter_priority" id="content_filter_priority" value="<?php echo esc_attr(stripslashes($ata_settings['content_filter_priority'])); ?>" style="width:250px" />
					<?php _e('A higher number will cause the content above to be processed after other filters. Number below 10 is not recommended',ATA_LOCAL_NAME); ?>
				</td>
				</tr>
			  </table>
			</div>
			<div class="tabbertab">
			<h3>
			  <?php _e('Footer options',ATA_LOCAL_NAME); ?>
			</h3>
			  <table class="form-table">
				<tr style="vertical-align: top; "><th scope="row" colspan="2"><?php _e('Any other HTML (no PHP) to add to <code>wp_footer</code>:',ATA_LOCAL_NAME); ?></th>
				</tr>
				<tr style="vertical-align: top; "><td scope="row" colspan="2"><textarea name="ft_other" id="ft_other" rows="15" cols="80"><?php echo stripslashes($ata_settings['ft_other']); ?></textarea></td>
				</tr>
			  </table>		
			</div>
			<div class="tabbertab">
			<h3>
			  <?php _e('Feed options',ATA_LOCAL_NAME); ?>
			</h3>
			  <table class="form-table">
				<tr style="vertical-align: top; "><td scope="row" colspan="2">
					<label><input type="checkbox" name="feed_addhtmlbefore" id="feed_addhtmlbefore" <?php if ($ata_settings['feed_addhtmlbefore']) echo 'checked="checked"' ?> /> <?php _e('Add the following to the feed before the content. (You can use HTML):',ATA_LOCAL_NAME); ?></label>
					<br /><textarea name="feed_htmlbefore" id="feed_htmlbefore" rows="15" cols="80"><?php echo stripslashes($ata_settings['feed_htmlbefore']); ?></textarea></td>
				</tr>
				<tr style="vertical-align: top; "><td scope="row" colspan="2">
					<label><input type="checkbox" name="feed_addhtmlafter" id="feed_addhtmlafter" <?php if ($ata_settings['feed_addhtmlafter']) echo 'checked="checked"' ?> /> <?php _e('Add the following to the feed after the content. (You can use HTML):',ATA_LOCAL_NAME); ?></label>
					<br /><textarea name="feed_htmlafter" id="feed_htmlafter" rows="15" cols="80"><?php echo stripslashes($ata_settings['feed_htmlafter']); ?></textarea></td>
				</tr>
				<tr style="vertical-align: top; "><td scope="row" colspan="2">
					<label><input type="checkbox" name="feed_addcopyright" id="feed_addcopyright" <?php if ($ata_settings['feed_addcopyright']) echo 'checked="checked"' ?> /> <?php _e('Add the following copyright notice to the feed (You can use HTML):',ATA_LOCAL_NAME); ?></label>
					<br /><textarea name="feed_copyrightnotice" id="feed_copyrightnotice" rows="15" cols="80"><?php echo stripslashes($ata_settings['feed_copyrightnotice']); ?></textarea></td>
				</tr>
				<tr style="vertical-align: top;"><th scope="row"><label for="feed_addtitle"><?php _e('Add a link to the title of the post in the feed:',ATA_LOCAL_NAME); ?></label></th>
				<td><input type="checkbox" name="feed_addtitle" id="feed_addtitle" <?php if ($ata_settings['feed_addtitle']) echo 'checked="checked"' ?> /></td>
				</tr>
				<tr style="vertical-align: top;"><th scope="row"><label for="addcredit"><?php _e('Add a link to "Add to All" plugin page:',ATA_LOCAL_NAME); ?></label></th>
				<td><input type="checkbox" name="addcredit" id="addcredit" <?php if ($ata_settings['addcredit']) echo 'checked="checked"' ?> /></td>
				</tr>
			  </table>
			</div>
		</div>
		<p>
		  <input type="submit" name="ata_save" id="ata_save" value="Save Options" class="button-primary" />
		  <input name="ata_default" type="submit" id="ata_default" value="Default Options" class="button-secondary" onclick="if (!confirm('<?php _e('Do you want to set options to Default?',ATA_LOCAL_NAME); ?>')) return false;" />
		</p>
		</fieldset>
	  </form>
	</div>

	  </div>
	  <div style="clear: both;"></div>
	</div>
</div>
<?php

}


function ata_adminmenu() {
	if ((function_exists('add_options_page'))) {
		$plugin_page = add_options_page(__("Add to All", ATA_LOCAL_NAME), __("Add to All", ATA_LOCAL_NAME), 'manage_options', 'ata_options', 'ata_options');
		add_action( 'admin_head-'. $plugin_page, 'ata_adminhead' );
	}
}
add_action('admin_menu', 'ata_adminmenu');

// Admin notices
function ata_admin_notice() {
	$plugin_settings_page = '<a href="' . admin_url( 'options-general.php?page=ata_options' ) . '">' . __('plugin settings page', ATA_LOCAL_NAME ) . '</a>';

	if ( !current_user_can( 'manage_options' ) ) return;

    echo '<div class="error">
       <p>'.__('Add to All plugin has just been installed / upgraded. Please visit the ', ATA_LOCAL_NAME ).$plugin_settings_page.__(' to configure.', ATA_LOCAL_NAME ).'</p>
    </div>';
}
// add_action('admin_notices', 'ata_admin_notice');

function ata_adminhead() {
	global $ata_url;

?>
<link rel="stylesheet" type="text/css" href="<?php echo $ata_url ?>/wick/wick.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $ata_url ?>/admin-styles.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $ata_url ?>/tabber/tabber.css" />
<script type="text/javascript" language="JavaScript">
function checkForm() {
answer = true;
if (siw && siw.selectingSomething)
	answer = false;
return answer;
}//
</script>
<script type="text/javascript" src="<?php echo $ata_url ?>/wick/sample_data.js.php"></script>
<script type="text/javascript" src="<?php echo $ata_url ?>/wick/wick.js"></script>
<script type="text/javascript" src="<?php echo $ata_url ?>/tabber/tabber.js"></script>
<?php }

?>