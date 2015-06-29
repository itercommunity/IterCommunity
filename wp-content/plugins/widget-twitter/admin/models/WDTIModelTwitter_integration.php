<?php

class WDTIModelTwitter_integration {
  ////////////////////////////////////////////////////////////////////////////////////////
  // Events                                                                             //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constants                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Variables                                                                          //
  ////////////////////////////////////////////////////////////////////////////////////////
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function __construct() {
  }
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
  public function get_rows_data() {
    global $wpdb;
    $where = ((isset($_POST['search_value']) && (esc_html($_POST['search_value']) != '')) ? 'WHERE title LIKE "%' . esc_html($_POST['search_value']) . '%"' : '');
    $asc_or_desc = ((isset($_POST['asc_or_desc'])) ? esc_html($_POST['asc_or_desc']) : 'asc');
    $order_by = ' ORDER BY ' . ((isset($_POST['order_by']) && esc_html($_POST['order_by']) != '') ? '`'.(esc_html($_POST['order_by'])).'`' : 'id') . ' ' . $asc_or_desc;
    if (isset($_POST['page_number']) && $_POST['page_number']) {
      $limit = ((int) $_POST['page_number'] - 1) * 20;
    }
    else {
      $limit = 0;
    }
    $query = "SELECT * FROM " . $wpdb->prefix . "twitter_integration " . $where . $order_by . " LIMIT " . $limit . ",20";
    $rows = $wpdb->get_results($query);
    return $rows;
  }
  public function get_row_data($id) {
    global $wpdb;
    if ($id != 0) {
      $row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'twitter_integration WHERE id="%d"', $id));   
      }
    else {
	     $row= new stdClass();
	     $row->id=0;
		 $row->title = '';
		 $row->type = 'tweetbutton';
		 $row->order = '';
		 $row->published = 1;
		 $row->url = '';
		 $row->lang = 'normal';
		 $row->width = '250';   
		 $row->dnt = 'false';
		 $row->count_mode = 'vertical';
		 $row->url_type = 'auto';
		 $row->via = '';
		 $row->tw_text = '';
		 $row->lang_type = 'auto';
		 $row->counturl = '';
		 $row->but_size = 'medium';
		 $row->align = 'left';
		 $row->show_screen_name = 'true';
		 $row->place = 'bottom';
		 $row->item_place = 'bottom';
		 $row->css = 
'padding-top:0px;	
padding-right:0px;
padding-bottom:0px;
padding-left:0px;
margin-top:0px;
margin-right:0px;
margin-bottom:0px;
margin-left:0px;';
		 $row->height = '250';
		 $row->login_text = '';
		 $row->posts = '';
		 $row->pages = '';
		 $row->code = '';
		 $row->show_count = 0;
		 $row->theme = 'light';
		 $row->link_color = '';
		 $row->chrome = '';
		 $row->border = '';
		 $row->tweet_limit = '';
		 $row->aria_polite = 'polite';
		 $row->show_replies = 'false';
		 $row->screen_name = '';
		 $row->widget_id = '';
		 $row->user_options = '';
		 $row->timeline_type = 'user';
		 $row->tweet_to = '';
		 $row->username_to_1 = '';
		 $row->username_to_2 = '';
		 $row->tw_stories = '';
		 $row->tw_hashtag = '';
		 $row->noheader = '';
		 $row->nofooter = '';
		 $row->noborders = '';
		 $row->noscrollbar = '';
		 $row->transparent = '';
    }
    return $row;
  }
  
  public function get_post_name($id) {
    global $wpdb; 
	$args = array(
			'post_type'       => 'post',
			'post_status'     => 'publish',
			'numberposts'     => '',
			'include'         => $id,
         );
	  $posts=get_posts( $args ); 
	  foreach ( $posts as $post ) {
	    return $post->post_title;
	  }
  }
  
  public function get_pages_name($id) {
    global $wpdb; 
	$args = array(
			'post_type'       => 'page',
			'post_status'     => 'publish',
			'number'     => '',
			'include'         => $id,
         );
	  $posts=get_pages( $args ); 
	  foreach ( $posts as $post ) {
	    return $post->post_title;
	  }
  }
  
  public function page_nav() {
    global $wpdb;
    $where = ((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != '')) ? ' WHERE title LIKE "%' . esc_html(stripslashes($_POST['search_value'])) . '%"'  : '');
    $query = "SELECT COUNT(*) FROM " . $wpdb->prefix . "twitter_integration " . $where;
    $total = $wpdb->get_var($query);
    $page_nav['total'] = $total;
    if (isset($_POST['page_number']) && $_POST['page_number']) {
      $limit = ((int) $_POST['page_number'] - 1) * 20;
    }
    else {
      $limit = 0;
    }
    $page_nav['limit'] = (int) ($limit / 20 + 1);
    return $page_nav;
  } 
}
?>