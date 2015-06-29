<?php

ini_set("arg_separator.output", "&raquo;");

add_action( 'after_setup_theme', 'cs_theme_setup' );
function cs_theme_setup() {

	/* Add theme-supported features. */
	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
 	load_theme_textdomain('Statfort', get_template_directory() . '/languages');
	
	if (!isset($content_width)){
		$content_width = 1170;
	}
	$args = array(
		'default-color' => '',
		'flex-width' => true,
		'flex-height' => true,
		'default-image' => '',
	);
	add_theme_support('custom-background', $args);
	add_theme_support('custom-header', $args);
	// This theme uses post thumbnails
	add_theme_support('post-thumbnails');

	// Add default posts and comments RSS feed links to head
	add_theme_support('automatic-feed-links');
	/* Add custom actions. */
	global $pagenow;

	if (is_admin() && isset($_GET['activated']) && $pagenow == 'themes.php'){
		
		if(!get_option('cs_theme_options')){
			add_action('admin_head', 'cs_activate_widget');
			add_action('init', 'cs_activation_data');
			wp_redirect( admin_url( 'admin.php?page=cs_demo_importer' ) );
		}
	}
	if (!session_id()){ 
		add_action('init', 'session_start');
	}
 	add_action( 'init', 'cs_register_my_menus' );
	add_action('admin_enqueue_scripts', 'cs_admin_scripts_enqueue');
	add_action('wp_enqueue_scripts', 'cs_front_scripts_enqueue');
	add_action('pre_get_posts', 'cs_get_search_results');
	/* Add custom filters. */
	add_filter('widget_text', 'do_shortcode');
	add_filter('user_contactmethods','cs_contact_options',10,1);
	add_filter('the_password_form', 'cs_password_form' );
	add_filter('wp_page_menu','cs_add_menuid');
	add_filter('wp_page_menu', 'cs_remove_div' );
	add_filter('nav_menu_css_class', 'cs_add_parent_css', 10, 2);
	add_filter('pre_get_posts', 'cs_change_query_vars');
}
// adding custom images while uploading media start
add_image_size('cs_media_1', 1080, 450, true);
add_image_size('cs_media_2', 980, 408, true);
add_image_size('cs_media_3', 585, 440, true);
add_image_size('cs_media_4', 230, 172, true);
// adding custom images while uploading media end

/* Display navigation to next/previous for single.php */
if ( ! function_exists( 'cs_next_prev_post' ) ) { 
	function cs_next_prev_post(){
	global $post;
	posts_nav_link();
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() )?get_post( $post->post_parent):get_adjacent_post(false,'',true);
	$next     = get_adjacent_post( false, '', false );
	if ( ! $next && ! $previous )
		return;
	?>
    	<div class="post-btn">
 			<?php 
				previous_post_link('%link','<i class="fa fa-angle-left"></i>Previous'); 
				next_post_link('%link','Next<i class="fa fa-angle-right"></i>');
			 ?>
		</div>
	<?php
	}
}


/// Next post link class
function cs_posts_link_next_class($format){
  $format = str_replace('href=', 'class="nextpost" href=', $format);
  return $format;
}
add_filter('next_post_link', 'cs_posts_link_next_class');
/// prev post link class
function cs_posts_link_prev_class($format) {
 $format = str_replace('href=', 'class="prevpost" href=', $format);
  return $format;
}
add_filter('previous_post_link', 'cs_posts_link_prev_class');

/*

Top and Main Navigation

*/

if ( ! function_exists( 'cs_navigation' ) ) {

	function cs_navigation($nav='', $menus = 'menus'){

		global $cs_theme_option;

		// Menu parameters	

		$defaults = array(

			'theme_location' => "$nav",

			'menu' => '',

			'container' => '',

			'container_class' => '',

			'container_id' => '',

			'menu_class' => '',

			'menu_id' => "$menus",

			'echo' => false,

			'fallback_cb' => 'wp_page_menu',

			'before' => '',

			'after' => '',

			'link_before' => '',

			'link_after' => '',

			'items_wrap' => '<ul id="%1$s">%3$s</ul>',

			'depth' => 0,

			'walker' => '',);

		echo do_shortcode(wp_nav_menu($defaults));

	}

}
if ( ! function_exists( 'cs_logo' ) ) {
	function cs_logo(){
		global $cs_theme_option;	
		?>
		<a href="<?php echo home_url(); ?>">
        	<?php  if(isset($cs_theme_option['logo'])){ ?>
        	<img src="<?php echo $cs_theme_option['logo']; ?>" width="<?php echo $cs_theme_option['logo_width']; ?>" height="<?php echo $cs_theme_option['logo_height']; ?>" alt="<?php echo bloginfo('name'); ?>" />
        	<?php }else{ ?>
			<img src="<?php echo get_template_directory_uri();?>/images/logo.png" alt="<?php echo bloginfo('name'); ?>" /> 
			<?php }?>
        </a>

	 <?php

	}

}

/*

Add http to URL
*/
if ( ! function_exists( 'cs_addhttp' ) ) { 
		function cs_addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
		}
}
/*
Remove http from URL
*/
if ( ! function_exists( 'cs_remove_http' ) ) { 
	  function cs_remove_http($url) {
		 $disallowed = array('http://', 'https://');
		 foreach($disallowed as $d) {
			if(strpos($url, $d) === 0) {
			   return str_replace($d, '', $url);
			}
		 }
		 return $url;
	  }
}

// tgm class for (internal and WordPress repository) plugin activation end


// stripslashes / htmlspecialchars for theme option save start
if ( ! function_exists( 'stripslashes_htmlspecialchars' ) ) { 
		function stripslashes_htmlspecialchars($value)
		
		{
		
			$value = is_array($value) ? array_map('stripslashes_htmlspecialchars', $value) : stripslashes(htmlspecialchars($value));
		
			return $value;
		
		}
}

// stripslashes / htmlspecialchars for theme option save end
 
//Home Page Services
if ( ! function_exists( 'cs_services' ) ) { 
		function cs_services(){
			global $cs_theme_option;
			if(isset($cs_theme_option['varto_services_shortcode']) and $cs_theme_option['varto_services_shortcode'] <> ""){ ?>
			<div class="parallax-fullwidth services-container">
					<div class="container">
						<?php if($cs_theme_option['varto_sevices_title'] <> ""){ ?>
						<header class="cs-heading-title">
							<h2 class="cs-section-title"><?php echo $cs_theme_option['varto_sevices_title']; ?></h2>
						</header>
						<?php }
						$content = $cs_theme_option['varto_services_shortcode'];
						$content = htmlspecialchars(stripslashes($content));
	
						$content = str_replace('&', '', $content);

						$content = str_replace(array('amp;quot;', 'quot;', 'amp;#8221;', 'amp;#8243;', 'lt;', 'gt;'), array('"', '"', '"', '"', '<', '>'), $content);
						
						echo do_shortcode($content);
						?> 
					</div>
				</div>
			<div class="clear"></div>
			<?php
			}
			 
		}
}

//Countries Array
if ( ! function_exists( 'cs_get_countries' ) ) {
		function cs_get_countries() {
		
			$get_countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan",
		
				"Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Brazil", "British Virgin Islands",
		
				"Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China",
		
				"Colombia", "Comoros", "Costa Rica", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Democratic People's Republic of Korea", "Democratic Republic of the Congo", "Denmark", "Djibouti",
		
				"Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "England", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Fiji", "Finland", "France", "French Polynesia",
		
				"Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong",
		
				"Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan",
		
				"Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macao", "Macedonia", "Madagascar", "Malawi", "Malaysia",
		
				"Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Morocco", "Mozambique",
		
				"Myanmar(Burma)", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Northern Ireland",
		
				"Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico",
		
				"Qatar", "Republic of the Congo", "Romania", "Russia", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa",
		
				"San Marino", "Saudi Arabia", "Scotland", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa",
		
				"South Korea", "Spain", "Sri Lanka", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor-Leste", "Togo", "Tonga",
		
				"Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Tuvalu", "US Virgin Islands", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "Uruguay",
		
				"Uzbekistan", "Vanuatu", "Vatican", "Venezuela", "Vietnam", "Wales", "Yemen", "Zambia", "Zimbabwe");
		
			return $get_countries;
		
		}
}


