<?php

    function ICPress_icpressInText ($atts)
    {
        /*
        *   GLOBAL VARIABLES
        *
        *   $GLOBALS['icp_shortcode_instances'] {instantiated previously}
        *
        */
        
        extract(shortcode_atts(array(
            
            'item' => false,
            'items' => false,
            
            'pages' => false,
            'format' => "(%a%, %d%, %p%)",
			'brackets' => false,
            'etal' => false, // default (false), yes, no
            'separator' => false, // default (comma), semicolon
            'and' => false, // default (no), and, comma-and
            
            'userid' => false,
            'api_user_id' => false,
            'nickname' => false,
            'nick' => false
            
        ), $atts));
        
        
        
        // PREPARE ATTRIBUTES
        
        if ($items) $items = str_replace('"','',html_entity_decode($items));
        else if ($item) $items = str_replace('"','',html_entity_decode($item));
        
        $pages = str_replace('"','',html_entity_decode($pages));
        $format = str_replace('"','',html_entity_decode($format));
        $brackets = str_replace('"','',html_entity_decode($brackets));
        
        $etal = str_replace('"','',html_entity_decode($etal));
        if ($etal == "default") { $etal = false; }
        
        $separator = str_replace('"','',html_entity_decode($separator));
        if ($separator == "default") { $separator = false; }
        
        $and = str_replace('"','',html_entity_decode($and));
        if ($and == "default") { $and = false; }
        
        if ($userid) { $api_user_id = str_replace('"','',html_entity_decode($userid)); }
        if ($nickname) { $nickname = str_replace('"','',html_entity_decode($nickname)); }
        if ($nick) { $nickname = str_replace('"','',html_entity_decode($nick)); }
        
        
        
        // GET ACCOUNTS
        
        global $wpdb;
        
        $icp_account = false;
        
        if ($nickname !== false)
        {
            $icp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress WHERE nickname='".$nickname."'", OBJECT);
            $api_user_id = $icp_account->api_user_id;
        }
        else if ($api_user_id !== false)
        {
            $icp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress WHERE api_user_id='".$api_user_id."'", OBJECT);
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
        $icp_instance_id = "icpress-".md5($api_user_id.$nickname.$pages.$items.$format);
        
        if ($items !== false)
        {
            
            // PREPARE ITEM KEYS: Single, with or without curly bracket, or multiple
            
            if (strpos($items, "{") !== false)
            {
                if (strpos($items, "},") !== false)
                {
                    $items = explode("},", $items);
                    foreach ($items as $id => $item) $items[$id] = explode(",", str_replace("{", "", str_replace("}", "", $item)));
                }
                else
                {
                    $items = str_replace("{", "", str_replace("}", "", $items));
                    if (strpos($items, ",") !== false) $items = explode(",", $items);
                }
            }
            
            
            // PREPARE ITEM QUERY
            
            $icp_query = "SELECT items.*, ".$wpdb->prefix."icpress_zoteroItemImages.image AS itemImage ";
            
            $icp_query .= "FROM ".$wpdb->prefix."icpress_zoteroItems AS items ";
            $icp_query .= "LEFT JOIN ".$wpdb->prefix."icpress_zoteroItemImages
								ON items.item_key=".$wpdb->prefix."icpress_zoteroItemImages.item_key
								AND items.api_user_id=".$wpdb->prefix."icpress_zoteroItemImages.api_user_id ";
            
            $icp_query .= "WHERE items.api_user_id='".$api_user_id."' AND ";
            
            /*$icp_citation_attr =
                array(
                    'posts_per_page' => -1,
                    'post_type' => 'icp_entry',
                    'meta_key' => 'author',
                    'orderby' => 'meta_value',
                    'order' => 'ASC',
                    'meta_query' => ''
                );
                
            $icp_citation_meta_query =
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'api_user_id',
                        'value' => $api_user_id,
                        'compare' => '='
                    )
                );*/
            
            if ( is_array($items) )
            {
                if ( count($items) == 2 && !is_array($items[0]) )
                {
                    $icp_query .= " items.item_key='" . $items[0] . "'";
                    /*array_push( $icp_citation_meta_query,
                            array(
                                'key' => 'item_key',
                                'value' => $items[0],
                                'compare' => '='
                            )
                        );*/
                }
                else
                {
                    $icp_query .= " items.item_key IN ( ";
                    foreach ($items as $id => $item)
                    {
                        $icp_query .= "'" . $item[0] . "'";
                        if (count($items)-1 != $id) $icp_query .= ",";
                        /*array_push( $icp_citation_meta_query,
                            array(
                                'key' => 'item_key',
                                'value' => $items[0],
                                'compare' => '='
                            )
                        );*/
                    }
                    $icp_query .=" )";
                }
            }
            else // single item
            {
                $icp_query .= " items.item_key='" . $items . "'";
                /*array_push( $icp_citation_meta_query,
                    array(
                        'key' => 'item_key',
                        'value' => $items,
                        'compare' => '='
                    )
                );*/
            }
            
            //$icp_citation_attr['meta_query'] = $icp_citation_meta_query;
            $icp_query .= " ORDER BY items.author ASC, items.zpdate ASC;";
            
            
            
            // QUERY DATABASE
            //var_dump($icp_query);
            $icp_results = $wpdb->get_results($icp_query, OBJECT);
            //var_dump($icp_results);
            
            $icp_intext_citation = ""; // Output for display
			$icp_intext_citation_arr = array(); // Array for sorting
			
            
            
            // FORMAT IN-TEXT CITATION
			
			$prev_num = 1;
            
            foreach ($icp_results as $id => $item)
            {
                $icp_json = json_decode( $item->json );
                
                // Determine author if "author" doesn't exist
                if ( trim($item->author) == "" )
                {
					if ( isset($icp_json->creators) && count($icp_json->creators) > 0 )
					{
						foreach ( $icp_json->creators as $i => $icp_creator )
						{
							$item->author = $icp_creator->name;
							if ( $i != (count($icp_json->creators)-1) ) $item->author .= ", ";
						}
					}
					else // assume no author exists; use title instead
					{
						$item->author .= "\"" . $item->title . "\"";
					}
                }
                
                // Shorten author ...
                if ($etal)
                {
                    if ($etal == "yes") $item->author = substr($item->author, 0, strpos($item->author, ",")) . " <em>et al.</em>";
                }
                else // default
                {
                    if (isset($GLOBALS['icp_shortcode_instances'][get_the_ID()][$api_user_id.",".$item->item_key])
                            && count(explode(",", $item->author)) > 3)
                    {
                        $item->author = substr($item->author, 0, strpos($item->author, ",")) . " <em>et al.</em>";
                    }
                }
                
                // Deal with 'and' => false, // default (no), and, comma-and
                if ($and)
                {
                    if ($and == "and")
                    {
                        if ( strrpos($item->author, ",") !== false )
                            $item->author = substr_replace( $item->author, " and", strrpos($item->author, ","), 1 );
                    }
                    else if ($and == "comma-and")
                    {
                        if ( strrpos($item->author, ",") !== false )
                            $item->author = substr_replace( $item->author, ", and", strrpos($item->author, ","), 1 );
                    }
                }
                
                // Determine %num%
                // Determine if this citation has already been referenced
                $num = false;
                if (isset($GLOBALS['icp_shortcode_instances'][get_the_ID()]) && count($icp_results) >= 1)
                {
                    $numloop = 1;
                    foreach ($GLOBALS['icp_shortcode_instances'][get_the_ID()] as $position => $instance)
                    {
                        if ($position == $api_user_id.",".$item->item_key)
                        {
                            $num = $numloop;
                            break;
                        }
                        $numloop++;
                    }
                }
                
                // Determine what %num% is if not already referenced
                if ($num === false)
                    if (isset($GLOBALS['icp_shortcode_instances'][get_the_ID()]))
                        $num = count($GLOBALS['icp_shortcode_instances'][get_the_ID()])+1;
                    else
                        $num = 1;
                
                // Fill in author, date and number
                $citation = str_replace("%num%", $num, str_replace("%a%", $item->author, str_replace("%d%", icp_get_year($item->zpdate, true), $format)));
                
                // Deal with pages
                if ($pages)
                {
                    $citation = str_replace("%p%", $pages, $citation);
                }
                else // New way
                {
                    if (is_array($items))
                    {
                        if (count($items) == 2 && !is_array($items[0]))
                        {
                            $citation = str_replace("%p%", $items[1], $citation);
                        }
                        else 
                        {
							// Multiple citations -- shouldn't have page numbers
                            //if (isset($items[$id][1])) {
                            //    $citation =  str_replace("%p%", $items[$id][1], $citation);
                            //}
                            //else {
                                $citation = str_replace("%p%", "", str_replace(" %p%", "", str_replace(", %p%", "", $citation)));
                            //}
                        }
                    }
                    else // No pages
                    {
                        $citation = str_replace("%p%", "", str_replace(" %p%", "", str_replace(", %p%", "", $citation)));
                    }
                }
                
                // Format for multiple (only expected characters)
                if (count($icp_results) > 1)
                {
                    if ($id == 0)
                        $citation = str_replace("&#93;", "", str_replace(")", "", $citation));
                    else if ($id == (count($icp_results)-1))
                        $citation = str_replace("&#91;", "", str_replace("(", " ", $citation));
                    else
                        $citation = str_replace("&#93;", "", str_replace("&#91;", "", str_replace(")", "", str_replace("(", " ", $citation))));
                }
				
				// Deal with download
				$item_download = false; if (isset($item->attachment_data)) $item_download = $item->attachment_data;
				$item_download_key = false; if (isset($item->attachment_key)) $item_download_key = $item->attachment_key;
                
				// SET SORT ARRAY
				$icp_intext_citation_arr[$api_user_id.",".$item->item_key] = array(
                        "instance_id" => $icp_instance_id,
                        "api_user_id" => $api_user_id,
                        "item_key" => $item->item_key,
                        "author" => $item->author,
                        "title" => $item->title,
                        "zpdate" => icp_get_year($item->zpdate),
                        "citation" => $citation,
						"alphacount" => ""
                    );
				
                // SET BIBLIOGRAPHY CITATIONS: Per item
                $GLOBALS['icp_shortcode_instances'][get_the_ID()][$api_user_id.",".$item->item_key] = array(
                        "instance_id" => $icp_instance_id,
                        "userid" => $api_user_id,
                        "account_type" => $icp_account->account_type,
                        "public_key" => $icp_account->public_key,
                        "item_key" => $item->item_key,
                        "author" => $item->author,
                        "title" => $item->title,
                        "date" => icp_get_year($item->zpdate),
                        "download" => $item_download,
                        "download_key" => $item_download_key,
                        "image" => $item->itemImage,
                        "json" => $item->json,
                        "citation" => $item->citation,
                        "style" => $item->style,
						"alphacount" => ""
                    );
            }
			
			// First, sort in-text items
			//$icp_intext_citation_arr = subval_sort($icp_intext_citation_arr, "author", "asc");
			$icp_intext_citation_output_arr = array();
			
			$icp_alphacount = "";
			$icp_alphacount_author = "";
			
			// Then build output array
			
			foreach ( $icp_intext_citation_arr as $id => $item_arr )
				$icp_intext_citation_output_arr[count($icp_intext_citation_output_arr)] = $item_arr;
            
			
			foreach ( $icp_intext_citation_output_arr as $i => $item )
			{
				$icp_alphacount_this = "";
				
				if ( isset($icp_intext_citation_output_arr[$i+1]["author"])
						&& $item["author"] == $icp_intext_citation_output_arr[$i+1]["author"]
						&& $item["zpdate"] == $icp_intext_citation_output_arr[$i+1]["zpdate"] )
				{
					if ( $icp_alphacount == "" )
						$icp_alphacount_this = "a";
					else
						if ( $icp_alphacount_author != $item["author"] )
							$icp_alphacount_this = "a";
						else
							$icp_alphacount_this = ++$icp_alphacount;
					
					$icp_alphacount_author = $item["author"];
					
					// Update the counts on this and the next one
					$item["alphacount"] = $icp_alphacount_this;
					$GLOBALS['icp_shortcode_instances'][get_the_ID()][$item["api_user_id"].",".$item["item_key"]]["alphacount"] = $icp_alphacount_this;
					$GLOBALS['icp_shortcode_instances'][get_the_ID()][$icp_intext_citation_output_arr[$i+1]["api_user_id"].",".$icp_intext_citation_output_arr[$i+1]["item_key"]]["alphacount"] = ++$icp_alphacount_this;
					
					$icp_alphacount = $icp_alphacount_this;
				}
				
				$item["alphacount"] = $GLOBALS['icp_shortcode_instances'][get_the_ID()][$icp_intext_citation_output_arr[$i]["api_user_id"].",".$icp_intext_citation_output_arr[$i]["item_key"]]["alphacount"];
				
				$icp_intext_citation .= "<a title='";
				
				if ($item["author"])
				{
					// Remove author if same in a row
					if ( isset($icp_intext_citation_output_arr[$i-1]["author"])
							&& $item["author"] == $icp_intext_citation_output_arr[$i-1]["author"] )
						$item["citation"] = str_replace( $item["author"] . ", ", "", $item["citation"] );
					
					$icp_intext_citation .= htmlspecialchars(strip_tags($item["author"]), ENT_QUOTES) . " ";
				}
				else { $item["author"] = $item["title"]; $icp_intext_citation .= "No author "; }
				
				if ($item["zpdate"])
				{
					$icp_intext_citation .= "(".$item["zpdate"].$item["alphacount"]."). ";
					$item["citation"] = str_replace( $item["zpdate"], $item["zpdate"].$item["alphacount"], $item["citation"]);
				}
				
				$icp_intext_citation .= htmlspecialchars(strip_tags($item["title"]), ENT_QUOTES) . ".' id='".$item["instance_id"]."' class='icp-ICPressInText' href='#icp-".get_the_ID()."-".$item["item_key"]."'>" . $item["citation"] . "</a>";
				$icp_intext_citation = str_replace( "al..", "al.", $icp_intext_citation);
				
				// Determine delineation for multiple citations
				if ( count($icp_intext_citation_arr) > 1 && $i != (count($icp_intext_citation_arr)-1) )
					if ( $separator && $separator == "comma" )
						$icp_intext_citation .= ",";
					else
						if ( isset($icp_intext_citation_output_arr[$i+1]["author"])
								&& $item["author"] == $icp_intext_citation_output_arr[$i+1]["author"] )
							$icp_intext_citation .= ",";
						else
							if ( $brackets )
								$icp_intext_citation .= ", ";
							else
								$icp_intext_citation .= ";";
			}
			
			// Add brackets, if necessary
			if ( $brackets ) $icp_intext_citation = "&#91;" . $icp_intext_citation . "&#93;";
			
            return $icp_intext_citation;
            
            unset($icp_query);
            unset($icp_results);
            unset($icp_intext_citation);
            unset($icp_intext_citation_arr);
            unset($icp_intext_citation_output_arr);
            
            $wpdb->flush();
        }
        
        // Display notification if no citations found
        else
        {
            return "\n<div id='".$icp_instance_id."' class='icp-ICPress'>Sorry, no citation(s) found.</div>\n";
        }
        
        // Show theme scripts
        $GLOBALS['icp_is_shortcode_displayed'] = true;
        
    }

    
?>