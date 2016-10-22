<?php
/**
 * Generates the settings page in the Admin
 *
 * @package Add_to_All
 */

// If this file is called directly, then abort execution.
if ( ! defined( 'WPINC' ) ) {
	die( "Aren't you supposed to come here via WP-Admin?" );
}

/**
 * Add to All options.
 */
function ata_options() {

	global $allowedposttags;

	// We need more tags to allow for script and style.
	$moretags = array(
		'script' => array(
			'type' => true,
			'src' => true,
		),
		'style' => array(
			'type' => true,
		),
	);

	$allowedatatags = array_merge( $allowedposttags, $moretags );

	$ata_settings = ata_read_options();

	if ( ( isset( $_POST['ata_save'] ) ) && ( check_admin_referer( 'add-to-all-admin-ops' ) ) ) {

		$ata_settings['addcredit'] = ( isset( $_POST['addcredit'] ) ) ? true : false;

		// Save Header related options.
		$ata_settings['head_CSS'] = wp_kses_post( wp_unslash( $_POST['head_CSS'] ) );
		$ata_settings['head_other'] = wp_kses( wp_unslash( $_POST['head_other'] ), $allowedatatags );

		// Save Content related options
		$ata_settings['content_htmlbefore'] = $_POST['content_htmlbefore'];
		$ata_settings['content_htmlafter'] = $_POST['content_htmlafter'];
		$ata_settings['content_addhtmlbefore'] = ( isset( $_POST['content_addhtmlbefore'] ) ) ? true : false;
		$ata_settings['content_addhtmlafter'] = ( isset( $_POST['content_addhtmlafter'] ) ) ? true : false;
		$ata_settings['content_htmlbeforeS'] = $_POST['content_htmlbeforeS'];
		$ata_settings['content_htmlafterS'] = $_POST['content_htmlafterS'];
		$ata_settings['content_addhtmlbeforeS'] = ( isset( $_POST['content_addhtmlbeforeS'] ) ) ? true : false;
		$ata_settings['content_addhtmlafterS'] = ( isset( $_POST['content_addhtmlafterS'] ) ) ? true : false;
		$ata_settings['content_filter_priority'] = intval( $_POST['content_filter_priority'] );

		// Save Feed related options
		$ata_settings['feed_htmlbefore'] = $_POST['feed_htmlbefore'];
		$ata_settings['feed_htmlafter'] = $_POST['feed_htmlafter'];
		$ata_settings['feed_copyrightnotice'] = $_POST['feed_copyrightnotice'];
		$ata_settings['feed_addhtmlbefore'] = ( isset( $_POST['feed_addhtmlbefore'] ) ) ? true : false;
		$ata_settings['feed_addhtmlafter'] = ( isset( $_POST['feed_addhtmlafter'] ) ) ? true : false;
		$ata_settings['feed_addtitle'] = (isset( $_POST['feed_addtitle'] ) ) ? true : false;
		$ata_settings['feed_titletext'] = $_POST['feed_titletext'];
		$ata_settings['feed_addcopyright'] = ( isset( $_POST['feed_addcopyright'] ) ) ? true : false;

		// Save Footer related options
		$ata_settings['ft_other'] = wp_kses( wp_unslash( $_POST['ft_other'] ), $allowedatatags );

		// 3rd party options
		$ata_settings['tp_sc_project'] = $_POST['tp_sc_project'];
		$ata_settings['tp_sc_security'] = $_POST['tp_sc_security'];

		$ata_settings['tp_ga_uacct'] = $_POST['tp_ga_uacct'];
		$ata_settings['tp_ga_domain'] = $_POST['tp_ga_domain'];
		$ata_settings['tp_ga_ua'] = isset( $_POST['tp_ga_ua'] ) ? true : false;

		$ata_settings['tp_kontera_ID'] = $_POST['tp_kontera_ID'];
		$ata_settings['tp_kontera_linkcolor'] = $_POST['tp_kontera_linkcolor'];
		$ata_settings['tp_kontera_addZT'] = ( isset( $_POST['tp_kontera_addZT'] ) ) ? true : false;

		$ata_settings['tp_tynt_id'] = $_POST['tp_tynt_id'];

		update_option( 'ald_ata_settings', $ata_settings );

		$str = '<div id="message" class="updated fade"><p>' . __( 'Options saved successfully.', 'add-to-all' ) . '</p></div>';
		echo $str; // WPCS: XSS OK.
	}

	if ( ( isset( $_POST['ata_default'] ) ) && ( check_admin_referer( 'add-to-all-admin-ops' ) ) ) {

		delete_option( 'ald_ata_settings' );
		$ata_settings = ata_default_options();
		update_option( 'ald_ata_settings', $ata_settings );

		$str = '<div id="message" class="updated fade"><p>' . __( 'Options set to Default.', 'add-to-all' ) . '</p></div>';
		echo $str; // WPCS: XSS OK.
	}

?>

<div class="wrap">
	<h2><?php esc_html_e( 'Add to All', 'add-to-all' ); ?></h2>
	<div id="poststuff">
	<div id="post-body" class="metabox-holder columns-2">
	<div id="post-body-content">
	  <div id="options-div">
	  <form method="post" id="ata_options" name="ata_options" onsubmit="return checkForm()">
	    <div id="thirdpartydiv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'add-to-all' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Options for 3rd party services', 'add-to-all' ); ?></span></h3>
	      <div class="inside">
			<table class="form-table">
			  <tbody>
				<tr><th scope="row" colspan="2" style="background:#eee;padding-left:10px;"><?php esc_html_e( 'Statcounter Options:', 'add-to-all' ); ?></th>
				</tr>
				<tr><th scope="row"><label for="tp_sc_project"><?php esc_html_e( 'StatCounter Project ID (Value of sc_project):', 'add-to-all' ); ?></label></th>
					<td><input type="textbox" name="tp_sc_project" id="tp_sc_project" value="<?php echo esc_attr( $ata_settings['tp_sc_project'] ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="tp_sc_security"><?php esc_html_e( 'StatCounter Security ID (Value of sc_security):', 'add-to-all' ); ?></label></th>
					<td><input type="textbox" name="tp_sc_security" id="tp_sc_security" value="<?php echo esc_attr( $ata_settings['tp_sc_security'] ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row" colspan="2" style="background:#eee;padding-left:10px;"><?php esc_html_e( 'Google Analytics Options:', 'add-to-all' ); ?></th>
				</tr>
				<tr>
					<th scope="row"><label for="tp_ga_ua"><?php esc_html_e( 'Enable Universal Analytics:', 'add-to-all' ); ?></label></th>
					<td>
						<input type="checkbox" name="tp_ga_ua" id="tp_ga_ua" <?php if ( $ata_settings['tp_ga_ua'] ) { echo 'checked="checked"'; } ?> />
						<p class="description"><?php printf( esc_html__( 'Only check this box if you have upgraded to Universal Analytics. Visit the <a href="%s" target="_blank">Universal Analytics Upgrade Center</a> to know more', 'add-to-footer' ), esc_url( 'https://developers.google.com/analytics/devguides/collection/upgrade/' ) ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><label for="tp_ga_uacct"><?php esc_html_e( 'Tracking ID:', 'add-to-all' ); ?></label></th>
					<td><input type="textbox" name="tp_ga_uacct" id="tp_ga_uacct" value="<?php echo esc_attr( $ata_settings['tp_ga_uacct'] ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="tp_ga_domain"><?php esc_html_e( 'Multiple sub-domain and top-level domain support (Value of _setDomainName):', 'add-to-all' ); ?></label></th>
					<td><input type="textbox" name="tp_ga_domain" id="tp_ga_domain" value="<?php echo esc_attr( $ata_settings['tp_ga_domain'] ); ?>" style="width:250px" />
				    <a href="https://support.google.com/analytics/bin/answer.py?hl=en-GB&utm_id=ad&answer=1034342" target="_blank"><?php esc_html_e( 'View Explanation', 'add-to-all' ); ?></a></td>
				</tr>
				<tr><th scope="row" colspan="2" style="background:#eee;padding-left:10px;"><?php esc_html_e( 'Kontera Options:', 'add-to-all' ); ?></th>
				</tr>
				<tr><th scope="row"><label for="tp_kontera_ID"><?php esc_html_e( 'Kontera ID (Value of dc_PublisherID):', 'add-to-all' ); ?></label></th>
					<td><input type="textbox" name="tp_kontera_ID" id="tp_kontera_ID" value="<?php echo esc_attr( $ata_settings['tp_kontera_ID'] ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="tp_kontera_linkcolor"><?php esc_html_e( 'Kontera link colour (Value of dc_AdLinkColor):', 'add-to-all' ); ?></label></th>
					<td><input type="textbox" name="tp_kontera_linkcolor" id="tp_kontera_linkcolor" value="<?php echo esc_attr( $ata_settings['tp_kontera_linkcolor'] ); ?>" style="width:250px" /></td>
				</tr>
				<tr><th scope="row"><label for="tp_kontera_addZT"><?php esc_html_e( 'Wrap post content with Kontera Zone Tags:', 'add-to-all' ); ?></label></th>
					<td><input type="checkbox" name="tp_kontera_addZT" id="tp_kontera_addZT" <?php if ( $ata_settings['tp_kontera_addZT'] ) { echo 'checked="checked"'; } ?> /></td>
				</tr>
				<tr><th scope="row" colspan="2" style="background:#eee;padding-left:10px;"><?php esc_html_e( 'Tynt Options:', 'add-to-all' ); ?></th>
				</tr>
				<tr><th scope="row"><label for="tp_tynt_id"><?php esc_html_e( 'Tynt ID:', 'add-to-all' ); ?></label></th>
					<td>
						<input type="textbox" name="tp_tynt_id" id="tp_tynt_id" value="<?php echo esc_attr( $ata_settings['tp_tynt_id'] ); ?>" style="width:250px" />
						<p class="description"><?php esc_html_e( "This is the text between <code>Tynt.push('ID IS HERE')</code> in the <a href='http://tcr1.tynt.com/install' target='_blank'>Install Code</a>", 'add-to-all' ); ?></p>
					</td>
				</tr>
			  </tbody>
			</table>
	      </div>
	    </div>
	    <div id="headeropdiv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'add-to-all' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Header options', 'add-to-all' ); ?></span></h3>
	      <div class="inside">
			<table class="form-table">
			  <tbody>
				<tr><th scope="row" colspan="2"><?php esc_html_e( 'Custom CSS to add to header:', 'add-to-all' ); ?></th>
				</tr>
				<tr><td scope="row" colspan="2"><textarea name="head_CSS" id="head_CSS" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['head_CSS'] ); ?></textarea></td>
				<br /><em><?php esc_html_e( 'Do not include <code>style</code> tags', 'add-to-all' ); ?></em></tr>
				<tr><th scope="row" colspan="2"><?php esc_html_e( 'Any other HTML (no PHP) to add to <code>wp_head</code>:', 'add-to-all' ); ?></th>
				</tr>
				<tr><td scope="row" colspan="2"><textarea name="head_other" id="head_other" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['head_other'] ); ?></textarea></td>
				</tr>
			  </tbody>
			</table>
	      </div>
	    </div>
	    <div id="contentopdiv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'add-to-all' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Content options', 'add-to-all' ); ?></span></h3>
	      <div class="inside">
			<table class="form-table">
			  <tbody>
				<tr><th scope="row" colspan="2"><?php esc_html_e( 'Content to add to all posts before and/or after post content:', 'add-to-all' ); ?></th>
				</tr>
				<tr><td scope="row" colspan="2">
					<label><input type="checkbox" name="content_addhtmlbefore" id="content_addhtmlbefore" <?php if ( $ata_settings['content_addhtmlbefore'] ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Add the following before the content. (You can use HTML):', 'add-to-all' ); ?></label>
					<br /><textarea name="content_htmlbefore" id="content_htmlbefore" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['content_htmlbefore'] ); ?></textarea></td>
				</tr>
				<tr><td scope="row" colspan="2">
					<label><input type="checkbox" name="content_addhtmlafter" id="content_addhtmlafter" <?php if ( $ata_settings['content_addhtmlafter'] ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Add the following after the content. (You can use HTML):', 'add-to-all' ); ?></label>
					<br /><textarea name="content_htmlafter" id="content_htmlafter" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['content_htmlafter'] ); ?></textarea></td>
				</tr>
				<tr><th scope="row" colspan="2"><?php esc_html_e( 'Content to add to single posts before and/or after post content:', 'add-to-all' ); ?></th>
				</tr>
				<tr><td scope="row" colspan="2">
					<label><input type="checkbox" name="content_addhtmlbeforeS" id="content_addhtmlbeforeS" <?php if ( $ata_settings['content_addhtmlbeforeS'] ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Add the following before the content. (You can use HTML):', 'add-to-all' ); ?></label>
					<br /><textarea name="content_htmlbeforeS" id="content_htmlbeforeS" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['content_htmlbeforeS'] ); ?></textarea></td>
				</tr>
				<tr><td scope="row" colspan="2">
					<label><input type="checkbox" name="content_addhtmlafterS" id="content_addhtmlafterS" <?php if ( $ata_settings['content_addhtmlafterS'] ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Add the following after the content. (You can use HTML):', 'add-to-all' ); ?></label>
					<br /><textarea name="content_htmlafterS" id="content_htmlafterS" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['content_htmlafterS'] ); ?></textarea></td>
				</tr>
				<tr><th scope="row" colspan="2"><?php esc_html_e( 'Adjust content filter priority:', 'add-to-all' ); ?></th>
				</tr>
				<tr>
				<td colspan="2">
					<input type="textbox" name="content_filter_priority" id="content_filter_priority" value="<?php echo esc_attr( $ata_settings['content_filter_priority'] ); ?>" />
					<p class="description"><?php esc_html_e( 'A higher number will cause the content above to be processed after other filters. Number below 10 is not recommended', 'add-to-all' ); ?></p>
				</td>
				</tr>
			  </tbody>
			</table>
	      </div>
	    </div>
	    <div id="footeropdiv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'add-to-all' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Footer options', 'add-to-all' ); ?></span></h3>
	      <div class="inside">
			<table class="form-table">
			  <tbody>
				<tr><th scope="row" colspan="2"><?php esc_html_e( 'Any other HTML (no PHP) to add to <code>wp_footer</code>:', 'add-to-all' ); ?></th>
				</tr>
				<tr><td scope="row" colspan="2"><textarea name="ft_other" id="ft_other" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['ft_other'] ); ?></textarea></td>
				</tr>
			  </tbody>
			</table>
	      </div>
	    </div>
	    <div id="feedopdiv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'add-to-all' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Feed options', 'add-to-all' ); ?></span></h3>
	      <div class="inside">
			<table class="form-table">
			  <tbody>
				<tr><td scope="row" colspan="2">
					<label><input type="checkbox" name="feed_addhtmlbefore" id="feed_addhtmlbefore" <?php if ( $ata_settings['feed_addhtmlbefore'] ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Add the following to the feed before the content. (You can use HTML):', 'add-to-all' ); ?></label>
					<br /><textarea name="feed_htmlbefore" id="feed_htmlbefore" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['feed_htmlbefore'] ); ?></textarea></td>
				</tr>
				<tr><td scope="row" colspan="2">
					<label><input type="checkbox" name="feed_addhtmlafter" id="feed_addhtmlafter" <?php if ( $ata_settings['feed_addhtmlafter'] ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Add the following to the feed after the content. (You can use HTML):', 'add-to-all' ); ?></label>
					<br /><textarea name="feed_htmlafter" id="feed_htmlafter" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['feed_htmlafter'] ); ?></textarea></td>
				</tr>
				<tr><td scope="row" colspan="2">
					<label><input type="checkbox" name="feed_addcopyright" id="feed_addcopyright" <?php if ( $ata_settings['feed_addcopyright'] ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Add the following copyright notice to the feed (You can use HTML):', 'add-to-all' ); ?></label>
					<br /><textarea name="feed_copyrightnotice" id="feed_copyrightnotice" rows="15" cols="40" style="width:100%"><?php echo esc_attr( $ata_settings['feed_copyrightnotice'] ); ?></textarea></td>
				</tr>
				<tr>
					<td scope="row" colspan="2">
						<label><input type="checkbox" name="feed_addtitle" id="feed_addtitle" <?php if ( $ata_settings['feed_addtitle'] ) { echo 'checked="checked"'; } ?> /> <?php esc_html_e( 'Add a link to the title of the post in the feed. Customize this below', 'add-to-all' ); ?></label>
						<br /><textarea name="feed_titletext" id="feed_titletext" rows="5" cols="80" style="width:100%"><?php echo esc_attr( $ata_settings['feed_titletext'] ); ?></textarea>
						<p class="description"><?php esc_html_e( 'The above text will be added to the feed. You can use %title% to add a link to the post, %date% and %time% to display the date and time of the post respectively', 'add-to-all' ); ?></p>
					</td>
				</tr>
				<tr><th scope="row"><label for="addcredit"><?php esc_html_e( 'Add a link to "Add to All" plugin page:', 'add-to-all' ); ?></label></th>
					<td><input type="checkbox" name="addcredit" id="addcredit" <?php if ( $ata_settings['addcredit'] ) { echo 'checked="checked"'; } ?> /></td>
				</tr>
			  </tbody>
			</table>
	      </div>
	    </div>

		<p>
		  <input type="submit" name="ata_save" id="ata_save" value="Save Options" class="button-primary" />
		  <input name="ata_default" type="submit" id="ata_default" value="Default Options" class="button-secondary" onclick="if (!confirm('<?php esc_html_e( 'Do you want to set options to Default?', 'add-to-all' ); ?>')) return false;" />
		</p>
		<?php wp_nonce_field( 'add-to-all-admin-ops' ); ?>
	  </form>
	</div><!-- /options-div -->
	</div><!-- /post-body-content -->
	<div id="postbox-container-1" class="postbox-container">
	  <div id="side-sortables" class="meta-box-sortables ui-sortable">
	    <div id="donatediv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'add-to-all' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Support the development', 'add-to-all' ); ?></span></h3>
	      <div class="inside">
			<div id="donate-form">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_xclick">
				<input type="hidden" name="business" value="donate@ajaydsouza.com">
				<input type="hidden" name="lc" value="IN">
				<input type="hidden" name="item_name" value="Donation for Add to All">
				<input type="hidden" name="item_number" value="ata">
				<strong><?php esc_html_e( 'Enter amount in USD: ', 'add-to-all' ); ?></strong> <input name="amount" value="10.00" size="6" type="text"><br />
				<input type="hidden" name="currency_code" value="USD">
				<input type="hidden" name="button_subtype" value="services">
				<input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted">
				<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php esc_html_e( 'Send your donation to the author of', 'add-to-all' ); ?> Add to All?">
				<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
				</form>
			</div>
	      </div>
	    </div>
	    <div id="followdiv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'add-to-all' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Follow me', 'add-to-all' ); ?></span></h3>
	      <div class="inside">
			<a href="https://facebook.com/webberzone/" target="_blank"><img src="<?php echo esc_url( ATA_PLUGIN_URL . 'admin/images/fb.png' ); ?>" width="100" height="100" /></a>
			<a href="https://twitter.com/webberzonewp/" target="_blank"><img src="<?php echo esc_url( ATA_PLUGIN_URL . 'admin/images/twitter.jpg' ); ?>" width="100" height="100" /></a>
	      </div>
	    </div>
	    <div id="qlinksdiv" class="postbox"><div class="handlediv" title="<?php esc_html_e( 'Click to toggle', 'add-to-all' ); ?>"><br /></div>
	      <h3 class='hndle'><span><?php esc_html_e( 'Quick links', 'add-to-all' ); ?></span></h3>
	      <div class="inside">
	        <div id="quick-links">
				<ul>
					<li><a href="http://ajaydsouza.com/wordpress/plugins/add-to-all/"><?php esc_html_e( 'Add to All plugin page', 'add-to-all' ); ?></a></li>
					<li><a href="http://ajaydsouza.com/wordpress/plugins/"><?php esc_html_e( 'Other plugins', 'add-to-all' ); ?></a></li>
					<li><a href="http://ajaydsouza.com/"><?php esc_html_e( "Ajay's blog", 'add-to-all' ); ?></a></li>
					<li><a href="https://wordpress.org/plugins/add-to-all/faq/"><?php esc_html_e( 'FAQ', 'add-to-all' ); ?></a></li>
					<li><a href="https://wordpress.org/support/plugin/add-to-all"><?php esc_html_e( 'Support', 'add-to-all' ); ?></a></li>
					<li><a href="https://wordpress.org/support/view/plugin-reviews/add-to-all"><?php esc_html_e( 'Reviews', 'add-to-all' ); ?></a></li>
				</ul>
	        </div>
	      </div>
	    </div>
	  </div><!-- /side-sortables -->
	</div><!-- /postbox-container-1 -->
	</div><!-- /post-body -->
	<br class="clear" />
	</div><!-- /poststuff -->
</div><!-- /wrap -->

<?php
}


