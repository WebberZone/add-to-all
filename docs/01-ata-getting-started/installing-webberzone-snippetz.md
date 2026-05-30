---
slug: installing-webberzone-snippetz
title: "Installing WebberZone Snippetz"
products: [add-to-all]
sections: [01-ata-getting-started]
tags: [add-to-all,installation,snippetz]
status: publish
order: 0
---

WebberZone Snippetz (formerly Add to All) is available in the WordPress.org plugins repository. This means you can search for and install the plugin directly from within your WordPress site’s dashboard.

## WordPress install (the easy way)

1.  Navigate to Plugins within your WordPress Admin Area
2.  Click “Add new” and enter “WebberZone Snippetz” in the search box.
3.  Find the plugin in the list (usually the first result) and click “Install Now”.

## Manual install

1.  Download the plugin
2.  Extract the contents of where-did-they-go-from-here.zip to the **/wp-content/plugins** folder. You should get a folder called **add-to-all**.
3.  Activate the Plugin “WebberZone Followed Posts” in WP-Admin.
4.  Go to **Snippetz » Settings** to configure.

## Installing via WP CLI

If you’re using [WP CLI](http://wp-cli.org/), you can install and activate this plugin by running:

``` wp-block-code
wp plugin install add-to-all --activate
```

This plugin can also be network-activated using the following:

``` wp-block-code
wp plugin install add-to-all --activate-network
```
