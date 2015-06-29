<?php

// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{
	
?>

		<div id="icp-ICPress" class="wrap">
            
            <?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>
			
			<div id="icp-Options-Wrapper">
				
				<h3>Defaults</h3>
				
				<?php include('admin.options.form.php'); ?>
				
				
				<hr />
				
				
				<?php if ( ICPRESS_EXPERIMENTAL_EDITOR ): ?>
				<!-- START OF EDITOR -->
				<div class="icp-Column-1">
					<div class="icp-Column-Inner">
						
						<h4>Editor Features</h4>
						
						<p class="note">Enable or disable the word processor-like features in the rich text editor.</p>
						
						<div id="icp-ICPress-Options-Editor" class="icp-ICPress-Options">
							
							<label for="icp-ICPress-Options-Editor">Enable editor features?</label>
							<select id="icp-ICPress-Options-Editor">
								<?php
								
								// Determine default editor features status
								$icp_default_editor = "editor_enable";
								if (get_option("ICPress_DefaultEditor")) $icp_default_editor = get_option("ICPress_DefaultEditor");
								
								?>
								<option id="editor_enable" value="editor_enable" <?php if ( $icp_default_editor == "editor_enable" ) { ?>selected='selected'<?php } ?>>Enable</option>
								<option id="editor_disable" value="editor_disable" <?php if ( $icp_default_editor == "editor_disable" ) { ?>selected='selected'<?php } ?>>Disable</option>
							</select>
							
							<script type="text/javascript" >
							jQuery(document).ready(function() {
							
								jQuery("#icp-ICPress-Options-Editor-Button").click(function()
								{
									// Plunk it together
									var data = 'submit=true&editor=' + jQuery('select#icp-ICPress-Options-Editor').val();
									
									// Prep for data validation
									jQuery(this).attr('disabled','true');
									jQuery('#icp-ICPress-Options-Editor .icp-Loading').show();
									
									// Set up uri
									var xmlUri = '<?php echo ICPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
									
									// AJAX
									jQuery.get(xmlUri, {}, function(xml)
									{
										var $result = jQuery('result', xml).attr('success');
										
										jQuery('#icp-ICPress-Options-Editor .icp-Loading').hide();
										jQuery('input#icp-ICPress-Options-Editor-Button').removeAttr('disabled');
										
										if ($result == "true")
										{
											jQuery('#icp-ICPress-Options-Editor div.icp-Errors').hide();
											jQuery('#icp-ICPress-Options-Editor div.icp-Success').show();
											
											jQuery.doTimeout(1000,function() {
												jQuery('#icp-ICPress-Options-Editor div.icp-Success').hide();
											});
										}
										else // Show errors
										{
											jQuery('#icp-ICPress-Options-Editor div.icp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
											jQuery('#icp-ICPress-Options-Editor div.icp-Errors').show();
										}
									});
									
									// Cancel default behaviours
									return false;
									
								});
								
							});
							</script>
							
							<input type="button" id="icp-ICPress-Options-Editor-Button" class="button-secondary" value="Set Editor Features" />
							<div class="icp-Loading">loading</div>
							<div class="icp-Success">Success!</div>
							<div class="icp-Errors">Errors!</div>
							
						</div>
					</div>
				</div><!-- END OF EDITOR -->
				<?php endif; ?>
				
				
				
				<!-- START OF CPT -->
				<div class="icp-Column-1">
					<div class="icp-Column-Inner">
						
						<h4>Set Reference Widget</h4>
						
						<p class="note">Enable or disable the ICPress Reference widget for specific post types.</p>
						
						<div id="icp-ICPress-Options-CPT" class="icp-ICPress-Options">
							
							<?php
							
							// See if default exists
                            $icp_default_cpt = "post,page";
                            if (get_option("ICPress_DefaultCPT"))
                                $icp_default_cpt = get_option("ICPress_DefaultCPT");
							$icp_default_cpt = explode(",",$icp_default_cpt);
							
							$post_types = get_post_types( '', 'names' ); 
							
							foreach ( $post_types as $post_type )
							{
								echo "<div class='icp-CPT-Checkbox'>";
								echo "<input type=\"checkbox\" name=\"icp-CTP\" id=\"".$post_type."\" value=\"".$post_type."\" ";
								//if ( in_array( $post_type, $icp_default_cpt ) ) echo "disabled=\"disabled\" checked ";
								if ( in_array( $post_type, $icp_default_cpt ) ) echo "checked ";
								echo "/>";
								echo "<label ";
								//if ( in_array( $post_type, $icp_default_cpt ) )  echo "class=\"dis\" ";
								echo "for=\"".$post_type."\">".$post_type."</label>";
								echo "</div>\n";
							}
							
							?>
							
							<script type="text/javascript" >
							jQuery(document).ready(function() {
							
								jQuery("#icp-ICPress-Options-CPT-Button").click(function()
								{
									// Get all post types
									var zpTempCPT = "";
									jQuery("input[name='icp-CTP']:checked").each(function()
									{
										zpTempCPT = zpTempCPT + "," + jQuery(this).val();
									});
									
									// Plunk it together
									var data = 'submit=true&cpt=' + zpTempCPT.substring(1);
									
									// Prep for data validation
									jQuery(this).attr('disabled','true');
									jQuery('#icp-ICPress-Options-CPT .icp-Loading').show();
									
									// Set up uri
									var xmlUri = '<?php echo ICPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?'+data;
									
									// AJAX
									jQuery.get(xmlUri, {}, function(xml)
									{
										var $result = jQuery('result', xml).attr('success');
										
										jQuery('#icp-ICPress-Options-CPT .icp-Loading').hide();
										jQuery('input#icp-ICPress-Options-CPT-Button').removeAttr('disabled');
										
										if ($result == "true")
										{
											jQuery('#icp-ICPress-Options-CPT div.icp-Errors').hide();
											jQuery('#icp-ICPress-Options-CPT div.icp-Success').show();
											
											jQuery.doTimeout(1000,function() {
												jQuery('#icp-ICPress-Options-CPT div.icp-Success').hide();
											});
										}
										else // Show errors
										{
											jQuery('#icp-ICPress-Options-CPT div.icp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
											jQuery('#icp-ICPress-Options-CPT div.icp-Errors').show();
										}
									});
									
									// Cancel default behaviours
									return false;
									
								});
								
							});
							</script>
							
							<input type="button" id="icp-ICPress-Options-CPT-Button" class="button-secondary" value="Set Reference Widget" />
							<div class="icp-Loading">loading</div>
							<div class="icp-Success">Success!</div>
							<div class="icp-Errors">Errors!</div>
							
						</div>
					</div>
				</div><!-- END OF EDITOR -->
				
				
				
				<!-- START OF RESET -->
				<div class="icp-Column-1">
					<div class="icp-Column-Inner">
						
						<h4>Reset ICPress</h4>
						
						<p class="note">Note: This action will clear all database entries associated with ICPress, including account information and citations&#8212;it <strong>cannot be undone</strong>. Proceed with caution.</p>
						
						<div id="icp-ICPress-Options-Reset" class="icp-ICPress-Options">
							
							<script type="text/javascript" >
							jQuery(document).ready(function() {
							
								jQuery("#icp-ICPress-Options-Reset-Button").click(function()
								{
									var confirmDelete = confirm("Are you sure you want to reset ICPress? This cannot be undone.");
									
									if ( confirmDelete == true )
									{
										// Prep for data validation
										jQuery(this).attr( 'disabled', 'true' );
										jQuery('#icp-ICPress-Options-Reset .icp-Loading').show();
										
										jQuery.get( '<?php echo ICPRESS_PLUGIN_URL; ?>lib/widget/widget.metabox.actions.php?submit=true&reset=true', {}, function(xml)
										{
											var $result = jQuery('result', xml).attr('success');
											
											jQuery('#icp-ICPress-Options-Reset .icp-Loading').hide();
											jQuery('input#icp-ICPress-Options-Reset-Button').removeAttr('disabled');
											
											if ($result == "true")
											{
												jQuery('#icp-ICPress-Options-Reset div.icp-Errors').hide();
												jQuery('#icp-ICPress-Options-Reset div.icp-Success').show();
												
												jQuery.doTimeout(1000,function() {
													jQuery('#icp-ICPress-Options-Reset div.icp-Success').hide();
													window.parent.location = "<?php echo ICPRESS_PLUGIN_URL; ?>../../../wp-admin/admin.php?page=ICPress";
												});
											}
											else // Show errors
											{
												jQuery('#icp-ICPress-Options-Reset div.icp-Errors').html("<p>"+jQuery('errors', xml).text()+"</p>\n");
												jQuery('#icp-ICPress-Options-Reset div.icp-Errors').show();
											}
										});
									} // confirmDelete
									
									// Cancel default behaviours
									return false;
									
								});
								
							});
							</script>
							
							<input type="button" id="icp-ICPress-Options-Reset-Button" class="button-secondary" value="Reset ICPress" />
							<div class="icp-Loading">loading</div>
							<div class="icp-Success">Success!</div>
							<div class="icp-Errors">Errors!</div>
							
						</div>
					</div>
				</div><!-- END OF RESET -->
				
			</div><!-- icp-Browse-Wrapper -->
		
		</div>
	
<?php

} // !current_user_can('edit_others_posts')

else
{
	echo "<p>Sorry, you don't have permission to access this page.</p>";
}

?>