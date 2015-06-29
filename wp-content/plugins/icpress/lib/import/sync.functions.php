<?php
    
    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('ICPress: Access Denied') );
    
    
    /****************************************************************************************
    *
    *     ICPRESS BASIC SYNC FUNCTIONS
    *
    ****************************************************************************************/
    
    function icp_autoupdate()
    {
	// Get interval
	$icp_default_autoupdate = "weekly";
	if (get_option("ICPress_DefaultAutoUpdate"))
	    $icp_default_autoupdate = get_option("ICPress_DefaultAutoUpdate");
        
        // Get last update date
        $icp_last_autoupdate = date('Y-m-d-');
        if (get_option("ICPress_LastAutoUpdate"))
            $icp_last_autoupdate= get_option("ICPress_LastAutoUpdate");
        
        // Find difference
        $diff_in_days = intval( floor((strtotime(date('Y-m-d')) - strtotime($icp_last_autoupdate))/3600/24) );
        
        $to_update_or_not = false;
        
        // Determine whether to update
        if (($icp_default_autoupdate == "weekly" && $diff_in_days > 7) ||
                ($icp_default_autoupdate == "daily" && $diff_in_days > 1))
            $to_update_or_not = true;
        
	return $to_update_or_not;
    }
    
    
    
    /****************************************************************************************
    *
    *     ICPRESS SYNC ITEMS
    *
    ****************************************************************************************/
    
    function icp_get_local_items ($wpdb, $api_user_id)
    {
        $query = "SELECT * FROM ".$wpdb->prefix."icpress_zoteroItems WHERE api_user_id='".$api_user_id."'";
        
        $results = $wpdb->get_results( $query, OBJECT );
        $items = array();
        
        // Set item key as id, updated to false
        foreach ($results as $item) {
            $item->updated = 0;
            $items[$item->item_key] = $item;
        }
        
        unset($results);
        return $items;
    }
    
    
    
    function icp_get_server_items ($wpdb, $api_user_id, $icp_start)
    {
        $icp_import_contents = new ICPressRequest();
        $icp_account = icp_get_account($wpdb, $api_user_id);
        //$icp_account = $GLOBALS['icp_session'][$api_user_id]['icp_account'];
        
        
        // See if default exists
        $icp_default_style = "apa";
        if (get_option("ICPress_DefaultStyle"))
            $icp_default_style = get_option("ICPress_DefaultStyle");
        
        // Build request URL
        $icp_import_url = "https://api.zotero.org/".$icp_account[0]->account_type."/".$api_user_id."/items?";
        if (is_null($icp_account[0]->public_key) === false && trim($icp_account[0]->public_key) != "")
            $icp_import_url .= "key=".$icp_account[0]->public_key."&";
        $icp_import_url .= "format=atom&content=json,bib&style=".$icp_default_style."&limit=50&start=".$icp_start;
        //var_dump($icp_import_url);
        
        // Read the external data
	$icp_xml = $icp_import_contents->get_request_contents( $icp_import_url, false );
        
        // Stop in our tracks if there's a request error
        if ($icp_import_contents->request_error)
            return $icp_import_contents->request_error;
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($icp_xml);
        
        // Get last set
        if (!isset($GLOBALS['icp_session'][$api_user_id]['items']['last_set']))
        {
            $last_set = "";
            $links = $doc_citations->getElementsByTagName("link");
            
            foreach ($links as $link)
            {
                if ($link->getAttribute('rel') == "last")
                {
                    if (stripos($link->getAttribute('href'), "start=") !== false)
                    {
                        $last_set = explode("start=", $link->getAttribute('href'));
                        $GLOBALS['icp_session'][$api_user_id]['items']['last_set'] = intval($last_set[1]);
                    }
                    else
                    {
                        $GLOBALS['icp_session'][$api_user_id]['items']['last_set'] = 0;
                    }
                }
            }
        }
        
        $entries = $doc_citations->getElementsByTagName("entry");
        
        
        // COMPARE EACH ENTRY TO LOCAL
        // Entries can be items or attachments (e.g. notes)
        
        foreach ($entries as $entry)
        {
            $item_key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            
            // Check to see if item key exists in local
            if (array_key_exists( $item_key, $GLOBALS['icp_session'][$api_user_id]['items']['icp_local_items'] ))
            {
                // Check to see if it needs updating
                if ($retrieved != $GLOBALS['icp_session'][$api_user_id]['items']['icp_local_items'][$item_key]->retrieved)
                {
                    $GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_update'][$item_key] = $GLOBALS['icp_session'][$api_user_id]['items']['icp_local_items'][$item_key]->id;
                    //unset($GLOBALS['icp_session'][$api_user_id]['items']['icp_local_items'][$item_key]); // Leave only the local ones that should be deleted
                    update_option('ICPRESS_DELETE_'.$api_user_id, get_option('ICPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                }
                else // ignore
                {
                    //unset($GLOBALS['icp_session'][$api_user_id]['items']['icp_local_items'][$item_key]); // Leave only the local ones that should be deleted
                    update_option('ICPRESS_DELETE_'.$api_user_id, get_option('ICPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                    continue;
                }
            }
            
            // Item key doesn't exist in local, or needs updating, so collect metadata and add
            $item_type = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "itemType")->item(0)->nodeValue;
            
            // Get citation content (json and bib)
            
            $citation_content = "";
            $citation_content_temp = new DOMDocument();
            
            foreach($entry->getElementsByTagNameNS("http://zotero.org/ns/api", "subcontent") as $child)
            {
                if ($child->attributes->getNamedItem("type")->nodeValue == "json")
                {
                    $json_content = $child->nodeValue;
                }
                else // Styled citation
                {
                    foreach($child->childNodes as $child_content) {
                        $citation_content_temp->appendChild($citation_content_temp->importNode($child_content, true));
                        $citation_content = $citation_content_temp->saveHTML();
                    }
                }
            }
            
            // Get basic metadata from JSON
            $json_content_decoded = json_decode($json_content);
            
            $author = "";
            $author_other = "";
            $date = "";
            $year = "";
            $title = "";
            $numchildren = 0;
            $parent = "";
            $link_mode = "";
            
            if (count($json_content_decoded->creators) > 0)
                foreach ( $json_content_decoded->creators as $creator )
                    if ($creator->creatorType == "author")
                        $author .= $creator->lastName . ", ";
                    else
                        $author_other .= $creator->lastName . ", ";
            else
                $author .= $creator->creators["lastName"];
            
            // Determine if we use author or other author type
            if (trim($author) == "")
                $author = $author_other;
            
            // Remove last comma
            $author = preg_replace('~(.*)' . preg_quote(', ', '~') . '~', '$1' . '', $author, 1);
            
            $date = $json_content_decoded->date;
            $year = icp_extract_year($date);
            
            if (trim($year) == "")
                $year = "1977";
            
            $title = $json_content_decoded->title;
            
            $numchildren = intval($entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numChildren")->item(0)->nodeValue);
            
            // DOWNLOAD: Find URL
            if ($item_type == "attachment")
            {
                if (isset($json_content_decoded->linkMode))
                    $link_mode = $json_content_decoded->linkMode;
            }
            
            // PARENT
            foreach($entry->getElementsByTagName("link") as $entry_link)
            {
                if ($entry_link->getAttribute('rel') == "up") {
                    $temp = explode("items/", $entry_link->getAttribute('href'));
                    $temp = explode("?", $temp[1]);
                    $parent = $temp[0];
                }
                
                // Get download URL
                if ($link_mode == "imported_file" && $entry_link->getAttribute('rel') == "self") {
                    $citation_content = substr($entry_link->getAttribute('href'), 0, strpos($entry_link->getAttribute('href'), "?"));
                }
            }
            
            
            // If item key needs updating
            if (array_key_exists( $item_key, $GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_update'] ))
            {
                $GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_update'][$item_key] = array (
                        "api_user_id" => $icp_account[0]->api_user_id,
                        "item_key" => $item_key,
                        "retrieved" => icp_db_prep($retrieved),
                        "json" => icp_db_prep($json_content),
                        "author" => icp_db_prep($author),
                        "zpdate" => icp_db_prep($date),
                        "year" => icp_db_prep($year),
                        "title" => icp_db_prep($title),
                        "itemType" => $item_type,
                        "linkMode" => $link_mode,
                        "citation" => icp_db_prep($citation_content),
                        "style" => icp_db_prep($icp_default_style),
                        "numchildren" => $numchildren,
                        "parent" => $parent);
            }
            // If item key isn't in local, add it
            else if (!array_key_exists( $item_key, $GLOBALS['icp_session'][$api_user_id]['items']['icp_local_items'] ))
            {
                array_push($GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_add'],
                        $icp_account[0]->api_user_id,
                        $item_key,
                        icp_db_prep($retrieved),
                        icp_db_prep($json_content),
                        icp_db_prep($author),
                        icp_db_prep($date),
                        icp_db_prep($year),
                        icp_db_prep($title),
                        $item_type,
                        $link_mode,
                        icp_db_prep($citation_content),
                        icp_db_prep($icp_default_style),
                        $numchildren,
                        $parent);
                
                $GLOBALS['icp_session'][$api_user_id]['items']['query_total_items_to_add']++;
            }
            
        } // foreach entry
        
        // LAST ITEM
        if ($GLOBALS['icp_session'][$api_user_id]['items']['last_set'] == $icp_start)
        {
            return false;
        }
        else // continue to next set of items
        {
            return true;
        }
        
        unset($icp_import_contents);
        unset($icp_import_url);
        unset($icp_xml);
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: icp_get_server_items
    
    
    
    function icp_save_synced_items ($wpdb, $api_user_id, $done=true)
    {
        // RUN QUERIES: UPDATE
        
        if (count($GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_update']) > 0)
        {
            foreach ($GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_update'] as $item_params)
            {
                $wpdb->update( 
                    $wpdb->prefix.'icpress_zoteroItems', 
                    $item_params, 
                    array( 'item_key' => $item_params["item_key"], 'api_user_id' => $item_params["api_user_id"] ), 
                    array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s' ),
                    array( '%s', '%s' ) 
                );
            }
            
            $wpdb->flush();
        }
        
        // ADD
        if (count($GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_add']) > 0)
        {
            $wpdb->query( $wpdb->prepare(
                    "
                    INSERT INTO ".$wpdb->prefix."icpress_zoteroItems 
                    ( api_user_id, item_key, retrieved, json, author, zpdate, year, title, itemType, linkMode, citation, style, numchildren, parent )
                    VALUES ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )".str_repeat(", ( %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %d, %s )", $GLOBALS['icp_session'][$api_user_id]['items']['query_total_items_to_add']-1), 
                $GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_add']
            ) );
            
            $wpdb->flush();
        }
        
        // REMOVE: Only at the last set
        
        if ($done && count($GLOBALS['icp_session'][$api_user_id]['items']['icp_local_items']) > 0)
        {
            $icp_delete_items = explode(",", get_option('ICPRESS_DELETE_'.$api_user_id));
            
            foreach ($icp_delete_items as $item_params)
            //foreach ($GLOBALS['icp_session'][$api_user_id]['items']['icp_local_items'] as $item_params)
            {
                $wpdb->query( $wpdb->prepare( 
                        "
                        DELETE FROM ".$wpdb->prefix."icpress_zoteroItems
                        WHERE item_key = %s
                        AND api_user_id = %s
                        ",
                        $item_params->item_key, $item_params->api_user_id
                ) );
            }
            
            $wpdb->flush();
        }
        
        if ($done) // unset everything
        {
            unset($GLOBALS['icp_session'][$api_user_id]['items']);
        }
        else // reset add and update
        {
            $GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_add'] = array();
            $GLOBALS['icp_session'][$api_user_id]['items']['icp_items_to_update'] = array();
            $GLOBALS['icp_session'][$api_user_id]['items']['query_total_items_to_add'] = 0;
        }
        
    } // FUNCTION: icp_save_synced_items
    
    
    
    /****************************************************************************************
    *
    *     ICPRESS SYNC COLLECTIONS
    *
    ****************************************************************************************/
    
    function icp_get_local_collections ($wpdb, $api_user_id)
    {
        $query = "SELECT * FROM ".$wpdb->prefix."icpress_zoteroCollections WHERE api_user_id='".$api_user_id."'";
        
        $results = $wpdb->get_results( $query, OBJECT );
        $items = array();
        
        // Set item key as id, updated to false
        foreach ($results as $item) {
            $item->updated = 0;
            $items[$item->item_key] = $item;
        }
        
        unset($results);
        return $items;
    }
    
    
    
    function icp_get_server_collections ($wpdb, $api_user_id, $icp_start)
    {
        $icp_import_contents = new ICPressRequest();
        $icp_account = icp_get_account($wpdb, $api_user_id);
        //$icp_account = $GLOBALS['icp_session'][$api_user_id]['icp_account'];
        
        $icp_import_url = "https://api.zotero.org/".$icp_account[0]->account_type."/".$icp_account[0]->api_user_id."/collections?limit=50&start=".$icp_start;
        if (is_null($icp_account[0]->public_key) === false && trim($icp_account[0]->public_key) != "")
            $icp_import_url .= "&key=".$icp_account[0]->public_key;
        
	$icp_xml = $icp_import_contents->get_request_contents( $icp_import_url, false );
        
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($icp_xml);
        
        // Get last set
        if (!isset($GLOBALS['icp_session'][$api_user_id]['collections']['last_set']))
        {
            $last_set = "";
            $links = $doc_citations->getElementsByTagName("link");
            
            foreach ($links as $link)
            {
                if ($link->getAttribute('rel') == "last")
                {
                    if (stripos($link->getAttribute('href'), "start=") !== false)
                    {
                        $last_set = explode("start=", $link->getAttribute('href'));
                        $GLOBALS['icp_session'][$api_user_id]['collections']['last_set'] = intval($last_set[1]);
                    }
                    else
                    {
                        $GLOBALS['icp_session'][$api_user_id]['collections']['last_set'] = 0;
                    }
                }
            }
        }
        
        
        // PREPARE EACH ENTRY FOR DB INSERT
        
        $entries = $doc_citations->getElementsByTagName("entry");
        
        foreach ($entries as $entry)
        {
            $item_key = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "key")->item(0)->nodeValue;
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            
            // Check to see if item key exists in local
            if (array_key_exists( $item_key, $GLOBALS['icp_session'][$api_user_id]['collections']['icp_local_collections'] ))
            {
                // Check to see if it needs updating
                if ($retrieved != $GLOBALS['icp_session'][$api_user_id]['collections']['icp_local_collections'][$item_key]->retrieved)
                {
                    $GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_update'][$item_key] = $GLOBALS['icp_session'][$api_user_id]['collections']['icp_local_collections'][$item_key]->id;
                    //unset($GLOBALS['icp_session'][$api_user_id]['collections']['icp_local_collections'][$item_key]); // Leave only the local ones that should be deleted
                    update_option('ICPRESS_DELETE_'.$api_user_id, get_option('ICPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                }
                else // ignore
                {
                    //unset($GLOBALS['icp_session'][$api_user_id]['collections']['icp_local_collections'][$item_key]); // Leave only the local ones that should be deleted
                    update_option('ICPRESS_DELETE_'.$api_user_id, get_option('ICPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                    continue;
                }
            }
            
            $title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
            $parent = "";
            
            // Get parent collection
            foreach($entry->getElementsByTagName("link") as $link)
            {
                if ($link->attributes->getNamedItem("rel")->nodeValue == "up")
                {
                    $parent_temp = explode("/", $link->attributes->getNamedItem("href")->nodeValue);
                    $parent = $parent_temp[count($parent_temp)-1];
                }
            }
            
            $numCollections = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numCollections")->item(0)->nodeValue;
            $numItems = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numItems")->item(0)->nodeValue;
            
            unset($icp_import_contents);
            unset($icp_import_url);
            unset($icp_xml);
            
            
            
            // GET LIST OF ITEM KEYS
            $icp_import_contents = new ICPressRequest();
            
            $icp_import_url = "https://api.zotero.org/".$icp_account[0]->account_type."/".$icp_account[0]->api_user_id."/collections/".$item_key."/items?format=keys";
            if (is_null($icp_account[0]->public_key) === false && trim($icp_account[0]->public_key) != "")
                $icp_import_url .= "&key=".$icp_account[0]->public_key;
            
            // Import content
	    $icp_xml = $icp_import_contents->get_request_contents( $icp_import_url, false );
            
            $icp_collection_itemkeys = rtrim(str_replace("\n", ",", $icp_xml), ",");
            
            
            
            // If item key needs updating
            if (array_key_exists( $item_key, $GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_update'] ))
            {
                $GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_update'][$item_key] = array (
                        "api_user_id" => $icp_account[0]->api_user_id,
                        "title" => icp_db_prep($title),
                        "retrieved" => icp_db_prep($retrieved),
                        "parent" => $parent,
                        "item_key" => $item_key,
                        "numCollections" => $numCollections,
                        "numItems" => $numItems,
                        "listItems" => icp_db_prep($icp_collection_itemkeys)
                        );
            }
            // If item key isn't in local, add it
            else if (!array_key_exists( $item_key, $GLOBALS['icp_session'][$api_user_id]['collections']['icp_local_collections'] ))
            {
                array_push($GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_add'],
                    $icp_account[0]->api_user_id,
                    icp_db_prep($title),
                    icp_db_prep($retrieved),
                    $parent,
                    $item_key,
                    $numCollections,
                    $numItems,
                    icp_db_prep($icp_collection_itemkeys)
                    );
                $GLOBALS['icp_session'][$api_user_id]['collections']['query_total_collections_to_add']++;
            }
            
            unset($title);
            unset($retrieved);
            unset($parent);
            unset($item_key);
            unset($numCollections);
            unset($numItems);
            unset($icp_collection_itemkeys);
            
        } // entry
        
        
        // LAST SET
        if ($GLOBALS['icp_session'][$api_user_id]['collections']['last_set'] == $icp_start)
        {
            return false;
        }
        else // continue to next set of collections
        {
            return true;
        }
        
        unset($icp_import_contents);
        unset($icp_import_url);
        unset($icp_xml);
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: icp_get_server_collections
    
    
    
    function icp_save_synced_collections ($wpdb, $api_user_id, $done=true)
    {
        // RUN QUERIES: UPDATE
        
        if (count($GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_update']) > 0)
        {
            foreach ($GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_update'] as $item_params)
            {
                $wpdb->update( 
                    $wpdb->prefix.'icpress_zoteroCollections', 
                    $item_params, 
                    array( 'item_key' => $item_params["item_key"], 'api_user_id' => $item_params["api_user_id"] ), 
                    array( '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s' ),
                    array( '%s', '%s' ) 
                );
            }
            
            $wpdb->flush();
        }
        
        // ADD
        
        if (count($GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_add']) > 0)
        {
            $wpdb->query( $wpdb->prepare(
                    "
                        INSERT INTO ".$wpdb->prefix."icpress_zoteroCollections
                        ( api_user_id, title, retrieved, parent, item_key, numCollections, numItems, listItems )
                        VALUES ( %s, %s, %s, %s, %s, %d, %d, %s )".str_repeat(", ( %s, %s, %s, %s, %s, %d, %d, %s )", $GLOBALS['icp_session'][$api_user_id]['collections']['query_total_collections_to_add']-1), 
                $GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_add']
            ) );
            
            $wpdb->flush();
        }
        
        // REMOVE
        
        if ($done && count($GLOBALS['icp_session'][$api_user_id]['collections']['icp_local_collections']) > 0)
        {
            $icp_delete_items = explode(",", get_option('ICPRESS_DELETE_'.$api_user_id));
            
            foreach ($icp_delete_items as $item_params)
            //foreach ($GLOBALS['icp_session'][$api_user_id]['collections']['icp_local_collections'] as $item_params)
            {
                $wpdb->query( $wpdb->prepare( 
                        "
                        DELETE FROM ".$wpdb->prefix."icpress_zoteroCollections 
                        WHERE item_key = %s
                        AND api_user_id = %s
                        ",
                        $item_params->item_key, $item_params->api_user_id
                ) );
            }
            
            $wpdb->flush();
        }
        
        if ($done) // unset everything
        {
            unset($GLOBALS['icp_session'][$api_user_id]['collections']);
        }
        else // reset add and update
        {
            $GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_update'] = array();
            $GLOBALS['icp_session'][$api_user_id]['collections']['icp_collections_to_add'] = array();
            $GLOBALS['icp_session'][$api_user_id]['collections']['query_total_collections_to_add'] = 0;
        }
        
    } // FUNCTION: icp_save_synced_collections

    
    
    /****************************************************************************************
    *
    *     ICPRESS SYNC TAGS
    *
    ****************************************************************************************/
    
    function icp_get_local_tags ($wpdb, $api_user_id)
    {
        $query = "SELECT * FROM ".$wpdb->prefix."icpress_zoteroTags WHERE api_user_id='".$api_user_id."'";
        
        $results = $wpdb->get_results( $query, OBJECT );
        $items = array();
        
        // Set title as id, updated to false
        foreach ($results as $item) {
            $item->updated = 0;
            $items[($item->title)] = $item;
        }
        
        unset($results);
        return $items;
    }
    
    
    
    function icp_get_server_tags ($wpdb, $api_user_id, $icp_start)
    {
        $icp_import_contents = new ICPressRequest();
        $icp_account = icp_get_account($wpdb, $api_user_id);
        //$icp_account = $GLOBALS['icp_session'][$api_user_id]['icp_account'];
        
        // Build request URL
        $icp_import_url = "https://api.zotero.org/".$icp_account[0]->account_type."/".$icp_account[0]->api_user_id."/tags?limit=50&start=".$icp_start;
        if (is_null($icp_account[0]->public_key) === false && trim($icp_account[0]->public_key) != "")
            $icp_import_url .= "&key=".$icp_account[0]->public_key;
        
	$icp_xml = $icp_import_contents->get_request_contents( $icp_import_url, false );
        
        // Make it DOM-traversable 
        $doc_citations = new DOMDocument();
        $doc_citations->loadXML($icp_xml);
        
        // Get last set
        if (!isset($GLOBALS['icp_session'][$api_user_id]['tags']['last_set']))
        {
            $last_set = "";
            $links = $doc_citations->getElementsByTagName("link");
            
            foreach ($links as $link)
            {
                if ($link->getAttribute('rel') == "last")
                {
                    if (stripos($link->getAttribute('href'), "start=") !== false)
                    {
                        $last_set = explode("start=", $link->getAttribute('href'));
                        $GLOBALS['icp_session'][$api_user_id]['tags']['last_set'] = intval($last_set[1]);
                    }
                    else
                    {
                        $GLOBALS['icp_session'][$api_user_id]['tags']['last_set'] = 0;
                    }
                }
            }
        }
        
       $entries = $doc_citations->getElementsByTagName("entry");
        
        foreach ($entries as $entry)
        {
            $title = $entry->getElementsByTagName("title")->item(0)->nodeValue;
            $retrieved = $entry->getElementsByTagName("updated")->item(0)->nodeValue;
            
            // Check to see if tags exists in local
            if (array_key_exists( trim($title), $GLOBALS['icp_session'][$api_user_id]['tags']['icp_local_tags'] ))
            {
                // Check to see if it needs updating
                if ($retrieved != $GLOBALS['icp_session'][$api_user_id]['tags']['icp_local_tags'][trim($title)]->retrieved)
                {
                    $GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_update'][trim($title)] = $GLOBALS['icp_session'][$api_user_id]['tags']['icp_local_tags'][trim($title)]->id;
                    //unset($GLOBALS['icp_session'][$api_user_id]['tags']['icp_local_tags'][trim($title)]); // Leave only the local ones that should be deleted
                    update_option('ICPRESS_DELETE_'.$api_user_id, get_option('ICPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                }
                else // ignore
                {
                    //unset($GLOBALS['icp_session'][$api_user_id]['tags']['icp_local_tags'][trim($title)]); // Leave only the local ones that should be deleted
                    update_option('ICPRESS_DELETE_'.$api_user_id, get_option('ICPRESS_DELETE_'.$api_user_id) . "," . $item_key);
                    continue;
                }
            }
            
            $numItems = $entry->getElementsByTagNameNS("http://zotero.org/ns/api", "numItems")->item(0)->nodeValue;
            
            unset($icp_import_contents);
            unset($icp_import_url);
            unset($icp_xml);
            
            
            
            // GET LIST OF ITEM KEYS
            $icp_import_contents = new ICPressRequest();
            
            $icp_import_url = "https://api.zotero.org/".$icp_account[0]->account_type."/".$icp_account[0]->api_user_id."/tags/".urlencode($title)."/items?format=keys";
            if (is_null($icp_account[0]->public_key) === false && trim($icp_account[0]->public_key) != "")
                $icp_import_url .= "&key=".$icp_account[0]->public_key;
            
            // Import content
	    $icp_xml = $icp_import_contents->get_request_contents( $icp_import_url, false );
            
            $icp_tag_itemkeys = rtrim(str_replace("\n", ",", $icp_xml), ",");
            
            
            
            // If item key needs updating
            if (array_key_exists( trim($title), $GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_update'] ))
            {
                $GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_update'][trim($title)] = array (
                        "api_user_id" => $icp_account[0]->api_user_id,
                        "title" => icp_db_prep($title),
                        "retrieved" => icp_db_prep($retrieved),
                        "numItems" => $numItems,
                        "listItems" => icp_db_prep($icp_tag_itemkeys)
                        );
            }
            // If item key isn't in local, add it
            else if (!array_key_exists( trim($title), $GLOBALS['icp_session'][$api_user_id]['tags']['icp_local_tags'] ))
            {
                array_push($GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_add'],
                    $icp_account[0]->api_user_id,
                    icp_db_prep($title),
                    icp_db_prep($retrieved),
                    $numItems,
                    icp_db_prep($icp_tag_itemkeys)
                    );
                $GLOBALS['icp_session'][$api_user_id]['tags']['query_total_tags_to_add']++;
            }
            
            unset($title);
            unset($retrieved);
            unset($numItems);
            unset($icp_tag_itemkeys);
            
        } // entry
        
        
        // LAST SET
        if ($GLOBALS['icp_session'][$api_user_id]['tags']['last_set'] == $icp_start)
        {
            return false;
        }
        else // continue to next set of tags
        {
            return true;
        }
        
        unset($icp_import_contents);
        unset($icp_import_url);
        unset($icp_xml);
        unset($doc_citations);
        unset($entries);
        
    } // FUNCTION: icp_get_server_tags
    
    
    
    function icp_save_synced_tags ($wpdb, $api_user_id, $done=true)
    {
        // RUN QUERIES: UPDATE
        
        if (count($GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_update']) > 0)
        {
            foreach ($GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_update'] as $item_params)
            {
                $wpdb->update( 
                    $wpdb->prefix.'icpress_zoteroTags', 
                    $item_params, 
                    array( 'title' => trim($item_params["title"]), 'api_user_id' => $item_params["api_user_id"] ), 
                    array( '%s', '%s', '%s', '%d', '%s' ),
                    array( '%s', '%s' ) 
                );
            }
            
            $wpdb->flush();
        }
        
        // ADD
        
        if (count($GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_add']) > 0)
        {
            $wpdb->query( $wpdb->prepare(
                    "
                        INSERT INTO ".$wpdb->prefix."icpress_zoteroTags
                        ( api_user_id, title, retrieved, numItems, listItems )
                        VALUES ( %s, %s, %s, %d, %s )".str_repeat(", ( %s, %s, %s, %d, %s )", $GLOBALS['icp_session'][$api_user_id]['tags']['query_total_tags_to_add']-1), 
                $GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_add']
            ) );
            
            $wpdb->flush();
        }
        
        // REMOVE
        
        if ($done && count($GLOBALS['icp_session'][$api_user_id]['tags']['icp_local_tags']) > 0)
        {
            $icp_delete_items = explode(",", get_option('ICPRESS_DELETE_'.$api_user_id));
            
            foreach ($icp_delete_items as $item_params)
            //foreach ($GLOBALS['icp_session'][$api_user_id]['tags']['icp_local_tags'] as $item_params)
            {
                $wpdb->query( $wpdb->prepare( 
                        "
                        DELETE FROM ".$wpdb->prefix."icpress_zoteroTags 
                        WHERE title = %s
                        AND api_user_id = %s
                        ",
                        trim($item_params->title), $item_params->api_user_id
                ) );
            }
            
            $wpdb->flush();
        }
        
        if ($done) // unset everything
        {
            unset($GLOBALS['icp_session'][$api_user_id]['tags']);
        }
        else // reset add and update
        {
            $GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_update'] = array();
            $GLOBALS['icp_session'][$api_user_id]['tags']['icp_tags_to_add'] = array();
            $GLOBALS['icp_session'][$api_user_id]['tags']['query_total_tags_to_add'] = 0;
        }
        
    } // FUNCTION: icp_save_synced_tags



?>