<?php


    // Include shortcode functions
    require("shortcode.functions.php");
    
    
    function ICPress_func($atts)
    {
        extract(shortcode_atts(array(
            
            'user_id' => false, // deprecated
            'userid' => false,
            'nickname' => false,
            'nick' => false,
            
            'author' => false,
            'authors' => false,
            'year' => false,
            'years' => false,
            
            'data_type' => false, // deprecated
            'datatype' => "items",
            
            'collection_id' => false,
            'collection' => false,
            'collections' => false,
            
            'item_key' => false,
            'item' => false,
            'items' => false,
            
            'inclusive' => "yes",
            
            'tag_name' => false,
            'tag' => false,
            'tags' => false,
            
            'content' => false, // deprecated
            'style' => false,
            'limit' => false,
            
            'sortby' => "default",
            'order' => false,
            'sort' => false,
            
            'title' => "no",
            
            'image' => false,
            'images' => false,
            'showimage' => "no",
            
            'showtags' => "no",
            
            'downloadable' => "no",
            'download' => "no",
            
            'note' => false,
            'notes' => "no",
            
            'abstract' => false,
            'abstracts' => "no",
            
            'cite' => "no",
            'citeable' => false,
            
            'metadata' => false,
            
            'link' => "no",
            'linkedlist' => "no",
            
            'target' => false,
			
			'forcenumber' => false,
			
			'depth' => false
            
        ), $atts, "icpress"));
        
        
        // FORMAT PARAMETERS
        
        // Filter by account
        if ($user_id) $api_user_id = str_replace('"','',html_entity_decode($user_id));
        else if ($userid) $api_user_id = str_replace('"','',html_entity_decode($userid));
        else $api_user_id = false;
        
        if ($nickname) $nickname = str_replace('"','',html_entity_decode($nickname));
        if ($nick) $nickname = str_replace('"','',html_entity_decode($nick));
        
        // Filter by author
        $author = str_replace('"','',html_entity_decode($author));
        if ($authors) $author = str_replace('"','',html_entity_decode($authors));
        if (strpos($author, ",") > 0) $author = explode(",", $author);
        
        // Filter by year
        $year = str_replace('"','',html_entity_decode($year));
        if ($years) $year = str_replace('"','',html_entity_decode($years));
        if (strpos($year, ",") > 0) $year = explode(",", $year);
        
        // Format with datatype and content
        if ($data_type) $data_type = str_replace('"','',html_entity_decode($data_type));
        else $data_type = str_replace('"','',html_entity_decode($datatype));
        
        // Filter by collection
        if ($collection_id) $collection_id = str_replace('"','',html_entity_decode($collection_id));
        else if ($collection) $collection_id = str_replace('"','',html_entity_decode($collection));
        else if ($collections) $collection_id = str_replace('"','',html_entity_decode($collections));
        //else $collection_id = str_replace('"','',html_entity_decode($collection));
        
        if (strpos($collection_id, ",") > 0) $collection_id = explode(",", $collection_id);
        if ($data_type == "collections" && isset($_GET['zpcollection']) ) $collection_id = htmlentities( urldecode( $_GET['zpcollection'] ) );
        
        // Filter by tag
        if ($tag_name) $tag_name = str_replace('"','',html_entity_decode($tag_name));
        else if ($tags) $tag_name = str_replace('"','',html_entity_decode($tags));
        else $tag_name = str_replace('"','',html_entity_decode($tag));
        
        $tag_name = str_replace("+", "", $tag_name);
        if (strpos($tag_name, ",") > 0) $tag_name = explode(",", $tag_name);
        if ($data_type == "tags" && isset($_GET['zptag']) ) $tag_name = htmlentities( urldecode( $_GET['zptag'] ) );
        
        // Filter by itemkey
        if ($item_key) $item_key = str_replace('"','',html_entity_decode($item_key));
        if ($items) $item_key = str_replace('"','',html_entity_decode($items));
        if ($item) $item_key = str_replace('"','',html_entity_decode($item));
        if (strpos($item_key, ",") > 0) $item_key = explode(",", $item_key);
        
        $content = str_replace('"','',html_entity_decode($content));
        $inclusive = str_replace('"','',html_entity_decode($inclusive));
        
        // Format style
        $style = str_replace('"','',html_entity_decode($style));
        
        // Limit
        $limit = str_replace('"','',html_entity_decode($limit));
        
        // Order / sort
        $sortby = str_replace('"','',html_entity_decode($sortby));
        
        if ($order) $order = str_replace('"','',html_entity_decode($order));
        else if ($sort) $order = str_replace('"','',html_entity_decode($sort));
        if ($order === false) $order = "ASC";
        
        // Show title
        $title = str_replace('"','',html_entity_decode($title));
        if ($title == "yes" || $title == "true" || $title === true)
        {
            $title = true;
            $sortby = "year";
            $order= "DESC";
        }
        else { $title = false; }
        
        // Show image
        if ($showimage) $showimage = str_replace('"','',html_entity_decode($showimage));
        if ($image) $showimage = str_replace('"','',html_entity_decode($image));
        if ($images) $showimage = str_replace('"','',html_entity_decode($images));
        
        if ($showimage == "yes" || $showimage == "true" || $showimage === true) $showimage = true;
        else $showimage = false;
        
        // Show tags
        if ($showtags == "yes" || $showtags == "true" || $showtags === true) $showtags = true;
        else $showtags = false;
        
        // Show download link
        if ($download == "yes" || $download == "true" || $download === true
                || $downloadable == "yes" || $downloadable == "true" || $downloadable === true)
            $download = true; else $download = false;
        
        // Show notes
        if ($notes) $notes = str_replace('"','',html_entity_decode($notes));
        else if ($note) $notes = str_replace('"','',html_entity_decode($note));
        
        if ($notes == "yes" || $notes == "true" || $notes === true) $notes = true;
        else $notes = false;
        
        // Show abstracts
        if ($abstracts) $abstracts = str_replace('"','',html_entity_decode($abstracts));
        if ($abstract) $abstracts = str_replace('"','',html_entity_decode($abstract));
        
        if ($abstracts == "yes" || $abstracts == "true" || $abstracts === true) $abstracts = true;
        else $abstracts = false;
        
        // Show cite link
        if ($cite) $cite = str_replace('"','',html_entity_decode($cite));
        if ($citeable) $cite = str_replace('"','',html_entity_decode($citeable));
        
        if ($cite == "yes" || $cite == "true" || $cite === true) $cite = true;
        else $cite = false;
        
        if ( !preg_match("/^[0-9a-zA-Z]+$/", $metadata) ) $metadata = false;
        
        if ( $link == "yes" || $link == "true" || $link === true ) $link = str_replace('"','',html_entity_decode($link));
        else if ( $linkedlist == "yes" || $linkedlist == "true" || $linkedlist === true ) $link = str_replace('"','',html_entity_decode($linkedlist));
        
        if ($target == "yes" || $target == "_blank" || $target == "new" || $target == "true" || $target === true)
        $target = true; else $target = false;
        
        if ($forcenumber == "yes" || $forcenumber == "true" || $forcenumber === true)
        $forcenumber = true; else $forcenumber = false;
        
        if ($depth == "all" || $depth == "true" || $depth === true)
        $depth = true; else $depth = false;
        
        
        
        // GET ACCOUNT
        
        global $wpdb;
        
        // Get account (api_user_id)
        $icp_account = false;
        
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
        
        // Generate instance id for shortcode
        $icp_instance_id = "icpress-".md5($api_user_id.$nickname.$author.$year.$data_type.$collection_id.$item_key.$tag_name.$content.$style.$sortby.$order.$limit.$showimage.$download.$note.$cite);
        
        
        
        // GENERATE SHORTCODE
        
        if ($icp_account !== false)
        {
            
            
            // ITEMS
            
            if ( $data_type == "items"
                    || ($data_type == "tags" && isset($_GET['zptag']) )
                    || ($data_type == "collections" && isset($_GET['zpcollection'])) )
            {
                $icp_query = "";
                
                if ($download)
                {
                    $wpdb->get_results(
                        "
                        CREATE TEMPORARY TABLE attachments 
                        SELECT * FROM 
                        ( 
                            SELECT 
                            ".$wpdb->prefix."icpress_zoteroItems.parent AS parent,
                            ".$wpdb->prefix."icpress_zoteroItems.citation AS content,
                            ".$wpdb->prefix."icpress_zoteroItems.item_key AS item_key,
                            ".$wpdb->prefix."icpress_zoteroItems.json AS data,
                            ".$wpdb->prefix."icpress_zoteroItems.linkmode AS linkmode 
                            FROM ".$wpdb->prefix."icpress_zoteroItems LEFT JOIN                           
                            ".$wpdb->prefix."icpress_zoteroUserItems ON 
                            ".$wpdb->prefix."icpress_zoteroItems.id = ".$wpdb->prefix."icpress_zoteroUserItems.id
                            
                            WHERE ".$wpdb->prefix."icpress_zoteroItems.linkmode IN ( 'imported_file', 'linked_url' ) 
                            ORDER BY linkmode ASC 
                        )
                        AS attachments_sub 
                        GROUP BY parent;
                        "
                    );
                }
                
                $icp_query .= "SELECT DISTINCT ".$wpdb->prefix."icpress_zoteroItems.*";
                
                if ($download) $icp_query .= ", attachments.content AS attachment_content, attachments.item_key AS attachment_key, attachments.data AS attachment_data, attachments.linkmode AS attachment_linkmode";
				
				if ($showimage) $icp_query .= ", ".$wpdb->prefix."icpress_zoteroItemImages.image AS itemImage";
                
                $icp_query .= " FROM ".$wpdb->prefix."icpress_zoteroItems ";
                
                
                // JOINS: download, itemimage, collections, tags
                
                // Filter by account
                if ($api_user_id)
                    $icp_query .= " LEFT JOIN ".$wpdb->prefix."icpress_zoteroUserItems ON ".$wpdb->prefix."icpress_zoteroItems.id = ".$wpdb->prefix."icpress_zoteroUserItems.id";
                                
                if ($download)
                    $icp_query .= " LEFT JOIN (attachments) ON  (".$wpdb->prefix."icpress_zoteroItems.item_key=attachments.parent) ";
                
                if ($showimage)
                    $icp_query .= " LEFT JOIN (".$wpdb->prefix."icpress_zoteroItemImages) ON  (".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroItemImages.item_key) ";
                
                if ($collection_id)
                {
					if ( is_array($collection_id) )
					{
						// create inner joins
						for ($i = 0; $i < count($collection_id); $i++)
							$icp_query .= " INNER JOIN ".$wpdb->prefix."icpress_zoteroRelItemColl AS zpRelItemColl".$i." ON ".$wpdb->prefix."icpress_zoteroItems.item_key=zpRelItemColl".$i.".item_key ";
						
						// inclusive?
						if ( $inclusive != "yes" )
						{
							$icp_query .= " AND ( ";
							
							// exclusive to specific collections
							for ($i = 0; $i < count($collection_id); $i++)
							{
								if ($i != 0) $icp_query .= " AND ";
								$icp_query .= " zpRelItemColl".$i.".collection_key='".$collection_id[$i]."' ";
							}
							$icp_query .= " ) ";
						}
					}
					else // single collection
					{
						$icp_query .= " LEFT JOIN ".$wpdb->prefix."icpress_zoteroRelItemColl ON (".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroRelItemColl.item_key) ";
					}
//                    if (!is_array($collection_id)
//							|| (is_array($collection_id) && $inclusive == "yes"))
//                    {
//                        $icp_query .= " LEFT JOIN ".$wpdb->prefix."icpress_zoteroRelItemColl ON (".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroRelItemColl.item_key) ";
//                    }
//                    else if (is_array($collection_id) && $inclusive != "yes")
//                    {
//                        // create inner joins
//                        for ($i = 0; $i < count($collection_id); $i++)
//                            $icp_query .= " INNER JOIN ".$wpdb->prefix."icpress_zoteroRelItemColl AS zpRelItemColl".$i." ON ".$wpdb->prefix."icpress_zoteroItems.item_key=zpRelItemColl".$i.".item_key ";
//                        
//                        $icp_query .= " AND ( ";
//                        
//                        // exclusive to specific collections
//                        for ($i = 0; $i < count($collection_id); $i++)
//                        {
//                            if ($i != 0) $icp_query .= " AND ";
//                            $icp_query .= " zpRelItemColl".$i.".collection_key='".$collection_id[$i]."' ";
//                        }
//                        $icp_query .= " ) ";
//                    }
                }
                
                if ($tag_name)
                {
                    if (!is_array($tag_name) || (is_array($tag_name) && $inclusive == "yes"))
                    {
                        $icp_query .= " LEFT JOIN ".$wpdb->prefix."icpress_zoteroRelItemTags ON (".$wpdb->prefix."icpress_zoteroItems.item_key=".$wpdb->prefix."icpress_zoteroRelItemTags.item_key) ";
                    }
                    else if (is_array($tag_name) && $inclusive != "yes")
                    {
                        // create inner joins
                        for ($i = 0; $i < count($tag_name); $i++)
                            $icp_query .= " INNER JOIN ".$wpdb->prefix."icpress_zoteroRelItemTags AS zpRelItemTags".$i." ON ".$wpdb->prefix."icpress_zoteroItems.item_key=zpRelItemTags".$i.".item_key ";
                        
                        $icp_query .= " AND ( ";
                        
                        // exclusive to specific tags
                        for ($i = 0; $i < count($tag_name); $i++)
                        {
                            if ($i != 0) $icp_query .= " AND ";
                            $icp_query .= " zpRelItemTags".$i.".tag_title='".$tag_name[$i]."' ";
                        }
                        $icp_query .= " ) ";
                    }
                }
                
                // WHERE
                
                $icp_query .= " WHERE ".$wpdb->prefix."icpress_zoteroItems.itemType != 'attachment' AND ".$wpdb->prefix."icpress_zoteroItems.itemType != 'note' ";
                
                // Filter by collection(s)
                if ($collection_id)
                {
                    // Multiple inclusive collections
                    if (is_array($collection_id))
                    {
                        if ($inclusive == "yes")
                        {
                            $icp_query .= " AND (";
                            
                            foreach ($collection_id as $i => $id)
                            {
                                $icp_query .= "zpRelItemColl0.collection_key='".$id."' "; // for some reason, only need first reference to this table
                                
                                if ($i != count($collection_id)-1) $icp_query .= " OR ";
                            }
                            $icp_query .= ") ";
                        }
                    }
                    // Single collection
                    else
                    {
                        $icp_query .= " AND ".$wpdb->prefix."icpress_zoteroRelItemColl.collection_key='".$collection_id."' ";
                    }
                } // $collection_id
                
                // Filter by tag(s)
                if ($tag_name)
                {
                    // Multiple inclusive collections
                    if (is_array($tag_name))
                    {
                        if ($inclusive == "yes")
                        {
                            $icp_query .= " AND (";
                            
                            foreach ($tag_name as $i => $id)
                            {
                                $icp_query .= $wpdb->prefix."icpress_zoteroRelItemTags.tag_title='".$id."' ";
                                
                                if ($i != count($tag_name)-1) $icp_query .= " OR ";
                            }
                            $icp_query .= ") ";
                        }
                    }
                    // Single collection
                    else
                    {
                        $icp_query .= " AND ".$wpdb->prefix."icpress_zoteroRelItemTags.tag_title='".$tag_name."' ";
                    }
                } // $tag_name
                
                // Filter by account
                if ($api_user_id)
                    $icp_query .= " AND ".$wpdb->prefix."icpress_zoteroUserItems.api_user_id='".$api_user_id."'";
                
                // Filter by author
                if ($author)
                {
                    $icp_query .= " AND ( ";
                    
                    // Multiple authors
                    if (is_array($author))
                    {
                        foreach ($author as $i => $icp_author)
                        {
                            // Prep author
                            $icp_author = strtolower(trim($icp_author));
                            if (strpos($icp_author, " ") > 0) $icp_author = preg_split("/\s+(?=\S*+$)/", $icp_author);
                            
							// Deal with two last names by case
							//if ( $icp_author[0] == "van" ) $icp_author[1] = "van " . $icp_author[1];
							
                            if (is_array($icp_author)) // full name (or multiple last names)
                            {
                                if ($inclusive == "yes" && $i != 0) $icp_query .= " OR "; else if ($inclusive != "yes" && $i != 0) $icp_query .= " AND ";
                                
                                //$icp_query .= " ".$wpdb->prefix."icpress_zoteroItems.author LIKE '%".$icp_author[1]."%'";
	                            $icp_query .= " FIND_IN_SET( '".$icp_author[1]."', REPLACE(".$wpdb->prefix."icpress_zoteroItems.author, ', ', ',') )";
	                            $icp_query .= " OR FIND_IN_SET( '".implode(" ", $icp_author)."', REPLACE(".$wpdb->prefix."icpress_zoteroItems.author, ', ', ',') )";
                            }
                            else // last name only
                            {
                                if ($inclusive == "yes" && $i != 0) $icp_query .= " OR "; else if ($inclusive != "yes" && $i != 0) $icp_query .= " AND ";
                                
                                //$icp_query .= " ".$wpdb->prefix."icpress_zoteroItems.author LIKE '%".$icp_author."%'";
	                            $icp_query .= " FIND_IN_SET( '".$icp_author."', REPLACE(".$wpdb->prefix."icpress_zoteroItems.author, ', ', ',') )";
                            }
                        }
                    }
                    else // Single author
                    {
                        // Prep author
                        $author = strtolower(trim($author));
                        if ( strpos($author, " ") > 0 ) $author = preg_split("/\s+(?=\S*+$)/", $author);
						
						// Deal with two last names by case
						//if ( $author[0] == "van" ) $author[1] = "van " . $author[1];
                        
						// Full name in array (or multiple last names)
                        if (is_array($author))
						{
                            //$icp_query .= " ".$wpdb->prefix."icpress_zoteroItems.author LIKE '%".$author[1]."%'";
                            $icp_query .= " FIND_IN_SET( '".$author[1]."', REPLACE(".$wpdb->prefix."icpress_zoteroItems.author, ', ', ',') )";
                            $icp_query .= " OR FIND_IN_SET( '".implode(" ", $author)."', REPLACE(".$wpdb->prefix."icpress_zoteroItems.author, ', ', ',') )";
						}
						// Last name only
                        else
						{
                            //$icp_query .= " ".$wpdb->prefix."icpress_zoteroItems.author LIKE '%".$author."%'";
                            $icp_query .= " FIND_IN_SET( '".$author."', REPLACE(".$wpdb->prefix."icpress_zoteroItems.author, ', ', ',') )";
						}
                    }
                    $icp_query .= " ) ";
                } // $author
                
                // Filter by year: zpdate or year
                if ($year)
                {
                    if (is_array($year))
                    {
                        $icp_query .= " AND FIND_IN_SET(".$wpdb->prefix."icpress_zoteroItems.year, '".implode(",", $year)."')";
                    }
                    else // single
                    {
                        $icp_query .= " AND ".$wpdb->prefix."icpress_zoteroItems.year LIKE '%".$year."%'";
                    }
                }
                
                // Filter by item key
                if ($item_key)
                {
                    if (is_array($item_key))
                        $icp_query .= " AND ".$wpdb->prefix."icpress_zoteroItems.item_key IN('" . implode("','", $item_key) . "')";
                    else // single
                        $icp_query .= " AND ".$wpdb->prefix."icpress_zoteroItems.item_key='".$item_key."'";
                }
                
                // Sort by and sort direction
				// Relies on db column and MySQL sorting
				// Maybe sort by retrieved here, then do sorting after query execution?
                if ($sortby)
                {
                    if ($sortby == "default")
                        $sortby = "retrieved";
                    else if ($sortby == "date")
                        $sortby = "year"; // zpdate -- MySQL doesn't understand
                    
                    if (($tag_name && $collection_id) || (is_array($year)))
                        $icp_query .= " ORDER BY ".$sortby." " . $order;
                    else
                        $icp_query .= " ORDER BY ".$wpdb->prefix."icpress_zoteroItems.".$sortby." " . $order;
                }
                
                // Limit
                if ($limit) $icp_query .= " LIMIT ".$limit;
                
                
                // Prep query -- still necessary?
                
                if ($item_key || $tag_name || $collection_id)
                {
                    $icp_query = str_replace("AND  AND", "AND", $icp_query);
                }
                else if ($author || $year) {
                    $icp_query = str_replace("OR ORDER BY", "ORDER BY", str_replace("OR AND", "OR", str_replace("  ", " ", $icp_query)));
                }
                
                // GET ITEMS FROM DB
                
                // var_dump( $icp_query . "<br /><br />");
                $icp_results = $wpdb->get_results($icp_query, ARRAY_A); unset($icp_query);
                // var_dump( $icp_results );  
                
                /*
                  
                    DISPLAY CITATIONS - loop
                    
                */
                
                $current_title =  "";
                $citation_notes = array();
                $icp_notes_num = 1;
                
                $icp_output = "\n<div class=\"icp-ICPress";
				
				// Force numbering despite style
				if ( $forcenumber ) $icp_output .= " forcenumber";
				
				$icp_output .= "\">\n\n";
                $icp_output .= "<span class=\"ICPRESS_PLUGIN_URL\" style=\"display:none;\">" . ICPRESS_PLUGIN_URL . "</span>\n\n";
                //$icp_output .= "<span class=\"ICPRESS_UPDATE_NOTICE\">Checking ...</span>\n\n";
                
                // Add style, if set
                if ($style) $icp_output .= "<span class=\"icp-ICPress-Style\" style=\"display:none;\">".$style."</span>\n\n";
                
                // TAG OR COLLECTION TITLE
                if ( $data_type == "collections" && isset($_GET['zpcollection']) )
                {
                    $collection_title = $wpdb->get_row("SELECT title FROM ".$wpdb->prefix."icpress_zoteroCollections WHERE item_key='".$collection_id."'");
                    $icp_output .= "<h2>" . $collection_title->title . " / <a title='Back' class='icp-BackLink' href='javascript:window.history.back();'>Back</a></h2>\n\n";
                }
                if ( $data_type == "tags" && isset($_GET['zptag']) )
                {
                    $icp_output .= "<h2>" . $tag_name . " / <a title='Back' class='icp-BackLink' href='javascript:window.history.back();'>Back</a></h2>\n\n";
                }
				
				// SORT
				
				if ($sortby)
				{
					if ($sortby == "default")
                        $sortby = "retrieved";
                    else if ($sortby == "year")
						$sortby = "date";
					
					$icp_results = subval_sort( $icp_results, $sortby, $order );
				}
                
                if ( count($icp_results) > 0 )
                {
                    foreach ($icp_results as $icp_citation)
                    {
                        $citation_image = false;
                        $citation_tags = false;
                        $citation_abstract = "";
                        $has_citation_image = false;
                        $icp_this_meta = json_decode( $icp_citation["json"] );
                        $icp_output .= "<span class=\"icp-ICPress-Userid\" style=\"display:none;\">".$icp_citation['api_user_id']."</span>\n\n";
                        //$icp_output .= "<span class=\"ICPRESS_AUTOUPDATE_KEY\" style=\"display:none;\">" . $_SESSION['icp_session'][$icp_citation['api_user_id']]['key'] . "</span>\n\n";
                        
                        
                        // IMAGE
                        if ($showimage && !is_null($icp_citation["itemImage"]) && $icp_citation["itemImage"] != "")
                        {
                            if ( is_numeric($icp_citation["itemImage"]) )
                            {
                                $icp_citation["itemImage"] = wp_get_attachment_image_src( $icp_citation["itemImage"], "full" );
                                $icp_citation["itemImage"] = $icp_citation["itemImage"][0];
                            }
                            
                            $citation_image = "<div id='icp-Citation-".$icp_citation["item_key"]."' class='icp-Entry-Image'>";
                            $citation_image .= "<img src='".$icp_citation["itemImage"]."' alt='image' />";
                            $citation_image .= "</div>\n";
                            $has_citation_image = " icp-HasImage";
                        }
                        
                        // TAGS
                        // Grab tags associated with item
                        if ( $showtags )
                        {
                            $icp_showtags_query = "SELECT DISTINCT ".$wpdb->prefix."icpress_zoteroTags.title FROM ".$wpdb->prefix."icpress_zoteroTags LEFT JOIN ".$wpdb->prefix."icpress_zoteroRelItemTags ON ".$wpdb->prefix."icpress_zoteroRelItemTags.tag_title=".$wpdb->prefix."icpress_zoteroTags.title WHERE ".$wpdb->prefix."icpress_zoteroRelItemTags.item_key='".$icp_citation["item_key"]."' ORDER BY ".$wpdb->prefix."icpress_zoteroTags.title ASC;";
                            $icp_showtags_results = $wpdb->get_results($icp_showtags_query, ARRAY_A);
                            
                            if ( count($icp_showtags_results) > 0)
                            {
                                $citation_tags = "<p class='icp-ICPress-ShowTags'><span class='title'>Tags:</span> ";
                                
                                foreach ($icp_showtags_results as $i => $icp_showtags_tag)
                                {
                                    $citation_tags .= "<span class='tag'>" . $icp_showtags_tag["title"] . "</span>";
                                    if ( $i != (count($icp_showtags_results)-1) ) $citation_tags .= "<span class='separator'>,</span> ";
                                }
                                $citation_tags .= "</p>\n";
                            }
                            unset($icp_showtags_query);
                            unset($icp_showtags_results);
                        }
                        
                        // ABSTRACT
                        if ( $abstracts && isset($icp_this_meta->abstractNote) && strlen(trim($icp_this_meta->abstractNote)) > 0 )
                        {
                            $citation_abstract = "<p class='icp-Abstract'><span class='icp-Abstract-Title'>Abstract:</span> " . sprintf($icp_this_meta->abstractNote) . "</p>\n";
                        }
                        
                        
                        // NOTES
                        if ($notes)
                        {
                            $icp_notes = $wpdb->get_results("SELECT json FROM ".$wpdb->prefix."icpress_zoteroItems WHERE api_user_id='".$icp_citation['api_user_id']."'
                                    AND parent = '".$icp_citation["item_key"]."' AND itemType = 'note';", OBJECT);
                            
                            if (count($icp_notes) > 0)
                            {
                                $temp_notes = "<li id=\"icp-Note-".$icp_citation["item_key"]."\">\n";
								
								// Only create a list if there's more than one note for this item
								if ( count($icp_notes) == 1 )
								{
                                    $note_json = json_decode($icp_notes[0]->json);
                                    $temp_notes .= $note_json->note . "\n";
								}
								else if ( count($icp_notes) > 1 )
								{
									$temp_notes .= "<ul class='icp-Citation-Item-Notes'>\n";
									
									foreach ($icp_notes as $note)
									{
										$note_json = json_decode($note->json);
										$temp_notes .= "<li class='icp-Citation-note'>" . $note_json->note . "\n</li>\n";
									}
									$temp_notes .= "\n</ul>";
								}
								
                                $temp_notes .= "\n</li>\n\n";
								
								$citation_notes[count($citation_notes)] = $temp_notes;
                                
                                // Add note reference
                                $icp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <sup class=\"icp-Notes-Reference\"><a href=\"#icp-Note-".$icp_citation["item_key"]."\">".$icp_notes_num."</a></sup> </div>" . '$2', $icp_citation['citation'], 1);
                                $icp_notes_num++;
                            }
                            unset($icp_notes);
                            
                        } // end notes
                        
                        
                        // Hyperlink URL: Has to go before Download
                        if (isset($icp_this_meta->url) && strlen($icp_this_meta->url) > 0)
                        {
                            $icp_url_replacement = "<a title=\"". htmlspecialchars($icp_this_meta->title) ."\" rel=\"external\" ";
                            if ( $target ) $icp_url_replacement .= "target=\"_blank\" ";
                            $icp_url_replacement .= "href=\"".urldecode(urlencode($icp_this_meta->url))."\">".urldecode(urlencode($icp_this_meta->url))."</a>";
                            
                            // Replace ampersands
                            $icp_citation['citation'] = str_replace(htmlspecialchars($icp_this_meta->url), $icp_this_meta->url, $icp_citation['citation']);
                            
                            // Then replace with linked URL
                            $icp_citation['citation'] = str_replace($icp_this_meta->url, $icp_url_replacement, $icp_citation['citation']);
                        }
                        
                        
                        // DOWNLOAD
                        if ($download)
                        {
                            //$icp_download_url = $wpdb->get_row("SELECT item_key, citation, json, linkMode FROM ".$wpdb->prefix."icpress_zoteroItems WHERE api_user_id='".$icp_citation['api_user_id']."'
                            //        AND parent = '".$icp_citation["item_key"]."' AND linkMode IN ( 'imported_file', 'linked_url' ) ORDER BY linkMode ASC LIMIT 1;", OBJECT);
                            
                            if ( !is_null($icp_citation['attachment_data']) )
                            {
                                $icp_download_url = json_decode($icp_citation['attachment_data']);
                                
                                if ($icp_download_url->linkMode == "imported_file")
                                {
                                    $icp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='icp-DownloadURL' href='".ICPRESS_PLUGIN_URL."lib/request/rss.file.php?api_user_id=".$icp_citation['api_user_id']."&amp;download=".$icp_citation["attachment_key"]."'>(Download)</a> </div>" . '$2', $icp_citation['citation'], 1); // Thanks to http://ideone.com/vR073
                                }
                                else
                                {
                                    $icp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='icp-DownloadURL' href='".$icp_download_url->url."'>(Download)</a> </div>" . '$2', $icp_citation['citation'], 1);
                                }
                            }
                            unset($icp_download_url);
                        }
                        
                        
                        // CITE LINK
                        if ($cite == "yes" || $cite == "true" || $cite === true)
                        {
                            $cite_url = "https://api.zotero.org/".$icp_account->account_type."/".$icp_account->api_user_id."/items/".$icp_citation["item_key"]."?format=ris";
                            $icp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Cite in RIS Format' class='icp-CiteRIS' href='".$cite_url."'>(Cite)</a> </div>" . '$2', $icp_citation['citation'], 1);
                        }
                        
                        
                        // TITLE
                        if ($title)
                        {
                            if ($current_title == "" || (strlen($current_title) > 0 && $current_title != $icp_citation["year"]))
                            {
                                $current_title = $icp_citation["year"];
                                
                                if ($icp_citation["year"] == "0000")
                                    $icp_output .= "<h3>n.d.</h3>\n";
                                else // regular year
                                    $icp_output .= "<h3>".$current_title."</h3>\n";
                            }
                        }
						
						// HYPERLINK DOIs
						if ( isset($icp_this_meta->DOI) )
							$icp_citation['citation'] = str_replace( "doi:".$icp_this_meta->DOI, "<a href='http://dx.doi.org/".$icp_this_meta->DOI."'>doi:".$icp_this_meta->DOI."</a>", $icp_citation['citation'] );
                        
                        // SHOW CURRENT STYLE AS REL
                        $icp_citation['citation'] = str_replace( "class=\"csl-bib-body\"", "rel=\"".$icp_citation['style']."\" class=\"csl-bib-body\"", $icp_citation['citation'] );
                        
                        
                        // OUTPUT
                        
                        $icp_output .= "<div class='icp-ID-".$api_user_id."-".$icp_citation["item_key"]." icp-Entry".$has_citation_image."'>\n";
                        $icp_output .= $citation_image . $icp_citation['citation'] . $citation_abstract . $citation_tags . "\n";
                        $icp_output .= "</div><!--Entry-->\n\n";
                    }
                    
                    // DISPLAY NOTES, if any exist
                    if ( count($citation_notes) > 0 )
					{
						$icp_output .= "<div class='icp-Citation-Notes'>\n<h4>Notes</h4>\n<ol>\n";
						
						foreach ( $citation_notes as $citation_note )
	                        $icp_output .= $citation_note;
						
						$icp_output .= "</ol>\n</div><!-- .icp-Citation-Notes -->\n\n";
					}
                }
                
                // No items to display
                else
                {
                    $icp_output .= "<p>Sorry, there's no items to display.</p>\n";
                }
                
                $icp_output .= "</div><!--.icp-ICPress-->\n\n";
                
            } // end items
            
            
            
            // COLLECTIONS
            
            else if ($data_type == "collections" && !isset($_GET['zpcollection']))
            {
                $icp_query = "SELECT ".$wpdb->prefix."icpress_zoteroCollections.* FROM ".$wpdb->prefix."icpress_zoteroCollections ";
                $icp_query .= "WHERE api_user_id='".$api_user_id."' AND parent = '' ";
                
                // Sort by and sort direction
                if ($sortby)
                {
                    if ($sortby == "default")
                        $sortby = "retrieved";
                    else if ($sortby == "date" || $sortby == "author")
                        continue;
                    
                    $icp_query .= " ORDER BY ".$sortby." " . $order;
                }
                
                // Limit
                if ($limit) $icp_query .= " LIMIT ".$limit;
                
                $icp_results = $wpdb->get_results($icp_query, OBJECT); unset($icp_query);
                
                
                // DISPLAY CITATIONS
                
                $icp_output = "\n<div class=\"icp-ICPress\">\n\n";
                $icp_output .= "<span class=\"ICPRESS_PLUGIN_URL\" style=\"display:none;\">" . ICPRESS_PLUGIN_URL . "</span>\n\n";
                $icp_output .= "<ul>\n";
                
                foreach ($icp_results as $icp_collection)
                {
                    $icp_output .= "<li rel=\"" . $icp_collection->item_key . "\">";
                    if ($link == "yes")
                    {
                        $icp_output .= "<a class='icp-CollectionLink' title='" . $icp_collection->title . "' href='" . $_SERVER["REQUEST_URI"];
                        if ( strpos($_SERVER["REQUEST_URI"], "?") === false ) { $icp_output .= "?"; } else { $icp_output .= "&"; }
                        $icp_output .= "zpcollection=" . $icp_collection->item_key . "'>";
                    }
                    $icp_output .= $icp_collection->title;
                    if ($link == "yes") { $icp_output .= "</a>"; }
					
					// Place nested collections here
                    
                    if ($icp_collection->numCollections > 0)
                        $icp_output .= icp_get_subcollections($wpdb, $api_user_id, $icp_collection->item_key, $sortby, $order, $link);
					
                    $icp_output .= "</li>\n";
                }
                
                $icp_output .= "</ul>\n";
                $icp_output .= "</div><!--.icp-ICPress-->\n\n";
                
            } // end collections
            
            
            
            // TAGS
            
            else if ($data_type == "tags" && !isset($_GET['zptag']))
            {
                $icp_query = "SELECT * FROM ".$wpdb->prefix."icpress_zoteroTags WHERE api_user_id='".$api_user_id."' ";
                
                // Sort by and sort direction
                if ($sortby)
                {
                    if ($sortby == "default") $sortby = "retrieved";
                    else if ($sortby == "date" || $sortby == "author") continue;
                    
                    $icp_query .= " ORDER BY ".$sortby." " . $order;
                }
                
                // Limit
                if ($limit) $icp_query .= " LIMIT ".$limit;
                
                $icp_results = $wpdb->get_results($icp_query, OBJECT); unset($icp_query);
                
                
                // DISPLAY CITATIONS
                
                $icp_output = "\n<div class=\"icp-ICPress\">\n\n";
                $icp_output .= "<span class=\"ICPRESS_PLUGIN_URL\" style=\"display:none;\">" . ICPRESS_PLUGIN_URL . "</span>\n\n";
                $icp_output .="<ul>\n";
                
                foreach ($icp_results as $icp_tag)
                {
                    $icp_output .= "<li rel=\"" . $icp_tag->title . "\">";
                    if ($link == "yes")
                    {
                        $icp_output .= "<a class='icp-TagLink' title='" . $icp_tag->title . "' rel='" . $icp_tag->title . "' href='" . $_SERVER["REQUEST_URI"];
                        if ( strpos($_SERVER["REQUEST_URI"], "?") === false ) { $icp_output .= "?"; } else { $icp_output .= "&"; }
                        $icp_output .= "zptag=" . urlencode($icp_tag->title) . "'>";
                    }
                    $icp_output .= $icp_tag->title . " <span class=\"icp-numItems\">(" . $icp_tag->numItems . " items)</span>";
                    if ($link == "yes") { $icp_output .= "</a>"; }
                    $icp_output .= "</li>\n";
                }
                
                $icp_output .="</ul>\n";
                $icp_output .= "</div><!--.icp-ICPress-->\n\n";
                
            } // end tags
            
            
            // FINISH UP
            
            // Clean up
            $wpdb->flush(); unset($icp_results);
            
            // Show theme scripts
            $GLOBALS['icp_is_shortcode_displayed'] = true;
            
            return $icp_output;
        }
        
        
        // Display notification if no citations found
        else
        {
            return "\n<div id='".$icp_instance_id."' class='icp-ICPress'>Sorry, no citation(s) found.</div>\n";
        }
    }
    

    
?>