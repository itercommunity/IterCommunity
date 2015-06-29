<?php
		//adding columns start
		add_filter('manage_team_posts_columns', 'team_columns_add');
			function team_columns_add($columns) {
				$columns['category'] = 'Category';
				$columns['author'] = 'Author';
				return $columns;
		}
		add_action('manage_team_posts_custom_column', 'team_columns');
			function team_columns($name) {
				global $post;
				switch ($name) {
					case 'category':
						$categories = get_the_terms( $post->ID, 'team-category' );
							if($categories <> ""){
								$couter_comma = 0;
								foreach ( $categories as $category ) {
									echo $category->name;
									$couter_comma++;
									if ( $couter_comma < count($categories) ) {
										echo ", ";
									}
								}
							}
						break;
					case 'author':
						echo get_the_author();
						break;
				}
			}
		//adding columns end
	
		function cs_team_register() {
			// adding Team start
			$labels = array(
				'name' => 'Our Staffs',
				'add_new_item' => 'Add New Member',
				'edit_item' => 'Edit Member',
				'new_item' => 'New Member',
				'add_new' => 'Add New Member',
				'view_item' => 'View Member',
				'search_items' => 'Search Member',
				'not_found' => 'Nothing found',
				'not_found_in_trash' => 'Nothing found in Trash',
				'parent_item_colon' => ''
			);
			$args = array(
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'query_var' => true,
				'menu_icon' => get_template_directory_uri() . '/images/admin/team-icon.png',
				//'show_in_menu' => 'edit.php?post_type=albums',
				'show_in_nav_menus'=>true,
				'rewrite' => true,
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array('title','editor' ,'thumbnail')
			); 
			register_post_type( 'teams' , $args );  
		}
			// adding Team end
		add_action('init', 'cs_team_register');
		function cs_team_categories() 
			{
				  $labels = array(
					'name' => 'Staff Categories',
					'search_items' => 'Search Team Categories',
					'edit_item' => 'Edit Team Category',
					'update_item' => 'Update Team Category',
					'add_new_item' => 'Add New Category',
					'menu_name' => 'Team Categories',
				  ); 	
				  register_taxonomy('team-category',array('teams'), array(
					'hierarchical' => true,
					'labels' => $labels,
					'show_ui' => true,
					'query_var' => true,
					'rewrite' => array( 'slug' => 'team-category' ),
				  ));
			}
			add_action( 'init', 'cs_team_categories');
		// adding Team meta info start
		add_action( 'add_meta_boxes', 'cs_meta_team_add' );
		function cs_meta_team_add()
		{  
			add_meta_box( 'cs_meta_team', 'Team Options', 'cs_meta_team', 'teams', 'normal', 'high' );  
		}
		function cs_meta_team( $post ) {
			$cs_team = get_post_meta($post->ID, "cs_team", true);
			global $cs_xmlObject;
			if ( $cs_team <> "" ) {
				$cs_xmlObject = new SimpleXMLElement($cs_team);
					$var_cp_expertise = $cs_xmlObject->var_cp_expertise;
					$var_cp_about = $cs_xmlObject->var_cp_about;
					$var_cp_team_email = $cs_xmlObject->var_cp_team_email;
					$var_cp_team_phone = $cs_xmlObject->var_cp_team_phone;
					$var_cp_team_time = $cs_xmlObject->var_cp_team_time;
 			}
			else {
				$var_cp_expertise ='';
				$var_cp_about = '';
				$var_cp_team_email = '';
				$var_cp_team_phone = '';
				$var_cp_team_time = '';
 			}
		?>
            <div class="page-wrap page-opts left" style="overflow:hidden; position:relative;">
            	<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/scripts/admin/jquery.scrollTo-min.js"></script>
				<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/scripts/admin/select.js"></script>
                <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/scripts/admin/prettyCheckable.js"></script>
                <div class="option-sec" style="margin-bottom:0;">
                    <div class="opt-conts">
				        <ul class="form-elements">
                            <li class="to-label"><label>Expertise</label></li>
                            <li class="to-field">
                                <input type="text" name="var_cp_expertise" value="<?php echo htmlspecialchars($var_cp_expertise)?>"/>
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>About Text</label></li>
                            <li class="to-field">
                            	<textarea name="var_cp_about" rows="8" cols="20"><?php echo htmlspecialchars($var_cp_about)?></textarea>
                                <p>For best view please add maximum 150 characters</p>
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Email</label></li>
                            <li class="to-field">
                                <input type="text" name="var_cp_team_email" value="<?php echo htmlspecialchars($var_cp_team_email)?>"/>
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Phone</label></li>
                            <li class="to-field">
                                <input type="text" name="var_cp_team_phone" value="<?php echo htmlspecialchars($var_cp_team_phone)?>"/>
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Time</label></li>
                            <li class="to-field">
                                <input type="text" name="var_cp_team_time" value="<?php echo htmlspecialchars($var_cp_team_time)?>"/>
                            </li>
                        </ul>
                    </div>
					<div class="clear"></div>
                </div>
                 <input type="hidden" name="team_meta_form" value="1" />
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
	<?php
		}
		if ( isset($_POST['team_meta_form']) and $_POST['team_meta_form'] == 1 ) {
			if ( empty($_POST['cs_layout']) ) $_POST['cs_layout'] = 'none';
			add_action( 'save_post', 'cs_meta_team_save' );  
			function cs_meta_team_save( $cs_post_id )
			{  
				$sxe = new SimpleXMLElement("<team></team>");
					if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
					if (empty($_POST["var_cp_expertise"])){ $_POST["var_cp_expertise"] = "";}
					if (empty($_POST["var_cp_about"])){ $_POST["var_cp_about"] = "";}
					if (empty($_POST["var_cp_team_email"])){ $_POST["var_cp_team_email"] = "";}
					if (empty($_POST["var_cp_team_phone"])){ $_POST["var_cp_team_phone"] = "";}
					if (empty($_POST["var_cp_team_time"])){ $_POST["var_cp_team_time"] = "";}
						$sxe = save_layout_xml($sxe);
						$sxe->addChild('var_cp_expertise', $_POST['var_cp_expertise'] );
						$sxe->addChild('var_cp_about', $_POST['var_cp_about'] );
						$sxe->addChild('var_cp_team_email', $_POST['var_cp_team_email'] );
						$sxe->addChild('var_cp_team_phone', $_POST['var_cp_team_phone'] );
						$sxe->addChild('var_cp_team_time', $_POST['var_cp_team_time'] );
				update_post_meta( $cs_post_id, 'cs_team', $sxe->asXML() );
			}
		}
		// adding Team meta info end
	?>