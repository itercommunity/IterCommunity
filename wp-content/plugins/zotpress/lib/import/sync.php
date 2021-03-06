<?php

    // Include WordPress
    require('../../../../../wp-load.php');
    define('WP_USE_THEMES', false);

    // Prevent access to users who are not editors
    if ( !current_user_can('edit_others_posts') && !is_admin() ) wp_die( __('Only editors can access this page through the admin panel.'), __('Zotpress: Access Denied') );
    
    // Ignore user abort
    ignore_user_abort(true);
    set_time_limit(60*10); // ten minutes
    
    // Access WordPress db
    global $wpdb;
    
    // Include Request Functionality
    require("../request/rss.request.php");
    
    // Include Import and Sync Functions
    require("import.functions.php");
    require("sync.functions.php");
    
    
    
?><!DOCTYPE html 
    PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    
    <head profile="http://www.w3.org/2005/11/profile">
        <title> Syncing </title>
        <script type="text/javascript" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>js/jquery-1.5.2.min.js"></script>
        <script type="text/javascript" src="<?php echo ZOTPRESS_PLUGIN_URL; ?>js/jquery.livequery.min.js"></script>
    </head>
    
    <body><?php
    
    
    // START WITH ITEMS
    
    if ( isset($_GET['step']) && $_GET['step'] == "items")
    {
        $api_user_id = zp_get_api_user_id();
        
        //if (get_option('ZOTPRESS_PASSCODE') && isset($_GET['key']) && get_option('ZOTPRESS_PASSCODE') == $_GET['key'])
        //{
            // Get account
            //$_SESSION['zp_session'][$api_user_id]['zp_account'] = zp_get_account($wpdb, $api_user_id);
            
            // Set delete list
            update_option('ZOTPRESS_DELETE_'.$api_user_id, zp_get_local_items ($wpdb, $api_user_id));
            
            // Set current sync time
            zp_set_update_time( date('Y-m-d') );
            
            // GET ITEM COUNT AND LOCAL ITEMS
            //$_SESSION['zp_session'][$api_user_id]['items']['zp_local_items'] = zp_get_local_items ($wpdb, $api_user_id);
            
            // Set up session item query vars
            //$_SESSION['zp_session'][$api_user_id]['items']['zp_items_to_add'] = array();
            //$_SESSION['zp_session'][$api_user_id]['items']['zp_items_to_update'] = array();
            //$_SESSION['zp_session'][$api_user_id]['items']['query_total_items_to_add'] = 0;
            
            // SYNC ITEMS
            ?><script type="text/javascript">
            
            jQuery(document).ready(function()
            {
                function zp_get_items (zp_plugin_url, api_user_id, zp_start)
                {
                    var zpXMLurl = zp_plugin_url + "lib/actions/actions.sync.php?api_user_id=" + api_user_id + "&step=items&start=" + zp_start;
                    //alert(zpXMLurl); // DEBUG
                    
                    jQuery.get( zpXMLurl, {}, function(xml)
                    {
                        var $result = jQuery("result", xml);
                        
                        if ($result.attr("success") == "true") // Move on to the next 50
                        {
                            jQuery('div#zp-Account-<?php echo $api_user_id; ?> span.delete .zp-Sync-Messages', window.parent.document).text("Syncing items " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
                            zp_get_items (zp_plugin_url, api_user_id, $result.attr("next"));
                        }
                        else if ($result.attr("success") == "next")
                        {
                            jQuery('div#zp-Account-<?php echo $api_user_id; ?> span.delete .zp-Sync-Messages', window.parent.document).text("Syncing collections 1-50 ...");
                            jQuery("iframe#zp-Sync-<?php echo $api_user_id; ?>", window.parent.document).attr('src', jQuery("iframe#zp-Sync-<?php echo $api_user_id; ?>", window.parent.document).attr('src').replace("step=items", "step=collections"));
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem syncing items: " + jQuery("errors", xml).text() );
                        }
                    });
                }
                
                zp_get_items( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', 0);
                
            });
            
            </script><?php
        //}
        //
        //else /* key fails */ { exit ("key incorrect "); }
        
    }
    
    
    // THEN COLLECTIONS
    
    else if ( isset($_GET['step']) && $_GET['step'] == "collections")
    {
        $api_user_id = zp_get_api_user_id();
        
        //if (get_option('ZOTPRESS_PASSCODE') && isset($_GET['key']) && get_option('ZOTPRESS_PASSCODE') == $_GET['key'])
        //{
            // GET LOCAL COLLECTIONS
            //$_SESSION['zp_session'][$api_user_id]['collections']['zp_local_collections'] = zp_get_local_collections ($wpdb, $api_user_id);
            //
            //// Set up session item query vars
            //$_SESSION['zp_session'][$api_user_id]['collections']['zp_collections_to_update'] = array();
            //$_SESSION['zp_session'][$api_user_id]['collections']['zp_collections_to_add'] = array();
            //$_SESSION['zp_session'][$api_user_id]['collections']['query_total_collections_to_add'] = 0;
            
            // Set delete list
            update_option('ZOTPRESS_DELETE_'.$api_user_id, zp_get_local_collections ($wpdb, $api_user_id));
            
            // SYNC COLLECTIONS
            ?><script type="text/javascript">
            
            jQuery(document).ready(function()
            {
                function zp_get_collections (zp_plugin_url, api_user_id, zp_start)
                {
                    var zpXMLurl = zp_plugin_url + "lib/actions/actions.sync.php?api_user_id=" + api_user_id + "&step=collections&start=" + zp_start;
                    //alert(zpXMLurl); // DEBUG
                    
                    jQuery.get( zpXMLurl, {}, function(xml)
                    {
                        var $result = jQuery("result", xml);
                        
                        if ($result.attr("success") == "true") // Move on to the next 50
                        {
                            jQuery('div#zp-Account-<?php echo $api_user_id; ?> span.delete .zp-Sync-Messages', window.parent.document).text("Syncing collections " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
                            zp_get_collections (zp_plugin_url, api_user_id, $result.attr("next"));
                        }
                        else if ($result.attr("success") == "next")
                        {
                            jQuery('div#zp-Account-<?php echo $api_user_id; ?> span.delete .zp-Sync-Messages', window.parent.document).text("Syncing tags 1-50 ...");
                            jQuery("iframe#zp-Sync-<?php echo $api_user_id; ?>", window.parent.document).attr('src', jQuery("iframe#zp-Sync-<?php echo $api_user_id; ?>", window.parent.document).attr('src').replace("step=collections", "step=tags"));
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem syncing collections: " + jQuery("errors", xml).text() );
                        }
                    });
                }
                
                zp_get_collections( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', 0);
                
            });
            
            </script><?php
        //}
        //else /* key fails */ { exit ("key incorrect "); }
    }
    
    
    // THEN TAGS
    
    else if ( isset($_GET['step']) && $_GET['step'] == "tags")
    {
        $api_user_id = zp_get_api_user_id();
        
        //if (get_option('ZOTPRESS_PASSCODE') && isset($_GET['key']) && get_option('ZOTPRESS_PASSCODE') == $_GET['key'])
        //{
            // GET LOCAL TAGS
            //$_SESSION['zp_session'][$api_user_id]['tags']['zp_local_tags'] = zp_get_local_tags ($wpdb, $api_user_id);
            //
            //// Set up session item query vars
            //$_SESSION['zp_session'][$api_user_id]['tags']['zp_tags_to_update'] = array();
            //$_SESSION['zp_session'][$api_user_id]['tags']['zp_tags_to_add'] = array();
            //$_SESSION['zp_session'][$api_user_id]['tags']['query_total_tags_to_add'] = 0;
            
            // Set delete list
            update_option('ZOTPRESS_DELETE_'.$api_user_id, zp_get_local_tags ($wpdb, $api_user_id));
            
            // SYNC TAGS
            ?><script type="text/javascript">
            
            jQuery(document).ready(function()
            {
                function zp_get_tags (zp_plugin_url, api_user_id, zp_start)
                {
                    var zpXMLurl = zp_plugin_url + "lib/actions/actions.sync.php?api_user_id=" + api_user_id + "&step=tags&start=" + zp_start;
                    //alert(zpXMLurl); // DEBUG
                    
                    jQuery.get( zpXMLurl, {}, function(xml)
                    {
                        var $result = jQuery("result", xml);
                        
                        if ($result.attr("success") == "true") // Move on to the next 50
                        {
                            jQuery('div#zp-Account-<?php echo $api_user_id; ?> span.delete .zp-Sync-Messages', window.parent.document).text("Syncing tags " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
                            zp_get_tags (zp_plugin_url, api_user_id, $result.attr("next"));
                        }
                        else if ($result.attr("success") == "next")
                        {
                            jQuery('div#zp-Account-<?php echo $api_user_id; ?> span.delete .zp-Sync-Messages', window.parent.document).text("Sync complete!");
                            jQuery('div#zp-Account-<?php echo $api_user_id; ?> span.delete a.sync', window.parent.document).removeClass("syncing").addClass("success");
                        }
                        else // Show errors
                        {
                            alert( "Sorry, but there was a problem syncing tags: " + jQuery("errors", xml).text() );
                        }
                    });
                }
                
                zp_get_tags( <?php echo "'" . ZOTPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', 0);
                
            });
            
            </script><?php
        //}
        //
        //else /* key fails */ { exit ("key incorrect "); }
        
    } ?>

    </body>
</html>