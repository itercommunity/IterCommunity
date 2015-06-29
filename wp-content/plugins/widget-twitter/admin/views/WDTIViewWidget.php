<?php

class WDTIViewWidget extends WDTIControllerWidget {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  private $model;

  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct($model) {
    $this->model = $model;
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////

  public function display() {
  }

  function widget($args, $instance) {
    extract($args);
    $title = $instance['title'];
    $id = (isset($instance['twitter_id']) ? $instance['twitter_id'] : 0);
    // Before widget.
    echo $before_widget;
    // Title of widget.
    if ($title) {
      echo $before_title . $title . $after_title;
    }
    // Widget output.
    if ($id) {
       echo front_end_twitt($id);
    }
    // After widget.
    echo $after_widget;
  }
  
  // Widget Control Panel.
  function form($instance, $id_title, $name_title,$id_twitter_id, $name_twitter_id) {
    $defaults = array(
      'title' => 'Widget Twitter',
      'twitter_id' => 0,
    );
    $instance = wp_parse_args((array) $instance, $defaults); 
    $get_twitter_tools_rows_data = $this->model->get_twitter_tools_rows_data();
    ?>
    <p>
      <label for="<?php echo $id_title; ?>">Title:</label>
      <input class="widefat" id="<?php echo $id_title; ?>" name="<?php echo $name_title; ?>'" type="text" value="<?php echo $instance['title']; ?>"/>
    </p>
    <p id="p_galleries" style="display:block;">
      <select name="<?php echo $name_twitter_id; ?>" id="<?php echo $id_twitter_id; ?>" class="widefat">
        <option value="0">Select Plugin</option>
        <?php
        foreach ($get_twitter_tools_rows_data as $get_twitter_tools_row_data) {
          ?>
          <option value="<?php echo $get_twitter_tools_row_data->id; ?>" <?php echo (($instance['twitter_id'] == $get_twitter_tools_row_data->id) ? 'selected="selected"' : ''); ?>><?php echo $get_twitter_tools_row_data->title; ?></option>
          <?php
        }
        ?>
      </select>
    </p>
    <?php
  
  
  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Private Methods                                                                    //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Listeners                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
}
}