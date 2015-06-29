            <h3>Add a Zotero Account</h3>
            
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="icp-Add" name="icp-Add">
            
                <fieldset>
                    <input id="ICPRESS_PLUGIN_URL" name="ICPRESS_PLUGIN_URL" type="hidden" value="<?php echo ICPRESS_PLUGIN_URL; ?>" />
					
                    <div class="field">
                        <label for="account_type" class="required">Account Type</label>
                        <select id="account_type" name="account_type" tabindex="1">
                            <option value="users">User</option>
                            <option value="groups">Group</option>
                        </select>
                    </div>
					
                    <div class="field">
                        <label for="api_user_id" class="required" title="API User ID">API User ID</label>
                        <input id="api_user_id" name="api_user_id" type="text" tabindex="2" />
						<aside>
							<p>
								For individual accounts, find the API User ID in your <a href="http://www.zotero.org/settings" target="_blank" rel="ext">Zotero Settings</a> at the <strong>Feeds/API tab</strong>. For group accounts, there are two ways to find the API Group ID. Older Zotero groups will have it listed in the group's Zotero URL: a number 1-6+ digits in length after "groups." The API Group ID of new Zotero groups can be found in the <strong>RSS Feed URL</strong>.
							</p>
						</aside>
                    </div>
					
                    <div class="field icp-public_key">
                        <label for="public_key" class="icp-Help required" title="Private Key">Private Key</label>
                        <input id="public_key" name="public_key" type="text" tabindex="3" />
						<aside>
							<p>
								A private key is required for ICPress to make requests to Zotero from WordPress.
								<?php if (isset($oauth_is_not_installed) && $oauth_is_not_installed === false) { ?><strong>You can create a key using OAuth <u>after</u> you've added your account.</strong><?php } else { ?>Go to the <a href="http://www.zotero.org/settings" target="_blank" rel="ext">Zotero Settings</a> at the <strong>Feeds/API</strong> tab and choose "Create new private key."</strong><?php } ?>
								If you've already created a key, you can find it at <em>Settings > Feeds/API</em> on the <a title="Zotero" rel="nofollow" href="http://www.zotero.org/">Zotero</a> website. Make sure that <strong>"Allow read access"</strong> is checked. For groups, make sure the Default Group Permissions or Specific Group Permissions are set to "<strong>Read Only</strong>" or "Read/Write."
							</p>
						</aside>
                    </div>
					
                    <div class="field last">
                        <label for="nickname" class="icp-Help" title="Nickname"><span>Nickname</span></label>
                        <input id="nickname" name="nickname" type="text" tabindex="4" />
						<aside>
							<p>
								Your API User/Group ID can be hard to remember. Make it easier for yourself by giving your account a nickname.
							</p>
						</aside>
                    </div>
					
                    <div class="proceed">
                        <input id="icp-Connect" name="icp-Connect" class="button-primary" type="submit" value="Validate" tabindex="5" />
                    </div>
                    
                    <div class="message">
                        <div class="icp-Loading">loading</div>
                        <div class="icp-Errors"><p>Errors!</p></div>
                        <div class="icp-Success"><p>Success!</p></div>
                    </div>
                    
                </fieldset>
                
            </form>