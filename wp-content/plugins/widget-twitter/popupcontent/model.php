<?php 
class PopupcontentModel {

    ////////////////////////////////////////////////////////////////////////////////////////
    // Events                                                                             //
    ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////
    // Constants                                                                          //
    ////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////
    // Variables                                                                          //
    ////////////////////////////////////////////////////////////////////////////////////////
    //private $controller;

    ////////////////////////////////////////////////////////////////////////////////////////
    // Constructor & Destructor                                                           //
    ////////////////////////////////////////////////////////////////////////////////////////
    public function __construct() {
	
    }
    ////////////////////////////////////////////////////////////////////////////////////////
    // Public Methods                                                                     //
    ////////////////////////////////////////////////////////////////////////////////////////
	public function get_rows_data_for_posts() {
      global $wpdb;
	  $comma = ' , '; 
	  $count = 1;
	  $exclude_post_ids = '';
      $where = ((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != '')) ? 'WHERE post_status="publish" AND post_type="post" AND !(post_title LIKE "%' . esc_html(stripslashes($_POST['search_value'])) . '%")' : 'WHERE post_status="publish" AND post_type="post"');
      $asc_or_desc = ((isset($_POST['asc_or_desc'])) ? esc_html($_POST['asc_or_desc']) : 'asc');
	  $order_by =((isset($_POST['order_by']) && esc_html($_POST['order_by']) != '') ? (esc_html($_POST['order_by'])) : 'post_date'); 	 
	  $search_select_value = ((isset($_POST['search_select_value'])) ? (int) $_POST['search_select_value'] : '');
	  if (isset($_POST['page_number']) && $_POST['page_number']) {
        $limit = ((int) $_POST['page_number'] - 1) * 20;
      }
      else {
        $limit = 0;
      }
	  
