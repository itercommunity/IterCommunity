<?php
get_header();
	global $cs_node, $cs_theme_option;
	$cs_layout = '';
	$cs_counter_events=1;
 	$post_xml = get_post_meta($post->ID, "cs_event_meta", true);	
	if ( $post_xml <> "" ) {
		$cs_xmlObject = new SimpleXMLElement($post_xml);
  		$cs_layout = $cs_xmlObject->sidebar_layout->cs_layout;
		$cs_sidebar_left = $cs_xmlObject->sidebar_layout->cs_sidebar_left;
		$cs_sidebar_right = $cs_xmlObject->sidebar_layout->cs_sidebar_right;
		$event_social_sharing = $cs_xmlObject->event_social_sharing;
		$event_start_time = $cs_xmlObject->event_start_time;
		$event_end_time = $cs_xmlObject->event_end_time;
 		$event_all_day = $cs_xmlObject->event_all_day;
		$event_booking_url = $cs_xmlObject->event_booking_url;
		$event_phone_no = $cs_xmlObject->event_phone_no;
		$event_address = $cs_xmlObject->event_address;
 		$inside_event_map = $cs_xmlObject->event_map;
		$width = 230;
		$height = 172;
		$image_id = cs_get_post_img($post->ID, $width,$height);
		
		if ( $cs_layout == "left") {
			$cs_layout = "content-right col-md-9";
			$custom_height = 300;
 		}
		else if ( $cs_layout == "right" ) {
			$cs_layout = "content-left col-md-9";
			$custom_height = 300;
 		}
		else {
			$cs_layout = "col-md-12";
			$custom_height = 403;
		}
  	}else{
		$event_social_sharing = '';
 		$inside_event_thumb_view = '';
   		$inside_event_thumb_map_lat = '';
		$inside_event_thumb_map_lon = '';
		$inside_event_thumb_map_zoom = '';
		$inside_event_thumb_map_address = '';
		$inside_event_thumb_map_controls = '';
 	}
	$cs_event_loc = get_post_meta($cs_xmlObject->event_address, "cs_event_loc_meta", true);
	if ( $cs_event_loc <> "" ) {
		$cs_event_loc = new SimpleXMLElement($cs_event_loc);
 			$event_loc_lat = $cs_event_loc->event_loc_lat;
			$event_loc_long = $cs_event_loc->event_loc_long;
			$event_loc_zoom = $cs_event_loc->event_loc_zoom;
			$loc_address = $cs_event_loc->loc_address;
			$loc_city = $cs_event_loc->loc_city;
			$loc_postcode = $cs_event_loc->loc_postcode;
			$loc_region = $cs_event_loc->loc_region;
			$loc_country = $cs_event_loc->loc_country;
	}
	else {
		$event_loc_lat = '';
		$event_loc_long = '';
		$event_loc_zoom = '';
		$loc_address = '';
		$loc_city = '';
		$loc_postcode = '';
		$loc_region = '';
		$loc_country = '';
	}
	$cs_event_to_date = get_post_meta($post->ID, "cs_event_to_date", true); 
	$cs_event_from_date = get_post_meta($post->ID, "cs_event_from_date", true); 
	$year_event = date_i18n("Y", strtotime($cs_event_from_date));
	$month_event = date_i18n("m", strtotime($cs_event_from_date));
	$month_event_c = date_i18n("M", strtotime($cs_event_from_date));							
	$date_event = date_i18n("d", strtotime($cs_event_from_date));
	$cs_event_meta = get_post_meta($post->ID, "cs_event_meta", true);
	$date_format = get_option( 'date_format' );
	$time_format = get_option( 'time_format' );							
	if ( $cs_event_meta <> "" ) {
		$cs_event_meta = new SimpleXMLElement($cs_event_meta);
	}	
	$address_map = '';
	$address_map = get_the_title("$cs_xmlObject->event_address");		
	$time_left = date_i18n("H,i,s", strtotime("$cs_event_meta->event_start_time"));
	$current_date = date_i18n('Y-m-d');
    if ( have_posts() ) while ( have_posts() ) : the_post();
		$cs_event_meta = get_post_meta($post->ID, "cs_event_meta", true);
		if ( $cs_event_meta <> "" ) $cs_event_meta = new SimpleXMLElement($cs_event_meta);
		
		if($inside_event_map != "on"){
			$class = 'eventdetail-parallax-full';
		}else{
			$class ='';
		}
		
	?>
	 
