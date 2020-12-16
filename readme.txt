=== Add to All ===
Tags: header, footer, feed, snippets, Google Analytics, Statcounter, Tynt, content, Adsense, site verification
Contributors: Ajay, webberzone
Donate link: https://ajaydsouza.com/donate/
Stable tag: trunk
Requires at least: 4.9
Tested up to: 5.6
License: GPLv2 or later

A powerful plugin that will allow you to add custom code or CSS to your header, footer, sidebar, content or feed.

== Description ==

[Add to All](https://webberzone.com/plugins/add-to-all/) is a simple, yet powerful plugin that will allow you to add HTML, JavaScript or CSS to your header, footer, content or feed.

The Snippets Manager introduced in v1.7.0 also allows you to add create and manage custom snippets of code that can be added to the header, footer and content. You can selectively include this based on different criteria including specific post IDs, post types and/or categories/tags.

It comes with out of the box support for Google Analytics, Statcounter and Tynt. Additionally, it supports site verification for Google, Bing and Pinterest via their HTML tag methods.

If you have any script that isn't supported by Add to All currently, you can just add the code to the Header, Content or Footer sections.

Add to All includes a copyright notice that can be automatically added your feed. Additionally, you can also add in a link to your post as well as any other HTML or text that you might want.

With this plugin installed, you do not need to edit your theme files every time you switch themes.


= Key features =

* Snippets manager: Create custom snippets that can be added to Header, Footer and Content based on inclusion criteria
* Add any HTML or JavaScript code to your header, content, footer and feed
* Inbuilt support for Google Analytics, Statcounter and Tynt
* Site verification for Google, Bing and Pinterest
* Add custom CSS code to your header
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

If your question isn't listed here, please post a comment at the [WordPress.org support forum](https://wordpress.org/support/plugin/add-to-all). I monitor the forums on an ongoing basis. If you're looking for more advanced **paid** support, please see [details here](https://webberzone.com/support/).

If you would like a feature to be added, or if you already have the code for the feature, you can let me know by [posting in this forum](https://wordpress.org/support/plugin/add-to-all) or creating an issue in the [Github repository](https://github.com/ajaydsouza/add-to-all/issues).


== Screenshots ==

1. Add to All options in WP-Admin - 3rd Party Options
2. Add to All options in WP-Admin - Header Options
3. Add to All options in WP-Admin - Content Options
4. Add to All options in WP-Admin - Footer Options
5. Add to All options in WP-Admin - Feed Options


== Changelog ==

= 1.7.0 =

Release post: [https://webberzone.com/blog/add-to-all-v1-7-0/](https://webberzone.com/blog/add-to-all-v1-7-0/)

* Features:
	* New Settings API class to control the plugins settings
	* New Snippets Manager - create custom snippets that can be added to Header, Footer and Content based on inclusion criteria

= 1.6.0 =

Release post: [https://webberzone.com/blog/add-to-all-v1-6-0/](https://webberzone.com/blog/add-to-all-v1-6-0/)

* Features:
	* Added CodeMirror highlighting for the HTML and CSS fields in the settings page

= 1.5.0 =

Release post: [https://ajaydsouza.com/add-to-all-v1-5-0/](https://ajaydsouza.com/add-to-all-v1-5-0/)

* Features:
	* New options for displaying content before and after on posts and pages only

* Bug fixes:
	* Delete first year transient on uninstall

= 1.4.0 =

Release post: [https://ajaydsouza.com/add-to-all-v1-4-0/](https://ajaydsouza.com/add-to-all-v1-4-0/)

* Features:
	* Deleting the plugin on WordPress Multisite will remove the settings from all blogs
	* Use `%home_url%`, `%year%`, `%month%`, `%date%` and `%first_year%` to display the URL for a given site, current year, current month (text), current date and year of first blog post respectively. You can add more replacement terms by creating a function filtering `ata_placeholders`

* Modifications:
	* Deprecated `ald_ata_header()`, `ald_ata_footer()`, `ald_ata_rss()`

* Bug fixes:
	* Fixed bug with displaying the credit line in feed

= 1.3.0 =

Release post: [https://ajaydsouza.com/add-to-all-v1-3-0/](https://ajaydsouza.com/add-to-all-v1-3-0/)

* Features:
	* New option to enable processing of shortcode in content, footer or feed HTML fields
	* New option to exclude display of content HTML fields on certain posts

* Enhancements:
	* New functions and filters for content, feed, header and footer
	* Admin interface: switch between tabs without reloading
	* Google Analytics now use gtag.js implementation instead of analytics.js
	* Google Analytics: new option to anonymize IPs

Check changelog.txt for older entries the [Releases page on Github](https://github.com/WebberZone/add-to-all/releases)

== Upgrade Notice ==

= 1.6.0 =
Implemented CodeMirror for code highlighting; Minimum WordPress supported version bumped up to v4.9; Check out the Changelog for more details