// installing tables on theme activating start

	global $pagenow;

	

	// Theme default widgets activation

 
	function cs_activate_widget(){

		$sidebars_widgets = get_option('sidebars_widgets');  //collect widget informations

		// ---- calendar widget setting---

		$calendar = array();

		$calendar[1] = array(

		"title"		=>	'Calendar'

		);

						

		$calendar['_multiwidget'] = '1';

		update_option('widget_calendar',$calendar);

		$calendar = get_option('widget_calendar');

		krsort($calendar);

		foreach($calendar as $key1=>$val1)

		{

			$calendar_key = $key1;

			if(is_int($calendar_key))

			{

				break;

			}

		}

		//---Blog Categories

		$categories = array();

		$categories[1] = array(

		"title"		=>	'Categories',

		"count" => 'checked'

		);

						

		$calendar['_multiwidget'] = '1';

		update_option('widget_categories',$categories);

		$categories = get_option('widget_categories');

		krsort($categories);

		foreach($categories as $key1=>$val1)

		{

			$categories_key = $key1;

			if(is_int($categories_key))

			{

				break;

			}

		}

		// ----upcoming menus with thumbnail widget setting---

		$upcoming_menus_widget = array();

		$upcoming_menus_widget[1] = array(

		"title"		=>	'Our Food',

		"get_post_slug" 	=> 'kitchen',

		"showcount" => '4',

		"thumb" => 'true'

		 );						

		$recent_post_widget['_multiwidget'] = '1';

		update_option('widget_upcoming_menus',$upcoming_menus_widget);

		$upcoming_menus_widget = get_option('widget_upcoming_menus');

		krsort($upcoming_menus_widget);

		foreach($upcoming_menus_widget as $key1=>$val1)

		{

			$upcoming_menus_widget_key = $key1;

			if(is_int($upcoming_menus_widget_key))

			{

				break;

			}

		}

		// ----   recent post with thumbnail widget setting---

		$recent_post_widget = array();

		$recent_post_widget[1] = array(

		"title"		=>	'Latest Blogs',

		"select_category" 	=> 'aidreform',

		"showcount" => '3',

		"thumb" => 'true'

		 );						

		$recent_post_widget['_multiwidget'] = '1';

		update_option('widget_recentposts',$recent_post_widget);

		$recent_post_widget = get_option('widget_recentposts');

		krsort($recent_post_widget);

		foreach($recent_post_widget as $key1=>$val1)

		{

			$recent_post_widget_key = $key1;

			if(is_int($recent_post_widget_key))

			{

				break;

			}

		}

		// ----   recent post without thumbnail widget setting---

		$recent_post_widget2 = array();

		$recent_post_widget2 = get_option('widget_recentposts');

		$recent_post_widget2[2] = array(

		"title"		=>	'Latest Posts',

		"select_category" 	=> 'aidreform',

		"showcount" => '2',

		"thumb" => 'true'

		 );						

		$recent_post_widget2['_multiwidget'] = '1';

		update_option('widget_recentposts',$recent_post_widget2);

		$recent_post_widget2 = get_option('widget_recentposts');

		krsort($recent_post_widget2);

		foreach($recent_post_widget2 as $key1=>$val1)

		{

			$recent_post_widget_key2 = $key1;

			if(is_int($recent_post_widget_key2))

			{

				break;

			}

		}

 		// ----   recent event widget setting---

		$upcoming_events_widget = array();

		$upcoming_events_widget[1] = array(

		"title"		=>	'Upcoming Events',

		"get_post_slug" 	=> 'social-events',

		"showcount" => '4',

 		 );						

		$upcoming_events_widget['_multiwidget'] = '1';

		update_option('widget_upcoming_events',$upcoming_events_widget);

		$upcoming_events_widget = get_option('widget_upcoming_events');

		krsort($upcoming_events_widget);

		foreach($upcoming_events_widget as $key1=>$val1)

		{

			$upcoming_events_widget_key = $key1;

			if(is_int($upcoming_events_widget_key))

			{

				break;

			}

		}

		// ----   recent event countdown widget setting---

		$upcoming_events_countdown_widget = array();

		$upcoming_events_countdown_widget[1] = array(

		"title"		=>	'Upcoming Events',

		"get_post_slug" 	=> 'social-events',

		"showcount" => '1',

 		 );						

		$upcoming_events_countdown_widget['_multiwidget'] = '1';

		update_option('widget_cs_upcomingevents_count',$upcoming_events_countdown_widget);

		$upcoming_events_countdown_widget = get_option('widget_cs_upcomingevents_count');

		krsort($upcoming_events_countdown_widget);

		foreach($upcoming_events_countdown_widget as $key1=>$val1)

		{

			$upcoming_events_countdown_widget = $key1;

			if(is_int($upcoming_events_countdown_widget))

			{

				break;

			}

		}
  
		// --- text widget setting ---

		$text = array();

		$text[1] = array(

			'title' => '',

			'text' => '<a href="'.site_url().'/"><img src="'.get_template_directory_uri().'/images/img-wi1.jpg" alt="" /></a>',

		);						

		$text['_multiwidget'] = '1';

		update_option('widget_text',$text);

		$text = get_option('widget_text');

		krsort($text);

		foreach($text as $key1=>$val1)

		{

			$text_key = $key1;

			if(is_int($text_key))

			{

				break;

			}

		}

	 	//----text widget for contact info----------

		$text2 = array();

		$text2 = get_option('widget_text');

		$text2[2] = array(
			'title' => ' Contact Us',
			'text' => ' <p>The Statfort University<br>1234 South Lipsum Avenue<br>United States , 123456</p>
                <ul>
                    <li>Phone : +618 9261 4600</li>
                    <li>Fax: +618 9261 4699</li>
                    <li>Email: <a href="mailto:info@cloonmore.com">info@cloonmore.com</a></li>
                    <li><a href="">www.website.com</a></li>
                </ul>',



		);						

		$text2['_multiwidget'] = '1';

		update_option('widget_text',$text2);

		$text2 = get_option('widget_text');

		krsort($text2);

		foreach($text2 as $key1=>$val1)

		{

			$text_key2 = $key1;

			if(is_int($text_key2))

			{

				break;

			}

		}

		// --- gallery widget setting ---

		$cs_gallery = array();

		$cs_gallery[1] = array(

			'title' => 'Our Photos',

			'get_names_gallery' => 'gallery',

			'showcount' => '12'

		);						

		$cs_gallery['_multiwidget'] = '1';

		update_option('widget_cs_gallery',$cs_gallery);

		$cs_gallery = get_option('widget_cs_gallery');

		krsort($cs_gallery);

		foreach($cs_gallery as $key1=>$val1)

		{

			$cs_gallery_key = $key1;

			if(is_int($cs_gallery_key))

			{

				break;

			}

		}

		 

		// ---- search widget setting---		

		$search = array();

		$search[1] = array(

			"title"		=>	'',

		);	

		$search['_multiwidget'] = '1';

		update_option('widget_search',$search);

		$search = get_option('widget_search');

		krsort($search);

		foreach($search as $key1=>$val1)

		{

			$search_key = $key1;

			if(is_int($search_key))

			{

				break;

			}

		}
		// --- facebook widget setting-----

		$facebook_module = array();

		$facebook_module[1] = array(

		"title"		=>	'facebook',

		"pageurl" 	=>	"https://www.facebook.com/envato",

		"showfaces" => "on",

		"likebox_height" => "265",

		"fb_bg_color" =>"#F5F2F2",

		);						

		$facebook_module['_multiwidget'] = '1';

		update_option('widget_facebook_module',$facebook_module);

		$facebook_module = get_option('widget_facebook_module');

		krsort($facebook_module);

		foreach($facebook_module as $key1=>$val1)

		{

			$facebook_module_key = $key1;

			if(is_int($facebook_module_key))

			{

				break;

			}

		}

		//----text widget for footer----------
		// Add widgets in sidebars

	$sidebars_widgets['Sidebar'] = array("categories-$categories_key", "upcoming_events-$upcoming_events_widget_key","facebook_module-$facebook_module_key", "cs_gallery-$cs_gallery_key","text-$text_key");

	$sidebars_widgets['footer-widget'] = array("text-$text_key2", "categories-$categories_key", "cs_gallery-$cs_gallery_key", "recentposts-$recent_post_widget_key");
$sidebars_widgets['shop'] = "";	
	update_option('sidebars_widgets',$sidebars_widgets);  //save widget informations

	}

	// Install data on theme activation

 
	function cs_activation_data() {

		global $wpdb;

		$args = array(

			'style_sheet' => 'custom',

			'custom_color_scheme' => '#409F74',
   
			'layout_option' => 'wrapper_boxed',

			// Banner Backgorung Color

			'banner_bg_color' => '#29688a',

			// footer Color Settigs

			'header_styles' => 'header1',

			'default_header' => 'header1',

			// HEADER SETTINGS header_cart 

			'header_search' => 'on',
			'header_logo' => 'on',

 
			'header_languages' => 'on',

			'header_cart' => 'off',

 
			'header_languages' => '',

			'header_social_icons' => 'on',
			'show_beadcrumbs' => 'on',

			'header_next_event' => 'our-event',
   
 
			'announcement_title' => 'Announcement',

			'announcement_blog_category' => '',

			'announcement_no_posts' => '5',



			'bg_img' => '0',

			'bg_img_custom' => '',

			'bg_position' => 'center',

			'bg_repeat' => 'no-repeat',

			'bg_attach' => 'fixed',

			'pattern_img' => '0',

			'custome_pattern' => '',

			'bg_color' => '#444E58',

			'logo' => get_template_directory_uri().'/images/logo.png',

			'logo_width' => '122',

			'logo_height' => '62',

			'header_sticky_menu' => 'on',

			'fav_icon' => get_template_directory_uri() . '/images/favicon.png',

			'header_code' => '',

 			'footer_bgimg' => get_template_directory_uri().'/images/footer-bg.jpg',

			'footer_logo' => get_template_directory_uri().'/images/footer-logo.png',

			'copyright' =>  '&copy;'.gmdate("Y")." ".get_option("blogname")." Wordpress All rights reserved.", 

			'powered_by' => '<a href="#">Design by ChimpStudio</a>',

			'powered_icon' => '',

			'analytics' => '',

			'responsive' => 'on',

			'style_rtl' => '',
 
			// switchers

			'color_switcher' => '',

			'trans_switcher' => '',

			'show_slider' => '',

			'slider_name' => 'slider',

			'slider_type' => 'flex',

			'show_partners' => 'none',

			'partners_title' => 'Our Partners',

			'partners_gallery' => '',

 
			'post_title' => '',

  
			'sidebar' => array( 'Sidebar', 'footer-widget','shop'),
			
			//Fonts
			'content_size' => '12',
			
			'content_size_g_font' => '',

			// slider setting

			'flex_effect' => 'fade',

			'flex_auto_play' => 'on',

			'flex_animation_speed' => '7000',

			'flex_pause_time' => '600',

			'slider_id' => '',

			'slider_view' => '',

			'social_net_title' => '',

			'social_net_icon_path' => array( '', '', '', '', '', '', ),

			'social_net_awesome' => array( 'fa-facebook-square', 'fa-google-plus-square', 'fa fa-twitter-square', 'fa-youtube-square', 'fa-skype', 'fa-instagram', ' fa-foursquare' ),

			//'social_net_color_input' => array( '#005992', '#2a99e1', '#927f46', '#d70d38', '#ff0000', '#009bff;', '#2a99e1', '#2a99e1', ' #2a99e1' ),

			'social_net_url' => array( 'Facebook URL', 'Google-plus URL', 'Twitter URL', 'Youtube URL', 'Skype URL', 'Instagram URL', 'Foursquare URL' ),

			'social_net_tooltip' => array( 'Facebook', 'Google-plus', 'Twitter', 'Youtube', 'Skype', 'Instagram', 'Foursquare' ),

			'facebook_share' => 'on',

			'twitter_share' => 'on',

			'linkedin_share' => 'on',

			'pinterest_share' => 'on',

			'tumblr_share' => 'on',

			'google_plus_share' => 'on',

			'cs_other_share' => 'on',

			'mailchimp_key' => '',

			// tranlations
			'trans_view_all' => 'View All',
			'trans_course_programms' => 'Programms',
			'trans_course_start_from' => 'Starts',
            'trans_course_apply_now' => 'Apply',
			
            'trans_course_instructor' => 'Instructors',
            'trans_course_credit_hours' => 'Credit Hours',
            'trans_course_course_title' => 'Course Title',
			'trans_course_phone' => 'Phone',
			'trans_course_fax' => 'Fax',
			'trans_course_email' => 'Email',
			'trans_course_admission' => 'For Admission',
			'trans_course_duration' => 'Duration',
		
			'trans_event_free_entry' => 'Free Entry',
			'trans_event_sold_out' => 'Sold Out',
			'trans_event_cancelled' => 'Cancelled',
            'trans_event_buy_ticket' => 'Buy Ticket',
			'trans_event_eventtime' => 'Event Time',
			'trans_event_location' => 'Location',
			'trans_event_speakers' => 'Speakers',
			

			'res_first_name' => 'First Name',

			'res_last_name' => 'Last Name',

            'trans_subject' => 'Subject',

            'trans_message' => 'Message',

            'trans_share_this_post' => 'Share Now',

            'trans_content_404' => "It seems we can not find what you are looking for.",

			'trans_featured' => 'Featured',

			'trans_read_more' => 'read more',

			

			

			// translation end

           	'pagination' => 'Show Pagination',

			'record_per_page' => '5',

			'cs_layout' => 'none',

			'cs_sidebar_left' => '',

			'cs_sidebar_right' => '',

			'under-construction' => '',

			'showlogo' => 'on',

			'socialnetwork' => 'on',

			'under_construction_text' => '<h1 class="colr">OUR WEBSITE IS UNDERCONSTRUCTION</h1><p>We shall be here soon with a new website, Estimated Time Remaining</p>',

			'launch_date' => '2014-10-24',

 			'consumer_key' => '',
			'consumer_secret' => '',
			'access_token' => '',
			'access_token_secret' => '',
			'varto_sevices_title' => '',
			'varto_services_shortcode' => '',
			

		);

		/* Merge Heaser styles

		*/

		update_option("cs_theme_option", $args );
 		update_option("cs_theme_option_restore", $args );
 
	}






