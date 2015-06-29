<?php

    function ICPress_icpressInTextBib ($atts)
    {
        /*
        *   RELIES ON THESE GLOBAL VARIABLES:
        *
        *   $GLOBALS['icp_shortcode_instances'][get_the_ID()] {instantiated previously}
        *   
        */
        
        extract(shortcode_atts(array(
            'style' => false,
            'sortby' => "default",
            'sort' => false,
            'order' => false,
            
            'image' => false,
            'images' => false,
            'showimage' => "no",
            
            'showtags' => "no",
            'title' => "no",
            'download' => "no",
            'downloadable' => false,
            'notes' => false,
            'abstract' => false,
            'abstracts' => false,
            'cite' => false,
            'citeable' => false,
            'target' => false,
            'forcenumber' => false
        ), $atts));
        
        
        
        // FORMAT PARAMETERS
        $style = str_replace('"','',html_entity_decode($style));
        $sortby = str_replace('"','',html_entity_decode($sortby));
        
        if ($order) $order = str_replace('"','',html_entity_decode($order));
        else if ($sort) $order = str_replace('"','',html_entity_decode($sort));
        else $order = "ASC";
        
        // Show image
        if ($showimage) $showimage = str_replace('"','',html_entity_decode($showimage));
        if ($image) $showimage = str_replace('"','',html_entity_decode($image));
        if ($images) $showimage = str_replace('"','',html_entity_decode($images));
        
        if ($showimage == "yes" || $showimage == "true" || $showimage === true) $showimage = true;
        else $showimage = false;
        
        // Show tags
        if ($showtags == "yes" || $showtags == "true" || $showtags === true) $showtags = true;
        else $showtags = false;
        
        $title = str_replace('"','',html_entity_decode($title));
        
        if ($download) $download = str_replace('"','',html_entity_decode($download));
        else if ($downloadable) $download = str_replace('"','',html_entity_decode($downloadable));
        if ($download == "yes" || $download == "true" || $download === true) $download = true; else $download = false;
        
        $notes = str_replace('"','',html_entity_decode($notes));
        
        if ($abstracts) $abstracts = str_replace('"','',html_entity_decode($abstracts));
        else if ($abstract) $abstracts = str_replace('"','',html_entity_decode($abstract));
        
        if ($cite) $cite = str_replace('"','',html_entity_decode($cite));
        else if ($citeable) $cite = str_replace('"','',html_entity_decode($citeable));
        
        if ($target == "new" || $target == "yes" || $target == "_blank" || $target == "true" || $target === true) $target = true;
        else $target = false;
        
        if ($forcenumber == "yes" || $forcenumber == "true" || $forcenumber === true)
        $forcenumber = true; else $forcenumber = false;
        
        
        // SORT BY AND SORT ORDER
        if ( $sortby != "default" && isset($GLOBALS['icp_shortcode_instances'][get_the_ID()]) )
            $GLOBALS['icp_shortcode_instances'][get_the_ID()] = subval_sort( $GLOBALS['icp_shortcode_instances'][get_the_ID()], $sortby, $order );
        
        // TITLE: Sort by date and add headings
        if ( ( strtolower($title) == "yes" || strtolower($title) == "true" )  && isset($GLOBALS['icp_shortcode_instances'][get_the_ID()]) )
            $GLOBALS['icp_shortcode_instances'][get_the_ID()] = subval_sort( $GLOBALS['icp_shortcode_instances'][get_the_ID()], "date", $order );
        
        
        // DISPLAY IN-TEXT BIBLIOGRAPHY
        
        $current_title =  "";
        $citation_abstract = "";
        $citation_tags = "";
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
        
        if ( isset($GLOBALS['icp_shortcode_instances'][get_the_ID()]) )
        {
            foreach ($GLOBALS['icp_shortcode_instances'][get_the_ID()] as $item => $icp_citation)
            {
                $citation_image = false;
                $has_citation_image = false;
                $icp_this_meta = json_decode( $icp_citation["json"] );
                $icp_output .= "<span class=\"icp-ICPress-Userid\" style=\"display:none;\">".$icp_citation['userid']."</span>\n\n";
                
                // AUTOUPDATE
                //if (!isset($_SESSION['icp_session'][$icp_citation['userid']]['key']))
                //    $_SESSION['icp_session'][$icp_citation['userid']]['key'] = substr(number_format(time() * rand(),0,'',''),0,10); /* Thanks to http://elementdesignllc.com/2011/06/generate-random-10-digit-number-in-php/ */
                //$icp_output .= "<span class=\"ICPRESS_AUTOUPDATE_KEY\" style=\"display:none;\">" . $_SESSION['icp_session'][$icp_citation['userid']]['key'] . "</span>\n\n";
                
                // IMAGE
                if ($showimage == "yes" && is_null($icp_citation["image"]) === false && $icp_citation["image"] != "")
                {
                    if ( is_numeric($icp_citation["image"]) )
                    {
                        $icp_citation["image"] = wp_get_attachment_image_src( $icp_citation["image"], "full" );
                        $icp_citation["image"] = $icp_citation["image"][0];
                    }
                    
                    $citation_image = "<div id='icp-Citation-".$icp_citation["item_key"]."' class='icp-Entry-Image'>";
                    $citation_image .= "<img src='".$icp_citation["image"]."' alt='image' />";
                    $citation_image .= "</div>\n";
                    $has_citation_image = " icp-HasImage";
                }
                
                // TAGS
                // Grab tags associated with item
                if ( $showtags )
                {
                    global $wpdb;
                    
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
                    unset($icp_showtags_results);
                    unset($icp_showtags_query);
                }
                
                // ABSTRACT
                if ($abstracts)
                {
                    if (isset($icp_this_meta->abstractNote) && strlen(trim($icp_this_meta->abstractNote)) > 0)
                    {
                        $citation_abstract = "<p class='icp-Abstract'><span class='icp-Abstract-Title'>Abstract:</span> " . sprintf($icp_this_meta->abstractNote) . "</p>\n";
                    }
                }
                
                // NOTES
                if ($notes == "yes")
                {
                    global $wpdb;
                    
                    $icp_notes = $wpdb->get_results("SELECT json FROM ".$wpdb->prefix."icpress_zoteroItems WHERE api_user_id='".$icp_citation['userid']."'
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
                }
                
                // Hyperlink URL: Has to go before Download
                if (isset($icp_this_meta->url) && strlen($icp_this_meta->url) > 0)
                {
                    $icp_url_replacement = "<a title='".$icp_this_meta->title."' rel='external' ";
                    if ( $target ) $icp_url_replacement .= "target='_blank' ";
                    $icp_url_replacement .= "href='".urldecode(urlencode(htmlentities($icp_this_meta->url)))."'>".urldecode(urlencode(htmlentities($icp_this_meta->url)))."</a>";
                    
                    // Replace ampersands
                    $icp_citation['citation'] = str_replace(htmlspecialchars($icp_this_meta->url), $icp_this_meta->url, $icp_citation['citation']);
                    
                    // Then replace with linked URL
                    $icp_citation['citation'] = str_replace($icp_this_meta->url, $icp_url_replacement, $icp_citation['citation']);
                }
                
                // DOWNLOAD
                if ( $download )
                {
                    global $wpdb;
                    //
                    //$icp_download_url = $wpdb->get_row("SELECT item_key, citation, json, linkMode FROM ".$wpdb->prefix."icpress_zoteroItems WHERE api_user_id='".$icp_citation['userid']."'
                    //        AND parent = '".$icp_citation["item_key"]."' AND linkMode IN ( 'imported_file', 'linked_url' ) ORDER BY linkMode ASC LIMIT 1;", OBJECT);
                    
                    //$icp_download_url = json_decode($icp_citation["download"]);
                    
                    $icp_download = $wpdb->get_results(
                            "
                            SELECT * FROM 
                            ( 
                                SELECT 
                                ".$wpdb->prefix."icpress_zoteroItems.parent AS parent,
                                ".$wpdb->prefix."icpress_zoteroItems.citation AS content,
                                ".$wpdb->prefix."icpress_zoteroItems.item_key AS item_key,
                                ".$wpdb->prefix."icpress_zoteroItems.json AS data,
                                ".$wpdb->prefix."icpress_zoteroItems.linkmode AS linkmode 
                                FROM ".$wpdb->prefix."icpress_zoteroItems 
                                WHERE api_user_id='".$icp_citation["userid"]."'
                                AND ".$wpdb->prefix."icpress_zoteroItems.parent = '".$icp_citation["item_key"]."' 
                                AND ".$wpdb->prefix."icpress_zoteroItems.linkmode IN ( 'imported_file', 'linked_url' ) 
                                ORDER BY linkmode ASC 
                            )
                            AS attachments_sub 
                            GROUP BY parent;
                            "
                            , OBJECT
                        );
                    
                    if ( count($icp_download) > 0 )
                    {
                        $icp_download_url = json_decode($icp_download[0]->data);
                        
                        if ($icp_download_url->linkMode == "imported_file")
                        {
                            $icp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='icp-DownloadURL' href='".ICPRESS_PLUGIN_URL."lib/request/rss.file.php?api_user_id=".$icp_citation['api_user_id']."&amp;download=".$icp_citation["attachment_key"]."'>(Download)</a> </div>" . '$2', $icp_citation['citation'], 1); // Thanks to http://ideone.com/vR073
                        }
                        else
                        {
                            $icp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Download URL' class='icp-DownloadURL' href='".$icp_download_url->url."'>(Download)</a> </div>" . '$2', $icp_citation['citation'], 1);
                        }
                        
                        unset($icp_download_url);
                        unset($icp_download);
                    }
                }
                
                // CITE LINK
                if ($cite == "yes" || $cite == "true" || $cite === true)
                {
                    $cite_url = "https://api.zotero.org/".$icp_citation["account_type"]."/".$icp_citation['userid']."/items/".$icp_citation["item_key"]."?format=ris";
                    $icp_citation['citation'] = preg_replace('~(.*)' . preg_quote('</div>', '~') . '(.*?)~', '$1' . " <a title='Cite in RIS Format' class='icp-CiteRIS' href='".$cite_url."'>(Cite)</a> </div>" . '$2', $icp_citation['citation'], 1);
                }
                
                // TITLE
                if (strtolower($title) == "yes" || strtolower($title) == "true")
                {
                    if ($current_title == "" || (strlen($current_title) > 0 && $current_title != $icp_citation["date"]))
                    {
                        $current_title = $icp_citation["date"];
                        $icp_output .= "<h3>".$current_title."</h3>\n";
                    }
                }
                
                // HYPERLINK DOIs
                if ( isset($icp_this_meta->DOI) )
                    $icp_citation['citation'] = str_replace( "doi:".$icp_this_meta->DOI, "<a href='http://dx.doi.org/".$icp_this_meta->DOI."'>doi:".$icp_this_meta->DOI."</a>", $icp_citation['citation'] );
                    
                // SHOW CURRENT STYLE AS REL
                $icp_citation['citation'] = str_replace( "class=\"csl-bib-body\"", "rel=\"".$icp_citation['style']."\" class=\"csl-bib-body\"", $icp_citation['citation'] );
                
                // Add alphabetical dates
                $icp_citation['citation'] = str_replace( $icp_citation["date"], $icp_citation["date"].$icp_citation["alphacount"], $icp_citation['citation'] );
                
                // OUTPUT
                $icp_output .= "<a title='Reference to citation for `".$icp_citation["title"]."`' id='icp-".get_the_ID()."-".$icp_citation["item_key"]."'></a><div class='icp-ID-".$icp_citation['userid']."-".$icp_citation["item_key"]." icp-Entry".$has_citation_image."'>\n";
                $icp_output .= $citation_image . $icp_citation['citation'] . $citation_abstract . $citation_tags . "\n";
                $icp_output .= "</div><!--Entry-->\n\n";
            }
        }
        
        // DISPLAY NOTES, if exist
        if ( count($citation_notes) > 0 )
        {
            $icp_output .= "<div class='icp-Citation-Notes'>\n<h4>Notes</h4>\n<ol>\n";
            
            foreach ( $citation_notes as $citation_note )
                $icp_output .= $citation_note;
            
            $icp_output .= "</ol>\n</div><!-- .icp-Citation-Notes -->\n\n";
        }
        
        $icp_output .= "</div><!--.icp-ICPress-->\n\n";
        
        // Show theme scripts
        $GLOBALS['icp_is_shortcode_displayed'] = true;
        
        return $icp_output;
    }

?>