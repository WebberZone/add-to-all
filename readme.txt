=== WebberZone Snippetz - Header, Body and Footer manager ===
Tags: snippets, html, css, javascript, code, header, footer, content, body, feed
Contributors: Ajay, webberzone
Donate link: https://ajaydsouza.com/donate/
Stable tag: 2.0.0
Requires at least: 5.6
Tested up to: 6.2
Requires PHP: 7.3
License: GPLv2 or later

The ultimate snippet manager for WordPress to create and manage custom HTML, CSS or JS code snippets.

== Description ==

Do you want to customize your site with code but don’t want to edit your theme files or worry about losing your changes when you switch themes? Do you want to add analytics, site verification, custom CSS, or any other code to your site without using multiple plugins? Do you want to have full control over where and when your code snippets are displayed on your site?

If you answered yes to any of these questions, then WebberZone Snippetz is the perfect plugin for you!

[WebberZone Snippetz](https://webberzone.com/plugins/add-to-all/) (formerly Add to All) is a simple, yet powerful plugin that will allow you to create and manage custom snippets of HTML, CSS or JS code and add them to your header, footer, content or feed. You can also choose where and when to display your snippets based on various criteria such as post IDs, post types, categories, tags, and more.

WebberZone Snippetz comes with out of the box support for Google Analytics, Statcounter and Tynt. Additionally, it supports site verification for Google, Bing and Pinterest via their HTML tag methods.

WebberZone Snippetz also enhances your site’s feed with a copyright notice and a link to the post. Plus, it comes with tons of actions and filters to extend its functionality.

Here are some of the key features of WebberZone Snippetz:

* Create custom snippets with HTML, CSS or JS code
* Add snippets to your header, footer, content or feed
* Choose where and when to display your snippets based on post IDs, post types, categories, tags, and more
* Support for Google Analytics, Statcounter and Tynt
* Site verification for Google, Bing and Pinterest
* No need to edit theme files or lose changes when switching themes

WebberZone Snippetz is the ultimate snippet manager for WordPress users who want to customize their site with code. Download it today and see the difference!


== Installation ==

= WordPress install =
1. Navigate to Plugins within your WordPress Admin Area

2. Click "Add new" and in the search box enter "Snippetz" and select "Keyword" from the dropdown

3. Find the plugin in the list (usually the first result) and click "Install Now"

= Manual install =
1. Download WebberZone Snippetz from the WordPress plugin repository.
2. Go to Plugins > Add New in your WordPress dashboard and click Upload Plugin.
3. Browse to the zip file that contains the plugin files and upload it.
4. Activate the plugin through the Plugins menu in WordPress.
5. Go to Settings > WebberZone Snippetz to configure the plugin options.


== Frequently Asked Questions ==

If your question isn't listed here, please post a comment at the [WordPress.org support forum](https://wordpress.org/support/plugin/add-to-all). I monitor the forums on an ongoing basis. If you're looking for more advanced **paid** support, please see [details here](https://webberzone.com/support/).

If you would like a feature to be added, or if you already have the code for the feature, you can let me know by [posting in this forum](https://wordpress.org/support/plugin/add-to-all) or creating an issue in the [Github repository](https://github.com/ajaydsouza/add-to-all/issues).

= How do I create a snippet? =

To create a snippet, go to Snippets > Add New in your WordPress dashboard. Give your snippet a title and enter your code in the editor. Follow the steps in the WebberZone Snippetz meta box to configure where the code snippet should display.

= How do I add a snippet to my site? =

To add a snippet to your site, go to Snippets > All Snippets in your WordPress dashboard. Find the snippet you want to add and click on Edit. Under the Display Options meta box, you can choose where you want to display your snippet (header, footer, content or feed) and when you want to display it (always or based on certain criteria). You can also set a priority for your snippet if you have multiple snippets in the same location.

= How do I use Google Analytics with WebberZone Snippetz? =

To use Google Analytics with WebberZone Snippetz, go to Settings > WebberZone Snippetz in your WordPress dashboard. Under the Google Analytics tab, enter your Google Analytics tracking ID and enable tracking for your site.

Currently, WebberZone Snippetz only supports Google Analytics 4 code implementation.

= How do I use site verification with WebberZone Snippetz? =

To use site verification with WebberZone Snippetz, go to Settings > WebberZone Snippetz in your WordPress dashboard. Under the Site Verification tab, enter your verification codes for Google, Bing and Pinterest and save the changes.

= How do I add custom CSS code to my site? =

To add custom CSS code to your site, go to Snippets > Add New in your WordPress dashboard. Give your snippet a title and enter your CSS code in the editor. Choose CSS from the dropdown menu and select Header as the location. You can also choose when to display your CSS code based on certain criteria.

== Screenshots ==

1. Snippets Manager
2. General Options
3. 3rd Party Options
4. Header Options
5. Content Options
6. Footer Options
7. Feed Options
8. The display options meta box where you can choose where and when to display your snippets

== Other Notes ==

WebberZone Snippetz is one of the many plugins developed by WebberZone. Check out our other plugins:

* [Contextual Related Posts](https://wordpress.org/plugins/contextual-related-posts/) - Display related posts on your WordPress blog and feed
* [Top 10](https://wordpress.org/plugins/top-10/) - Track daily and total visits on your blog posts and display the popular and trending posts
* [Knowledge Base](https://wordpress.org/plugins/knowledgebase/) - Create a knowledge base or FAQ section on your WordPress site
* [Better Search](https://wordpress.org/plugins/better-search/) - Enhance the default WordPress search with contextual results sorted by relevance
* [Auto-Close](https://wordpress.org/plugins/autoclose/) - Automatically close comments, pingbacks and trackbacks and manage revisions

== Changelog ==

= 2.0.0 =

*Add to All* plugin has now been rebranded to *WebberZone Snippetz*. The plugin code has also been rewritten to use OOP.

* New feature:
	* Add Meta verification. Read how to verify your domain in the [Meta Business Help Centre](https://www.facebook.com/business/help/321167023127050)
	* Snippet priority: New global option to set the priority of when snippets are added to the content. Additionally, the snippet screen allows to set a priority amongst other snippets that add to content
	* Snippet type: New dropdown to select if the snippet is a CSS, JS or HTML snippet. If you select JS or CSS, then the `script` and `style` tags are automatically added
	* New option to add content to the `wp_body_open()` tag

* Enhancements:
	* Google Analytics code has been updated to GA4. Please update your [Google Tag ID](https://support.google.com/analytics/answer/9539598?hl=en)
	* *uninstall.php* now uses `get_sites()` function

* Deprecated:
	* Support for Tynt has been removed. If you are using Tynt, you will need to directly use the full code in the *Footer* tab.

= 1.8.0 =

Release post: [https://webberzone.com/blog/add-to-all-v1-8-0/](https://webberzone.com/blog/add-to-all-v1-8-0/)

* New feature:
	* New constant `ATA_DISABLE_SNIPPETS` can be added to your wp-config.php to disable snippets. Perfect for WordPress multisite
	* New placeholder `%updated_time%` to display the updated date
	* New feature to display the estimated reading time. You can use `%reading_time%` in the Settings page. Alternatively use the shortcode `[ata_reading_time]` to display the reading time

* Enhancements/modifications:
	* Upgraded Settings_API to v2

Check changelog.txt for older entries the [Releases page on Github](https://github.com/WebberZone/add-to-all/releases)

== Upgrade Notice ==

= 2.0.0 =
Multiple new features; Check out the Changelog for more details.
