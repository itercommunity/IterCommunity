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
	
	require_once("../import/import.functions.php");
	
	
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

    $zpSearch = array();

	// Generally held search results outside of those stored by Zotero
	// The MARC records from an API call
	
	// temporarily muted until the source is fleshed out
	if (1 == 1) {
			$doc_incr = 0;
			$vufind_url = "http://iter-id.utsc.utoronto.ca:8080/solr/biblio/select?q=".sanitize_text_field( $_GET['term'] );	
			$context  = stream_context_create(array('http' => array('header' => 'Accept: application/xml')));
			$xml = file_get_contents($vufind_url, false, $context);
			$xml = simplexml_load_string($xml);
			
			$author_incr = array("","2","3","4","5");
			
			foreach ($xml->result->doc as $doc) {	
				// print "<hr/>";
				$label = "";	

				// look at strings "str"
				foreach ($doc->str as $string) {
				
					$asstring = (string) $string;	
					$asarray = (array) $string;			
					$attr = $asarray['@attributes']['name'];

					// print __LINE__."<br/><pre><i>".print_r($string, TRUE)."</i>\n and $asstring and $attr</pre> <hr/>";																			

					unset($string['@attributes']);
		
					// id
					if ($attr == "id") {
						$id = $asstring; 
					}	
					elseif ($attr == "callnumber") {
						$id = $asstring; 
					} 
					elseif ($attr == "callnumber-a") {
						$id = $asstring; 
					}
					
					// title
					
					if (($attr == "title_full") && ($label == "")) {
						$label = $asstring; 
					}	
				}
				
				// look at arrays "arr"		
				
				$year = "";
				$itemType = "";
				$citation = "";
				
				foreach ($doc->arr as $array) {
					$array = (array) $array;
					$attr = $array['@attributes'];
					unset($array['@attributes']);
					$output[$doc_incr][$attr['name']] = $array; 
					// print __LINE__."<br/><pre><i>".print_r($array, TRUE)."</i>\n and $attr</pre> <hr/>";	
									
					$author = ""; 
					$comma = "";
					for ($author_num = 0; $author_num < 5; $author_num++) {
						if (array_key_exists('author'.$author_incr[$author_num],$output[$doc_incr])) {
							$author .= $comma.implode(", ", $output[$doc_incr]['author'.$author_incr[$author_num]]['str']); 
							$comma = ", ";
						}						
					}
					if (($attr['name'] == "spellingShingle") && ($label == "")) {
						$label = trim(implode(" ", $array['str']));
					}
					

					if (($attr['name'] == "spelling") && ($citation == "")) {
						$citation = trim(implode(" ", $array['str']));
					}
					
					
					if (($year == "") && ($attr['name'] == 'publishDate')) {
						$year = $array['str'];
					}

					if (($itemType == "") && ($attr['name'] == 'format')) {
						$itemType = $array['str'];
					}
															
					if (($year == "") && (is_array($array['str'])) && (preg_match('/\((\d{4})\)/', implode(" ", $array['str']), $matches)))  {	
						$year = $matches[1];
					}
				}
								
				$params = array(1 => $id, 2 => date("c"), 3 => json_encode($doc), 4 => $author, 5 => $year, 6 => $year, 7 => $label, 8 => $itemType, 9 => '', 10 => $citation,
						11 => '', 12 => 0, 13 => 0);
				
				icp_save_marc_items($wpdb,  $icp_api_user_id, $params);
				array_push( $zpSearch, array( "id"=> $id, "author" => $author, "label" => $label, "value" => $id, "source" => 'MARC') );
				
				// print "<pre>".print_r($output[$doc_incr], TRUE)."</pre> <hr/>";
				$doc_incr++;				
			}			
		}


    // Zotero search results 
    // - maybe allow a bleed out and come back
    //   - search ALL records and couple those selected with a given user
    //   - change the zotero schema (admin.install)
    $zpSearchResults = $wpdb->get_results(
        $wpdb->prepare( 
            "
                SELECT iczItems.id, iczItems.author, iczItems.json, CONCAT(' (', iczItems.year, '). ', iczItems.title, '.') AS label, iczItems.item_key AS value FROM ".$wpdb->prefix."icpress_zoteroItems iczItems 
                LEFT JOIN  ".$wpdb->prefix."icpress_zoteroUserItems iczUserItems ON iczUserItems.id = iczItems.id WHERE iczItems.json LIKE %s 
                AND iczItems.itemType NOT IN ('attachment', 'note') AND (iczUserItems.api_user_id='".$icp_api_user_id."' OR iczUserItems.api_user_id IS NULL)
                ORDER BY iczItems.author ASC
            ", 
            '%' . $wpdb->esc_like($_GET['term']) . '%'
    ), OBJECT );
        
    if ( count($zpSearchResults) > 0 )
    {
        foreach ( $zpSearchResults as $zpResult )
        {
            // Deal with author
            
            $id = -1;
            if (isset($zpResult->id)) {
            	$id = $zpResult->id;
            }
            
            $author = "";
            if (isset($zpResult->author)) {
            	$author = $zpResult->author;
            }            
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