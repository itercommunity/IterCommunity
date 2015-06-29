jQuery(document).ready( function()
{
    
    
    /*
     
        SETUP BUTTONS
        
    */

    jQuery("input#icp-ICPress-Setup-Options-Next").click(function()
    {
        window.parent.location = "admin.php?page=ICPress&setup=true&setupstep=three";
        return false;
    });

    jQuery("input#icp-ICPress-Setup-Options-Complete").click(function()
    {
        if ( jQuery(this).hasClass("import") )
            window.parent.location = "admin.php?page=ICPress";
        else
            window.parent.location = "admin.php?page=ICPress&accounts=true";
        return false;
    });
    
    
    
    /*
        
        SYNC ACCOUNT WITH ICPRESS
        
    */

    jQuery('#icp-Connect').click(function ()
    {
        var data = 'connect=true'
                    + '&account_type=' + jQuery('select[name=account_type] option:selected').val()
                    + '&api_user_id=' + jQuery('input[name=api_user_id]').val()
                    + '&public_key=' + jQuery('input[name=public_key]').val()
                    + '&nickname=' + escape(jQuery('input[name=nickname]').val());
        
        // Disable all the text fields
        jQuery('input[name!=update], textarea, select').attr('disabled','true');
        
        // Show the loading sign
        jQuery('.icp-Errors').hide();
        jQuery('.icp-Success').hide();
        jQuery('.icp-Loading').show();
        
        // Set up uri
        var xmlUri = jQuery('input[name=ICPRESS_PLUGIN_URL]').val() + 'lib/actions/actions.php?'+data;
        
        if (jQuery('input[name=update]').val() !== undefined)
            xmlUri += "&update=" + jQuery('input[name=update]').val();
        
        // AJAX
        jQuery.get(xmlUri, {}, function(xml)
        {
            var $result = jQuery('result', xml).attr('success');
            
            if ($result == "true")
            {
                jQuery('div.icp-Errors').hide();
                jQuery('.icp-Loading').hide();
                jQuery('div.icp-Success').html("<p><strong>Success!</strong> Your Zotero account has been validated.</p>\n");
                
                jQuery('div.icp-Success').show();
                
                // SETUP
                if (jQuery("div#icp-Setup").length > 0)
                {
                    jQuery.doTimeout(1000,function() {
                        window.parent.location = "admin.php?page=ICPress&setup=true&setupstep=two";
                    });
                }
                
                // REGULAR
                else 
                {
                    jQuery.doTimeout(1000,function()
                    {
                        jQuery('div#icp-AddAccount').slideUp("fast");
                        jQuery('form#icp-Add')[0].reset();
                        jQuery('input[name!=update], textarea, select').removeAttr('disabled');
                        jQuery('div.icp-Success').hide();
                        
                        DisplayAccounts();
                    });
                }
            }
            else // Show errors
            {
                jQuery('input, textarea, select').removeAttr('disabled');
                jQuery('div.icp-Errors').html("<p><strong>Oops!</strong> "+jQuery('errors', xml).text()+"</p>\n");
                jQuery('div.icp-Errors').show();
                jQuery('.icp-Loading').hide();
            }
        });
        
        return false;
    });
    
    
    
    /*
     
        OAUTH MODAL
        
    */
    
    jQuery('a.icp-OAuth-Button').livequery('click', function() { 
        tb_show('', jQuery(this).attr('href')+'&TB_iframe=true');
        return false;
    });


    

    /*
        
        REMOVE ACCOUNT
        
    */

    jQuery('#icp-Accounts').delegate(".actions a.delete", "click", function () {
        
        $this = jQuery(this);
        $thisProject = $this.parent().parent();
        
        var confirmDelete = confirm("Are you sure you want to remove this account?");
        
        if (confirmDelete==true)
        {
            var xmlUri = jQuery('#ICPRESS_PLUGIN_URL').text() + 'lib/actions/actions.php?delete=' + $this.attr("href").replace("#", "");
            
            jQuery.get(xmlUri, {}, function(xml)
            {
                if ( jQuery('result', xml).attr('success') == "true" )
                {
                    if ( jQuery('result', xml).attr('total_accounts') == 0 )
                        window.location = 'admin.php?page=ICPress';
                    else
                        window.location = 'admin.php?page=ICPress&accounts=true';
                }
                else
                {
                    alert( "Sorry - couldn't delete that account." );
                }
            });
        }
        
    });
    
    
    
    /*
     
        SET UP IMPORT BUTTON
        
    */
    
    jQuery("iframe#icp-Setup-Import").ready(function()
    {
        jQuery("input#icp-ICPress-Setup-Import").removeAttr('disabled');
        jQuery("input.icp-Import-Button").removeAttr('disabled');
        
        // IMPORT ITEMS
        jQuery("input#icp-ICPress-Setup-Import-Items").click(function()
        {
            jQuery(".import .icp-Loading-Initial").show();
            jQuery(".import .icp-Import-Messages").show();
            jQuery("input[type=button]").attr('disabled', 'true');
            
            jQuery("iframe#icp-Setup-Import").attr('src', jQuery("iframe#icp-Setup-Import").attr('src') + "&go=true&step=items&singlestep=true");
            
            return false;
        });
        
        // IMPORT COLLECTIONS
        jQuery("input#icp-ICPress-Setup-Import-Collections").click(function()
        {
            jQuery(".import .icp-Loading-Initial").show();
            jQuery(".import .icp-Import-Messages").text("Importing collections 1-50 ...").show();
            jQuery("input[type=button]").attr('disabled', 'true');
            
            jQuery("iframe#icp-Setup-Import").attr('src', jQuery("iframe#icp-Setup-Import").attr('src') + "&go=true&step=collections&singlestep=true");
            
            return false;
        });
        
        // IMPORT TAGS
        jQuery("input#icp-ICPress-Setup-Import-Tags").click(function()
        {
            jQuery(".import .icp-Loading-Initial").show();
            jQuery(".import .icp-Import-Messages").text("Importing tags 1-50 ...").show();
            jQuery("input[type=button]").attr('disabled', 'true');
            
            jQuery("iframe#icp-Setup-Import").attr('src', jQuery("iframe#icp-Setup-Import").attr('src') + "&go=true&step=tags&singlestep=true");
            
            return false;
        });
        
        // IMPORT EVERYTHING
        jQuery("input#icp-ICPress-Setup-Import").click(function()
        {
            jQuery(".import .icp-Loading-Initial").show();
            jQuery(".import .icp-Import-Messages").show();
            jQuery("input[type=button]").attr('disabled', 'true');
            
            jQuery("iframe#icp-Setup-Import").attr('src', jQuery("iframe#icp-Setup-Import").attr('src') + "&go=true&step=items");
            
            return false;
        });
    });
    
    
    
    /*
        
        SET UP SYNC BUTTON
        
    */

    //jQuery('div#icp-AccountsList div.icp-Account .actions a.sync').click(function(e)
    //{
    //    var $this = jQuery(this);
    //    
    //    // Disable sync link until done
    //    e.preventDefault();
    //    
    //    // Prep and show loading sign
    //    $this.removeClass("success");
    //    $this.removeClass("error");
    //    $this.addClass("syncing");
    //    
    //    // Add sync iframe to DOM
    //    if (jQuery("iframe#icp-Sync-" + jQuery("span", $this).text()).length == 0)
    //    {
    //        jQuery('<iframe/>', {
    //            id: 'icp-Sync-' + jQuery('span.api_user_id', $this.parent().parent()).text(),
    //            'class': 'icp-Setup-Sync', // IE ISSUE - needs quotations around class
    //            //src: jQuery('#ICPRESS_PLUGIN_URL').text() + 'lib/import/sync.php?api_user_id=' + $this.attr("rel") + '&key=' + jQuery("span#ICPRESS_PASSCODE").text() + '&step=items',
    //            src: jQuery('#ICPRESS_PLUGIN_URL').text() + 'lib/import/sync.php?api_user_id=' + $this.attr("rel") + '&step=items',
    //            scrolling: 'yes'
    //        }).appendTo('#icp-ManageAccounts');
    //    }
    //    else
    //    {
    //        jQuery("iframe#icp-Sync-" + jQuery("span", $this).text()).attr("src", jQuery('#ICPRESS_PLUGIN_URL').text() + 'lib/import/sync.php?api_user_id=' + $this.attr("rel") + '&key=' + jQuery("span", $this).text() + '&step=items');
    //    }
    //    
    //    $this.parent().find('.icp-Sync-Messages').text("Syncing items 1-50 ...");
    //    
    //    return false;
    //});
    
    
    
    
    /*
        
        SELECTIVE IMPORT BY COLLECTION
        
    */
    
    jQuery("iframe#icp-Step-Import-Collection-Frame").on("load", function()
    {
        jQuery("#icp-Step-Import-Collection").removeClass("loading");
        jQuery("#icp-Step-Import-Collection, iframe#icp-Step-Import-Collection-Frame").animate({ height: jQuery("iframe#icp-Step-Import-Collection-Frame").contents().find(".icp-Collection-List").outerHeight() + "px"}, 0);
        jQuery("input#icp-ICPress-Setup-Import-Selective").removeAttr('disabled');
    });
    
    jQuery("#icp-ICPress-Setup-Import-Selective").click(function ()
    {
        if ( jQuery("#icp-Step-Import-Collection-Frame").contents().find(".icp-Collection.selected").length > 0 )
        {
            var zpSelectedCollections = "";
            
            jQuery("#icp-Step-Import-Collection-Frame").contents().find(".icp-Collection.selected").each( function()
            {
                zpSelectedCollections += jQuery(this).attr("rel") + ",";
            });
            
            zpSelectedCollections = zpSelectedCollections.slice(0, - 1);
            
            jQuery(".selective.icp-Loading-Initial").show();
            jQuery(".selective.icp-Import-Messages").text("Importing items 1-50 ....").show();
            jQuery(this).attr('disabled', 'true');
            
            jQuery("iframe#icp-Setup-Import").attr('src', jQuery("iframe#icp-Setup-Import").attr('src') + "&go=true&step=selective&collections=" + zpSelectedCollections);
            
            return false;
        }
        else
        {
            alert ("Please select at least one collection to import."); return false;
        }
    });
    
    
	
	
	// SET DEFAULT ACCOUNT
	
	jQuery(".icp-Accounts-Default").click(function()
	{
		var $this = jQuery(this);
		
		// Plunk it together
		var data = 'submit=true&account=' + $this.attr("rel");
		
		// Prep for data validation
		$this.addClass("loading");
		
		// Set up uri
		var xmlUri = jQuery('#ICPRESS_PLUGIN_URL').text() + 'lib/widget/widget.metabox.actions.php?'+data;
		
		// AJAX
		jQuery.get(xmlUri, {}, function(xml)
		{
			var $result = jQuery('result', xml).attr('success');
			
			$this.removeClass("success loading");
			
			if ($result == "true")
			{
				$this.addClass("success");
				jQuery(".icp-Accounts-Default").parent().removeClass("selected");
				
				jQuery.doTimeout(1000,function() {
					$this.removeClass("success");
					$this.parent().addClass("selected");
				});
			}
			else // Show errors
			{
				alert(jQuery('errors', xml).text());
			}
		});
		
		// Cancel default behaviours
		return false;
		
	});


});