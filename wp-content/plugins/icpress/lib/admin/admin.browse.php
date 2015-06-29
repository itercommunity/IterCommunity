<?php

    global $wpdb;
    
    $icp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."icpress ORDER BY account_type DESC");
    $icp_accounts_total = $wpdb->num_rows;
    
	
	// Display Browse page if there's at least one Zotero account synced
	
    if ( $icp_accounts_total > 0 )
    {
		// FILTER PARAMETERS
		
		// API User ID
		
		global $api_user_id;
		$account_name = false;
		
		if ( isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) )
		{
			$icp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress WHERE api_user_id='".$_GET['api_user_id']."'", OBJECT);
			$api_user_id = $icp_account->api_user_id;
			$account_name = $icp_account->nickname;
		}
		else
		{
			if ( get_option("ICPress_DefaultAccount") )
			{
				$icp_account = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress WHERE api_user_id='".get_option("ICPress_DefaultAccount")."'", OBJECT);
				$api_user_id = $icp_account->api_user_id;
				$account_name = $icp_account->nickname;
			}
			else
			{
				$icp_account = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."icpress LIMIT 1");
				
				if (count($icp_account) > 0)
				{
					$account_name = $icp_account[0]->nickname;
					$account_type = $icp_account[0]->account_type;
					$api_user_id = $icp_account[0]->api_user_id;
					$public_key = $icp_account[0]->public_key;
					$nickname = $icp_account[0]->nickname;
				}
				else
				{
					$api_user_id = false;
				}
			}
		}
		
		
		// ACCOUNT DEFAULTS
		
		if (count($icp_account) == 1)
		{
			$account_type = $icp_account->account_type;
			$api_user_id = $icp_account->api_user_id;
			$public_key = $icp_account->public_key;
			$nickname = $icp_account->nickname;
		}
		
		
		// Use Browse class
		
		$zpLib = new icpressBrowse;
		$zpLib->setAccount($api_user_id);
	?>
    
    <div id="icp-ICPress" class="wrap">
        
        <?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>
        
        <div id="icp-Browse-Wrapper">
            
            <h3><?php if ( count($icp_accounts) == 1 ): echo "Your Library"; else: ?>
            
				<div id="icp-Browse-Accounts">
					<label for="icp-FilterByAccount">Account:</label>
					<select id="icp-FilterByAccount">
						<?php
						
						// DISPLAY ACCOUNTS
						
						foreach ($icp_accounts as $icp_account)
						{
							// DETERMINE CURRENTLY ACTIVE ACCOUNT
							if ($api_user_id && $api_user_id == $icp_account->api_user_id)
							{
								$account_type = $icp_account->account_type;
								$public_key = $icp_account->public_key;
								$nickname = $icp_account->nickname;
							}
							
							// DISPLAY ACCOUNTS IN DROPDOWN
							echo "<option ";
							if ($api_user_id && $api_user_id == $icp_account->api_user_id) echo "selected='selected' ";
							echo "rel='".$icp_account->api_user_id."' value='".$icp_account->api_user_id."'>";
							if ($icp_account->nickname) echo $icp_account->nickname; else echo $icp_account->api_user_id;
							echo "'s Library</option>\n";
						}
						
						?>
					</select>
				</div>
			
			<?php endif; ?></h3>
			
			<div id="icp-Browse-Account-Options">
				
				<?php $is_default = false; if ( get_option("ICPress_DefaultAccount") && get_option("ICPress_DefaultAccount") == $api_user_id ) { $is_default = true; } ?>
				<a href="admin.php?page=ICPress&selective=true&api_user_id=<?php echo $api_user_id; ?>" class="icp-Browse-Account-Import button button-secondary">Selectively Import</a>
				<a href="javascript:void(0);" rel="<?php echo $api_user_id; ?>" class="icp-Browse-Account-Default button button-secondary<?php if ( $is_default ) { echo " selected disabled"; } ?>"><?php if ( $is_default ) { echo "Default"; } else { echo "Set as Default"; } ?></a>
				
			</div>
            
            <span id="ICPRESS_PLUGIN_URL"><?php echo ICPRESS_PLUGIN_URL; ?></span>
            
            <?php $zpLib->getLib(); ?>
			
        </div><!-- #icp-Browse-Wrapper -->
        
    </div>
    
    