// Admin scripts enqueue

function cs_admin_scripts_enqueue() {

    $template_path = get_template_directory_uri() . '/scripts/admin/media_upload.js';

    wp_enqueue_script('my-upload', $template_path, array('jquery', 'media-upload', 'thickbox', 'jquery-ui-droppable', 'jquery-ui-datepicker', 'jquery-ui-slider', 'wp-color-picker'));

    wp_enqueue_script('custom_wp_admin_script', get_template_directory_uri() . '/scripts/admin/cs_functions.js');

    wp_enqueue_style('custom_wp_admin_style', get_template_directory_uri() . '/css/admin/admin-style.css', array('thickbox'));

	wp_enqueue_style('wp-color-picker');

}

// Backend functionality files



require_once (TEMPLATEPATH . '/include/event.php');

require_once (TEMPLATEPATH . '/include/slider.php');

require_once (TEMPLATEPATH . '/include/gallery.php');

require_once (TEMPLATEPATH . '/include/page_builder.php');

require_once (TEMPLATEPATH . '/include/post_meta.php');

require_once (TEMPLATEPATH . '/include/short_code.php');

require_once (TEMPLATEPATH . '/include/course.php');

require_once (TEMPLATEPATH . '/include/admin_functions.php');

require_once (TEMPLATEPATH . '/include/team.php');

require_once (TEMPLATEPATH . '/include/widgets.php');

require_once (TEMPLATEPATH . '/functions-theme.php');


require_once (TEMPLATEPATH . '/include/mailchimpapi/mailchimpapi.class.php');

require_once (TEMPLATEPATH . '/include/mailchimpapi/chimp_mc_plugin.class.php');



/////// Require Woocommerce///////



require_once (TEMPLATEPATH . '/include/config_woocommerce/config.php');

require_once (TEMPLATEPATH . '/include/config_woocommerce/product_meta.php');



/////////////////////////////////





if (current_user_can('administrator')) {

	// Addmin Menu CS Theme Option
	require_once (TEMPLATEPATH . '/include/theme_option.php');

	add_action('admin_menu', 'cs_theme');

	function cs_theme() {

		add_theme_page('CS Theme Option', 'CS Theme Option', 'read', 'cs_theme_options', 'theme_option');
		add_theme_page( "Import Demo Data" , "Import Demo Data" ,'read', 'cs_demo_importer' , 'cs_demo_importer');

	}
}


// add twitter option in user profile

function cs_contact_options( $contactoptions ) {

	$contactoptions['twitter'] = 'Twitter';

	return $contactoptions;

}
// Template redirect in single Gallery and Slider page
if ( ! function_exists( 'cs_slider_gallery_template_redirect' ) ) {
	
		function cs_slider_gallery_template_redirect(){
		
			if ( get_post_type() == "cs_gallery" or get_post_type() == "cs_slider" ) {
		
				global $wp_query;
		
				$wp_query->set_404();
		
				status_header( 404 );
		
				get_template_part( 404 ); exit();
		
			}
		
		}
}

// enque style and scripts front end

function cs_front_scripts_enqueue() {

	global $cs_theme_option;

     if (!is_admin()) {

		wp_enqueue_style('style_css', get_stylesheet_uri());
 		wp_enqueue_style('shop_css', get_template_directory_uri() . '/css/shop.css');
		wp_enqueue_style('prettyPhoto_css', get_template_directory_uri() . '/css/prettyphoto.css');
  		if ( $cs_theme_option['color_switcher'] == "on" ) {

			wp_enqueue_style('color-switcher_css', get_template_directory_uri() . '/css/color-switcher.css');

		}

  		wp_enqueue_style('bootstrap_css', get_template_directory_uri() . '/css/bootstrap.css');

		wp_enqueue_style('font-awesome_css', get_template_directory_uri() . '/css/font-awesome.css');

 
		wp_enqueue_style('widget_css', get_template_directory_uri() . '/css/widget.css');
  
		// Register stylesheet

    	// Enqueue stylesheet

 
		   	wp_enqueue_style( 'wp-mediaelement' );

 		    wp_enqueue_script('jquery');

			wp_enqueue_script( 'wp-mediaelement' );

			wp_enqueue_script('bootstrap_js', get_template_directory_uri() . '/scripts/frontend/bootstrap.min.js', '', '', true);

			wp_enqueue_script('modernizr_js', get_template_directory_uri() . '/scripts/frontend/modernizr.js', '', '', true);
			wp_enqueue_script('prettyPhoto_js', get_template_directory_uri() . '/scripts/frontend/jquery.prettyphoto.js', '', '', true);
			wp_enqueue_script('functions_js', get_template_directory_uri() . '/scripts/frontend/functions.js', '0', '', true);

 			if (isset($cs_theme_option['header_sticky_menu']) and $cs_theme_option['header_sticky_menu'] == "on"){
				wp_enqueue_script('bscrolltofixed_js', get_template_directory_uri() . '/scripts/frontend/jquery-scrolltofixed.js', '', '', true);
			}	

 			if ( $cs_theme_option['style_rtl'] == "on"){

				wp_enqueue_style('rtl_css', get_template_directory_uri() . '/css/rtl.css');

 			}

			if 	($cs_theme_option['responsive'] == "on") {

				echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">';

				wp_enqueue_style('responsive_css', get_template_directory_uri() . '/css/responsive.css');

			}

     }

}

// Masonry Style and Script enqueue

function cs_enqueue_masonry_style_script(){

	wp_enqueue_style('masonry_css', get_template_directory_uri() . '/css/masonry.css');

	wp_enqueue_script('jquery.masonry_js', get_template_directory_uri() . '/scripts/frontend/jquery.masonry.min.js', '', '', true);

}

// Validation Script Enqueue

function cs_enqueue_validation_script(){

	wp_enqueue_script('jquery.validate.metadata_js', get_template_directory_uri() . '/scripts/admin/jquery.validate.metadata.js', '', '', true);

	wp_enqueue_script('jquery.validate_js', get_template_directory_uri() . '/scripts/admin/jquery.validate.js', '', '', true);

}

