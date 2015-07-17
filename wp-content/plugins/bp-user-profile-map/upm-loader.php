<?php
/*
Plugin Name: BP User Profile Map
Description: Display a google map in BuddyPress members profile, or maps for Members Directory or Group members list.
Author: Hugo - aka hnla hugo.ashmore@gmail.com
Author URI: http://buddypress.org/developers/hnla
Plugin URI: http://buddypress.org/groups/BP-User-Profile-Map
Version: 1.4.2
Network: true
License: CC-GNU-GPL http://creativecommons.org/licenses/GPL/2.0/
*/


/* Only load the plugin if BP is loaded and initialized. */
function bp_upm_init() {
	require( dirname( __FILE__ ) . '/upm.php' );
}
add_action( 'bp_include', 'bp_upm_init' );
/* end stuff for this file */
?>