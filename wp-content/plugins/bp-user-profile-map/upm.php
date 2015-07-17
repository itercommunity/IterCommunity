<?php 
if ( !defined( 'ABSPATH' ) ) exit;
global $bp, $wp;

define( 'BP_UPM_VERSION', '1.4.2' );
define( 'BP_UPM_ACTIVE', 1 );
/**
* create url paths for multisite network admin screens or plain vanills admin screen
* workaround for adding links to various other site admin pages e.g linking to
* the BP extended profile screen.
*/
if( is_multisite() && function_exists( 'is_network_admin' ) ){
	$path_to = site_url() . "/wp-admin/network/admin.php";
} else {
	$path_to = site_url() . "/wp-admin/admin.php";
}

// Set up translatable
$upm_mo = WP_PLUGIN_DIR . "/bp-user-profile-map/languages/bp-upm-" . get_locale() . ".mo";
if ( file_exists( $upm_mo ) )
	load_textdomain( 'bp-upm', $upm_mo );
  
require_once ( dirname( __FILE__ ) . '/upm-widget.php' );
require_once ( dirname( __FILE__ ) . '/admin/upm-admin.php' );

function upm_add_styles() {
		$version = BP_UPM_VERSION;
  $style_url = WP_PLUGIN_URL . '/bp-user-profile-map/upm.css';
		$style_file = WP_PLUGIN_DIR . '/bp-user-profile-map/upm.css';
			
			if (file_exists($style_file)) {
				wp_enqueue_style('upm-style', $style_url, $version, 'all' );			
			}    
}
if( get_option('upm_styles') == 'yes' ) {
add_action( 'wp_enqueue_scripts', 'upm_add_styles' );
}


################## create the Admin dashboard settings ###############
/** create WP admin settings ***/
function bp_upm_menu() {
	global $bp;

 if ( true == is_super_admin() ):
   $user_is_admin = true;
 elseif (true == is_site_admin() ):
   $user_is_admin = true;
 else:
   $user_is_admin = false;
 endif;
    
	if ( !$user_is_admin )
		return false;
	
	// which page do we want?
	if( is_network_admin() ) {
		$settings_page = 'settings.php';
	}else {
		$settings_page = 'options-general.php';
	}	

	add_submenu_page( $settings_page, __( 'UPM setup', 'bp-upm' ), __( 'UPM Setup', 'bp-upm' ), 'manage_options', 'bp-upm-settings', 'bp_upm_admin' );
	
}

	if( is_multisite() && function_exists( 'is_network_admin' ) ):
		add_action( 'network_admin_menu', 'bp_upm_menu' );
	else:
		add_action( 'admin_menu', 'bp_upm_menu' );
	endif;


// Fetch values for map page location &  directory/screen displays.
if( get_option( 'upm_display_where' ) !== '0') {
	$where = get_option( 'upm_display_where' );
}
if( get_option( 'upm_display_members_where' ) !== '5') {
	$where_members_dir = get_option( 'upm_display_members_where' );
}
if( get_option( 'upm_display_group_members_where' ) !== '8') {
	$where_groups_screen = get_option( 'upm_display_group_members_where' );
}

$members_dir_listing   = get_option( 'upm_members_directory' );
$members_group_listing = get_option( 'upm_group_members_screen' );
$users_account_screen  = get_option( 'upm_user_account_screen' );
  

#### Profile location maps - google V3 api no api key required ####