// Flexslider Script and style enqueue

function cs_enqueue_flexslider_script(){

   	wp_enqueue_script('jquery.flexslider-min_js', get_template_directory_uri() . '/scripts/frontend/jquery.flexslider-min.js', '', '', true);
    wp_enqueue_style('flexslider_css', get_template_directory_uri() . '/css/flexslider.css');

}

function cs_cycleslider_script(){
	wp_enqueue_script('jquerycycle2_js', get_template_directory_uri().'/scripts/frontend/jquerycycle2.js', '', '', true);
	wp_enqueue_script('cycleslider_js', get_template_directory_uri().'/scripts/frontend/cycle2carousel.js', '', '', true);
} 

// Flexslider Script and style enqueue

function cs_enqueue_countdown_script(){

   	wp_enqueue_script('jquery.countdown_js', get_template_directory_uri() . '/scripts/frontend/jquery.countdown.js', '', '', true);

}
// news ticker enqueue style and script
function cs_enqueue_newsticker(){

   	wp_enqueue_script('jquery.newsticker_js', get_template_directory_uri() . '/scripts/frontend/news-ticker.js', '', '', true);
    wp_enqueue_style('newsticker_css', get_template_directory_uri() . '/css/news-ticker.css');

}

function cs_addthis_script_init_method(){
	if( is_single()){
		wp_enqueue_script( 'cs_addthis', 'http://s7.addthis.com/js/250/addthis_widget.js#pubid=xa-4e4412d954dccc64', ",",'true');
	}
}


// Favicon and header code in head tag//
if ( ! function_exists( 'cs_header_settings' ) ) {
	
	  function cs_header_settings() {
	  
		  global $cs_theme_option;
	  
		  ?>
	  
		   <link rel="shortcut icon" href="<?php echo $cs_theme_option['fav_icon'] ?>" />
	  
		   <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	  
		   
	  
		   <?php  
	  
		   echo  htmlspecialchars_decode($cs_theme_option['header_code']); 
	  
	  }
}

// Favicon and header code in head tag//
if ( ! function_exists( 'cs_footer_settings' ) ) {
	
	  function cs_footer_settings() {
	  
		  global $cs_theme_option;
	  
		  ?>
	  
			<!--[if lt IE 9]><link rel="stylesheet" href="<?php echo get_template_directory_uri();?>/css/ie8.css" /><![endif]-->
	  
		   <?php  
		  if(isset($cs_theme_option['analytics'])){
			  echo htmlspecialchars_decode($cs_theme_option['analytics']);
		  }
	  
	  }
}

// Home page Slider //
if(!function_exists('cs_get_home_slider')){
	function cs_get_home_slider(){

    global $cs_theme_option;

	if($cs_theme_option['show_slider'] =="on"){
?>

      <div id="banner">

           <?php 
            if(isset($cs_theme_option['slider_type']) and $cs_theme_option['slider_type'] == "flex"){
			
			 $width = 980;
             $height = 408;
              $slider_slug = $cs_theme_option['slider_name'];

              if($slider_slug <> ''){

                      $args=array(

                        'name' => $slider_slug,

                        'post_type' => 'cs_slider',

                        'post_status' => 'publish',

                        'showposts' => 1,

                      );

                      $get_posts = get_posts($args);

                      if($get_posts){

                              $slider_id = $get_posts[0]->ID;



                                      cs_flex_slider($width,$height,$slider_id);



                      } else {

                              $slider_id = '';

                              echo '<div class="box-small no-results-found heading-color"> <h5>';

                                      _e("No results found.",'Statfort');

                              echo ' </h5></div>';

                      }

              }

			}else if(isset($cs_theme_option['slider_type']) and $cs_theme_option['slider_type'] == "custom"){
            	echo do_shortcode(htmlspecialchars_decode($cs_theme_option['slider_id']));	
    		}

      		?>

      </div>

    <?php 


	}

	}
}

// Page Sub header title and subtitle //
if(!function_exists('get_subheader_title')){
	function get_subheader_title(){

	global $post, $wp_query;;

	$show_title=true;

	$show_subtitle=true;

	$subtitle = '';

	$get_title = '';

		if (is_page() || is_single()) {

				if (is_page() ){

				  $cs_xmlObject = cs_meta_page('cs_page_builder');

				  if (isset($cs_xmlObject)) {

					  if ($cs_xmlObject->page_title == "No") {

						  $show_title = false;

					  }

					  $subtitle = $cs_xmlObject->page_sub_title;

				  }

				  if(isset($show_title) && $show_title==true){

					$get_title = '<h1 class="cs-page-title">' . substr(strip_tags(get_the_title()), 0, 40) . '</h1>';

					}

                } elseif (is_single()) {

						$post_type = get_post_type($post->ID);

						 if ($post_type == "events") {

							 $post_type = "cs_event_meta";

						 }else if ($post_type == "courses") {
							 $post_type = "cs_course";

						 }else {

							 $post_type = "post";

						 }

						 $post_xml = get_post_meta($post->ID, $post_type, true);

						 if ($post_xml <> "") {

						   $cs_xmlObject = new SimpleXMLElement($post_xml);

						  

						 }

					   if (isset($cs_xmlObject) && $cs_xmlObject->sub_title <> "") {

						  $subtitle = $cs_xmlObject->sub_title;

					   }

					   	$show_title=true;

						$show_subtitle=true;

					    if(isset($show_title) && $show_title==true){

							$get_title = '<h1 class="cs-page-title">' . get_the_title() . '</h1>';

						}

				}

				if(isset($show_title) && $show_title==true){

					echo $get_title;

				}

                if(isset($subtitle) && $subtitle <> ''){echo '<p>' . $subtitle . '</p>';}

		  } else { ?>

			<h1 class="cs-page-title"><?php cs_post_page_title();?></h1>



 		 <?php }

	}
}

// character limit 
if(!function_exists('cs_character_limit')){
	function cs_character_limit($string = '',$start_limit ='',$end_limit=''){

	return substr($string,$start_limit,$end_limit)."...";

	}
}

// hide figure tag on post list page
if ( ! function_exists( 'fnc_post_type' ) ) {
	function fnc_post_type($post_view,$image_url = ''){
		$cs_post_cls = '';
		if ( $post_view <> "" ) {
			
			if($post_view=="Audio"){
				$cs_post_cls ='cls-post-audio';
			}elseif($post_view == "Video"){
	
				$cs_post_cls ='cls-post-video';
	
			}elseif($post_view == "Slider"){
	
				$cs_post_cls ='cls-post-slider';
	
			}elseif($image_url <> '' and $post_view == "Single Image"){
	
				$cs_post_cls ='cls-post-image';
	
			}else{
	
				$cs_post_cls ='cls-post-default cls-post-noimg';
	
			}
	
		}
	
		return $cs_post_cls;
	}
}
// Get post meta in xml format at front end //
if ( ! function_exists( 'cs_show_partner' ) ) {
function cs_show_partner(){

	global $cs_theme_option;

	?>

    <div class="element_size_100">

    <!-- Logo Slide Start -->

    <div class="our-sponcers">

        <?php if(isset($cs_theme_option['partners_title']) and $cs_theme_option['partners_title'] <> ''){ ?>

             <header class="cs-heading-title">

                <h2 class="cs-section-title cs-heading-color"><?php echo $cs_theme_option['partners_title'];?></h2>

            </header> 

        <?php } ?>

      	<div class="container">

        <div id="container" class="fullwidth">

            <div class="flexslider">



            <ul class="slides lightbox">

                <?php

					$gal_album_db = '';

                    $gal_album_db =$cs_theme_option['partners_gallery'];

                    if($gal_album_db <> "0"){

                        // galery slug to id start

                        $args=array(

                            'name' => $gal_album_db,

                            'post_type' => 'cs_gallery',

                            'post_status' => 'publish',

                            'showposts' => 1,

                        );

                        $get_posts = get_posts($args);

                        if($get_posts){

                            $gal_album_db = $get_posts[0]->ID;

                        }

                        // galery slug to id end	

                        $cs_meta_gallery_options = get_post_meta($gal_album_db, "cs_meta_gallery_options", true);

                        // pagination start

                        if ( $cs_meta_gallery_options <> "" ) {

                            $xmlObject = new SimpleXMLElement($cs_meta_gallery_options);

                            $limit_start = 0;

                            $limit_end = count($xmlObject);

                            //foreach ( $xmlObject->children() as $node ) {

                            for ( $i = $limit_start; $i < $limit_end; $i++ ) {

                                $path = $xmlObject->gallery[$i]->path;

                                $title = $xmlObject->gallery[$i]->title;

                                $use_image_as = $xmlObject->gallery[$i]->use_image_as;

                                $video_code = $xmlObject->gallery[$i]->video_code;

                                $link_url = $xmlObject->gallery[$i]->link_url;

                                //$image_url = wp_get_attachment_image_src($path, array(438,288),false);

                                $image_url = cs_attachment_image_src($path, 150, 150);

                                //$image_url_full = wp_get_attachment_image_src($path, 'full',false);

                                $image_url_full = cs_attachment_image_src($path, 0, 0);

                                ?>
                                <li>
                                	<a href="<?php if($use_image_as==1)echo $video_code; elseif($use_image_as==2) echo $link_url; else echo $image_url_full;?>" 
                                    target="<?php if($use_image_as==2) { echo '_blank'; }else{ echo '_self';}?>" 
                                    data-rel="<?php if($use_image_as==1)echo "prettyPhoto1";  elseif($use_image_as==2) echo ""; else echo "prettyPhoto[gallery2]"?>">
									<?php echo "<img src='".$image_url."' alt='".$title."' />"; ?>
                                    </a>

                                </li>

                                <?php

                            }

                        }

                    }else{

                        echo '<h4 class="cs-heading-color">'.__( 'No results found.', 'Perspective' ).'</h4></li>';

                    } 

                ?>

            

            </ul>

            </div>

        </div>

        </div>

         <?php 

            cs_enqueue_flexslider_script();

        ?>

        <script type="text/javascript">
             jQuery(window).load(function(){
				 jQuery('.our-sponcers .flexslider').flexslider({
					animation: "slide",
				 	itemWidth: 153,
					itemMargin: 5,
					start: function(slider) {
				  	jQuery('body').removeClass('loading');
			
				}
			  });
             });

        </script>

    <!-- Logo Slide End -->

    </div>

</div>

<?php                    
	}
}


