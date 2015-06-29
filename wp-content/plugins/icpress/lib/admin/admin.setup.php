<?php if (!isset( $_GET['setupstep'] )) { ?>

    <div id="icp-Setup">
        
        <div id="icp-ICPress-Navigation">
        
            <div id="icp-Icon" title="Zotero + WordPress = ICPress"><br /></div>
            
            <div class="nav">
                <div id="step-1" class="nav-item nav-tab-active"><strong>1.</strong> Validate Account</div>
                <div id="step-2" class="nav-item"><strong>2.</strong> Default Options</div>
                <div id="step-3" class="nav-item"><strong>3.</strong> Import</div>
            </div>
        
        </div><!-- #icp-ICPress-Navigation -->
        
        <div id="icp-Setup-Step">
            
            <?php
            
            $icp_check_curl = intval( function_exists('curl_version') );
            $icp_check_streams = intval( function_exists('stream_get_contents') );
            $icp_check_fsock = intval( function_exists('fsockopen') );
            
            if ( ($icp_check_curl + $icp_check_streams + $icp_check_fsock) <= 1 ) { ?>
            <div id="icp-Setup-Check" class="error">
                <p><strong>Warning.</strong> ICPress requires at least one of the following to work: cURL, fopen with Streams (PHP 5), or fsockopen. You will not be able to import items until your administrator or tech support has set up one of these options. cURL is recommended.</p>
            </div>
            <?php } ?>
            
            <div id="icp-AddAccount-Form" class="visible">
                <?php include('admin.accounts.addform.php'); ?>
            </div>
            
        </div>
        
    </div>
    
    
    
<?php } else if (isset($_GET['setupstep']) && $_GET['setupstep'] == "two") { ?>

    <div id="icp-Setup">
        
        <div id="icp-ICPress-Navigation">
        
            <div id="icp-Icon" title="Zotero + WordPress = ICPress"><br /></div>
            
            <div class="nav">
                <div id="step-1" class="nav-item"><strong>1.</strong> Validate Account</div>
                <div id="step-2" class="nav-item nav-tab-active"><strong>2.</strong> Default Options</div>
                <div id="step-3" class="nav-item"><strong>3.</strong> Import</div>
            </div>
        
        </div><!-- #icp-ICPress-Navigation -->
        
        <div id="icp-Setup-Step">
            
            <h3>Set Default Options</h3>
            
            <?php include("admin.options.form.php"); ?>
            
            <div id="icp-ICPress-Setup-Buttons" class="proceed">
                <input type="button" id="icp-ICPress-Setup-Options-Next" class="button-primary" value="Next" />
            </div>
            
        </div>
        
    </div>
    
    
    
<?php } else if (isset($_GET['setupstep']) && $_GET['setupstep'] == "three") { ?>

    <?php
    
        if (isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1)
        {
            $api_user_id = htmlentities($_GET['api_user_id']);
        }
        else // not set, so select last added
        {
            global $wpdb;
            $api_user_id = $wpdb->get_var( "SELECT api_user_id FROM ".$wpdb->prefix."icpress ORDER BY id DESC LIMIT 1" );
        }
        
    ?>


    <div id="icp-Setup">
        
        <div id="icp-ICPress-Navigation">
        
            <div id="icp-Icon" title="Zotero + WordPress = ICPress"><br /></div>
            
            <div class="nav">
                <div id="step-1" class="nav-item"><strong>1.</strong> Validate Account</div>
                <div id="step-2" class="nav-item"><strong>2.</strong> Default Options</div>
                <div id="step-3" class="nav-item nav-tab-active"><strong>3.</strong> Import</div>
            </div>
        
        </div><!-- #icp-ICPress-Navigation -->
        
        <div id="icp-Setup-Step" class="import">
            
            <?php if ($api_user_id) {
                global $wpdb;
                $temp = $wpdb->get_row("SELECT nickname FROM ".$wpdb->prefix."icpress WHERE api_user_id='".$api_user_id."'", OBJECT);
            ?>
            <h3>Import <?php if (strlen($temp->nickname) > 0) { echo $temp->nickname; } else { echo $api_user_id; }?>'s Library</h3>
            <?php } else { ?>
            <h3>Import Zotero Library</h3>
            <?php } ?>
            
            <div id="icp-Step-Import">
                
                <p>
                    The importing process might take a few minutes, depending on what you choose to import and the size of your Zotero library.
                </p>
                
                <div id="icp-ICPress-Setup-Import-Buttons">
                    <input id="icp-ICPress-Setup-Import" type="button" disabled="disabled" class="button-primary" value="Import Everything" />
                    <input id="icp-ICPress-Setup-Import-Items" type="button" disabled="disabled" class="button-secondary icp-Import-Button" value="Import Items" />
                    <input id="icp-ICPress-Setup-Import-Collections" type="button" disabled="disabled" class="button-secondary icp-Import-Button" value="Import Collections" />
                    <input id="icp-ICPress-Setup-Import-Tags" type="button" disabled="disabled" class="button-secondary icp-Import-Button" value="Import Tags" />
                    
                    <div class="icp-Loading-Container">
                        <div class="icp-Loading-Initial icp-Loading-Import regular"></div>
                        <div class="icp-Import-Messages regular">Importing items 1-50 ...</div>
                    </div>
                </div>
                
            </div>
            
        </div>
        
        <div class="icp-Setup-Step second">
            
            <div class="icp-Step-Import">
                
                <p>
                    Alternatively, you can selectively import top-level collections below. You may need to wait a few moments if you have several top-level collections.
                </p>
                
                <div id="icp-Step-Import-Collection" class="loading">
                    <iframe id="icp-Step-Import-Collection-Frame" name="icp-Step-Import-Collection-Frame"
						src="<?php echo wp_nonce_url( ICPRESS_PLUGIN_URL . 'lib/import/import.collection.php?api_user_id=' . $api_user_id, 'icp_importing_' . intval($api_user_id) . '_' . date('Y-j-G'), 'icp_nonce' ); ?>"
						scrolling="no" frameborder="0" marginwidth="0" marginheight="0">
					</iframe>
                </div><!-- #icp-Step-Import-Collection -->
                
                <input id="icp-ICPress-Setup-Import-Selective" type="button" disabled="disabled" class="button-secondary" value="Import Selected" />
                
                <div class="icp-Loading-Container selective">
                    <div class="icp-Loading-Initial icp-Loading-Import selective"></div>
                    <div class="icp-Import-Messages selective">Importing selected collection(s) ...</div>
                </div>
                
            </div>
            
            <iframe id="icp-Setup-Import" name="icp-Setup-Import"
				src="<?php echo wp_nonce_url( ICPRESS_PLUGIN_URL . 'lib/import/import.iframe.php?api_user_id=' . $api_user_id, 'icp_importing_' . intval($api_user_id) . '_' . date('Y-j-G'), 'icp_nonce' ); ?>"
				scrolling="yes" frameborder="0" marginwidth="0" marginheight="0">
			</iframe>
            
            <div id="icp-ICPress-Setup-Buttons" class="proceed" style="display: none;">
                <input type="button" id="icp-ICPress-Setup-Options-Complete" class="button-primary" value="Finish" />
            </div>
            
        </div>
        
    </div>
    
<?php } ?>