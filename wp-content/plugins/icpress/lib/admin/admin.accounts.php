<?php

// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{

	// Determine if server supports OAuth
	if (in_array ('oauth', get_loaded_extensions())) { $oauth_is_not_installed = false; } else { $oauth_is_not_installed = true; }
	
	if (isset( $_GET['oauth'] )) { include("admin.accounts.oauth.php"); } else {
	
	?>
	
		<div id="icp-ICPress" class="wrap">
			
			<?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>
			
			
			<!-- ICPRESS MANAGE ACCOUNTS -->
			
			<div id="icp-ManageAccounts">
				
				<h3>Synced Zotero Accounts</h3>
				<?php if (!isset( $_GET['no_accounts'] ) || (isset( $_GET['no_accounts'] ) && $_GET['no_accounts'] != "true")) { ?><a title="Sync your Zotero account" class="icp-AddAccountButton button button-secondary" href="<?php echo admin_url("admin.php?page=ICPress&setup=true"); ?>"><span>Add account</span></a><?php } ?>
				
				<table id="icp-Accounts" class="wp-list-table widefat fixed posts">
					
					<thead>
						<tr>
							<th class="default first manage-column" scope="col">Default</th>
							<th class="account_type first manage-column" scope="col">Type</th>
							<th class="api_user_id manage-column" scope="col">User ID</th>
							<th class="public_key manage-column" scope="col">Private Key</th>
							<th class="nickname manage-column" scope="col">Nickname</th>
							<!--<th class="status manage-column" scope="col">Status</th>-->
							<th class="actions last manage-column" scope="col">Actions</th>
						</tr>
					</thead>
					
					<tbody id="icp-AccountsList">
						<?php
							
							global $wpdb;
							
							$accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."icpress");
							$zebra = " alternate";
							
							foreach ($accounts as $num => $account)
							{
								if ($num % 2 == 0) { $zebra = " alternate"; } else { $zebra = ""; }
								
								$code = "<tr id='icp-Account-" . $account->api_user_id . "' class='icp-Account".$zebra."' rel='" . $account->api_user_id . "'>\n";
								
								// DEFAULT
								$code .= "                          <td class='default";
								if ( get_option("ICPress_DefaultAccount") && get_option("ICPress_DefaultAccount") == $account->api_user_id ) $code .= " selected";
								$code .= " first'><a href='javascript:void(0);' rel='". $account->api_user_id ."' class='default icp-Accounts-Default' title='Set as Default'>Set as Default</a></td>\n";
								
								// ACCOUNT TYPE
								$code .= "                          <td class='account_type'>" . substr($account->account_type, 0, -1) . "</td>\n";
								
								// API USER ID
								$code .= "                          <td class='api_user_id'>" . $account->api_user_id . "</td>\n";
								
								// PUBLIC KEY
								$code .= "                          <td class='public_key'>";
								if ($account->public_key)
								{
									$code .= $account->public_key;
								}
								else
								{
									$code .= 'No private key entered. <a class="icp-OAuth-Button" href="'.get_bloginfo( 'url' ).'/wp-content/plugins/icpress/lib/admin/admin.accounts.oauth.php?oauth_user='.$account->api_user_id.'&amp;return_uri='.get_bloginfo('url').'">Start OAuth?</a>';
								}
								$code .= "</td>\n";
								
								// NICKNAME
								$code .= "                          <td class='nickname'>";
								if ($account->nickname)
									$code .= $account->nickname;
								$code .= "</td>\n";
								
								// ACTIONS
								$code .= "                          <td class='actions last'>\n";
								//$code .= "                              <a title='Sync' class='sync' rel='".$account->api_user_id."' href='javascript:void(0);'><span class='icon'></span>Sync</a>\n";
								$code .= "                              <a title='Selective Import' class='selective' rel='".$account->api_user_id."' href='admin.php?page=ICPress&selective=true&api_user_id=" . $account->api_user_id . "'>Selective Import</a>\n";
								$code .= "                              <a title='(Re)Import' class='import' href='admin.php?page=ICPress&import=true&api_user_id=" . $account->api_user_id . "'>Import</a>\n";
								$code .= "                              <a title='Remove this account' class='delete' href='#" . $account->id . "'>Remove</a>\n";
								//$code .= "                              <span class='icp-Sync-Messages'>&nbsp;</span>\n";
								$code .= "                          </td>\n";
								
								$code .= "                         </tr>\n\n";
								
								echo $code;
							}
						?>
					</tbody>
					
				</table>
				
			</div>
			
			<span id="ICPRESS_PLUGIN_URL" style="display: none;"><?php echo ICPRESS_PLUGIN_URL; ?></span>
			<span id="ICPRESS_PASSCODE" style="display: none;"><?php /*echo get_option('ICPRESS_PASSCODE'); */ ?></span>
			
			<?php if (!$oauth_is_not_installed){ ?>
				<h3>What is OAuth?</h3>
				
				<p>
					OAuth helps you create the necessary private key for allowing ICPress to read your Zotero library and display
					it for all to see. You can do this manually through the Zotero website; using OAuth in ICPress is just a quicker, more straightforward way of going about it.
					<strong>Note: You'll need to have OAuth installed on your server to use this option.</strong> If you don't have OAuth installed, you'll have to generate a private key manually through the <a href="http://www.zotero.org/">Zotero</a> website.
				</p>
			<?php } ?>
			
			
		</div>
		
<?php

	} /* OAuth check */

} // !current_user_can('edit_others_posts')

else
{
	echo "<p>Sorry, you don't have permission to access this page.</p>";
}

?>