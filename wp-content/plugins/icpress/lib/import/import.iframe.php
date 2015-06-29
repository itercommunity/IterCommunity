<?php

// Include WordPress
require('../../../../../wp-load.php');
define('WP_USE_THEMES', false);

// Prevent access to non-editors
if ( !current_user_can('edit_others_posts') && !is_admin() )
	wp_die( __('Only editors can access this page.'), __('ICPress: Access Denied'), array( 'response' => '403' ) );


/*
*   IMPORT PSEUDOCODE:
*
*   Get list of all item keys
*   Import items in sets of 50
*   Import categories in sets of 50
*       Get list of all item keys for each tag
*   Import tags in sets of 50
*       Get list of all item keys for each tag
*
*   Requests to Zotero given 100 of each:
*   1 + 2 + 2 + 100 + 2 + 100 = 207
*
*/

if ( isset($_GET['go']) && $_GET['go'] == "true"
		&& check_admin_referer( 'icp_importing_' . intval($_GET['api_user_id']) . '_' . date('Y-j-G'), 'icp_nonce' ) )
{
	// Access WordPress db
	global $wpdb;
	
	// Ignore user abort
	ignore_user_abort(true);
	set_time_limit(60*10); // ten minutes
	
	// Include Request Functionality
	require( dirname(__FILE__) . '/../request/rss.request.php' );
	
	// Include Import Functions
	require( dirname(__FILE__) . '/import.functions.php' );
	
	
	?><!DOCTYPE html 
	PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	
	<head profile="http://www.w3.org/2005/11/profile">
		<title> Importing </title>
		<script type="text/javascript" src="<?php echo ICPRESS_PLUGIN_URL; ?>js/jquery-1.5.2.min.js"></script>
		<script type="text/javascript" src="<?php echo ICPRESS_PLUGIN_URL; ?>js/jquery.livequery.min.js"></script>
	</head>
	
	<body><?php
	
	// SET UP FUNCTIONS
	
	if ( isset($_GET['step']) )
	{
		global $current_user;
		if ( !get_user_meta($current_user->ID, 'icpress_5_2_ignore_notice') )
			add_user_meta($current_user->ID, 'icpress_5_2_ignore_notice', 'true', true);
	
	?><script type="text/javascript">
		
		jQuery(document).ready(function()
		{
			jQuery.ajaxSetup({timeout: 60000});
			
			
			/*
			 *
			 *  JQUERY IMPORT FUNCTIONS
			 *
			 */
			
			function icp_get_items_ajax (icp_plugin_url, api_user_id, icp_start, icp_selective)
			{
				var icp_type = "regular";
				
				if ( typeof(icp_selective) === "undefined" || icp_selective == "false" || icp_selective == "" )
					icp_selective = "false";
				else
					icp_type = "selective";
				
				jQuery.ajax({
					type: "GET",
					url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
					data: { "action": "icpress_import_ajax", "api_user_id": api_user_id, "step": "items", "start": icp_start, "selective": icp_selective, "icp_nonce": "<?php echo $_REQUEST['icp_nonce']; ?>" },
					dataType: "xml",
					success: function( xml )
					{
						var $result = jQuery("result", xml);
						
						if ($result.attr("success") == "true") // Move on to the next 50
						{
							jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Importing items " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
							
							if ( icp_selective != "false" )
							{
								icp_get_items_ajax (icp_plugin_url, api_user_id, $result.attr("next"), icp_selective);
							}
							else // regular
							{
								icp_get_items_ajax (icp_plugin_url, api_user_id, $result.attr("next"));
							}
						}
						else if ($result.attr("success") == "next")
						{
							<?php if (isset($_GET["singlestep"])) { ?>
							jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Import of items complete!");
							jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
							jQuery("#icp-ICPress-Setup-Buttons", window.parent.document).removeAttr("style");
							jQuery(".icp-Loading-Initial", window.parent.document).hide();
							<?php } else { ?>
							
							jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Importing collections 1-50 ...");
							
							if ( icp_type == "selective" )
							{
								icp_get_collections_ajax (icp_plugin_url, api_user_id, '0', icp_selective);
							}
							else // regular
							{
								jQuery("iframe#icp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#icp-Setup-Import", window.parent.document).attr('src').replace("step=items", "step=collections"));
							}
							<?php } ?>
						}
						else // Show errors
						{
							alert( "Sorry, but there was a problem importing items: " + jQuery("errors", xml).text() );
						}
					},
					error: function(jqXHR, textStatus, errorThrown)
					{
						alert("Sorry, but there was a problem importing items: " + errorThrown);
					}
				});
			}
			
			
			
			
			function icp_get_collections_ajax (icp_plugin_url, api_user_id, icp_start, icp_selective)
			{
				var icp_type = "regular";
				
				if ( typeof(icp_selective) === "undefined" || icp_selective == "false" || icp_selective == "" )
					icp_selective = "false";
				else
					icp_type = "selective";
				
				jQuery.ajax({
					type: "GET",
					url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
					data: { "action": "icpress_import_ajax", "api_user_id": api_user_id, "step": "collections", "start": icp_start, "selective": icp_selective, "icp_nonce": "<?php echo $_REQUEST['icp_nonce']; ?>" },
					dataType: "xml",
					success: function( xml )
					{
						var $result = jQuery("result", xml);
						
						if ($result.attr("success") == "true") // Move on to the next 50
						{
							jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Importing collections " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
							
							if ( icp_selective != "false" )
							{
								// Add subcollections (if any) to collection list
								if ( jQuery("subcollections", xml) && jQuery("subcollections", xml).text() != "" )
								{
									zpCollections = zpCollections.concat( (jQuery("subcollections", xml).text()).split(',') );
								}
								// Move on to the next 50
								icp_get_collections_ajax (icp_plugin_url, api_user_id, $result.attr("next"), icp_selective);
							}
							else // regular
							{
								icp_get_collections_ajax (icp_plugin_url, api_user_id, $result.attr("next"));
							}
						}
						else if ($result.attr("success") == "next")
						{
							<?php if (isset($_GET["singlestep"])) { ?>
							jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Import of collections complete!");
							jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
							jQuery("#icp-ICPress-Setup-Buttons", window.parent.document).removeAttr("style");
							jQuery(".icp-Loading-Initial", window.parent.document).hide();
							<?php } else { ?>
							
							jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Importing tags 1-50 ...");
							
							if ( icp_type == "selective" )
							{
								// Add subcollections (if any) to collection list
								if ( jQuery("subcollections", xml) && jQuery("subcollections", xml).text() != "" )
								{
									zpCollections = zpCollections.concat( (jQuery("subcollections", xml).text()).split(',') );
								}
								// Move on to tags
								icp_get_tags_ajax (icp_plugin_url, api_user_id, '0', icp_selective);
							}
							else // regular
							{
								jQuery("iframe#icp-Setup-Import", window.parent.document).attr('src', jQuery("iframe#icp-Setup-Import", window.parent.document).attr('src').replace("step=collections", "step=tags"));
							}
							<?php } ?>
						}
						else // Show errors
						{
							alert( "Sorry, but there was a problem importing collections: " + jQuery("errors", xml).text() );
						}
					},
					error: function(jqXHR, textStatus, errorThrown)
					{
						alert("Sorry, but there was a problem importing collections: " + errorThrown);
					}
				});
			}
			
			
			
			
			function icp_get_tags_ajax (icp_plugin_url, api_user_id, icp_start, icp_selective)
			{
				var icp_type = "regular";
				
				if ( typeof(icp_selective) === "undefined" || icp_selective == "false" || icp_selective == "" )
					icp_selective = "false";
				else
					icp_type = "selective";
				
				jQuery.ajax({
					type: "GET",
					url: "<?php echo admin_url( 'admin-ajax.php' ); ?>",
					data: { "action": "icpress_import_ajax", "api_user_id": api_user_id, "step": "tags", "start": icp_start, "selective": icp_selective, "icp_nonce": "<?php echo $_REQUEST['icp_nonce']; ?>" },
					dataType: "xml",
					success: function( xml )
					{
						var $result = jQuery("result", xml);
						
						if ($result.attr("success") == "true") // Move on to the next 50
						{
							jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Importing tags " + $result.attr("next") + "-" + (parseInt($result.attr("next"))+50) + "...");
							
							if ( icp_selective != "false" )
								icp_get_tags_ajax (icp_plugin_url, api_user_id, $result.attr("next"), icp_selective);
							else // regular
								icp_get_tags_ajax (icp_plugin_url, api_user_id, $result.attr("next"));
						}
						else if ($result.attr("success") == "next")
						{
							<?php if (isset($_GET["singlestep"])) { ?>
							jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Import of tags complete!");
							jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
							jQuery("#icp-ICPress-Setup-Buttons", window.parent.document).removeAttr("style");
							jQuery(".icp-Loading-Initial", window.parent.document).hide();
							<?php } else { ?>
							
							if ( icp_type == "selective" )
							{
								// Remove collection from list
								zpCollections.splice( jQuery.inArray(icp_selective, zpCollections), 1 );
								
								// If no more (sub)collections, then finish
								if ( zpCollections == "" )
								{
									jQuery(".selective.icp-Import-Messages", window.parent.document).text("Import of selected top level collections complete!");
									jQuery("input[type=button]", window.parent.document).removeAttr('disabled');
									jQuery(".selective.icp-Loading-Initial", window.parent.document).hide();
									jQuery("#icp-ICPress-Setup-Buttons", window.parent.document).show();
								}
								else // Keep going ...
								{
									jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Preparing to import items 1-50 from the next collection ...");
									icp_get_items_ajax (icp_plugin_url, api_user_id, '0', zpCollections[0]);
								}
							}
							else // regular
							{
								jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Import complete!");
								window.parent.location = "<?php echo ICPRESS_PLUGIN_URL; ?>../../../wp-admin/admin.php?page=ICPress&api_user_id=" + api_user_id;
							}<?php } ?>
						}
						else // Show errors
						{
							alert( "Sorry, but there was a problem importing tags: " + jQuery("errors", xml).text() );
						}
					},
					error: function(jqXHR, textStatus, errorThrown)
					{
						if ( textStatus != "timeout" )
						{
							alert("Sorry, but there was a problem importing tags: " + errorThrown);
						}
						else // timeout error
						{
							if ( this.retrycount < 3 )
							{
								jQuery.ajax(this);
								this.retrycount++;
							}
							else
							{
								this.retrycount = 0;
								jQuery('.'+icp_type+'.icp-Import-Messages', window.parent.document).text("Import of tags failed. Please try again.");
								alert("Sorry, but ICPress was unable to import all tags.");
							}
							return;
						}
					}
				});
			}
		
	<?php
		
		
		// START WITH ITEMS
		
		if ( isset($_GET['step']) && $_GET['step'] == "items")
		{
			$api_user_id = icp_get_api_user_id();
			
			// Set current import time
			icp_set_update_time( date('Y-m-d') );
			
			// Clear last import
			icp_clear_last_import ($wpdb, $api_user_id, $_GET['step']);
			
			?>
			
			icp_get_items_ajax( <?php echo "'" . ICPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', 0);
			
			<?php
			
			//global $ICPress_update_version;
			
			$wpdb->update( 
				$wpdb->prefix."icpress", 
				array( 'version' => $GLOBALS['ICPress_update_db_by_version'] ), 
				array( 'api_user_id' => $api_user_id ), 
				array( '%s' ), 
				array( '%s' ) 
			);
		}
		
		
		// THEN COLLECTIONS
		
		else if (isset($_GET['step']) && $_GET['step'] == "collections")
		{
			$api_user_id = icp_get_api_user_id();
			
			// Clear last import
			icp_clear_last_import ($wpdb, $api_user_id, "collections");
			
			?>
			
			icp_get_collections_ajax( <?php echo "'" . ICPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', 0);
			
			<?php
			
			//global $ICPress_update_version;
			
			$wpdb->update( 
				$wpdb->prefix."icpress", 
				array( 'version' => $GLOBALS['ICPress_update_db_by_version'] ), 
				array( 'api_user_id' => $api_user_id ), 
				array( '%s' ), 
				array( '%s' ) 
			);
		}
		
		
		// THEN TAGS
		
		else if (isset($_GET['step']) && $_GET['step'] == "tags")
		{
			$api_user_id = icp_get_api_user_id();
			
			// Clear last import
			icp_clear_last_import ($wpdb, $api_user_id, "tags");
			
			?>
			
			icp_get_tags_ajax( <?php echo "'" . ICPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', 0);
			
			<?php
			
			//global $ICPress_update_version;
			
			$wpdb->update( 
				$wpdb->prefix."icpress", 
				array( 'version' => $GLOBALS['ICPress_update_db_by_version'] ), 
				array( 'api_user_id' => $api_user_id ), 
				array( '%s' ), 
				array( '%s' ) 
			);
			
		} // step
		
		
		
		// OR SELECTIVELY IMPORT BY COLLECTION
		
		// For each collection selected for import:
		
		// <userOrGroupPrefix>/collections/<collectionKey>/collections -- subcollections -- check if they have their own subcollections and loop through for each
		// <userOrGroupPrefix>/collections/<collectionKey>/items
		// <userOrGroupPrefix>/collections/<collectionKey>/tags
		
		else if (isset($_GET['step']) && $_GET['step'] == "selective")
		{
			// Check selected top level collections
			if ( isset($_GET['collections']) && preg_match("/[0-9a-zA-Z,]+/", $_GET['collections']) )
			{
				$api_user_id = icp_get_api_user_id();
				
				// Clear last import
				icp_clear_last_import ($wpdb, $api_user_id, "selective", $_GET['collections']);
				
				// Import top level collections' data
				$GLOBALS['icp_session'][$api_user_id]['collections']['query_params'] = array();
				$GLOBALS['icp_session'][$api_user_id]['collections']['query_total_entries'] = 0;
				
				foreach ( explode(",", $_GET['collections']) as $icp_single_collection )
					icp_get_collections ($wpdb, $api_user_id, 0, false, false, $icp_single_collection);
				icp_save_collections ($wpdb, $api_user_id, false, false);
				
				?>
					var zpCollections = '<?php echo $_GET['collections']; ?>';
					zpCollections = zpCollections.split(',');
					
					icp_get_items_ajax( <?php echo "'" . ICPRESS_PLUGIN_URL . "', '" . $api_user_id; ?>', '0', zpCollections[0] );
					
				<?php
				
				$wpdb->update( 
					$wpdb->prefix."icpress", 
					array( 'version' => $GLOBALS['ICPress_update_db_by_version'] ), 
					array( 'api_user_id' => $api_user_id ), 
					array( '%s' ), 
					array( '%s' ) 
				);
				
			} else {
				?>alert("Sorry, but the collection(s) were missing or weren't formatted correctly.");<?php
			}
			
		} // step
		?>
		
		});
		
	</script><?php
	
	} // step
	
	?>
	
	</body>
	</html><?php
}

else
{
	echo "Page cannot be directly accessed.";
}
?>