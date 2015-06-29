<?php if ( isset( $_GET['import'] ) && ( isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1 ) )
{
	global $wpdb;
	$api_user_id = htmlentities($_GET['api_user_id']);
	$api_user_id_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress WHERE api_user_id='".$api_user_id."'", OBJECT);
	
?>

    <div id="icp-Setup" class="icp-Step-Selective">
		
		<?php include( dirname(__FILE__) . '/../admin/admin.menu.php' ); ?>
        
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
            
            <iframe id="icp-Setup-Import" name="icp-Setup-Import" src="<?php echo wp_nonce_url( ICPRESS_PLUGIN_URL . 'lib/import/import.iframe.php?api_user_id=' . $api_user_id, 'icp_importing_' . intval($api_user_id) . '_' . date('Y-j-G'), 'icp_nonce' ); ?>" scrolling="yes" frameborder="0" marginwidth="0" marginheight="0"></iframe>
            
            <div id="icp-ICPress-Setup-Buttons" class="proceed" style="display: none;">
				<a title="Go to Browse" id="icp-Import-Browse-Button" class="button button-primary" href="admin.php?page=ICPress">Browse Library</a>
				<a title="Go to Accounts" id="icp-Import-Accounts-Button" class="button button-secondary" href="admin.php?page=ICPress&accounts=true">Return to Accounts</a>
            </div>
            
        </div>
        
    </div>
    
<?php } ?>