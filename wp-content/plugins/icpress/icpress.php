<?php

/*
 
    Plugin Name: ICPress
    Plugin URI: http://www.itercom.org/
    Description: Converting ZotPress with features for Iter Commons
    Author: Shawn DeWolfe from work by Katie Seaborn
    Version: 1.0.0
    Author URI: http://shawn.dewolfe.bc.ca from http://katieseaborn.com
    
*/

/*
 
    Copyright 2015 Shawn DeWolfe for the ETCL (http://etcl.uvic.ca/)
    
    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at
    
        http://www.apache.org/licenses/LICENSE-2.0
    
    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
    
*/



// GLOBAL VARS ----------------------------------------------------------------------------------
    
    define('ICPRESS_PLUGIN_FILE',  __FILE__ );
    define('ICPRESS_PLUGIN_URL', plugin_dir_url( ICPRESS_PLUGIN_FILE ));
    define('ICPRESS_PLUGIN_DIR', dirname( __FILE__ ));
    define('ICPRESS_EXPERIMENTAL_EDITOR', FALSE); // Whether experimental editor feature is active or not
    define('ICPRESS_VERSION', '5.4' );
    
    $GLOBALS['icp_is_shortcode_displayed'] = false;
    $GLOBALS['icp_shortcode_instances'] = array();
    
    $GLOBALS['ICPress_update_db_by_version'] = "1.0.0"; // Only change this if the db needs updating - 1.0.0

// GLOBAL VARS ----------------------------------------------------------------------------------
    


// INSTALL -----------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/admin/admin.install.php' );

// INSTALL -----------------------------------------------------------------------------------------



// ADMIN -------------------------------------------------------------------------------------------
    
    include( dirname(__FILE__) . '/lib/admin/admin.php' );

// END ADMIN --------------------------------------------------------------------------------------



// SHORTCODE -------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/shortcode/shortcode.php' );
    include( dirname(__FILE__) . '/lib/shortcode/shortcode.intext.php' );
    include( dirname(__FILE__) . '/lib/shortcode/shortcode.intextbib.php' );
    include( dirname(__FILE__) . '/lib/shortcode/shortcode.lib.php' );
    
// SHORTCODE -------------------------------------------------------------------------------------



// SIDEBAR WIDGET -------------------------------------------------------------------------------
    
    include( dirname(__FILE__) . '/lib/widget/widget.sidebar.php' );

// SIDEBAR WIDGET -------------------------------------------------------------------------------



// META BOX WIDGET -----------------------------------------------------------------------------
    
    function ICPress_add_meta_box()
    {
        $icp_default_cpt = "post,page";
        if (get_option("ICPress_DefaultCPT"))
            $icp_default_cpt = get_option("ICPress_DefaultCPT");
        $icp_default_cpt = explode(",",$icp_default_cpt);
        
        foreach ($icp_default_cpt as $post_type )
        {
            add_meta_box( 
                'ICPressMetaBox',
                __( 'ICPress Reference', 'ICPress_textdomain' ),
                'ICPress_show_meta_box',
                $post_type,
                'side'
            );
        }
    }
    add_action('admin_init', 'ICPress_add_meta_box', 1); // backwards compatible
    
    function ICPress_show_meta_box()
    {
        require( dirname(__FILE__) . '/lib/widget/widget.metabox.php');
    }
    
// META BOX WIDGET ---------------------------------------------------------------------------------