<?php } else { ?>
    
    <div id="icp-ICPress">
        
        <div id="icp-Setup">
            
            <div id="icp-ICPress-Navigation">
            
                <div id="icp-Icon" title="Zotero + WordPress = ICPress"><br /></div>
                
                <div class="nav">
                    <div id="step-1" class="nav-item nav-tab-active">System Check</div>
                </div>
            
            </div><!-- #icp-ICPress-Navigation -->
            
            <div id="icp-Setup-Step">
                
                <h3>Welcome to ICPress</h3>
                
                <div id="icp-Setup-Check">
                    
                    <p>
                        Before we get started, let's make sure your system can support ICPress:
                    </p>
                    
                    <?php
                    
                    $icp_check_curl = intval( function_exists('curl_version') );
                    $icp_check_streams = intval( function_exists('stream_get_contents') );
                    $icp_check_fsock = intval( function_exists('fsockopen') );
                    
                    if ( ($icp_check_curl + $icp_check_streams + $icp_check_fsock) <= 1 ) { ?>
                    
                    <div id="icp-Setup-Check-Message" class="error">
                        <p><strong><em>Warning:</em></strong> ICPress requires at least one of the following: <strong>cURL, fopen with Streams (PHP 5), or fsockopen</strong>. You will not be able to import items until your administrator or tech support has set up one of these options. cURL is recommended.</p>
                    </div>
                    
                    <?php } else { ?>
                    
                    <div id="icp-Setup-Check-Message" class="updated">
                        <p><strong><em>Hurrah!</em></strong> Your system meets the requirements necessary for ICPress to communicate with Zotero from WordPress.</p>
                    </div>
                    
                    <p>Sometimes systems aren't configured to allow communication with external websites. Let's check by accessing WordPress.org:
                    
                    <?php
                    
                    $response = wp_remote_get( "https://wordpress.org", array( 'headers' => array("Zotero-API-Version: 2") ) );
                    
                    if ( $response["response"]["code"] == 200 ) { ?>
                    
                    <script>
                    
                    jQuery(document).ready(function() {
                        
                        jQuery("#icp-Connect").removeAttr("disabled").click(function()
                        {
                            window.parent.location = "admin.php?page=ICPress&setup=true";
                            return false;
                        });
                        
                    });
                    
                    </script>
                    
                    <div id="icp-Setup-Check-Message" class="updated">
                        <p><strong><em>Great!</em></strong> We successfully connected to WordPress.org.</p>
                    </div>
                    
                    <p>Everything appears to check out. Let's continue setting up ICPress by adding your Zotero account. Click "Next."
                    
                    <?php } else { ?>
                    
                    <div id="icp-Setup-Check-Message" class="error">
                        <p><strong><em>Warning:</em></strong> ICPress was not able to connect to WordPress.org.</p>
                    </div>
                    
                    <p>Unfortunately, ICPress ran into an error. Here's what WordPress has to say: <?php if ( is_wp_error($response) ) { echo $response->get_error_message(); } else { echo "Sorry, but there's no details on the error." ; } ?></p>
                    
                    <p>First, try reloading. If the error recurs, your system may not be set up to run ICPress. Please contact your system administrator or website host and ask about allowing PHP scripts to access content like RSS feeds from external websites through cURL, fopen with Streams (PHP 5), or fsockopen.</p>
                    
                    <p>You can still try to use ICPress, but it may not work and/or you may encounter further errors.</p>
                    
                    <script>
                    
                    jQuery(document).ready(function() {
                        
                        jQuery("#icp-Connect").removeAttr("disabled").click(function()
                        {
                            window.parent.location = "admin.php?page=ICPress&setup=true";
                            return false;
                        });
                        
                    });
                    
                    </script>
                    
                    <?php }
                    } ?>
                    
                </div>
                
                <div class="proceed">
                    <input id="icp-Connect" name="icp-Connect" class="button-primary" type="submit" value="Next" tabindex="5" disabled="disabled" />
                </div>
                
            </div>
            
        </div>
        
    </div>
    
<?php } ?>