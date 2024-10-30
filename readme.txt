=== Automated Content for Real Estate ===
Contributors: reblogdog, kevin-brent
Donate link: https://reblogdog.com/donate/
Tags: Real Estate, Realtor, Automated Content, IDX, MLS, Blogging, Listings, RETS, ihomefinder, dsidxpress, simplyrets, placester, ifoundagent, diverse solutions
Requires at least: 4.0
Tested up to: 6.0
Requires PHP: 5.6
Stable tag: /trunk
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Get a new real estate blog post automatically published website everyday. Share the blog post to your social media.

== Description ==

Add automated real estate content to your website. We publish an SEO optimized blog post to your website for you everyday. The post includes an SEO optimized image and content. We also include a list of current listings to match the keywords for the blog post. The listings are updated every time your MLS updates.

Social Media can be intimidating. We understand that. That's why we have made each Blogdog post sharable. Oh, and it also includes a featured image. Professional in look that is sure to draw attention. 

NOTE: 
We reccommend using WordPress SEO, a.k.a. Yoast SEO for Facebook Open Graph and Twitter cards. As well as your SEO. And, Yes, they have a FREE version of the plugin that works great.

A content subcription with Real Estate Blogdog is required to use this plugin. 

Check our website for the most current list of [Featured IDX Providers](https://reblogdog.com/featured-idx-providers/ "Featured IDX Providers").

The `ifoundagent` extended video is hosted on the `ifoundagent` servers.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/blogdog` directory, or install the plugin through the WordPress plugins screen directly.
2. Navigate to WP Admin > Automated Content.
3. Visit our website to purchase your subscription. [Purchase Now](https://reblogdog.com/order-form// "Buy Now Page")
4. Check your email, we will send you your api key.
5. Paste your api key into the Blogdog Settings page API Key input.
6. Select your shortcode type. 
7. Input user settings. Make sure to press Save Settings.
8. Map only the property types you plan to use. (Click the [How-To](https://reblogdog.com/map-property-types/ "How to Map Property Types") link for instructions.) Make sure to press Save Settings.
9. City mapping may be required. (Check your IDX at [Featured IDX Providers](https://reblogdog.com/featured-idx-providers/ "Featured IDX Providers") to verify. If required. [Contact Support](https://reblogdog.com/support/ "Contact Support") we will handle this for you.
10. Community Mapping may be required. [Contact Support](https://reblogdog.com/support/ "Contact Support"). They will handle this part for you.
11. Add the loactions you plan to use. (These can be updated at any time.)
12. Choose the criteria for each location.
13. Make sure to press Save Settings before leaving page.
14. Click the Active API button. If the set-up is complete, the button will read API Activated. 
15. Check your website blog for a few days. If you see any issues with your blog posts. [Contact Support](https://reblogdog.com/support/ "Contact Support"). We are happy to help.

== Frequently Asked Questions ==

= Do I need a subscription? =

Yes. You need a subscription with Real Estate Blogdog to receive automated content. To display listings with a shortcode (optional), you will need an account with one of our featured IDX providers.

= Are any contracts required to use the Blogdog Automated Posting service? =

No. You can cancel at any time.

== Screenshots ==

1. 1. **Sample Blog Post** - Every blog post is different and contains the location information you provide. This is a sample using Scottsdale AZ as the location.
2. Dashboard
3. API settings - You will receive the API key when you start your subscription.
4. Location Settings - Unlimited locations are allowed. This information can be updated at any time depending on your changing market conditions.
5. Property Type Mapping - Detailed Instructions are provided for this section. You will only need to do this once.

== Changelog ==

= 9.3.1 =
* Update - WordPress compatibility

= 9.3 =
* Bug Fix - remove deprecated action.

= 9.2 =
* Improvement - add logging for push request
* Tweek - update readme

= 9.1.2 =

* Remove depricated plugin specific functionality.

= 9.1.1 =

* Update to placement of API request. 

= 9.1 =

* Remove Elite settings

= 9.0.7 =

* Remove community mapping

= 9.0.6 =

* Set transient for elite settings.

= 9.0.5 = 

* Require admin PHP for post_exists function

= 9.0.4 =

* Hide mapping admin until API is active.

= 9.0.3 =

* Bug fix - zipcode ajax request.
* Update conditions for Elite Automation posts.

= 9.0.2 =

* Bug fix - Wrong options key for api_key in blogdog API push.
* Verify title does not exists for Elite Automation posts.

= 9.0.1 =

* Bug fix - API Key not saving for new accounts

= 9.0.0 =

* Update to use blogdog REST API.
* Update API connections.
* Redesign blogdog admin.
* Add Elite Automation for use with iFound plugin.
* Add Tips Content for use with iFound plugin.
* Add API support for Articles.
* Remove Property Type mapping for Profound plugin.
* Add API support for Property Type mapping for Profound plugin.

= 7.0.1 =

* Bug fix - Storing locations json in separate transients.

= 7.0.0 =

* Massive rewrite of most of the plugin.
* Update API to v3.0.0

= 6.0.0 =

* Removed Sidebars Functionality
* Added support for iFound Real Estate IDX plugin


= 5.6.9 =

* Add functionality to check that posts are scheduled correcrly.

= 5.6.8 =

* Minor bug fix in admin.php

= 5.6.7 =

* Add filter to limit shortcode types display.

= 5.6.6 =

* Update Admin functionality for better user experience.
* Update Blogdog API v2.3.3.
* Remove `wp_cron` for scheduling post delivery.
* Add REST API push for scheduling post delivery.
* Replace dashicons with font awesome.
* Change API response from array to object.
* Add community mapping.

= 5.6.5 =

* minor bug fix

= 5.6.4 =

* Comvine Dewey and Humboldt for Prescott AZ mls to Dewey-Humboldt
* Restrict subdivision to PAAR AZ and ARMLS in ifoundagent extended options.

= 5.6.3 =

* Update API to v2.3.2 to include Closed listings for iFoundagent

= 5.6.2 =

* Condense auto_content shortcode to single method.
* Update sidebar descriptions for Auto Content sidebars.

= 5.6.1 =

* Rename sidebars from Blogdog to Auto Content for resellers.

= 5.6.0 =

* Add shortcodes to templates to display widget content.
* Add class .clear to blogdog css clear:both.

= 5.5.3 =

* Add Luxury home options to mapping and property typea. 
* Update API v2.3.1

= 5.5.2 =

* Add missing zip codes. This update includes over 10000 missing zip codes.

= 5.5.1 =

* Bug fix for case sensitive replacement of contact anchor.
* Add none value to Contact page select.

= 5.5.0 =

* Update api to v2.3.0 to add 9 page templates.
* Include templates in plugin.
* Update API to not incude HTML in POST response.
* Allow sanitize text from API response.
* Include selection option for Contact page url.

= 5.4.3 =

* Update api to v2.2.0 to add multiple page templates.
* Security Updates to API in v2.2.0 

= 5.4.2 =

* Add missing other missing zipcodes in json.
* Update additional admin css to overide other plugin css issues.

= 5.4.1 =

* Add missing Scottsdale AZ zipcode 85266 to json.
* Update admin css to overide other plugin css issues.

= 5.4.0 =

* Add zipcode to city location headings for better sorting.
* Add option to deactivate location tabs without deleting location.

= 5.3.6 =

* Remove `ifoundagent` extend video from plugin to maintain plugin file size.

= 5.3.5 =

* Debug inconsistant detection of plugin by adding condition `class_exists( 'ProFoundMLS' )` in `ifoundagent` extension.

= 5.3.4 =

* Add WPMU capibility to `ifoundagent` extended.

= 5.3.3 =

* Create the `blogdog_extend_ifoundagent` Class to extend option and compatibility with the `ifoundagent` IDX plugin.
* Move all existing methods for `ifoundagent` extensions to the new Class

= 5.3.2=

* Move `update_option` to fire earlier for API activation/deactivation.

= 5.3.1=

* Replace `delete_option` with `update_option` for API activation/deactivation.

= 5.3.0 =

* Added functionality to prevent API deactivation on plugin update.
* Added location heading functionality in admin/admin.js

= 5.2.6 =

* Add custom options for I Found Agent IDX
* Update api to v2.1.6

= 5.2.3 =

* Add additional Cnandler AZ zip codes

= 5.2.2 =

* Add method to hide `[reblogdog_cta]` from posts used with RE_blogdog v1.0 
* RE_blogdog v1.0 is now completely depricated.

= 5.2.1 =

* Due to missing files in version 5.2.0 svn update we are doing this update to insure all user recieve the complete plugin.

= 5.2.0 =

* Full overhaul of the admin.
* Add new API version

= 5.0.1 =

* Change how cron is avtivated and deactivated. 
* Fix layout issue with update messages in admin.
* Add API Activatte button in admin.

== Upgrade Notice ==

= 9.0.0 =

* Critical Update Required. New API vesion. Blogdog API v3.0.0 has been REMOVED.

= 7.0.0 =

* Critical Update Required. New API vesion. Blogdog API v3.0.0 is depricated and will be removed very soon.

= 5.6.5 =

* Critical Update Required. New API vesion. Blogdog API v2.3.2 is depricated and will be removed.

= 5.6.4 =

* Minor updates to ifoundagent extended options.

= 5.5.3 =

* Minor Update. This update is not required unless you use the keyword Luxury.

= 5.5.0 =

* Critical Update. Due to security issue, All api versions before v2.3.0 will be depricated and removed.

= 5.4.3 =

* Critical Update. All api versions before v2.2.0 will be depricated soon.

= 5.3.0 =

* Minor update to prevent Api Deactivation at plugin update
 
= 5.2.6 =

* Minor update for I Found Agent users only. This is not a required update.

= 5.2.2 =

* Minor update for RE_blogdog v1.0 users only. This is not a required update.

= 5.2.1 =

* New API version Update Required.