	  if((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != ''))) {
	  $query = "SELECT * FROM " . $wpdb->prefix . "posts " . $where . " LIMIT " . $limit . ",100000";
      $rows = $wpdb->get_results($query);
	    foreach ($rows as $row) {
			  $exclude_post_ids.=$row->ID . " ";
			}
	  }
	    $args = array(
			'numberposts'     => 20,
			'offset'          => $limit,
			'category'        => $search_select_value,
			'orderby'         => $order_by,
			'order'           => $asc_or_desc,
			'post_type'       => 'post',
			'post_status'     => 'publish',
			'exclude'         => $exclude_post_ids,
			'include'         => '',
			'meta_key'        => '',
			'meta_value'      => '',
			'post_mime_type'  => '',
			'post_parent'     => ''
         );
	  $posts=get_posts( $args );
	  foreach ($posts as $post) {
	    $categories = get_the_category($post->ID);
	      foreach ($categories as $categorie) {  
	       if($count==sizeof($categories)) $comma = '';
	       $post->cat_name .= $categorie->cat_name .$comma;
	       $count++;
	  }
	    $count = 1;
	    $comma = " , ";
		$user_info = get_userdata($post->post_author);
		$post->user_name = $user_info->user_login; 
	  }
	  
      return $posts;
    }
	
    public function page_nav_for_posts() {
      global $wpdb;
      $where = ((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != '')) ? ' WHERE post_status="publish" AND post_type="post" AND !( post_title LIKE "%' . esc_html(stripslashes($_POST['search_value'])) . '%")'  : 'WHERE post_status="publish" AND post_type="post"');     
	  $search_select_value = ((isset($_POST['search_select_value'])) ? (int) $_POST['search_select_value'] : '');
	  $exclude_post_ids = '';
	  if((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != ''))) {
	  $query = "SELECT * FROM " . $wpdb->prefix . "posts " . $where . " LIMIT 0,100000";
      $rows = $wpdb->get_results($query);
	    foreach ($rows as $row) {
			  $exclude_post_ids .= $row->ID . " ";
			}
	  }
	    $args = array(
			'category'        => $search_select_value,
			'post_type'       => 'post',
			'post_status'     => 'publish',
			'exclude'         => $exclude_post_ids,			
			'numberposts'     => -1,
			'offset'          => 0,
			'orderby'         => '',
			'order'           => '',
			'include'         => '',
			'meta_key'        => '',
			'meta_value'      => '',
			'post_mime_type'  => '',
			'post_parent'     => ''
         );
	  $posts=get_posts( $args ); 
      $page_nav['total'] = sizeof($posts);
      if (isset($_POST['page_number']) && $_POST['page_number']) {
        $limit = ((int) $_POST['page_number'] - 1) * 20;
      }
      else {
        $limit = 0;
      }
      $page_nav['limit'] = (int) ($limit / 20 + 1); 
      return $page_nav;
    }
	
	public function get_rows_data_for_pages() {
      global $wpdb;
	  $exclude_post_ids = '';
      $where = ((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != '')) ? 'WHERE post_status="publish" AND post_type="page" AND !(post_title LIKE "%' . esc_html(stripslashes($_POST['search_value'])) . '%")' : 'WHERE post_status="publish" AND post_type="page"');
      $asc_or_desc = ((isset($_POST['asc_or_desc'])) ? esc_html($_POST['asc_or_desc']) : 'asc');
	  $order_by =((isset($_POST['order_by']) && esc_html($_POST['order_by']) != '') ? (esc_html($_POST['order_by'])) : 'post_date'); 	
	  if (isset($_POST['page_number']) && $_POST['page_number']) {
        $limit = ((int) $_POST['page_number'] - 1) * 20;
      }
      else {
        $limit = 0;
      }	  
	  if((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != ''))) {
	  $query = "SELECT * FROM " . $wpdb->prefix . "posts " . $where . " LIMIT " . $limit . ",100000"; 
      $rows = $wpdb->get_results($query);
	    foreach ($rows as $row) {
			  $exclude_post_ids .= $row->ID . ' ';
			}
	  } 
		 $args = array(
			'sort_order' => $asc_or_desc,
			'sort_column' => $order_by,
			'exclude' => $exclude_post_ids,
			'number' => 20,
			'offset' => $limit,
			'post_type' => 'page',
			'child_of' => 0,
	        'parent' => -1,
			'post_status' => 'publish'
		);
			
	  $posts=get_pages( $args ); 
	  foreach ($posts as $post) {
		$user_info = get_userdata($post->post_author);
		$post->user_name = $user_info->user_login; 
	  }
	  
      return $posts;
    }
	
    public function page_nav_for_pages() {
      global $wpdb;
      $where = ((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != '')) ? ' WHERE post_status="publish" AND post_type="page" AND !(post_title LIKE "%' . esc_html(stripslashes($_POST['search_value'])) . '%")'  : 'WHERE post_status="publish" AND post_type="page"');     
	  $exclude_post_ids = '';
	  if((isset($_POST['search_value']) && (esc_html(stripslashes($_POST['search_value'])) != ''))) {
	  $query = "SELECT * FROM " . $wpdb->prefix . "posts " . $where . " LIMIT 0,100000";
      $rows = $wpdb->get_results($query);
	    foreach ($rows as $row) {
			  $exclude_post_ids.=$row->ID . " ";
			}
	  }		 
		$args = array(
			'sort_order' => '',
			'sort_column' => '',
			'hierarchical' => 1,
			'exclude' => $exclude_post_ids,
			'include' => '',
			'meta_key' => '',
			'meta_value' => '',
			'authors' => '',
			'child_of' => 0,
	        'parent' => -1,
			'exclude_tree' => '',
			'post_type' => 'page',
			'post_status' => 'publish'
		);		 
	  $posts = get_pages( $args ); 
      $page_nav['total'] = sizeof($posts);
      if (isset($_POST['page_number']) && $_POST['page_number']) {
        $limit = ((int) $_POST['page_number'] - 1) * 20;
      }
      else {
        $limit = 0;
      }
      $page_nav['limit'] = (int) ($limit / 20 + 1); 
      return $page_nav;
    }	
	
	public function get_category_ids (){
      global $wpdb;	  
	  $args = array(
		'type'                     => 'post',
		'child_of'                 => 0,
		'parent'                   => '',
		'orderby'                  => 'ID',
		'order'                    => 'ASC',
		'hide_empty'               => 1,
		'hierarchical'             => 1,
		'exclude'                  => '',
		'include'                  => '',
		'number'                   => '',
		'taxonomy'                 => 'category',
		'pad_counts'               => false 
	  ); 
      $categories = get_categories( $args );
      if ($categories) {
        foreach ($categories as $category) {
          $rows[$category->cat_ID] = $category->cat_name;
        }
		  $rows[0] = "Show all categories";
      }
      return $rows;
    }
	
	public function get_twitts() {
      global $wpdb;	  
	  $twitts = $wpdb->get_results("SELECT `id`,`title` FROM ".$wpdb->prefix."twitter_integration WHERE `published`=1 order by `id` DESC");
      return $twitts;
    }	
	
}	
?>