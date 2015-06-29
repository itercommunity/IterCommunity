<?php
/*
Plugin Name: Welcome User Widget
Plugin URI: http://dev.coziplace.com/free-wordpress-plugins/welcome-user-widget
Description:  a simple widget that displays the welcome message plus the user_login of the user. Please <a href="http://wordpress.org/extend/plugins/welcome-user-widget">[Rate]</a> this plugin.
Version: 1.1
Author: Narin Olankijanan
Author URI: http://dev.coziplace.com
License: GPLv2
*/

/* Copyright 2012 Narin Olankijanan (email: narin@dekisugi.net)

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this progam; if not, write to the Free Software Foundation, Inc. 51 Franklin St, Fifth floor, Boston MA 02110-1301 USA
*/

add_action( 'widgets_init', 'dk_create_widgets' );

function dk_create_widgets() {
	register_widget( 'Welcome_User' );
	}
	
class Welcome_User extends WP_Widget {
	function __construct () {
		parent::__construct( 'display_username', 'Welcome User', array ('description' => 'Display Welcome message and the current login_name' ) );
	}
	
	function widget( $args, $instance ) {
	    extract( $args );
        echo $before_widget;    
	    $data = wp_get_current_user();
		if ( is_user_logged_in()) 
		 echo 'Welcome, '.$data->user_login;
		else
		 echo "Welcome, visitor!";
		echo $after_widget;
	}
}
/*EOF*/