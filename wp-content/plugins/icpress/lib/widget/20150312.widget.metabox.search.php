<?php
	
    // Include WordPress
	
	$includewp = realpath("../../../../../wp-load.php");
	$includewp2 = realpath("../../../../../../wp-load.php");
	$includewp3 = realpath("../../../../../../../wp-load.php");
	
	if ( $includewp === false )
		if ( $includewp2 === false )
			if ( $includewp3 === false )
				trigger_error("Could not find file {$filename}", E_USER_ERROR);
			else
				require($includewp3);
		else
			require($includewp2);
	else
	    require($includewp);
	
    define('WP_USE_THEMES', false);
    
    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('ICPress: Access Denied') );
    
    global $wpdb;
    
    header('Content-type: text/html; charset=utf-8');
    
    // Determine account
    if (get_option("ICPress_DefaultAccount"))
    {
        $icp_api_user_id = get_option("ICPress_DefaultAccount");
    }
    else
    {
        $icp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress LIMIT 1", OBJECT);
        $icp_api_user_id = $icp_account->api_user_id;
    }

	// Generally held search results outside of those stored by Zotero
	
	// The MARC records from an API call
	
	// temporarily muted until the source is fleshed out
	if (1 == 2) {
			$zpSearchResults = $wpdb->get_results(
			$wpdb->prepare( 
				"DO DIFFERENT QUERY IN HERE", 
				'%' . $wpdb->esc_like($_GET['term']) . '%'
		), OBJECT );
	
		$zpSearch = array();
	
		if ( count($zpSearchResults) > 0 )
		{
			foreach ( $zpSearchResults as $zpResult )
			{
				// Deal with author
				$id = $zpResult->id;		
				$author = $zpResult->author;
				$zpResultJSON = json_decode( $zpResult->json );
			
				if ( $author == "" )
				{
					if ( isset($zpResultJSON->creators) && count($zpResultJSON->creators) > 0 )
						foreach ( $zpResultJSON->creators as $i => $creator )
							if ( $i != (count($zpResultJSON->creators)-1) )
								$author .= $creator->name . ', ';
							else
								$author .= $creator->name;
				}
			
				array_push( $zpSearch, array( "id"=> $id, "author" => $author, "label" => $zpResult->label, "value" => $zpResult->value, "source" => 'MARC') );
			}
		}	
	}


    // Zotero search results 
    // - maybe allow a bleed out and come back
    //   - search ALL records and couple those selected with a given user
    //   - change the zotero schema (admin.install)
    $zpSearchResults = $wpdb->get_results(
        $wpdb->prepare( 
            "
                SELECT iczItems.author, iczItems.json, CONCAT(' (', iczItems.year, '). ', iczItems.title, '.') AS label, iczItems.item_key AS value FROM ".$wpdb->prefix."icpress_zoteroItems iczItems 
                LEFT JOIN  ".$wpdb->prefix."icpress_zoteroUserItems iczUserItems ON iczUserItems.id = iczItems.id WHERE iczItems.json LIKE %s 
                AND iczItems.itemType NOT IN ('attachment', 'note') AND (iczUserItems.api_user_id='".$icp_api_user_id."' OR iczUserItems.api_user_id IS NULL)
                ORDER BY iczItems.author ASC
            ", 
            '%' . $wpdb->esc_like($_GET['term']) . '%'
    ), OBJECT );
    
    $zpSearch = array();
    
    if ( count($zpSearchResults) > 0 )
    {
        foreach ( $zpSearchResults as $zpResult )
        {
            // Deal with author
            $id = $zpResult->id;
            $author = $zpResult->author;
            $zpResultJSON = json_decode( $zpResult->json );
            
            if ( $author == "" )
            {
                if ( isset($zpResultJSON->creators) && count($zpResultJSON->creators) > 0 )
                    foreach ( $zpResultJSON->creators as $i => $creator )
                        if ( $i != (count($zpResultJSON->creators)-1) )
                            $author .= $creator->name . ', ';
                        else
                            $author .= $creator->name;
            }
            
            array_push( $zpSearch, array( "id"=> $id, "author" => $author, "label" => $zpResult->label, "value" => $zpResult->value, "source" => 'Zotero') );
        }
    }
    
	// array_push( $zpSearch, array( "author" => "Writer", "label" => "title", "value" => "book", "source" => 'Zotero') );
	// array_push( $zpSearch, array( "author" => "Writer", "label" => "title", "value" => "record", "source" => 'MARC') );	
    echo json_encode($zpSearch);
    
    unset($icp_api_user_id);
    unset($icp_account);
    $wpdb->flush();

?>