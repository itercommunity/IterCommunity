<?php


    require("shortcode.classes.php");
    
    
    function ICPress_icpressLib($atts)
    {
        extract(shortcode_atts(array(
            
            'user_id' => false, // deprecated
            'userid' => false,
            'nickname' => false,
            'nick' => false,
            
        ), $atts, "icpress"));
        
        
        // FORMAT PARAMETERS
        
        // Filter by account
        if ($user_id) $api_user_id = str_replace('"','',html_entity_decode($user_id));
        else if ($userid) $api_user_id = str_replace('"','',html_entity_decode($userid));
        else $api_user_id = false;
        
        if ($nickname) $nickname = str_replace('"','',html_entity_decode($nickname));
        if ($nick) $nickname = str_replace('"','',html_entity_decode($nick));
		
		
		// Get API User ID
		
		global $wpdb;
		
        if ($nickname !== false)
        {
            $icp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress WHERE nickname='".$nickname."'", OBJECT);
			
			if ( is_null($icp_account) ): echo "<p>Sorry, but the selected ICPress nickname can't be found.</p>"; return false; endif;
			
            $api_user_id = $icp_account->api_user_id;
        }
        else if ($api_user_id !== false)
        {
            $icp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress WHERE api_user_id='".$api_user_id."'", OBJECT);
			
			if ( is_null($icp_account) ): echo "<p>Sorry, but the selected ICPress account can't be found.</p>"; return false; endif;
			
            $api_user_id = $icp_account->api_user_id;
        }
        else if ($api_user_id === false && $nickname === false)
        {
            if (get_option("ICPress_DefaultAccount") !== false)
            {
                $api_user_id = get_option("ICPress_DefaultAccount");
                $icp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress WHERE api_user_id ='".$api_user_id."'", OBJECT);
            }
            else // When all else fails ...
            {
                $icp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress LIMIT 1", OBJECT);
                $api_user_id = $icp_account->api_user_id;
            }
        }
		
		
		// Use Browse class
		
		$zpLib = new icpressBrowse;
		
		$zpLib->setAccount($api_user_id);
		
		$zpLib->getLib();
	}

    
?>