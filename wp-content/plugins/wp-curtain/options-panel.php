<?php
/*
The settings page
*/

function wpc_theme_menu() {
	global $wpc_settings_page_hook;
    $wpc_settings_page_hook = add_plugins_page(  
        'WP Curtain Settings',            			// The title to be displayed in the browser window for this page.
        'WP Curtain Settings',            			// The text to be displayed for this menu item
        'administrator',            				// Which type of users can see this menu item  
        'wpc_settings',    							// The unique ID - that is, the slug - for this menu item  
        'wpc_render_settings_page'     				// The name of the function to call when rendering this menu's page  
    );
}
add_action( 'admin_menu', 'wpc_theme_menu' );

function wpc_cp_scripts_styles($hook) {
	global $wpc_settings_page_hook;
	if( $wpc_settings_page_hook != $hook )
		return;
	wp_enqueue_style("options_panel_stylesheet", plugins_url( "static/css/options-panel.css" , __FILE__ ), false, "1.0", "all");
	wp_enqueue_style('jquery-style', plugins_url( "static/css/jquery-ui.css" , __FILE__ ));
	wp_enqueue_script("options_panel_script", plugins_url( "static/js/options-panel.js" , __FILE__ ), false, "1.0");
	wp_enqueue_script("jquery-ui-datepicker");
}
add_action( 'admin_enqueue_scripts', 'wpc_cp_scripts_styles' );

function wpc_render_settings_page() {
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"></div>
<h2>WP Curtain Settings</h2>
<?php settings_errors(); ?>
<div class="cp-flex-box">
	<div class="sections-wrap">
		<form method="post" action="options.php">
			<?php settings_fields( 'wpc_settings' ); ?>
			<?php do_settings_sections( 'wpc_settings' ); ?>
			</div></div> <!--For the last section-->
		</form>
	</div>
	<div class="cp-sidebar"><span>Need help?</span><br/><br/><a class="cp-button" target="_blank" href="http://wpgurus.net/hire-me/">Hire Us</a></div>
</div>
<?php }

function wpc_initialize_theme_options() { 
	
	add_settings_section(  
        'content_settings_section',
        '',
        'wpc_content_settings_section_callback', 
        'wpc_settings' 
    );
	
	add_settings_section(
        'timer_settings_section',
        '',
        'wpc_timer_settings_section_callback', 
        'wpc_settings' 
    );
	
	add_settings_section(  
        'login_settings_section', 
        '',
        'wpc_login_settings_section_callback',
        'wpc_settings'
    );
	
	add_settings_field(
        'page_title', 'Page Title', 'wpc_render_settings_field', 'wpc_settings', 'content_settings_section',
		array(
			'desc' => 'This will go in the head section of the page',
			'id' => 'page_title',
			'type' => 'text',
			'group' => 'wpc_settings'
		)
    );
	
	add_settings_field(
        'page_heading', 'Page Heading', 'wpc_render_settings_field', 'wpc_settings', 'content_settings_section',
		array(  
			'desc' => 'The main heading of the page',
			'id' => 'page_heading',
			'type' => 'text',
			'group' => 'wpc_settings'
		)
    );
	
	add_settings_field(
        'page_description', 'Page Description', 'wpc_render_settings_field', 'wpc_settings', 'content_settings_section',
		array(  
			'desc' => 'Short paragraph that\'ll go under the heading',
			'id' => 'page_description',
			'type' => 'textarea',
			'group' => 'wpc_settings'
		)
    );
	
	add_settings_field(
        'disable_timer', 'Hide countdown timer?', 'wpc_render_settings_field', 'wpc_settings', 'timer_settings_section',
		array(
			'desc' => 'Check if you don\' want to display the countdown timer',
			'id' => 'disable_timer',
			'type' => 'checkbox',
			'group' => 'wpc_settings'
		)
    );
	
	add_settings_field(
        'future_date', 'When will your website be online?', 'wpc_render_settings_field', 'wpc_settings', 'timer_settings_section',
		array( 
			'name' => 'When will your website be online?',
			'desc' => 'Enter the expected date when your website will be online',
			'id' => 'future_date',
			'type' => 'datetimepicker',
			'group' => 'wpc_settings'
		)
    );
	
	add_settings_field(
        'disable_login_box', 'Hide login box?', 'wpc_render_settings_field', 'wpc_settings', 'login_settings_section',
		array(
			'desc' => 'Check if you don\' want to display a login box. <b>The login section will automatically be hidden from logged in users</b>',
			'id' => 'disable_login_box',
			'type' => 'checkbox',
			'group' => 'wpc_settings'
		)
    );
	
	add_settings_field(
        'redirect_url', 'Redirect URL', 'wpc_render_settings_field', 'wpc_settings', 'login_settings_section',
		array(
			'desc' => 'Users will be sent here after login. Leave empty to send them to the page that they tried to access.',
			'id' => 'redirect_url',
			'type' => 'text',
			'group' => 'wpc_settings'
		)
    );
	
	add_settings_field(
        'minimum_role', 'Minimum access level required to view website', 'wpc_render_settings_field', 'wpc_settings', 'login_settings_section',
		array(
			'desc' => 'What\'s the minimum role that a logged in user needs to have in order to access your website',
			'id' => 'minimum_role',
			'type' => 'select',
			'options' => array("all" =>"Hide from everybody", "install_plugins" => "Administrator", "moderate_comments" => "Editor", "edit_published_posts" => "Author", "edit_posts" => "Contributor", "read" => "Subscriber"),
			'group' => 'wpc_settings'
		)
    );
	
    // Finally, we register the fields with WordPress 
	register_setting('wpc_settings', 'wpc_settings', 'wpc_settings_validation');
	
} // end sandbox_initialize_theme_options 
add_action('admin_init', 'wpc_initialize_theme_options');

