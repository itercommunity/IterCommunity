<?php if ( isset( $_GET['selective'] ) && ( isset($_GET['api_user_id']) && preg_match("/^[0-9]+$/", $_GET['api_user_id']) == 1 ) )
{
	global $wpdb;
	$api_user_id = htmlentities($_GET['api_user_id']);
	$api_user_id_data = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."icpress WHERE api_user_id='".$api_user_id."'", OBJECT);
	
?>

    <div id="icp-Setup" class="icp-Step-Selective">
		
		<?php include( dirname(__FILE__) . '/../admin/admin.menu.php' ); ?>
        
        <div class="icp-Setup-Step">
            
            <h3 class="pair">Selective Import</h3>
			<h4 class="pair"><?php if (strlen($api_user_id_data->nickname) > 0) { echo $api_user_id_data->nickname; } else { echo $api_user_id; }?>'s Library</h4>
            
            <div class="icp-Step-Import">
                
                <p style="margin: 1em 0 1.8em;">
                    You can selectively import top-level collections (which includes their items, subcollections, and subcollection items) below. You may need to wait a few moments if you have several top-level collections.
                </p>
                
                <div id="icp-Step-Import-Collection" class="loading">
                    <iframe id="icp-Step-Import-Collection-Frame" name="icp-Step-Import-Collection-Frame"
							src="<?php echo wp_nonce_url( ICPRESS_PLUGIN_URL . 'lib/import/import.collection.php?api_user_id=' . $api_user_id, 'icp_importing_' . intval($api_user_id) . '_' . date('Y-j-G'), 'icp_nonce' ); ?>"
							scrolling="no" frameborder="0" marginwidth="0" marginheight="0">
					</iframe>
                </div><!-- #icp-Step-Import-Collection -->
                
                <input id="icp-ICPress-Setup-Import-Selective" type="button" disabled="disabled" class="button button-primary" value="Import Selected" />
                
                <div class="icp-Loading-Container selective">
                    <div class="icp-Loading-Initial icp-Loading-Import selective"></div>
                    <div class="icp-Import-Messages selective">Importing selected collection(s) ...</div>
                </div>
                
            </div>
            
            <iframe id="icp-Setup-Import" name="icp-Setup-Import"
				src="<?php echo wp_nonce_url( ICPRESS_PLUGIN_URL . 'lib/import/import.iframe.php?api_user_id=' . $api_user_id, 'icp_importing_' . intval($api_user_id) . '_' . date('Y-j-G'), 'icp_nonce' ); ?>"
				scrolling="yes" frameborder="0" marginwidth="0" marginheight="0">
			</iframe>
            
            <div id="icp-ICPress-Setup-Buttons" class="proceed">
				<a title="Go to Browse" id="icp-Import-Browse-Button" class="button button-secondary" href="admin.php?page=ICPress">Browse Library</a>
				<a title="Go to Accounts" id="icp-Import-Accounts-Button" class="button button-secondary" href="admin.php?page=ICPress&accounts=true">Go to Accounts</a>
            </div>
            
        </div>
        
    </div>
    
<?php } ?>