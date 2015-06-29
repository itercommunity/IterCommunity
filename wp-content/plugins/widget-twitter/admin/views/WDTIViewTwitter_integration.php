<?php

class WDTIViewTwitter_integration{

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
   $rows_data = $this->model->get_rows_data();
   $page_nav = $this->model->page_nav();
   $search_value = ((isset($_POST['search_value'])) ? esc_html(stripslashes($_POST['search_value'])) : '');
   $order_by = (isset($_POST['order_by']) ? esc_html(stripslashes($_POST['order_by'])) : 'order');
   $asc_or_desc = ((isset($_POST['asc_or_desc'])) ? esc_html(stripslashes($_POST['asc_or_desc'])) : 'asc');
   $order_class = 'manage-column column-title sorted ' . $asc_or_desc;
  ?>
    <div style="float: right; text-align: right;">
        <a style="color: red; text-decoration: none;" target="_blank" href="http://web-dorado.com/files/fromTwitterIntegrationWP.php">
          <img width="215" border="0" alt="web-dorado.com" src="<?php echo WD_WDTI_URL . '/images/header.png'; ?>" />
          <p style="font-size: 16px; margin: 0; padding: 0 20px 0 0;">Get the full version</p>
        </a>
    </div>
    <form class="wrap" id="spider_twitt_form" method="post" action="admin.php?page=twitter_integration" style="width:95%;">
      <span class="twitt-icon">
	   	<img id="twitt_for_admin" class="twitt_for_admin" src="<?php echo WD_WDTI_URL."/images/twitt_for_admin.png"?>"/>
	  </span>
      <h2>
        Widget Twitter
        <a href="" class="add-new-h2" onclick="spider_set_input_value('task', 'add');
                                               spider_form_submit(event, 'spider_twitt_form')">Add new</a>
      </h2>
      <div id="draganddrop" class="updated" style="display:none;"><strong><p>Changes made in this table shoud be saved.</p></strong></div>
      <div class="buttons_div">
        <input id="show_hide_weights"  class="button" type="button" onclick="spider_show_hide_weights();return false;" value="Hide order column" style="border-radius:0px !important;" />
        <input class="button" type="submit" onclick="spider_set_input_value('task', 'save_order')" value="Save Order" style="border-radius:0px !important;" />
        <input class="button" type="submit" onclick="spider_set_input_value('task', 'publish_all')" value="Publish" style="border-radius:0px !important;" />
        <input class="button" type="submit" onclick="spider_set_input_value('task', 'unpublish_all')" value="Unpublish" style="border-radius:0px !important;" />
        <input class="button" type="submit" onclick="if (confirm('Do you want to delete selected items?')) {
                                                       spider_set_input_value('task', 'delete_all');
                                                     } else {
                                                       return false;
                                                     }" value="Delete" style="border-radius:0px !important;" />
      </div>
      <div class="tablenav top">
        <?php
        WDWLibrary::search('Title', $search_value, 'spider_twitt_form');
        WDWLibrary::html_page_nav($page_nav['total'], $page_nav['limit'], 'spider_twitt_form');
        ?>
      </div>
      <table class="wp-list-table widefat fixed pages">
        <thead>
          <th class="table_small_col"></th>
          <th class="manage-column column-cb check-column table_small_col" style="padding-right:8.9px" ><input id="check_all" type="checkbox" style="margin:0" /></th>
          <th class="table_small_col <?php if ($order_by == 'id') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('task', '');
                        spider_set_input_value('order_by', 'id');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'id') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'spider_twitt_form')" href="">
              <span>ID</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th class="<?php if ($order_by == 'title') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('task', '');
                        spider_set_input_value('order_by', 'title');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'title') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'spider_twitt_form')" href="">
              <span>Title</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th class="<?php if ($order_by == 'type') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('task', '');
                        spider_set_input_value('order_by', 'type');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'type') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'spider_twitt_form')" href="">
              <span>Type</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th id="th_order" class="table_medium_col <?php if ($order_by == 'order') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('task', '');
                        spider_set_input_value('order_by', 'order');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'order') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
                        spider_form_submit(event, 'spider_twitt_form')" href="">
              <span>Order</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th class="table_big_col <?php if ($order_by == 'published') {echo $order_class;} ?>">
            <a onclick="spider_set_input_value('task', '');
                        spider_set_input_value('order_by', 'published');
                        spider_set_input_value('asc_or_desc', '<?php echo ((isset($_POST['asc_or_desc']) && isset($_POST['order_by']) && (esc_html(stripslashes($_POST['order_by'])) == 'published') && esc_html(stripslashes($_POST['asc_or_desc'])) == 'asc') ? 'desc' : 'asc'); ?>');
						spider_form_submit(event, 'spider_twitt_form')" href="">
              <span>Published</span><span class="sorting-indicator"></span>
            </a>
          </th>
          <th class="table_big_col">Edit</th>
          <th class="table_big_col">Delete</th>
        </thead>
        <tbody id="tbody_arr">
          <?php
          if ($rows_data) {
            foreach ($rows_data as $row_data) {
              $published_image = (($row_data->published) ? 'publish' : 'unpublish');
              $published = (($row_data->published) ? 'unpublish' : 'publish');
              ?>
              <tr id="tr_<?php echo $row_data->id; ?>">
                <td class="connectedSortable table_small_col"><div title="Drag to re-order"class="handle" style="margin:5px auto 0 auto;"></div></td>
                <td class="table_small_col check-column"><input id="check_<?php echo $row_data->id; ?>" name="check_<?php echo $row_data->id; ?>" type="checkbox" /></td>
                <td class="table_small_col"><?php echo $row_data->id; ?></td>
                <td><a onclick="spider_set_input_value('task', 'edit');
                                spider_set_input_value('current_id', '<?php echo $row_data->id; ?>');
                                spider_form_submit(event, 'spider_twitt_form')" href="" title="Edit"><?php echo $row_data->title; ?></a></td>
                <td><?php echo $row_data->type; ?></td>
                <td class="spider_order table_medium_col"><input id="order_input_<?php echo $row_data->id; ?>" name="order_input_<?php echo $row_data->id; ?>" type="text" size="1" value="<?php echo $row_data->order; ?>" /></td>
                <td class="table_big_col"><a onclick="spider_set_input_value('task', '<?php echo $published; ?>');spider_set_input_value('current_id', '<?php echo $row_data->id; ?>');spider_form_submit(event, 'spider_twitt_form')" href=""><img src="<?php echo WD_WDTI_URL . '/images/' . $published_image . '.png'; ?>"></img></a></td>
                <td class="table_big_col"><a onclick="spider_set_input_value('task', 'edit');
                                                      spider_set_input_value('current_id', '<?php echo $row_data->id; ?>');
                                                      spider_form_submit(event, 'spider_twitt_form')" href="">Edit</a></td>
                <td class="table_big_col"><a onclick="spider_set_input_value('task', 'delete');
                                                      spider_set_input_value('current_id', '<?php echo $row_data->id; ?>');
                                                      spider_form_submit(event, 'spider_twitt_form')" href="">Delete</a></td>
              </tr>
              <?php
            }
          }
          ?>
        </tbody>
      </table>
	  <?php wp_nonce_field('nonce_wg_twitt', 'nonce_wg_twitt'); ?>
      <input id="task" name="task" type="hidden" value="" />
      <input id="current_id" name="current_id" type="hidden" value="" />
      <input id="asc_or_desc" name="asc_or_desc" type="hidden" value="asc" />
      <input id="order_by" name="order_by" type="hidden" value="<?php echo $order_by; ?>" />
      <script>
        window.onload = spider_show_hide_weights;
      </script>
    </form>
	<?php 
  }
  
  public function edit($id) {
   $user_agent = $_SERVER['HTTP_USER_AGENT'];
   $row = $this->model->get_row_data($id);
  ?>
	<style>
	    .wdti_label_for_select:after {
			content:<?php if (preg_match('/Firefox/i', $user_agent)) echo "none"; else echo "'<>'";?>;
		}
	</style>
    <div style="float: right; text-align: right;">
        <a style="color: red; text-decoration: none;" target="_blank" href="http://web-dorado.com/files/fromTwitterIntegrationWP.php">
          <img width="215" border="0" alt="web-dorado.com" src="<?php echo WD_WDTI_URL . '/images/header.png'; ?>" />
          <p style="font-size: 16px; margin: 0; padding: 0 20px 0 0;">Get the full version</p>
        </a>
    </div>
 <form class="wrap" method="post" id="spider_twitt__form" action="admin.php?page=twitter_integration" style="width:95%;">
     <div style="float:right;">
        <input class="button" type="submit" style="border-radius:0px !important;" onclick="if (spider_check_required('title', 'Title')) {return false;}; if (document.getElementById('url_tr').style.display=='' && spider_check_required('url_tr_for', 'URL to Tweet')) {return false;}; if (document.getElementById('url_tr').style.display=='' && check_url_to_twitt()) {return false;}; if (check_hashtag()) {return false;};
                                                     spider_set_input_value('task', 'save')" value="Save" />
        <input class="button" type="submit" style="border-radius:0px !important;" onclick="if (spider_check_required('title', 'Title')) {return false;}; if (document.getElementById('url_tr').style.display=='' && spider_check_required('url_tr_for', 'URL to Tweet')) {return false;}; if (document.getElementById('url_tr').style.display=='' && check_url_to_twitt()) {return false;}; if (check_hashtag()) {return false;};
                                                     spider_set_input_value('task', 'apply')" value="Apply" />
        <input class="button" type="submit" style="border-radius:0px !important;" onclick="spider_set_input_value('task', 'cancel')" value="Cancel" />
     </div>
       <div class="wdti_type_div_conteiner">
         <div class="wdti_type_div_content">
           <div class="wdti_span">
	        <img id="wdti_img" class="wdti_img" src="<?php echo WD_WDTI_URL."/images/twitterbg.png"?>"/>
           </div>
           <div class="wdti_type_span" onclick="spider_twitt_change_type('tweetbutton')" >
	        <img id="tweetbutton" class="wdti_type_img" src="<?php echo WD_WDTI_URL."/images/tweet.png"?>"/>
		    <img id="tweetbutton_hover" class="wdti_hover_img" src="<?php echo WD_WDTI_URL."/images/tweet-hover.png"?>"/>
           </div>    
           <div class="wdti_type_span" onclick="spider_twitt_change_type('followbutton')" style="right:3px" >
	        <img id="followbutton" class="wdti_type_img" src="<?php echo WD_WDTI_URL."/images/follow.png"?>"/>
		    <img id="followbutton_hover" class="wdti_hover_img" src="<?php echo WD_WDTI_URL."/images/follow-hover.png"?>"/>
           </div>     
           <div class="wdti_type_span" style="right:6px;opacity:0.3">
		    <img id="timeline" class="wdti_type_img" src="<?php echo WD_WDTI_URL."/images/timeline.png"?>"/>
		    <img id="timeline_hover" class="wdti_hover_img" src="<?php echo WD_WDTI_URL."/images/timeline-hover.png"?>"/>
           </div>
           <div class="wdti_type_span" onclick="spider_twitt_change_type('mention')" style="right:9px" >
		    <img id="mention" class="wdti_type_img" src="<?php echo WD_WDTI_URL."/images/mention.png"?>"/>
		    <img id="mention_hover" class="wdti_hover_img" src="<?php echo WD_WDTI_URL."/images/mention-hover.png"?>"/>
           </div> 		
           <div class="wdti_type_span" onclick="spider_twitt_change_type('hashtag')" style="right:12px" >
		    <img id="hashtag" class="wdti_type_img" src="<?php echo WD_WDTI_URL."/images/hashtag.png"?>"/>
		    <img id="hashtag_hover" class="wdti_hover_img" src="<?php echo WD_WDTI_URL."/images/hashtag-hover.png"?>"/>
           </div>   
          </div>
       </div>
   <table id="wdti_admintable" class="wdti_admintable">
	 <tr class="wdti_tr" id="title_tr">
	   <td class="wdti_td">
		 <label class="wdti_label" for="title">Title:</label>
	   </td>
	   <td >
         <input class="wdti_input spider_twitt_required" size="40" type="text" name="title" id="title" value="<?php echo $row->title; ?>"  />
	   </td>
     </tr>
	 <tr class="wdti_tr" id="note">
	   <td class="wdti_td">
		 <label class="wdti_label" for="notification" >Description:</label>
	   </td>
	   <td>
         <p id="notification" class="wdti_description_p"></p>
	   </td>
	 </tr>
     <tr class="wdti_tr" id="published">
       <td class="wdti_td">
	     <label class="wdti_label">Published:</label></td>
       <td>	 
         <input type="radio" class="wdti_checkbox" id="published1" name="published" <?php echo (($row->published==1) ? 'checked="checked"' : ''); ?> value="1" >
         <label class="wdti_label_rad wdti_label_check" for="published1">Yes</label>	   
         <input type="radio" class="wdti_checkbox" id="published0" name="published" <?php echo (($row->published==0) ? 'checked="checked"' : ''); ?> value="0" >
         <label class="wdti_label_rad wdti_label_check" for="published0">No</label>
       </td>
     </tr>
     <tr class="wdti_tr" id="tw_stories">
	   <td class="wdti_td"> 
		 <label class="wdti_label" for="stories">Tweeter Stories:</label>
	   </td>
	   <td>
         <span class="wdti_add-on">#</span> 
		 <input class="wdti_input_for_add_on wdti_input" type="text" name="tw_stories" id="stories" size="35" style="margin-left:31px;" value="<?php echo $row->tw_stories; ?>"/>
	   </td>
     </tr>	 
     <tr class="wdti_tr" id="tweet_to">
	   <td class="wdti_td">
		 <label class="wdti_label" for="tweet_to_1">Tweet to:</label>
	   </td>
	   <td>
		 <span class="wdti_add-on">@</span> 
         <input class="wdti_input_for_add_on wdti_input" type="text" name="tweet_to" id="tweet_to_1" size="35" style="margin-left:31px;" value="<?php echo $row->tweet_to; ?>" />
	   </td>
     </tr>
	 <tr class="wdti_tr" id="screen_name">
	   <td class="wdti_td">
		 <label class="wdti_label" for="screen_name_for">User to follow:</span></label>
	   </td>
	   <td >
	     <span class="wdti_add-on">@</span>
		  <input class="wdti_input_for_add_on wdti_input"  type="text" name="screen_name" id="screen_name_for" size="35" style="margin-left:31px;" value="<?php echo $row->screen_name; ?>"/>           
       </td>
	 </tr>
	 <tr  id="via_tr">
	   <td class="wdti_td"> 
         <label class="wdti_label" for="via">Via user:<span style="cursor:pointer;color:#3B5998" id="tooltip_span" class="wdti_tooltip" title="Choose the screen name of the user to attribute Twitter to."> [?]</span></label>
	   </td>
	   <td>
         <span class="wdti_add-on">@</span>
		 <input type="text" id="via" name="via"  size="35" class="wdti_input_for_add_on wdti_input"  style="margin-left:31px;" value="<?php echo $row->via; ?>"/>
	   </td>
     </tr>
	 <tr class="wdti_tr" id="text_tr">
	   <td class="wdti_td"> 
		 <label class="wdti_label" for="text_for">Tweet text:<span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip" title="Default Tweet text. If you leave it blank, it will automatically use the title of the post."> [?]</span></label>
	   </td>
	   <td>
         <input type="text" class="wdti_input" name="tw_text"  id="text_for" size="40" value="<?php echo $row->tw_text; ?>"/>
	   </td>
     </tr>
	 <tr class="wdti_tr" id="username_to_1">
	   <td class="wdti_td">
		 <label class="wdti_label" for="username_to_1_for">Username to Recommend:<span style="cursor:pointer;color:#3B5998" class="wdti_tooltip" title="Using the Username to Recommend field you can suggest account for a user to follow once they have sent a Tweet using your Tweet Button."> [?]</span></label>
	   </td>
	   <td >
         <span class="wdti_add-on">@</span>  
	     <input class="wdti_input_for_add_on wdti_input" type="text" name="username_to_1" id="username_to_1_for" size="35" style="margin-left:31px;" value="<?php echo $row->username_to_1; ?>" />
	   </td>
     </tr>
	 <tr class="wdti_tr" id="username_to_2">
	   <td class="wdti_td">
		 <label class="wdti_label" for="username_to_2_for">Username2 to Recommend:<span style="cursor:pointer;color:#3B5998" class="wdti_tooltip" title="Using the Username2 to Recommend field you can suggest second account for a user to follow once they have sent a Tweet using your Tweet Button."> [?]</span></label>
	   </td>
	   <td>
		 <span class="wdti_add-on">@</span> 
         <input  class="wdti_input_for_add_on wdti_input" type="text" name="username_to_2" id="username_to_2_for" size="35" style="margin-left:31px;" value="<?php echo $row->username_to_2; ?>" />
	   </td>
     </tr> 				
	 <tr class="wdti_tr" id="url_type">
       <td class="wdti_td">
	     <label class="wdti_label">Type of URL:<span style="cursor:pointer;color:#3B5998" class="wdti_tooltip" title="URL of the page to share"> [?]</span></label>
	   </td>
       <td>
         <input type="radio" class="wdti_checkbox"  id="url_type0" name="url_type" <?php echo (($row->url_type=="normal") ? 'checked="checked"' : ''); ?> onchange="spider_twitt_change_url('normal')"  value="normal" >
         <label class="wdti_label_rad wdti_label_check" for="url_type0">URL</label>
         <input type="radio" class="wdti_checkbox"  id="url_type1" name="url_type" <?php echo (($row->url_type=="auto") ? 'checked="checked"' : ''); ?> onchange="spider_twitt_change_url('auto')" value="auto" >
         <label class="wdti_label_rad wdti_label_check" for="url_type1">Current</label>
       </td>
     </tr>	 
	 <tr class="wdti_tr" id="url_tr">
	   <td class="wdti_td"> 
		 <label class="wdti_label" for="url_tr_for" id="l_url">URL to Tweet:<span style="cursor:pointer;color:#3B5998" class="wdti_tooltip" title="HTTP Referrer- e.g. http://web-dorado.com/web-dorado.html "> [?]</span></label>
	   </td>
	   <td >				
         <input class="wdti_input" type="text" name="url"  size="40" id="url_tr_for" value="<?php echo $row->url; ?>"/>
	   </td>
     </tr>	 
	 <tr class="wdti_tr" id="counturl_tr">
	   <td class="wdti_td"> 
	     <label class="wdti_label" for="counturl_tr_1" id="l_url">URL to which your shared URL resolves:<span style="cursor:pointer;color:#3B5998" class="wdti_tooltip"  id="tooltip_id9" title="URL to which your shared URL resolves- e.g. web-dorado.com"> [?]</span></p></label>
	   </td>
	   <td >
		 <input class="wdti_input" type="text" name="counturl" size="40" id="counturl_tr_1" value="<?php echo $row->counturl; ?>"/>
	   </td>
     </tr>	 
	 <tr class="wdti_tr" id="tw_hashtag">
	   <td class="wdti_td">
		 <label class="wdti_label" for="hashtag_for">Hashtag (#): <span style="cursor:pointer;color:#3B5998" class="wdti_tooltip" title="You can provide multiple hashtags by separating  them with commas."> [?]</span></label>
	   </td>
	   <td >
         <span class="wdti_add-on">#</span>
		 <input class="wdti_input_for_add_on wdti_input" type="text"  name="tw_hashtag" id="hashtag_for" size="35" style="margin-left:31px;" value="<?php echo $row->tw_hashtag; ?>" />
	   </td>
     </tr>	 
	 <tr class="wdti_tr" id="but_size">
       <td class="wdti_td">
	     <label class="wdti_label">Button Size: <span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip" title="Choose the size of the rendered button.">[?]</span></label>
       <td>
         <input type="radio" class="wdti_checkbox"  id="but_size0" name="but_size" <?php echo (($row->but_size=="medium") ? 'checked="checked"' : ''); ?> value="medium" >
         <label class="wdti_label_rad wdti_label_check" for="but_size0">Medium</label>
         <input type="radio" class="wdti_checkbox"  id="but_size1" name="but_size" <?php echo (($row->but_size=="large") ? 'checked="checked"' : ''); ?> value="large" >
         <label class="wdti_label_rad wdti_label_check" for="but_size1">Large</label>
       </td>
     </tr>
	 <tr class="wdti_tr" id="show_count">
       <td class="wdti_td">
	     <label class="wdti_label">Followers count display: <span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip" title="By default, the User's followers count is not displayed with the Follow Button.You can enable the display. "> [?]</span></label>
       <td>
	     <input type="radio" class="wdti_checkbox"  id="show_count1" name="show_count" <?php echo (($row->show_count=="true") ? 'checked="checked"' : ''); ?> value="true" >
         <label class="wdti_label_rad wdti_label_check" for="show_count1">Yes</label>
         <input type="radio" class="wdti_checkbox"  id="show_count0" name="show_count" <?php echo (($row->show_count=="false") ? 'checked="checked"' : ''); ?> value="false" >
         <label class="wdti_label_rad wdti_label_check" for="show_count0">No</label>
       </td>
     </tr>
	 <tr class="wdti_tr" id="width">
       <td class="wdti_td">
         <label class="wdti_label" for="width_for">Width: <span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip" title="Define the width of the social plugin in pixels.The height is set by default."> [?]</span></label>
       </td>
       <td>
         <input class="wdti_input" type="text" name="width" id="width_for" size="40" value="<?php echo $row->width; ?>" onkeypress="return spider_check_isnum(event)" />
       </td>
     </tr>	 
     <tr class="wdti_tr" id="align">
	   <td class="wdti_td">
	     <label class="wdti_label">Alignment: <span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip" title="You can specify the alignment of the Follow Button using this  parameter. "> [?]</span></label>
	   </td>
	   <td>					
	     <input type="radio" class="wdti_checkbox" value="left" name="align" id="align_1" <?php if($row->align=="left") echo 'checked="checked"';?> />
		 <label class="wdti_label_rad wdti_label_check" for="align_1">Left</label>
	     <input type="radio" class="wdti_checkbox" value="right" name="align" id="align_2"<?php if($row->align=="right") echo 'checked="checked"';?>/>
	     <label class="wdti_label_rad wdti_label_check" for="align_2">Right</label>
	   </td>
     </tr>
     <tr class="wdti_tr" id="show_screen_name">
	   <td class="wdti_td">
			<label class="wdti_label">Show Screen Name:<span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip" title="The user's screen name shows up by default, but you can opt not to show the screen name in the button."> [?]</span></label>
	   </td>
	   <td>
		 <input type="radio" class="wdti_checkbox" value="true" id="show_screen_name_1" name="show_screen_name" <?php if($row->show_screen_name=="true") echo 'checked="checked"';?> />
		 <label class="wdti_label_rad wdti_label_check" for="show_screen_name_1">Yes</label>
         <input type="radio" class="wdti_checkbox" value="false" id="show_screen_name_2" name="show_screen_name" <?php if($row->show_screen_name=="false") echo 'checked="checked"';?>/>
		 <label class="wdti_label_rad wdti_label_check" for="show_screen_name_2">No</label>
	   </td>
	 </tr>	 
     <tr class="wdti_tr" id="count_tr">
       <td class="wdti_td">
         <label class="wdti_label" for="reg_type">Count Box Position:<span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip" title="You can choose to display or hide the count box, or place it above or next to the Tweet Button.When using large button vertical position is disabled."> [?]</span></label>
       </td>
       <td>
	     <label class="wdti_label_for_select">
	      <select name="count_mode" class="wdti_select" id="reg_type">
		   <option value="vertical"   <?php if($row->count_mode=="vertical") echo 'selected="selected"';?>>Vertical</option>
           <option value="horizontal" <?php if($row->count_mode=="horizontal") echo 'selected="selected"';?>>Horizontal</option>
           <option value="none"       <?php if($row->count_mode=="none") echo 'selected="selected"';?>>None</option>			
		  </select>
		 </label>
       </td>
     </tr>
     <tr class="wdti_tr" id="dnt">
       <td class="wdti_td">
	     <label class="wdti_label">Opt Out: <span style="cursor:pointer;color:#3B5998" class="wdti_tooltip" title="Twitter buttons on your site can help us tailor content and suggestions for Twitter users. If you want to opt-out of this feature, set the optional Opt Out parameter to be yes.">[?]</span></label></td>
       <td>
         <input type="radio" class="wdti_checkbox"  id="dnt0" name="dnt" <?php echo (($row->dnt=="true") ? 'checked="checked"' : ''); ?> value="true" >
         <label class="wdti_label_rad wdti_label_check" for="dnt0">Yes</label>
         <input type="radio" class="wdti_checkbox"  id="dnt1" name="dnt" <?php echo (($row->dnt=="false") ? 'checked="checked"' : ''); ?> value="false" >
         <label class="wdti_label_rad wdti_label_check" for="dnt1">No</label>
       </td>
     </tr>	 
	 <tr class="wdti_tr" id="lang_type">
       <td class="wdti_td">
	     <label class="wdti_label">Language Preference:</label></td>
       <td>
         <input type="radio" class="wdti_checkbox"  id="lang_type0" name="lang_type" title="Plugin language" <?php echo (($row->lang_type=="normal") ? 'checked="checked"' : ''); ?> onchange="spider_twitt_change_lang('normal')"  value="normal" >
         <label class="wdti_label_rad wdti_label_check" for="lang_type0">Custom</label>
         <input type="radio" class="wdti_checkbox"  id="lang_type1" name="lang_type" title="Adjusts to the language of the website" <?php echo (($row->lang_type=="auto") ? 'checked="checked"' : ''); ?>  onchange="spider_twitt_change_lang('auto')"  value="auto" >
         <label class="wdti_label_rad wdti_label_check" for="lang_type1">Current</label>
       </td>
     </tr>	 
	 <tr class="wdti_tr" id="lang">
	   <td class="wdti_td">
		 <label class="wdti_label" for="lang">Language:</label>
	   </td>
	   <td>
	     <label class="wdti_label_for_select">
		  <select name="lang" class="wdti_select">
		   <option value="ar_AR" <?php if($row->lang=="ar_AR") echo 'selected="selected"';?> >Arabic</option>
		   <option value="eu_ES" <?php if($row->lang=="eu_ES") echo 'selected="selected"';?> >Basque</option>
		   <option value="cs_CZ" <?php if($row->lang=="cs_CZ") echo 'selected="selected"';?> >Czech</option>
		   <option value="ca_CA" <?php if($row->lang=="ca_CA") echo 'selected="selected"';?> >Catalan</option>
		   <option value="da_DK" <?php if($row->lang=="da_DK") echo 'selected="selected"';?> >Danish</option>
		   <option value="nl_NL" <?php if($row->lang=="nl_NL") echo 'selected="selected"';?> >Dutch</option>
		   <option value="nl_BE" <?php if($row->lang=="nl_BE") echo 'selected="selected"';?> >Dutch (Belgie)</option>
		   <option value="en_GB" <?php if($row->lang=="en_GB") echo 'selected="selected"';?> >English (UK)</option>
		   <option value="en_US" <?php if($row->lang=="en_US") echo 'selected="selected"';?> >English </option>
		   <option value="fo_FO" <?php if($row->lang=="fo_FO") echo 'selected="selected"';?> >Faroese</option>
		   <option value="tl_PH" <?php if($row->lang=="tl_PH") echo 'selected="selected"';?> >Filipino</option>
		   <option value="fi_FI" <?php if($row->lang=="fi_FI") echo 'selected="selected"';?> >Finnish</option>
		   <option value="fr_FR" <?php if($row->lang=="fr_FR") echo 'selected="selected"';?> >French (France)</option>
		   <option value="gl_ES" <?php if($row->lang=="gl_ES") echo 'selected="selected"';?> >Galician</option>
		   <option value="de_DE" <?php if($row->lang=="de_DE") echo 'selected="selected"';?> >German</option>
		   <option value="el_GR" <?php if($row->lang=="el_GR") echo 'selected="selected"';?> >Greek</option>
		   <option value="he_IL" <?php if($row->lang=="he_IL") echo 'selected="selected"';?> >Hebrew</option>
		   <option value="hi_IN" <?php if($row->lang=="hi_IN") echo 'selected="selected"';?> >Hindi</option>
		   <option value="hu_HU" <?php if($row->lang=="hu_HU") echo 'selected="selected"';?> >Hungarian</option>
		   <option value="id_ID" <?php if($row->lang=="id_ID") echo 'selected="selected"';?> >Indonesian</option>
		   <option value="it_IT" <?php if($row->lang=="it_IT") echo 'selected="selected"';?> >Italian</option>
		   <option value="ja_JP" <?php if($row->lang=="ja_JP") echo 'selected="selected"';?> >Japanese</option>
		   <option value="ko_KR" <?php if($row->lang=="ko_KR") echo 'selected="selected"';?> >Korean</option>
		   <option value="xx-lc" <?php if($row->lang=="xx-lc") echo 'selected="selected"';?> >Lolcat</option>
		   <option value="ms_MY" <?php if($row->lang=="ms_MY") echo 'selected="selected"';?> >Malay</option>
		   <option value="nn_NO" <?php if($row->lang=="nn_NO") echo 'selected="selected"';?> >Norwegian (nynorsk) </option>
		   <option value="pl_PL" <?php if($row->lang=="pl_PL") echo 'selected="selected"';?> >Polish</option>
		   <option value="pt_PT" <?php if($row->lang=="pt_PT") echo 'selected="selected"';?> >Portuguese (Portugal)</option>
		   <option value="ro_RO" <?php if($row->lang=="ro_RO") echo 'selected="selected"';?> >Romanian</option>
		   <option value="ru_RU" <?php if($row->lang=="ru_RU") echo 'selected="selected"';?> >Russian</option>
		   <option value="zh_CN" <?php if($row->lang=="zh_CN") echo 'selected="selected"';?> >Simplified Chinese (China) </option>
		   <option value="es_ES" <?php if($row->lang=="es_ES") echo 'selected="selected"';?> >Spanish (Spain)</option>
		   <option value="sv_SE" <?php if($row->lang=="sv_SE") echo 'selected="selected"';?> >Swedish</option>
		   <option value="th_TH" <?php if($row->lang=="th_TH") echo 'selected="selected"';?> >Thai</option>
		   <option value="zh_TW" <?php if($row->lang=="zh_TW") echo 'selected="selected"';?> >Traditional Chinese (Taiwan) </option>
		   <option value="tr_TR" <?php if($row->lang=="tr_TR") echo 'selected="selected"';?> >Turkish</option>
		   <option value="uk_UA" <?php if($row->lang=="uk_UA") echo 'selected="selected"';?> >Ukrainian</option>
		   <option value="ur_UR" <?php if($row->lang=="ur_UR") echo 'selected="selected"';?> >Urdu</option>				
          </select>
         </label>		  
	   </td>
	 </tr>
	 <tr class="wdti_tr" id="all_posts">
       <td class="wdti_td">
	     <label class="wdti_label" for="all_posts_input">All Posts:<span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip"  title="Adds the plugin to all the posts in the website"> [?]</span></label></td>
       <td>
         <input type="checkbox" class="wdti_checkbox" id="all_posts_input" name="all_posts" <?php echo (($row->posts=='all_posts') ? 'checked="checked"' : ''); ?> onclick="spider_twitt_all_posts(this.checked)" value="all_posts" >
         <label class="wdti_label_rad wdti_label_check" for="all_posts_input"></label>
	   </td>
     </tr>	 
	 <tr class="wdti_tr" id="add_post">
	   <td class="wdti_td">
	     <label class="wdti_label" for="content-add_media">Posts: </label></td>
	   <td>			
		 <a href="<?php echo add_query_arg(array('action' => 'addPostsPages', 'width' => '700', 'height' => '550', 'function_kind' => 'display_posts_for_twitt', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" class="button-primary thickbox thickbox-preview" id="content-add_media" title="Choose the items to which you want to add the plugin" onclick="return false;" style="margin-bottom:5px;">
			Add Posts
		 </a>
	   </td>
	 </tr>
	 <tr class="wdti_tr" id="posts_div">
	   <td class="wdti_td">
       </td>
	   <td>
        <div class="posts_or_pages_div" id="div_for_posts">
          <?php
            $posts_id_string = '';
            if ($row->posts != 'all_posts' && $row->posts!="") {
			   $posts_array=explode(',',$row->posts);
               foreach($posts_array as $post) { if($post=="")break;
               ?>
                <div class="post_or_page_div" id="post_<?php echo $post; ?>">
                 <span class="post_or_page_name"><?php echo $this->model->get_post_name($post); ?></span>
                 <span style="float:right;" class="spider_delete_img_small" onclick="twitt_remove_post('<?php echo $post; ?>')" />
                </div>
          <?php
            $posts_id_string .= $post . ',';
            }
            }
          ?>
        </div>
        <input type="hidden" value="<?php echo $posts_id_string; ?>" id="posts_ids" name="posts_ids"/>
       </td>
	   </td>
	 </tr>
	 <tr class="wdti_tr" id="place">
	   <td class="wdti_td">
		 <label class="wdti_label" for="place">Vertical position:<span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip"  title="Choose whether to place the plugin at the top or at the bottom of the article"> [?]</span></label>
	   </td>
	   <td>
	     <label class="wdti_label_for_select">
		  <select name="place" class="wdti_select">
		   <option value="top"     <?php if($row->place=="top") echo 'selected="selected"';?>>Top</option>
		   <option value="bottom" id="comment_pos_art" <?php if($row->place=="bottom") echo 'selected="selected"';?>>Bottom</option>
		   <option value="both"  <?php if($row->place=="both") echo 'selected="selected"';?>>Both</option>
		  </select>
		 </label> 
	   </td>
	 </tr>
	 <tr class="wdti_tr" id="all_pages">
       <td class="wdti_td">
	     <label class="wdti_label" for="all_pages_input">All pages:<span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip"  title="Adds the plugin to all the pages in the website"> [?]</span></label></td>
       <td>
         <input type="checkbox" class="wdti_checkbox" id="all_pages_input" name="all_pages" <?php echo (($row->pages=='all_pages') ? 'checked="checked"' : ''); ?> onclick="spider_twitt_all_pages(this.checked)" value="all_pages" >
         <label class="wdti_label_rad wdti_label_check" for="all_pages_input"></label>
	   </td>
     </tr>
	 <tr class="wdti_tr" id="add_pages">
	   <td class="wdti_td">
	     <label class="wdti_label" for="content-add_media">Pages: </label></td>
	   <td>			
		 <a href="<?php echo add_query_arg(array('action' => 'addPostsPages', 'width' => '700', 'height' => '550', 'function_kind' => 'display_pages_for_twitt', 'TB_iframe' => '1'), admin_url('admin-ajax.php')); ?>" class="button-primary thickbox thickbox-preview" id="content-add_media" title="Choose the items to which you want to add the plugin" onclick="return false;" style="margin-bottom:5px;">
			Add Pages
		 </a>
	   </td>
	 </tr>
	 <tr class="wdti_tr" id="pages_div">
	   <td class="wdti_td">
       </td>
	   <td>
        <div class="posts_or_pages_div" id="div_for_pages">
          <?php
            $pages_id_string = '';
            if ($row->pages != 'all_pages' && $row->pages!="") {
			   $pages_array=explode(',',$row->pages);
               foreach($pages_array as $page) { if($page=="")break;
               ?>
                <div class="post_or_page_div" id="page_<?php echo $page; ?>">
                 <span class="post_or_page_name"><?php echo $this->model->get_pages_name($page); ?></span>
                 <span style="float:right;" class="spider_delete_img_small" onclick="twitt_remove_page('<?php echo $page; ?>')" />
                </div>
          <?php
            $pages_id_string .= $page . ',';
            }
            }
          ?>
        </div>
        <input type="hidden" value="<?php echo $pages_id_string; ?>" id="pages_ids" name="pages_ids"/>
       </td>
	   </td>
	 </tr>	 
	 <tr class="wdti_tr" id="con_place">
	   <td class="wdti_td">
	     <label class="wdti_label" for="item_place">Vertical position: <span style="cursor:pointer;color:#3B5998" class="wdti_tooltip"  title="Choose whether to place the plugin at the top or at the bottom of the pages"> [?]</span></label>
	   </td>
	   <td >
	     <label class="wdti_label_for_select">
	      <select name="item_place" class="wdti_select">
	       <option value="top"     <?php if($row->item_place=="top")    echo 'selected="selected"';?>>Top</option>
	       <option value="bottom" id="comment_pos_item"  <?php if($row->item_place=="bottom") echo 'selected="selected"';?>>Bottom</option>
	       <option value="both"  <?php if($row->item_place=="both") echo 'selected="selected"';?>>Both</option>
	      </select>
         </label>		  
	   </td>
	 </tr>
     <tr class="wdti_tr" id="css">
	   <td class="wdti_td">
	     <label class="wdti_label" for="style">STYLE:<span style="cursor:pointer;color:#3B5998"  class="wdti_tooltip"  title="You can provide a custom Style for the plugin container"> [?]</span></label>
	   </td>					           
	   <td >                                 
		 <textarea name="css"   style="font-size:13px; color:#787878; font-family:Segoe UI;width:200px;height:200px"><?php echo $row->css ?></textarea>   				
	   </td>
	 </tr> 	 
   </table>
   	   <div id="wdti_preview" class="wdti_preview">
         <div align="center" id="tweetbutton_prev"  >
            <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/tweet_buttons01preview.png" ?>" />
            <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/tweet_buttons2.png" ?>" />
            <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/tweet_buttons03preview.png" ?>" />
         </div>
         <div align="center" id="followbutton_prev" >
            <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/follow_buttons05.png" ?>" />
	        <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/follow_buttons1.png" ?>" />
            <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/follow_buttons.png" ?>" />	
         </div>
         <div align="center" id="timeline_prev" >
           <div id="user_prev">	
            <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/user_prev.png" ?>"  />
		    <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/user_prev_2.png" ?>"  />
           </div>
           <div id="list_prev">	
		    <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/list_prev.png" ?>" /> 
		    <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/list_prev_2.png" ?>" /> 
           </div>
           <div id="fav_prev">		
		    <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/fav_prev.png" ?>" />
		    <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/fav_prev_2.png" ?>" />
           </div>
		   <div id="search_prev">  
			<img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/search_prev.png" ?>" />
			<img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/search_prev_2.png" ?>" />
		   </div>
         </div>
         <div align="center" id="mention_prev" >
			<img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/mentionpreview.png" ?>" />
			<img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/mention-boxpreview.png" ?>" />
			<img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/mention-box-recommendspreview.png" ?>" />
		 </div>
         <div align="center" id="hashtag_prev" >
            <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/hashtagpreview.png" ?>" />
            <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/hashtag-boxpreview.png" ?>" />
	        <img style="padding-top:50px;max-width:95%" src="<?php echo WD_WDTI_URL."/images/hashtag-box-recommendspreview.png" ?>" />
         </div>
       </div>
	   <?php wp_nonce_field('nonce_wg_twitt', 'nonce_wg_twitt'); ?>
	   <input type="hidden" id="current_id" name="current_id" value="<?php echo $row->id; ?>" />
	   <input type="hidden" id="task"       name="task"       value="" />
	   <input type="hidden" id="type"       name="type"       value="<?php echo $row->type?>"/>
 </form>
 <script>
 spider_twitt_change_type ('<?php echo $row->type?>');
 jQuery(document).ready(function(){
  simple_tooltip("wdti_tooltip","tooltip");
 });
 </script>
  <?php
  }
 }
?>