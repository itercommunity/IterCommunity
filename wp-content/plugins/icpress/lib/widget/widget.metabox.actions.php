<?php


    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);

    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('ICPress: Access Denied') );

    // Include import functions
    require_once("../import/import.functions.php");
    
    // Set up XML document
    $xml = "";
    
    
    if (isset($_GET['submit']))
    {
        // Set up error array
        $errors = array("account_empty"=>array(0,"<strong>Account</strong> was left blank."),
                                "account_format"=>array(0,"<strong>Account</strong> was incorrectly formatted."),
                                "editor_empty"=>array(0,"<strong>Editor</strong> was left blank."),
                                "editor_format"=>array(0,"<strong>Editor</strong> was incorrectly formatted."),
                                "style_empty"=>array(0,"<strong>Style</strong> was left blank."),
                                "style_format"=>array(0,"<strong>Style</strong> was incorrectly formatted."),
                                "reset_empty"=>array(0,"<strong>Reset</strong> was left blank."),
                                "reset_format"=>array(0,"<strong>Reset</strong> was incorrect."),
                                "cpt_empty"=>array(0,"<strong>Reference Widget</strong> was left blank."),
                                "cpt_format"=>array(0,"<strong>Reference Widget</strong> was incorrect."),
                                "autoupdate_empty"=>array(0,"<strong>Autoupdate</strong> was left blank."),
                                "autoupdate_format"=>array(0,"<strong>Autoupdate</strong> was incorrectly formatted."),
                                "post_empty"=>array(0,"<strong>Post ID</strong> was left blank."),
                                "post_format"=>array(0,"<strong>Post ID</strong> was incorrectly formatted."));
        
        
        
        /*
         
            SET DEFAULT ACCOUNT
            
        */
        
        if (isset($_GET['account']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['account']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['account'])) == 1)
                    $account = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['account']))));
                else
                    $errors['account_format'][0] = 1;
            else
                $errors['account_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("ICPress_DefaultAccount", $account);
                $xml .= "<result success='true' account='".$account."' />\n";
            }
        } // default account
        
        
        
        /*
         
            SET REFERENCE WIDGET
            
        */
        
        else if (isset($_GET['cpt']))
        {
            // Check the post variables and record errors
            if (trim($_GET['cpt']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_,]+$/', stripslashes($_GET['cpt'])) == 1)
                    $cpt = trim($_GET['cpt']);
                else
                    $errors['account_format'][0] = 1;
            else
                $errors['account_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("ICPress_DefaultCPT", $cpt);
                $xml .= "<result success='true' cpt='".$cpt."' />\n";
            }
        } // default reference widget
        
        
        
        /*
         
            SET DEFAULT FOR EDITOR FEATURES
            
        */
        
        else if (isset($_GET['editor']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['editor']) != '')
                if (preg_match('/^[\'a-zA-Z _]+$/', stripslashes($_GET['editor'])) == 1)
                    $editor = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['editor']))));
                else
                    $errors['editor_format'][0] = 1;
            else
                $errors['editor_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("ICPress_DefaultEditor", $editor);
                $xml .= "<result success='true' editor='".$editor."' />\n";
            }
        } // default editor features
        
        
        
        /*
         
            SET AUTOUPDATE
            
        */
        
        else if (isset($_GET['autoupdate']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['autoupdate']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['autoupdate'])) == 1)
                    $autoupdate = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['autoupdate']))));
                else
                    $errors['autoupdate_format'][0] = 1;
            else
                $errors['autoupdate_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                update_option("ICPress_DefaultAutoUpdate", strtolower($autoupdate));
                $xml .= "<result success='true' autoupdate='".strtolower($autoupdate)."' />\n";
            }
        } // autoupdate
        
        
        
        /*
         
            SET RESET
            
        */
        
        else if (isset($_GET['reset']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['reset']) == 'true')
                $reset = $_GET['reset'];
            else
                $errors['reset_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                global $wpdb;
                global $current_user;
                
                // Drop all tables except accounts/main
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_oauth;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroItems;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroCollections;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroTags;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroRelItemColl;");
                $wpdb->query("DROP TABLE IF EXISTS ".$wpdb->prefix."icpress_zoteroRelItemTags;");
                
                /*// Delete entries/items
                $icp_entry_array = get_posts(
					array(
						'posts_per_page'   => -1,
						'post_type' => 'icp_entry'
					)
				);
				foreach ($icp_entry_array as $icp_entry) wp_delete_post( $icp_entry->ID, true );
                
                // Delete collections
                $icp_collections_array = get_terms(
					'icp_collections',
					array(
						'hide_empty' => false
					)
				);
				foreach ($icp_collections_array as $icp_collection_term) icp_delete_collection ($icp_collection_term->term_id);
                
                // Delete tags
				$icp_tags_array = get_terms(
					'icp_tags',
					array(
						'hide_empty' => false
					)
				);
				foreach ($icp_tags_array as $icp_tag_term) icp_delete_tag ($icp_tag_term->term_id);*/
                
                //delete_option( 'ICPRESS_PASSCODE' );
                delete_option( 'ICPress_DefaultAccount' );
                delete_option( 'ICPress_DefaultEditor' );
                delete_option( 'ICPress_LastAutoUpdate' );
                delete_option( 'ICPress_DefaultStyle' );
                delete_option( 'ICPress_StyleList' );
                delete_option( 'ICPress_DefaultAutoUpdate' );
                delete_option( 'ICPress_update_version' );
                delete_option( 'ICPress_main_db_version' );
                delete_option( 'ICPress_oauth_db_version' );
                delete_option( 'ICPress_zoteroItems_db_version' );
                delete_option( 'ICPress_zoteroCollections_db_version' );
                delete_option( 'ICPress_zoteroTags_db_version' );
                delete_option( 'ICPress_zoteroRelItemColl_db_version' );
                delete_option( 'ICPress_zoteroRelItemTags_db_version' );
				delete_option( 'ICPress_zoteroItemImages_db_version' );
                
                delete_user_meta( $current_user->ID, 'icpress_5_2_ignore_notice' );
                delete_user_meta( $current_user->ID, 'icpress_survey_notice_ignore' );
                
                $xml .= "<result success='true' reset='complete' />\n";
            }
        } // reset
        
        
        
        /*
         
            SET DEFAULT STYLE
            
        */
        
        else if (isset($_GET['style']))
        {
            
            // Check the post variables and record errors
            if (trim($_GET['style']) != '')
                if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['style'])) == 1)
                    $style = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['style']))));
                else
                    $errors['style_format'][0] = 1;
            else
                $errors['style_empty'][0] = 1;
            
            // Only for post-specific
            if (isset($_GET['forpost']) && $_GET['forpost'] == "true")
                if (isset($_GET['post']) && trim($_GET['post']) != '')
                    if (preg_match('/^[\'0-9]+$/', stripslashes($_GET['post'])) == 1)
                        $post = str_replace("'","",str_replace(" ","",trim(urldecode($_GET['post']))));
                    else
                        $errors['post_format'][0] = 1;
                else
                    $errors['post_empty'][0] = 1;
            
            
            // CHECK ERRORS
            $errorCheck = false;
            foreach ($errors as $field => $error) {
                if ($error[0] == 1) {
                    $errorCheck = true;
                    break;
                }
            }
            
            
            // SET DEFAULT STYLE
            if ($errorCheck == false)
            {
                // Update style list
                if (strpos(get_option("ICPress_StyleList"), $style) === false)
                    update_option( "ICPress_StyleList", get_option("ICPress_StyleList") . ", " . $style);
                
                // Update default style
                if (isset($_GET['forpost']) && $_GET['forpost'] == "true")
                {
                    update_option("ICPress_DefaultStyle_".$post, $style);
                    $xml .= "<result success='true' post='".$post."' style='".$style."' />\n";
                }
                else // Overal defaults
                {
                    update_option("ICPress_DefaultStyle", $style);
                    $xml .= "<result success='true' style='".$style."' />\n";
                }
            }
        } // default style
        
        
        // DISPLAY ERRORS
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
        }
    
    } // isset(submit)
    
    
    
    /*
     
        DISPLAY XML
        
    */

    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    echo "<options>\n";
    echo $xml;
    echo "</options>";

?>