<?php
	global $cs_node, $cs_theme_option, $cs_counter_node, $post;
	if ( !isset($cs_node->var_pb_team_per_page) || empty($cs_node->var_pb_team_per_page) ) { $cs_node->var_pb_team_per_page = -1; }
	 if ( empty($_GET['page_id_all']) ) $_GET['page_id_all'] = 1;
	 $border_color = '#C70808'; 
	 $color_array = array('#C70808','#8a9045','#3e769a','#409F74', '#7d7b7b','#c16622');
	 $counter_color=0;
	 $filter_category=$cs_node->var_pb_team_cat;
	 
	?>
	<div class="element_size_<?php echo $cs_node->var_pb_team_element_size;?>">
    	<?php if ($cs_node->var_pb_team_title <> '') { ?>
            <header class="cs-heading-title">
                <h2 class="cs-section-title"><?php echo $cs_node->var_pb_team_title; ?></h2>
            </header>
        <?php  } ?>
        <div class="our_staff our-carousel">
		<?php 
            $args = array(
						'posts_per_page'			=> "-1",
						'paged'						=> $_GET['page_id_all'],
						'post_type'					=> 'class',
						'post_status'				=> 'publish',
						'order'						=> 'ASC',
					 );
					
					$custom_query = new WP_Query($args);
					$post_count = $custom_query->post_count;
					$args = array(
							'posts_per_page'			=> "$cs_node->var_pb_team_per_page",
							'paged'						=> '1',
							'post_type'					=> 'teams',
							'post_status'				=> 'publish',
							'order'						=> 'ASC',
						 );
						 if(isset($filter_category) && $filter_category <> '' && $filter_category <> '0'){
						$event_category_array = array('team-category' => "$filter_category");
						$args = array_merge($args, $event_category_array);
					}
						$custom_query = new WP_Query($args);
						if ( $custom_query->have_posts() <> "" ){
							$width = 230; 
							$height = 172;
						cs_cycleslider_script();
					?>
                    <div class="center">
                        <a id="prev<?php echo $cs_counter_node;?>" href="#" class="prev-btn"><i class="fa fa-long-arrow-left"></i></a>
                        <a id="next<?php echo $cs_counter_node; ?>" href="#" class="next-btn"><i class="fa fa-long-arrow-right"></i></a>
                    </div>
                    <div class="cycle-slideshow" data-cycle-timeout=4000 data-cycle-fx=carousel data-cycle-slides="article" data-allow-wrap="true" 
                   data-cycle-next="#next<?php echo $cs_counter_node;?>" data-cycle-prev="#prev<?php echo $cs_counter_node;?>">
                    <?php 
                    while ( $custom_query->have_posts() ): $custom_query->the_post();
                        $cs_team = get_post_meta($post->ID, "cs_team", true);
                        if ( $cs_team <> "" ) {
                            $cs_xmlObject_team = new SimpleXMLElement($cs_team);
                        }
						
                        $noimg = '';
                        $image_url = cs_attachment_image_src(get_post_thumbnail_id($post->ID),$width,$height); 
                        if($image_url == ''){
                            $noimg = 'no-img';
                        }else{
                            $noimg  ='post-img';
                        }
						if(isset($color_array[$counter_color])){
							$border_color = $color_array[$counter_color];
						}
                    ?>
                            <article <?php if(isset($border_color) && $border_color <> ''){?>style="border-color: <?php echo $border_color;?>"<?php }?>>
                                <figure class="<?php echo $noimg;?>">
                                    <?php if($image_url <> ''){?><img src="<?php echo $image_url;?>" alt=""><?php }?>
                                    <figcaption>
                                        <a href="<?php the_permalink(); ?>" class="btnreadmore bgcolr"><em class="fa fa-long-arrow-right"></em></a>
                                    </figcaption>
                                </figure>
                                
                                <div class="text">
                                    <h2 class="cs-post-title"><a href="<?php the_permalink(); ?>" class="colrhvr"><?php the_title();?></a></h2>
                                    <?php
                                     if($cs_xmlObject_team->var_cp_expertise <> ''){
                                          echo '<h6 class="cat-department">'.$cs_xmlObject_team->var_cp_expertise.'</h6>';
                                     }
                                    
									if($cs_xmlObject_team->var_cp_about <> ''){?><p><?php echo substr($cs_xmlObject_team->var_cp_about,0,120);if(strlen($cs_xmlObject_team->var_cp_about)>120)echo '...';?></p><?php }?>
                                    <div class="post-options">
                                        <ul>
                                            <?php if($cs_xmlObject_team->var_cp_team_email <> ''){?><li><em class="fa fa-envelope-o"></em> <a href="mailto:<?php echo $cs_xmlObject_team->var_cp_team_email;?>"><?php echo $cs_xmlObject_team->var_cp_team_email;?></a></li><?php }?>
                                            <?php if($cs_xmlObject_team->var_cp_team_phone <> ''){?><li><em class="fa fa-phone"></em> <?php echo $cs_xmlObject_team->var_cp_team_phone;?></li><?php }?>
                                            <?php if($cs_xmlObject_team->var_cp_team_time <> ''){?><li><em class="fa fa-clock-o"></em> <time datetime="2011-01-12"><?php echo $cs_xmlObject_team->var_cp_team_time;?></time></li><?php }?>
                                        </ul>
                                    </div>
                                </div>
                            </article>
                        <?php 
						$counter_color++;
						if($counter_color>5){
							$counter_color = 0;
						}
						endwhile;?>
                	</div>
                <?php }?>
                  <!-- Our Classes Close -->
				
            </div>
</div>