function wpc_settings_validation($input){
	$allowed_html = array(
						'a' => array(
							'href' => array(),
							'title' => array()
						),
						'b' => array(),
						'em' => array(),
						'i' => array(),
						'strong' => array()
					);
	$input['page_title'] = esc_attr($input['page_title']);
	$input['page_heading'] = wp_kses($input['page_heading'], $allowed_html);
	$input['page_description'] = wp_kses($input['page_description'], $allowed_html);
	$input['future_date']['date'] = wp_kses($input['future_date']['date'],array());
	$input['future_date']['hh'] = intval($input['future_date']['hh']);
	$input['future_date']['mm'] = intval($input['future_date']['mm']);
	$input['future_date']['ss'] = intval($input['future_date']['ss']);
	$input['redirect_url'] = esc_url($input['redirect_url']);
	return $input;
}

function wpc_content_settings_section_callback(){
	echo '<div class="section"><div class="section-title"><h3>Content</h3>';
	submit_button('Save Changes', 'secondary','submit', false);
	echo '<div class="clearfix"></div></div><div class="section-options">';
}

function wpc_timer_settings_section_callback(){
	echo '</div></div><div class="section"><div class="section-title"><h3>Countdown Timer</h3>';
	submit_button('Save Changes', 'secondary','submit', false);
	echo '<div class="clearfix"></div></div><div class="section-options">';
}

function wpc_login_settings_section_callback(){
	echo '</div></div><div class="section"><div class="section-title"><h3>Login Section</h3>';
	submit_button('Save Changes', 'secondary','submit', false);
	echo '<div class="clearfix"></div></div><div class="section-options">';
}

function wpc_render_settings_field($args){
	$option_value = get_option($args['group']);
	
	if($args['type'] == 'text'){
?>
		<input type="text" id="<?php echo $args['id'] ?>" name="<?php echo $args['group'].'['.$args['id'].']'; ?>" value="<?php echo esc_attr($option_value[$args['id']]); ?>">
		<small><?php echo $args['desc'] ?></small>
<?php
	}
	else if ($args['type'] == 'select')
	{ 
?>
		<select name="<?php echo $args['group'].'['.$args['id'].']'; ?>" id="<?php echo $args['id']; ?>">
			<?php foreach ($args['options'] as $key=>$option) { ?>
				<option <?php selected($option_value[$args['id']], $key); echo 'value="'.$key.'"'; ?>><?php echo $option; ?></option><?php } ?>
		</select>
		<small><?php echo $args['desc']; ?></small>
<?php	
	}
	else if($args['type'] == 'checkbox')
	{
?>		
		<input type="hidden" name="<?php echo $args['group'].'['.$args['id'].']'; ?>" value="0" />
		<input type="checkbox" name="<?php echo $args['group'].'['.$args['id'].']'; ?>" id="<?php echo $args['id']; ?>" value="1" <?php checked($option_value[$args['id']]); ?> />
		<small><?php echo $args['desc']; ?></small><div class="clearfix"></div>
<?php
	}
	else if($args['type'] == 'textarea')
	{
?>
		<textarea name="<?php echo $args['group'].'['.$args['id'].']'; ?>" type="<?php echo $args['type']; ?>" cols="" rows=""><?php if ( $option_value[$args['id']] != "") { echo stripslashes(esc_textarea($option_value[$args['id']]) ); } ?></textarea>
		<small><?php echo $args['desc']; ?></small><div class="clearfix"></div>
<?php
	}
	else if($args['type'] == 'datetimepicker')
	{
?>
		<label for="<?php echo $args['id'].'_date'; ?>">Date:</label> <input type="text" style="width:130px;" id="<?php echo $args['id'].'_date'; ?>" name="<?php echo $args['group'].'['.$args['id'].']'.'[date]'; ?>" value="<?php echo esc_attr($option_value[$args['id']]['date']); ?>">
		<label for="<?php echo $args['id'].'_hh'; ?>">HH:</label> <input type="text" style="width:25px;" id="<?php echo $args['id'].'_hh'; ?>" name="<?php echo $args['group'].'['.$args['id'].']'.'[hh]'; ?>" value="<?php echo esc_attr($option_value[$args['id']]['hh']); ?>">
		<label for="<?php echo $args['id'].'_mm'; ?>">MM:</label> <input type="text" style="width:25px;" id="<?php echo $args['id'].'_mm'; ?>" name="<?php echo $args['group'].'['.$args['id'].']'.'[mm]'; ?>" value="<?php echo esc_attr($option_value[$args['id']]['mm']); ?>">
		<label for="<?php echo $args['id'].'_ss'; ?>">SS:</label> <input type="text" style="width:25px;" id="<?php echo $args['id'].'_ss'; ?>" name="<?php echo $args['group'].'['.$args['id'].']'.'[ss]'; ?>" value="<?php echo esc_attr($option_value[$args['id']]['ss']); ?>">
		<small><?php echo $args['desc'] ?></small>
<?php
	}
}

?>