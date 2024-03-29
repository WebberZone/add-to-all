<?php
/**
 * Sidebar
 *
 * @link  https://webberzone.com
 * @since 1.2.0
 *
 * @package WebberZone\Snippetz
 */

?>
<div class="postbox-container">
	<div id="donatediv" class="postbox meta-box-sortables">
		<h2 class='hndle'><span><?php esc_attr_e( 'Support the development', 'add-to-all' ); ?></span></h2>

		<div class="inside" style="text-align: center">
			<div id="donate-form">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
					<input type="hidden" name="cmd" value="_xclick"> <input type="hidden" name="business" value="donate@ajaydsouza.com"> <input type="hidden" name="lc" value="IN"> <input type="hidden" name="item_name" value="<?php esc_attr_e( 'Donation for WebberZone Snippetz', 'add-to-all' ); ?>"> <input type="hidden" name="item_number" value="wz_snippetz_settings"> <strong><?php esc_attr_e( 'Enter amount in USD', 'add-to-all' ); ?>:</strong> <input name="amount" value="10.00" size="6" type="text"><br>
					<input type="hidden" name="currency_code" value="USD"> <input type="hidden" name="button_subtype" value="services"> <input type="hidden" name="bn" value="PP-BuyNowBF:btn_donate_LG.gif:NonHosted"> <input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="<?php esc_attr_e( 'Send your donation to the author of WebberZone Snippetz', 'add-to-all' ); ?>">
				</form>
			</div><!-- /#donate-form -->
		</div><!-- /.inside -->
	</div><!-- /.postbox -->

	<div id="qlinksdiv" class="postbox meta-box-sortables">
		<h2 class='hndle metabox-holder'><span><?php esc_html_e( 'Quick links', 'add-to-all' ); ?></span></h2>

		<div class="inside">
			<div id="quick-links">
				<ul class="subsub">
					<li>
						<a href="https://webberzone.com/plugins/add-to-all/"><?php esc_html_e( 'WebberZone Snippetz plugin homepage', 'add-to-all' ); ?></a>
					</li>

					<li>
						<a href="https://wordpress.org/plugins/add-to-all/faq/"><?php esc_html_e( 'FAQ', 'add-to-all' ); ?></a>
					</li>

					<li>
						<a href="https://wordpress.org/support/plugin/add-to-all/"><?php esc_html_e( 'Support', 'add-to-all' ); ?></a>
					</li>

					<li>
						<a href="https://wordpress.org/support/plugin/add-to-all/reviews/"><?php esc_html_e( 'Reviews', 'add-to-all' ); ?></a>
					</li>

					<li>
						<a href="https://github.com/ajaydsouza/add-to-all"><?php esc_html_e( 'Github repository', 'add-to-all' ); ?></a>
					</li>

					<li>
						<a href="https://webberzone.com/plugins/"><?php esc_html_e( 'Other plugins', 'add-to-all' ); ?></a>
					</li>

					<li>
						<a href="https://webberzone.com/"><?php esc_html_e( "Ajay's blog", 'add-to-all' ); ?></a>
					</li>
				</ul>
			</div>
		</div><!-- /.inside -->
	</div><!-- /.postbox -->
</div>

<div class="postbox-container">
	<div id="followdiv" class="postbox meta-box-sortables">
		<h2 class='hndle'><span><?php esc_html_e( 'Follow me', 'add-to-all' ); ?></span></h2>

		<div class="inside" style="text-align: center">
			<a href="https://facebook.com/webberzone/" target="_blank"><img src="<?php echo esc_url( WZ_SNIPPETZ_URL . 'includes/admin/images/fb.png' ); ?>" width="100" height="100"></a> <a href="https://twitter.com/webberzone/" target="_blank"><img src="<?php echo esc_url( WZ_SNIPPETZ_URL . 'includes/admin/images/twitter.jpg' ); ?>" width="100" height="100"></a>
		</div><!-- /.inside -->
	</div><!-- /.postbox -->
</div>

