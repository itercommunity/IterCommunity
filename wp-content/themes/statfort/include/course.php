<?php
	//adding columns start
    add_filter('manage_courses_posts_columns', 'course_columns_add');
		function course_columns_add($columns) {
			$columns['category'] = 'Category';
			$columns['author'] = 'Author';
			return $columns;
    }
    add_action('manage_courses_posts_custom_column', 'course_columns');
		function course_columns($name) {
			global $post;
			switch ($name) {
				case 'category':
					$categories = get_the_terms( $post->ID, 'course-category' );
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

	function cs_course_register() {
		$labels = array(
			'name' => 'Courses',
			'add_new_item' => 'Add New Course',
			'edit_item' => 'Edit Course',
			'new_item' => 'New Course Item',
			'add_new' => 'Add New Course',
			'view_item' => 'View Course Item',
			'search_items' => 'Search Course',
			'not_found' =>  'Nothing found',
			'not_found_in_trash' => 'Nothing found in Trash',
			'parent_item_colon' => ''
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'menu_icon' => get_template_directory_uri() . '/images/admin/course-icon.png',
			'rewrite' => true,
			'capability_type' => 'post',
			'hierarchical' => false,
			'menu_position' => null,
			'supports' => array('title','editor','thumbnail', 'comments' )
		); 
        register_post_type( 'courses' , $args );
	}
	add_action('init', 'cs_course_register');

		// adding cat start
		  $labels = array(
			'name' => 'Course Categories',
			'search_items' => 'Search Course Categories',
			'edit_item' => 'Edit Course Category',
			'update_item' => 'Update Course Category',
			'add_new_item' => 'Add New Category',
			'menu_name' => 'Course Categories',
		  ); 	
		  register_taxonomy('course-category',array('courses'), array(
			'hierarchical' => true,
			'labels' => $labels,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => array( 'slug' => 'course-category' ),
		  ));
		// adding cat end
		// adding tag start
		  $labels = array(
			'name' => 'Course Tags',
			'singular_name' => 'course-tag',
			'search_items' => 'Search Tags',
			'popular_items' => 'Popular Tags',
			'all_items' => 'All Tags',
			'parent_item' => null,
			'parent_item_colon' => null,
			'edit_item' => 'Edit Tag', 
			'update_item' => 'Update Tag',
			'add_new_item' => 'Add New Tag',
			'new_item_name' => 'New Tag Name',
			'separate_items_with_commas' => 'Separate tags with commas',
			'add_or_remove_items' => 'Add or remove tags',
			'choose_from_most_used' => 'Choose from the most used tags',
			'menu_name' => 'Course Tags',
		  ); 
		  register_taxonomy('course-tag','courses',array(
			'hierarchical' => false,
			'labels' => $labels,
			'show_ui' => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var' => true,
			'rewrite' => array( 'slug' => 'course-tag' ),
		  ));
		// adding tag end

	// adding course meta info start
		add_action( 'add_meta_boxes', 'cs_meta_course_add' );
		function cs_meta_course_add()
		{  
			add_meta_box( 'cs_meta_course', 'Course Options', 'cs_meta_course', 'courses', 'normal', 'high' );  
		}
		function cs_meta_course( $post ) {
			$cs_course = get_post_meta($post->ID, "cs_course", true);
			global $cs_xmlObject;
			if ( $cs_course <> "" ) {
				$cs_xmlObject = new SimpleXMLElement($cs_course);
				$sub_title = $cs_xmlObject->sub_title;
				$course_date = $cs_xmlObject->course_date;
				$course_duration = $cs_xmlObject->course_duration;
				$course_apply = $cs_xmlObject->course_apply;
				$course_phone = $cs_xmlObject->course_phone;
				$course_fax = $cs_xmlObject->course_fax;
				$course_web_url = $cs_xmlObject->course_web_url;
				$course_email = $cs_xmlObject->course_email;
				$var_cp_dept_name = $cs_xmlObject->var_cp_dept_name;
				$var_cp_course_color = $cs_xmlObject->var_cp_course_color;
				if(isset($cs_xmlObject->var_cp_team_members) && $cs_xmlObject->var_cp_team_members <> ''){
					$var_cp_team_members = $cs_xmlObject->var_cp_team_members;
					if ($var_cp_team_members)
					{
						$var_cp_team_members = explode(",", $var_cp_team_members);
	
					}
				} else {
					$var_cp_team_members = array();
				}
				$course_social = $cs_xmlObject->course_social;
			}
			else {
				$sub_title = '';
				$course_date = '';
				$course_duration = '';
				$course_apply = '';
				$course_phone = '';
				$course_fax = '';
				$course_web_url = '';
				$course_email = '';
				$course_social = '';
				$var_cp_dept_name = '';
				$var_cp_course_color = '#3e769a';
				$var_cp_team_members = array();
			}
?>
            <div class="page-wrap page-opts left" style="overflow:hidden; position:relative; height: 1432px;">
            
            <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/scripts/admin/select.js"></script>
			<script type="text/javascript" src="<?php echo get_template_directory_uri()?>/scripts/admin/prettyCheckable.js"></script>
            <script type="text/javascript" src="<?php echo get_template_directory_uri()?>/scripts/admin/jquery.scrollTo-min.js"></script>
            <link href="<?php echo get_template_directory_uri()?>/css/admin/datePicker.css" rel="stylesheet" type="text/css" />
            <script type="text/javascript">
				 jQuery(document).ready(function($){
					$('.bg_color').wpColorPicker(); 
				});
			
			</script>
                <div class="option-sec" style="margin-bottom:0;">
                    <div class="opt-conts">
                        <ul class="form-elements">
                            <li class="to-label"><label>Sub Title</label></li>
                            <li class="to-field">
                                <input type="text" name="sub_title" value="<?php echo $sub_title ?>" />
                                <p>Put the sub title.</p>
                            </li>
                        </ul>
                       
                    	
                        <ul class="form-elements">
                                <li class="to-label"><label>Social Sharing</label></li>
                                <li class="to-field">
                                    <div class="on-off"><input type="checkbox" name="course_social" value="on" class="myClass" <?php if($course_social=='on')echo "checked"?> /></div>
                                    <p>Make Social Sharing On/Off</p>
                                </li>
                            </ul>
                        
                        <ul class="form-elements">
                          <li class="to-label">
                            <label>Custom Color Scheme</label>
                          </li>
                          <li class="to-field">
                            <input type="text" name="var_cp_course_color" value="<?php echo $var_cp_course_color?>" class="bg_color" />
                            <p>Pick a custom color for Scheme of the course that will be used for course view. e.g. #697e09</p>
                          </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Apply Now URL</label></li>
                            <li class="to-field">
                            	<input type="text" name="course_apply" value="<?php echo htmlspecialchars($course_apply)?>" />
								<p>Put Apply Now URL</p>
							</li>
                        </ul>
                        
                        <ul class="form-elements">
                            <li class="to-label"><h3 class="hndle"><span>Admissions Information</span></h3></li>
                            
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Department Name</label></li>
                            <li class="to-field">
                            	<input type="text" name="var_cp_dept_name" value="<?php echo htmlspecialchars($var_cp_dept_name)?>" />
								<p>Put Department Name</p>
							</li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Instructors</label></li>
                            <li class="to-field">
                                <select name="var_cp_team_members[]"  multiple="multiple"  class="dropdown" style="height: 100px !important;">
                                    <option value="">Select Speakers</option>
                                    <?php
                                        query_posts( array('posts_per_page' => "-1", 'post_status' => 'publish', 'post_type' => 'teams') );
                                            while ( have_posts()) : the_post();
                                            ?>
                                                <option <?php if (in_array(get_the_ID(), $var_cp_team_members)) { echo 'selected="selected"';}?> value="<?php the_ID()?>"><?php the_title()?></option>
                                            <?php
                                            endwhile;
                                    ?>
                                </select>
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Course Date</label></li>
                            <li class="to-field">
                                    <!--date picker start-->
                                        <link rel="stylesheet" href="<?php echo get_template_directory_uri()?>/css/admin/jquery.ui.datepicker.css">
                                        <link rel="stylesheet" href="<?php echo get_template_directory_uri()?>/css/admin/jquery.ui.datepicker.theme.css">
                                        <script>
                                        jQuery(function($) {
                                            $( "#course_date" ).datepicker({
                                                defaultDate: "+1w",
                                                dateFormat: "yy-mm-dd",
												changeMonth: true,
                                                numberOfMonths: 1,
                                                //onSelect: function( selectedDate ) {
                                                    //$( "#cs_event_to_date" ).datepicker( "option", "minDate", selectedDate );
                                                //}
                                            });
                                        });
                                        </script>
                                    <!--date picker end-->
                                <input type="text" id="course_date" name="course_date" value="<?php if ($course_date=="") echo gmdate("Y-m-d"); else echo $course_date?>" />
                                <p>Put course date</p>
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Course Duration</label></li>
                            <li class="to-field">
                            	<input type="text" name="course_duration" value="<?php echo htmlspecialchars($course_duration)?>" />
								<p>Put the course duration</p>
							</li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Phone</label></li>
                            <li class="to-field">
                            	<input type="text" name="course_phone" value="<?php echo htmlspecialchars($course_phone)?>" />
								<p>Put Phone</p>
							</li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Fax</label></li>
                            <li class="to-field">
                            	<input type="text" name="course_fax" value="<?php echo htmlspecialchars($course_fax)?>" />
								<p>Put Fax</p>
							</li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Email</label></li>
                            <li class="to-field">
                            	<input type="text" name="course_email" value="<?php echo htmlspecialchars($course_email)?>" />
								<p>Put Email</p>
							</li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Website URL</label></li>
                            <li class="to-field">
                            	<input type="text" name="course_web_url" value="<?php echo htmlspecialchars($course_web_url)?>" />
								<p>Put Website URL</p>
							</li>
                        </ul>
                        
                    </div>
					<div class="clear"></div>
                </div>
                <div class="boxes tracklists">
                	<div id="add_track" class="poped-up">
                        <div class="opt-head">
                            <h5>Subject Settings</h5>
                            <a href="javascript:closepopedup('add_track')" class="closeit">&nbsp;</a>
                            <div class="clear"></div>
                        </div>
                        <ul class="form-elements">
                            <li class="to-label"><label>Subject Title</label></li>
                            <li class="to-field">
                            	<input type="text" id="subject_title_dummy" name="subject_title_dummy" value="Subject Title" />
                                <p>Put Subject title</p>
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Instructor</label></li>
                            <li class="to-field">
                            	<input type="text" id="subject_instructor" name="subject_instructor" />
                                <p>Put Instructor Name</p>
                            </li>
                        </ul>
                        <ul class="form-elements">
                            <li class="to-label"><label>Credit Hours</label></li>
                            <li class="to-field">
                            	<input type="text" id="subject_credit_hours" name="subject_credit_hours" />
                                <p>Put the Credit Hours</p>
                            </li>
                        </ul>
                        <ul class="form-elements noborder">
                            <li class="to-label"></li>
                            <li class="to-field"><input type="button" value="Add Subject to List" onclick="add_subject_to_list('<?php echo admin_url()?>', '<?php echo get_template_directory_uri()?>')" /></li>
                        </ul>
                    </div>
            <script>
				jQuery(document).ready(function($) {
					$("#total_tracks").sortable({
						cancel : 'td div.poped-up',
					});
				});
				</script>
                    <div class="opt-head">
                        <h4 style="padding-top:12px;">Subjects</h4>
                        <a href="javascript:openpopedup('add_track')" class="button">Add Subject</a>
                        <div class="clear"></div>
                    </div>
                    <table class="to-table" border="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width:80%;">Subject Title</th>
                                <th style="width:80%;" class="centr">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="total_tracks">
                            <?php
								global $counter_subject, $subject_title, $subject_instructor , $subject_credit_hours;
								$counter_subject = $post->ID;
								if ( $cs_course <> "" ) {
									foreach ( $cs_xmlObject as $track ){
										if ( $track->getName() == "subject" ) {
											$subject_title = $track->subject_title;
											$subject_instructor = $track->subject_instructor;
											$subject_credit_hours = $track->subject_credit_hours;
											$counter_subject++;
											cs_add_subject_to_list();
										}
									}
								}
							?>
                        </tbody>
                    </table>
                </div>
				<?php meta_layout()?>
                <input type="hidden" name="course_meta_form" value="1" />
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
<?php
		}

		if ( isset($_POST['course_meta_form']) and $_POST['course_meta_form'] == 1 ) {
			//if ( empty($_POST['cs_layout']) ) $_POST['cs_layout'] = 'none';
			add_action( 'save_post', 'cs_meta_course_save' );  
			function cs_meta_course_save( $post_id )
			{  
				$sxe = new SimpleXMLElement("<course></course>");
					if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return; 
					if ( empty($_POST["sub_title"]) ) $_POST["sub_title"] = "";
					if ( empty($_POST["course_date"]) ) $_POST["course_date"] = "";
					if ( empty($_POST["course_duration"]) ) $_POST["course_duration"] = "";
					if ( empty($_POST["course_apply"]) ) $_POST["course_apply"] = "";
					if ( empty($_POST["course_phone"]) ) $_POST["course_phone"] = "";
					if ( empty($_POST["course_fax"]) ) $_POST["course_fax"] = "";
					if ( empty($_POST["course_email"]) ) $_POST["course_email"] = "";
					if ( empty($_POST["course_web_url"]) ) $_POST["course_web_url"] = "";
					if ( empty($_POST["course_social"]) ) $_POST["course_social"] = "";
					if ( empty($_POST["var_cp_dept_name"]) ) $_POST["var_cp_dept_name"] = "";
					if ( empty($_POST["var_cp_course_color"]) ) $_POST["var_cp_course_color"] = "";
					if (empty($_POST["var_cp_team_members"])){ $var_cp_team_members = "";} else {
						$var_cp_team_members = implode(",", $_POST["var_cp_team_members"]);
					}
					$sxe->addChild('sub_title', $_POST['sub_title'] );
					$sxe->addChild('course_date', $_POST['course_date'] );
					$sxe->addChild('course_duration', htmlspecialchars($_POST['course_duration']) );
					$sxe->addChild('course_apply', htmlspecialchars($_POST['course_apply']) );
					$sxe->addChild('course_fax', htmlspecialchars($_POST['course_fax']) );
					$sxe->addChild('course_phone', htmlspecialchars($_POST['course_phone']) );
					$sxe->addChild('course_email', htmlspecialchars($_POST['course_email']) );
					$sxe->addChild('course_web_url', htmlspecialchars($_POST['course_web_url']) );
					$sxe->addChild('course_social', htmlspecialchars($_POST['course_social']) );
					$sxe->addChild('course_social', $_POST['course_social'] );
					$sxe->addChild('var_cp_dept_name', $_POST['var_cp_dept_name'] );
					$sxe->addChild('var_cp_course_color', $_POST['var_cp_course_color'] );
					$sxe->addChild('var_cp_team_members', $var_cp_team_members);
					$counter = 0;
					if ( isset($_POST['subject_title']) ) {
						foreach ( $_POST['subject_title'] as $count ){
							$track = $sxe->addChild('subject');
								$track->addChild('subject_title', htmlspecialchars($_POST['subject_title'][$counter]) );
								$track->addChild('subject_instructor', htmlspecialchars($_POST['subject_instructor'][$counter]) );
								$track->addChild('subject_credit_hours', htmlspecialchars($_POST['subject_credit_hours'][$counter]) );
								$counter++;
						}
					}
					$sxe = save_layout_xml($sxe);
				update_post_meta( $post_id, 'cs_course', $sxe->asXML() );
			}
		}
		// adding course meta info end
?>