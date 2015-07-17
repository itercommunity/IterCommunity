=== BP User Profile Map ===
Contributors: hnla AKA Hugo 
Tags: buddypress, profiles, map, profile map, location
Requires at least: BuddyPress 1.5
Tested up to: WP 3.6, BuddyPress 1.8.1
Stable tag: 1.4.2

Add a map display of a members location to their BuddyPress profile.

== Description ==
A simple user location  map plugin for BuddyPress sites combined with a separate function  and shortcode to enable standalone maps in posts/pages and embedded in template files.

Add Google maps to display a members Location. Maps are displayed in the users profile / account page as well as member directory listing or group members listing if enabled.

A widget is available to allow finer control of map placement in custom widget areas that may exist. At present the widget works only for members profile areas and does not have the range of options available to the other maps. 

A nod to Andy Peatling for the inspiration of the original map php code.

This version  replaces the Google API with the V3 which benefits from not requiring an API key.

The admin settings page allows  maps to be assigned dimensions as well as basic options for map overlay controls and to define which area of a page maps should display in by selecting page action hooks.

Maps can be set to display on both the BP members loop and the groups loop directories as well as the user account screens.

A standalone function and shortcode are provided  to allow embedding single maps in templates or for calling from a post or page via shortcode.

Important: You must have set up an extended profile field for members to use to add their location.  The location can be either set in the 'Base' profile group in which case it will appear on the signup page or you can
create a new group and have the field display on the members profile settings only. The maps will only display once the member has added their location to this new field.

== Installation ==

* Upload the directory '/bp-user-profile-map/' to your WP plugins directory and activate from the Dashboard of the main blog.
* Configure the plugin at Dashboard > BuddyPress > UPM Setup or Dashboard > Settings > UPM Setup.
* Presently you can set map dimensions plus map overlay options and select BP screens such as 'Members', 'Groups' or 'Profile'.
* A widget allows you to place a map in a widget aware region such as sidebar and displays the current displayed user
* A function and shortcode provision allows maps in templates and rendered through posts/pages
* To display a map in the members profile screen within the location field requires placing a specific function call in the template loop, instructions are in the settings page.

 == Frequently Asked Questions ==
 None

 == Upgrade Notice ==
	1.4.2 Removes check for BP default theme for stylesheet loading order, returning true even if BP theme compatibility layer in use. Minor updates to widget - remove title/markup if no user location.
   
 == Screenshots ==
1. A view of a small map displayed floated right in the members public profile page.
2. Map showing the infowindow popup with 'get directions' link.
3. A view of a BuddyPress members list with small profile maps for each member

== Changelog ==
** V 1.4.2 ** 27/08/13

- Minor adjustments for BP theme compatibility - removing stylesheet enqueueing check for bp default theme as this returns true & styles with a loading based on bp-default stylesheet fails.
- Remove unwanted functions and commented out code.
- Update profile widget, remove widget markup if no user location set.

** V 1.4.1 ** 29/07/12

- Revised approach to options screen - corrects issue of not displaying correctly on WP MS installs, option screen now no longer displays under BP but under WP general settings.

** V 1.4 ** 29/04/12

- Add gmap infowindow map marker to display a 'Get Direction' link.
- Add a new ability to display user profile map directly in a location profile field (requires adding function to loop).
- Corrects friends screen in user account from showing account users address instead of individual ones.
- Corrects or provides ability to size the user account map in member header independently of the friends list maps.
- Adds various admin options for control of new functions
- Adds additional class tokens for styling and span/incremented classes for address lines.
- Add new args for standalone function and shortcode maps to turn on/off map address titles.
- add missed text strings to translation files.
- Moves settings screen to wp 'Settings' in accordance with BP 1.6 admin changes
 
** V 1.3 ** 16/01/12
1.3 is a major re-write and requires BP 1.5 as a minimum it adds a number of new capabilities for Buddypress lists and for embedding map function in WP pages and additional option settings.

- Add ability to show maps on BP members directory.
- Add ability to show maps on BP group member list.
- Added user set field for Xprofile field name - user can set a name of their choice for location.
- Provided ability to use map function as standalone passing through location string via parameter.
- Provided Shortcode capability for post/page content body.
- Added new options in dashboard settings for additional map displays, provide ability to switch displays on/off, switch off styles, change width to 'auto' or percentage.
- Cleaned up and improved settings page.

** V 1.2.1 ** 13/03/11

- Corrected version number update

** V 1.2 ** 13/03/11

- Plugin updated to work with WP 3.1 and BP 1.3. bleeding edge
- Checked to work with WP 3.0.* stream BP 1.2.7 /1.2.8 and generally for  multisite or single install.
- Checked to work for BP 1.2.8 / 1.3 bleeding edge.
- General tidy up of admin screens and fixes for display region checkbox deafult value 'none'.
- Added class to parent div depending on display area selected to improve flexibility in styling.

** V 1.1 ** 27/12/10
Version introduces further admin options for configuring the map overlay controls.

- Turn off the map navigation overlay (zoom, pan).
- choose to display navigation overlay in 'Small' mode regardless of map size.
- Select 'dropdown' as the primary Map Type selection display regardless of map size.
- Set the preferred initial map zoom value (defaults to 11 ).

** V 1.0.1 ** 13/12/10 
Minor bug fixes and author induced errors

- Corrected bad link in admin settings page pointing to BP profile field setup

- Corrected issue with passing variable values for width and height inline styles - out of function scope.

** V 1.0 **  06/12/10
Stable initial release
