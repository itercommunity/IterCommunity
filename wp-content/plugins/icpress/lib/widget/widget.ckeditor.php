<?php

    if (isset($_GET['iframe']) && trim($_GET['iframe']) == "true")
    {
        // Include WordPress
        require('../../../wp-load.php');
        
        global $wpdb;
        
        require_once( '../../../wp-admin/admin.php' );
        
        $title = __( 'ICPress' );
        
        list( $display_version ) = explode( '-', $wp_version );
        
        include( '../../../wp-admin/admin-header.php' );
        
        if (!defined('WP_USE_THEMES'))
            define('WP_USE_THEMES', false);
            
    } // iframe
?>



<input type="hidden" id="ICPRESS_PLUGIN_URL" name="ICPRESS_PLUGIN_URL" value="<?php echo ICPRESS_PLUGIN_URL; ?>" />



<!-- START OF ICPRESS CKEDITOR DIALOG ------------------------------------------------------------------------------------------------------------------------------------>

<div id="icp-ICPress-CkEditor"<?php if (!isset($_GET['bib'])) { echo ' class="citation"'; } ?>>

<?php if (isset($_GET['bib']) && $_GET['bib'] == "true") { ?>
    <div id="icp-ICPress-CkEditor-Tabs" class="icp-ICPressMetaBox-Tabs">
        
        <ul>
            <li><a href="#icp-ICPress-CkEditor-0">Add/Edit Citations</a></li>
            <li><a href="#icp-ICPress-CkEditor-1">Options</a></li>
        </ul>
    
<?php } ?>
    
        
        <!-- START OF ADD/EDIT CITATION ----------------------------------------------------------------------------------------------------------------------------------------- -->
        <div id="icp-ICPress-CkEditor-0" class="icp-Tab">
            
            <!-- START OF ACCOUNT SELECTION -->
            <div id="icp-ICPressMetaBox-Tabs" class="icp-ICPressMetaBox-Tabs">
                
                <ul>
                    <li><a href="#icp-ICPressMetaBox-Tabs-2">By Collection</a></li>
                    <li><a href="#icp-ICPressMetaBox-Tabs-3">By Tag</a></li>
                </ul>
                
                <!-- START OF By Collection -->
                <div id="icp-ICPressMetaBox-Tabs-2" class="icp-Tab">
                    
                    <!-- NEED TO AUTO-COMPLETE DEFAULT WITH    get_option("ICPress_DefaultAccount")     -->
                    
                    <div id="icp-ICPress-Collection-Account-Select">
                        <label for="icp-ICPressMetaBox-Collection-Accounts">Account:</label>
                        <select id="icp-ICPressMetaBox-Collection-Accounts">
                            <option id='default' class='default' value=''>Choose Account:</option>
                            <?php
                            
                                global $wpdb;
                                $accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."icpress ORDER BY account_type DESC");
                                
                                foreach ($accounts as $account)
                                    if (isset( $account->nickname ))
                                        echo "                    <option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->nickname." (".$account->api_user_id.")</option>\n";
                                    else
                                        echo "                    <option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->api_user_id." (".str_replace("s", "", $account->account_type).")</option>\n";
                            
                            ?>
                        </select>
                    </div>
                    
                </div>
                <!-- END OF By Collection -->
                
                <!-- START OF By Tags -->
                <div id="icp-ICPressMetaBox-Tabs-3" class="icp-Tab">
                    
                    <label for="icp-ICPressMetaBox-Tags-Accounts">Account:</label>
                    <select id="icp-ICPressMetaBox-Tags-Accounts" multiple="yes">
                        <option id='default' class='default' value=''>Choose Account:</option>
                        <?php
                            foreach ($accounts as $account)
                                if (isset( $account->nickname ))
                                    echo "                    <option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->nickname." (".$account->api_user_id.")</option>\n";
                                else
                                    echo "                    <option id='".$account->api_user_id."' class='".$account->account_type."' value='".$account->api_user_id."'>".$account->api_user_id." (".str_replace("s", "", $account->account_type).")</option>\n";
                        ?>
                    </select>
                    
                </div>
                <!-- END OF By Tag -->
                
            </div>
            
        </div> <!-- #icp-ICPress-CkEditor-0 -->
    
