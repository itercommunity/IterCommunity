<?php

/**** CALLS ****/
/* FUNCTION FILTER CALLS */

// add_filter( 'posts_where', 'sboxr_imgsearch_posts_where', 10, 2 );

/* FUNCTION OPTION CALLS */

/*
if (isset($default_sboxr_general_options)) {
	add_option('sboxr_xxx_options', $default_sboxr_xxx_options);
}
*/

/* FUNCTION ACTION CALLS */

add_action('admin_menu', 'add_sboxr_options_menu', 3);
add_action('admin_init', 'register_and_build_fields');

add_action('add_meta_boxes', 'sboxr_meta_boxes');
add_action('save_post', 'sboxr_meta_all_update');

/* FUNCTION SHORTCODE CALLS */

// add_shortcode('sboxrone', 'sboxrone_shortcode');


/**** FUNCTIONS ****/
/* FUNCTION INIT FUNCTIONS */

/* naming convention exception */
function sandboxer_init() {
    $sboxr_options['general'] = get_option('sboxr_options_general');
    $sboxr_options['modules'] = get_option('sboxr_options_modules');
    $sboxr_options['docs'] = get_option('sboxr_options_docs');
    $sboxr_options['support'] = get_option('sboxr_options_support');
}

/**
 * Adds sandboxer options
 * @return null
 */
function sboxr_admin_init(){
    register_setting( 'sboxr_general_options', 'sboxr_general_options' );
    register_setting( 'sboxr_about_options', 'sboxr_about_options' );
}

/* FUNCTION FILTER FUNCTIONS */

function sboxr_filter_x() {

}

/* FUNCTION OPTION FUNCTIONS */

function sboxr_option_x() {

}

/*
The modules editor
*/

function sboxr_general_form_submit() {
	if (isset($_REQUEST['action'])) {
		// let's do this!
		switch ($_REQUEST['action']) {
			case 'edit':
				sboxr_general_edit($_REQUEST);
			break;
			case 'update':
				// TBD
			break;
			case 'delete':
				// TBD
			break;			
		}
	}
}

// the bias is to get the arg
function sboxr_either_arg($key, $args) {
	if (isset($args[$key])) {
		return $args[$key];
	}
	else {
		$o = get_option($key);
		if (isset($o)) {
			return $o;
		}
		else {
			return "";
		}
	}
}

// the bias is to get the option
function sboxr_either_option($key, $args) {
	$o = get_option($key);
	if (isset($o)) {
		return $o;
	}
	else {
		if (isset($args[$key])) {
			return $args[$key];
		}
		else {
			return "";
		}
	}
}


function sboxr_general_edit($args) {	
	$module_list = sboxr_modules_discover();	
	foreach ($args as $key => $value) {
		$form_value_array = explode("_", $key);
		if (in_array($form_value_array[0], $module_list)) {
			$module = array_shift($form_value_array);
			call_user_func('sboxr_'.$module.'_put_variables', array('key' => $key, 'value' => $value));
			
		}
	}
}


function sboxr_modules_form_submit() {
	if (isset($_REQUEST['action'])) {
		// let's do this!
		switch ($_REQUEST['action']) {
			case 'edit':
				sboxr_modules_edit();
			break;
			case 'update':
				// TBD
			break;
			case 'delete':
				// TBD
			break;			
		}
	}
}

function sboxr_modules_edit() {	
	// mail("mikedewolfe@gmail.com", "test - ".__LINE__, print_r($_POST, TRUE));
	sboxr_modules_update($_POST);
}



/* FUNCTION ACTION FUNCTIONS */

function sboxr_action_x() {

}

/* FUNCTION SHORTCODE FUNCTIONS */

function sboxr_shortcode_x() {

}

/* FUNCTION CONTENT TYPE FUNCTIONS */


