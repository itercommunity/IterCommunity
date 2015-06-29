<?php
/*
Plugin Name: WP Curtain
Plugin URI: http://wpgurus.net/
Description: WP Curtain is a simple plugin that allows you to hide your website from the general public and display an elegant countdown timer.
Version: 0.1
Author: Hassan Akhtar
Author URI: http://wpgurus.net/
License: GPL2
*/

function wpc_redirect() {
	$wpc_settings = get_option("wpc_settings");
	if ( !is_user_logged_in() || !current_user_can($wpc_settings['minimum_role']) ){
		include('template.php');
		exit();
	}
}
add_action('template_redirect', 'wpc_redirect', 1);

function wpc_scripts_styles(){
	$wpc_settings = get_option("wpc_settings");
	wp_enqueue_style('wpc-stylesheet', plugins_url( 'static/css/style.min.css' , __FILE__ ) );
	if(!$wpc_settings['disable_timer'] && $wpc_settings['future_date'])
		wp_enqueue_script('flipclock', plugins_url( 'static/js/flipclock.package.min.js' , __FILE__ ), array('jquery'));
}
add_action('wp_enqueue_scripts', 'wpc_scripts_styles', 1);


function wpc_initialize_options(){
	$wpc_settings = get_option("wpc_settings");
	if($wpc_settings)
		return;
		
	$wpc_settings['minimum_role'] = 'install_plugins';
	update_option('wpc_settings', $wpc_settings);
}
register_activation_hook(__FILE__, 'wpc_initialize_options');

function wpc_rollback(){
	delete_option('wpc_settings');
}
register_uninstall_hook(__FILE__, 'wpc_rollback');

include('options-panel.php');