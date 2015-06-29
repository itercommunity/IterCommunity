<?php 
class PopupcontentView {

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
  private $model;
  private $function_kind;
  ////////////////////////////////////////////////////////////////////////////////////////
  // Constructor & Destructor                                                           //
  ////////////////////////////////////////////////////////////////////////////////////////
	
  public function __construct($function_kind,$model) {
      $this->function_kind = $function_kind;
      $this->model = $model;
  }
	
  ////////////////////////////////////////////////////////////////////////////////////////
  // Public Methods                                                                     //
  ////////////////////////////////////////////////////////////////////////////////////////
    
  public function execute() {
    if($this->function_kind=="display_posts_for_twitt")
    $this->display_posts_for_twitt();
    else if($this->function_kind=="display_pages_for_twitt")
    $this->display_pages_for_twitt();
	else if($this->function_kind=="display_shortcode_for_twitt")
	$this->display_shortcode_for_twitt();
  }
    
  public function display_posts_for_twitt() {
    global $wpdb;
    $rows_data = $this->model->get_rows_data_for_posts(); 
	$page_nav = $this->model->page_nav_for_posts();
    $search_value = ((isset($_POST['search_value'])) ? esc_html(stripslashes($_POST['search_value'])) : '');
	$search_select_value = ((isset($_POST['search_select_value'])) ? (int) $_POST['search_select_value'] : "");
    $order_by = (isset($_POST['order_by']) ? esc_html(stripslashes($_POST['order_by'])) : 'post_date'); 
    $asc_or_desc = ((isset($_POST['asc_or_desc'])) ? esc_html(stripslashes($_POST['asc_or_desc'])) : 'asc');
    $order_class = 'manage-column column-title sorted ' . $asc_or_desc;
	?>
      <link media="all" type="text/css" href="<?php echo get_admin_url(); ?>load-styles.php?c=1&amp;dir=ltr&amp;load=admin-bar,wp-admin,dashicons,buttons,wp-auth-check" rel="stylesheet">
      <link media="all" type="text/css" href="<?php echo get_admin_url(); ?>css/colors<?php echo ((get_bloginfo('version') < '3.8') ? '-fresh' : ''); ?>.min.css" id="colors-css" rel="stylesheet">
      <link media="all" type="text/css" href="<?php echo WD_WDTI_URL . '/css/twitt_tables.css'; ?>" id="twitt_tables-css" rel="stylesheet">
	  <script src="<?php echo get_option("siteurl"); ?>/wp-includes/js/jquery/jquery.js" type="text/javascript"></script>
      <script src="<?php echo WD_WDTI_URL . '/js/twitt.js?ver=3.6'; ?>" type="text/javascript"></script>
	  <form class="wrap" id="posts_form" method="post" action="<?php echo add_query_arg(array('action' => 'addPostsPages', 'width' => '700', 'height' => '550', 'function_kind' => 'display_posts_for_twitt', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" style="width:95%; margin: 0 auto;">
	  <h2 style="width:200px; float:left;">Posts</h2>
      <a href="" class="thickbox thickbox-preview" id="content-add_media" title="Add Tag" onclick="twitt_get_posts(event);" style="float:right; padding: 9px 0px 4px 0">
        <img src="<?php echo WD_WDTI_URL . '/images/add_but.png'; ?>" style="border:none;"/>
      </a>
	  <div class="tablenav top">
        <?php
		WDWLibrary::search_select('Category',$search_select_value, $this->model->get_category_ids(), 'posts_form');
        WDWLibrary::search('Title', $search_value, 'posts_form');
        WDWLibrary::html_page_nav($page_nav['total'], $page_nav['limit'], 'posts_form');
        ?>
      </div>
	  <table class="wp-list-table widefat fixed pages">
        <thead>
          <th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;margin-right: 6.5px;" /></th>
          <th class="table_small_col <?php if ($order_by == 'ID') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('task', '');
                        spider_set_input_value('order_by', 'ID');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'ID') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
						spider_form_submit(event, 'posts_form')" href="" style="padding-right:6px">
              <span>ID</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th class="<?php if ($order_by == 'post_title') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('task', '');
                        spider_set_input_value('order_by', 'post_title');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'post_title') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
						spider_form_submit(event, 'posts_form')" href="">
              <span>Title</span><span class="sorting-indicator"></span>
            </a>
          </th>
		  <th class="<?php if ($order_by == 'post_author') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('order_by', 'post_author');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'post_author') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'posts_form')" href="">
              <span>Author</span><span class="sorting-indicator"></span>
            </a>
          </th>
		  <th class="<?php if ($order_by == 'category') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('order_by', 'category');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'category') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
						spider_form_submit(event, 'posts_form')" href="">
              <span>Categories</span><span class="sorting-indicator"></span>
            </a>
          </th>
        </thead>
        <tbody id="tbody_arr">
          <?php
          if ($rows_data) {
            foreach ($rows_data as $row_data) {
              ?>
              <tr id="tr_<?php echo $row_data->ID; ?>">
                <td class="table_small_col check-column"><input id="check_<?php echo $row_data->ID; ?>" name="check_<?php echo $row_data->ID; ?>" type="checkbox" /></td>
                <td class="table_small_col"><?php echo $row_data->ID; ?></td>
                <td>
				<a id="a_<?php echo $row_data->ID; ?>" onclick="window.parent.twitt_add_post(['<?php echo $row_data->ID; ?>'],['<?php echo htmlspecialchars(addslashes($row_data->post_title))?>'])" style="cursor:pointer" title="Add post"><?php echo $row_data->post_title; ?></a>
				</td>
				<td><?php echo $row_data->user_name; ?></td>
				<td><?php echo $row_data->cat_name; ?></td>
              </tr>
              <?php
            }
          }
          ?>
        </tbody>
      </table>
	  <input id="asc_or_desc" name="asc_or_desc" type="hidden" value="asc" />
      <input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>" />
	  </form>
	  <script src="<?php echo get_admin_url(); ?>load-scripts.php?c=1&load%5B%5D=common,admin-bar" type="text/javascript"></script>
	 <?php
	 die();
   	}
	
  public function display_pages_for_twitt() {
	global $wpdb;
    $rows_data = $this->model->get_rows_data_for_pages(); 
	$page_nav = $this->model->page_nav_for_pages();
    $search_value = ((isset($_POST['search_value'])) ? esc_html(stripslashes($_POST['search_value'])) : '');
    $order_by = (isset($_POST['order_by']) ? esc_html(stripslashes($_POST['order_by'])) : 'post_date'); 
    $asc_or_desc = ((isset($_POST['asc_or_desc'])) ? esc_html(stripslashes($_POST['asc_or_desc'])) : 'asc');
    $order_class = 'manage-column column-title sorted ' . $asc_or_desc;
	?>
      <link media="all" type="text/css" href="<?php echo get_admin_url(); ?>load-styles.php?c=1&amp;dir=ltr&amp;load=admin-bar,wp-admin,dashicons,buttons,wp-auth-check" rel="stylesheet">
      <link media="all" type="text/css" href="<?php echo get_admin_url(); ?>css/colors<?php echo ((get_bloginfo('version') < '3.8') ? '-fresh' : ''); ?>.min.css" id="colors-css" rel="stylesheet">
      <link media="all" type="text/css" href="<?php echo WD_WDTI_URL . '/css/twitt_tables.css'; ?>" id="twitt_tables-css" rel="stylesheet">
	  <script  src="<?php echo get_option("siteurl"); ?>/wp-includes/js/jquery/jquery.js" type="text/javascript"></script>
      <script src="<?php echo WD_WDTI_URL . '/js/twitt.js?ver=3.6'; ?>" type="text/javascript"></script>
	  <form class="wrap" id="posts_form" method="post" action="<?php echo add_query_arg(array('action' => 'addPostsPages', 'width' => '700', 'height' => '550', 'function_kind' => 'display_pages_for_twitt', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" style="width:95%; margin: 0 auto;">
	  <h2 style="width:200px; float:left;">Pages</h2>
      <a href="" class="thickbox thickbox-preview" id="content-add_media" title="Add Tag" onclick="twitt_get_pages(event);" style="float:right; padding: 9px 0px 4px 0">
        <img src="<?php echo WD_WDTI_URL . '/images/add_but.png'; ?>" style="border:none;"/>
      </a>
	  <div class="tablenav top">
        <?php
        WDWLibrary::search('Title', $search_value, 'posts_form');
        WDWLibrary::html_page_nav($page_nav['total'], $page_nav['limit'], 'posts_form');
        ?>
      </div>
	  <table class="wp-list-table widefat fixed pages">
        <thead>
          <th class="manage-column column-cb check-column table_small_col"><input id="check_all" type="checkbox" style="margin:0;margin-right: 6.5px;" /></th>
          <th class="<?php if ($order_by == 'ID') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('task', '');
                        spider_set_input_value('order_by', 'ID');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'ID') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
						spider_form_submit(event, 'posts_form')" href="" style="padding-right:6px">
              <span>ID</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th class="<?php if ($order_by == 'post_title') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('task', '');
                        spider_set_input_value('order_by', 'post_title');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'post_title') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
						spider_form_submit(event, 'posts_form')" href="">
              <span>Title</span><span class="sorting-indicator"></span>
            </a>
          </th>
		  <th class="<?php if ($order_by == 'post_author') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('order_by', 'post_author');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'post_author') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'posts_form')" href="">
              <span>Author</span><span class="sorting-indicator"></span>
            </a>
          </th>
        </thead>
        <tbody id="tbody_arr">
          <?php
          if ($rows_data) {
            foreach ($rows_data as $row_data) {
              ?>
              <tr id="tr_<?php echo $row_data->ID; ?>">
                <td class="table_small_col check-column"><input id="check_<?php echo $row_data->ID; ?>" name="check_<?php echo $row_data->ID; ?>" type="checkbox" /></td>
                <td><?php echo $row_data->ID; ?></td>
                <td>
				<a id="a_<?php echo $row_data->ID; ?>" onclick="window.parent.twitt_add_page(['<?php echo $row_data->ID; ?>'],['<?php echo htmlspecialchars(addslashes($row_data->post_title))?>'])" style="cursor:pointer" title="Add post"><?php echo $row_data->post_title; ?></a>
				</td>
				<td><?php echo $row_data->user_name; ?></td>
              </tr>
              <?php
            }
          }
          ?>
        </tbody>
      </table>
	  <input id="asc_or_desc" name="asc_or_desc" type="hidden" value="asc" />
      <input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>" />
	  </form>
	  <script src="<?php echo get_admin_url(); ?>load-scripts.php?c=1&load%5B%5D=common,admin-bar" type="text/javascript"></script>
	 <?php
	 die();
   	}

  public function display_shortcode_for_twitt() {
    global $wpdb;
    $twitts = $this->model->get_twitts(); 
	?>
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	 <title>Widget Twitter</title>
	 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	 <script language="javascript" type="text/javascript" src="<?php echo get_option("siteurl"); ?>/wp-includes/js/jquery/jquery.js"></script>
	 <script language="javascript" type="text/javascript" src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	 <script language="javascript" type="text/javascript" src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/utils/mctabs.js"></script>
	 <script language="javascript" type="text/javascript" src="<?php echo get_option("siteurl"); ?>/wp-includes/js/tinymce/utils/form_utils.js"></script>
	 <script src="<?php echo WD_WDTI_URL . '/js/twitt.js?ver=3.6'; ?>" type="text/javascript"></script>
	 <base target="_self">
	</head>
	<body id="link"  style="" dir="ltr" class="forceColors">
		<div class="tabs" role="tablist" tabindex="-1">
		 <ul>
		  <li id="form_maker_tab" class="current" role="tab" tabindex="0"><span><a href="javascript:mcTabs.displayTab('Single_product_tab','Single_product_panel');" onMouseDown="return false;" tabindex="-1">Widget Twitter</a></span></li>
		 </ul>
		</div>
	    <div class="panel_wrapper" style="height:200px !important" >
		 <div id="Single_product_panel" class="panel current">
		  <table>
		   <tr>
			<td style="height:100px; width:170px; vertical-align:top;">
			Select a Widget Twitter Plugin 
			</td>
			<td style="vertical-align:top">
			 <select name="Widget_Twitter" id="Widget_Twitter" style="width:190px; text-align:center; margin-top: 2px;">
			  <option  style="text-align:center" value="Select_a_Twitter" selected="selected">Select Plugin</option>
			   <?php foreach($twitts as $twitt) { ?>
			  <option value="<?php echo $twitt->id; ?>"><?php echo $twitt->title; ?></option>
			   <?php } ?>
			 </select>
		    </td>
		   </tr>
		  </table>
		 </div>
		</div>
		<div class="mceActionPanel">
		 <div style="float: left">
		   <input type="button" id="cancel" name="cancel" value="Cancel" onClick="tinyMCEPopup.close();" />
		 </div>	
		 <div style="float: right">
		   <input type="submit" id="insert" name="insert" value="Insert" onClick="insert_twitt();" />
		 </div>
		</div>
	</body>
	</html>
	<?php	
	 die();
	}
}	
?>