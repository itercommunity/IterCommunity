<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$sboxr_tabs = array();
$sboxr_tabs['sandboxer-general'] = array(
	'long_name' => 'Sandboxer Settings', 
	'short_name' => 'Sandboxer', 
	'function' => 'sboxr_general_page', 
	'access' => 'manage_options', 
	'slug' => 'sandboxer-general', 
	'parent' => '',
	'position' => 22,
	'type' => 'menu',
	);
$sboxr_tabs['sandboxer-modules'] = array(
	'long_name' => 'Sandboxer Modules', 
	'short_name' => 'Modules', 
	'function' => 'sboxr_modules_page', 
	'access' => 'manage_options', 
	'slug' => 'sboxr_modules_page', 
	'parent' => 'sandboxer-general',
	'position' => 1,
	'type' => 'submenu',
	);
$sboxr_tabs['sandboxer-docs'] = array(
	'long_name' => 'Sandboxer Docs', 
	'short_name' => 'Documentation', 
	'function' => 'sboxr_docs_page', 
	'access' => 'manage_options', 
	'slug' => 'sandboxer-docs', 
	'parent' => 'sandboxer-general',
	'position' => 2,
	'type' => 'submenu',
	);
$sboxr_tabs['sandboxer-support'] = array(
	'long_name' => 'Sandboxer Support', 
	'short_name' => 'Support', 
	'function' => 'sboxr_support_page', 
	'access' => 'manage_options', 
	'slug' => 'sandboxer-support', 
	'parent' => 'sandboxer-general',
	'position' => -1,
	'type' => 'option',
	);


$sboxr_options = array();
$sboxr_options['release_version'] = "0.1";
$sboxr_options['release_date'] = "November 19, 2014";
$sboxr_options['home_page'] = "http://etcl.uvic.ca/TBA";
$sboxr_options['wordpress_source'] = "n/a";

$sboxr_options['plugin_name'] = "Sandboxer";
$sboxr_options['author'] = "Shawn DeWolfe";

?>