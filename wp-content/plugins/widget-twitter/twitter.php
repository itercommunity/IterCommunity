<?php
 /**
 * Plugin Name: Widget Twitter
 * Plugin URI: http://web-dorado.com/products/wordpress-twitter-integration-plugin.html
 * Description: The Widget Twitter plugin lets you to fully integrate your WordPress site with your Twitter account.  
 * Version: 1.0.2
 * Author: http://web-dorado.com/
 * License: GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
 
 
define('WD_WDTI_DIR', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
define('WD_WDTI_URL', plugins_url(plugin_basename(dirname(__FILE__))));
 
//Twitter Integration Plugin menu. 

function twitt_options_panel() {
  $twitt_page = add_menu_page('Widget Twitter', 'Widget Twitter', 'manage_options', 'twitter_integration', 'twitter_integration', WD_WDTI_URL . '/images/new_twitt.png');
   add_action('admin_print_styles-' . $twitt_page, 'twitt_styles');
   add_action('admin_print_scripts-' . $twitt_page, 'twitt_scripts');
   
  $licensing_plugins_page = add_submenu_page('twitter_integration', 'Licensing', 'Licensing', 'manage_options', 'licensing_twitter_integration', 'twitter_integration');

  $featured_plugins_page = add_submenu_page('twitter_integration', 'Featured Plugins', 'Featured Plugins', 'manage_options', 'featured_plugins_twitter_integration', 'twitter_integration');
   add_action('admin_print_styles-' . $featured_plugins_page, 'WDTI_featured_plugins_styles'); 

  $uninstall_page = add_submenu_page('twitter_integration', 'Uninstall', 'Uninstall', 'manage_options', 'uninstall_twitter_integration', 'twitter_integration');
   add_action('admin_print_styles-' . $uninstall_page, 'twitt_styles');
   add_action('admin_print_scripts-' . $uninstall_page, 'twitt_scripts');
   
}
add_action('admin_menu', 'twitt_options_panel');
add_action('wp_ajax_addPostsPages', 'twitt_filemanager_ajax');

// Add the Twitter Integration button to editor for shortcode.

add_filter('mce_external_plugins', 'twitt_register');
add_filter('mce_buttons', 'twitt_add_button', 0);
add_action('wp_ajax_WDTIShortcode', 'twitt_filemanager_ajax');
// add_action('admin_head', 'twitt_admin_ajax');
add_action('print_head_scripts', 'twitt_admin_ajax');


function twitt_register($plugin_array) {
  $url = WD_WDTI_URL . '/js/twitt_editor_button.js';
  $plugin_array["twitt_mce"] = $url;
  return $plugin_array;
}

function twitt_add_button($buttons) {
  array_push($buttons, "twitt_mce");
  return $buttons;
}

function twitt_admin_ajax() {
  ?>
  <script>
    var twitt_admin_ajax = '<?php echo add_query_arg(array('action' => 'WDTIShortcode' , 'function_kind' => 'display_shortcode_for_twitt'), admin_url('admin-ajax.php')); ?>';
    var twitt_plugin_url = '<?php echo WD_WDTI_URL; ?>';
  </script>
  <?php
}

// Twitter Integration Widget.

if (class_exists('WP_Widget')) {
  require_once(WD_WDTI_DIR . '/admin/controllers/WDTIControllerWidget.php');
  add_action('widgets_init', create_function('', 'return register_widget("WDTIControllerWidget");'));
}

// Twitter Integration functions for popup

function twitt_filemanager_ajax() {
  global $wpdb;
  require_once(WD_WDTI_DIR . '/framework/WDWLibrary.php');
  $page = WDWLibrary::get('action');
  if ($page != '') { 
    require_once (WD_WDTI_DIR . '/popupcontent/controller.php');
    $controller_class = 'PopupcontentController';
    $controller = new $controller_class();
    $controller->execute();
  }
} 

// Twitter Integration functions for page_nav,search 

function  twitter_integration() {
  global $wpdb;
  require_once(WD_WDTI_DIR . '/framework/WDWLibrary.php');
  $page = WDWLibrary::get('page');
   if ($page == 'twitter_integration' or $page == 'uninstall_twitter_integration' or $page == 'featured_plugins_twitter_integration' or $page == 'licensing_twitter_integration') {
    require_once (WD_WDTI_DIR . '/admin/controllers/WDTIController' . ucfirst(strtolower($page)) . '.php');
    $controller_class = 'WDTIController' . ucfirst(strtolower($page));
    $controller = new $controller_class();
    $controller->execute();
  }
}

//Twitter Integration frontend

add_shortcode('Widget_Twitter', 'twitt_shortcode');
function twitt_shortcode($atts) {
  extract(shortcode_atts(array(
	      'id' => 'no Twitter'
     ), $atts));	 
	 ob_start();
     front_end_twitt($id);
	 return ob_get_clean();
}

//by shortcode

function front_end_twitt($id) {
  global $wpdb;
  global $post;
  $query = "SELECT * FROM ".$wpdb->prefix."twitter_integration WHERE (id LIKE '%" . $id . "%') AND `published`=1 ";
  $param = $wpdb->get_row($query);
    if($param) {
		switch($param->type) {
		    case 'tweetbutton':
		        if($param->url_type=='normal') {
	             $url = $param->url;
                } 
			    else {
	             $url = get_permalink($post->ID);
				} 
			    $param->code=str_replace('autoSITEURLauto',$url,$param->code);			 
			    if($param->tw_text=='')
			     $param->code=str_replace('data-text=""','data-text="' . $post->post_title . '"',$param->code);
            break;		   
		    case 'mention':
		        if($param->tw_text=='')
			     $param->code=str_replace('&text="','&text=' . $post->post_title . '"',$param->code); 
            break;		  
            case 'hashtag':
		        if($param->tw_text=='')
			     $param->code=str_replace('&text="','&text=' . $post->post_title . '"',$param->code);
            break;
		}
        echo $param->code;
    }
    else 
        echo 'no Twitter with current id';
}

//by choosen posts or pages 

add_filter('the_content','twitt_front_end',1000);

function twitt_front_end($content) {
  global $wpdb;  
  global $post; 
  $continue = false;
  $query = "SELECT * FROM ".$wpdb->prefix."twitter_integration WHERE (posts LIKE '%" . $post->ID . "," . "%' OR posts='all_posts' OR pages LIKE '%" . $post->ID . "," . "%' OR pages='all_pages') AND `published`=1 ";
  $params = $wpdb->get_results($query); 
    if($params) { 
        foreach ($params as $param) {
			if($param->posts=='all_posts') $param->posts .= ',all_posts'; 
			if($param->pages=='all_pages') $param->pages .= ',all_pages';
            $sorted_posts=explode(',',$param->posts);
			$sorted_pages=explode(',',$param->pages); 
			if($post->post_type=='post') {
				for ($i=0;$i <= (count($sorted_posts)-1);$i++) { 
					if($sorted_posts[$i]==$post->ID or $param->posts=='all_posts,all_posts')
						$continue = true;
				}
			}
			elseif($post->post_type=='page') {
				for ($j=0;$j <= (count($sorted_pages)-1);$j++) {
					if($sorted_pages[$j]==$post->ID or $param->pages=='all_pages,all_pages')
						$continue = true;
				}
			}
			if($continue) { 
				switch($param->type) {
					case 'tweetbutton':
						if($param->url_type=='normal') {
						 $url = $param->url;
						} 
						else {
						 $url = get_permalink($post->ID);
						}
						$param->code=str_replace('autoSITEURLauto',$url,$param->code); 			
						if($param->tw_text=='')
						 $param->code=str_replace('data-text=""','data-text="' . $post->post_title . '"',$param->code);
					break;		   
					case 'mention':
						if($param->tw_text=='')
						 $param->code=str_replace('&text="','&text=' . $post->post_title . '"',$param->code);
					break;  
					case 'hashtag':
						if($param->tw_text=='')
						 $param->code=str_replace('&text="','&text=' . $post->post_title . '"',$param->code);
					break;
				}		  
				if($post->post_type=='post') { 
					switch($param->place) {
						case 'top':		   
							$content =  $param->code . $content;
						break;
						case 'bottom':		   
							$content =  $content . $param->code;
						break;
						case 'both':		   
							$content = $param->code . $content . $param->code;
						break;
					}
				} 
				else if ($post->post_type=='page') { 
					switch($param->item_place) {
						case 'top':		   
							$content =  $param->code . $content;
						break;
						case 'bottom':		   
							$content =  $content . $param->code;
						break;
						case 'both':		   
							$content = $param->code . $content . $param->code;
						break;
					}
				}
				$continue = false;
			}	
		}
		return $content;
	}
    else 
	    return $content;
}

//Twitter Integration Activate plugin.

function twitter_integration_activate() {
  global $wpdb;
  $twitter_params = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "twitter_integration` (
    `id` bigint(20) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `type` varchar(255) NOT NULL,
	`order` bigint(20) NOT NULL,
    `published` tinyint(5) NOT NULL,
    `url` varchar(200) NOT NULL,
    `lang` varchar(200) NOT NULL,
    `width` varchar(255) NOT NULL,
    `dnt` varchar(15) NOT NULL,
    `count_mode` varchar(255) NOT NULL,
    `url_type` varchar(50) NOT NULL,
	`via` varchar(80) NOT NULL,
    `tw_text` varchar(4000) NOT NULL,
    `lang_type` varchar(255) NOT NULL,
    `counturl` varchar(255) NOT NULL,
    `but_size` varchar(255) NOT NULL,
    `align` varchar(255) NOT NULL,
    `show_screen_name` varchar(255) NOT NULL,
    `place` varchar(255) NOT NULL,
    `item_place` varchar(255) NOT NULL,
    `css` varchar(255) NOT NULL,
    `height` varchar(255) NOT NULL,
    `login_text` varchar(255) NOT NULL,
    `posts` text NOT NULL,
    `pages` text NOT NULL,
    `code` text NOT NULL,
    `show_count` varchar(255) NOT NULL,
    `theme` varchar(255) NOT NULL,
    `link_color` varchar(255) NOT NULL,
    `chrome` varchar(255) NOT NULL,
    `border` varchar(255) NOT NULL,
    `tweet_limit` varchar(255) NOT NULL,
    `aria_polite` varchar(255) NOT NULL,
    `show_replies` varchar(255) NOT NULL,
    `screen_name` varchar(255) NOT NULL,
    `widget_id` varchar(255) NOT NULL,
    `timeline_type` varchar(255) NOT NULL,
    `tweet_to` varchar(255) NOT NULL,
    `username_to_1` varchar(255) NOT NULL,
    `username_to_2` varchar(255) NOT NULL,
    `tw_stories` varchar(4000) NOT NULL,
    `tw_hashtag` varchar(255) NOT NULL,
    `noheader` varchar(200) NOT NULL,
    `nofooter` varchar(255) NOT NULL,
    `noborders` varchar(255) NOT NULL,
    `noscrollbar` varchar(255) NOT NULL,
	`transparent` varchar(255) NOT NULL,	
     PRIMARY KEY (`id`)
  ) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
  $wpdb->query($twitter_params);
}
 register_activation_hook(__FILE__, 'twitter_integration_activate');
 
 //Twitter Integration styles
 
 function twitt_styles() {
  wp_enqueue_style('twitt_tables', WD_WDTI_URL . '/css/twitt_tables.css');
  wp_admin_css('thickbox');
}

function WDTI_featured_plugins_styles() {
  wp_enqueue_style('Featured_Plugins', WD_WDTI_URL . '/css/WDTI_featured_plugins.css');
}

//Twitter Integration scripts

 function twitt_scripts() {
  wp_enqueue_script('thickbox');
  wp_enqueue_script('twitt_admin', WD_WDTI_URL . '/js/twitt.js');
  wp_enqueue_script('jquery');
  wp_enqueue_script('jquery-ui-sortable');
  wp_enqueue_script('jscolor', WD_WDTI_URL . '/js/jscolor/jscolor.js');
}
?>