<?php if (!isset($_GET['bib'])) { ?>

        <!-- START OF PAGES -->
        <div id="icp-ICPressMetaBox-Pages">
            <label for="icp-ICPressMetaBox-Pages-Input">Page/s:</label>
            <input id="icp-ICPressMetaBox-Pages-Input" type="text" size="10" />
            <input id="icp-ICPressMetaBox-Pages-Button" class="button-secondary" type="button" value="Add" />
            <p class="icp-Note">Optional. Single number or a range, e.g. 3-10.</p>
        </div>
        <!-- START OF PAGES -->
    
    
    
<?php } if (isset($_GET['bib']) && $_GET['bib'] == "true") { ?>

        <!-- START OF OPTIONS ----------------------------------------------------------------------------------------------------------------------------------------------------- -->
        <div id="icp-ICPress-CkEditor-1" class="icp-Tab">
            
            <!-- START OF TYPE: BIB/INTEXT -->
            <div id="icp-ICPressMetaBox-ShortcodeCreator-0" class="icp-Tab">
                <label for="icp-ICPressMetaBox-ShortcodeCreator-0-Type">Choose Type:</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-0-Type">
                    <option id="intext" value="In-Text" selected="selected">In-Text</option>
                    <option id="bib" value="Bibliography">Bibliography</option>
                </select>
            </div>
            <!-- END OF TYPE: BIB/INTEXT -->
            
            <!-- START OF USERID/NICK -->
            <div id="icp-ICPressMetaBox-ShortcodeCreator-1" class="icp-Tab">
                <p class="note">*Only required if you have more than one account.</p>
                <div class="icp-ICPressMetaBox-RadioButtons">
                    <label for="icp-ICPressMetaBox-ShortcodeCreator-1-Type-UserID">User ID:</label>
                    <input id="icp-ICPressMetaBox-ShortcodeCreator-1-Type-UserID" class="icp-ICPressMetaBox-ShortcodeCreator-1-Type" type="radio" value="UserID" />
                    <label for="icp-ICPressMetaBox-ShortcodeCreator-1-Type-Nick">Nickname:</label>
                    <input id="icp-ICPressMetaBox-ShortcodeCreator-1-Type-Nick" class="icp-ICPressMetaBox-ShortcodeCreator-1-Type" type="radio" value="Nickname" />
                </div>
                <?php
                
                $icp_accounts = $wpdb->get_results("SELECT api_user_id, nickname FROM ".$wpdb->prefix."icpress ORDER BY account_type DESC");
                $icp_accounts_total = $wpdb->num_rows;
                
                if ($icp_accounts_total > 0)
                {
                    $icp_userids = "";
                    $icp_nicks = "";
                    foreach ($icp_accounts as $icp_account)
                    {
                        $icp_userids .= "<option id=\"".$icp_account->api_user_id."\" value=\"".$icp_account->api_user_id."\">".$icp_account->api_user_id."</option>\n";
                        $icp_nicks .= "<option id=\"".$icp_account->nickname."\" value=\"".$icp_account->nickname."\">".$icp_account->nickname."</option>\n";
                    }
                }
                
                ?>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-1-TypeText-UserID" class="icp-ICPressMetaBox-ShortcodeCreator-1-UserIDText UserID">
                    <?php echo $icp_userids; ?>
                </select>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-1-TypeText-Nickname" class="icp-ICPressMetaBox-ShortcodeCreator-1-UserIDText Nickname">
                    <?php echo $icp_nicks; ?>
                </select>
            </div>
            <!-- END OF USERID/NICK -->
            
            <!-- START OF AUTHOR/YEAR -->
            <div id="icp-ICPressMetaBox-ShortcodeCreator-2" class="icp-Tab">
                <p class="note">Optional. Be sure to replace spaces with a +.</p>
                <label for="icp-ICPressMetaBox-ShortcodeCreator-2-Author">Author:</label>
                <input id="icp-ICPressMetaBox-ShortcodeCreator-2-Author" type="text" size="20" value="" />
                <input id="icp-ICPressMetaBox-ShortcodeCreator-2-Author-Button" class="button-secondary" type="button" value="Add" />
                <label for="icp-ICPressMetaBox-ShortcodeCreator-2-Year">Year:</label>
                <input id="icp-ICPressMetaBox-ShortcodeCreator-2-Year" type="text" size="20" value="" />
                <input id="icp-ICPressMetaBox-ShortcodeCreator-2-Year-Button" class="button-secondary" type="button" value="Add" />
            </div>
            <!-- END OF AUTHOR/YEAR -->
            
            <!-- START OF DATATYPE -->
            <div id="icp-ICPressMetaBox-ShortcodeCreator-3" class="icp-Tab">
                <p class="note">Optional. Default is "items."</p>
                <label for="icp-ICPressMetaBox-ShortcodeCreator-3-Datatype">Choose Data Type:</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-3-Datatype">
                    <option id="Items" value="Items" selected="selected">Items</option>
                    <option id="Tags" value="Tags">Tags</option>
                    <option id="Collections" value="Collections">Collections</option>
                </select>
            </div>
            <!-- END OF DATATYPE -->
            
            <!-- START OF DISPLAY -->
            <div id="icp-ICPressMetaBox-ShortcodeCreator-4" class="icp-Tab">
                <p class="note">Optional. Default is "bib."</p>
                <label for="icp-ICPressMetaBox-ShortcodeCreator-4-Content">Choose Content:</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-4-Content">
                    <option id="bib" value="bib" selected="selected">bib</option>
                    <option id="html" value="html">html</option>
                </select>
                <p class="note">Optional. Displays title by year.</p>
                <label for="icp-ICPressMetaBox-ShortcodeCreator-4-Title">Show Title?</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-4-Title">
                    <option id="no" value="no" selected="selected">no</option>
                    <option id="yes" value="yes">yes</option>
                </select>
                <p class="note">Optional. Displays image if exists.</p>
                <label for="icp-ICPressMetaBox-ShortcodeCreator-4-Image">Show Image?</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-4-Image">
                    <option id="no" value="no" selected="selected">no</option>
                    <option id="yes" value="yes">yes</option>
                </select>
            </div>
            <!-- END OF DISPLAY -->
            
            <!-- START OF STYLE -->
            <div id="icp-ICPressMetaBox-ShortcodeCreator-5" class="icp-Tab">
                <?php
                
                // Default style, per post or overall
                $icp_default_style = "apa";
                if (get_option("ICPress_DefaultStyle_". get_the_ID()))
                    $icp_default_style = get_option("ICPress_DefaultStyle_". get_the_ID());
                else
                    if (get_option("ICPress_DefaultStyle"))
                        $icp_default_style = get_option("ICPress_DefaultStyle");
                        
                ?>
                <p class="note">Optional. Default is "<?php echo $icp_default_style; ?>."</p>
                
                <label for="icp-ICPressMetaBox-ShortcodeCreator-5-Style">Choose Style:</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-5-Style">
                    <?php
                    
                    $icp_styles = "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nlm, nature, vancouver";
                    $icp_styles = explode(", ", $icp_styles);
                    
                    foreach($icp_styles as $icp_style)
                        if ($icp_style == $icp_default_style)
                            echo "<option id=\"".$icp_style."\" value=\"".$icp_style."\" selected='selected'>".$icp_style."</option>\n";
                        else
                            echo "<option id=\"".$icp_style."\" value=\"".$icp_style."\">".$icp_style."</option>\n";
                    
                    ?>
                </select>
                
                <script type="text/javascript" >
                jQuery(document).ready(function() {
                
                    jQuery("#icp-ICPressMetaBox-ShortcodeCreator-5-Default-Button").click(function()
                    {
                        // Plunk it together
                        var data = 'submit=true&style=' + jQuery('#icp-ICPressMetaBox-ShortcodeCreator-5-Style').val() + '&forpost=true&post=<?php the_ID(); ?>';
                        
                        // Prep for validation
                        jQuery('input#icp-ICPressMetaBox-ShortcodeCreator-5-Default-Button').attr('disabled','true');
                        jQuery('.icp-Loading').show();
                        
                        // Set up uri
                        var xmlUri = '<?php echo ICPRESS_PLUGIN_URL; ?>/icpress.widget.metabox.actions.php?'+data;
                        
                        // AJAX
                        jQuery.get(xmlUri, {}, function(xml)
                        {
                            var $result = jQuery('result', xml).attr('success');
                            
                            jQuery('.icp-Loading').hide();
                            jQuery('input#icp-ICPressMetaBox-ShortcodeCreator-5-Default-Button').removeAttr('disabled');
                            
                            if ($result == "true")
                            {
                                jQuery('div.icp-Errors').hide();
                                jQuery('div.icp-Success').show();
                                
                                jQuery.doTimeout(1000,function() {
                                    jQuery('div.icp-Success').hide();
                                });
                            }
                            else // Show errors
                            {
                                jQuery('div.icp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
                                jQuery('div.icp-Errors').show();
                            }
                        });
                        
                        // Cancel default behaviours
                        return false;
                        
                    });
                    
                });
                </script>
                
                <!--<form id="icp-ICPressMetaBox-ShortcodeCreator-5-Default-Form" action="<?php //echo $PHP_SELF;?>" method="post">-->
                    <label for="icp-ICPressMetaBox-ShortcodeCreator-5-Default-Button">Set Style as Post Default:</label>
                    <input type="button" id="icp-ICPressMetaBox-ShortcodeCreator-5-Default-Button" class="button-secondary" value="Set Default Style" />
                    <div class="icp-Loading">loading</div>
                    <div class="icp-Success">Success!</div>
                    <div class="icp-Errors">Errors!</div>
                <!--</form>-->
                
            </div>
            <!-- END OF STYLE -->
            
            <!-- START OF SORT -->
            <div id="icp-ICPressMetaBox-ShortcodeCreator-6" class="icp-Tab">
                <p class="note">Optional. Default is "latest."</p>
                <label for="icp-ICPressMetaBox-ShortcodeCreator-6-SortBy">Sort By:</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-6-SortBy">
                    <option id="latest" value="latest" selected="selected">latest</option>
                    <option id="author" value="author">author</option>
                    <option id="date" value="date">date</option>
                </select>
                <p class="note">Optional. Default is "desc."</p>
                <label for="icp-ICPressMetaBox-ShortcodeCreator-6-Sort">Sort By:</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-6-Sort">
                    <option id="desc" value="desc" selected="selected">desc</option>
                    <option id="asc" value="asc">asc</option>
                </select>
            </div>
            <!-- END OF SORT -->
            
            <!-- START OF EXTRA -->
            <div id="icp-ICPressMetaBox-ShortcodeCreator-7" class="icp-Tab">
                <p class="note">Optional. Displays download link.</p>
                <label for="icp-ICPressMetaBox-ShortcodeCreator-7-Download">Show Title?</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-7-Download">
                    <option id="no" value="no" selected="selected">no</option>
                    <option id="yes" value="yes">yes</option>
                </select>
                <p class="note">Optional. Displays note/s if they exist.</p>
                <label for="icp-ICPressMetaBox-ShortcodeCreator-7-Notes">Show Image?</label>
                <select id="icp-ICPressMetaBox-ShortcodeCreator-7-Notes">
                    <option id="no" value="no" selected="selected">no</option>
                    <option id="yes" value="yes">yes</option>
                </select>
            </div>
            <!-- END OF DISPLAY -->
            
            <div id="icp-ICPressMetaBox-ShortcodeCreator-Output">
                <label for="icp-ICPressMetaBox-ShortcodeCreator-Text"><span class="inTextOnly">In-Text</span><span class="bibOnly">Bibliography</span> Shortcode:</span></label>
                <textarea id="icp-ICPressMetaBox-ShortcodeCreator-Text">[icpressInText]</textarea>
                <div id="icp-ICPressMetaBox-ShortcodeCreator-Text-InTextBib-Container" class="inTextOnly">
                    <label for="icp-ICPressMetaBox-ShortcodeCreator-Text-InTextBib">In-Text Bibliography Shortcode:</span></label>
                    <p class="note">Copy-n-paste at the end of your post.</p>
                    <input id="icp-ICPressMetaBox-ShortcodeCreator-Text-InTextBib" type="text" value="[icpressInTextBib]" />
                </div>
            </div>
            
        </div>
        
    </div>
    
<?php } // bib ?>



</div><!-- #icp-ICPress-CkEditor -->

<!-- END OF ICPRESS CKEDITOR DIALOG -------------------------------------------------------------------------------------------------------------------------->



<?php if (isset($_GET['bib']) && $_GET['bib'] == "true") { ?>

<div id="icp-ICPress-CKEditor-Output">
    <label for="icp-ICPress-Output-Shortcode">Your shortcode:</label>
    <input id="icp-ICPress-Output-Shortcode" type="text" size="28" />
</div>

<?php } else { ?>

<div id="icp-ICPress-CKEditor-Output">
    <label for="icp-ICPress-Output-Citation">Preview:</label>
    <input id="icp-ICPress-Output-Citation" type="text" size="28" />
    <input id="icp-ICPress-Output-Shortcode" type="text" size="28" />
</div>

<?php } ?>



<?php if (isset($_GET['iframe']) && trim($_GET['iframe']) == "true") { include( '../../../wp-admin/admin-footer.php' ); }  ?>