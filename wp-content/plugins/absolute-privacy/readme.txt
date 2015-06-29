=== Plugin Name ===
Contributors: johnkolbert, ericmann
Tags: privacy, registration, moderation
Requires at least: 3.0
Tested up to: 3.6
Stable tag: 2.1
License: GPLv2

New user moderation, site lock-down or members area, users can chose their own password and must enter their name when registering.

== Description ==

IMPORTANT: v2.0+ does NOT have multisite support. The plugin will NOT protect any of your sites. I hope to include official multisite support in a future release.

After having a few odd registrations and comments on our family blog, my wife asked me to create a plugin that would give the blog security from strangers but still be easily accessible to family and friends. Absolute Privacy does just that! 

Absolute Privacy turns your WordPress blog into a fully private site where you control who has access. It's perfect for family blogs, private communities, and personal websites. You can lockdown the entire site or create a members only area, moderate new registrations, force users to enter first/last name and choose their password during sign up, and more!

Your site is now absolutely private!

== Installation ==

Simply upload to your wp-content/plugins folder and activate from the plugins menu. Nothing else to it!

== Frequently Asked Questions ==

Why can't users register?
Don't forget to enable user registration in the WordPress general options menu.

How do I use the shortcakes?
Simply enter the shortcode into the visual editor, brackets and all. Eg: [profilepage]

Where can I find more documentation?
Visit the plugins homepage

== Screenshots ==
1. A view of the added registration form fields
2. Custom error for non-approved user trying to login
3. Custom moderation menu to easily approve or delete all unapproved users.
4. Approval is easy: click the link in the approval pending email and press approve

== Changelog ==

= 2.1 =
* Add I18N
* Fix some deprecation notices

= 2.0.7 =
* fixed bug in authentication.

= 2.0.6 =
* fixed immediate security issue.
* fixed several PHP array index notices.
* fixed several deprecated function warnings.

= 2.0.5 =
* fixed styling issue with admin notices
* fixed bug where some users could not access admin page

= 2.0.4 =
* fixed bug where upgrading from v1.3 to 2.0.x would lose some settings
* fixed bug where members only lockdown method would lockdown the entire site
* fixed bug where those upgrading from v1.3 were unable to approve users in the moderate menu

= 2.0.3 =
* fixed inability to transfer old plugin settings from v1.3

= 2.0.1 =
* fixed fatal error due to bad file reference

= 2.0 =
* 3.0.x+ required
* Complete code rewrite. I rewrote the entire plugin from object oriented to procedural as this made more sense to me for this plugin. It should be much easier for others to modify and read through the code. No need for overkill here.
* Two different privacy methods: complete lockdown and members area. Complete lockdown functions like the plugin did previously, the entire site will be password protected. Members area allows you protect a specific page (and all subpages).

= 1.3 = 
* added ability to control RSS feeds. Feeds can be disabled, enabled, limited to headlines, or limited to a predefined number of characters
* added ability to allow non-logged in users to view specific pages
* added ability to redirect non-logged in users to the login screen OR any page or post
* added ability to customize email notification messages 

= 1.2 =
* fixed XML-RPC authentication for iPhone app and remote publishing tools
* added ability to prevent subscribers from accessing any admin pages (such as their profile or the dashboard)

= 1.1 = 
* Fixed bug where some users in Internet Explorer couldn�t login
* Added WordPress v2.8 compatibility

= 1.0 =
* Initial stable release