// REGISTER ACTIONS ---------------------------------------------------------------------------------
    
    /**
    * Admin scripts and styles
    */

    function ICPress_admin_scripts_css($hook)
    {
        wp_enqueue_script( 'jquery');
        wp_enqueue_media();
        wp_enqueue_script( 'jquery.dotimeout.min.js', ICPRESS_PLUGIN_URL . 'js/jquery.dotimeout.min.js', array( 'jquery' ) );
        wp_enqueue_script( 'icpress.default.js', ICPRESS_PLUGIN_URL . 'js/icpress.default.js', array( 'jquery' ) );
        
        if ( in_array( $hook, array('post.php', 'post-new.php') ) === true )
        {
            wp_enqueue_script( 'jquery.livequery.js', ICPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-position', 'jquery-ui-tabs', 'jquery-ui-autocomplete' ) );
            wp_enqueue_script( 'icpress.widget.metabox.js', ICPRESS_PLUGIN_URL . 'js/icpress.widget.metabox.js', array( 'jquery' ) );
        }
        else
        {
            wp_enqueue_script( 'jquery.livequery.js', ICPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array( 'jquery' ) );
        }
        
        if ( isset($_GET['accounts']) || isset($_GET['setup']) || isset($_GET['import']) || isset($_GET['selective']) )
        {
            wp_register_script('icpress.accounts.js', ICPRESS_PLUGIN_URL . 'js/icpress.accounts.js', array('jquery','media-upload','thickbox'));
            wp_enqueue_script('icpress.accounts.js');
        }
        
        wp_enqueue_style( 'icpress.css', ICPRESS_PLUGIN_URL . 'css/icpress.css' );
        wp_enqueue_style( 'ICPressGoogleFonts.css', 'http://fonts.googleapis.com/css?family=Source+Sans+Pro:300,600|Droid+Serif:400,400italic,700italic|Oswald:300,400' );
    }
    add_action( 'admin_enqueue_scripts', 'ICPress_admin_scripts_css' );
    
    
    /**
    * Add ICPress to admin menu
    */
    function ICPress_admin_menu()
    {
        add_menu_page( "ICPress", "ICPress", "edit_posts", "ICPress", "ICPress_options", ICPRESS_PLUGIN_URL."images/icon.png" );
		add_submenu_page( "ICPress", "Browse", "Browse", "edit_posts", "ICPress" );
		add_submenu_page( "ICPress", "Accounts", "Accounts", "edit_posts", "admin.php?page=ICPress&accounts=true" );
		add_submenu_page( "ICPress", "Options", "Options", "edit_posts", "admin.php?page=ICPress&options=true" );
		add_submenu_page( "ICPress", "Help", "Help", "edit_posts", "admin.php?page=ICPress&help=true" );
    }
    add_action( 'admin_menu', 'ICPress_admin_menu' );
	
	function ICPress_admin_menu_submenu($parent_file)
	{
		global $submenu_file;
		
		if ( isset($_GET['accounts']) || isset($_GET['selective'])  || isset($_GET['import']) ) $submenu_file = 'admin.php?page=ICPress&accounts=true';
		if ( isset($_GET['options']) ) $submenu_file = 'admin.php?page=ICPress&options=true';
		if ( isset($_GET['help']) ) $submenu_file = 'admin.php?page=ICPress&help=true';
		
		return $parent_file;
	}
	add_filter('parent_file', 'ICPress_admin_menu_submenu');
    
    
    /**
    * Add shortcode styles to user's theme
    * Note that this always displays: There's no way to conditionally include it,
    * because the existence of shortcodes is checked after CSS is included.
    */
    function ICPress_theme_includes()
    {
        wp_register_style('icpress.shortcode.css', ICPRESS_PLUGIN_URL . 'css/icpress.shortcode.css');
        wp_enqueue_style('icpress.shortcode.css');
    }
    add_action('wp_print_styles', 'ICPress_theme_includes');
    
    
    /**
    * Change HTTP request timeout
    */
    function ICPress_change_timeout($time)
    {
        return 60; // seconds
    }
    add_filter('http_request_timeout', 'ICPress_change_timeout');
    
    
    /**
    * TinyMCE word-processor-like features
    */
    function icpress_tinymce_buttonhooks()
    {
        // Determine default editor features status
        $icp_default_editor = "editor_enable";
        if (get_option("ICPress_DefaultEditor")) $icp_default_editor = get_option("ICPress_DefaultEditor");
        
        if ( ( 'post.php' != $hook || 'page.php' != $hook ) && $icp_default_editor != 'editor_enable' )
            return;
        
        // Only add hooks when the current user has permissions AND is in Rich Text editor mode
        if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') )
        {
            add_filter("mce_external_plugins", "icpress_register_tinymce_javascript");
            add_filter("mce_buttons", "icpress_register_tinymce_buttons");
        }
    }
   if ( ICPRESS_EXPERIMENTAL_EDITOR ) add_action('init', 'icpress_tinymce_buttonhooks');
    
    // Load the TinyMCE plugin : editor_plugin.js (wp2.5)
    function icpress_register_tinymce_javascript($plugin_array)
    {
        $plugin_array['icpress'] = plugins_url('/lib/tinymce-plugin/icpress-tinymce-plugin.js', __FILE__);
        return $plugin_array;
    }
    
    function icpress_register_tinymce_buttons($buttons)
    {
        array_push($buttons, "icpress-cite", "icpress-list", "icpress-bib" );
        return $buttons;
    }
   
   
    /**
    * Metabox styles
    */
    function ICPress_admin_post_styles()
    {
        wp_register_style('icpress.metabox.css', ICPRESS_PLUGIN_URL . 'css/icpress.metabox.css');
        wp_enqueue_style('icpress.metabox.css');
        
        wp_enqueue_style('jquery-ui-tabs', ICPRESS_PLUGIN_URL . 'css/smoothness/jquery-ui-1.8.11.custom.css');
    }
    add_action('admin_print_styles-post.php', 'ICPress_admin_post_styles');
    add_action('admin_print_styles-post-new.php', 'ICPress_admin_post_styles');
    
    
    // CKEDITOR SCRIPTS & STYLES
    // In progress and experimental
    
    //function ICPress_admin_editor_scripts()
    //{
    //    //wp_register_script('icpress.widget.ckeditor.js', ICPRESS_PLUGIN_URL . 'js/icpress.widget.ckeditor.js', array('jquery'));
    //    //wp_enqueue_script('icpress.widget.ckeditor.js');
    //}
    
    //function ICPress_admin_ckeditor_css()
    //{
    //    wp_register_style('icpress.ckeditor.css', ICPRESS_PLUGIN_URL . 'css/icpress.ckeditor.css');
    //    wp_enqueue_style('icpress.ckeditor.css');
    //}
    
    
    // Enqueue jQuery in theme if it isn't already enqueued
    // Thanks to WordPress user "eceleste"
    function ICPress_enqueue_scripts()
    {
        if (!isset( $GLOBALS['wp_scripts']->registered[ "jquery" ] )) wp_enqueue_script("jquery");
    }
    add_action( 'wp_enqueue_scripts' , 'ICPress_enqueue_scripts' );

    // Add shortcodes and sidebar widget
    add_shortcode( 'icpress', 'ICPress_func' );
    add_shortcode( 'icpressInText', 'ICPress_icpressInText' );
    add_shortcode( 'icpressInTextBib', 'ICPress_icpressInTextBib' );
    add_shortcode( 'icpressLib', 'ICPress_icpressLib' );
    add_action( 'widgets_init', 'ICPressSidebarWidgetInit' );
    
    // Conditionally serve shortcode scripts
    function ICPress_theme_conditional_scripts_footer()
    {
        if ( $GLOBALS['icp_is_shortcode_displayed'] === true )
        {
            if ( !is_admin() ) wp_enqueue_script('jquery');
            wp_register_script('jquery.livequery.js', ICPRESS_PLUGIN_URL . 'js/jquery.livequery.js', array('jquery'));
            wp_enqueue_script('jquery.livequery.js');
			
			wp_enqueue_script("jquery-effects-core");
			wp_enqueue_script("jquery-effects-highlight");
            
            wp_register_script('icpress.frontend.js', ICPRESS_PLUGIN_URL . 'js/icpress.frontend.js', array('jquery'));
            wp_enqueue_script('icpress.frontend.js');
        }
    }
    add_action('wp_footer', 'ICPress_theme_conditional_scripts_footer');
    
    
	
    // 5.2 - Notice of required re-import
    // Thanks to http://wptheming.com/2011/08/admin-notices-in-wordpress/
    
    function icpress_5_2_admin_notice()
    {
        global $wpdb;
        global $current_user;
        
        // See if any accounts are the old version
        $temp_version_count =
                $wpdb->get_var( "SELECT COUNT(version) FROM ".$wpdb->prefix."icpress
                                            WHERE version != '".$GLOBALS['ICPress_update_db_by_version']."';" );
        
        if ( $temp_version_count > 0
                && !get_user_meta($current_user->ID, 'icpress_5_2_ignore_notice')
                && ( current_user_can('edit_posts') || current_user_can('edit_pages') )
                && ( !isset($_GET['setup']) && !isset($_GET['selective']) && !isset($_GET['import']) )
            )
        {
            echo '<div class="error"><p>';
            printf(__('<strong>URGENT:</strong> Due to major changes in ICPress, your Zotero account(s) need to be <a href="admin.php?page=ICPress&accounts=true">re-imported</a>. | <a href="%1$s">Hide Notice</a>'), 'admin.php?page=ICPress&icpress_5_2_ignore=0');
            echo "</p></div>";
        }
    }
    add_action( 'admin_notices', 'icpress_5_2_admin_notice' );
    
    function icpress_5_2_ignore()
    {
        global $current_user;
        if ( isset($_GET['icpress_5_2_ignore']) && $_GET['icpress_5_2_ignore'] == '0' )
            add_user_meta($current_user->ID, 'icpress_5_2_ignore_notice', 'true', true);
    }
    add_action('admin_init', 'icpress_5_2_ignore');
	
// REGISTER ACTIONS ---------------------------------------------------------------------------------


// IMPORT -----------------------------------------------------------------------------------------

    include( dirname(__FILE__) . '/lib/import/import.actions.php' );
	
	function icp_nonce_message ($translation)
	{
		if ( $translation == 'Are you sure you want to do this?' )
			return 'Access denied: You cannot access this ICPress page.';
		else
			return $translation;
	}
	add_filter('gettext', 'icp_nonce_message');

// IMPORT -----------------------------------------------------------------------------------------



// EDIT KEYWORDS ----------------------------------------------------------------------------------

add_action('wp_ajax_nopriv_icp_editItem', 'icp_editItem');
add_action('wp_ajax_icp_editItem', 'icp_editItem');

if (($_REQUEST['action'] == 'icp_editItem') || ($_POST['action'] == 'icp_editItem')) {
	do_action( 'wp_ajax_nopriv_' . $_REQUEST['action'] );
	do_action( 'wp_ajax_' . $_POST['action'] );
}

// EDIT KEYWORDS ----------------------------------------------------------------------------------


?>
