jQuery(document).ready(function()
{
    
    
    /*
        
        TRIGGER AUTO-UPDATE: Needs to be reworked
        
    */
    
    //var icp_autoupdate_xmlUri = jQuery('.ICPRESS_PLUGIN_URL:first').text() + 'lib/actions/actions.autoupdate.php?autoupdate=true&step=items&api_user_id=all&key=' + jQuery('.ICPRESS_AUTOUPDATE_KEY:first').text();
    ////alert(icp_autoupdate_xmlUri);
    //
    //// AJAX
    //jQuery.get(icp_autoupdate_xmlUri, {}, function(xml)
    //{
    //    var $result = jQuery('result', xml).attr('success');
    //    
    //    if ($result == "true") {
    //        alert("updated");
    //    }
    //    else { // Show errors
    //        alert("error - not time to update yet");
    //    }
    //});
	
	
	
    /*
        
        BROWSE LIB SHORTCODE
        
    */
	
	if ( jQuery("#icp-Browse").length > 0 )
	{
		// NAVIGATE BY COLLECTION
		
		jQuery('div#icp-Browse-Bar').delegate("select#icp-Browse-Collections-Select", "change", function()
		{
			var zpHref = window.location.href.split("?");
			
			if ( jQuery(this).val() != "blank" )
			{
				if ( jQuery(this).val() != "toplevel" ) window.location = zpHref[0] + "?collection_id="+jQuery("option:selected", this).val();
				else window.location = zpHref[0];
			}
		});
		
		
		// NAVIGATE BY TAG
		
		jQuery('div#icp-Browse-Bar').delegate("select#icp-List-Tags", "change", function()
		{
			var zpHref = window.location.href.split("?");
			
			if ( jQuery(this).val() != "No tag selected" ) window.location = zpHref[0] + "?tag_id="+jQuery("option:selected", this).attr("rel");
			else window.location = zpHref[0];
		});
	}
	
    
    
    
    /*
     
        UDPATE STYLE
        
    */
    
    var icp_current_list_items = new Array();
    var icp_all_list_items = new Array();
    
    function zpCorrectOrderedList( $this )
    {
        var icp_current_list_item = 1;
        
        jQuery(".icp-Entry", $this).each(function()
        {
            var $zpEntry = jQuery(this);
            
            if (jQuery(".csl-left-margin", $zpEntry).length > 0 && jQuery(".csl-left-margin", $zpEntry).text().search(/[0-9]+/g) != -1)
            {
                jQuery(".csl-left-margin", $zpEntry).text(jQuery(".csl-left-margin", $zpEntry).text().replace(/[0-9]+/g, icp_current_list_item));
                icp_current_list_item++;
            }
        });
    }
    
    
    
    /*
     
        FORCE NUMBERING
        
    */
    
    function zpForceNumber( $this )
    {
		// Only force numbering if attribute is set
		if ( $this.hasClass("forcenumber") )
		{
			var icp_current_list_item = 1;
			
			jQuery(".icp-Entry", $this).each(function()
			{
				var $zpEntry = jQuery(this);
				
				if ( jQuery(".csl-left-margin", $zpEntry).length == 0 ) // if numbering not found
				{
					jQuery(".csl-entry", $zpEntry).html(icp_current_list_item + ". " + jQuery(".csl-entry", $zpEntry).html());
					icp_current_list_item++;
				}
			});
		}
    }
    
	
	
    /*
     
        FORMAT ICPRESS
        
    */
	
	jQuery(".icp-ICPress").each(function()
    {
        var $this = jQuery(this);
        
        // Update numbered lists
        zpCorrectOrderedList( $this );
        
        var icp_check = "";
		
		// First, check if style has been set
        if (jQuery(".icp-ICPress-Style", $this).length > 0)
		{
            icp_check = jQuery(".icp-ICPress-Style", $this).text();
		}
        else // Otherwise, look at the first item's style according to Zotero
		{
            icp_check = jQuery(".csl-bib-body:first", $this).attr("rel");
		}
        
        var icp_update_style = false;
        jQuery(".csl-bib-body", $this).each(function() {
            if (jQuery(this).attr("rel") != icp_check)
                icp_update_style = true;
        });
        
        if (icp_update_style)
        {
            jQuery(".icp-Entry", $this).each(function()
            {
                // Retain URLs, abstract and note reference
                var zpDownloadURL = ""; if (jQuery(this).find("a.icp-DownloadURL").length > 0) { zpDownloadURL = jQuery(this).find("a.icp-DownloadURL").attr("href"); }
                var zpCiteRIS = ""; if (jQuery(this).find("a.icp-CiteRIS").length > 0) { zpCiteRIS = jQuery(this).find("a.icp-CiteRIS").attr("href"); }
                var zpNoteReference = ""; if (jQuery(this).find(".icp-Notes-Reference").length > 0) { zpNoteReference = jQuery(this).find(".icp-Notes-Reference").text(); }
                var zpAbstractReference = ""; if (jQuery(this).find(".icp-Abstract").length > 0) { zpAbstractReference = jQuery(this).find(".icp-Abstract").html(); }
                
				// Get item id
				var zpItemID = jQuery(this).attr('class').split(' ')[0].split('icp-ID-')[1].split('-')[1];
				
                icp_current_list_items[zpItemID] = [ zpDownloadURL, zpCiteRIS, zpNoteReference, zpAbstractReference ];
                icp_all_list_items[icp_all_list_items.length] = zpItemID;
            });
            
            var icp_style_items = "";
            
            for (var icp_key = 0; icp_key < icp_all_list_items.length; ++icp_key)
                icp_style_items += icp_all_list_items[icp_key] +",";
            
            icp_style_items = icp_style_items.substring(0, icp_style_items.length - 1); // get rid of last comma
			
			var thisAPIUserID = jQuery(".icp-ICPress-Userid:first", $this).text();
            
            // Build URI
            var icp_style_xmlUri = jQuery('.ICPRESS_PLUGIN_URL:first').text() + 'lib/actions/actions.style.php?update=true';
            icp_style_xmlUri += '&api_user_id='+jQuery(".icp-ICPress-Userid:first", $this).text();
            icp_style_xmlUri += '&style='+jQuery(".icp-ICPress-Style:first", $this).text();
            icp_style_xmlUri += '&items='+icp_style_items;
            //alert(icp_style_xmlUri); // DEBUGGING
            
            // AJAX
            jQuery.get(icp_style_xmlUri, {}, function(xml)
            {
                var $result = jQuery('result', xml).attr('success');
                
                if ($result == "true")
                {
                    jQuery('item', xml).each(function()
                    {
                        // Replace with new style
                        jQuery(".icp-ID-" + thisAPIUserID + "-" + jQuery(this).attr("key"), $this).html( jQuery(this).text() );
                        
                        // Re-add URLs, if exist
                        var temp = "";
                        
                        if (icp_current_list_items[jQuery(this).attr("key")][2].length > 0)
                            temp += " <sup class=\"icp-Notes-Reference\">" + icp_current_list_items[jQuery(this).attr("key")][2] + "</sup>";
                        if (icp_current_list_items[jQuery(this).attr("key")][0].length > 0)
                            temp += " <a title=\"Download URL\" href=\"" + icp_current_list_items[jQuery(this).attr("key")][0] + "\">(Download)</a>";
                        if (icp_current_list_items[jQuery(this).attr("key")][1].length > 0)
                            temp += " <a title=\"Cite in RIS Format\" href=\"" + icp_current_list_items[jQuery(this).attr("key")][1] + "\">(Cite)</a>";
                        
                        jQuery(".icp-ID-" + thisAPIUserID + "-" + jQuery(this).attr("key") + " div:last", $this).append( temp );
                        
                        if (icp_current_list_items[jQuery(this).attr("key")][3].length > 0)
                        {
                            temp = "<p class='icp-Abstract'>" + icp_current_list_items[jQuery(this).attr("key")][3] + "</p>\n";
                            jQuery(".icp-ID-" + thisAPIUserID + "-" + jQuery(this).attr("key"), $this).append( temp );
                        }
                    });
                    
                    // Update numbered lists
                    zpCorrectOrderedList( $this );
					
					// Or, number the list, if forced
					zpForceNumber ( $this );
                }
                //else // Show errors
                //{
                //    alert("error - can't update citation styles"); // DEBUGGING
                //}
            });
        } // icp_update_style
		
		else // If style doesn't change, possibly do other things
		{
			// Like numbering the list by force
			zpForceNumber ( $this );
		}
    });
    
    
    
    /*
     
        HIGHLIGHT ENTRY ON JUMP
        
    */
    
    jQuery(".icp-ICPressInText").click( function()
	{
		$this = jQuery(this);
		
		// Get item key from e.g. #icp-256-S74KCIJR
		var zpBibItemKey = $this.attr("href").slice( $this.attr("href").lastIndexOf("-")+1, $this.attr("href").length );
		
		// Highlight bibliography item with that key
		jQuery(".icp-ID-" + thisAPIUserID + "-" +zpBibItemKey).effect("highlight", { color: "#C5EFF7", easing: "easeInExpo" }, 1200);
	});


});