// Front End Functions END

// post date/categories/tags
if ( ! function_exists( 'cs_posted_on' ) ) {
	function cs_posted_on(){
		?>
		<ul class="post-options">
			<li><i class="fa fa-calendar"></i><time datetime="<?php echo date('d-m-y',strtotime(get_the_date()));?>"><?php echo get_the_date();?></time></li>
			<li><i class="fa fa-user"></i><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"> <?php echo get_the_author(); ?></a></li>
			<?php
					/* translators: used between list items, there is a space after the comma */
					$before_cat = "<li><i class='fa fa-list'></i>".__( '','Statfort')."";
					$categories_list = get_the_term_list ( get_the_id(), 'category', $before_cat, ', ', '</li>' );
					if ( $categories_list ){
						printf( __( '%1$s', 'Statfort'),$categories_list );
					}
					if ( comments_open() ) {  
						echo "<li>
						<i class='fa fa-comment'></i>"; comments_popup_link( __( '0 Comment', 'Statfort' ) , __( '1 Comment', 'Statfort' ), __( '% Comment', 'Statfort' ) ); 
					}
					edit_post_link( __( '<i class="fa fa-pencil-square-o"></i>', 'Statfort'), '', '' ); 
			?>
		</ul>
	<?php
	}
}
/*------Header Functions End------*/
if ( ! function_exists( 'cs_register_my_menus' ) ) {
	function cs_register_my_menus() {

  		register_nav_menus(

			array(

				'main-menu'  => __('Main Menu','Statfort'),

				'top-menu'  => __('Top Menu','Statfort')

 			)

  		);

	}
}
// search varibales start

function cs_get_search_results($query) {

	if ( !is_admin() and (is_search())) {

		$query->set( 'post_type', array('page','post', 'events', 'cs_cause','teams') );

		remove_action( 'pre_get_posts', 'cs_get_search_results' );

	}

}
// password protect post/page

if ( ! function_exists( 'cs_password_form' ) ) {

	function cs_password_form() {

		global $post,$cs_theme_option;

		$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );

		$o = '<div class="password_protected">

				<div class="protected-icon"><a href="#"><i class="fa fa-unlock-alt fa-4x"></i></a></div>

				<h3>' . __( "This post is password protected. To view it please enter your password below:",'Statfort' ) . '</h3>';

		$o .= '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">

					<label><input name="post_password" id="' . $label . '" type="password" size="20" /></label>

					<input class="bgcolr" type="submit" name="Submit" value="'.__("Submit", "Statfort").'" />

				</form>

			</div>';

		return $o;

	}

}
// add menu id
if ( ! function_exists( 'cs_add_menuid' ) ) {
	
	  function cs_add_menuid($ulid) {
	  
		  return preg_replace('/<ul>/', '<ul id="menus">', $ulid, 1);
	  
	  }
}
// remove additional div from menu
if ( ! function_exists( 'cs_remove_div' ) ) {
	function cs_remove_div ( $menu ){
	
		return preg_replace( array( '#^<div[^>]*>#', '#</div>$#' ), '', $menu );
	
	}
}
// add parent class
if ( ! function_exists( 'cs_add_parent_css' ) ) {
	  function cs_add_parent_css($classes, $item) {
	  
		  global $cs_menu_children;
	  
		  if ($cs_menu_children)
	  
			  $classes[] = 'parent';
	  
		  return $classes;
	  
	  }
}
// change the default query variable start
if ( ! function_exists( 'cs_change_query_vars' ) ) {
	function cs_change_query_vars($query) {
	
		if (is_search() || is_home()) {
	
			if (empty($_GET['page_id_all']))
	
				$_GET['page_id_all'] = 1;
	
		   $query->query_vars['paged'] = $_GET['page_id_all'];
	
		   return $query;
	
		}

	 // Return modified query variables

	}
}
// Filter shortcode in text areas

if ( ! function_exists( 'cs_textarea_filter' ) ) { 

	function cs_textarea_filter($content=''){

		return do_shortcode($content);

	}

}

//////////////// Header Cart ///////////////////

add_filter('add_to_cart_fragments', 'woocommerce_header_add_to_cart_fragment');

function woocommerce_header_add_to_cart_fragment( $fragments ) {

	if ( class_exists( 'woocommerce' ) ){

		global $woocommerce;

		ob_start();

		?>

		<div class="cart-sec">

			<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>"><i class="fa fa-shopping-cart"></i><span class="amount"><?php echo $woocommerce->cart->cart_contents_count; ?></span></a>

		</div>

		<?php

		$fragments['div.cart-sec'] = ob_get_clean();

		return $fragments;

	}

}
if ( ! function_exists( 'cs_woocommerce_header_cart' ) ) {
	function cs_woocommerce_header_cart() {

	if ( class_exists( 'woocommerce' ) ){

		global $woocommerce;

		?>

		<div class="cart-sec">

			<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>">
            <i class="fa fa-shopping-cart"></i><span class="amount"><?php echo $woocommerce->cart->cart_contents_count; ?></span></a>

		</div>

		<?php

		}
	}
}

//////////////// Header Cart Ends ///////////////////

//	Add Featured/sticky text/icon for sticky posts.

if ( ! function_exists( 'cs_featured' ) ) {

	function cs_featured(){

		global $cs_transwitch,$cs_theme_option;

		if ( is_sticky() ){ ?>

		<li class="featured"><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Featured','Statfort');}else{ echo $cs_theme_option['trans_featured']; } ?></li>
        <?php

		}

	}

}
// custom function start


