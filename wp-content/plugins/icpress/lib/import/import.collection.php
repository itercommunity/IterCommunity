<?php

// Include WordPress
require('../../../../../wp-load.php');
define('WP_USE_THEMES', false);

// Prevent access to users who are not editors
if ( !current_user_can('edit_others_posts') && !is_admin() )
	wp_die( __('Only editors can access this page.'), __('ICPress: Access Denied'), array( 'response' => '403' ) );

// Check api user id
$api_user_id = false;
if ($_GET['api_user_id'] != "")
	if (preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1) $api_user_id = htmlentities($_GET['api_user_id']);
	else wp_die( __('Only editors can access this page.'), __('ICPress: Access Denied'), array( 'response' => '403' ) );
else
	wp_die( __('Only editors can access this page.'), __('ICPress: Access Denied'), array( 'response' => '403' ) );

// Check nonce
if ( check_admin_referer( 'icp_importing_' . intval($api_user_id) . '_' . date('Y-j-G'), 'icp_nonce' ) )
{
	// Access WordPress db
	global $wpdb;
	
	// Ignore user abort
	ignore_user_abort(true);
	set_time_limit(60); // 1 minute (vs. 60*10)
	
	// Include Request Functionality
	require("../request/rss.request.php");
	
	// Include Import Functions
	require("import.functions.php");
	
	$GLOBALS['icp_session'][$api_user_id]['collections']['last_set'] = 0;
	$GLOBALS['icp_session'][$api_user_id]['collections']['query_params'] = array();
	$GLOBALS['icp_session'][$api_user_id]['collections']['query_total_entries'] = 0;
	
	$icp_current_set = 0;
	
	while ( $icp_current_set <= $GLOBALS['icp_session'][$api_user_id]['collections']['last_set'] )
	{
		$icp_continue = icp_get_collections ($wpdb, $api_user_id, $icp_current_set, true);
		//icp_save_collections ($wpdb, $api_user_id, true, true); // saving now might be confusing to users because it doesn't import items or subcollections
		$icp_current_set += 50;
	}
	
	$output = "<!DOCTYPE HTML>\n<html>\n<head>";
	$output .= '<script type="text/javascript" src="'. ICPRESS_PLUGIN_URL .'js/jquery-1.5.2.min.js"></script>';
	$output .= '<script>
	
	jQuery(document).ready(function() {
		
		jQuery("input").click( function()
		{
			jQuery(this).parent().toggleClass("selected");
		});
		
		jQuery(".icp-Collection").click( function()
		{
			jQuery(this).toggleClass("selected");
			
			if ( jQuery("input", this).is("[checked]") )
				jQuery("input", this).removeAttr("checked");
			else
				jQuery("input", this).attr("checked","checked");
		});
		
	});
	
	</script>';
	$output .= "<style>
	
	body { font: normal 13px/13px 'Arial', sans-serif; }
	
	.icp-Collection { background: #f9f9f9 url('".ICPRESS_PLUGIN_URL."images/sprite.png') no-repeat 12px -642px; border-bottom: 2px solid #fff; padding: 12px; color: #555; padding-left: 42px; cursor: pointer; font-family: 'Open Sans', Helvetica, Arial, sans-serif; }
	.icp-Collection.selected { background: #6D798B url('".ICPRESS_PLUGIN_URL."images/sprite.png') no-repeat -466px -642px; color: #fff; }
	.icp-Collection .title { float: left; width: 49%; }
	.icp-Collection .meta { float: right; width: 49%; font-size: 11px; text-align: right; }
	p.error { margin: 0; padding: 1em; font-family: 'Open Sans', Helvetica, Arial, sans-serif; }
	
	/* Thanks to http://nicolasgallagher.com/micro-clearfix-hack/ */
	
	.icp-Collection:before,
	.icp-Collection:after {
		content: \"\";
		display: table;
	}
	.icp-Collection:after {
		clear: both;
	}
	
	.icp-Collection {
		*zoom: 1;
	}
	
	</style>\n";
	$output .= "</head><body>\n\n";
	
	// Display
	if ( $GLOBALS['icp_session'][$api_user_id]['collections']['query_total_entries'] > 0 )
	{
		$output .= "<div class='icp-Collection-List'>\n";
		for ($i = 0; $i <= ($GLOBALS['icp_session'][$api_user_id]['collections']['query_total_entries'] - 1); $i++ )
		{
			$mod = $i * 7;
			
			$output .= "<div class='icp-Collection' rel='" . $GLOBALS['icp_session'][$api_user_id]['collections']['query_params'][4+$mod] . "'>";
			$output .= "<span class='title'>" . $GLOBALS['icp_session'][$api_user_id]['collections']['query_params'][1+$mod] . "</span>";
			$output .= "<span class='meta'>" . $GLOBALS['icp_session'][$api_user_id]['collections']['query_params'][6+$mod] . " items, ";
			$output .= $GLOBALS['icp_session'][$api_user_id]['collections']['query_params'][5+$mod] . " subcollections</span>";
			$output .= "</div>\n";
		}
		$output .= "</div>\n\n";
	}
	else // No collections exist
	{
		$output .= "<p class='error'>Sorry, no collections to display.</p>";
	}
	
	$output .= "</body>\n</html>";
	
	echo $output;
	
	// Unset
	$GLOBALS['icp_session'][$api_user_id]['collections']['query_params'] = array();
	$GLOBALS['icp_session'][$api_user_id]['collections']['query_total_entries'] = 0;
	
} // nonce

?>