// Register Custom Post Type
function custom_sboxr_project() {

	$labels = array(
		'name'                => _x( 'IC Projects', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'IC Project', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'IC Project', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Project:', 'text_domain' ),
		'all_items'           => __( 'All Projects', 'text_domain' ),
		'view_item'           => __( 'View Project', 'text_domain' ),
		'add_new_item'        => __( 'Add New Project', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'edit_item'           => __( 'Edit Project', 'text_domain' ),
		'update_item'         => __( 'Update Project', 'text_domain' ),
		'search_items'        => __( 'Search Project', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$capabilities = array(
		'edit_post'           => 'edit_post',
		'read_post'           => 'read_post',
		'delete_post'         => 'delete_post',
		'edit_posts'          => 'edit_posts',
		'edit_others_posts'   => 'edit_others_posts',
		'publish_posts'       => 'publish_posts',
		'read_private_posts'  => 'read_private_posts',
	);
	$args = array(
		'label'               => __( 'sboxr_project', 'text_domain' ),
		'description'         => __( 'Iter Commons projects. ', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'page-attributes', ),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     =>  'post',	
		// 'capabilities'        => $capabilities,
	);
	register_post_type( 'sboxr_project', $args );

}

// Hook into the 'init' action
add_action( 'init', 'custom_sboxr_project', 0 );

/*

Meta box information

*/

function sboxr_meta_boxes() {
	global $post;
	add_meta_box( 'sboxr_dimensions', __( 'Project Details', 'sandboxer' ), 'sboxr_meta_box_settings', 'sboxr_project', 'side', 'high');
}

/**
 * Display the dimensions meta box.
 *
 * @access publickey
 * @param mixed $post
 * @return void
 */
function sboxr_meta_box_settings( $post ) {
	global $wpdb;
	/*
	
	Adding in:
	- project path 
	- project name
	- status
	- username
	- password (set-- not preserved)
	
	*/
	
	$sboxr_name = get_post_meta($post->ID, 'sboxr_name', TRUE); 
	$sboxr_path = get_post_meta($post->ID, 'sboxr_path', TRUE); 
	$sboxr_status = get_post_meta($post->ID, 'sboxr_status', TRUE); 
	$sboxr_user = get_post_meta($post->ID, 'sboxr_user', TRUE); 
	$sboxr_module = get_post_meta($post->ID, 'sboxr_module', TRUE); 
		
	$module_list = sboxr_modules_discover();
	$module_status = sboxr_modules_get();
	
	foreach ($module_list as $key => $module) {
		if ((array_key_exists($key, $module_status)) && ($module_status[$key]['status'] == 1)) {
			$modules[$key]['module'] = $key;				
			$modules[$key]['title'] = $module['title'];
			$modules[$key]['description'] = $module['description'];			
		}
	}
				
	$statuses = array('Under Construction','Public','Discontinued');
	
	?>	
	<strong>Installation Options</strong>: <?php print $sources[@$sboxr_source]['url']; ?><br/>
	<ul class="modules submitbox">
		<?php
		
		if ($post->post_status == 'auto-draft') { ?>
		<input type="hidden" name="sboxr_setup" value="0" />		
		<li class="wide" id="sboxr_name">Project Name: <input type="text" name="sboxr_name" value="<?php echo esc_attr($sboxr_name); ?>" /></li>			
		<li class="wide" id="sboxr_path">Project Path: <input type="text" name="sboxr_path" value="<?php echo esc_attr($sboxr_path); ?>" /></li>			
		<li class="wide" id="sboxr_module">
		<select name="sboxr_module">
			<option value=""><?php _e( 'Installation Profile', 'sandboxer' ); ?></option>
				<?						
				if ( ! empty( $modules ) ) {
					foreach ( $modules as $k => $v ) {
						echo '<option value="'. esc_attr( $k ) .'"'.($k == $sboxr_module ? " selected" : "").'>' . esc_html( $v['title'] ) . '</option>';
					}
				}
				?>
			</select>
		</li>
		<li class="wide" id="sboxr_username">Username: <input type="text" name="sboxr_username" value="<?php echo esc_attr($sboxr_username); ?>" /></li>
		<li class="wide" id="sboxr_password">Password: <input type="text" name="sboxr_password" /><br/><small>* Passwords are not retrained. They are only used for the project space installation.</small></li>		
			<?php } else { ?>
		<!-- set and cannot be changed -->
		<input type="hidden" name="sboxr_setup" value="1" />
		<li class="wide" id="sboxr_name">Project Name: <?php echo $sboxr_name; ?></li>			
		<li class="wide" id="sboxr_path">Project Path: <a href="http://www.itercom.org/<?php echo $sboxr_path; ?>">http://www.itercom.org/<?php echo $sboxr_path; ?></a></li>			
		<li class="wide" id="sboxr_module">Installation Profile: <?php echo $modules[@$sboxr_module]['title']; ?></li>
		<li class="wide" id="sboxr_username">Admin Username: <?php echo $sboxr_user; ?></li>
			
		<?php } ?>		
		<li class="wide" id="sboxr_status">Status: <select name="sboxr_status">
			<option value=""><?php _e( 'Project Status', 'sandboxer' ); ?></option>
				<?php					
				if ( ! empty( $statuses ) ) {
					foreach ( $statuses as $k => $v ) {
						echo '<option value="'. esc_attr( $k ) .'"'.($k == $sboxr_status ? " selected" : "").'>' . esc_html( $v ) . '</option>';
					}
				}
				?>
		</select></li>
	</ul>	
	<?php
}

function sboxr_meta_all_update($post_id) {
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;
        
        
    if ('sboxr_project' == @$_POST['post_type']) {
		if ( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		
		if (!empty($_POST['sboxr_name'])) {
			add_post_meta($post_id, 'sboxr_name', $_POST['sboxr_name']);
		}
		if (($_POST['sboxr_setup'] != 1)) {		
			if (!empty($_POST['sboxr_path'])) {
				// we need a path... one that's good
				if (sboxr_dir_status($_POST['sboxr_path'])) {
					$errors['sboxr_path'] = "The path is currently not available.";
				}
				else {
					// false, so the directory is available
					add_post_meta($post_id, 'sboxr_path', $_POST['sboxr_path']);		
				}
			}
			else {
				$errors['sboxr_path'] = "The path is currently empty.";
				return;
			}
			if (!empty($_POST['sboxr_module'])) {
				add_post_meta($post_id, 'sboxr_module', $_POST['sboxr_module']);
			}		
			if (!empty($_POST['sboxr_username'])) {
				add_post_meta($post_id, 'sboxr_username', $_POST['sboxr_username']);
			}
			if (!empty($_POST['sboxr_password'])) {		
				// something in here
			}	
		}
		if (intval($_POST['sboxr_status']) > -1) {
			if (!update_post_meta($post_id, 'sboxr_status', $_POST['sboxr_status'])) {
				add_post_meta($post_id, 'sboxr_status', $_POST['sboxr_status']);
			}
		}
		// do the step that spawns the module to build the project site.
		
		if (!isset($errors['sboxr_path'])) {
			$modules = sboxr_modules_discover();
			if ($modules[$_POST['sboxr_module']]) {
				// load the file
				
				require_once($modules[$_POST['sboxr_module']]['location']);
		
				// pass in arguments
		
				$function_call = "sboxr_".$_POST['sboxr_module']."_make_subsite";
				call_user_func($function_call, $_POST);
				add_post_meta($post_id, 'sboxr_setup', $_POST['sboxr_setup']);
			}
		}
	}
	return $errors;
}


/* FUNCTION ADMIN FUNCTIONS */

/**
 * Adds admin menu page(s)
 * @return null
 */
function sboxr_admin_menu() {
	global $sboxr_tabs;
	foreach ($sboxr_tabs as $slug => $value) {
		if ($value['type'] == 'menu') {
			// print "MENU - <br/><pre>".print_r($value, TRUE)."</pre>";
			add_menu_page($value['long_name'], $value['short_name'], $value['access'], $value['slug'], $value['function'], sboxr_get_asset('icon32.png'), $value['position']);	
		}
	}

	foreach ($sboxr_tabs as $slug => $value) {
		if ($value['type'] == 'submenu') {
			// print "SUB-MENU - <br/><pre>".print_r($value, TRUE)."</pre>";
			add_submenu_page($value['parent'], $value['long_name'], $value['short_name'], $value['access'], $value['slug'], $value['function']);
		}
	}
}

// the header common for all use cases of admin screens
function sboxr_options_admin_head() { 
$output = <<<EOD
<style type="text/css">
.container {width: 95%; margin: 10px 0px; font-family: "Lucida Grande", Verdana, Arial, "Bitstream Vera Sans", sans-serif;}
ul.tabs {margin: 0;padding: 0;float: left;list-style: none;height: 25px;border-bottom: 1px solid #e3e3e3;border-left: 1px solid #e3e3e3;width: 100%;}
ul.tabs li {float: left;margin: 0;padding: 0;	height: 24px;line-height: 24px;border: 1px solid #e3e3e3;border-left: none;margin-bottom: -1px;background:#EBEBEB;overflow: hidden;position: relative; background-repeat:repeat-x;}
ul.tabs li a {text-decoration: none;color: #21759b;display: block;font-size: 12px;padding: 0 20px;border: 1px solid #fff;outline: none;}
ul.tabs li a:hover {color: #d54e21;}
html ul.tabs li.active, html ul.tabs li.active a:hover  {background: #fff;border-bottom: 1px solid #fff;}
.tab_container {border: 1px solid #e3e3e3;border-top: none;clear: both;float: left; width: 100%;background: #fff;font-size:11px;}
.tab_content {padding: 20px;font-size: 1.2em;}
.tab_content h3 {margin-top:0px;margin-bottom:10px;}
.tab_content .head-description{font-style:italic;}
.tab_content .description{padding-left:15px}
.tab_content ul li{list-style:square outside; margin-left:20px}

a.delete_source { background-color: red; color: white; padding: 6px; -webkit-border-radius: 4px; border-radius: 4px; -webkit-box-shadow:  1px 2px 3px 3px rgba(90, 80, 80, 0.6); text-decoration: none; box-shadow:  1px 2px 3px 3px rgba(90, 80, 80, 0.6); border-bottom: #aa0000 1px solid; border-right: #bb0000 1px solid; }
a.add_source { background-color: #44aa44; color: white; padding: 6px; -webkit-border-radius: 4px; border-radius: 4px; -webkit-box-shadow:  1px 2px 3px 3px rgba(70, 90, 80, 0.6); text-decoration: none; box-shadow:  1px 2px 3px 3px rgba(70, 90, 80, 0.6); border-bottom: #00aa00 1px solid; border-right: #00bb00 1px solid; }

.zebra-1 td { background-color: #eeeeee; }
.zebra-0 td, .zebra-2 td { background-color: #ffffff; }
</style>
<script type="text/javascript">
jQuery(document).ready(function() {
	//Default Action
	// jQuery(".tab_content").hide(); //Hide all content
	// jQuery("ul.tabs li:first").addClass("active").show(); //Activate first tab
	// jQuery(".tab_content:first").show(); //Show first tab content
	//On Click Event
	jQuery("ul.tabs li").click(function() {
			// jQuery(".tab_content").hide(); //Hide all tab content
			var activeTab = jQuery(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
			jQuery(activeTab).show();
			return false;
	});
});
</script>
EOD;
	$output .= '<script type="text/javascript" src="'.SBOXR_URL.'sboxr-admin/add-remove.js"></script>';

	return $output;
}

function sboxr_top_element($current_tab = 'sandboxer-general') {
	global $sboxr_tabs;

	$output = '<div class="container"><ul class="tabs">';
	foreach ($sboxr_tabs as $slug => $value) {
		if ($value['type'] == 'menu') {
			$output .= '<li class="nav-tab '.(($current_tab == $slug) ? 'active' : '').'"><a href="'.site_url('/wp-admin/admin.php').'?page='.$value['slug'].'">'.translate($value['short_name'], 'sandboxer').'</a></li>';
		}
		if ($value['type'] == 'submenu') {
			$output .= '<li class="nav-tab '.(($current_tab == $slug) ? 'active' : '').'"><a href="'.site_url('/wp-admin/admin.php').'?page='.$value['slug'].'">'.translate($value['short_name'], 'sandboxer').'</a></li>';
		}
		if ($value['type'] == 'option') {
			$output .= '<li class="nav-tab '.(($current_tab == $slug) ? 'active' : '').'"><a href="'.site_url('/wp-admin/options-general.php').'?page='.$value['slug'].'">'.translate($value['short_name'], 'sandboxer').'</a></li>';		
		}
	}
	$output .= '</ul><div class="tab_container"><div id="tab1" class="tab_content">';
	return $output;
}

function sboxr_beg_element($current_tab = 'sandboxer-general') {
	global $sboxr_options;
	$output = '<div class="beg" style="clear: both; margin-top: 20px; margin-bottom; 40px;">';
	$status = get_option( 'sboxr_license_status' );

$output .= <<<EOD
<div class="postbox" id="about-box" style="width: 30% !important; min-height: 290px; float: left; margin: 10px 0 10px 10px; padding: 10px;">
<h3 class="hndle"><span>About Sandboxer</span></h3>
<div class="inside">
<p><b>Version 
EOD;

$output .= $sboxr_options['release_version'];

$output .= <<<EOD
</b><br>
<small>Release Date:  
EOD;

$output .= $sboxr_options['release_date'];

$output .= <<<EOD
</small><br/>
<small>Sandboxer Home Page:
EOD;

$output .= '<a href="'.$sboxr_options['home_page'].'">'.$sboxr_options['home_page'].'</a>';

$output .= <<<EOD
</small></p>

<p><b>Minimum Requirements</b><br />
<small>WordPress 3.0</small><br />
<small>PHP 5.2.6</small><br />
<small>MySQL 5.0.45</small><br /></p>

<p><small>Developed and maintained by 
EOD;

$output .= $sboxr_options['author'];

$output .= <<<EOD
<br /><a href="http://www.itercom.org" target="_blank">http://www.itercom.org</a></small></p>
</div>
</div>
EOD;
	$output .= "</div>";
	return $output;
}


function sboxr_btm_element($current_tab = 'sandboxer-general') {
	global $sboxr_options;
	$output = "</div></div>";
	$output .= '<div style="width: 200px; position: absolute; right: 10px; bottom: 30px;">Version '.$sboxr_options['release_version'].' - '.$sboxr_options['release_date'].'</div>';
	return $output;
}


function sboxr_general_page() {
	// add in the interactivity call
	sboxr_general_form_submit();
	print sboxr_options_admin_head();
	print sboxr_top_element('sandboxer-general');
	require_once(WP_PLUGIN_DIR.'/sandboxer/sandboxer-admin/general_tab.php');
	print sboxr_btm_element('sandboxer-general');
}

function sboxr_docs_page() {
	print sboxr_options_admin_head();
	print sboxr_top_element('sandboxer-docs');
	require_once(WP_PLUGIN_DIR.'/sandboxer/sandboxer-admin/docs_tab.php');
	print sboxr_beg_element('sandboxer-docs');
	print sboxr_btm_element('sandboxer-docs');
}

function sboxr_support_page() {
	print sboxr_options_admin_head();
	print sboxr_top_element('sandboxer-support');
	require_once(WP_PLUGIN_DIR.'/sandboxer/sandboxer-admin/support_tab.php');
	print sboxr_beg_element('sandboxer-support');
	print sboxr_btm_element('sandboxer-support');
}

function sboxr_modules_page($msg = "") {
	// add in the interactivity call
	// mail("mikedewolfe@gmail.com", "test - ".__LINE__, print_r($_POST, TRUE));
	sboxr_modules_form_submit();
	print sboxr_options_admin_head();
	print sboxr_top_element('sandboxer-modules');
	print $msg;
	require_once(WP_PLUGIN_DIR.'/sandboxer/sandboxer-admin/modules_tab.php');
	print sboxr_btm_element('sandboxer-modules');
}

/* LICENSING FUNCTIONALITY */

function sboxr_license_options() {
	// listen for our activate button to be clicked
	// run a quick security check 

	$msg = "";
	if ( isset($_POST['sboxr_license_key']) ) {
		if ( !isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'],'sboxr_nonce') )
			return; // get out if we didn't click the Activate button

		$options['sboxr_license_key'] = $_POST['sboxr_license_key'];
		update_option('sboxr_license_key', $options['sboxr_license_key']);

		$check = sboxr_check_license($options['sboxr_license_key']);
		$result_text = 'The license key is not valid';

		if ($check == 'valid') {
			update_option( 'sboxr_license_status', $check );
			$result_text = 'The license key was validated';		
		}
		$msg = '<div style="color: #22cc22; border: 1px solid #bbbbbb; padding: 10px; margin: 10px;">'.$result_text.'</div>';
	}
	// load the license key
	sboxr_license_page($msg);
}

function add_sboxr_options_menu() {
	global $sboxr_tabs;
	foreach ($sboxr_tabs as $slug => $value) {
		if ($value['type'] == 'option') {
			add_options_page($value['long_name'], $value['short_name'], $value['access'], $value['slug'], $value['function']);
		}
	}
}

function sboxr_register_option() {
	// creates our settings in the options table
	register_setting('sboxr_license_key', 'sboxr_license_status', 'sboxr_sanitize_license' );
}
 
function sboxr_sanitize_license( $new ) {
	$old = get_option( 'sboxr_license_key' );
	if( $old && $old != $new ) {
		delete_option( 'sboxr_license_status' ); // new license has been entered, so must reactivate
	}
	return $new;
}


/* MODULE FUNCTIONALITY : START */

/* modular import step */

function sboxr_modules_discover() {
	$return_modules = array(); // the empty array of modules that have been discovered
	$dir = SBOXR_PATH."sandboxer-includes/modules";
	$modules = scandir($dir, 1);
	foreach ($modules as $module) {
		// check for file
		$file = implode("/", array($dir, $module, "index.php")); 
		if ($modfile = file_get_contents($file)) {
			// if yes, parse for 
			// Plugin Module: Drupal
			// Description: This plugin creates subsites for Drupal
			
			if ((preg_match("/Plugin Module: ([^\n]+)/", $modfile, $title)) && (preg_match("/Description: ([^\n]+)/", $modfile, $description))) {
				// what has been found
				// print " title \n".print_r($title, TRUE);
				// print "\n description \n".print_r($description, TRUE);
				
				$return_modules[$module] = array('title' => $title[1], 'description' => $description[1], 'location' => $file);
			}
		}		
	}
	return $return_modules;
}

// What has been selected? What has not been selected?
function sboxr_modules_get() {
	// returns an array of key => status = > 1
	return get_option('sboxr_module_status');
}

// Take the form values and update the module list of selected options
function sboxr_modules_update($input) {
	for ($index = 1; $index <= $input['sboxr_module_index']; $index++) {
		$module_status[$input['module_'.$index]]['status'] = 1;	
	}
	// mail("mikedewolfe@gmail.com", "test - ".__LINE__, print_r($input, TRUE)." begets ".$module_status);
	update_option('sboxr_module_status', $module_status);
}


/* MODULE FUNCTIONALITY : END */


/* modular project creation functions */ 

function sboxr_defang($phrase) {
	$from = array('-','"', "'", '  ');
	$to = array(' ', " ", " ", " ");
	$output = str_replace($from, $to, $phrase);
	return $output;
}

function sboxr_copy_table($from, $to) {
    if(sboxr_table_exists($to)) {
        $success = false;
    }
    else {
        mysql_query("CREATE TABLE $to LIKE $from");
        // mysql_query("INSERT INTO $to SELECT * FROM $from");
        $success = true;
    }
   
    return $success;
   
}

function sboxr_table_exists($tablename, $database = false) {

    if(!$database) {
        $res = mysql_query("SELECT DATABASE()");
        $database = mysql_result($res, 0);
    }
   
    $res = mysql_query("
        SELECT COUNT(*) AS count
        FROM information_schema.tables
        WHERE table_schema = '$database'
        AND table_name = '$tablename'
    ");
   
    return mysql_result($res, 0) == 1;
}


function sboxr_check_license($license) {
	return 'valid';
}


/* FUNCTION ETC/SPECIFIC FUNCTIONS */


/* FILE MANAGEMENT : START */


function sboxr_dir_status($dir_raw, $relative = FALSE) {
	if ($relative === FALSE) {
		$dir_check = $dir_raw;
	}
	else {
		$dir_check = SBOXR_PATH."/".str_replace("..", ".", sanitize_file_name($dir_raw));
	}
	// returns a true or false 
	return file_exists($dir_check);
}

function sboxr_get_asset($file, $return = 'url') {
	$output = "";
	if ($asset = get_option('sboxr_'.$file)) {		
		if ($return == 'url') {
			$output = site_url('/wp-content/uploads/').$asset['data']['file'];
		}
	}
	return $output;
}

function sboxr_upload_attach_image($url, $post_id) {
	if ( ! function_exists( 'wp_handle_upload' ) ) 
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$upload_dir = wp_upload_dir();

	if ($uploadedfile = @file_get_contents($url)) {
		if (strpos($url,"?")) {
			list($url, $toss) = explode("?", $url);
		}
		$tmp_dir = $upload_dir['path'];
		$tmp = $tmp_dir."/".md5(basename($url));

		$fpc = file_put_contents($tmp, $uploadedfile);

		if ($fpc) {
			$file_info = stat($tmp);
			$file_info['tmp_name'] = $tmp;
			$file_info['name'] = basename($url);
			
			$upload_overrides = array('test_size' => false, 'test_upload' => false, 'test_form' => false);
			$filename = sboxr_handle_upload($file_info, $upload_overrides);

			if ($filename['file']) {
				// exit;
				$wp_filetype = wp_check_filetype(basename($filename['file']), null);
				$wp_upload_dir = wp_upload_dir();
				$attachment = array(
					'guid' => $wp_upload_dir['url'] . '/' . basename($filename['file']), 
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => preg_replace('/\.[^.]+$/', '', basename($filename['file'])),
					'post_content' => '',
					'post_status' => 'inherit'
				);
				$attach_id = wp_insert_attachment( $attachment, $filename['file'], $post_id );
				// you must first include the image.php file
				// for the function wp_generate_attachment_metadata() to work
				require_once(ABSPATH . 'wp-admin/includes/image.php');
				$attach_data = wp_generate_attachment_metadata( $attach_id, $filename['file']);
				wp_update_attachment_metadata( $attach_id, $attach_data );
				return array('id' => $attach_id, 'data' => $attach_data);
			}
		} else {
			// echo "\n".__LINE__." - could not upload and store $url !\n";
		}
	}
	return false;
}

function sboxr_handle_upload(&$file, $overrides = false, $time = null) {
	list($name, $toss) = explode(".", $file['name']);
	$uploaded_args = array(
		'post_type' => 'attachment',
		'posts_per_page' => -1,
		'sboxr_imgsearch_title' => $name,
		'post_parent' => null, // any parent
		); 

	$the_query = new WP_Query( $uploaded_args );

	if ( $the_query->have_posts() ) {
		while ( $the_query->have_posts() ) {
			$the_query->the_post();
			$post_id = $the_query->post->ID;

			// the minimum of what we need
			$img = wp_get_attachment_metadata($post_id);
			$output = array(
				'data' => array (
					'file' => $img['file'],
					'sizes' => array (
						'thumbnail' => array (
							'file' => $img['file'],
							'width' => $img['width'],
							'height' => $img['height'],
						)
					)						
				)	
			);
			wp_reset_postdata();
			return $output;
		}
	}
	/* Restore original Post Data */
	wp_reset_postdata();

	// The default error handler.
	if ( ! function_exists( 'wp_handle_upload_error' ) ) {
		function wp_handle_upload_error( &$file, $message ) {
			return array( 'error'=>$message );
		}
	}

	$file = apply_filters( 'wp_handle_upload_prefilter', $file );
	// You may define your own function and pass the name in $overrides['upload_error_handler']
	$upload_error_handler = 'wp_handle_upload_error';

	// You may have had one or more 'wp_handle_upload_prefilter' functions error out the file. Handle that gracefully.
	if ( isset( $file['error'] ) && !is_numeric( $file['error'] ) && $file['error'] )
		return $upload_error_handler( $file, $file['error'] );

	// You may define your own function and pass the name in $overrides['unique_filename_callback']
	$unique_filename_callback = null;

	// $_POST['action'] must be set and its value must equal $overrides['action'] or this:
	$action = 'wp_handle_upload';

	// Courtesy of php.net, the strings that describe the error indicated in $_FILES[{form field}]['error'].
	$upload_error_strings = array( false,
		__( "The uploaded file exceeds the upload_max_filesize directive in php.ini." ),
		__( "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form." ),
		__( "The uploaded file was only partially uploaded." ),
		__( "No file was uploaded." ),
		'',
		__( "Missing a temporary folder." ),
		__( "Failed to write file to disk." ),
		__( "File upload stopped by extension." ));

	// All tests are on by default. Most can be turned off by $overrides[{test_name}] = false;
	$test_form = true;
	$test_size = true;
	$test_upload = true;

	// If you override this, you must provide $ext and $type!!!!
	$test_type = true;
	$mimes = false;

	// Install user overrides. Did we mention that this voids your warranty?
	if ( is_array( $overrides ) )
		extract( $overrides, EXTR_OVERWRITE );

	// A correct form post will pass this test.
	if ( $test_form && (!isset( $_POST['action'] ) || ($_POST['action'] != $action ) ) )
		return call_user_func($upload_error_handler, $file, __( 'Invalid form submission.' ));

	// A successful upload will pass this test. It makes no sense to override this one.
	if ( $file['error'] > 0 )
		return call_user_func($upload_error_handler, $file, $upload_error_strings[$file['error']] );

	// A non-empty file will pass this test.
	if ( $test_size && !($file['size'] > 0 ) ) {
		if ( is_multisite() )
			$error_msg = __( 'File is empty. Please upload something more substantial.' );
		else
			$error_msg = __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.' );
		return call_user_func($upload_error_handler, $file, $error_msg);
	}

	// A properly uploaded file will pass this test. There should be no reason to override this one.
	if ( $test_upload && ! @ is_uploaded_file( $file['tmp_name'] ) )
		return call_user_func($upload_error_handler, $file, __( 'Specified file failed upload test.' ));

	// A correct MIME type will pass this test. Override $mimes or use the upload_mimes filter.
	if ( $test_type ) {
		$wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file['name'], $mimes );

		extract( $wp_filetype );

		// Check to see if wp_check_filetype_and_ext() determined the filename was incorrect
		if ( $proper_filename )
			$file['name'] = $proper_filename;

		if ( ( !$type || !$ext ) && !current_user_can( 'unfiltered_upload' ) )
			return call_user_func($upload_error_handler, $file, __( 'Sorry, this file type is not permitted for security reasons.' ));

		if ( !$ext )
			$ext = ltrim(strrchr($file['name'], '.'), '.');

		if ( !$type )
			$type = $file['type'];
	} else {
		$type = '';
	}

	// A writable uploads dir will pass this test. Again, there's no point overriding this one.
	if ( ! ( ( $uploads = wp_upload_dir($time) ) && false === $uploads['error'] ) )
		return call_user_func($upload_error_handler, $file, $uploads['error'] );

	$filename = wp_unique_filename( $uploads['path'], $file['name'], $unique_filename_callback );

	// Move the file to the uploads dir
	$new_file = $uploads['path'] . "/$filename";
	if ( false === @rename( $file['tmp_name'], $new_file ) ) {
		if ( 0 === strpos( $uploads['basedir'], ABSPATH ) )
			$error_path = str_replace( ABSPATH, '', $uploads['basedir'] ) . $uploads['subdir'];
		else
			$error_path = basename( $uploads['basedir'] ) . $uploads['subdir'];

		return $upload_error_handler( $file, sprintf( __('The uploaded file could not be moved to %s.' ), $error_path ) );
	}

	// Set correct file permissions
	$stat = stat( dirname( $new_file ));
	$perms = $stat['mode'] & 0000666;
	@ chmod( $new_file, $perms );

	// Compute the URL
	$url = $uploads['url'] . "/$filename";

	if ( is_multisite() )
		delete_transient( 'dirsize_cache' );

	return apply_filters( 'wp_handle_upload', array( 'file' => $new_file, 'url' => $url, 'type' => $type ), 'upload' );
}

/* FILE MANAGEMENT : END */



/* CLASSES : BEGIN */

/* form validator */
/*

methods:
- valid_path(text) -- looking for illegal characters
- reserved_path(text) -- scan a dir for taken names 
- text size(text, min, max) -- make sure input fits in a range
- is_empty(text) -- check for emptiness

*/

class Validator {
	var $error_msg = ""; // should be public or different to allow use inside of methods
	function valid_path($str) {
		$result = true;
		$reserved_paths = scandir(ABSPATH);		
		foreach ($reserved_paths as $reserved_path) {
			if (strtolower($str) == strtolower($reserved_path)) {
				$result = false;
				break;	
			}		
		}
		if (sanitize_file_name($str) != $str) {
			// something bad was added.
			$result = false;
			break;	
		}						
		return $result;
	}

	// 20141126 removed function reserved_path($str) 
	// handled by valid path	

	function text_size($str, $min = 0, $max = 4096) {
		$result = true;
		if ((strlen($str) > $max) || (strlen($str) < $min)) {
			$result = false;		
		}
		return $result;
	}
	
	// 20141126 removed function is_empty($str)
	// to use empty() instead
		
	function show_error() {
		echo '<div class="error"><p>Error Found!!</p></div>';
	}

	//update option when admin_notices is needed or not
	function update_option($val) {
		update_option('display_my_admin_message', $val);
	}

	//function to use for your admin notice
	function add_plugin_notice() {
		if (get_option('display_my_admin_message') == 1) { 
			// check whether to display the message
			add_action('admin_notices', array(&$this, 'show_error'));
			// turn off the message
			update_option('display_my_admin_message', 0); 
		}
	}
}



/* CLASSES : END   */