// display post page title
if ( ! function_exists( 'cs_post_page_title' ) ) {
	function cs_post_page_title(){
	
		if ( is_author() ) {
	
			global $author;
	
			$userdata = get_userdata($author);
	
			echo __('Author', 'Statfort') . " " . __('Archives', 'Statfort') . ": ".$userdata->display_name;
	
		}elseif ( is_tag() || is_tax('event-tag') || is_tax('portfolio-tag') || is_tax('sermon-tag') ) {
	
			echo __('Tags', 'Statfort') . " " . __('Archives', 'Statfort') . ": " . single_cat_title( '', false );
	
		}elseif ( is_category() || is_tax('event-category') || is_tax('portfolio-category')  || is_tax('sermon-category')  || is_tax('sermon-series')  || is_tax('sermon-pastors') ) {
	
			echo __('Categories', 'Statfort') . " " . __('Archives', 'Statfort') . ": " . single_cat_title( '', false );
	
		}elseif( is_search()){
	
			printf( __( 'Search Results %1$s %2$s', 'Statfort' ), ': ','<span>' . get_search_query() . '</span>' ); 
	
		}elseif ( is_day() ) {
	
			printf( __( 'Daily Archives: %s', 'Statfort' ), '<span>' . get_the_date() . '</span>' );
	
		}elseif ( is_month() ) {
	
			printf( __( 'Monthly Archives: %s', 'Statfort' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'Statfort' ) ) . '</span>' );
	
		}elseif ( is_year() ) {
	
			printf( __( 'Yearly Archives: %s', 'Statfort' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', 'Statfort' ) ) . '</span>' );
	
		}elseif ( is_404()){
	
			_e( 'Error 404', 'Statfort' );
	
		}elseif(!is_page()){
	
			_e( 'Archives', 'Statfort' );
	
		}
	}
}



// Dropcap shortchode with first letter in caps

if ( ! function_exists( 'cs_dropcap_page' ) ) {

	function cs_dropcap_page(){

		global $cs_node;

		$class = $cs_node->dropcap_class;

		$html = '<div class="element_size_'.$cs_node->dropcap_element_size.'">';

			$html .= '<div class="'.$class.'">';

				$html .= $cs_node->dropcap_content;

			$html .= '</div>';

		$html .= '</div>';

		return $html;

	}

}



// block quote short code

if ( ! function_exists( 'cs_quote_page' ) ) {

	function cs_quote_page(){

		global $cs_node;

		$html = '<div class="element_size_'.$cs_node->quote_element_size.'">';

			$html .= '<blockquote style=" text-align:' .$cs_node->quote_align. '; color:' . $cs_node->quote_text_color . '"><span>' . $cs_node->quote_content . '</span></blockquote>';

		$html .= '</div>';

		return $html . '<div class="clear"></div>';

	}

}
// map shortcode with various options

if ( ! function_exists( 'cs_map_page' ) ) {

	function cs_map_page(){

		global $cs_node, $cs_counter_node;

		if ( !isset($cs_node->map_lat) or $cs_node->map_lat == "" ) { $cs_node->map_lat = 0; }

		if ( !isset($cs_node->map_lon) or $cs_node->map_lon == "" ) { $cs_node->map_lon = 0; }

		if ( !isset($cs_node->map_zoom) or $cs_node->map_zoom == "" ) { $cs_node->map_zoom = 11; }

		if ( !isset($cs_node->map_info_width) or $cs_node->map_info_width == "" ) { $cs_node->map_info_width = 200; }

		if ( !isset($cs_node->map_info_height) or $cs_node->map_info_height == "" ) { $cs_node->map_info_height = 100; }

		if ( !isset($cs_node->map_show_marker) or $cs_node->map_show_marker == "" ) { $cs_node->map_show_marker = 'true'; }

		if ( !isset($cs_node->map_controls) or $cs_node->map_controls == "" ) { $cs_node->map_controls = 'false'; }

		if ( !isset($cs_node->map_scrollwheel) or $cs_node->map_scrollwheel == "" ) { $cs_node->map_scrollwheel = 'true'; }

		if ( !isset($cs_node->map_draggable) or $cs_node->map_draggable == "" )  { $cs_node->map_draggable = 'true'; }

		if ( !isset($cs_node->map_type) or $cs_node->map_type == "" ) { $cs_node->map_type = 'ROADMAP'; }

		if ( !isset($cs_node->map_info)) { $cs_node->map_info = ''; }

		if( !isset($cs_node->map_marker_icon)){ $cs_node->map_marker_icon = ''; }

		if( !isset($cs_node->map_title)){ $cs_node->map_title ='';}

		if( !isset($cs_node->map_element_size)){ $cs_node->map_element_size ='default';}

		if( !isset($cs_node->map_height)){ $cs_node->map_height ='180';}

		if ( !isset($cs_node->map_view)) { $cs_node->map_view = ''; }

		if ( !isset($cs_node->map_conactus_content)) { $cs_node->map_conactus_content = ''; }

		$map_show_marker = '';

		if ( $cs_node->map_show_marker == "true" ) { 

			$map_show_marker = " var marker = new google.maps.Marker({

						position: myLatlng,

						map: map,

						title: '',

						icon: '".$cs_node->map_marker_icon."',

						shadow:''

					});

			";

		}

	

		//wp_enqueue_script('googleapis', 'https://maps.googleapis.com/maps/api/js?sensor=true', '', '', true);

		$html = '<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script>';

		$html .= '<div class="element_size_'.$cs_node->map_element_size. ' cs-map-'.$cs_counter_node.'">';

		$html .= '<div class="mapcode iframe mapsection gmapwrapp" id="map_canvas'.$cs_counter_node.'" style="height:'.$cs_node->map_height.'px;"> </div>';

		$html .= '</div>';

		

		$html .= "<script type='text/javascript'>
					jQuery(window).load(function(){
						setTimeout(function(){
						jQuery('.cs-map-".$cs_counter_node."').animate({
							'height':'".$cs_node->map_height."'
						},400)
						},400)
					})
					function initialize() {

						var myLatlng = new google.maps.LatLng(".$cs_node->map_lat.", ".$cs_node->map_lon.");

						var mapOptions = {

							zoom: ".$cs_node->map_zoom.",

							scrollwheel: ".$cs_node->map_scrollwheel.",

							draggable: ".$cs_node->map_draggable.",

							center: myLatlng,

							mapTypeId: google.maps.MapTypeId.".$cs_node->map_type." ,

							disableDefaultUI: ".$cs_node->map_controls.",

						}

						var map = new google.maps.Map(document.getElementById('map_canvas".$cs_counter_node."'), mapOptions);

						var infowindow = new google.maps.InfoWindow({

							content: '".$cs_node->map_info."',

							maxWidth: ".$cs_node->map_info_width.",

							maxHeight:".$cs_node->map_info_height.",

						});

						".$map_show_marker."

						//google.maps.event.addListener(marker, 'click', function() {

	

							if (infowindow.content != ''){

							  infowindow.open(map, marker);

							   map.panBy(1,-60);

							   google.maps.event.addListener(marker, 'click', function(event) {

								infowindow.open(map, marker);

	

							   });

							}

						//});

					}

				

				google.maps.event.addDomListener(window, 'load', initialize);

				</script>";

		return $html;

	}

}
// If no content, include the "No posts found" function
if ( ! function_exists( 'fnc_no_result_found' ) ) {
	function fnc_no_result_found($search = true){
		
		?>
        <div class="pagenone cls-noresult-found">
            <i class="fa fa-warning colr"></i>
            <h1><?php _e( 'No results found.', 'Statfort'); ?></h1>
            <?php if($search == true){?>
                <div class="password_protected">
                    <?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
                    
                    <p><?php printf( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'twentyfourteen' ), admin_url( 'post-new.php' ) ); ?></p>
                    
                    <?php elseif ( is_search() ) : ?>
                    
                    <p><?php _e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'twentyfourteen' ); ?></p>
                    <?php get_search_form(); ?>
                    
                    <?php else : ?>
                         <p><?php _e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'twentyfourteen' ); ?></p>
                    <?php get_search_form(); ?>
                     
                    <?php endif; ?> 
               </div>
             <?php }?>
        </div>

	<?php
	}
}
// news announcement 
if ( ! function_exists( 'fnc_announcement' ) ) {
	function fnc_announcement(){
	?>
	<div class="outer-newsticker">
        <div class="container">
        <?php 
        global $cs_theme_option;
        $blog_category = $cs_theme_option['announcement_blog_category'];
        $announcement_no_posts = $cs_theme_option['announcement_no_posts'];
         if(isset($blog_category) && $blog_category <> '0'){
            if (empty($announcement_no_posts)){ $announcement_no_posts  = 5;}
            $args = array('posts_per_page' => "$announcement_no_posts", 'category_name' => "$blog_category",'post_status' => 'publish');
            $custom_query = new WP_Query($args);
            
            ?>
           
            <div class="announcement-ticker">
                <h5><?php echo $cs_theme_option['announcement_title'];?></h5>
                <?php 
					if($custom_query->have_posts()):
					cs_enqueue_newsticker();
				?>
                <script>
                	jQuery(document).ready(function(){
                	    fn_jsnewsticker('cls-news-ticker',10,80)
                	});
            	</script>
                <div class="ticker-wrapp">
                    <ul class="cls-news-ticker">
                      <?php 
                          while ($custom_query->have_posts()) : $custom_query->the_post();
                      ?>
                          <li>															
                              <a href="<?php the_permalink();?>"><?php the_title();?> - <?php echo get_the_date().' at '.get_the_time(); ?></a>
                          </li>
                         <?php endwhile;?>
                    </ul>
                </div>
                <?php else: 
                   fnc_no_result_found(false);
                  endif; ?>
            </div>
        <?php }?>
            </div>
        </div>
	<?php	
	}
}
// breadcrumb function
if ( ! function_exists( 'cs_breadcrumbs' ) ) { 
	function cs_breadcrumbs() {
		global $wp_query;
		/* === OPTIONS === */
		$text['home']     = 'Home'; // text for the 'Home' link
		$text['category'] = '%s'; // text for a category page
		$text['search']   = '%s'; // text for a search results page
		$text['tag']      = '%s'; // text for a tag page
		$text['author']   = '%s'; // text for an author page
		$text['404']      = 'Error 404'; // text for the 404 page
	
		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$showOnHome  = 1; // 1 - show breadcrumbs on the homepage, 0 - don't show
		$delimiter   = ''; // delimiter between crumbs
		$before      = '<li class="active">'; // tag before the current crumb
		$after       = '</li>'; // tag after the current crumb
		/* === END OF OPTIONS === */
	
		global $post;
		$homeLink = home_url() . '/';
		$linkBefore = '<li>';
		$linkAfter = '</li>';
		$linkAttr = '';
		$link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;
		$linkhome = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;
	
		if (is_home() || is_front_page()) {
	
			if ($showOnHome == "1") echo '<div class="breadcrumbs"><ul>'.$before.'<a href="' . $homeLink . '">' . $text['home'] . '</a>'.$after.'</ul></div>';
	
		} else {
			echo '<div class="breadcrumbs"><ul>' . sprintf($linkhome, $homeLink, $text['home']) . $delimiter;
			if ( is_category() ) {
				$thisCat = get_category(get_query_var('cat'), false);
				if ($thisCat->parent != 0) {
					$cats = get_category_parents($thisCat->parent, TRUE, $delimiter);
					$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
					$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
					echo $cats;
				}
				echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;
			} elseif ( is_search() ) {
				echo $before . sprintf($text['search'], get_search_query()) . $after;
			} elseif ( is_day() ) {
				echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
				echo sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;
				echo $before . get_the_time('d') . $after;
			} elseif ( is_month() ) {
				echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;
				echo $before . get_the_time('F') . $after;
			} elseif ( is_year() ) {
				echo $before . get_the_time('Y') . $after;
			} elseif ( is_single() && !is_attachment() ) {
				if ( get_post_type() != 'post' ) {
					
					$post_type = get_post_type_object(get_post_type());
					$slug = $post_type->rewrite;
					
					printf($link, $homeLink  . $slug['slug'] . '/', $post_type->labels->singular_name);
					if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
				} else {
					$cat = get_the_category(); $cat = $cat[0];
					$cats = get_category_parents($cat, TRUE, $delimiter);
					if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);
					$cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);
					$cats = str_replace('</a>', '</a>' . $linkAfter, $cats);
					echo $cats;
					if ($showCurrent == 1) echo $before . substr(get_the_title(),0,35) . $after;
				}
			} elseif ( !is_single() && !is_page() && get_post_type() <> '' && get_post_type() != 'post' && get_post_type() <> 'events' && get_post_type() <> 'portfolio' && get_post_type() <> 'sermon' && !is_404() ) {
					$post_type = get_post_type_object(get_post_type());
					echo $before . $post_type->labels->singular_name . $after;
			} elseif (isset($wp_query->query_vars['taxonomy']) && !empty($wp_query->query_vars['taxonomy'])){
				$taxonomy = $taxonomy_category = '';
				$taxonomy = $wp_query->query_vars['taxonomy'];
				echo $before . $wp_query->query_vars[$taxonomy] . $after;

			}elseif ( is_page() && !$post->post_parent ) {
				if ($showCurrent == 1) echo $before . get_the_title() . $after;
	
			} elseif ( is_page() && $post->post_parent ) {
				$parent_id  = $post->post_parent;
				$breadcrumbs = array();
				while ($parent_id) {
					$page = get_page($parent_id);
					$breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));
					$parent_id  = $page->post_parent;
				}
				$breadcrumbs = array_reverse($breadcrumbs);
				for ($i = 0; $i < count($breadcrumbs); $i++) {
					echo $breadcrumbs[$i];
					if ($i != count($breadcrumbs)-1) echo $delimiter;
				}
				if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;
	
			} elseif ( is_tag() ) {
				echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;
	
			} elseif ( is_author() ) {
				global $author;
				$userdata = get_userdata($author);
				echo $before . sprintf($text['author'], $userdata->display_name) . $after;
	
			} elseif ( is_404() ) {
				echo $before . $text['404'] . $after;
			}
			
			//echo "<pre>"; print_r($wp_query->query_vars); echo "</pre>";
			if ( get_query_var('paged') ) {
				// if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
				// echo __('Page') . ' ' . get_query_var('paged');
				// if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
			}
			echo '</ul></div>';
	
		}
	}
} 
// show breadcrums in header banner
if ( ! function_exists( 'cs_header_breadcrums' ) ) {
	function cs_header_breadcrums(){
		global $cs_theme_option;
		if(isset($cs_theme_option['show_beadcrumbs']) and $cs_theme_option['show_beadcrumbs'] == "on"){
			cs_breadcrumbs();
		}
	}
}
// author description custom function
if ( ! function_exists( 'cs_author_description' ) ) {
	function cs_author_description() {
		global $cs_theme_option;
		?>
		<div class="about-author">
            <figure>
            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
				<?php echo get_avatar(get_the_author_meta('user_email'), apply_filters('PixFill_author_bio_avatar_size', 50)); ?></a>
            </figure>
            <div class="text">
                <h2><?php echo get_the_author(); ?></h2>
                <p><?php the_author_meta('description'); ?></p>
                <?php if(get_the_author_meta('twitter') <> ''){?><a class="follow-tweet" href="http://twitter.com/<?php the_author_meta('twitter'); ?>"><i class="fa fa-twitter"></i>@<?php the_author_meta('twitter'); ?></a><?php }?>
            </div>
        </div>
		<?php
	}
}


