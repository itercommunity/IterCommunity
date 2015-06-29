<?php

	// Include WordPress
	require('../../../../../wp-load.php');
	define('WP_USE_THEMES', false);

	// Include Request Functionality
	require('rss.request.php');
	
	// Content prep
	$icp_xml = false;
	
	// Download (item key)
	if (isset($_GET['download']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['download']))
		$icp_item_key = trim(urldecode($_GET['download']));
	else
		$icp_xml = "No item key provided.";
	
	// Api User ID
	if (isset($_GET['api_user_id']) && preg_match("/^[a-zA-Z0-9]+$/", $_GET['api_user_id']))
		$icp_api_user_id = trim(urldecode($_GET['api_user_id']));
	else
		$icp_xml = "No API User ID provided.";
	
	// GET KEY FROM DB
	
	if ($icp_xml === false)
	{
		// Access WordPress db
		global $wpdb;
		
		$icp_download_url_query = "SELECT ".$wpdb->prefix."icpress.public_key, ".$wpdb->prefix."icpress_zoteroItems.citation
				FROM ".$wpdb->prefix."icpress
				JOIN ".$wpdb->prefix."icpress_zoteroItems ON ".$wpdb->prefix."icpress.api_user_id = ".$wpdb->prefix."icpress_zoteroItems.api_user_id
				WHERE ".$wpdb->prefix."icpress_zoteroItems.item_key='".$icp_item_key."' 
				AND ".$wpdb->prefix."icpress_zoteroItems.api_user_id='".$icp_api_user_id."';";
		
		$icp_download_url = $wpdb->get_results( $icp_download_url_query, OBJECT );
		
		if (count($icp_download_url) > 0)
		{
			header("Location: ".$icp_download_url[0]->citation."/file?key=".$icp_download_url[0]->public_key);
			exit;
		}
		else {
			$icp_xml = "No file to download found.";
		}
	}
	else {
		echo $icp_xml;
	}
?>