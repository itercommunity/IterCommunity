<?php
/*
Plugin Name: Sandboxer
Plugin URI: http://etcl.uvic.ca/TBA
Description: This plugin creates subsites in Drupal, WordPress and HTML (aka sandbox projects)
Author: Shawn DeWolfe - mikedewolfe@gmail.com
Version: 0.1
Author URI: http://etcl.uvic.ca/TBA
*/

/*

long phrase = sandboxer
short phrase sboxr

use long phrase for init() call
use short phrase for other function naming builds

*/

define( 'SBOXR_PATH', plugin_dir_path( __FILE__ ) );  

define( 'SBOXR_URL', plugin_dir_url( __FILE__ ) );

define( 'SBOXR_INC', SBOXR_PATH . trailingslashit( 'inc' ), true );

/* Load the variables */
require_once('sandboxer-includes/variables.php');

/* Load the function relevant to the plugin */
require_once('sandboxer-includes/functions.php');

/* Load the scripts files. */
require_once('sandboxer-admin/scripts.php');

function sboxr_register_activation_hook() {
	$dir = SBOXR_PATH.'/assets';
	$sboxr_graft_assets = scandir($dir, 1);

	foreach ($sboxr_graft_assets as $file) {
		$new_location = sboxr_upload_attach_image($dir.'/'.$file, 1);
		update_option('sboxr_'.$file, $new_location);		
	}
}

// Use the register_activation_hook to set default values
register_activation_hook(__FILE__, 'sboxr_register_activation_hook');

// Use the init action
add_action('init', 'sandboxer_init');

/*
position:

2 Dashboard
4 Separator
5 Posts
10 Media
15 Links
20 Pages
25 Comments
59 Separator
60 Appearance
65 Plugins
70 Users
75 Tools
80 Settings
99 Separator
*/

add_action('admin_menu', 'sboxr_admin_menu');

// Use the admin_init action to add register_setting
add_action('admin_init', 'sboxr_admin_init' );

// mail("mikedewolfe@gmail.com", "test", print_r($_POST, TRUE));

?>