function upm_gmap_display(){  
 
global $bp, $wpdb, $members_listing,  $members_template ;

	// Set our member id's.
	// This isn't ideal later on we need to re-check $mem_id 
	// if we are on an acount screen to see if it's empty for first
	// map loop i.e the profile members map and then assign it  bp_is_displayed_user again
	if( bp_is_members_component() || bp_is_user_friends() ) {
		$mem_id = $members_template->member->id;
	} elseif ( bp_is_group_members()) {
		$mem_id = bp_get_group_member_id();
	} else {
		$mem_id = bp_displayed_user_id();
	}
	
	// Get our array of infowindow options n.b ToDo: move all options to arrays.
	$upm_infowindow = get_option('upm_infowindow_values');
	$upm_infowindow_options = get_option('upm_infowindow_options');
	//var_dump($upm_infowindow);
	
	// We let the admin user set what field name they have used for location data
	// if not set we default it to 'Location' in add_option()
 $upm_profile_field = get_option('upm_user_profile_field');

 if ( $location = bp_get_profile_field_data( 'field=' . $upm_profile_field . '&user_id=' . $mem_id ) ):
	
	$upm_unit = 'px';
	
	if( bp_is_members_component() || bp_is_user_friends() ) {
		$map_width = get_option('upm_member_width');
		if( empty($map_width) ){
		$map_width = false;	
		}
		if( get_option('upm_member_unit') == 'yes' ){
			$upm_unit = '%';
		}
	}elseif( bp_is_group_members() ) {
		$map_width = get_option('upm_group_member_width');
		if( empty($map_width) ){ 
			$map_width = false;
		}
		if( get_option('upm_group_unit') == 'yes' ){
			$upm_unit = '%';
		}
	}else{
	 $map_width = get_option( 'upm_width' );
		if( empty($map_width) ){
			$map_width = false;
		}
		if( get_option('upm_profile_unit') == 'yes' ){
			$upm_unit = '%';
		}
	}			

	if( bp_is_members_component() || bp_is_user_friends() ) {
		$map_height = get_option('upm_member_height');
	}elseif( bp_is_group_members() ) {
		$map_height = get_option('upm_group_member_height');
	}else{
	 $map_height = get_option( 'upm_height' );
	}
		
 if ( get_option('upm_map_zoom') ){
    $map_zoom = get_option( 'upm_map_zoom' ); 
 } elseif ( empty($map_zoom) ){
    $map_zoom = 11;
 }  

	//	Set up some classes for top level parent - over-egging the pudding!
	if( bp_is_members_component() ){
		$where = get_option('upm_display_members_where');
	}elseif( bp_is_group_members() ){
		$where = get_option('upm_display_group_members_where');
	}else {
		$where = get_option('upm_display_where');
	}

 switch($where){
 case "1":
 $whereClass = ' before-header-meta';
 break;
 case "2":
 $whereClass = ' after-header-meta';
 break;
 case "3":
 $whereClass = ' after-header-content';
 break;
 case "4":
 $whereClass = ' profile-loop';
 break;
 case "4.1":
 $whereClass = ' profile-loop-field-'. $upm_profile_field . '';
 break;
 case "6":
 $whereClass = ' upm-members-list-item';
 break;
 case "7":
 $whereClass = ' upm-members-item-action';
 break;
 case "9":
 $whereClass = ' upm-group-list-item';
 break;
 case "10":
 $whereClass = ' upm-group-list-action';			
 default:
 $whereClass = '';
 }
 $map_tokens = 'map-display' . $whereClass;
 
	// create a display friendly address format
		$format_address = explode(',', $location);
		$i = '1'; // we need unique incremetal classes for address lines  
		foreach($format_address as  $value) { 
			$formatted_address .= '<span class="info-address-line address-line-' . $i++ . '">' . $value . '</span> ';
		}  			
	//print_r($formatted_address);
					
 $map_navigation = get_option( 'upm_navigation_display' );
 $navigation_size = get_option( 'upm_navigation_size' );
	$show_map_type_control = get_option( 'upm_show_map_type_control' );
 $map_type_control_style = get_option( 'upm_map_type_control_style' ); 
	//var_dump($where);
 
	// Bit of a hack to check if we're on friends screen and add unique id to main profile avatar loop.
	// As my earlier settings removed  the user_id appended to map canvas id all dimensions assume the friends/members loop sizes
	// these 7 lines attempts to work around that  in an inelegant fashion.
	( bp_is_user_friends() && empty($mem_id) )? $mem_id = bp_displayed_user_id(). '_fr' : $mem_id = $mem_id ;
	
	// likewise more hackishness need to reset the map canvas dimensions to stop friends loop overriding profile map dimensions.
	if (bp_is_user_friends() && bp_displayed_user_id(). '_fr' == $mem_id ) :
	// here we go re-do canvas widths - must be a better way?
		$map_width = get_option( 'upm_width' );
		if( empty($map_width) ){
			$map_width = false;
		}
		$map_height = get_option( 'upm_height' );
	endif;  // end re-do map widths
	?>
  <div class="<?php echo $map_tokens; ?><?php if(isset($mem_id)): ?> user-<?php echo $mem_id; ?>-location<?php endif; ?>">
   <?php if( get_option('upm_display_title') == 'yes'): ?>
		 	<p class="map-title"><?php echo __('Location: ', 'bp-upm') . $formatted_address ?> <?php if (  bp_is_my_profile() ) : ?> . <a href="<?php echo bp_loggedin_user_domain() .'profile/edit/' ?>">Edit</a><?php endif; ?></p>
		 <?php endif; ?>
  
		<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
  <script type="text/javascript">
		<!--//--><![CDATA[//><!--
 	var directionsDisplay;
  var directionsService = new google.maps.DirectionsService();     
  var geocoder;
  var map<?php echo '_' . $mem_id ?>;
  function initialize<?php echo '_' . $mem_id  ?>() {
    geocoder = new google.maps.Geocoder();
       var latlng = new google.maps.LatLng(-34.397, 150.644);
       var myOptions = {
       zoom: <?php echo $map_zoom; ?>,
       center: latlng,
       <?php if ( true == $map_navigation  ) {?>
       navigationControl: true,
       <?php   } else { ?>
       navigationControl: false,
       <?php  } ?>
       <?php if('small' == $navigation_size): ?>
       navigationControlOptions: {
       style: google.maps.NavigationControlStyle.SMALL
       },				 
       <?php endif; ?> 
       <?php if($show_map_type_control == 'yes') { ?>
       mapTypeControl: true,
				 <?php }else{ ?>
				 mapTypeControl: false,
				 <?php } ?>
       mapTypeControlOptions: {
				 <?php if('dropdown' == $map_type_control_style) { ?>
       style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
				 <?php }else{ ?>
				 style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
				 <?php } ?>
       },
       mapTypeId: google.maps.MapTypeId.ROADMAP
       }

     map<?php echo '_' . $mem_id ?> = new google.maps.Map(document.getElementById("map_canvas<?php echo '_' . $mem_id ?>"), myOptions);
  
		}      

		function showAddress<?php echo '_' . $mem_id ?>(address) {
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map<?php echo '_' . $mem_id ?>.setCenter(results[0].geometry.location);
        var thecords = results[0].geometry.location;
					var marker = new google.maps.Marker({
            map: map<?php echo '_' . $mem_id ?>,
							<?php if($upm_infowindow['upm_bp_regions'] == 'yes'){ ?>
							<?php if( $map_width >= '300'){ ?>
							title: 'Get Directions',
							<?php } 
							}?>
							clickable: true, 
            position: results[0].geometry.location
        });			
					
					<?php if($upm_infowindow['upm_bp_regions'] == 'yes'){ ?>
					<?php if( $map_width >= '300'){ ?>
					<?php if($upm_infowindow_options['upm_display_address'] == 'yes') { ?>
					var html1 = '<p class="upm-infowindow-address"><span class="upm-address-title">Location: </span><br />'; 
					var html2 =   '<?php echo $formatted_address ?>' ;  			
					//alert(html2);
					<?php }else{ ?>
					var html1 = '';
					var html2 = '';
					<?php } ?>
					<?php if( !empty($upm_infowindow_options['upm_anchor_text']) ) {?>
					var linkText = '<?php echo $upm_infowindow_options['upm_anchor_text'] ?>';
					<?php }else{ ?>
					var linkText = '<?php _e('Get Directions', 'bp-upm')?>';
					<?php } ?>
					var html3 = '</p><p class="upm-directions-link"><a target="_blank" href="http://maps.google.com/maps?saddr=&daddr=' + address + '" >' + linkText + '</a></p>';
						
					var infoWindow = new google.maps.InfoWindow({
				 		content: html1 + html2 + html3
				 	});
      	// add a listener to open the infowindow when a user clicks on one of the markers
      	google.maps.event.addListener(marker, 'click', function() {
        	infoWindow.open(map<?php echo '_' . $mem_id ?>, marker);
      	}); 
					// Add a close event to re-center map after infowindow closes 
					google.maps.event.addListener(infoWindow, 'closeclick', function() {
    			map<?php echo '_' . $mem_id ?>.setCenter(results[0].geometry.location);
					});
				<?php } 
				}?>
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }      
      
  jQuery(document).ready( function() { initialize<?php echo '_' . $mem_id ?>(); showAddress<?php echo '_' . $mem_id ?>('<?php echo $location ?>'); } );
		//--><!]]>
  </script>
		
  <div id="map_canvas<?php echo '_' . $mem_id ?>" class="upm-bp-maps" style="<?php if($map_width) : echo 'width: ' . $map_width . $upm_unit . ';'; endif; ?> height: <?php echo $map_height; ?>px;"></div>
  
		</div>
  <?php  
		endif;
		
 } // member_profile_locationMap
  
  // Add map to BuddyPress pages
		if($users_account_screen == 'yes' && $where) {
			if ( $where == '1') 
    	add_action('bp_before_member_header_meta', 'upm_gmap_display', 10);
  	if ( $where  == '2')
    	add_action('bp_profile_header_meta', 'upm_gmap_display', 10);
  	if (  $where == '3')
    	add_action('bp_after_member_header', 'upm_gmap_display', 10); 
  	if (  $where == '4')
    	add_action('bp_before_profile_loop_content', 'upm_gmap_display', 10);
			if( $where == '4.1'){
				// Add a map to the users profile field 'location'
				function upm_add_map_profile_field(){
					global $field;

					$upm_bp_field_name = bp_get_the_profile_field_name();
					$upm_bp_field_id = bp_get_the_profile_field_id();
					$upm_profile_field = get_option('upm_user_profile_field');

					if ( bp_get_the_profile_field_name() == $upm_profile_field){
						
						return upm_gmap_display();
					
					}else {	
						
						return false;						
					}
				
				}// close function
			
			}	// close if $where		
		
		} // close if accountscreen users
	
  if (  $members_dir_listing == 'yes' && $where_members_dir ){
    if($where_members_dir == '6') 
    	add_action('bp_directory_members_item', 'upm_gmap_display', 10);
			if($where_members_dir == '7')
				add_action('bp_directory_members_actions', 'upm_gmap_display', 10); 
		}

		if( $members_group_listing == 'yes' && $where_groups_screen ){
			if($where_groups_screen == '9')
				add_action('bp_group_members_list_item', 'upm_gmap_display');
			if($where_groups_screen == '10')
				add_action('bp_group_members_list_item_action', 'upm_gmap_display');
		}
		
