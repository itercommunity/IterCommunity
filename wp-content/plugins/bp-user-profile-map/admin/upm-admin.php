<?php

/**
 * bp_upm_admin()
 *
 * Checks for form submission, saves component settings and outputs admin screen HTML.
 */

add_action( 'admin_init', 'bp_upm_settings' );
function bp_upm_settings() {
	register_setting ( 'bp_wp_upm-settings-group', 'upm_width' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_height' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_member_width' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_member_height' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_group_member_width' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_group_member_height' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_profile_unit' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_members_unit' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_group_unit' );				
 register_setting ( 'bp_wp_upm-settings-group', 'upm_map_zoom' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_display_where' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_display_members_where' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_display_group_members_where' );
 register_setting ( 'bp_wp_upm-settings-group', 'upm_navigation_display' );
 register_setting ( 'bp_wp_upm-settings-group', 'upm_navigation_size' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_map_type_control_style' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_show_map_type_control' );
 register_setting ( 'bp_wp_upm-settings-group', 'upm_members_directory' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_group_members_screen' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_user_account_screen' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_user_profile_field' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_styles' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_display_title' );
	register_setting ( 'bp_wp_upm-settings-group', 'upm_infowindow_values');
	register_setting ( 'bp_wp_upm-settings-group', 'upm_infowindow_options');																									
 }
 add_option( 'upm_width', '200' );
 add_option( 'upm_height', '200' );
	add_option( 'upm_member_width', '150' );
	add_option( 'upm_member_height', '150' );
	add_option( 'upm_group_member_width', '150' );
	add_option( 'upm_group_member_height', '150' );
	add_option( 'upm_profile_unit', '' );
	add_option( 'upm_member_unit', '' );
	add_option( 'upm_group_unit', '' );				
 add_option( 'upm_map_zoom', '' );
 add_option( 'upm_display_where', '0' );
	add_option( 'upm_display_members_where', '5' );
	add_option( 'upm_display_group_members_where', '8' );
 add_option( 'upm_navigation_display', '' );
 add_option( 'upm_navigation_size', '' );
	add_option( 'upm_show_map_type_control', 'yes');
	add_option( 'upm_map_type_control_style', 'horizontal');
 add_option( 'upm_members_directory', 'no' );
	add_option( 'upm_group_members_screen', 'no');
	add_option( 'upm_user_account_screen', 'yes');
	add_option( 'upm_user_profile_field', 'Location' );
	add_option( 'upm_styles', 'yes' );
	add_option( 'upm_display_title', 'yes' );
	$upm_infowindow = array(
										'upm_bp_regions' => 'no',
										'upm_shortcode'  => 'no',
										'upm_function'   => 'no'
										);
	add_option('upm_infowindow_values', $upm_infowindow);
	$upm_infowindow_opts = array(
										'upm_anchor_text' => '',
										'upm_display_address'  => 'yes'
										);
	add_option('upm_infowindow_options', $upm_infowindow_opts);
function bp_upm_admin() {
	global $bp;

	/* If the form has been submitted and the admin referrer checks out, save the settings */
	if ( isset( $_POST['submit'] ) && check_admin_referer('update_upm_settings', 'upm_settings') ) {
		update_option( 'upm_width', $_POST['width'] );
		update_option( 'upm_height', $_POST['height'] );
		update_option( 'upm_member_width', $_POST['upm_member_width'] );
		update_option( 'upm_member_height', $_POST['upm_member_height'] );
		update_option( 'upm_member_width', $_POST['upm_group_member_width'] );
		update_option( 'upm_member_height', $_POST['upm_group_member_height'] );
		update_option( 'upm_profile_unit', $_POST['upm_profile_unit'] );
		update_option( 'upm_member_unit', $_POST['upm_member_unit'] );
		update_option( 'upm_group_unit', $_POST['upm_group_unit'] );								
  update_option( 'upm_display_where', $_POST['upm_display_where'] );
		update_option( 'upm_display_members_where', $_POST['upm_display_members_where'] );
		update_option( 'upm_display_group_members_where', $_POST['upm_display_group_members_where'] );
  update_option( 'upm_map_zoom', $_POST['upm_map_zoom'] );
  update_option( 'upm_navigation_display', $_POST['upm_navigation_display'] );
  update_option( 'upm_navigation_size', $_POST['upm_navigation_size'] );
		update_option( 'upm_show_map_type_control', $_POST['upm_show_map_type_control'] );
  update_option( 'upm_map_type_control_style', $_POST['upm_map_type_control_style'] );
  update_option( 'upm_members_directory', $_POST['upm_members_directory'] );
		update_option( 'upm_group_members_screen', $_POST['upm_group_members_screen'] );
		update_option( 'upm_user_account_screen', $_POST['upm_user_account_screen'] );
		update_option( 'upm_user_profile_field', $_POST['upm_user_profile_field'] );
		update_option( 'upm_styles', $_POST['upm_styles'] );
		update_option( 'upm_display_title', $_POST['upm_display_title'] );
		
		$new_infowindow_values = $_POST['upm_infowindow'];
		$bool_options = array('upm_bp_regions', 'upm_shortcode', 'upm_function');
		foreach($bool_options as $key) {
			$new_infowindow_values[$key] = $new_infowindow_values[$key]? 'yes' : 'no';
			}
		update_option('upm_infowindow_values', $new_infowindow_values);
		
		$new_infowindow_opts['upm_anchor_text']      = wp_kses($_POST['upm_anchor_text'], none);
		$new_infowindow_opts['upm_display_address']  = $_POST['upm_display_address'];
		update_option('upm_infowindow_options', $new_infowindow_opts);						

		// input name in style of name="upm_infowindow[upm_bp_regions]
		$updated = true;
	}
//var_dump($new_infowindow_opts );
	
	$map_width = get_option( 'upm_width' );
	$map_height = get_option( 'upm_height' );
	$member_map_width = get_option('upm_member_width');
	$member_map_height = get_option('upm_member_height');
	$group_member_map_width = get_option('upm_group_member_width');
	$group_member_map_height = get_option('upm_group_member_height');
	$upm_profile_unit = 	get_option('upm_profile_unit');
	$upm_member_unit = 	get_option('upm_member_unit');
	$upm_group_unit = 	get_option('upm_group_unit');
 $map_zoom   =  get_option('upm_map_zoom' );
 $navigation_display   =  get_option( 'upm_navigation_display' );
 $navigation_size = get_option( 'upm_navigation_size' );
	$upm_show_map_type_control = get_option( 'upm_show_map_type_control' ); 
 $upm_map_type_control_style = get_option( 'upm_map_type_control_style' );
 $set_members_dir = get_option( 'upm_members_directory' );
	$set_group_members_screen = get_option( 'upm_group_members_screen' );
	$set_user_account_screen = get_option('upm_user_account_screen');
	$upm_user_profile_field = get_option('upm_user_profile_field');
	$upm_styles = get_option('upm_styles');
	$upm_display_title = get_option('upm_display_title');
	$upm_infowindow = get_option('upm_infowindow_values');
	$upm_infowindow_options = get_option('upm_infowindow_options');
// $upm_infowindow['upm_bp_regions'] == 'yes';
// var_dump($upm_infowindow_options);
	function checked_radio($num) {
   global $bp;
   $radio_item = $num;
   $radio_selection = array(get_option( 'upm_display_where' ), get_option( 'upm_display_members_where' ), get_option('upm_display_group_members_where') );
   if ( !in_array($radio_item, $radio_selection) )
   return false;
   else
   echo  'checked="checked"';
   return; 
 }
if( is_multisite() && function_exists( 'is_network_admin' ) ){
	$path_to = site_url() . "/wp-admin/network/admin.php";
	
		if(floatval(BP_VERSION) >= 1.6) {
			$path_to_xprofile = site_url() . '/wp-admin/network/users.php?page=bp-profile-setup';
		}else{
			$path_to_xprofile = site_url() . '/wp-admin/network/admin.php?page=bp-profile-setup';
		}

} else {
	$path_to = site_url() . "/wp-admin/admin.php";
	
		if(floatval(BP_VERSION) >= 1.6) {
			$path_to_xprofile = site_url() . '/wp-admin/users.php?page=bp-profile-setup'; 
		}else{
			$path_to_xprofile = $path_to . '?page=bp-profile-setup';
		}
} 
?>
	<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
		<h2><?php _e( 'User Profile Map & single page maps configuration options', 'bp-upm' ) ?></h2>
		
		<?php if ( isset($updated) ) : ?><?php echo "<div id='message' class='updated fade'><p>" . __( 'Settings Updated.', 'bp-upm' ) . "</p></div>" ?><?php endif; ?>		
		<form action="<?php echo  $path_to  . '?page=bp-upm-settings' ?>" name="upm-settings-form" id="upm-settings-form" method="post">
   
		 <p><?php _e('To use The BuddyPress profile or members list maps you must have first set up a new custom xprofile field to ask users for a location. This is important as the map script looks for this field and value to obtain map location data, you may name this field as you choose and then enter that name below.', 'bp_upm') ?></p>
   <p><?php _e('If you have not yet set up a location field please visit the <a href="' . $path_to_xprofile . '">Profile Field Setup</a> page.', 'bp_upm') ?></p>
   <p><?php _e('You can either set the location field in the base group to show it on the sign up page or create a new group for the field name in which case it will show in the users profle setup and public display only.', 'bp_upm') ?></p>
		 <p><?php _e('Styling of the map elements is kept to a minimum, you might need to add styles for your theme via your primary styles, all elements of the maps are well tokenised so styling is fairly easy.', 'bp_upm') ?></p>
   
		 <h3><?php _e('Displaying Maps', 'bp-upm') ?></h3>
		 <p><?php _e('There are three options for displaying maps; as an embedded function call, as a shortcode, or on BuddyPress specific pages.', 'bp-upm') ?></p>
		 <ul>
		 	<li><?php _e('<b>As a function call:</b>', 'bp-upm') ?> 
				<p><?php _e('you can add a function directly to your WP template files/pages. copy paste the function below along with any parameters you choose.', 'bp-upm') ?></p>
				
      <pre><code>if( function_exists('upm_single_map_display') ) : upm_single_map_display(); endif;</code></pre>
      
				
				<p><?php _e('Available parameters are: ', 'bp-upm') ?><br />
				<code><?php _e('(\'address string\', \'width\', \'height\', \'unit\', \'display address\', \'zoom\', \'show navigation\', \'navigation size\', \'navigation type\')', 'bp-upm') ?> </code><br /> <?php  _e(' Set address as a string e.g London or pass a comma seperated address string \'Buckingham Palace, London, UK\'.  Width &amp; height are numeric strings e.g \'200\' and default to pixel units. To allow the map take the available width (width:auto) simply leave the parameter empty e.g \'\' but a fixed height does need to be set. Setting \'unit\' to \'%\' will convert any width string you set to a percentage value for fluid widths. \'zoom\' sets the degree of magnification, defaults to 12. \'show navigation\' Show the navigation  set to \'nav-yes\' or leave empty for no navigation. \'navigation size\' restrict the size of the navigation bar by setting to \'small\' this overrides Googles default auto adjustment of controls for larger map sizes, leave value empty for default size behaviour. \'navigation type\' controls how the nav bar behaves set to \'dropdown\' for smaller maps or \'horizontal\' to allow a horizontal nav bar', 'bp-upm') ?> </p>
				<p><?php _e('Example: ', 'bp-upm') ?><code>('buckingham palace,london,uk', '50', '500', '%', '14', 'no', 'nav-yes', 'small', 'dropdown')</code></p>
			</li>
			<li><?php _e('<b>As a Shortcode</b>', 'bp-upm') ?>
				<p><?php _e('You can set a map as a shortcode for any post or page you create by adding this shortcode into your post or page content body', 'bp-upm') ?></p>
				<p><code>[upm_map address="buckingham palace,london,uk" width="500" height="500" unit="" display_address="" zoom="" show_nav="yes" nav_size="small" map_type_control=""]</code></p>
				<p><?php _e('Use all the values shown, ones left as empty i.e "" will simply default to pre set values. As with the function above leave both \'width\' and \'unit\' empty for a auto width, set unit to \'%\' for a percentage width. \'display_address\' set to \'no\' turns off the address as a title. Leave \'show_nav\' and \'nav_size\' empty for no navigation and nav size as google default adjusting according to map size dynamically. An empty map type control removes the map type, setting it as \'dropdown\' forces the type control to dropdown setting \'horizontal\' displays normal wide type bar.', 'bp-upm') ?></p>
			</li>
			<li><?php _e('<b>On BuddyPress pages.</b>', 'bp-upm') ?><br />
			<?php _e('BuddyPress options are explained in the various options further down the page.', 'bp-upm') ?></li>
		 </ul>
		 
		 <h3><?php _e('General options', 'bp-upm') ?></h3>
		 <ol>
		 	<li><?php _e('BP xprofile field name: Set the name of your location profile field, this will override the default \'Location\' e.g if you have set a profile field for location but called it \'Country\' add \'Country\' in the field.', 'bp-upm') ?></li>
		 	<li><?php _e('Select the pages you wish to display location maps on.', 'bp-upm') ?></li>
			<li><?php _e('Enable or disable built in styles - styles are minimal and users may need to further style their maps to fit their theme.', 'bp-upm') ?></li>
			<li><?php _e('Map title display the title <i>\'Location: your users location address\'</i> above each map. Uncheck the box to remove titles. Address parts are extracted and wrapped in spans for ease of styling.', 'bp-upm') ?></li>
			<li><?php _e('Map infoWindows are popups available when you click on the map marker. Currently these display the location address and a link to get map directions. Use the options to select on which maps you show these. Generally this option isn\'t required on maps displaying user locations but may be usefull on standalone maps via the function or shortcode methods. <b>N.B.</b> these info windows require a map of approx 300px width or greater to display correctly, they do not work well on small maps, currently if a map is set to display at less than \'300\' the infowindow popup will be disabled.</b>', 'bp-upm') ?></li>
			<!--<li><?php _e('', 'bp-upm') ?></li>-->
		 </ol>
		 <table class="form-table">
			<colgroup>
				<col span="1" width="30%" />
				<col span="1" width="70%" />
			</colgroup>
			<tr>
				<th>
					<label>BP xprofile field name</label>
				</th>
				<td>
					<input type="text" value="<?php echo $upm_user_profile_field ?>" name="upm_user_profile_field" />
				</td>				
			</tr>
			<tr>
				<th>
					<label for="upm-members-listing"><?php _e( 'Members directory listing page ', 'bp-upm' ) ?></label>
				</th>
				<td>
					<input  name="upm_members_directory" type="checkbox" id="upm-members-listing" <?php if( $set_members_dir == 'yes'){?> checked="checked" <?php } ?>  value="yes" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="upm-group-members-screen"><?php _e( 'Group Members List ', 'bp-upm' ) ?></label>
				</th>
				<td>
					<input  name="upm_group_members_screen" type="checkbox" id="upm-group-members-screen" <?php if( $set_group_members_screen == 'yes'){?> checked="checked" <?php } ?>  value="yes" />
				</td>
			</tr>							 	
			<tr>
				<th>
					<label for="upm-user-account-screen"><?php _e( 'Users Account Screens ', 'bp-upm' ) ?></label>
				</th>
				<td>
					<input  name="upm_user_account_screen" type="checkbox" id="upm-user-account-screen" <?php if( $set_user_account_screen == 'yes'){?> checked="checked" <?php } ?>  value="yes" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="upm-styles"><?php _e( 'Enable Built in styles ', 'bp-upm' ) ?></label>
				</th>
				<td>
					<input  name="upm_styles" type="checkbox" id="upm-styles" <?php if( $upm_styles == 'yes'){?> checked="checked" <?php } ?>  value="yes" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="upm-display-title"><?php _e( 'Display titles for maps e.g \'Location: Buckingham Palace, London\' for BP user account/members/groups screens', 'bp-upm' ) ?></label>
				</th>
				<td>
					<input  name="upm_display_title" type="checkbox" id="upm-display-title" <?php if( $upm_display_title == 'yes'){?> checked="checked" <?php } ?>  value="yes" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="upm-infowindow-region"><?php _e( 'Enable map infowindow for member,group, user account screens', 'bp-upm' ) ?></label>
				</th>
				<td>
					<input  name="upm_infowindow[upm_bp_regions]" type="checkbox" id="upm-infowindow-regions" <?php if( $upm_infowindow['upm_bp_regions'] == 'yes'){?> checked="checked" <?php } ?>  value="yes" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="upm-infowindow-shortcode"><?php _e( 'Enable map infowindow for shortcode maps', 'bp-upm' ) ?></label>
				</th>
				<td>
					<input  name="upm_infowindow[upm_shortcode]" type="checkbox" id="upm-infowindow-shortcode" <?php if( $upm_infowindow['upm_shortcode'] == 'yes'){?> checked="checked" <?php } ?>  value="yes" />
				</td>
			</tr>
			<tr>
				<th>
					<label for="upm-infowindow-function"><?php _e( 'Enable map infowindow for function call maps', 'bp-upm' ) ?></label>
				</th>
				<td>
					<input  name="upm_infowindow[upm_function]" type="checkbox" id="upm-infowindow-function" <?php if( $upm_infowindow['upm_function'] == 'yes'){?> checked="checked" <?php } ?>  value="yes" />
					
					<fieldset style="border: 1px solid #ccc; margin-bottom: 5px; width: 40%; padding: 10px;">
						<legend style="padding: 0 3px;"><?php _e('Info Window Options', 'bp-upm')?></legend>
							
							<label for="upm-infowindow-link" style="display: block;"><?php _e('<b>Link Text</b> - <i>Default \'Get Directions\' if left blank</i>', 'bp-upm') ?></label>
							<input id="upm-infowindow-link" type="text" name="upm_anchor_text" value="<?php if(!empty($upm_infowindow_options['upm_anchor_text'])) echo $upm_infowindow_options['upm_anchor_text'] ?>" />
							
							<label for="upm-infowindow-address" style="display: block; margin-top: 5px;"><?php _e('Show Address', 'bp-upm') ?>
								<input id="upm-infowindow-address" name="upm_display_address" type="checkbox" <?php if( $upm_infowindow_options['upm_display_address'] == 'yes'){?> checked="checked" <?php } ?> value="yes" />
							</label>
							
					</fieldset>
				</td>
			</tr>									 
		 </table>
   <h3><?php _e('Map configuration options', 'bp-upm') ?></h3>
   
   <p><?php _e('Set map dimensions - Note: You may leave the width field empty which will cause the map to display at width auto (CSS default) matching the width of it\'s parent if the parent is fluid the map width will adjust accordingly, however when map is floated a width needs to be stated, this can be simply a value \'200\' for pixel width or a percentage. ', 'bp_upm') ?> </p>
     <fieldset style="border: 1px solid #ccc; margin-bottom: 5px; width: 60%;">
			 <legend style="padding: 0 3px;">User account screen map dimensions</legend>
			 <table class="form-table">
		    <colgroup>
       <col span="1" width="40%" />
       <col span="1" width="60%" />
      </colgroup>
      <tr valign="top">
					<th scope="row">
						<label for="upm-width"><?php _e( 'Map width', 'bp-upm' ) ?></label>
					</th>
					<td>
						<input name="width" type="text" id="upm-width"  value="<?php echo attribute_escape( $map_width ); ?>" size="20" />
					</td>
				</tr>
      <tr>
					<th scope="row">
						<label for="upm-height"><?php _e( 'Map height', 'bp-upm' ) ?></label>
					</th>
					<td>
						<input name="height" type="text" id="upm-height" value="<?php echo attribute_escape( $map_height ); ?>" size="20" />
					</td>
				</tr>
				<tr>
					<th>
				 		<label for="upm-profile-unit"><?php _e( 'Set width as a percentage', 'bp-upm' ) ?></label>
					</th>
       	<td>
						<input  name="upm_profile_unit" type="checkbox" id="upm-profile-unit" <?php if( $upm_profile_unit == yes){?> checked="checked" <?php } ?>  value="yes" />
					</td>
				</tr>
      <tr>
					<td colspan="2"><?php _e('If no map dimensions are set here the map will default to display at a width and height of 200px', 'bp-upm') ?></td>
				</tr>				
			</table>
			
			</fieldset>
     <fieldset style="border: 1px solid #ccc; margin-bottom: 5px; width: 60%;">
			 <legend style="padding: 0 3px;">Members Directory listing maps dimensions</legend>
			 <table class="form-table">
		    <colgroup>
       <col span="1" width="40%" />
       <col span="1" width="60%" />
      </colgroup>
      <tr valign="top">
					<th scope="row">
						<label for="upm-width"><?php _e( 'Members Directory Map width', 'bp-upm' ) ?></label>
					</th>
					<td>
						<input name="upm_member_width" type="text" id="upm-width"  value="<?php echo attribute_escape( $member_map_width ); ?>" size="20" />
					</td>
				</tr>
      <tr>
					<th scope="row">
						<label for="upm-height"><?php _e( 'Members Directory Map height', 'bp-upm' ) ?></label>
					</th>
					<td>
						<input name="upm_member_height" type="text" id="upm-height" value="<?php echo attribute_escape( $member_map_height ); ?>" size="20" />
					</td>
				</tr>
				<tr>
					<th>
				 		<label for="upm-member-unit"><?php _e( 'Set width as a percentage', 'bp-upm' ) ?></label>
					</th>
       	<td>
						<input  name="upm_member_unit" type="checkbox" id="upm-member-unit" <?php if( $upm_member_unit == yes){?> checked="checked" <?php } ?>  value="yes" />
					</td>
				</tr>				
      <tr>
					<td colspan="2"><?php _e('If no map dimensions are set here the map will default to display at a width and height of 150px', 'bp-upm') ?></td>
				</tr>
			</table>
			</fieldset>			
			
     <fieldset style="border: 1px solid #ccc; margin-bottom: 5px; width: 60%;">
			 <legend style="padding: 0 3px;">Group Members listing maps dimensions</legend>
			 <table class="form-table">
		    <colgroup>
       <col span="1" width="40%" />
       <col span="1" width="60%" />
      </colgroup>
      <tr valign="top">
					<th scope="row">
						<label for="upm-width"><?php _e( 'Group Members Map width', 'bp-upm' ) ?></label>
					</th>
					<td>
						<input name="upm_group_member_width" type="text" id="upm-width"  value="<?php echo attribute_escape( $group_member_map_width ); ?>" size="20" />
					</td>
				</tr>
      <tr>
					<th scope="row">
						<label for="upm-group-height"><?php _e( 'Group Members Map height', 'bp-upm' ) ?></label>
					</th>
					<td>
						<input name="upm_group_member_height" type="text" id="upm-group-height" value="<?php echo attribute_escape( $group_member_map_height ); ?>" size="20" />
					</td>
				</tr>
				<tr>
					<th>
				 		<label for="upm-group-unit"><?php _e( 'Set width as a percentage', 'bp-upm' ) ?></label>
					</th>
       	<td>
						<input  name="upm_group_unit" type="checkbox" id="upm-group-unit" <?php if( $upm_group_unit == yes){?> checked="checked" <?php } ?>  value="yes" />
					</td>
				</tr>				
      <tr>
					<td colspan="2"><?php _e('If no map dimensions are set here the map will default to display at a width and height of 150px', 'bp-upm') ?></td>
				</tr>
			</table>
			</fieldset>			
			
			<h3><?php _e('Map Overlay Controls', 'bp-upm') ?></h3>
    <table class="form-table">
		    <colgroup>
       <col span="1" width="30%" />
       <col span="1" width="70%" />
      </colgroup>					
      <tr valign="top">
					<td colspan="2"><?php _e('Map zoom sets the level of detail shown on the map, the higher the value the more you zoom into the location. Values between 8 &amp; 16 work well. If no value set Zoom value defaults to 11.', 'bp-upm') ?></td>      
      </tr>      
				<tr valign="top">
					<th scope="row">
						<label for="upm-zoom"><?php _e( 'Map Zoom', 'bp-upm' ) ?></label>
					</th>
					<td>
						<input name="upm_map_zoom" type="text" id="upm-zoom" maxlength="2" value="<?php echo attribute_escape( $map_zoom ); ?>" size="5" />
					</td>
				</tr>
      <tr>
       <td colspan="2"><?php _e('If you want to display a set of navigation controls, i.e zoom, pan etc then check the box below to show these controls on the maps.', 'bp-upm');  ?></td>
      </tr>      
				<tr>
					<th scope="row">
				 		<label for="map-navigation"><?php _e( 'Show the map navigation overlay controls. ', 'bp-upm' ) ?></label>
					</th>
					<td>
						<input  name="upm_navigation_display" type="checkbox" id="map-navigation" <?php if( $navigation_display == true){?> checked="checked" <?php } ?>  value="true" />
					</td>
      </tr>
      <tr valign="top">
       <td colspan="2"><?php _e('Google maps adjusts certain map controls according to the maps dimensions  If maps are smaller than 320px the default small overlays are used, if bigger then items like the map type bar is Horizontal. If you wish to keep the navigation controls fixed as small check the box below.', 'bp-upm');  ?></td>
      </tr>
      <tr>
					<th scope="row" valign="top">
				 		<label for="navigationSmall"><?php _e( 'Set the map navigation control size to small. ', 'bp-upm' ) ?></label>
					</th>
       	<td>
				 		<input  name="upm_navigation_size" type="checkbox" id="navigationSmall" <?php if( $navigation_size == 'small'){?> checked="checked" <?php } ?>  value="small" />
				 	</td>
      </tr>
      <tr valign="top">
       <td colspan="2"><?php _e('Map type controls select the map view I.E. Map, Satellite, Hybrid, these selections default for small maps and inline boxes for larger maps. If you would like to display as a dropdown only then check the box below', 'bp-upm');  ?></td>
      </tr>
				<tr>
					<th scope="row" valign="top">
				 		<label for="show-map-type-control"><?php _e( 'Show map type bar', 'bp-upm' ) ?></label>
					</th>
       	<td>
						<input  name="upm_show_map_type_control" type="checkbox" id="show-map-type-control" <?php if( $upm_show_map_type_control == 'yes'){?> checked="checked" <?php } ?>  value= 'yes' />
					</td>
      </tr>				      
				<tr>
					<th scope="row" valign="top">
				 		<label for="map-type-control-style"><?php _e( 'Set map type control as a dropdown', 'bp-upm' ) ?></label>
					</th>
       	<td>
						<input  name="upm_map_type_control_style" type="checkbox" id="map-type-control-style" <?php if( $upm_map_type_control_style == 'dropdown'){?> checked="checked" <?php } ?>  value= 'dropdown' />
					</td>
      </tr>
     </table>
    
    <h3><?php _e('Page display options', 'bp-upm') ?></h3>    
			<p><b><?php _e('Select the areas you would like to display the map in N.B defaults to no region', 'bp_upm') ?></b></p>
    <table class="form-table">

				<tr>
					<th style="vertical-align: top; padding-right: 10px;">
						<p style="margin-top: 0;"><?php _e('Select region for user profile display', 'bp-upm'); ?></p>
					</th>
					<td>
						<fieldset>
						<legend class="screen-reader-text">Select user account region for map display</legend>
							<p>
								<label for="upm-region-none"><input name="upm_display_where" type="radio" id="upm-region-none" <?php checked_radio('0') ?> value="0"  /><?php _e( ' Do not display ', 'bp-upm' ) ?></label>
							</p>               
							<p>
								<label for="upm-region-a"><input name="upm_display_where" type="radio" id="upm-region-a" <?php checked_radio('1') ?>  value="1"  /><?php _e( ' bp_before_member_header_meta <i>Will display before header meta to the right.</i>', 'bp-upm' ) ?></label>
							</p>					
							<p>
								<label for="upm-region-b"><input name="upm_display_where" type="radio" id="upm-region-b" <?php checked_radio('2') ?>  value="2"  /><?php _e( ' bp_profile_header_meta <i>Will display after the header meta.</i>', 'bp-upm' ) ?></label>
							</p>        
	     			<p>
								<label for="upm-region-c"><input name="upm_display_where" type="radio" id="upm-region-c" <?php checked_radio('3') ?>  value="3"  /><?php _e( ' bp_after_member_header <i>Displays after the members header content.</i>', 'bp-upm' ) ?></label>
							</p>						
	     			<p>
								<label for="upm-region-d"><input name="upm_display_where" type="radio" id="upm-region-d" <?php checked_radio('4') ?>  value="4" /><?php _e( ' bp_before_loop_content <i>Displays on the members public profile view to the right.</i>', 'bp-upm' ) ?></label>
							</p>
	     			<p><?php _e('This option requires adding a function in the profile loop page \members\single\profile-loop.php. Add <code>&lt;?php if( function_exists(\'upm_add_map_profile_field\') ) : upm_add_map_profile_field(); endif ; ?></code> to the loop just before <code>\'bp_the_profile_field_value()\'</code> so it will look like <code>&lt;td class="data">&lt;?php if( function_exists(\'upm_add_map_profile_field\') ) : upm_add_map_profile_field(); endif ; ?><?php bp_the_profile_field_value(); ?></code>', 'bp-upm')?><br /> 
								<label for="upm-region-e"><input name="upm_display_where" type="radio" id="upm-region-e" <?php checked_radio('4.1') ?>  value="4.1" /><?php _e( ' Users profile screen <i>Displays the map on the public profile screen but in the profile entry for your \'location\' field.</i>', 'bp-upm' ) ?></label>
							</p>						
						</fieldset>
					</td>
				</tr>
			</table>
    <table class="form-table">
				<tr>
					<th style="vertical-align: top; padding-right: 10px;">
						<p style="margin-top: 0;"><?php _e('Select region for Members directory display', 'bp-upm'); ?></p>
					</th>
					<td>
						<fieldset>
						<legend class="screen-reader-text">Select Members directory region for map display</legend>
							<p>
								<label for="upm-mem-region-none"><input name="upm_display_members_where" type="radio" id="upm-mem-region-none" <?php checked_radio('5') ?> value="5"  /><?php _e( ' Do not display ', 'bp-upm' ) ?></label>
							</p>               
							<p>
								<label for="upm-mem-region-a"><input name="upm_display_members_where" type="radio" id="upm-mem-region-a" <?php checked_radio('6') ?>  value="6"  /><?php _e( ' bp_directory_members_item <i>Displays in the list item area, after user avatar and activity meta.</i>', 'bp-upm' ) ?></label>
							</p>					
							<p>
								<label for="upm-mem-region-b"><input name="upm_display_members_where" type="radio" id="upm-mem-region-b" <?php checked_radio('7') ?>  value="7"  /><?php _e( ' bp_directory_members_actions <i>Displays in the members list action area.</i>', 'bp-upm' ) ?></label>
							</p>        						
						</fieldset>
					</td>
				</tr>
			</table>						      
    <table class="form-table">
				<tr>
					<th style="vertical-align: top; padding-right: 10px;">
						<p style="margin-top: 0;"><?php _e('Select region for Group Members List ', 'bp-upm'); ?></p>
					</th>
					<td>
						<fieldset>
						<legend class="screen-reader-text">Select Group Members region for maps display</legend>
							<p>
								<label for="upm-group-region-none"><input name="upm_display_group_members_where" type="radio" id="upm-group-region-none" <?php checked_radio('8') ?> value="8"  /><?php _e( ' Do not display ', 'bp-upm' ) ?></label>
							</p>               
							<p>
								<label for="upm-group-region-a"><input name="upm_display_group_members_where" type="radio" id="upm-group-region-a" <?php checked_radio('9') ?>  value="9"  /><?php _e( ' bp_group_members_list_item <i>Displays in the list item area, after user avatar and activity meta.</i>', 'bp-upm' ) ?></label>
							</p>					
							<p>
								<label for="upm-group-region-b"><input name="upm_display_group_members_where" type="radio" id="upm-group-region-b" <?php checked_radio('10') ?>  value="10"  /><?php _e( ' bp_group_members_list_item_action <i>Displays in the group members list action area.</i>', 'bp-upm' ) ?></label>
							</p>        						
						</fieldset>
					</td>
				</tr>
			</table>		     
    <p class="submit">
				<input type="submit" name="submit" value="<?php _e( 'Save Settings', 'bp-upm' ) ?>"/>
			</p>

			<?php
			/* This is very important, don't leave it out. */
			wp_nonce_field( 'update_upm_settings', 'upm_settings' );
			?>
		</form>
	</div>
<?php
}
/* pointless comment to force a new revision and commit */
?>