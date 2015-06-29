<?php

    // Include WordPress
    require(dirname(dirname(dirname(dirname(dirname( dirname( __FILE__ )))))) .'/wp-load.php');
    define('WP_USE_THEMES', false);
    
    // Include database
    global $wpdb;

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
		<title>ICPress In-Text Bibliography</title>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<link rel='stylesheet' href='<?php echo includes_url(); ?>/css/buttons.css' type='text/css' media='all' />
		<link rel='stylesheet' href='<?php echo admin_url(); ?>/css/wp-admin.css' type='text/css' media='all' />
		<link rel='stylesheet' href='<?php echo admin_url(); ?>/css/colors-fresh.css' type='text/css' media='all' />
		<link rel='stylesheet' href='../../css/icpress.metabox.css' type='text/css' media='all' />
		<link rel='stylesheet' href='../../css/smoothness/jquery-ui-1.8.11.custom.css' type='text/css' media='all' />
		<style>
		html { background: none; }
		body.icp-ICPress-TinyMCE-Popup { min-width: 0; margin: 0; height: auto; }
		.icp-ICPress-TinyMCE-Popup h4 { color: #666666; margin: 0.25em 0; padding: 0.8em 1em; }
		.icp-ICPress-TinyMCE-Popup div#icp-ICPressMetaBox-InTextCreator-Options { margin: 0; }
		.icp-ICPress-TinyMCE-Popup #icp-ICPressMetaBox-InTextCreator-Options-Inner { display: block; }
		.icp-ICPress-TinyMCE-Popup .icp-TinyMCESave { float: right; margin: 0.6em 0.5em 0.7em; padding: 0.25em 1em !important; }
		.icp-ICPress-TinyMCE-Popup .icp-TinyMCESave.top { position: absolute; float: none; top: 0; right: 0; }
		div#icp-ICPressMetaBox-Biblio-Options-Inner input, div#icp-ICPressMetaBox-Biblio-Options-Inner select, div#icp-ICPressMetaBox-InTextCreator-Options-Inner input, div#icp-ICPressMetaBox-InTextCreator-Options-Inner select { margin-right: 0; }
		.icp-ICPress-TinyMCE-Popup input, .icp-ICPress-TinyMCE-Popup select { font-family: "Open Sans", sans-serif; }
		.icp-ICPress-TinyMCE-Popup label { font-size: 0.9em; padding: 0 0.5em 0 1.25em; }
		div#icp-ICPressMetaBox-Biblio-Options p.note, div#icp-ICPressMetaBox-InTextCreator-Options p.note { margin: 0 1em 0.75em 1.5em; }
		div#icp-ICPressMetaBox-Biblio-Options div.right, div#icp-ICPressMetaBox-InTextCreator-Options div.right { margin-top: -0.15em; }
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
        
            var shortcode = tinyMCEPopup.getWindowArg("shortcode");
		    var zpInTextBibDefaults = { "style": "apa", "sortby": "default", "sort": "ASC", "images": "no", "download": "no", "notes": "no", "abstract": "no", "cite": "no", "title": "no" };
			
			if ( shortcode != "" ) // Set fields based on shortcode
			{
				var attributes = shortcode.match(/[\w-]+="[^"]*"/g);
				
				if ( attributes != null )
				{
					for (var i = 0; i < attributes.length; i++)
					{
						var attribute = attributes[i].split("=");
						
						if ( attribute[0].replace(/"/gi, '') == "style")
							jQuery("input[name=style]").val(attribute[1].replace(/"/gi, ''));
						else if (attribute[0].replace(/"/gi, '') == "sortby")
							jQuery("select[name=sortby]").val(attribute[1].replace(/"/gi, ''));
						else
							jQuery("input:radio[name="+attribute[0].replace(/"/gi, '')+"][value="+attribute[1].replace(/"/gi, '')+"]").click();
				   }
				}
			}
            
            jQuery(".icp-TinyMCESave").click(function()
			{
				var newShortcode = "[icpressInTextBib";
				
				// Check fields
				jQuery.each(zpInTextBibDefaults, function(attr, value)
				{
					if ( attr == "style" || attr == "sortby" ) {
						if ( jQuery("[name="+attr+"]").val() != value ) {
							if ( jQuery.trim(jQuery("[name="+attr+"]").val()).length > 0 ) {
								newShortcode += ' '+attr+'="'+jQuery("[name="+attr+"]").val()+'"';
							}
						}
					}
					else {
						if ( jQuery("[name="+attr+"]:checked").val() != value ) {
							newShortcode += ' '+attr+'="'+jQuery("[name="+attr+"]:checked").val()+'"';
						}
					}
				});
				newShortcode += "]";
				
				// Save new shortcode
                tinyMCEPopup.editor.execCommand('mceInsertContent', false, "<span class=\"icp-ICPressShortcode list\">"+newShortcode+"</span>");
                tinyMCEPopup.close();
            });
        
        });
        </script>
    </head>
	
	<body id="icp-ICPress-TinyMCE-Cite" class="icp-ICPress-TinyMCE-Popup wp-core-ui">
		
		<!-- START OF OPTIONS -->
		<div id="icp-ICPressMetaBox-InTextCreator-Options">
			
			<h4>Options</h4>
			
			<input type="button" class="icp-TinyMCESave button button-primary top" value="Save" />
			
			<div id="icp-ICPressMetaBox-InTextCreator-Options-Inner">
				
				<label for="icp-ICPressMetaBox-InTextCreator-Options-Style">Style:</label>
					<?php
					
					// See if default exists
					$icp_default_style = "apa";
					if (get_option("ICPress_DefaultStyle")) $icp_default_style = get_option("ICPress_DefaultStyle");
					
					?>
				<input id="icp-ICPressMetaBox-InTextCreator-Options-Style" name="style" type="text" value="<?php echo $icp_default_style; ?>" />
				<p class="note">Styles listed <a title="Zotero-supported citation styles" rel="nofollow" href="http://www.zotero.org/styles">here</a>. Examples: apa, chicago-author-date, nature, modern-language-association.</p>
				
				<hr />
				
				<!--Sort by:-->
				<label for="icp-ICPressMetaBox-InTextCreator-Options-SortBy">Sort by:</label>
				<select id="icp-ICPressMetaBox-InTextCreator-Options-SortBy" name="sortby">
					<option id="default" value="default" rel="default" selected="selected">Latest Added</option>
					<option id="author" value="author">Author</option>
					<option id="date" value="date">Date</option>
					<option id="title" value="title">Title</option>
				</select>
				
				<hr />
				
				<div class="icp-ICPressMetaBox-Field">
					Sort order:
					<div class="right">
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Sort-ASC">Ascending</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Sort-ASC" name="sort" value="ASC" checked="checked" />
						
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Sort-DESC">Descending</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Sort-DESC" name="sort" value="DESC" />
					</div>
				</div>
				
				<hr />
				
				<div class="icp-ICPressMetaBox-Field">
					Show images?
					<div class="right">
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Image-Yes">Yes</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Image-Yes" name="images" value="yes" />
						
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Image-No">No</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Image-No" name="images" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="icp-ICPressMetaBox-Field">
					Show title by year?
					<div class="right">
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Title-Yes">Yes</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Title-Yes" name="title" value="yes" />
						
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Title-No">No</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Title-No" name="title" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="icp-ICPressMetaBox-Field">
					Downloadable?
					<div class="right">
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Download-Yes">Yes</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Download-Yes" name="download" value="yes" />
						
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Download-No">No</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Download-No" name="download" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="icp-ICPressMetaBox-Field">
					Abstract?
					<div class="right">
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Abstract-Yes">Yes</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Abstract-Yes" name="abstract" value="yes" />
						
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Abstract-No">No</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Abstract-No" name="abstract" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="icp-ICPressMetaBox-Field">
					Notes?
					<div class="right">
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Notes-Yes">Yes</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Notes-Yes" name="notes" value="yes" />
						
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Notes-No">No</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Notes-No" name="notes" value="no" checked="checked" />
					</div>
				</div>
				
				<hr />
				
				<div class="icp-ICPressMetaBox-Field">
					Citable (in RIS format)?
					<div class="right">
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Cite-Yes">Yes</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Cite-Yes" name="cite" value="yes" />
						
						<label for="icp-ICPressMetaBox-InTextCreator-Options-Cite-No">No</label>
						<input type="radio" id="icp-ICPressMetaBox-InTextCreator-Options-Cite-No" name="cite" value="no" checked="checked" />
					</div>
				</div>
				
			</div>
		</div>
		<!-- END OF OPTIONS -->
		
		<input type="button" class="icp-TinyMCESave button button-primary" value="Save" />
	
	</body>
</html>