<?php
	global $cs_node,$post,$cs_theme_option,$cs_counter_node,$wpdb;
	date_default_timezone_set('UTC');
	$current_time = current_time('Y-m-d H:i', $gmt = 0 ); 
	if ( !isset($cs_node->cs_event_per_page) || empty($cs_node->cs_event_per_page) ) { $cs_node->cs_event_per_page = -1; }
	if ( !isset($cs_node->cs_event_view) || empty($cs_node->cs_event_view) ) { $cs_node->cs_event_view = 'eventlisting'; }
	  $meta_compare = '';
        $filter_category = '';
        if ( $cs_node->cs_event_type == "Upcoming Events" ) $meta_compare = ">";
        else if ( $cs_node->cs_event_type == "Past Events" ) $meta_compare = "<";
        $row_cat = $wpdb->get_row("SELECT * from ".$wpdb->prefix."terms WHERE slug = '" . $cs_node->cs_event_category ."'" );
        if ( isset($_GET['filter_category']) ) {$filter_category = $_GET['filter_category'];}
        else {
            if(isset($row_cat->slug)){
            $filter_category = $row_cat->slug;
            }
        }
		$cs_counter_events = 0;
		if ( empty($_GET['page_id_all']) ) $_GET['page_id_all'] = 1;
            if ( $cs_node->cs_event_type == "All Events" ) {
                $args = array(
                    'posts_per_page'			=> "-1",
                    'post_type'					=> 'events',
                  //  'event-category'			=> "$filter_category",
                    'post_status'				=> 'publish',
                    'orderby'					=> 'meta_value',
                    'order'						=> 'ASC',
                );
            }else {
                $args = array(
                    'posts_per_page'			=> "-1",
                    'post_type'					=> 'events',
                  //  'event-category'			=> "$filter_category",
                    'post_status'				=> 'publish',
                    'meta_key'					=> 'cs_event_from_date_time',
                    'meta_value'				=> $current_time,
                    'meta_compare'				=> $meta_compare,
                    'orderby'					=> 'meta_value',
                    'order'						=> 'ASC',
                );
            }
			
			if(isset($filter_category) && $filter_category <> '' && $filter_category <> '0'){
					$event_category_array = array('event-category' => "$filter_category");
					$args = array_merge($args, $event_category_array);
				}
		
            $custom_query = new WP_Query($args);
            $count_post = 0;
			$counter = 1;
			$count_post = $custom_query->post_count;
			if ( $cs_node->cs_event_type == "Upcoming Events") {
				$args = array(
					'posts_per_page'			=> "$cs_node->cs_event_per_page",
					'paged'						=> $_GET['page_id_all'],
					'post_type'					=> 'events',
					'event-category'			=> "$filter_category",
					'post_status'				=> 'publish',
					'meta_key'					=> 'cs_event_from_date_time',
                    'meta_value'				=> $current_time,
                    'meta_compare'				=> $meta_compare,
					'orderby'					=> 'meta_value',
					'order'						=> 'ASC',
				 );
			}else if ( $cs_node->cs_event_type == "All Events" ) {
				$args = array(
					'posts_per_page'			=> "$cs_node->cs_event_per_page",
					'paged'						=> $_GET['page_id_all'],
					'post_type'					=> 'events',
					//'event-category'			=> "$filter_category",
					'meta_key'					=> 'cs_event_from_date_time',
					'meta_value'				=> '',
					'post_status'				=> 'publish',
					'orderby'					=> 'meta_value',
					'order'						=> 'DESC',
				);
			}
			else {
				$args = array(
					'posts_per_page'			=> "$cs_node->cs_event_per_page",
					'paged'						=> $_GET['page_id_all'],
					'post_type'					=> 'events',
				//	'event-category'			=> "$filter_category",
					'post_status'				=> 'publish',
					'meta_key'					=> 'cs_event_from_date_time',
                    'meta_value'				=> $current_time,
                    'meta_compare'				=> $meta_compare,
					'orderby'					=> 'meta_value',
					'order'						=> 'ASC',
				 );
			}
			if(isset($filter_category) && $filter_category <> '' && $filter_category <> '0'){
			$event_category_array = array('event-category' => "$filter_category");
			$args = array_merge($args, $event_category_array);
		}
		$custom_query = new WP_Query($args);
	?>
    
    <div class="element_size_<?php echo $cs_node->event_element_size; ?>">
    
    
    <header class="cs-heading-title">
    	<?php if ($cs_node->cs_event_title <> '') { ?>
            <h2 class="cs-section-title float-left"><?php echo $cs_node->cs_event_title;?></h2>
         <?php }?>
         <?php if($cs_node->cs_event_filterables == "Yes"){
			$qrystr= "";
			if ( isset($_GET['page_id']) ) $qrystr = "&page_id=".$_GET['page_id'];
		?>  
                <!-- Sortby Start -->
                <ul class="sortby">
                        
                         <?php
                            if( isset($cs_node->cs_event_category) && ($cs_node->cs_event_category <> "" && $cs_node->cs_event_category <> "0") && isset( $row_cat->term_id )){
                            $categories = get_categories( array('child_of' => "$row_cat->term_id", 'taxonomy' => 'event-category', 'hide_empty' => 0) );
                            ?>
                            <li class="<?php if(($cs_node->cs_event_category==$filter_category)){echo 'bgcolr';}?>">
                            	<a href="?<?php echo $qrystr."&filter_category=".$row_cat->slug?>"><?php _e("All",'Statfort');?></a>
                            </li>
                            <?php
                            }else{
                            $categories = get_categories( array('taxonomy' => 'event-category', 'hide_empty' => 0) );
                            }
                            foreach ($categories as $category) {
                            ?>
                                <li <?php if($category->slug==$filter_category){echo 'class="bgcolr"';}?>><a href="?<?php echo $qrystr."&filter_category=".$category->slug?>"><?php echo $category->cat_name?></a>
                                </li>
                       <?php }?>
                    </ul>
                <!-- Sortby End -->
        <?php }?>
    </header>
    <div class="event <?php echo $cs_node->cs_event_view;?>">
    
    
    		<?php
			if ( $cs_node->cs_event_type == "Upcoming Events") {
				$args = array(
					'posts_per_page'			=> "$cs_node->cs_event_per_page",
					'paged'						=> $_GET['page_id_all'],
					'post_type'					=> 'events',
					'post_status'				=> 'publish',
					'meta_key'					=> 'cs_event_from_date_time',
                    'meta_value'				=> $current_time,
                    'meta_compare'				=> $meta_compare,
					'orderby'					=> 'meta_value',
					'order'						=> 'ASC',
				 );
			}else if ( $cs_node->cs_event_type == "All Events" ) {
				$args = array(
					'posts_per_page'			=> "$cs_node->cs_event_per_page",
					'paged'						=> $_GET['page_id_all'],
					'post_type'					=> 'events',
					'meta_key'					=> 'cs_event_from_date_time',
					'meta_value'				=> '',
					'post_status'				=> 'publish',
					'orderby'					=> 'meta_value',
					'order'						=> 'ASC',
				);
			}
			else {
				$args = array(
					'posts_per_page'			=> "$cs_node->cs_event_per_page",
					'paged'						=> $_GET['page_id_all'],
					'post_type'					=> 'events',
					'post_status'				=> 'publish',
					'meta_key'					=> 'cs_event_from_date_time',
                    'meta_value'				=> $current_time,
                    'meta_compare'				=> $meta_compare,
					'orderby'					=> 'meta_value',
					'order'						=> 'DESC',
				 );
			}
			if(isset($filter_category) && $filter_category <> '' && $filter_category <> '0'){
				$event_category_array = array('event-category' => "$filter_category");
				$args = array_merge($args, $event_category_array);
			}
			$custom_query = new WP_Query($args);
			
		if ( $custom_query->have_posts() <> "" ) {
			$width = 230;
			$height=172;
 			 if($cs_node->cs_event_view == 'event-gridview'){
				 
				 while ( $custom_query->have_posts() ): $custom_query->the_post();	
			$event_from_date = get_post_meta($post->ID, "cs_event_from_date", true);
				$year_event = date_i18n("Y", strtotime($event_from_date));
				$month_event = date_i18n("m", strtotime($event_from_date));
				$month_event_c = date_i18n("M", strtotime($event_from_date));							
				$date_event = date_i18n("d", strtotime($event_from_date));
			$image_url = cs_attachment_image_src( get_post_thumbnail_id($post->ID),$width,$height ); 
			if($image_url == ''){
				$noimg = ' no-img';
			}else{
				$noimg  ='';
								}
			$cs_event_meta = get_post_meta($post->ID, "cs_event_meta", true);
				if ( $cs_event_meta <> "" ) {
					$cs_event_meta = new SimpleXMLElement($cs_event_meta);
					$inside_event_gallery = $cs_event_meta->inside_event_gallery;
					$event_start_time = $cs_event_meta->event_start_time;
					$event_end_time = $cs_event_meta->event_end_time;
					$event_all_day = $cs_event_meta->event_all_day;
				}
		 ?>
            <article <?php post_class($noimg);?>>
                    <figure>
                        <?php if($image_url<>''){?><img src="<?php echo $image_url;?>" alt=""> <?php }?>
                        <figcaption>
						 </figcaption>
						 <?php 
                              if($cs_event_meta->event_ticket_options == "Buy Now"){?> 
                                    <a class="btn cs-buynow cs-bgcolr bgcolr" href="<?php echo $cs_event_meta->event_buy_now;?>"><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Buy Ticket','Statford');}else{ echo $cs_theme_option['trans_event_buy_ticket']; } ?></a>
                                    <?php } ?>
                                    <?php if($cs_event_meta->event_ticket_options == "Free"){?> 
                                    <a class="cs-btnfree btn"><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Free Entry','Statford');}else{ echo $cs_theme_option['trans_event_free_entry']; } ?></a>
                                    <?php } ?>
                                    <?php if($cs_event_meta->event_ticket_options == "Cancelled"){?> 
                                    <a class="cs-btncancel btn" ><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Cancelled','Statford');}else{ echo $cs_theme_option['trans_event_cancelled']; } ?></a>
                                    <?php } ?>
                                    <?php if($cs_event_meta->event_ticket_options == "Full Booked"){?> 
                                    <a class="cs-btnbook btn" ></i><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Sold Out','Statford');}else{ echo $cs_theme_option['trans_event_sold_out']; } ?></a>
                              <?php } ?>
					
                    </figure>
                   
                    <div class="text">
                        <div class="event-texttop">
                            <h2 class="cs-post-title">
                                <a href="<?php the_permalink();?>" class="colrhvr"><?php echo substr(get_the_title(),0, 18); if(strlen(get_the_title())>18){echo '...';}?></a>
                            </h2>
                            <ul class="post-categories">
                                <li><time><?php echo date_i18n(get_option('date_format'),strtotime($event_from_date));?></time></li>
                            </ul>
                        </div>
                        <div class="post-options">
                            <ul>
                            	
                                 <?php if($event_start_time <> "" and $cs_node->cs_event_time == "Yes"){?>
                                        <li><i class="fa fa-clock-o"></i>
                                         <?php if ( $cs_event_meta->event_all_day != "on" ) {?>
                                                    <time> <?php echo $event_start_time; if($cs_event_meta->event_end_time <> ''){ echo "-";  echo $cs_event_meta->event_end_time; }?></time>
                                           <?php } else {
                                               echo '<time>';
                                                    _e("All",'Statford') . printf( __("%s day",'Statford'), ' ');
                                               echo '</time>';
                                           }?>
                                        </li>
                                <?php }?>
                               <?php if($cs_event_meta->event_address <> ''){?>
                                        <li>
                                            <i class="fa fa-map-marker"></i>
                                            <span><?php echo get_the_title((int)$cs_event_meta->event_address);?></span>
                                        </li>
                                 <?php }?>
                                 
                            </ul>
                        </div>
                    </div>
                </article>
            <?php 
			endwhile; 
			
			} else {
				
				while ( $custom_query->have_posts() ): $custom_query->the_post();	
			$event_from_date = get_post_meta($post->ID, "cs_event_from_date", true);
				$year_event = date_i18n("Y", strtotime($event_from_date));
				$month_event = date_i18n("m", strtotime($event_from_date));
				$month_event_c = date_i18n("M", strtotime($event_from_date));							
				$date_event = date_i18n("d", strtotime($event_from_date));
			$image_url = cs_attachment_image_src( get_post_thumbnail_id($post->ID),$width,$height ); 
			if($image_url == ''){
				$noimg = ' no-img';
			}else{
				$noimg  ='';
								}
			$cs_event_meta = get_post_meta($post->ID, "cs_event_meta", true);
				if ( $cs_event_meta <> "" ) {
					$cs_event_meta = new SimpleXMLElement($cs_event_meta);
					$inside_event_gallery = $cs_event_meta->inside_event_gallery;
					$event_start_time = $cs_event_meta->event_start_time;
					$event_end_time = $cs_event_meta->event_end_time;
					$event_all_day = $cs_event_meta->event_all_day;
				}
		?>
                <article <?php post_class($noimg);?>>
                <?php if($image_url<>''){?>
                    <figure>
                        <img src="<?php echo $image_url;?>" alt="">
                        <figcaption>
                            <a class="btnreadmore bgcolr" href="<?php the_permalink(); ?>"> <em class="fa fa-long-arrow-right"></em></a>
                        </figcaption>
                    </figure>
                    <?php }?>
                    <div class="text">
                        <div class="event-texttop">
                            <h2 class="cs-post-title">
                                <a href="<?php the_permalink();?>" class="colrhvr"><?php echo substr(get_the_title(),0, 40); if(strlen(get_the_title())>40){echo '...';}?></a>
                            </h2>
                                <ul class="post-categories">
                                    <?php 
                                        $before_cat ='<li>';
                                        $categories_list = get_the_term_list ( get_the_id(), 'event-category', $before_cat, ' ', '</li>' );
                                        if ( $categories_list ): printf( __( '%1$s', 'Statford'),$categories_list ); endif;
                                    ?>
                                    <li><time><?php echo date_i18n(get_option('date_format'),strtotime($event_from_date));?></time></li>
                                </ul>
                        </div>
                        <div class="post-options">
                            <ul>
                                <?php if($event_start_time <> "" and $cs_node->cs_event_time == "Yes"){?>
                                        <li><i class="fa fa-clock-o"></i>
                                         <?php if ( $cs_event_meta->event_all_day != "on" ) {?>
                                           
                                           <?php 
										   		if($cs_theme_option['trans_switcher'] == "on"){
											    	_e('Event Time','Statford');
												}else{ 
													echo $cs_theme_option['trans_event_eventtime']; 
												} 
											?>:
                                            <time> 
												<?php echo $event_start_time; if($cs_event_meta->event_end_time <> ''){ echo "-";  echo $cs_event_meta->event_end_time; }?>
                                            </time>
                                           <?php } else {
                                               echo '<time>';
                                                    _e("All",'Statford') . printf( __("%s day",'Statford'), ' ');
                                               echo '</time>';
                                           }?>
                                        </li>
                                <?php }
                                 if($cs_event_meta->event_address <> ''){?>
                                        <li>
                                            <i class="fa fa-map-marker"></i>
                                            <?php if($cs_theme_option['trans_switcher'] == "on"){ 
											 	_e('Location','Statford');}else{ 
												echo $cs_theme_option['trans_event_location']; 
											} 
											?>: 
                                            <span><?php echo get_the_title((int)$cs_event_meta->event_address);?></span>
                                        </li>
                                 <?php }?>
                                  <?php
								$var_cp_team_members = $cs_event_meta->var_cp_team_members;
								if ($var_cp_team_members)
								{
									$var_cp_team_members = explode(",", $var_cp_team_members);
									if(count($var_cp_team_members)>0){?>
									<li>
                                    <i class="fa fa-user"></i>
                                     <?php 
									 	if($cs_theme_option['trans_switcher'] == "on"){ 
										 	_e('Speakers','Statford');
										}else{ 
											echo $cs_theme_option['trans_event_speakers']; 
										} 
										?>:
                                    <span>
                                        <?php
										$cs_count=0;
										foreach($var_cp_team_members as $speakers){
											if($cs_count!=0){ echo ','; }
											echo '<a href="'.get_permalink((int) $speakers).'">'.get_the_title((int) $speakers).'</a>';
											$cs_count++;
										}
										?>
									</span>
                                	</li>
                                      <?php  
									}
				
								}
								?> 
                            </ul>
                        </div>
                        <?php if($cs_event_meta->event_ticket_options == "Buy Now" && $cs_node->cs_event_view <> 'event-gridview'){?> 
                                <a class="btn cs-buynow cs-bgcolr bgcolr" href="<?php echo $cs_event_meta->event_buy_now;?>"><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Buy Ticket','Statford');}else{ echo $cs_theme_option['trans_event_buy_ticket']; } ?></a>
                                <?php } ?>
                                <?php if($cs_event_meta->event_ticket_options == "Free"){?> 
                                <a class="cs-free btn cs-btnfree"><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Free Entry','Statford');}else{ echo $cs_theme_option['trans_event_free_entry']; } ?></a>
                                <?php } ?>
                                <?php if($cs_event_meta->event_ticket_options == "Cancelled"){?> 
                                <a class="cs-btncancel btn" ><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Cancelled','Statford');}else{ echo $cs_theme_option['trans_event_cancelled']; } ?></a>
                                <?php } ?>
                                <?php if($cs_event_meta->event_ticket_options == "Full Booked"){?> 
                                <a class="cs-btnbook btn" ></i><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Sold Out','Statford');}else{ echo $cs_theme_option['trans_event_sold_out']; } ?></a>
                          <?php } ?>
                    </div>
                </article>
			<?php
			endwhile; 
			}
			}
            wp_reset_query();
            ?>
      </div>
		<?php 
        $qrystr = '';
          if ( $cs_node->cs_event_pagination == "Show Pagination" and $count_post > $cs_node->cs_event_per_page and $cs_node->cs_event_per_page > 0 and $cs_node->cs_event_filterables != "Yes" ) {
				if ( isset($_GET['page_id']) ) $qrystr = "&page_id=".$_GET['page_id'];
					echo cs_pagination($count_post, $cs_node->cs_event_per_page,$qrystr);
        }
        ?>
</div>