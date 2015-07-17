=== Plugin Name ===
Contributors: hikingnerd
License: GPLv2 or later
Donate link: http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#Contribution
Tags: owncloud, Bookmarks, api, integration, shortcode, widget
Requires at least: 3.9.2
Tested up to: 4.2.2
Stable tag: 1.1.0

Embed your Bookmarks that are managed by ownCloud in your WordPress posts and pages as table or as list within widget areas.

== Description ==
This plugin allows you to make use of your ownCloud bookmarks in WordPress posts, pages or widgets. You can:

* Make use of the ownCloud Bookmarks App (<a href="https://github.com/owncloud/Bookmarks" target="_blank">Link to the newest version</a>).
* Access the ownCloud database after configuring it like described <a href="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#MySQL mode" target="_blank">in this tutorial section</a>.
* Use the shortcode [ oc2wpbm] to generate tables that contain ownCloud Bookmarks that ared tagged with 'public'.
* Use the shortcode [ oc2wpbm tag="example"] to generate tables that contain ownCloud Bookmarks that ared tagged with 'example'.
* Use the shortcode [ oc2wpbm tag="example, public"] to generate tables that contain ownCloud Bookmarks that ared tagged with 'example' or 'public'.
* Use the shortcode [ oc2wpbm tag="example, public" connector="AND"] to generate tables that contain ownCloud Bookmarks that ared tagged with both: 'example' AND 'public'.
* Configure the <a href ="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#configure the table layout" target="_blank">layout of the table</a>.
* Display a list of bookmarks out of your owncloud instance in the widget areas by using the widget 'ownCloud Bookmarks' that can be found in the backend menu of your WordPress instance under /Design/Widgets

find more <a href ="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/" target="_blank">in this tutorial</a>


== Installation ==
1. Decide for one <a href="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#Preconditions & Installation">operation mode</a>
1. If you wish to make use of the ownCloud App operation mode ensure that on your ownCloud server php5-curl is running and that the ownCloud Bookmarks App <a href="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#Replace the ownCloud Bookmarks App" target="_blank">is supporting the REST API (> ownCloud Version 8.0)</a>
1. Download and copy the plugin into the folder `/wp-content/plugins/` of your WordPress instance
1. Activate the plugin by making use of the /Plugin area in the WordPress backend menu.
1. go to /Settings/OC2WP Bookmarks and configure the operation mode of the plugin
1. put the shortcode [ oc2wp] into the page or post that should contain a table of bookmarks that are tagged with 'public'
1. go to /Design/Widgets and drag & drop the ownCloud Bookmarks Widget to the widget area where a list of Bookmarks should be displayed. <a href="http://www.nolte-netzwerk.de/oc2wp-bookmarks-configuration/#widget" target="_blank">Configure the widget </a>.

== Frequently Asked Questions ==

= Are there preconditions that my ownCloud instance has to satisfy? =

The server running your owncloud instance needs to run php5-curl. Furthermore you need to activate the Bookmarks App in your ownCloud instance which supports the REST API (> ownCloud 8.0 or <a href="https://github.com/owncloud/Bookmarks" target="_blank">see here</a> ).

= Which operation mode is appropriate? =

In general it is recommended to use the ownCloud App mode. The MySQL mode only is for those appropriate who whish to access the Bookmarks of all users of an ownCloud instance or those that cannot change the ownCloud Bookmarks App.

= How to configure the OC mode =

Enter the credentials of the ownCloud account that owns the Bookmarks that should be published. 

= Language =
This plugin is currently only available in English but you can set the title of the generated tables  and of the widget to your own needs in your own language.

= What are the shortcodes to embed a table containing the ownCloud Bookmarks into posts or pages? =
* embed those Bookmarks that are tagged with 'public': [ oc2wpbm]
* embed those Bookmarks that contain one out of a set of tags (in this case 'public' or 'example'): [ oc2wpbm tags=”public, example”] 
* embed those Bookmarks that contain a specific set of Bookmars (in this case 'public' AND 'example'): [ oc2wpbm tags=”public, example” connector=”AND”]

= Can the content of the widget be customized? =
Specifying tags in the field 'Tags of bookmarks to be displayed' will list all those bookmarks that contain one of the tags specified. If only those bookmarks should be displayed that contain all of those tags activate the checkbox 'Bookmark has to contain all tags (AND connector)'. Furthermore you can determine the lenght of the list and additional content like tags or the description (e. g. displayed as mouseover text).

= Can the layout of the widget be customized? =
In the style.css of your theme you can use the classes oc2wp-row, oc2wp-row-title and oc2wp-description to set the layout.

= What are the next steps for this plugin =
Currently I am working to enhance the sorting capabilites. Further suggestions are welcome!


== Screenshots ==
1. oc2wp Settings
2. ownCloud Plugin
3. Resulting Table enhanded by TablePress
4. Widget configuration
5. Widget with description of the Bookmarks as mouseover text


== Changelog ==
= 1.1.0 =
* Added functionality for widgets.
= 1.0.0 =
* Very first version enabling to connect via SQL or the ownCloud Plugin REST API to the ownCloud instance using the tags.

