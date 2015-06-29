<?php

    // Include WordPress
    require(dirname(dirname(dirname(dirname(dirname( dirname( __FILE__ )))))) .'/wp-load.php');
    define('WP_USE_THEMES', false);
    
    // Include database
    global $wpdb;

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>ICPress In-Text Citation</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel='stylesheet' href='<?php echo includes_url(); ?>/css/buttons.css' type='text/css' media='all' />
		<link rel='stylesheet' href='<?php echo admin_url(); ?>/css/wp-admin.css' type='text/css' media='all' />
		<link rel='stylesheet' href='<?php echo admin_url(); ?>/css/colors-fresh.css' type='text/css' media='all' />
		<link rel='stylesheet' href='../../css/icpress.metabox.css' type='text/css' media='all' />
		<link rel='stylesheet' href='../../css/smoothness/jquery-ui-1.8.11.custom.css' type='text/css' media='all' />
		<style>
		html { background: none; }
		body.icp-ICPress-TinyMCE-Popup { min-width: 0; margin: 0; height: auto; }
		.icp-ICPress-TinyMCE-Popup h4 { color: #666666; padding: 0.5em 1em; }
		.icp-ICPress-TinyMCE-Popup div#icp-ICPressMetaBox-InTextCreator-Options { margin: 0; }
		#icp-ICPressMetaBox-InTextCreator-Options-Inner { display: none; }
		#icp-TinyMCESave { float: right; margin: 0.6em 0.5em 1em; padding: 0.25em 1em; }
		#icp-ICPressMetaBox-Account-ID { display: none; }
		.icp-ICPress-TinyMCE-Popup input { font-family: "Open Sans", sans-serif; }
		.icp-ICPress-TinyMCE-Popup label { font-size: 0.9em; padding: 0 0.5em 0 1.25em; }
		.icp-ICPress-TinyMCE-Popup .options label { font-size: 1em; padding-left: 5px; }
		div#icp-ICPressMetaBox-Biblio-Citations-List div.item div.options input, div#icp-ICPressMetaBox-Citations-List div.item div.options input { font-size: 1em; border: none; padding: 0.15em 0.35em; }
		div#icp-ICPressMetaBox-Biblio-Options p.note, div#icp-ICPressMetaBox-InTextCreator-Options p.note { margin: 0 1em 0.75em 1.5em; }
		</style>
		
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/tinymce/tiny_mce_popup.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/jquery.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.core.min.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.widget.min.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.position.min.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.menu.min.js"></script>
        <script type="text/javascript" src="<?php echo includes_url(); ?>/js/jquery/ui/jquery.ui.autocomplete.min.js"></script>
        <script type="text/javascript" src="../../js/jquery.livequery.min.js"></script>
        <script type="text/javascript" src="../../js/icpress.widget.metabox.js"></script>
        <script>
        
        jQuery(document).ready(function() {
			
			// Assumes: summaries with multiple citations are separated with "; " (with the space)
			
            var shortcode = tinyMCEPopup.getWindowArg("shortcode");
            var summary = tinyMCEPopup.getWindowArg("summary").split("; ");
			
			if ( shortcode != "" ) // Set fields based on shortcode
			{
				var attributes = shortcode.match(/[\w-]+="[^"]*"/g);
				
				if ( attributes != null )
				{
					for (var i = 0; i < attributes.length; i++)
					{
						var attribute = attributes[i].split("=");
						
						if ( attribute[0].replace(/"/gi, '') == "item")
						{
							var scItems = attribute[1].replace(/"/gi, '').split("},{");
							
							if (scItems.length > 1) // multiple items
							{
								for (var s = 0; s < scItems.length; s++)
								{
									var scItemTemp = scItems[s].split(","); // 0=key, 1=pages
									
									if (scItemTemp.length > 1)
										var scItem = { "key": scItemTemp[0].replace(/{/gi, ''), "pages": scItemTemp[1].replace(/}/gi, '') };
									else
										var scItem = { "key": scItemTemp[0].replace(/{/gi, '').replace(/}/gi, ''), "pages" : "" };
									
									jQuery("#icp-ICPressMetaBox-Citations-List-Inner").append("<div class='item' rel='"+scItem.key+"'><span class='label'>"+summary[s]+"</span><div class='toggle'></div><div class='delete'></div><div class='options'><label for='icp-Item-"+scItem.key+"'>Page(s):</label><input id='icp-Item-"+scItem.key+"' type='text' value='"+scItem.pages+"' /><div class='id'>Key: "+scItem.key+"</div></div></div>\n");
								}
							}
							else if (scItems.length == 1) // one item
							{
								var scItemTemp = scItems[0].split(","); // 0=key, 1=pages
								
								if (scItemTemp.length > 1)
									var scItem = { "key": scItemTemp[0].replace(/{/gi, ''), "pages": scItemTemp[1].replace(/}/gi, '') };
								else
									var scItem = { "key": scItemTemp[0].replace(/{/gi, '').replace(/}/gi, ''), "pages" : "" };
								
								jQuery("#icp-ICPressMetaBox-Citations-List-Inner").append("<div class='item' rel='"+scItem.key+"'><span class='label'>"+summary[0]+"</span><div class='toggle'></div><div class='delete'></div><div class='options'><label for='icp-Item-"+scItem.key+"'>Page(s):</label><input id='icp-Item-"+scItem.key+"' type='text' value='"+scItem.pages+"' /><div class='id'>Key: "+scItem.key+"</div></div></div>\n");
							}
						}
						else if ( attribute[0].replace(/"/gi, '') == "format")
						{
							jQuery("#icp-ICPressMetaBox-InTextCreator-Options-Format").val(attribute[1].replace(/"/gi, ''));
						}
						else if ( attribute[0].replace(/"/gi, '') == "etal")
						{
							jQuery("#icp-ICPressMetaBox-InTextCreator-Options-Etal").val(attribute[1].replace(/"/gi, ''));
						}
						else if ( attribute[0].replace(/"/gi, '') == "and")
						{
							jQuery("#icp-ICPressMetaBox-InTextCreator-Options-And").val(attribute[1].replace(/"/gi, ''));
						}
						else if ( attribute[0].replace(/"/gi, '') == "separator")
						{
							jQuery("#icp-ICPressMetaBox-InTextCreator-Options-Separator").val(attribute[1].replace(/"/gi, ''));
						}
						else if ( attribute[0].replace(/"/gi, '') == "userid")
						{
							jQuery("#icp-ICPressMetaBox-Account-ID").text(attribute[1].replace(/"/gi, ''));
						}
					}
				}
			}
            
            jQuery("#icp-TinyMCESave").click(function()
			{
				var zpItemsExist = false;
				
				// Check items
				if ( jQuery("#icp-ICPressMetaBox-Citations-List .item").length > 0 )
				{
					zpItemsExist = true;
					summary = "";
					var newShortcode = "[icpressInText userid=\""+jQuery("#icp-ICPressMetaBox-Account-ID").text()+"\"";
					var newShortcodeItems = "";
					var newShortcodeFormat = ' format="';
					
					jQuery("#icp-ICPressMetaBox-Citations-List .item").each(function()
					{
						if ( newShortcodeItems.length == 0 ) newShortcodeItems = ' item="'; else newShortcodeItems += ",";
						newShortcodeItems += "{" + jQuery(this).attr("rel");
						if ( jQuery("input", this).val().length > 0 ) newShortcodeItems += "," + jQuery("input", this).val();
						newShortcodeItems += "}";
						
						if ( summary.length == 0 ) summary = jQuery(".label", this).text(); else summary += "; " + jQuery(".label", this).text();
					});
					newShortcode += newShortcodeItems + '"';
					
					// Format
					if ( jQuery("#icp-ICPressMetaBox-InTextCreator-Options-Format").val().length > 0 ) newShortcodeFormat += jQuery("#icp-ICPressMetaBox-InTextCreator-Options-Format").val(); else newShortcodeFormat += "(%a%, %d%, %p%)";
					newShortcodeFormat = newShortcodeFormat.replace(/&/gi, '&amp;'); // maintain entities
					newShortcode += newShortcodeFormat +'"';
					
					// Etal
					if ( jQuery("#icp-ICPressMetaBox-InTextCreator-Options-Etal").val() != "default" ) newShortcode += ' etal="'+jQuery("#icp-ICPressMetaBox-InTextCreator-Options-Etal").val()+'"';
					
					// And
					if ( jQuery("#icp-ICPressMetaBox-InTextCreator-Options-And").val() != "default" ) newShortcode += ' and="'+jQuery("#icp-ICPressMetaBox-InTextCreator-Options-And").val()+'"';
					
					// Separator
					if ( jQuery("#icp-ICPressMetaBox-InTextCreator-Options-Separator").val() != "default" ) newShortcode += ' separator="'+jQuery("#icp-ICPressMetaBox-InTextCreator-Options-Separator").val()+'"';
					
					newShortcode += ']';
				}
				
				// Save new shortcode
                if ( zpItemsExist === true )
				{
					tinyMCEPopup.editor.execCommand('mceInsertContent', false, "<span class=\"icp-ICPressShortcode cite\"><span class='summary'>"+summary+"</span><span class='shortcode'>"+newShortcode+"</span></span>");
				}
				else // Or remove shortcode
				{
					tinyMCEPopup.editor.execCommand('mceInsertContent', false, "");
				}
                tinyMCEPopup.close();
            });
        
        });
        </script>
    </head>
	<body id="icp-ICPress-TinyMCE-Cite" class="icp-ICPress-TinyMCE-Popup wp-core-ui">
		
		
		<!-- START OF ICPRESS IN-TEXT -->
		
		<div id="icp-ICPressMetaBox-InTextCreator">
			
			<?php if ( count($wpdb->get_var( "SELECT COUNT(id) FROM ".$wpdb->prefix."icpress;" )) > 1) { ?>
			<!-- START OF ACCOUNT -->
			<div id="icp-ICPressMetaBox-Account">
				<?php
				
				// See if default exists
				$icp_default_account = false;
				if (get_option("ICPress_DefaultAccount")) $icp_default_account = get_option("ICPress_DefaultAccount");
				
				if ($icp_default_account !== false)
					$icp_account = $wpdb->get_results( $wpdb->prepare( "SELECT api_user_id, nickname FROM ".$wpdb->prefix."icpress WHERE api_user_id = '".$icp_default_account."';" ) );
				else
					$icp_account = $wpdb->get_results( $wpdb->prepare( "SELECT api_user_id, nickname FROM ".$wpdb->prefix."icpress LIMIT 1;" ) );
				
				?>
				<span id="icp-ICPressMetaBox-Account-ID"><?php echo $icp_account[0]->api_user_id; ?></span>
				<?php
				
				if (is_null($icp_account[0]->nickname) === false && $icp_account[0]->nickname != "")
					$icp_default_account = $icp_account[0]->nickname . " (" . $icp_account[0]->api_user_id . ")";
				
				?>
				Searching <?php echo $icp_default_account; ?>. <a target="icpress" href="<?php echo admin_url( 'admin.php?page=ICPress&amp;options=true'); ?>">Change account?</a>
			</div>
			<!-- END OF ACCOUNT -->
			<?php } else { ?>
			<span id="icp-ICPressMetaBox-Account-ID"><?php echo $wpdb->get_var( "SELECT api_user_id FROM ".$wpdb->prefix."icpress;" ); ?></span>
			<?php } ?>
			
			<!-- START OF SEARCH -->
			<div id="icp-ICPressMetaBox-Citations">
				<input id="icp-ICPressMetaBox-Citations-Search" class="help" type="text" value="Type to search" />
				<input type="hidden" id="ICPRESS_PLUGIN_URL" name="ICPRESS_PLUGIN_URL" value="<?php echo ICPRESS_PLUGIN_URL; ?>" />
				
			</div><div id="icp-ICPressMetaBox-Citations-List"><div id="icp-ICPressMetaBox-Citations-List-Inner"></div><hr class="clear" /></div>
			<!-- END OF SEARCH -->
			
			<!-- START OF OPTIONS -->
			<div id="icp-ICPressMetaBox-InTextCreator-Options">
				
				<h4>Options <span class='toggle'></span></h4>
				
				<div id="icp-ICPressMetaBox-InTextCreator-Options-Inner">
					
					<label for="icp-ICPressMetaBox-InTextCreator-Options-Format">Format:</label>
					<input type="text" id="icp-ICPressMetaBox-InTextCreator-Options-Format" value="(%a%, %d%, %p%)" />
					<p class="note">Use these placeholders: %a% for author, %d% for date, %p% for page, %num% for list number.</p>
					
					<label for="icp-ICPressMetaBox-InTextCreator-Options-Etal">Et al:</label>
					<select id="icp-ICPressMetaBox-InTextCreator-Options-Etal">
						<option id="default" value="default" selected="selected">Default</option>
						<option id="yes" value="yes">Yes</option>
						<option id="no" value="no">No</option>
					</select>
					
					<hr />
					
					<label for="icp-ICPressMetaBox-InTextCreator-Options-Separator">Separator:</label>
					<select id="icp-ICPressMetaBox-InTextCreator-Options-Separator">
						<option id="default" value="default" selected="selected">Semicolon</option>
						<option id="comma" value="comma">Comma</option>
					</select>
					
					<hr />
					
					<label for="icp-ICPressMetaBox-InTextCreator-Options-And">And:</label>
					<select id="icp-ICPressMetaBox-InTextCreator-Options-And">
						<option id="default" value="default" selected="selected">No</option>
						<option id="and" value="and">and</option>
						<option id="comma-and" value="comma-and">, and</option>
					</select>
					
				</div><!-- #icp-ICPressMetaBox-InTextCreator-Options-Inner -->
				
			</div>
			<!-- END OF OPTIONS -->
		
		</div>
		<!-- END OF ICPRESS IN-TEXT -->
		
		<input type="button" id="icp-TinyMCESave" class="button button-primary button-large" value="Save" />
	
	</body>
</html>