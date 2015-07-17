<?php
/**
* Displays a user map for a member when viewing user account/profile screens
* @package bp-user-profile-map
* 
*/
	add_action('widgets_init', create_function('', 'return register_widget("BP_upm_Widget");') );


class BP_upm_Widget extends WP_Widget {

	function bp_upm_widget() {   
		parent::WP_Widget( false, $name = __( 'UPM User Location', 'upm' ) );
	}
		
	function widget( $args, $instance ) {
		global $bp, $current_user, $wpdb;
		
		$field = get_option('upm_user_profile_field');
		$location = bp_get_profile_field_data( 'field=' . $field . '&user_id=' . bp_displayed_user_id() );  
		
		if (  is_user_logged_in() && bp_displayed_user_id() ):
		
		extract( $args );
  $title = apply_filters( 'widget_title', empty($instance['upm_wdg_title']) ? $widget_name : $instance['upm_wdg_title'], $instance );
		
		if( $location ) :
		
			echo $before_widget;
			echo $before_title .
			 $title .
		  $after_title;
			
  /** 
  * Presently Widget displays only for BP user profile account screens
  */

		?> 
		 
		<div class="map-display user-id-<?php echo bp_displayed_user_id()?>">
  
		<?php if( get_option('upm_display_title') == 'yes'): ?>
		<p class="map-title"><?php echo _e('Location: ', 'bp-upm') ?> <?php echo $location ?> <?php if ( bp_is_my_profile() ) : ?>. <a href="<?php echo bp_loggedin_user_domain() .'profile/edit/' ?>">Edit</a><?php endif; ?></p>
  <?php endif; ?>
		
		<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
  <script type="text/javascript">
      
  var geocoder;
  var map;
  function initialize() {
    geocoder = new google.maps.Geocoder();
       var latlng = new google.maps.LatLng(-34.397, 150.644);
       var myOptions = {
       zoom: <?php echo $instance['upm_wdg_zoom'] ?>,
       center: latlng,
       mapTypeId: google.maps.MapTypeId.ROADMAP
       }
     map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
  }      
  function showAddress(address) {
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);
        var marker = new google.maps.Marker({
            map: map, 
            position: results[0].geometry.location
        });
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
  }      
      
  jQuery(document).ready( function() { initialize(); showAddress('<?php echo $location ?>'); } );

  </script>

  <div id="map_canvas" style="<?php if( !empty($instance['upm_wdg_width']) ) { ?>width: <?php echo $instance['upm_wdg_width'];?>px;<?php } ?><?php if( !empty($instance['upm_wdg_height']) ) { ?> height: <?php echo $instance['upm_wdg_height']; ?>px;<?php } ?>"></div>
  </div>
  <?php    
	/***
  * end map markup generation
  */
  ?>
   
	<?php 
		echo $after_widget; 
	
		endif; // if $location
  		
		endif; // if logged in  / displayed_user
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* This is where you update options for this widget */
  $instance['upm_wdg_title'] = strip_tags($new_instance['upm_wdg_title']);
		$instance['upm_wdg_width'] = strip_tags( $new_instance['upm_wdg_width'] );
		$instance['upm_wdg_height'] = strip_tags( $new_instance['upm_wdg_height'] );
		$instance['upm_wdg_zoom'] = strip_tags( $new_instance['upm_wdg_zoom'] );

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'upm_wdg_width' => 150, 'upm_wdg_height' => 150, 'upm_wdg_title' => 'User Map', 'upm_wdg_zoom' => '11' ) );
		$width = strip_tags( $instance['upm_wdg_width'] );
		$height = strip_tags( $instance['upm_wdg_height'] );
  $title = strip_tags($instance['upm_wdg_title'] );
		$upm_wdg_zoom = strip_tags($instance['upm_wdg_zoom'] );
		?>
		<p><?php _e('This map widget only displays a map while viewing a user profile screen.', 'bp-upm') ?></p>
  <p><label for="bp-upm-title"><?php _e( 'Title:', 'bp-upm' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'upm_wdg_title' ); ?>" name="<?php echo $this->get_field_name( 'upm_wdg_title' ); ?>" type="text" value="<?php echo attribute_escape( $title ); ?>" style="width: 60%" /></label></p>
  <p><?php _e('You may set height on it\'s own  for propotional width or set both values.', 'bp-upm') ?></p>
		<p><label for="bp-upm-width"><?php _e( 'Set the map width:', 'bp-upm' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'upm_wdg_width' ); ?>" name="<?php echo $this->get_field_name( 'upm_wdg_width' ); ?>" type="text" value="<?php echo attribute_escape( $width ); ?>" style="width: 30%" /></label></p>
		<p><label for="bp-upm-height"><?php _e( 'Set the map height:', 'bp-upm' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'upm_wdg_height' ); ?>" name="<?php echo $this->get_field_name( 'upm_wdg_height' ); ?>" type="text" value="<?php echo attribute_escape( $height ); ?>" style="width: 30%" /></label></p>
		<p><label for="bp-upm-zoom"><?php _e( 'Set the map zoom:', 'bp-upm' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'upm_wdg_zoom' ); ?>" name="<?php echo $this->get_field_name( 'upm_wdg_zoom' ); ?>" type="text" value="<?php echo attribute_escape( $upm_wdg_zoom ); ?>" style="width: 30%" /></label></p>
	<?php
	}
}

?>