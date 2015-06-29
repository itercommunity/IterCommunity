<?php

// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{
	
?>
            <!-- START OF ACCOUNT -->
            <div class="icp-Column-1">
                <div class="icp-Column-Inner">
                    
                    <h4>Set Default Account</h4>
                    
                    <p class="note">Note: Only applicable if you have multiple synced Zotero accounts.</p>
                    
                    <div id="icp-ICPress-Options-Account" class="icp-ICPress-Options">
                        
                        <label for="icp-ICPress-Options-Account">Choose Account:</label>
                        <select id="icp-ICPress-Options-Account">
                            <?php
                            
                            global $wpdb;
                            $icp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."icpress ORDER BY account_type DESC");
                            $icp_accounts_total = $wpdb->num_rows;
                            
                            // See if default exists
                            $icp_default_account = "";
                            if (get_option("ICPress_DefaultAccount"))
                                $icp_default_account = get_option("ICPress_DefaultAccount");
                            
                            foreach ($icp_accounts as $icp_account)
                                if ($icp_account->api_user_id == $icp_default_account)
                                    echo "<option id=\"".$icp_account->api_user_id."\" value=\"".$icp_account->api_user_id."\" selected='selected'>".$icp_account->api_user_id." (".$icp_account->nickname.") [".substr($icp_account->account_type, 0, strlen($icp_account->account_type)-1)."]</option>\n";
                                else
                                    echo "<option id=\"".$icp_account->api_user_id."\" value=\"".$icp_account->api_user_id."\">".$icp_account->api_user_id." (".$icp_account->nickname.") [".substr($icp_account->account_type, 0, strlen($icp_account->account_type)-1)."]</option>\n";
                            
                            ?>
                        </select>
                        
                        <script type="text/javascript" >
                        jQuery(document).ready(function() {
                        
                            jQuery("#icp-ICPress-Options-Account-Button").click(function()
                            {
                                // Plunk it together
                                var data = 'submit=true&account=' + jQuery('select#icp-ICPress-Options-Account').val();
                                
                                // Prep for data validation
                                jQuery(this).attr('disabled','true');
                                jQuery('#icp-ICPress-Options-Account .icp-Loading').show();
                                
                                // Set up uri
                                var xmlUri = '<?php echo ICPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
                                
                                // AJAX
                                jQuery.get(xmlUri, {}, function(xml)
                                {
                                    var $result = jQuery('result', xml).attr('success');
                                    
                                    jQuery('#icp-ICPress-Options-Account .icp-Loading').hide();
                                    jQuery('input#icp-ICPress-Options-Account-Button').removeAttr('disabled');
                                    
                                    if ($result == "true")
                                    {
                                        jQuery('#icp-ICPress-Options-Account div.icp-Errors').hide();
                                        jQuery('#icp-ICPress-Options-Account div.icp-Success').show();
                                        
                                        jQuery.doTimeout(1000,function() {
                                            jQuery('#icp-ICPress-Options-Account div.icp-Success').hide();
                                        });
                                    }
                                    else // Show errors
                                    {
                                        jQuery('#icp-ICPress-Options-Account div.icp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
                                        jQuery('#icp-ICPress-Options-Account div.icp-Errors').show();
                                    }
                                });
                                
                                // Cancel default behaviours
                                return false;
                                
                            });
                            
                        });
                        </script>
                        
                        <input type="button" id="icp-ICPress-Options-Account-Button" class="button-secondary" value="Set Default Account" />
                        <div class="icp-Loading">loading</div>
                        <div class="icp-Success">Success!</div>
                        <div class="icp-Errors">Errors!</div>
                        
                        <h4 class="clear" />
                        
                    </div>
                    <!-- END OF ACCOUNT -->
                    
                </div>
            </div>
            
            <div class="icp-Column-2">
                <div class="icp-Column-Inner">
                    
                    <!-- START OF STYLE -->
                    <h4>Set Default Citation Style</h4>
                    
                    <p class="note">Note: Styles must be listed <a title="Zotero Styles" href="http://www.zotero.org/styles">here</a>. Use the name found in the style's URL, e.g. modern-language-association.</p>
                    
                    <div id="icp-ICPress-Options-Style-Container" class="icp-ICPress-Options">
                        
                        <label for="icp-ICPress-Options-Style">Choose Style:</label>
                        <select id="icp-ICPress-Options-Style">
                            <?php
                            
                            if (!get_option("ICPress_StyleList"))
                                add_option( "ICPress_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nature, vancouver");
                            
                            $icp_styles = explode(", ", get_option("ICPress_StyleList"));
                            sort($icp_styles);
                            
                            // See if default exists
                            $icp_default_style = "apa";
                            if (get_option("ICPress_DefaultStyle"))
                                $icp_default_style = get_option("ICPress_DefaultStyle");
                            
                            foreach($icp_styles as $icp_style)
                                if ($icp_style == $icp_default_style)
                                    echo "<option id=\"".$icp_style."\" value=\"".$icp_style."\" selected='selected'>".$icp_style."</option>\n";
                                else
                                    echo "<option id=\"".$icp_style."\" value=\"".$icp_style."\">".$icp_style."</option>\n";
                            
                            ?>
                            <option id="new" value="new-style">Add another style ...</option>
                        </select>
                        
                        <div id="icp-ICPress-Options-Style-New-Container">
                            <label for="icp-ICPress-Options-Style-New">Add Style:</label>
                            <input id="icp-ICPress-Options-Style-New" type="text" />
                        </div>
                        
                        <script type="text/javascript" >
                        jQuery(document).ready(function() {
                            
                            // Show/hide add style input
                            jQuery("#icp-ICPress-Options-Style").change(function()
                            {
                                if (this.value === 'new-style')
                                {
                                    jQuery("#icp-ICPress-Options-Style-New-Container").show();
                                }
                                else
                                {
                                    jQuery("#icp-ICPress-Options-Style-New-Container").hide();
                                    jQuery("#icp-ICPress-Options-Style-New").val("");
                                }
                            });
                            
                            jQuery("#icp-ICPress-Options-Style-Button").click(function()
                            {
                                var styleOption = jQuery('select#icp-ICPress-Options-Style').val();
                                var updateStyleList = false;
                                
                                // Determine if using existing or adding new; if adding new, also update ICPress_StyleList option
                                if ( styleOption == "new-style" )
                                {
                                    styleOption = jQuery("#icp-ICPress-Options-Style-New").val();
                                    updateStyleList = true;
                                }
                                
                                if ( styleOption != "" )
                                {
                                    // Plunk it together
                                    var data = 'submit=true&style=' + styleOption;
                                    
                                    // Prep for data validation
                                    jQuery(this).attr('disabled','true');
                                    jQuery('#icp-ICPress-Options-Style-Container .icp-Loading').show();
                                    
                                    // Set up uri
                                    var xmlUri = '<?php echo ICPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
                                    
                                    // AJAX
                                    jQuery.get(xmlUri, {}, function(xml)
                                    {
                                        var $result = jQuery('result', xml).attr('success');
                                        
                                        jQuery('#icp-ICPress-Options-Style-Container .icp-Loading').hide();
                                        jQuery('input#icp-ICPress-Options-Style-Button').removeAttr('disabled');
                                        
                                        if ($result == "true")
                                        {
                                            jQuery('#icp-ICPress-Options-Style-Container div.icp-Errors').hide();
                                            jQuery('#icp-ICPress-Options-Style-Container div.icp-Success').show();
                                            
                                            jQuery.doTimeout(1000,function()
                                            {
                                                jQuery('#icp-ICPress-Options-Style-Container div.icp-Success').hide();
                                                
                                                if (updateStyleList === true)
                                                {
                                                    jQuery('#icp-ICPress-Options-Style').prepend(jQuery("<option/>", {
                                                        value: styleOption,
                                                        text: styleOption,
                                                        selected: "selected"
                                                    }));
                                                    
                                                    jQuery("#icp-ICPress-Options-Style-New-Container").hide();
                                                    jQuery("#icp-ICPress-Options-Style-New").val("");
                                                }
                                            });
                                        }
                                        else // Show errors
                                        {
                                            jQuery('#icp-ICPress-Options-Style-Container div.icp-Errors').html(jQuery('errors', xml).text()+"\n");
                                            jQuery('#icp-ICPress-Options-Style-Container div.icp-Errors').show();
                                        }
                                    });
                                }
                                else // Show errors
                                {
                                    jQuery('#icp-ICPress-Options-Style-Container div.icp-Errors').html("No style was entered.\n");
                                    jQuery('#icp-ICPress-Options-Style-Container div.icp-Errors').show();
                                }
                                
                                // Cancel default behaviours
                                return false;
                                
                            });
                            
                        });
                        </script>
                        
                        <input type="button" id="icp-ICPress-Options-Style-Button" class="button-secondary" value="Set Default Style" />
                        <div class="icp-Loading">loading</div>
                        <div class="icp-Success">Success!</div>
                        <div class="icp-Errors">Errors!</div>
                        
                        <h4 class="clear" />
                        
                    </div>
                    <!-- END OF STYLE -->
                    
                </div>
            </div>
            
            <?php /* autoupdate temporarily disabled */ if ( 1==2) { ?>
            <hr />
            
            <div class="icp-Column-1">
                <div class="icp-Column-Inner">
                    
                    <h4>Set Auto-Update</h4>
                    
                    <p class="note">Have ICPress automatically sync your Zotero accounts.</p>
                    
                    <div id="icp-ICPress-Options-AutoUpdate" class="icp-ICPress-Options">
                        
                        <label for="icp-ICPress-Options-AutoUpdate">Choose Interval:</label>
                        <select id="icp-ICPress-Options-AutoUpdate">
                            <?php
                            
                            // See if default exists
                            $icp_default_autoupdate = "weekly";
                            if (get_option("ICPress_DefaultAutoUpdate"))
                                $icp_default_autoupdate = get_option("ICPress_DefaultAutoUpdate");
                            
                            ?>
                            <option id="daily" <?php if ($icp_default_autoupdate == "daily") { ?>selected="selected"<?php } ?>>Daily</option>
                            <option id="weekly" <?php if ($icp_default_autoupdate == "weekly") { ?>selected="selected"<?php } ?>>Weekly</option>
                        </select>
                        
                        <script type="text/javascript" >
                        jQuery(document).ready(function() {
                        
                            jQuery("#icp-ICPress-Options-AutoUpdate-Button").click(function()
                            {
                                // Plunk it together
                                var data = 'submit=true&autoupdate=' + jQuery('select#icp-ICPress-Options-AutoUpdate').val();
                                
                                // Prep for data validation
                                jQuery(this).attr('disabled','true');
                                jQuery('#icp-ICPress-Options-AutoUpdate .icp-Loading').show();
                                
                                // Set up uri
                                var xmlUri = '<?php echo ICPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
                                
                                // AJAX
                                jQuery.get(xmlUri, {}, function(xml)
                                {
                                    var $result = jQuery('result', xml).attr('success');
                                    
                                    jQuery('#icp-ICPress-Options-AutoUpdate .icp-Loading').hide();
                                    jQuery('input#icp-ICPress-Options-AutoUpdate-Button').removeAttr('disabled');
                                    
                                    if ($result == "true")
                                    {
                                        jQuery('#icp-ICPress-Options-AutoUpdate div.icp-Errors').hide();
                                        jQuery('#icp-ICPress-Options-AutoUpdate div.icp-Success').show();
                                        
                                        jQuery.doTimeout(1000,function() {
                                            jQuery('#icp-ICPress-Options-AutoUpdate div.icp-Success').hide();
                                        });
                                    }
                                    else // Show errors
                                    {
                                        jQuery('#icp-ICPress-Options-AutoUpdate div.icp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
                                        jQuery('#icp-ICPress-Options-AutoUpdate div.icp-Errors').show();
                                    }
                                });
                                
                                // Cancel default behaviours
                                return false;
                                
                            });
                            
                        });
                        </script>
                        
                        <input type="button" id="icp-ICPress-Options-AutoUpdate-Button" class="button-secondary" value="Set Auto-Update Interval" />
                        <div class="icp-Loading">loading</div>
                        <div class="icp-Success">Success!</div>
                        <div class="icp-Errors">Errors!</div>
                        
                    </div>
                    <!-- END OF ACCOUNT -->
                    
                </div>
            </div><!-- .icp-Column-1 --><?php } ?>
			
			
<?php

} // !current_user_can('edit_others_posts')

else
{
	echo "<p>Sorry, you don't have permission to access this page.</p>";
}

?>