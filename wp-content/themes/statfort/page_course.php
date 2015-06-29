<?php
	global $cs_node,$post,$cs_theme_option,$cs_counter_node,$wpdb;
	if ( !isset($cs_node->var_pb_course_per_page) || empty($cs_node->var_pb_course_per_page) ) { $cs_node->var_pb_course_per_page = -1; }
	if ( !isset($cs_node->var_pb_course_view) || empty($cs_node->var_pb_course_view) ) { $cs_node->var_pb_course_view = 'courselisting'; }
	 	 $meta_compare = '';
        $filter_category = '';
        $row_cat = $wpdb->get_row("SELECT * from ".$wpdb->prefix."terms WHERE slug = '" . $cs_node->var_pb_course_cat ."'" );
        if ( isset($_GET['filter_category']) ) {$filter_category = $_GET['filter_category'];}
        else {
            if(isset($row_cat->slug)){
            $filter_category = $row_cat->slug;
            }
        }
		$cs_counter_course = 0;
		if ( empty($_GET['page_id_all']) ) $_GET['page_id_all'] = 1;
                $args = array(
                    'posts_per_page'			=> "-1",
                    'post_type'					=> 'courses',
                    'post_status'				=> 'publish',
                    'orderby'					=> 'meta_value',
                    'order'						=> 'ASC',
                );
			if(isset($filter_category) && $filter_category <> '' && $filter_category <> '0'){
					$course_category_array = array('course-category' => "$filter_category");
					$args = array_merge($args, $course_category_array);
				}
            $custom_query = new WP_Query($args);
            $count_post = 0;
			$counter = 1;
			$count_post = $custom_query->post_count;
	?>
    <div class="element_size_<?php echo $cs_node->var_pb_course_element_size; ?>">
                        <header class="cs-heading-title">
                            <?php if ($cs_node->var_pb_course_title <> '') { ?><h2 class="cs-section-title"><?php echo $cs_node->var_pb_course_title;?></h2><?php }?>
                            <?php if($cs_node->var_pb_course_filterable == "Yes"){
								$qrystr= "";
								if ( isset($_GET['page_id']) ) $qrystr = "&page_id=".$_GET['page_id'];
							?>  
			                <!-- Sortby Start -->
                            <ul class="sortby">
                            	 <?php
									if( isset($cs_node->var_pb_course_cat) && ($cs_node->var_pb_course_cat <> "" && $cs_node->var_pb_course_cat <> "0") && isset( $row_cat->term_id )){
									$categories = get_categories( array('child_of' => "$row_cat->term_id", 'taxonomy' => 'course-category', 'hide_empty' => 0) );
									?>
									<li class="<?php if(($cs_node->var_pb_course_cat==$filter_category)){echo 'bgcolr';}?>"><a href="?<?php echo $qrystr."&filter_category=".$row_cat->slug?>"><?php _e("All",'Statfort');?></a></li>
									<?php
									}else{
									$categories = get_categories( array('taxonomy' => 'course-category', 'hide_empty' => 0) );
									}
									foreach ($categories as $category) {
									?>
										<li <?php if($category->slug==$filter_category){echo 'class="bgcolr"';}?>><a href="?<?php echo $qrystr."&filter_category=".$category->slug?>"><?php echo $category->cat_name?></a></li>
							   <?php }?>
                               
                            </ul>
                            <!-- Sortby End -->
                            <?php }?>
                            <?php if ($cs_node->var_pb_course_view_all <> '') { ?><a class="btnshowmore float-right" href="<?php echo $cs_node->var_pb_course_view_all;?>"> <em class="fa fa-long-arrow-right"></em><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('View All','Statfort'); }else{ echo $cs_theme_option['trans_view_all']; } ?></a><?php }?>
                        </header>
                        <?php 
				$args = array(
					'posts_per_page'			=> "$cs_node->var_pb_course_per_page",
					'paged'						=> $_GET['page_id_all'],
					'post_type'					=> 'courses',
					'post_status'				=> 'publish',
					'order'						=> 'ASC',
				 );
				if(isset($filter_category) && $filter_category <> '' && $filter_category <> '0'){
					$course_category_array = array('course-category' => "$filter_category");
					$args = array_merge($args, $course_category_array);
				}
				$custom_query = new WP_Query($args);
				if ( $custom_query->have_posts() <> "" ) {
					$width = 230;
					$height=172;
					
					
				if($cs_node->var_pb_course_view == 'courses-home-view'){?>
                		<div class="our_courses">
                            <ul>
                            	<?php
								 while ( $custom_query->have_posts() ): $custom_query->the_post();	
									$cs_course = get_post_meta($post->ID, "cs_course", true);
									if ( $cs_course <> "" ) {
										$cs_xmlObject = new SimpleXMLElement($cs_course);
										$var_cp_course_color = $cs_xmlObject->var_cp_course_color;
									}
									$image_url = cs_attachment_image_src( get_post_thumbnail_id($post->ID),$width,$height ); 
									if($image_url == ''){
										$noimg = ' no-img';
									}else{
										$noimg  ='';
									}
								?>
                                <li>
                                    <article style="background-color: <?php echo $cs_xmlObject->var_cp_course_color;?>">
                                        <h5><a href="<?php the_permalink();?>" class="colrhovr"><?php echo substr(get_the_title(),0, 25); if(strlen(get_the_title())>25){echo '...';}?></a></h5>
                                        <?php if(count($cs_xmlObject->subject )>0){?>
                                        <span class="course-program"> 
										<?php echo count($cs_xmlObject->subject );?> 
										<?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Programms','Statfort'); }else{ echo $cs_theme_option['trans_course_programms']; } ?>
                                        </span><?php }?>
                                    </article>
                                </li>
                              <?php endwhile;?>
                            </ul>
                        </div>
                <?php } else {?>
                  <div class="courses <?php echo $cs_node->var_pb_course_view;?>">
                  <?php
				  	 while ( $custom_query->have_posts() ): $custom_query->the_post();	
					 		$cs_course = get_post_meta($post->ID, "cs_course", true);
							if ( $cs_course <> "" ) {
								$cs_xmlObject = new SimpleXMLElement($cs_course);
								$var_cp_course_color = $cs_xmlObject->var_cp_course_color;
							}
							$image_url = cs_attachment_image_src( get_post_thumbnail_id($post->ID),$width,$height ); 
							if($image_url == ''){
								$noimg = ' no-img';
							}else{
								$noimg  ='';
							}
						?>	       
                    	<article <?php post_class($noimg);?>>
                        	<figure class="fig-<?php echo $post->ID;?>">
                            	<?php if($image_url <> ''){?><img src="<?php echo $image_url;?>" alt=""><?php }?>
                                <?php if($cs_node->var_pb_course_view == 'courses-gridview'){?>
                                <style type="text/css">
                                .courses.courses-gridview article figure.fig-<?php echo $post->ID;?> figcaption:before{
								 	background-color:<?php echo $cs_xmlObject->var_cp_course_color;?>;
								}
                                </style>
                                    <figcaption>
                                    	<?php if($cs_xmlObject->course_apply <> ''){?> 
                                        <a href="<?php echo $cs_xmlObject->course_apply;?>" class="btn cs-buynow bgcolrhvr"  >
										<?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Starts','Statfort'); }else{ echo $cs_theme_option['trans_course_apply_now']; } ?></a><?php }?>
                                    </figcaption>
                                 <?php }
								 if($cs_node->var_pb_course_view == 'courselisting'){
								 ?>
                                 <figcaption>
                                    <a class="btnreadmore" href="<?php the_permalink(); ?>"  style="background-color: <?php echo $cs_xmlObject->var_cp_course_color;?>"> <em class="fa fa-long-arrow-right"></em></a>
                                </figcaption>
                                 <?php } ?>
                            </figure>
                            <div class="text">
                            	<?php if($cs_node->var_pb_course_view == 'courses-gridview'){?>
                                		<div class="event-texttop">
                                            <h2 class="cs-post-title">
                                                <a href="<?php the_permalink();?>" class="colrhvr"><?php echo substr(get_the_title(),0, 40); if(strlen(get_the_title())>40){echo '...';}?></a>
                                            </h2>
										</div>	
									<?php } 
                                     if($cs_node->var_pb_course_view <> 'courses-gridview'){?>
                                     	<h2 class="cs-post-title">
                                            <a href="<?php the_permalink();?>" class="colrhvr"><?php echo substr(get_the_title(),0, 40); if(strlen(get_the_title())>40){echo '...';}?></a>
                                        </h2>
                                     	 <?php 
										$before_cat ='<ul class="post-categories"><li>';
										$categories_list = get_the_term_list ( get_the_id(), 'course-category', $before_cat, ', ', '</li></ul>' );
										if ( $categories_list ): printf( __( '%1$s', 'Statford'),$categories_list ); endif;
										?>
                                    	<p><?php  cs_get_the_excerpt($cs_node->var_pb_course_excerpt,true);?></p>
                                        <div class="post-options">
                                            <ul>
                                                <li><i class="fa fa-clock-o"></i><span class="hd"><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Starts','Statfort'); }else{ echo $cs_theme_option['trans_course_start_from']; } ?>:</span> <time><?php echo date_i18n(get_option('date_format') ,strtotime($cs_xmlObject->course_date));?></time></li>
                                                <li><i class="fa fa-map-marker"></i><span class="hd"><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Duration','Statfort'); }else{ echo $cs_theme_option['trans_course_duration']; } ?>:</span> <?php echo $cs_xmlObject->course_duration;?></li>
                                                 <?php
                                                    $var_cp_team_members = $cs_xmlObject->var_cp_team_members;
                                                    if ($var_cp_team_members)
                                                    {$count_members = 0;
                                                        $var_cp_team_members = explode(",", $var_cp_team_members);
                                                        if(count($var_cp_team_members)>0){?>
                                                        <li><i class="fa fa-user"></i><span class="hd"><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Instructors','Statfort'); }else{ echo $cs_theme_option['trans_course_instructor']; } ?>:</span>
                                                            <?php
                                                            foreach($var_cp_team_members as $speakers){
																$count_members++;
                                                                echo get_the_title((int) $speakers);
																if($count_members<count($var_cp_team_members)){
																	echo ', ';	
																}
                                                            }
                                                            ?>
                                                        </li>
                                                          <?php  
                                                        }
                                                    }
                                                    ?> 
                                            </ul>
                                        </div>
                                     <?php } else {?>

                                     <div class="post-options">
                                        <ul>
                                            <li><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Starts','Statfort'); }else{ echo $cs_theme_option['trans_course_start_from']; } ?>: <time><?php echo date_i18n(get_option('date_format'),strtotime($cs_xmlObject->course_date));?></time></li>
                                            <li><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Duration','Statfort'); }else{ echo $cs_theme_option['trans_course_duration']; } ?>: <span><?php echo $cs_xmlObject->course_duration;?></span></li>
                                            
                                        </ul>
                                    </div>
                                <?php 
									 }
								if($cs_node->var_pb_course_view <> 'courses-gridview'){
                               				if($cs_xmlObject->course_apply <> ''){?> 
                                            <a href="<?php echo $cs_xmlObject->course_apply;?>" class="btn cs-buynow bgcolrhvr" >
											<?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Starts','Statfort'); }else{ echo $cs_theme_option['trans_course_apply_now']; } ?>
                                            </a><?php }
                                 }?>
                            </div>
                        </article>
						
						<?php endwhile;
						
						?>
                    </div>
                    <?php 
					}}
						$qrystr = '';
					  if ( $cs_node->var_pb_course_pagination == "Show Pagination" and $count_post > $cs_node->var_pb_course_per_page and $cs_node->var_pb_course_per_page > 0 and $cs_node->var_pb_course_filterable != "Yes" ) {
							if ( isset($_GET['page_id']) ) $qrystr = "&page_id=".$_GET['page_id'];
								echo cs_pagination($count_post, $cs_node->var_pb_course_per_page,$qrystr);
					}
					?>
	</div>