jQuery(document).ready(function() {


	/*
		
		NAVIGATION STYLES
		
	*/
	
    jQuery("div#icp-ICPress div#icp-ICPress-Navigation a.nav-item").click( function() {
        jQuery(this).addClass("active");
    });
	jQuery(".icp-List-Subcollection").focus(function() {
		jQuery(this).addClass("down");
	});
	
	
	
	/*
		
		COPYING ITEM KEYS ON CLICK
		
	*/
	
	jQuery('.icp-Entry-ID-Text span').click( function() {
		jQuery(this).parent().find('input').show().select();
		jQuery(this).hide();
	});
	jQuery('.icp-Entry-ID-Text input').blur( function() {
		jQuery(this).hide();
		jQuery(this).parent().find('span').show();
	});
	
	jQuery('.icp-Collection-Title .item_key_inner span').click( function() {
		jQuery(this).parent().find('input').show().select();
		jQuery(this).hide();
	});
	jQuery('.icp-Collection-Title .item_key_inner input').blur( function() {
		jQuery(this).hide();
		jQuery(this).parent().find('span').show();
	});
	
	
	
	/*
		
		FILTER CITATIONS
		
	*/
	
	// FILTER BY ACCOUNT
	
	jQuery('div#icp-Browse-Accounts').delegate("select#icp-FilterByAccount", "change", function()
	{
		var id = jQuery(this).val();
		
		jQuery(this).addClass("loading");
		jQuery("#icp-Browse-Account-Options a").addClass("disabled").unbind("click",
			function (e) {
				e.preventDefault();
				return false;
			}
		);
		
		window.location = "admin.php?page=ICPress&api_user_id="+id;
	});
	
	
	// FILTER BY TAG
	
	jQuery('div#icp-Browse-Bar').delegate("select#icp-List-Tags", "change", function()
	{
		if ( jQuery(this).val() != "No tag selected" ) window.location = "admin.php?page=ICPress&api_user_id="+jQuery('select#icp-FilterByAccount option:selected').val()+"&tag_id="+jQuery("option:selected", this).attr("rel");
	});
	
	
	
	/*
		
		CITATION IMAGE HOVER
		
	*/
	
	jQuery('div#icp-List').delegate("div.icp-Entry-Image", "hover", function () {
		jQuery(this).toggleClass("hover");
	});
	
	
	
	/*
		
		SET IMAGE FOR ENTRIES
		Thanks to http://www.webmaster-source.com/2013/02/06/using-the-wordpress-3-5-media-uploader-in-your-plugin-or-theme/
		
	*/
	
	var icp_uploader;
	
	jQuery('.icp-Entry-Image a.upload').click(function(e)
	{
        e.preventDefault();
		$this = jQuery(this);
		
        if (icp_uploader)
		{
            icp_uploader.open();
            return;
        }
		
        icp_uploader = wp.media.frames.file_frame = wp.media(
		{
			title: 'Choose Image',
			button: {
				text: 'Choose Image'
			},
			multiple: false
		});
		
        icp_uploader.on( 'select', function()
		{
            attachment = icp_uploader.state().get('selection').first().toJSON();
			var icp_xml_url = jQuery('#ICPRESS_PLUGIN_URL').text()
					+ 'lib/actions/actions.php?image=true&api_user_id='+jQuery(".icp-Browse-Account-Default").attr("rel")+'&entry_id='+$this.attr('rel')+'&image_id='+attachment.id;
			
			// Save as featured image
			jQuery.get( icp_xml_url, {}, function(xml)
			{
				var $result = jQuery('result', xml).attr('success');
				
				if ( $result == "true" )
				{
					if ( $this.parent().find(".thumb").length > 0 ) {
						$this.parent().find(".thumb").attr("src", attachment.url);
					}
					else {
						$this.parent().addClass("hasimage");
						$this.parent().prepend("<img class='thumb' src='"+attachment.url+"' alt='image' />");
					}
				}
				else // Show errors
				{
					alert ("Sorry, featured image couldn't be set.");
				}
			});
        });
		
        icp_uploader.open();
		
    });
	
	
	// REMOVE FEATURED IMAGE
	
	jQuery(".icp-Entry-Image a.delete").click( function(e)
	{
        e.preventDefault();
		$this = jQuery(this);
		
		var icp_xml_url = jQuery('#ICPRESS_PLUGIN_URL').text() + 'lib/actions/actions.php?remove=image&image_id='+$this.parent().attr('rel');
		
		// Save as featured image
		jQuery.get( icp_xml_url, {}, function(xml)
		{
			var $result = jQuery('result', xml).attr('success');
			
			if ( $result == "true" )
			{
				$this.parent().removeClass("hasimage");
				$this.parent().find(".thumb").remove();
			}
			else // Show errors
			{
				alert ("Sorry, featured image couldn't be set.");
			}
		});
	});
	
	
	
	// BROWSE PAGE: SET DEFAULT ACCOUNT
	
	jQuery(".icp-Browse-Account-Import.button").click(function() { jQuery(this).addClass("loading"); });
	
	jQuery(".icp-Browse-Account-Default.button").click(function()
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
				
				jQuery.doTimeout(1000,function() {
					$this.removeClass("success").addClass("selected disabled");
				});
			}
			else // Show errors
			{
				alert("Sorry, but there were errors: " + jQuery('errors', xml).text());
			}
		});
		
		// Cancel default behaviours
		return false;
		
	});
    
    
});