=== Add to All ===
Tags: header, footer, feed, Google Analytics, Statcounter, Tynt, content, Adsense, site verification
Contributors: Ajay, webberzone
Donate link: https://ajaydsouza.com/donate/
Stable tag: trunk
Requires at least: 4.2
Tested up to: 4.9
License: GPLv2 or later

A powerful plugin that will allow you to add custom code or CSS to your header, footer, sidebar, content or feed.

== Description ==

[Add to All](https://ajaydsouza.com/wordpress/plugins/add-to-all/) is a simple, yet powerful plugin that will allow you to add HTML, JavaScript or CSS to your header, footer, content or feed.

It comes with out of the box support for Google Analytics, Statcounter and Tynt. Additionally, it supports site verification for Google, Bing and Pinterest via their HTML tag methods.

If you have any script that isn't supported by Add to All currently, you can just add the code to the Header, Content or Footer sections.

Add to All includes a copyright notice that can be automatically added your feed. Additionally, you can also add in a link to your post as well as any other HTML or text that you might want.

With this plugin installed, you do not need to edit your theme files every time you switch themes.


= Key features =

* Inbuilt support for Google Analytics, Statcounter and Tynt
* Site verification for Google, Bing and Pinterest
* Add custom CSS code to your header
* Add any HTML or JavaScript code to your header, content, footer and feed
* Add a copyright notice and a link to the post in your site's feed
* Tonnes of actions and filters to easily extend the plugin.


== Installation ==

= WordPress install =
1. Navigate to Plugins within your WordPress Admin Area

2. Click "Add new" and in the search box enter "Add to All" and select "Keyword" from the dropdown

3. Find the plugin in the list (usually the first result) and click "Install Now"

= Manual install =
1. Download the plugin

2. Extract the contents of add-to-all.zip to wp-content/plugins/ folder. You should get a folder called add-to-all.

3. Activate the Plugin in WP-Admin.

4. Goto **Settings &raquo; Add to All** to configure


== Frequently Asked Questions ==

If your question isn't listed here, please post a comment at the [WordPress.org support forum](https://wordpress.org/support/plugin/add-to-all). I monitor the forums on an ongoing basis. If you're looking for more advanced **paid** support, please see [details here](https://ajaydsouza.com/support/).

If you would like a feature to be added, or if you already have the code for the feature, you can let me know by [posting in this forum](https://wordpress.org/support/plugin/add-to-all) or creating an issue in the [Github repository](https://github.com/ajaydsouza/add-to-all/issues).


== Screenshots ==

1. Add to All options in WP-Admin - 3rd Party Options
2. Add to All options in WP-Admin - Header Options
3. Add to All options in WP-Admin - Content Options
4. Add to All options in WP-Admin - Footer Options
5. Add to All options in WP-Admin - Feed Options


== Changelog ==

= 1.3.0 =
* Features:
	* New option to enable processing of shortcode in content, footer or feed HTML fields
	* New option to exclude display of content HTML fields on certain posts

* Enhancements:
	* New functions and filters for content, feed, header and footer
	* Admin interface: switch between tabs without reloading
	* Google Analytics now use gtag.js implementation instead of analytics.js
	* Google Analytics: new option to anonymize IPs

= 1.2.2 =
* Bug fix:
	* Disable aggressive textarea filter in Settings page

= 1.2.1 =
* Enhancements:
	* Admin settings page will allow more attributes and tags for `script`, `style` and `link` allowing you to use favicons, async, etc.

= 1.2.0 =
* Features:
	* Settings now uses the WordPress Settings API. Verify your site options by visiting the Add to All settings page
	* Site verification for Google, Bing and Pinterest

* Enhancements:
	* Google Analytics code now supports cross domain tracking using the Linker Plugin
	* Updated code for Statcounter and Tynt

* Deprecated:
	* Removed support for Kontera. If you'd like to continue using Kontera, please get the code from [the Kontera setup page](https://publishers.kontera.com/main/tag) and then add this under <strong>Footer settings</strong>

Check changelog.txt for older entries.

== Upgrade Notice ==

= 1.2.2 =
New plugin settings interface. Verify your settings on upgrade; Site verification support, bug fixes;
Check out the Changelog for more details