<!-- Event Outer Image Strat -->
 <div id="main" role="main"> 
  <!-- Container Start -->
  <div class="container"> 
        <!-- Row Start -->
        <div class="row"> 
			<!--Left Sidebar Starts-->
			<?php if ($cs_layout == 'content-right col-md-9'){ ?>
                <aside class="sidebar-left col-md-3"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar_left) ) : ?><?php endif; ?></aside>
            <?php wp_reset_query();} ?>
			<!--Left Sidebar End-->
			<div class="<?php echo $cs_layout; ?>">
            
            <div class="element_size_100">
                        <div class="event  event-detail">
                            <article>
                                
                                <?php 
								if($inside_event_map == "on"){
									echo '<div class="detail_figure">';
									if($address_map <> "" && $event_loc_lat <> "" && $event_loc_long <>"" && $event_loc_zoom <> ''){ ?>
										<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=true"></script> 
										<script type="text/javascript">
										jQuery(document).ready(function(){
											event_map("<?php echo $loc_address.'<br>'.$loc_city.'<br>'.$loc_postcode.'<br>'.$loc_country  ?>",<?php echo $event_loc_lat ?>,			<?php echo $event_loc_long ?>,<?php echo $event_loc_zoom ?>,<?php echo $cs_counter_events; ?>);
										});
										</script>
											<div id="map_canvas<?php echo $cs_counter_events; ?>" class="event-map" style="height:300px; width:100%;"></div>
									<?php }
									echo '</div>';
								}
								?>
                                <div class="detail_inner">
                                    <figure>
                                    	<?php if($image_id <> ""){ echo $image_id;}?>
                                    </figure>
                                    <div class="text">
                                    	
                                        <ul class="post-categories">
                                            <?php 
												$before_cat ='<li>';
												$categories_list = get_the_term_list ( get_the_id(), 'event-category', $before_cat, ' ', '</li>' );
												if ( $categories_list ): printf( __( '%1$s', 'Statford'),$categories_list ); endif;
											?>
                                        	<li><time><?php echo date_i18n(get_option('date_format'),strtotime($cs_event_from_date));?></time></li>
                                        </ul>
                                        <div class="post-options">
                                            <ul>
                                             <?php if($event_start_time <> ""){?>
                                                    <li><i class="fa fa-clock-o"></i>
                                                     <?php if ( $cs_event_meta->event_all_day != "on" ) {?>
                                                                <span class="hd">Event Time:</span> <time> 
																<?php  //echo $event_start_time.' am'; 
																
																echo date('g:i',strtotime($event_start_time)); if($event_end_time <> ''){ echo "-";  echo $event_end_time; }?></time>
                                                       <?php } else {
                                                           echo '<time>';
                                                                _e("All",'Statford') . printf( __("%s day",'Statford'), ' ');
                                                           echo '</time>';
                                                       }?>
                                                    </li>
                                            <?php }?>
                                                <li><i class="fa fa-map-marker"></i><span class="hd">Location:</span> <?php echo get_the_title((int)$cs_xmlObject->event_address);?></li>
                                                <?php
													$var_cp_team_members = $cs_event_meta->var_cp_team_members;
													if ($var_cp_team_members)
													{
														$var_cp_team_members = explode(",", $var_cp_team_members);
														if(count($var_cp_team_members)>0){?>
														 <li><i class="fa fa-user"></i><span class="hd">Speakers:</span>
															<?php
															foreach($var_cp_team_members as $speakers){
																echo '<a href="'.get_permalink((int) $speakers).'">'.get_the_title((int) $speakers).'</a>, ';
																
															}
															?>
														</li>
														  <?php  
														}
									
													}
													?> 
                                            </ul>
                                        </div>
                                        <?php if($cs_event_meta->event_ticket_options == "Buy Now"){?> 
                                        
                                            <a class="btn cs-buynow cs-bgcolr bgcolr"href="<?php echo $cs_event_meta->event_buy_now;?>"><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Buy Ticket','Statfort');}else{ echo $cs_theme_option['trans_event_buy_ticket']; } ?></a>
                                            <?php } 
											
											 if($cs_event_meta->event_ticket_options == "Free"){?> 
                                           	 <a class="btn cs-btnfree"><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Free Entry','Statfort');}else{ echo $cs_theme_option['trans_event_free_entry']; } ?></a>
                                            <?php } 
											
											 if($cs_event_meta->event_ticket_options == "Cancelled"){?> 
                                           	 <a class="btn cs-btncancel"><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Cancelled','Statfort');}else{ echo $cs_theme_option['trans_event_cancelled']; } ?></a>
                                            <?php } 
											
											 if($cs_event_meta->event_ticket_options == "Full Booked"){?> 
                                            	<a class="btn cs-btnbook"></i><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Sold Out','Statfort');}else{ echo $cs_theme_option['trans_event_sold_out']; } ?></a>
                                            <?php } ?>
                                    </div>
                                </div>
                            
                            <div class="detail_text rich_editor_text">
                                   <?php the_content();
					  				 wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'Statfort' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) );?>
                            </div>
                            </article>

                            <!-- Post tags Section -->
                            <div class="post-tags">
                            	<?php 
									$before_cat = "<ul><li><i class='fa fa-tags'></i>";
									$categories_list = get_the_term_list ( get_the_id(), 'event-tag', $before_cat, ', ', '</li></ul>' );
									if ( $categories_list ){
										printf( __( '%1$s', 'Statfort'),$categories_list );  
									} // End if tags 
									if ($event_social_sharing == "on"){
										cs_addthis_script_init_method();
									?>
                                <a href="#" class="share-post addthis_button_compact"><i class="fa fa-share-square-o"></i><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Share Now','Statfort');}else{ echo $cs_theme_option['trans_share_this_post']; } ?> </a>
                                <?php }?>
                            </div>
                            <!-- Post tags Section Close -->
                         	<?php comments_template('', true); ?>
                        </div>
                   </div>
            
                </div>
			<!-- layout End -->
				<?php if ( $cs_layout  == 'content-left col-md-9'){ ?>
                    <aside class="sidebar-right col-md-3"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar_right) ) : ?><?php endif; ?></aside>
                <?php wp_reset_query();} ?>
<?php
    endwhile;
  get_footer(); ?>