// Google Fonts array
if ( ! function_exists( 'cs_get_google_fonts' ) ) {
	function cs_get_google_fonts() {
		$fonts = array("Abel", "Aclonica", "Acme", "Actor", "Advent Pro", "Aldrich", "Allerta", "Allerta Stencil", "Amaranth", "Andika", "Anonymous Pro", "Antic", "Anton", "Arimo", "Armata", "Asap", "Asul",
			"Basic", "Belleza", "Cabin", "Cabin Condensed", "Cagliostro", "Candal", "Cantarell", "Carme", "Chau Philomene One", "Chivo", "Coda Caption", "Comfortaa", "Convergence", "Cousine", "Cuprum", "Days One",
			"Didact Gothic", "Doppio One", "Dorsa", "Dosis", "Droid Sans", "Droid Sans Mono", "Duru Sans", "Economica", "Electrolize", "Exo", "Federo", "Francois One", "Fresca", "Galdeano", "Geo", "Gudea",
			"Hammersmith One", "Homenaje", "Imprima", "Inconsolata", "Inder", "Istok Web", "Jockey One", "Josefin Sans", "Jura", "Karla", "Krona One", "Lato", "Lekton", "Magra", "Mako", "Marmelad", "Marvel",
			"Maven Pro", "Metrophobic", "Michroma", "Molengo", "Montserrat", "Muli", "News Cycle", "Nobile", "Numans", "Nunito", "Open Sans", "Open Sans Condensed", "Orbitron", "Oswald", "Oxygen", "PT Mono",
			"PT Sans", "PT Sans Caption", "PT Sans Narrow", "Paytone One", "Philosopher", "Play", "Pontano Sans", "Port Lligat Sans", "Puritan", "Quantico", "Quattrocento Sans", "Questrial", "Quicksand", "Rationale",
			"Ropa Sans", "Rosario", "Ruda", "Ruluko", "Russo One", "Shanti", "Sigmar One", "Signika", "Signika Negative", "Six Caps", "Snippet", "Spinnaker", "Syncopate", "Telex", "Tenor Sans", "Ubuntu",
			"Ubuntu Condensed", "Ubuntu Mono", "Varela", "Varela Round", "Viga", "Voltaire", "Wire One", "Yanone Kaffeesatz", "Adamina", "Alegreya", "Alegreya SC", "Alice", "Alike", "Alike Angular", "Almendra",
			"Almendra SC", "Amethysta", "Andada", "Antic Didone", "Antic Slab", "Arapey", "Artifika", "Arvo", "Average", "Balthazar", "Belgrano", "Bentham", "Bevan", "Bitter", "Brawler", "Bree Serif", "Buenard",
			"Cambo", "Cantata One", "Cardo", "Caudex", "Copse", "Coustard", "Crete Round", "Crimson Text", "Cutive", "Della Respira", "Droid Serif", "EB Garamond", "Enriqueta", "Esteban", "Fanwood Text", "Fjord One",
			"Gentium Basic", "Gentium Book Basic", "Glegoo", "Goudy Bookletter 1911", "Habibi", "Holtwood One SC", "IM Fell DW Pica", "IM Fell DW Pica SC", "IM Fell Double Pica", "IM Fell Double Pica SC",
			"IM Fell English", "IM Fell English SC", "IM Fell French Canon", "IM Fell French Canon SC", "IM Fell Great Primer", "IM Fell Great Primer SC", "Inika", "Italiana", "Josefin Slab", "Judson", "Junge",
			"Kameron", "Kotta One", "Kreon", "Ledger", "Linden Hill", "Lora", "Lusitana", "Lustria", "Marko One", "Mate", "Mate SC", "Merriweather", "Montaga", "Neuton", "Noticia Text", "Old Standard TT", "Ovo",
			"PT Serif", "PT Serif Caption", "Petrona", "Playfair Display", "Podkova", "Poly", "Port Lligat Slab", "Prata", "Prociono", "Quattrocento", "Radley", "Rokkitt", "Rosarivo", "Simonetta", "Sorts Mill Goudy",
			"Stoke", "Tienne", "Tinos", "Trocchi", "Trykker", "Ultra", "Unna", "Vidaloka", "Volkhov", "Vollkorn", "Abril Fatface", "Aguafina Script", "Aladin", "Alex Brush", "Alfa Slab One", "Allan", "Allura",
			"Amatic SC", "Annie Use Your Telescope", "Arbutus", "Architects Daughter", "Arizonia", "Asset", "Astloch", "Atomic Age", "Aubrey", "Audiowide", "Averia Gruesa Libre", "Averia Libre", "Averia Sans Libre",
			"Averia Serif Libre", "Bad Script", "Bangers", "Baumans", "Berkshire Swash", "Bigshot One", "Bilbo", "Bilbo Swash Caps", "Black Ops One", "Bonbon", "Boogaloo", "Bowlby One", "Bowlby One SC",
			"Bubblegum Sans", "Buda", "Butcherman", "Butterfly Kids", "Cabin Sketch", "Caesar Dressing", "Calligraffitti", "Carter One", "Cedarville Cursive", "Ceviche One", "Changa One", "Chango", "Chelsea Market",
			"Cherry Cream Soda", "Chewy", "Chicle", "Coda", "Codystar", "Coming Soon", "Concert One", "Condiment", "Contrail One", "Cookie", "Corben", "Covered By Your Grace", "Crafty Girls", "Creepster", "Crushed",
			"Damion", "Dancing Script", "Dawning of a New Day", "Delius", "Delius Swash Caps", "Delius Unicase", "Devonshire", "Diplomata", "Diplomata SC", "Dr Sugiyama", "Dynalight", "Eater", "Emblema One",
			"Emilys Candy", "Engagement", "Erica One", "Euphoria Script", "Ewert", "Expletus Sans", "Fascinate", "Fascinate Inline", "Federant", "Felipa", "Flamenco", "Flavors", "Fondamento", "Fontdiner Swanky",
			"Forum", "Fredericka the Great", "Fredoka One", "Frijole", "Fugaz One", "Geostar", "Geostar Fill", "Germania One", "Give You Glory", "Glass Antiqua", "Gloria Hallelujah", "Goblin One", "Gochi Hand",
			"Gorditas", "Graduate", "Gravitas One", "Great Vibes", "Gruppo", "Handlee", "Happy Monkey", "Henny Penny", "Herr Von Muellerhoff", "Homemade Apple", "Iceberg", "Iceland", "Indie Flower", "Irish Grover",
			"Italianno", "Jim Nightshade", "Jolly Lodger", "Julee", "Just Another Hand", "Just Me Again Down Here", "Kaushan Script", "Kelly Slab", "Kenia", "Knewave", "Kranky", "Kristi", "La Belle Aurore",
			"Lancelot", "League Script", "Leckerli One", "Lemon", "Lilita One", "Limelight", "Lobster", "Lobster Two", "Londrina Outline", "Londrina Shadow", "Londrina Sketch", "Londrina Solid",
			"Love Ya Like A Sister", "Loved by the King", "Lovers Quarrel", "Luckiest Guy", "Macondo", "Macondo Swash Caps", "Maiden Orange", "Marck Script", "Meddon", "MedievalSharp", "Medula One", "Megrim",
			"Merienda One", "Metamorphous", "Miltonian", "Miltonian Tattoo", "Miniver", "Miss Fajardose", "Modern Antiqua", "Monofett", "Monoton", "Monsieur La Doulaise", "Montez", "Mountains of Christmas",
			"Mr Bedfort", "Mr Dafoe", "Mr De Haviland", "Mrs Saint Delafield", "Mrs Sheppards", "Mystery Quest", "Neucha", "Niconne", "Nixie One", "Norican", "Nosifer", "Nothing You Could Do", "Nova Cut",
			"Nova Flat", "Nova Mono", "Nova Oval", "Nova Round", "Nova Script", "Nova Slim", "Nova Square", "Oldenburg", "Oleo Script", "Original Surfer", "Over the Rainbow", "Overlock", "Overlock SC", "Pacifico",
			"Parisienne", "Passero One", "Passion One", "Patrick Hand", "Patua One", "Permanent Marker", "Piedra", "Pinyon Script", "Plaster", "Playball", "Poiret One", "Poller One", "Pompiere", "Press Start 2P",
			"Princess Sofia", "Prosto One", "Qwigley", "Raleway", "Rammetto One", "Rancho", "Redressed", "Reenie Beanie", "Revalia", "Ribeye", "Ribeye Marrow", "Righteous", "Rochester", "Rock Salt", "Rouge Script",
			"Ruge Boogie", "Ruslan Display", "Ruthie", "Sail", "Salsa", "Sancreek", "Sansita One", "Sarina", "Satisfy", "Schoolbell", "Seaweed Script", "Sevillana", "Shadows Into Light", "Shadows Into Light Two",
			"Share", "Shojumaru", "Short Stack", "Sirin Stencil", "Slackey", "Smokum", "Smythe", "Sniglet", "Sofia", "Sonsie One", "Special Elite", "Spicy Rice", "Spirax", "Squada One", "Stardos Stencil",
			"Stint Ultra Condensed", "Stint Ultra Expanded", "Sue Ellen Francisco", "Sunshiney", "Supermercado One", "Swanky and Moo Moo", "Tangerine", "The Girl Next Door", "Titan One", "Trade Winds", "Trochut",
			"Tulpen One", "Uncial Antiqua", "UnifrakturCook", "UnifrakturMaguntia", "Unkempt", "Unlock", "VT323", "Vast Shadow", "Vibur", "Voces", "Waiting for the Sunrise", "Wallpoet", "Walter Turncoat",
			"Wellfleet", "Yellowtail", "Yeseva One", "Yesteryear", "Zeyada");
		return $fonts;
	}
}
// adding font start

