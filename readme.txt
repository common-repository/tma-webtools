=== TMA-WebTools ===
Contributors: thmarx
Tags: digital experience platform, targeting, analytics, tracking, product targeting, behaviour targets, woocommerce
Requires at least: 4.4.1
Tested up to: 4.9.5
Stable tag: 1.5.1
License: GPLv3 or later
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=CPLYTLAKYAVLS&lc=DE&item_name=tma%2dwebtools&no_note=1&no_shipping=1&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted

TMA-WebTools is a integration for https://thorstenmarx.com/projekte/webtools/

== Description ==

This plugin is an integration for the webTools-Platform a opensource event analytics and segmentation platform.

Features:

* Tracking of user events
* Scoring for user behaviour
* User segmentation
* Content targeting via shortcodes
* Tracking of WooCommerce events
* Segment simulator in the preview
* Support for WPBakery PageBuilder (formerly Visual Composer)
* Support for Pagebuilder by SiteOrigin
* Support for Elementor Page Builder
* Support for Beaver Builder
* Template-Tag to check if a user match a specific segment ( e.q. tma_is_in_segment("a_segment_id"))
* Replace WooCommerce recommendations by recommendations based on user segments


[youtube https://www.youtube.com/watch?v=ovwScstmPVA]

== Installation ==

This section describes how to install the plugin and get it working.
You need to install webTools from http://thorstenmarx.com/projects/webtools

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Use the "TMA WebTools->Settings" menu to configure the plugin

== Usage ==
After installing the plugin, you have access to the tma_content shortcode to target content in posts and pages for specific user segments.
[tma_content segments='clothing']this content is relevant for users of the segment "clothing"[/tma_content]
More help about using the shortcode, see the TMA-WebTools menu in your wordpress installation.

If you decide to track WooCommerce events, than all complete orders will be tracked. That information can be used to define user segments in the webTools platform.


== Frequently Asked Questions ==

= Can I use the plugin without WebTools? =
No, the plugin integrates WebTools into WordPress. It is not possible to use it without WebTools.

= Is WebTools free? =
Yes, it is. As this plugin, WebTools is licensed under the GPLv3 or later

= Is this addon compatible with other WordPress cache addons like WP Super Cache? =
Yes and no, the problem with most cache plugins is, they produce static html content.
Maybe you can try http://www.xoogu.com/dynamic-shortcodes-plugin-for-wp-super-cache/ to prevent the tma_content shortcode from being cached.
The preferred solution is to disable caching for pages where the shortcodes are used.


== Screenshots ==

1. The configuration panel
2. Tinymce integration
3. Visual Composer integration
4. SiteOrigin PageBuilder integration
5. Target audience selector in the preview


== Known issues ==

1.5.0
 * SiteOrigin PageBuilder has some unreproducable preview issues

== Changelog ==

1.5.1
 * fix Beaver Builder integration
 * fix WPBakery PageBuilder integration

1.5.0
 * Beaver Builder support added
 * Redux Framework removed
 * Extended support vor WPBakery PageBuilder

= 1.4.1 =
 * Fix Elementor preview issue

= 1.4.0 =
 * Add support for Elementor PageBuilder

= 1.3.2 =
 * add missing files

= 1.3.1 =
 * remove debug logging

= 1.3.0 =
 * Update ReduxFramework to 3.6.5
 * Modify MetaData of posts containing segmented content
 * Add targeting to widgets

= 1.2.0 =
 * Add support for cookie domain
 * Add hook for recommendations

= 1.1.1 =
 * MetaBox Bugfix

= 1.1.0 =
 * Use the ReduxFramework for settings
 * Replace WooCommerce recommendation

= 1.0.1 =
 * Tracking should be disabled in the preview
 * Disable tracking in SiteOrigin PageBuilder live editor
 * Disable tracking in VisualComposer frontend editor

= 1.0.0 =
 * Fix js issue with tinymce integration
 * Support for PageBuilder by SiteOrigin
 * Support for Visual Composer
 * New template tag

IMPORTANT: If you upgrade to version 1.0.0 you need at least webTools-Platform version 1.1.0

= 0.10.0 = 
 * Fix error in adminbar
 * MetaBox for scoring
 * Tracking of add/remove item to/from cart
 * Use of post type and slug for unique tracking

= 0.9.0 =
 * Add segment selector to the preview
 * webTools version 0.12.0 is the minimum version

= 0.8.0 = 
 * Hook to integration custom configuration into the TMA_CONFIG Json
 * TMA_Request class extended to call extension rest endpoints
 * fix issue with webtools rest api

= 0.7.0 =
* usage of new tracking of custom attributes
* enable/disable tracking for logged in users
* tracking of product ids for orders
* tinymce button for shortcodes

If you update to this version you need at least version 0.9.0 of the webTools-Platform.


= 0.6.0 =
* add scoring only if single page of post is shown

= 0.5.0 =
* Fix cookie issue

= 0.4.0 =
* Tracking of WooCommerce events

= 0.3.0 =
* Fix minor issue with unhandled NULL value
* add translation

= 0.2.0 =
* ShortCodes for content targeting by user segments

= 0.1.0 =
* Disable/Enable tracking and scoring.

== Upgrade Notice ==
 * since version 0.10.0 the post type is used to generate a unique page id, if you use pageview for segmentation in the webTools-Platform, you have to update your rules to <post_type>#<post_slug>.
 * version 0.10.0 you need at least webTools version 0.14.0
 * For version 0.8.0 you need at least webTools version 0.11.0