/**
 * Add menu item in WP-Admin.
 */
function ata_adminmenu() {
	$plugin_page = add_options_page( __( 'Add to All', 'add-to-all' ), __( 'Add to All', 'add-to-all' ), 'manage_options', 'ata_options', 'ata_options' );
	add_action( 'admin_head-' . $plugin_page, 'ata_adminhead' );
}
add_action( 'admin_menu', 'ata_adminmenu' );


/**
 * Add script to the Admin head.
 */
function ata_adminhead() {

	wp_enqueue_script( 'common' );
	wp_enqueue_script( 'wp-lists' );
	wp_enqueue_script( 'postbox' );

?>
	<style type="text/css">
		.postbox .handlediv:before {
			right:12px;
			font:400 20px/1 dashicons;
			speak:none;
			display:inline-block;
			top:0;
			position:relative;
			-webkit-font-smoothing:antialiased;
			-moz-osx-font-smoothing:grayscale;
			text-decoration:none!important;
			content:'\f142';
			padding:8px 10px;
		}
		.postbox.closed .handlediv:before {
			content: '\f140';
		}
	</style>

	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('ata_options');
		});
		//]]>
	</script>

	<script type="text/javascript" language="JavaScript">
		//<![CDATA[
		function checkForm() {
		answer = true;
		if (siw && siw.selectingSomething)
			answer = false;
		return answer;
		}//
		//]]>
	</script>

<?php
}

/**
 * Adding WordPress plugin action links.
 *
 * @param array $links Array of links.
 * @return array
 */
function ata_plugin_actions_links( $links ) {

	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=ata_options' ) . '">' . __( 'Settings', 'add-to-all' ) . '</a>',
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
		$links[] = '<a href="http://wordpress.org/support/plugin/add-to-all">' . __( 'Support', 'add-to-all' ) . '</a>';
		$links[] = '<a href="http://ajaydsouza.com/donate/">' . __( 'Donate', 'add-to-all' ) . '</a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'ata_plugin_actions', 10, 2 );

