<?php
class oc2wpBMwidget extends WP_Widget {
     
    function __construct() {
      parent::__construct(
         
        // base ID of the widget
        'oc2wpBM_list_widget',
         
        // name of the widget
        __('ownCloud Bookmarks', 'oc2wpBMwidget' ),
         
        // widget options
        array (
            'description' => __( 'Displays bookmarks of your ownCloud instance as list.', 'oc2wpBMwidget' )
        )
         
    );
    
    }
     
    /* Set the form for widget configuration*/
    function form( $instance ) {
    
      $defaults = array(
	  'title' => 'ownCloud Bookmarks',
	  'tags' => 'public',
	  'amount' => 'all',
	  'addInfo' => 'onlyTitle'
	  );
      $title = $instance[ 'title' ];
      $tags = $instance[ 'tags' ];
      $amount = $instance[ 'amount' ];
      $connector = $instance[ 'connector' ];
      $addInfo = $instance[ 'addInfo' ];
      
      
      // markup for form ?>
      <p>
	  <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
	  <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
      </p>
      <p>
	  <label for="<?php echo $this->get_field_id( 'tags' ); ?>">Tags of bookmarks to be displayed:</label>
	  <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'tags' ); ?>" name="<?php echo $this->get_field_name( 'tags' ); ?>" value="<?php echo esc_attr( $tags ); ?>">
      </p>
      <p>
	  <input class="checkbox" type="checkbox" <?php checked($instance['connector_checkbox'], 'on'); ?> id="<?php echo $this->get_field_id('connector_checkbox'); ?>" name="<?php echo $this->get_field_name('connector_checkbox'); ?>" /> 
	  <label for="<?php echo $this->get_field_id('connector_checkbox'); ?>">Bookmark has to contain all tags (AND connector)</label>
      </p>
      <p>
	  <label for="<?php echo $this->get_field_id( 'amount' ); ?>">Amount items ('all' shows all bookmarks):</label>
	  <input class="widefat" type="text" id="<?php echo $this->get_field_id( 'amount' ); ?>" name="<?php echo $this->get_field_name( 'amount' ); ?>" value="<?php echo esc_attr( $amount ); ?>">
      </p>
          <p>
    <label for="<?php echo $this->get_field_id( 'addInfo' ); ?>"><?php _e('Additional Information', 'oc2wpBMwidget'); ?></label> 
    <select id="<?php echo $this->get_field_id( 'addInfo' ); ?>" name="<?php echo $this->get_field_name( 'addInfo' ); ?>" class="widefat" style="width:100%;">
      <option <?php selected( $instance['addInfo'], 'onlyTitle'); ?> value="onlyTitle"><?php _e('Only title','oc2wpBMwidget') ?></option> 
      <option <?php selected( $instance['addInfo'], 'dateDDMMYYYY'); ?> value="dateDDMMYYYY"><?php _e('Date (dd.mm.yyyy)','oc2wpBMwidget') ?></option> 
      <option <?php selected( $instance['addInfo'], 'dateYYYYMMDD'); ?> value="dateYYYYMMDD"><?php _e('Date (yyyy-mm-yy)','oc2wpBMwidget') ?></option> 
      <option <?php selected( $instance['addInfo'], 'descriptionText'); ?> value="descriptionText"><?php _e('Description as additional text','oc2wpBMwidget') ?></option> 
      <option <?php selected( $instance['addInfo'], 'descriptionTitle'); ?> value="descriptionTitle"><?php _e('Description as mouse over','oc2wpBMwidget') ?></option> 
      <option <?php selected( $instance['addInfo'], 'tags'); ?> value="tags"><?php _e('Tags (in brackets)','oc2wpBMwidget') ?></option> 
    </select>
    </p>

	      
  <?php
    }
     
    /* Set behaviour for updating the widget configuration form */
    function update( $new_instance, $old_instance ) {      
	$instance = $old_instance;
	$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
	$instance[ 'tags' ] = strip_tags( $new_instance[ 'tags' ] );
	$instance[ 'amount' ] = strip_tags( $new_instance[ 'amount' ] );
	$instance[ 'connector_checkbox'] = $new_instance['connector_checkbox'];
	$instance[ 'addInfo'] = $new_instance['addInfo'];
	return $instance;
    }

    /* generate the output for the widget area */
    function widget( $args, $instance ) {
	
      // kick things off
      extract( $args );
      $connector_checkbox = $instance['connector_checkbox'] ? 'true' : 'false';
      echo $before_widget;        
      echo $before_title . $instance[ 'title' ] . $after_title;     
	
      // generate Bookmark output
      $tagArray = oc2wpbm_textToTagsArray($instance[ 'tags' ]);

	
      if(get_option('oc2wpbm_op_type')=='sql'){
	$bookmarks = getBMfromSQL($tagArray, 'desc');
      }
      
      if(get_option('oc2wpbm_op_type')=='ocApp'){
	$bookmarks = getBMfromOC($tagArray, 'desc');
      }
      
      //while the OR connector needs no further operations (all Bookmarks can be deployed in the Widget), the AND connector requires to delete within the $bookmark array all those bookmarks that contain not all Bookmarks
      if(strcasecmp($instance[ 'connector_checkbox' ],'on')==0){
      $bookmarks = oc2wpbm_filterBookmarks($bookmarks, $tagArray);
      }
      
      // to fit the layout of the widget area the user can determine the maximal amout of Bookmarks that should be displayed within the widget area.
      $iterator = count($bookmarks);
      if($instance[ 'amount' ] < $iterator) {$iterator = $instance[ 'amount' ];}
      if(strcmp($instance[ 'amount' ], 'all')==0) {$iterator = count($bookmarks);}
      
      // determine the intended output and generate the output
      if(strcmp($instance[ 'addInfo' ], 'onlyTitle')==0){ 
	  for ($i=0; $i<$iterator; $i++){
	  echo "<div class='oc2wp-row'><a href ='" . $bookmarks[$i]->link . "' target='_blank'> ".$bookmarks[$i]->title . " </a> </div>";
	  }
	}
	
      if(strcmp($instance[ 'addInfo' ], 'dateDDMMYYYY')==0){ 
	  for ($i=0; $i<$iterator; $i++){
	  echo "<div class='oc2wp-row'><a href ='" . $bookmarks[$i]->link . "' target='_blank'> <span class='oc2wp-date'>" . date("d.m.Y",$bookmarks[$i]->dateLastModified) . "</span>: " . $bookmarks[$i]->title . " </a> </div>";
	  }
	}
	
      if(strcmp($instance[ 'addInfo' ], 'dateYYYYMMDD')==0){ 
	  for ($i=0; $i<$iterator; $i++){
	  echo "<div class='oc2wp-row'><a href ='" . $bookmarks[$i]->link . "' target='_blank'> <span class='oc2wp-date'>" . date("Y-m-d",$bookmarks[$i]->dateLastModified) . "</span>: " . $bookmarks[$i]->title . " </a> </div>";
	  }
	}
	
      if(strcmp($instance[ 'addInfo' ], 'descriptionText')==0){ 
	  for ($i=0; $i<$iterator; $i++){
	  echo "<div class='oc2wp-row'> <span class='oc2wp-row-title'><a href ='" . $bookmarks[$i]->link . "' target='_blank'>".$bookmarks[$i]->title ." </a>: </span> <span class='oc2wp-description'>" . $bookmarks[$i]->description . "</span></div>";
	  }
	}
	
      if(strcmp($instance[ 'addInfo' ], 'descriptionTitle')==0){ 
	  for ($i=0; $i<$iterator; $i++){
	  echo "<div class='oc2wp-row'><a title='". $bookmarks[$i]->description ."' href ='" . $bookmarks[$i]->link . "' target='_blank'> ".$bookmarks[$i]->title . " </a> </div>";
	  }
	}
	
      if(strcmp($instance[ 'addInfo' ], 'tags')==0){ 
	  for ($i=0; $i<$iterator; $i++){
	  echo "<div class='oc2wp-row'><a title='". $bookmarks[$i]->description ."' href ='" . $bookmarks[$i]->link . "' target='_blank'> ".$bookmarks[$i]->title . " (" . oc2wpbm_arrayToTagstext($bookmarks[$i]->tags)  .") </a> </div>";
	    $tags = $bookmarks[$i]->tags;
	  }
	}
	
    }
     
} 