function cs_font_head() {
	$cs_fonts = get_option('cs_theme_option');
		
 		if ( isset($cs_fonts['content_size']) ) echo '<style> body{ font-size:'.$cs_fonts['content_size'].'px !important; } </style>';
		if ( isset($cs_fonts['content_size_g_font']) and $cs_fonts['content_size_g_font'] <> "" ) {
			echo '<style>';
				echo "@import url(https://fonts.googleapis.com/css?family=".$cs_fonts['content_size_g_font'].");";
				echo "body { font-family: '".$cs_fonts['content_size_g_font']."', sans-serif !important; }";
			echo '</style>';
		}
 	}
	add_action( 'wp_head', 'cs_font_head' );
	
// import demo xml file
function cs_demo_importer(){
	?>
    <div class="cs-demo-data">
        <h2>Import Demo Data</h2>
        <div class="inn-text">
            <p>Importing demo data helps to build site like the demo site by all means. It is the quickest way to setup theme. Following things happen when dummy data is imported;</p>
            <ul class="import-data">
                <li>&#8226; All wordpress settings will remain same and intact.</li>
                <li>&#8226; Posts, pages and dummy images shown in demo will be imported.</li>
                <li>&#8226; Only dummy images will be imported as all demo images have copy right restriction.</li>
                <li>&#8226; No existing posts, pages, categories, custom post types or any other data will be deleted or modified.</li>
                <li>&#8226; To proceed, please click "Import Demo Data" and wait for a while.</li>
            </ul>
        </div>
        <form method="post">
            <input name="reset"  type="submit" value="Import Demo Data" id="submit_btn"/>
            <input type="hidden" name="demo" value="demo-data" />
        </form>
       </div>
   <?php
	if(isset($_REQUEST['demo']) && $_REQUEST['demo']=='demo-data'){
			
		require_once ABSPATH . 'wp-admin/includes/import.php';
 		if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);
		$cs_demoimport_error = false;
		
		if ( !class_exists( 'WP_Importer' ) ) {
				$cs_import_class = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				if ( file_exists( $cs_import_class ) ){
					require_once($cs_import_class);
				}
				else{
					$cs_demoimport_error = true;
				}
			}
		if ( !class_exists( 'WP_Import' ) ) {
			$cs_import_class = get_template_directory() . '/include/importer/wordpress-importer.php';
			if ( file_exists( $cs_import_class ) )
				require_once($cs_import_class);
			else
				$cs_demoimport_error = true;
		}
		
		if($cs_demoimport_error){
 			echo __( 'Error.', 'wordpress-importer' ) . '</p>';
			die();
		}else{
 			if(!is_file( get_template_directory() . '/include/importer/demo.xml')){
				echo '<p><strong>' . __( 'Sorry, there has been an error.', 'wordpress-importer' ) . '</strong><br />';
				echo __( 'The file does not exist, please try again.', 'wordpress-importer' ) . '</p>';
			}
			else{
				
				global $wpdb;
				$theme_mod_val = array();
				$term_exists = term_exists('top-menu', 'nav_menu');
				if ( !$term_exists ) {
					$wpdb->query(" INSERT INTO `" . $wpdb->prefix . "terms` VALUES ('', 'Top Menu' , 'top-menu', '0'); ");
					$insert_id = $wpdb->insert_id;
					$theme_mod_val['top-menu'] = $insert_id;
					$wpdb->query(" INSERT INTO `" . $wpdb->prefix . "term_taxonomy` VALUES ('', '".$insert_id."' , 'nav_menu', '', '0', '0'); ");
				}
				else $theme_mod_val['top-menu'] = $term_exists['term_id'];
				$term_exists = term_exists('main-menu', 'nav_menu');
				if ( !$term_exists ) {
					$wpdb->query(" INSERT INTO `" . $wpdb->prefix . "terms` VALUES ('', 'Main Menu' , 'main-menu', '0'); ");
					$insert_id = $wpdb->insert_id;
					$theme_mod_val['main-menu'] = $insert_id;
					$wpdb->query(" INSERT INTO `" . $wpdb->prefix . "term_taxonomy` VALUES ('', '".$insert_id."' , 'nav_menu', '', '0', '0'); ");
				}
				else $theme_mod_val['main-menu'] = $term_exists['term_id'];
				$term_exists = term_exists('footer-menu', 'nav_menu');
				if ( !$term_exists ) {
					$wpdb->query(" INSERT INTO `" . $wpdb->prefix . "terms` VALUES ('', 'Footer Menu' , 'footer-menu', '0'); ");
					$insert_id = $wpdb->insert_id;
					$theme_mod_val['footer-menu'] = $insert_id;
					$wpdb->query(" INSERT INTO `" . $wpdb->prefix . "term_taxonomy` VALUES ('', '".$insert_id."' , 'nav_menu', '', '0', '0'); ");
				}
				else $theme_mod_val['footer-menu'] = $term_exists['term_id'];
				set_theme_mod( 'nav_menu_locations', $theme_mod_val );
				$cs_demo_import = new WP_Import();
				$cs_demo_import->fetch_attachments = true;
				$cs_demo_import->import( get_template_directory() . '/include/importer/demo.xml');
				
				// Menu Location
				/*
				$cs_theme_option = get_option('cs_theme_option');
				$cs_theme_option['show_slider']='on';
				$cs_theme_option['slider_type']='Flex Slider';
				$cs_theme_option['slider_name']='6352';
				$cs_theme_option['announcement_title']='Announcement';
				$cs_theme_option['announcement_blog_category']='aidreform';
				$cs_theme_option['announcement_no_posts']='5';
				$cs_theme_option['partners_title']='Our Partners';
				$cs_theme_option['partners_gallery']='our-clients';
				$cs_theme_option['show_partners']='home';
				$cs_theme_option['layout_option']='wrapper_boxed';
				update_option("cs_theme_option", $cs_theme_option );
				*/
				update_option( 'cs_import_data', 'success' );
				
				$home = get_page_by_title( 'Home' );
				if($home <> '' && get_option( 'page_on_front' ) == "0"){
					update_option( 'page_on_front', $home->ID );
					update_option( 'show_on_front', 'page' );
					update_option( 'front_page_settings', '1' );
				}
				
		  	}
		}
   }
}

	if (is_admin() && isset($_GET['activated']) && $pagenow == 'themes.php'){
 		
 	}
