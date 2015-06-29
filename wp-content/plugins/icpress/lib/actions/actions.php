<?php


    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);

    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('ICPress: Access Denied') );
    
    require("../import/import.functions.php");
    
    // Set up XML document
    $xml = "";
    
    

    /*
     
        ADD ZOTERO ACCOUNT
        
    */

    if (isset($_GET['connect'])) // Delete slide
    {
        // Set up error array
        $errors =
            array(
                "api_user_id_blank"=>array(0,"<strong>User ID</strong> was left blank."),
                "api_user_id_format"=>array(0,"<strong>User ID</strong> was formatted incorrectly."),
                "public_key_blank"=>array(0,"<strong>Public Key</strong> was left blank."),
                "public_key_format"=>array(0,"<strong>Public Key</strong> was formatted incorrectly."),
                "nickname_format"=>array(0,"<strong>Nickname</strong> was formatted incorrectly.")
            );
        
        
        // Check the post variables and record errors
        
        // ACCOUNT TYPE
        
        if ($_GET['account_type'] != "")
            if ($_GET['account_type'] == "groups")
                $account_type = "groups";
            else
                $account_type = "users";
        else
            $account_type = "users";
        
        // API USER ID
        
        if ($_GET['api_user_id'] != "")
            if (preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1)
                $api_user_id = htmlentities($_GET['api_user_id']);
            else
                $errors['api_user_id_format'][0] = 1;
        else
            $errors['api_user_id_blank'][0] = 1;
        
        // PUBLIC KEY
        
        if ($_GET['public_key'] != "")
            if (preg_match("/^[0-9a-zA-Z]+$/", $_GET['public_key']) == 1)
                $public_key = htmlentities(trim($_GET['public_key']));
            else
                if ($account_type == "users")
                    $errors['public_key_format'][0] = 1;
        else
            if ($account_type == "users")
                $errors['public_key_blank'][0] = 1;
        
        // NICKNAME 
        
        if (isset($_GET['nickname']) && trim($_GET['nickname']) != '')
            if (preg_match('/^[\'0-9a-zA-Z -_]+$/', stripslashes($_GET['nickname'])) == 1)
                $nickname = str_replace("'", "", str_replace(" ", "", trim(urldecode($_GET['nickname']))));
            else
                $errors['nickname_format'][0] = 1;
        
        
        // CHECK ERRORS
        
        $errorCheck = false;
        foreach ($errors as $field => $error) {
            if ($error[0] == 1) {
                $errorCheck = true;
                break;
            }
        }
        
        
        // ADD ACCOUNT
        
        if ($errorCheck == false)
        {
            $query = "INSERT INTO ".$wpdb->prefix."icpress (account_type, api_user_id, public_key";
            if ($nickname) $query .= ", nickname";
            $query .= ") ";
            $query .= "VALUES ('$account_type', '$api_user_id', '$public_key'";
            if ($nickname) $query .= ", '$nickname'";
            $query .= ")";
            
            // Insert new list item into the list:
            $wpdb->query($query);
            
            // Display success XML
            $xml .= "<result success='true' api_user_id='".$api_user_id."' public_key='".$public_key."' />\n";
        }
        
        
        // DISPLAY ERRORS
        
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<citation>\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
            $xml .= "</citation>\n";
        }
    }
    
    
    
    /*
     
        REMOVE ACCOUNT
        
    */

    else if (isset($_GET['delete']))
    {
        if (preg_match("/^[0-9]+$/", $_GET['delete']))
        {
            // Get api_user_id
            $api_user_id = $wpdb->get_var( "SELECT api_user_id FROM ".$wpdb->prefix."icpress WHERE id='".$_GET['delete']."'" );
            
            // Delete account and items
            $wpdb->query("DELETE FROM ".$wpdb->prefix."icpress WHERE id='".$_GET['delete']."'");
            //$wpdb->query("DELETE FROM ".$wpdb->prefix."icpress_zoteroItems WHERE api_user_id='".$api_user_id."'");
            //$wpdb->query("DELETE FROM ".$wpdb->prefix."icpress_zoteroCollections WHERE api_user_id='".$api_user_id."'");
            //$wpdb->query("DELETE FROM ".$wpdb->prefix."icpress_zoteroTags WHERE api_user_id='".$api_user_id."'");
            icp_clear_last_import ($wpdb, $api_user_id, "items");
            icp_clear_last_import ($wpdb, $api_user_id, "collections");
            icp_clear_last_import ($wpdb, $api_user_id, "tags");
            
            // Check if default account
            if ( get_option("ICPress_DefaultAccount") && get_option("ICPress_DefaultAccount") == $api_user_id )
                delete_option( 'ICPress_DefaultAccount' );
            
            $wpdb->flush();
            unset($api_user_id);
            
            $total_accounts = $wpdb->get_var( "SELECT COUNT(*) FROM ".$wpdb->prefix."icpress;" );
            
            // Display success XML
            $xml .= "<result success='true' total_accounts='".$total_accounts."' />\n";
            $xml .= "<account id='".str_replace("#","",$_GET['delete'])."' type='delete' />\n";
        }
        else // die
        {
            exit();
        }
    }



    /*
     
       ADD OR UPDATE IMAGE
        
    */

    else if ( isset($_GET['image']) && $_GET['image'] == "true" )
    {
        // Set up error array
        $errors = array("entry_id_blank"=>array(0,"<strong>Entry ID</strong> was left blank or formatted incorrectly."),
                                "image_id_blank"=>array(0,"<strong>Image ID</strong> was left blank or formatted incorrectly."),
                                "api_user_id_blank"=>array(0,"<strong>API User ID</strong> was left blank or formatted incorrectly.")
                                );
        
        
        // BASIC VARS
        $api_user_id = false;
        if (preg_match("/^[0-9]+$/", $_GET['api_user_id']))
            $api_user_id = htmlentities(trim($_GET['api_user_id']));
        else
            $errors['api_user_id_blank'][0] = 1;
        
        $entry_id = false;
        if (preg_match("/^[a-zA-Z0-9]+$/", $_GET['entry_id']))
            $entry_id = htmlentities(trim($_GET['entry_id']));
        else
            $errors['entry_id_blank'][0] = 1;
        
        $image_id = false;
        if (preg_match("/^[0-9]+$/", $_GET['image_id']))
            $image_id = htmlentities(trim($_GET['image_id']));
        else
            $errors['image_id_blank'][0] = 1;
        
        
        // CHECK ERRORS
        $errorCheck = false;
        foreach ($errors as $field => $error) {
            if ($error[0] == 1) {
                $errorCheck = true;
                break;
            }
        }
        
        
        // SET FEATURED IMAGE
        
        if ($errorCheck == false)
        {
            //$icp_set_entry_image = update_post_meta( $entry_id, '_thumbnail_id', $image_id );
            //$query = "UPDATE ".$wpdb->prefix."icpress_zoteroItems ";
            //$query .= "SET image='$image' WHERE api_user_id='".$api_user_id."' AND id='".$entry_id."';";
            //$wpdb->query($query);
            
            // Insert new list item into the list:
            $wpdb->query( 
                $wpdb->prepare( 
                    "
                    INSERT INTO ".$wpdb->prefix."icpress_zoteroItemImages (api_user_id, item_key, image) 
                    VALUES (%s, %s, %s)
                    ON DUPLICATE KEY UPDATE image=%s
                    ",
                    $api_user_id, $entry_id, $image_id, $image_id
                )
            );
            //$wpdb->query( 
            //    $wpdb->prepare( 
            //        "
            //        UPDATE ".$wpdb->prefix."icpress_zoteroItems
            //        SET image=%s
            //        WHERE id=%s
            //        ",
            //        $image_id, $entry_id
            //    )
            //);
            
            //if ( $icp_set_entry_image !== false )
                $xml .= "<result success='true' citation_id='".$entry_id."' />\n";
            //else
                //$xml .= "<result success='false' />\n";
        }
        
        
        // DISPLAY ERRORS
        
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<citation>\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
            $xml .= "</citation>\n";
        }
    }
    
    
    
    /*
     
       REMOVE IMAGE
        
    */

    else if ( isset($_GET['remove']) && $_GET['remove'] == "image" )
    {
        // Set up error array
        $errors = array("image_id_blank"=>array(0,"<strong>Image ID</strong> was left blank or formatted incorrectly."));
        
        
        // BASIC VARS
        $image_id = false;
        if (preg_match("/^[A-Z0-9]+$/", $_GET['image_id']))
            $image_id = htmlentities(trim($_GET['image_id']));
        else
            $errors['image_id_blank'][0] = 1;
        
        
        // CHECK ERRORS
        $errorCheck = false;
        foreach ($errors as $field => $error) {
            if ($error[0] == 1) {
                $errorCheck = true;
                break;
            }
        }
        
        
        // REMOVE FEATURED IMAGE
        
        if ($errorCheck == false)
        {
            //$icp_set_entry_image = update_post_meta( $entry_id, '_thumbnail_id', NULL );
            
            $wpdb->query( 
                $wpdb->prepare( 
                    "
                    DELETE FROM ".$wpdb->prefix."icpress_zoteroItemImages
                    WHERE item_key=%s
                    ",
                    $image_id
                )
            );
            
            //if ( $icp_set_entry_image !== false )
                $xml .= "<result success='true' image_id='".$image_id."' />\n";
            //else
                //$xml .= "<result success='false' />\n";
        }
        
        // DISPLAY ERRORS
        
        else
        {
            $xml .= "<result success='false' />\n";
            $xml .= "<citation>\n";
            $xml .= "<errors>\n";
            foreach ($errors as $field => $error)
                if ($error[0] == 1)
                    $xml .= $error[1]."\n";
            $xml .= "</errors>\n";
            $xml .= "</citation>\n";
        }
    }

    
    /*
     
        DISPLAY XML
        
    */

    header('Content-Type: application/xml; charset=ISO-8859-1');
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"yes\"?>\n";
    echo "<accounts>\n";
    echo $xml;
    echo "</accounts>";

?>