// gmap single map display for calling as a function in WP pages/templates
function upm_single_map_display($upm_single_address = '', $upm_single_width = '', $upm_single_height = '', $upm_size_unit = '', $upm_single_zoom = '', $upm_address_display = '', $upm_map_navigation = '', $upm_navigation_size = '', $upm_map_type_controls = '') {
	
	if( !empty($upm_single_address) ){
		$upm_single_address = $upm_single_address;
	}else{
		$upm_single_address = 'London';
	}
	
	if( !empty($upm_single_width) ){
		$upm_single_width = $upm_single_width;
	}else{
		$upm_single_width = false;
	}
	
	if( !empty($upm_single_height) ){
		$upm_single_height = $upm_single_height;
	}else{
		$upm_single_height = '200';
	}

	if( !empty($upm_size_unit) ){
		$upm_size_unit = $upm_size_unit;
	}else{
		$upm_size_unit = 'px';
	}

	if( empty($upm_address_display) ){
		$upm_address_display = 'yes';
	}else{
		$upm_address_display = 'no';
	}
			
	if( !empty($upm_single_zoom) ){
		$upm_single_zoom = $upm_single_zoom;
	}else{
		$upm_single_zoom = '12';
	}

	if( !empty($upm_map_navigation) ){
		$upm_map_navigation = $upm_map_navigation;
	}else{
		$upm_map_navigation = false;
	}
	
	if( !empty($upm_navigation_size) ){
		$upm_navigation_size = $upm_navigation_size;
	}else{
		$upm_navigation_size = false;
	}
	
	if( !empty($upm_map_type_controls) ){
		$upm_map_type_controls = $upm_map_type_controls;
	}else{
		$upm_map_type_controls = false;
	}
	// create a display friendly address format
			$format_address = explode(',', $upm_single_address);
			$i = '1'; // we need unique incremetal classes for address lines  
			foreach($format_address as  $value) { 
			$formatted_address .= '<span class="info-address-line address-line-' . $i++ . '">' . $value . '</span> ';
			}  			
	//print_r($formatted_address);
		
	$upm_infowindow = get_option('upm_infowindow_values');
	$upm_infowindow_options = get_option('upm_infowindow_options');						
?>


  <div class="single-map-page">
		<?php if('yes' == $upm_address_display ){ ?>	
			<p class="single-map-title"><span class="address-prefix"><?php _e('Location: ', 'bp-upm') ?> </span><?php echo $formatted_address ?></p>
		<?php } ?>
  <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
  <script type="text/javascript">
		<!--//--><![CDATA[//><!--      
  var geocoder;
  var map;
  function initialize() {
    geocoder = new google.maps.Geocoder();
       var latlng = new google.maps.LatLng(-34.397, 150.644);
       var myOptions = {
       zoom: <?php echo $upm_single_zoom; ?>,
       center: latlng,
       <?php if ( $upm_map_navigation && 'nav-yes' == $upm_map_navigation  ) {?>
       navigationControl: true,
       <?php   } else { ?>
       navigationControl: false,
       <?php  } ?>
       <?php if ('small' == $upm_navigation_size): ?>
       navigationControlOptions: {
       style: google.maps.NavigationControlStyle.SMALL
       },
       <?php endif; ?> 
       <?php if (!empty( $upm_map_type_controls )){ ?>
       mapTypeControl: true,
				 <?php }else{ ?>
				 mapTypeControl: false,
				 <?php } ?>
       mapTypeControlOptions: {
				 <?php if ('dropdown' == $upm_map_type_controls) { ?>
       style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
				 <?php }else{?>
				 style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
				 <?php } ?>
       },
        
       mapTypeId: google.maps.MapTypeId.ROADMAP
       }
     map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }      
  function showAddress(address) {
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
					var thecords = results[0].geometry.location;
        var marker = new google.maps.Marker({
            map: map,
							<?php if($upm_infowindow['upm_function'] == 'yes'){ ?>
							<?php if($upm_single_width >= '300'){ ?>
							title: 'Get Directions',
							<?php } 
							}?>
							clickable: true,							 
            position: results[0].geometry.location
        });
					<?php if($upm_infowindow['upm_function'] == 'yes'){ ?>
					<?php if($upm_single_width >= '300'){ ?>
					<?php if($upm_infowindow_options['upm_display_address'] == 'yes') { ?>
					var html1 = '<p class="upm-address-fields"><span class="upm-address-title"><?php _e('Location: ', 'bp-upm') ?> </span><br />'; 
					var html2 =   '<?php echo $formatted_address ?>' ;  			
					//alert(html2);
					<?php }else{ ?>
					var html1 = '';
					var html2 = '';
					<?php } ?>
					<?php if( !empty($upm_infowindow_options['upm_anchor_text']) ) {?>
					var linkText = '<?php echo $upm_infowindow_options['upm_anchor_text'] ?>';
					<?php }else{ ?>
					var linkText = '<?php _e('Get Directions', 'bp-upm')?>';
					<?php } ?>					
					var html3 = '</p><p class="upm-directions-link"><a target="_blank" href="http://maps.google.com/maps?saddr=&daddr=' + address + '" >' + linkText + '</a></p>';
						
					var infoWindow = new google.maps.InfoWindow({
				 		content: html1 + html2 + html3
				 	});
      	// add a listener to open the infowindow when a user clicks on one of the markers
      	google.maps.event.addListener(marker, 'click', function() {
        infoWindow.open(map, marker);
      	});
					// Add a close event to re-center map after infowindow closes 
					google.maps.event.addListener(infoWindow, 'closeclick', function() {
    			map.setCenter(results[0].geometry.location);
					});
					<?php } 
					}?>										
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }      
      
  jQuery(document).ready( function() { initialize(); showAddress('<?php echo $upm_single_address ?>'); } );
		//--><!]]>
  </script>

  <div id="map_canvas" class="upm-function-standalone" style="<?php if($upm_single_width) : echo 'width: ' . $upm_single_width . $upm_size_unit . ';'; endif; ?> height: <?php echo $upm_single_height  ?>px;"></div>
  </div>

<?php
}
function upm_map_short($atts, $content = null){
	extract(shortcode_atts(array(
		"address" => 'Buckinham Palace, London, UK',
		"display_address" => 'yes',
		"width" =>  '200',
		"height" => '200',
		"unit" =>   'px',
		"zoom" =>   '12',
		"show_nav" => 'yes',
		"nav_size" => '',
		"map_type_control" => '' 
	), $atts));
 
	( empty($atts['address']) )?  $upm_address = 'buckinham palace, london, uk' : $upm_address = $atts['address'];
	( empty($atts['display_address']) )?  $upm_display_address = 'yes' : $upm_display_address = $atts['display_address'];
	( empty($atts['width']) )?  $upm_single_width = false : $upm_single_width = $atts['width'];
	( empty($atts['height']) )? $upm_single_height = '200' : $upm_single_height = $atts['height'];
	( empty($atts['unit']) )? $upm_size_unit = 'px' : $upm_size_unit = $atts['unit'];
	( empty($atts['zoom']) )? $upm_single_zoom = '14': $upm_single_zoom = $atts['zoom'];
	( empty($atts['show_nav']) )? $upm_map_navigation = 'no' :  $upm_map_navigation = $atts['show_nav'];
	( empty($atts['nav_size']) )? $upm_navigation_size = false : $upm_navigation_size = $atts['nav_size'];
	( empty($atts['map_type_control']) ) ? $upm_map_type_controls = false : $upm_map_type_controls = $atts['map_type_control'];
	
	$upm_infowindow = get_option('upm_infowindow_values');
	$upm_infowindow_options = get_option('upm_infowindow_options');

	// create a display friendly address format
	$format_address = explode(',', $upm_address); 
	$i = '1'; // we need unique incremetal classes for address lines 
	foreach($format_address as  $value) {
	 
		$formatted_address .= '<span class="info-address-line address-line-' . $i++ . '">' . $value . '</span> ';
	}  			
	//print_r($formatted_address);
	$values = json_encode($atts);
	//var_dump($upm_infowindow);
	//var_dump($values);
	?>
	
	
  <div class="shortcode-single-map-page">
			<?php if('yes' == $upm_display_address) { ?>
			<p class="sortcode-map-title"><span class="address-prefix"><?php _e('Location: ', 'bp-upm') ?></span><?php echo $formatted_address ?></p>
			<?php } ?>
  <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
  <script type="text/javascript">
		<!--//--><![CDATA[//><!--      
  var geocoder;
  var map;
  function initialize() {
    geocoder = new google.maps.Geocoder();
       var latlng = new google.maps.LatLng(-34.397, 150.644);
       var myOptions = {
       zoom: <?php echo $upm_single_zoom; ?>,
       center: latlng,
       <?php if ( $upm_map_navigation && 'yes' == $upm_map_navigation  ) {?>
       navigationControl: true,
       <?php   } else { ?>
       navigationControl: false,
       <?php  } ?>
       <?php if ('small' == $upm_navigation_size): ?>
       navigationControlOptions: {
       style: google.maps.NavigationControlStyle.SMALL
       },
       <?php endif; ?> 
       <?php if ( !empty($upm_map_type_controls) ){ ?>
       mapTypeControl: true, 
				 <?php }else{ ?>
				 mapTypeControl: false,
				 <?php } ?>
       mapTypeControlOptions: {
				 <?php  if ('dropdown' == $upm_map_type_controls) { ?>
       style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
				 <?php  }else{?>
				 style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
					<?php } ?>
       },
        
					mapTypeId: google.maps.MapTypeId.ROADMAP
       }
     map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }      
  function showAddress(address) {
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
					var thecords = results[0].geometry.location;
        var marker = new google.maps.Marker({
            map: map,
							<?php if($upm_infowindow['upm_shortcode'] == 'yes'){ ?>
							<?php if($upm_single_width >= '300'){ ?>
							title: '<?php _e('Get Directions', 'bp-upm')?>',
							<?php } 
							}?>
							clickable: true,							 
            position: results[0].geometry.location
        });
					<?php if($upm_infowindow['upm_shortcode'] == 'yes'){ ?>
					<?php if($upm_single_width >= '300'){ ?>
					<?php if($upm_infowindow_options['upm_display_address'] == 'yes') { ?>
					var html1 = '<p class="upm-address-fields"><span class="upm-address-title"><?php _e('Location: ', 'bp-upm') ?> </span><br />';
					var html2 =   '<?php echo $formatted_address ?>';  			
					//alert(html2);
					<?php }else { ?>
					var html1 = '';
					var html2 = '';
					<?php } ?>
					<?php if( !empty($upm_infowindow_options['upm_anchor_text']) ) {?>
					var linkText = '<?php echo $upm_infowindow_options['upm_anchor_text'] ?>';
					<?php }else{ ?>
					var linkText = 'Get Directions';
					<?php } ?>					 
					var html3 = '</p><p class="upm-directions-link"><a target="_blank" href="http://maps.google.com/maps?saddr=&daddr=' + address + '" >' + linkText + '</a></p>';
						
					var infoWindow = new google.maps.InfoWindow({
				 		content: html1 + html2 + html3
				 	});
      	// add a listener to open the infowindow when a user clicks on one of the markers
      	google.maps.event.addListener(marker, 'click', function() {
        	infoWindow.open(map, marker);
      	});
					// Add a close event to re-center map after infowindow closes 
					google.maps.event.addListener(infoWindow, 'closeclick', function() {
    			map.setCenter(results[0].geometry.location);
					});
					<?php } 
					}?>      
				} else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }      
      
  jQuery(document).ready( function() { initialize(); showAddress(' <?php echo $upm_address; ?> '); } );
		//--><!]]>
  </script>

  <div id="map_canvas" class="upm-shortcode" style="<?php if($upm_single_width) : echo 'width: ' . $upm_single_width . $upm_size_unit . ';'; endif; ?> height: <?php echo $upm_single_height  ?>px;"></div>
  </div>	
	<?php	
	return ;
}
add_shortcode('upm_map', 'upm_map_short');
?>