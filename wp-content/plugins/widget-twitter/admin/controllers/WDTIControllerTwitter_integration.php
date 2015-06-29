<?php

class WDTIControllerTwitter_integration {
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
  ////////////////////////////////////////////////////////////////////////////////////////
  // Getters & Setters                                                                  //
  ////////////////////////////////////////////////////////////////////////////////////////
  
  public function execute() {
    $task = ((isset($_POST['task'])) ? esc_html(stripslashes($_POST['task'])) : '');
    $id = ((isset($_POST['current_id'])) ? esc_html(stripslashes($_POST['current_id'])) : 0);
    if (method_exists($this, $task)) {
      check_admin_referer('nonce_wg_twitt', 'nonce_wg_twitt');
	  $this->$task($id);
    }
    else {
      $this->display();
    }
  }
  public function display() {
    require_once WD_WDTI_DIR . "/admin/models/WDTIModelTwitter_integration.php";
    $model = new WDTIModelTwitter_integration();

    require_once WD_WDTI_DIR . "/admin/views/WDTIViewTwitter_integration.php";
    $view = new WDTIViewTwitter_integration($model);
    $view->display();
  }
  public function add() {
    require_once WD_WDTI_DIR . "/admin/models/WDTIModelTwitter_integration.php";
    $model = new WDTIModelTwitter_integration();

    require_once WD_WDTI_DIR . "/admin/views/WDTIViewTwitter_integration.php";
    $view = new WDTIViewTwitter_integration($model);
    $view->edit(0);
  }
  public function edit($id) {
    require_once WD_WDTI_DIR . "/admin/models/WDTIModelTwitter_integration.php";
    $model = new WDTIModelTwitter_integration();
    
    require_once WD_WDTI_DIR . "/admin/views/WDTIViewTwitter_integration.php";
    $view = new WDTIViewTwitter_integration($model);
    $view->edit($id);
  }
  public function save($id) {
    global $wpdb;
    $this->save_twitt_db($id);
    $this->display();
  }
  public function apply($id) {
    global $wpdb;
    $this->save_twitt_db($id);
	if($id==0) $id = (int) $wpdb->get_var('SELECT MAX(`id`) FROM ' . $wpdb->prefix . 'twitter_integration');
    $this->edit($id);
  }
  public function save_twitt_db($id) {
    global $wpdb; 
	$twitt_id = (($id) ? (int) $id : 0);
    $title = ((isset($_POST['title'])) ? esc_html(stripslashes($_POST['title'])) : '');
    $type = (isset($_POST['type']) ? stripslashes($_POST['type']) : '');
    $order = ((isset($_POST['order']) && esc_html(stripslashes($_POST['order'])) != '') ? esc_html(stripslashes($_POST['order'])) : '');
    $published = ((isset($_POST['published']) && esc_html(stripslashes($_POST['published'])) != '') ? (int)  esc_html(stripslashes($_POST['published'])) : 1);
    $width = ((isset($_POST['width']) && esc_html(stripslashes($_POST['width'])) != '') ? esc_html(stripslashes($_POST['width'])) : 150); 
	$dnt = ((isset($_POST['dnt']) && esc_html(stripslashes($_POST['dnt'])) != '') ? esc_html(stripslashes($_POST['dnt'])) : 'false');
	$count_mode = ((isset($_POST['count_mode']) && esc_html(stripslashes($_POST['count_mode'])) != '') ? esc_html(stripslashes($_POST['count_mode'])) : '');
	$url_type = ((isset($_POST['url_type']) && esc_html(stripslashes($_POST['url_type'])) != '') ? esc_html(stripslashes($_POST['url_type'])) : 'auto');
	$url = ((isset($_POST['url']) && esc_html(stripslashes($_POST['url'])) != '') ? esc_html(stripslashes($_POST['url'])) : '');
	if($url_type=='normal') {
    $url_not_null =	$url; } else {
	$url_not_null =	'autoSITEURLauto';}
	$via = ((isset($_POST['via']) && esc_html(stripslashes($_POST['via'])) != '') ? esc_html(stripslashes($_POST['via'])) : '');
	$tw_text = ((isset($_POST['tw_text']) && esc_html(stripslashes($_POST['tw_text'])) != '') ? esc_html(stripslashes($_POST['tw_text'])) : '');
	$lang_type = ((isset($_POST['lang_type']) && esc_html(stripslashes($_POST['lang_type'])) != '') ? esc_html(stripslashes($_POST['lang_type'])) : 'auto');
	if($lang_type=='normal') {
	$lang = ((isset($_POST['lang']) && esc_html(stripslashes($_POST['lang'])) != '') ? esc_html(stripslashes($_POST['lang'])) : ''); } else {
	$lang='autoLANGauto'; }
	$counturl = ((isset($_POST['counturl']) && esc_html(stripslashes($_POST['counturl'])) != '') ? esc_html(stripslashes($_POST['counturl'])) : '');
	$but_size = ((isset($_POST['but_size']) && esc_html(stripslashes($_POST['but_size'])) != '') ? esc_html(stripslashes($_POST['but_size'])) : 'medium');
	$align = ((isset($_POST['align']) && esc_html(stripslashes($_POST['align'])) != '') ? esc_html(stripslashes($_POST['align'])) : 'left');
	$show_screen_name = ((isset($_POST['show_screen_name']) && esc_html(stripslashes($_POST['show_screen_name'])) != '') ? esc_html(stripslashes($_POST['show_screen_name'])) : 'false');    
	$place = ((isset($_POST['place']) && esc_html(stripslashes($_POST['place'])) != '') ? esc_html(stripslashes($_POST['place'])) : '');
	$item_place = ((isset($_POST['item_place']) && esc_html(stripslashes($_POST['item_place'])) != '') ? esc_html(stripslashes($_POST['item_place'])) : '');
	$css = ((isset($_POST['css']) && esc_html(stripslashes($_POST['css'])) != '') ? esc_html(stripslashes($_POST['css'])) : '');
	$height = ((isset($_POST['height']) && esc_html(stripslashes($_POST['height'])) != '') ? esc_html(stripslashes($_POST['height'])) : '');
	$login_text = ((isset($_POST['login_text']) && esc_html(stripslashes($_POST['login_text'])) != '') ? esc_html(stripslashes($_POST['login_text'])) : '');
	$posts = ((isset($_POST['all_posts']) && esc_html(stripslashes($_POST['all_posts'])) != '') ? esc_html(stripslashes($_POST['all_posts'])) : '');
	if($posts=='') {
	$posts = ((isset($_POST['posts_ids']) && esc_html(stripslashes($_POST['posts_ids'])) !='' ) ? esc_html(stripslashes($_POST['posts_ids'])) : '');} 
    $pages = ((isset($_POST['all_pages']) && esc_html(stripslashes($_POST['all_pages'])) != '') ? esc_html(stripslashes($_POST['all_pages'])) : '');
	if($pages=='') {
	$pages = ((isset($_POST['pages_ids']) && esc_html(stripslashes($_POST['pages_ids'])) != '') ? esc_html(stripslashes($_POST['pages_ids'])) : '');}
	$code = ((isset($_POST['code']) && esc_html(stripslashes($_POST['code'])) != '') ? esc_html(stripslashes($_POST['code'])) : '');
	$show_count = ((isset($_POST['show_count']) && esc_html(stripslashes($_POST['show_count'])) != '') ? esc_html(stripslashes($_POST['show_count'])) : 'false');
	$theme = ((isset($_POST['theme']) && esc_html(stripslashes($_POST['theme'])) != '') ? esc_html(stripslashes($_POST['theme'])) : 'light');
	$link_color = ((isset($_POST['link_color']) && esc_html(stripslashes($_POST['link_color'])) != '') ? esc_html(stripslashes($_POST['link_color'])) : '');	
	$border = ((isset($_POST['border']) && esc_html(stripslashes($_POST['border'])) != '') ? esc_html(stripslashes($_POST['border'])) : '');
    $tweet_limit = ((isset($_POST['tweet_limit']) && esc_html(stripslashes($_POST['tweet_limit'])) != '') ? esc_html(stripslashes($_POST['tweet_limit'])) : '');
	$aria_polite = ((isset($_POST['aria_polite']) && esc_html(stripslashes($_POST['aria_polite'])) != '') ? esc_html(stripslashes($_POST['aria_polite'])) : '');
	$show_replies = ((isset($_POST['show_replies']) && esc_html(stripslashes($_POST['show_replies'])) != '') ? esc_html(stripslashes($_POST['show_replies'])) : 'false');
	$screen_name = ((isset($_POST['screen_name']) && esc_html(stripslashes($_POST['screen_name'])) != '') ? esc_html(stripslashes($_POST['screen_name'])) : '');
	$widget_id = ((isset($_POST['widget_id']) && esc_html(stripslashes($_POST['widget_id'])) != '') ? esc_html(stripslashes($_POST['widget_id'])) : '');	
	$timeline_type = ((isset($_POST['timeline_type']) && esc_html(stripslashes($_POST['timeline_type'])) != '') ? esc_html(stripslashes($_POST['timeline_type'])) : 'user');
    $tweet_to = ((isset($_POST['tweet_to']) && esc_html(stripslashes($_POST['tweet_to'])) != '') ? esc_html(stripslashes($_POST['tweet_to'])) : '');
	$username_to_1 = ((isset($_POST['username_to_1']) && esc_html(stripslashes($_POST['username_to_1'])) != '') ? esc_html(stripslashes($_POST['username_to_1'])) : '');
	$username_to_2 = ((isset($_POST['username_to_2']) && esc_html(stripslashes($_POST['username_to_2'])) != '') ? esc_html(stripslashes($_POST['username_to_2'])) : '');
	$tw_stories = ((isset($_POST['tw_stories']) && esc_html(stripslashes($_POST['tw_stories'])) != '') ? esc_html(stripslashes($_POST['tw_stories'])) : '');
    $tw_hashtag = ((isset($_POST['tw_hashtag']) && esc_html(stripslashes($_POST['tw_hashtag'])) != '') ? esc_html(stripslashes($_POST['tw_hashtag'])) : '');
	$noheader = ((isset($_POST['noheader']) && esc_html(stripslashes($_POST['noheader'])) != '') ? esc_html(stripslashes($_POST['noheader'])) : '');
	$nofooter = ((isset($_POST['nofooter']) && esc_html(stripslashes($_POST['nofooter'])) != '') ? esc_html(stripslashes($_POST['nofooter'])) : '');
	$noborders = ((isset($_POST['noborders']) && esc_html(stripslashes($_POST['noborders'])) != '') ? esc_html(stripslashes($_POST['noborders'])) : '');
	$noscrollbar = ((isset($_POST['noscrollbar']) && esc_html(stripslashes($_POST['noscrollbar'])) != '') ? esc_html(stripslashes($_POST['noscrollbar'])) : '');
	$chrome = $noheader . ' ' . $nofooter . ' ' . $noborders . ' ' . $noscrollbar;
	$transparent = ((isset($_POST['transparent']) && esc_html(stripslashes($_POST['transparent'])) != '') ? esc_html(stripslashes($_POST['transparent'])) : '');
	$href_fav = 'https://twitter.com/'.$screen_name.'/favorites';
	if($username_to_1 !='' && $username_to_2 !='')
	$recom_user = $username_to_1.','.$username_to_2;
	else if($username_to_1 =='' && $username_to_2 !='')
	$recom_user = $username_to_2;
	else if($username_to_1 !='' && $username_to_2 =='')
	$recom_user = $username_to_1;
	else if($username_to_1 =='' && $username_to_2 =='')
	$recom_user = '';
	switch ( $type ) {	
		case 'tweetbutton':
				$code='<div style="'.$css.'"><a href="https://twitter.com/share" class="twitter-share-button" 
				        data-url="'.$url_not_null.'" 
				        data-via="'.$via.'"
					    data-text="'.$tw_text.'"
					    data-related=""
					    data-count="'.$count_mode.'"
					    data-hashtags="'.$tw_hashtag.'"
					    data-lang="'.$lang.'"
					    data-counturl="'.$counturl.'"
					    data-size="'.$but_size.'"
					    data-dnt="'.$dnt.'"	> Tweet </a> </div>
		                <script>
					    !function(d,s,id) {
					      var js,fjs=d.getElementsByTagName(s)[0];
					      if(!d.getElementById(id)) {
					       js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);
					      }
					    }
					   (document,"script","twitter-wjs");
					    </script>';
		break;
		
		case 'followbutton':
				$code='<div style="'.$css.'"><a href="https://twitter.com/'.$screen_name.'" class="twitter-follow-button" 
						data-show-count="'.$show_count.'"
						data-lang="'.$lang.'"
						data-width="'.$width.'px"
						data-align="'.$align.'"
						data-show-screen-name="'.$show_screen_name.'"
						data-size="'.$but_size.'"
						data-dnt="'.$dnt.'">
						Follow @'.$screen_name.' </a> </div>
						<script>
						!function(d,s,id) {
						  var js,fjs=d.getElementsByTagName(s)[0];
						  if(!d.getElementById(id)) {
						   js=d.createElement(s);
						   js.id=id;js.src="//platform.twitter.com/widgets.js";
						   fjs.parentNode.insertBefore(js,fjs);
						  }
						}
						(document,"script","twitter-wjs");
						</script>';
		break;
		
		case 'timeline':
	            switch ( $timeline_type ) {		
	                case 'user':
							$code='<div style="'.$css.'"><a class="twitter-timeline" 
									data-widget-id="'.$widget_id.'"
									height ="'.$height.'"
									data-theme="'.$theme.'"
									data-link-color="#'.$link_color.'"
									data-dnt="'.$dnt.'"
									width ="'.$width.'"
									data-chrome="'.$chrome.'"
									data-border-color="#'.$border.'"
									data-lang="'.$lang.'"
									data-tweet-limit="'.$tweet_limit.'"
									data-aria-polite="'.$aria_polite.'"
									data-show-replies="'.$show_replies.'">
									Tweets by @'.$screen_name.'</a></div>
									<script>
									!function(d,s,id) { 
									  var js,fjs=d.getElementsByTagName(s)[0];
									  if(!d.getElementById(id)) {
									   js=d.createElement(s);
									   js.id=id;js.src="//platform.twitter.com/widgets.js";
									   fjs.parentNode.insertBefore(js,fjs);
									  }
									}
									(document,"script","twitter-wjs");
									</script>' ;
					break;
					
					case 'fav':
							$code='<div style="'.$css.'"><a class="twitter-timeline" 
									href="'.$href_fav.'"
									data-widget-id="'.$widget_id.'"									
									height ="'.$height.'"
									data-theme="'.$theme.'"
									data-link-color="#'.$link_color.'"
									data-dnt="'.$dnt.'"
									width ="'.$width.'"
									data-chrome="'.$chrome.'"
									data-border-color="#'.$border.'"
									data-lang="'.$lang.'"
									data-tweet-limit="'.$tweet_limit.'"
									data-aria-polite="'.$aria_polite.'">Tweets by @'.$screen_name.'</a></div>
									<script>
									!function(d,s,id) {
									  var js,fjs=d.getElementsByTagName(s)[0];
									  if(!d.getElementById(id)) {
									   js=d.createElement(s);
									   js.id=id;js.src="//platform.twitter.com/widgets.js";
									   fjs.parentNode.insertBefore(js,fjs);
									  }
									}
									(document,"script","twitter-wjs");
									</script>' ;
					break;
					
					case 'list': 
							$code='<div style="'.$css.'"><a class="twitter-timeline" href="'.$href_list.'"
									data-widget-id="'.$widget_id.'"
									data-theme="'.$theme.'"
									data-link-color="#'.$link_color.'"
									data-dnt="'.$dnt.'"
									width="'.$width.'"
									height="'.$height.'"
									data-chrome="'.$chrome.'"
									data-border-color="#'.$border.'"
									data-lang="'.$lang.'"
									data-tweet-limit="'.$tweet_limit.'"
									data-aria-polite="'.$aria_polite.'">Tweets by @'.$screen_name.'</a></div>
									<script>
									!function(d,s,id) {
									  var js,fjs=d.getElementsByTagName(s)[0];
									  if(!d.getElementById(id)) {
									   js=d.createElement(s);
									   js.id=id;js.src="//platform.twitter.com/widgets.js";
									   fjs.parentNode.insertBefore(js,fjs);
									  }
									}
									(document,"script","twitter-wjs");
									</script>' ;
					break;
					
					case 'search':
							$code='<div style="'.$css.'"><a class="twitter-timeline" 
									data-widget-id="'.$widget_id.'" 
									data-theme="'.$theme.'"
									width ="'.$width.'"
									height ="'.$height.'"
									data-dnt="'.$dnt.'"
									data-chrome="'.$chrome.'"
									data-link-color="#'.$link_color.'"
									data-border-color="#'.$border.'"
									data-lang="'.$lang.'"
									data-tweet-limit="'.$tweet_limit.'"
									data-aria-polite="'.$aria_polite.'" style="'.$css.'" >Tweets by @'.$screen_name.'</a></div>
									<script>
									!function(d,s,id) {
									  var js,fjs=d.getElementsByTagName(s)[0];
									  if(!d.getElementById(id)) {
									   js=d.createElement(s);
									   js.id=id;js.src="//platform.twitter.com/widgets.js";
									   fjs.parentNode.insertBefore(js,fjs);
									  }
									}
									(document,"script","twitter-wjs");
									</script>' ;
					break;	
	            }
		break;
		
		case 'mention':
				$code='<div style="'.$css.'"><a href="https://twitter.com/intent/tweet?screen_name='.$tweet_to.'&text='.$tw_text.'" 
						class="twitter-mention-button"
						data-lang="'.$lang.'"
						data-related="'.$recom_user.'" >
						Tweet to '.$tweet_to.'</a></div>
						<script>
						!function(d,s,id) {
						  var js,fjs=d.getElementsByTagName(s)[0];
						  if(!d.getElementById(id)) {
						   js=d.createElement(s);js.id=id;
						   js.src="https://platform.twitter.com/widgets.js";
						   fjs.parentNode.insertBefore(js,fjs);
						  }
						}
						(document,"script","twitter-wjs");
						</script>';
	    break;
		
		case 'hashtag':		
				$code='<div style="'.$css.'"><a href="https://twitter.com/intent/tweet?button_hashtag='.$tw_stories.'&text='.$tw_text.'" 
						class="twitter-hashtag-button" 
						data-lang="'.$lang.'"
						data-related="'.$recom_user.'"
						data-url="'.$url_not_null.'"  >
						Tweet #'.$tw_stories.'</a></div>
						<script>
						!function(d,s,id) {
						  var js,fjs=d.getElementsByTagName(s)[0];
						  if(!d.getElementById(id)) {
						   js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";
						   fjs.parentNode.insertBefore(js,fjs);
						  }
						}
						(document,"script","twitter-wjs");
						</script>';
		break;
			
	}
    if ($twitt_id != 0) {
      $save = $wpdb->update($wpdb->prefix . 'twitter_integration', array(
	  
		  'title' => $title,
		  'type' => $type,
		  'order' => $order,
		  'published' => $published,
		  'url' => $url,
		  'lang' => $lang,
		  'width' => $width,   
		  'dnt' => $dnt,
		  'count_mode' => $count_mode,
		  'url_type' => $url_type,
		  'via' => $via,
		  'tw_text' => $tw_text,
		  'lang_type' => $lang_type,
		  'counturl' => $counturl,
		  'but_size' => $but_size,
		  'align' => $align,
		  'show_screen_name' => $show_screen_name,
		  'place' => $place,
		  'item_place' => $item_place,
		  'css' => $css,
		  'height' => $height,
		  'login_text' => $login_text,
		  'posts' => $posts,
		  'pages' => $pages,
		  'code' => $code,
		  'show_count' => $show_count,
		  'theme' => $theme,
		  'link_color' => $link_color,
		  'chrome' => $chrome,
		  'border' => $border,
		  'tweet_limit' => $tweet_limit,
		  'aria_polite' => $aria_polite,
		  'show_replies' => $show_replies,
		  'screen_name' => $screen_name,
		  'widget_id' => $widget_id,
		  'timeline_type' => $timeline_type,
		  'tweet_to' => $tweet_to,
		  'username_to_1' => $username_to_1,
		  'username_to_2' => $username_to_2,
		  'tw_stories' => $tw_stories,
		  'tw_hashtag' => $tw_hashtag,
		  'noheader' => $noheader,
		  'nofooter' => $nofooter,
		  'noborders' => $noborders,
		  'noscrollbar' => $noscrollbar,
		  'transparent' => $transparent,
	 ),
    array('id' => $twitt_id));	
  }
  else {
      $save = $wpdb->insert($wpdb->prefix . 'twitter_integration', array(
	      
		  'title' => $title,
		  'type' => $type,
		  'order' => $order,
		  'published' => $published,
		  'url' => $url,
		  'lang' => $lang,
		  'width' => $width,   
		  'dnt' => $dnt,
		  'count_mode' => $count_mode,
		  'url_type' => $url_type,
		  'via' => $via,
		  'tw_text' => $tw_text,
		  'lang_type' => $lang_type,
		  'counturl' => $counturl,
		  'but_size' => $but_size,
		  'align' => $align,
		  'show_screen_name' => $show_screen_name,
		  'place' => $place,
		  'item_place' => $item_place,
		  'css' => $css,
		  'height' => $height,
		  'login_text' => $login_text,
		  'posts' => $posts,
		  'pages' => $pages,
		  'code' => $code,
		  'show_count' => $show_count,
		  'theme' => $theme,
		  'link_color' => $link_color,
		  'chrome' => $chrome,
		  'border' => $border,
		  'tweet_limit' => $tweet_limit,
		  'aria_polite' => $aria_polite,
		  'show_replies' => $show_replies,
		  'screen_name' => $screen_name,
		  'widget_id' => $widget_id,
		  'timeline_type' => $timeline_type,
		  'tweet_to' => $tweet_to,
		  'username_to_1' => $username_to_1,
		  'username_to_2' => $username_to_2,
		  'tw_stories' => $tw_stories,
		  'tw_hashtag' => $tw_hashtag,
		  'noheader' => $noheader,
		  'nofooter' => $nofooter,
		  'noborders' => $noborders,
		  'noscrollbar' => $noscrollbar,
		  'transparent' => $transparent,
		  ), array(
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
                '%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',	 
	           ));
    }
    if ($save !== FALSE) {
      echo WDWLibrary::message('Item Succesfully Saved.', 'updated');
    }
    else {
      echo WDWLibrary::message('Error. Please install plugin again.', 'error');
    }   
   }
  public function publish($id) {
    global $wpdb;
    $save = $wpdb->update($wpdb->prefix . 'twitter_integration', array('published' => 1), array('id' => $id));
    if ($save !== FALSE) {
      echo WDWLibrary::message('Item Succesfully Published.', 'updated');
    }
    else {
      echo WDWLibrary::message('Error. Please install plugin again.', 'error');
    }
    $this->display();
  }
  
  public function publish_all() {
    global $wpdb;
    $flag = FALSE;
    $twitt_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'twitter_integration');
    foreach ($twitt_ids_col as $twitt_id) {
      if (isset($_POST['check_' . $twitt_id])) {
        $flag = TRUE;
        $wpdb->update($wpdb->prefix . 'twitter_integration', array('published' => 1), array('id' => $twitt_id));
      }
    }
    if ($flag) {
      echo WDWLibrary::message('Items Succesfully Published.', 'updated');
    }
    else {
      echo WDWLibrary::message('You must select at least one item.', 'error');
    }
    $this->display();
  }

  public function unpublish($id) {
    global $wpdb;
    $save = $wpdb->update($wpdb->prefix . 'twitter_integration', array('published' => 0), array('id' => $id));
    if ($save !== FALSE) {
      echo WDWLibrary::message('Item Succesfully Unpublished.', 'updated');
    }
    else {
      echo WDWLibrary::message('Error. Please install plugin again.', 'error');
    }
    $this->display();
  }
  
  public function unpublish_all() {
    global $wpdb;
    $flag = FALSE;
    $twitt_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'twitter_integration');
    foreach ($twitt_ids_col as $twitt_id) {
      if (isset($_POST['check_' . $twitt_id])) {
        $flag = TRUE;
        $wpdb->update($wpdb->prefix . 'twitter_integration', array('published' => 0), array('id' => $twitt_id));
      }
    }
    if ($flag) {
      echo WDWLibrary::message('Items Succesfully Unpublished.', 'updated');
    }
    else {
      echo WDWLibrary::message('You must select at least one item.', 'error');
    }
    $this->display();
  }
  public function save_order() {
    global $wpdb;
    $twitt_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'twitter_integration');
    if ($twitt_ids_col) {
      foreach ($twitt_ids_col as $twitt_id) {
        if (isset($_POST['order_input_' . $twitt_id])) {
          $order_values[$twitt_id] = (int) $_POST['order_input_' . $twitt_id];
        }
        else {
          $order_values[$twitt_id] = (int) $wpdb->get_var($wpdb->prepare('SELECT `order` FROM ' . $wpdb->prefix . 'twitter_integration WHERE `id`="%d"', $twitt_id));
        }
      }
      $flag =asort($order_values);
      foreach ($order_values as $key => $order_value) {
        $wpdb->update($wpdb->prefix . 'twitter_integration', array('order' => $order_value), array('id' => $key));
      }
    }
	if ($flag !== FALSE) {
      echo WDWLibrary::message('Ordering Succesfully Saved.', 'updated');
    }
    else {
      echo WDWLibrary::message('Error. Please install plugin again.', 'error');
    }
    $this->display();
  }
  
  public function delete($id) {
    global $wpdb;
    $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'twitter_integration WHERE id="%d"', $id);
    if ($wpdb->query($query)) {
      echo WDWLibrary::message('Item Succesfully Deleted.', 'updated');
    }
    else {
      echo WDWLibrary::message('Error. Please install plugin again.', 'error');
    }
    $this->display();
  }
  
  public function delete_all() {
    global $wpdb;
    $flag = FALSE;
    $twitt_ids_col = $wpdb->get_col('SELECT id FROM ' . $wpdb->prefix . 'twitter_integration');
    foreach ($twitt_ids_col as $twitt) {
      if (isset($_POST['check_' . $twitt])) {
        $flag = TRUE;
        $query = $wpdb->prepare('DELETE FROM ' . $wpdb->prefix . 'twitter_integration WHERE id="%d"', $twitt);
        $wpdb->query($query);
      }
    }
    if ($flag) {
      echo WDWLibrary::message('Items Succesfully Deleted.', 'updated');
    }
    else {
      echo WDWLibrary::message('You must select at least one item.', 'error');
    }
    $this->display();
  }  
  }
?>