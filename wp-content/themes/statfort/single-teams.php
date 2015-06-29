<?php
	cs_slider_gallery_template_redirect();
	global $cs_node,$cs_theme_option,$cs_counter_node,$cs_video_width;
  	get_header();
	$cs_layout = '';
	if (have_posts()):
		while (have_posts()) : the_post();	
	$post_xml = get_post_meta($post->ID, "cs_team", true);	
	if ( $post_xml <> "" ) {
		$cs_xmlObject_team = new SimpleXMLElement($post_xml);
			$cs_layout = "col-md-8";
			$custom_height = 470;
 	}else{
		$cs_layout = "col-md-8";
	}
			if ( $post_xml <> "" ) {
				$width = 585;
				$height = 440;
				$image_url = cs_get_post_img_src($post->ID, $width, $height);
			}
			else {
				$cs_xmlObject_team = new stdClass();
				$image_url = '';
				$width = 0;
				$height = 0;
 			}		
			?>
                <!-- Columns Start -->
                <div class="clear"></div>
                <!-- Content Section Start -->
    			<div id="main" role="main">
    			<!-- Container Start -->
					<div class="container">
        			<!-- Row Start -->
                        <div class="row">
                        <!-- Need to add code below in function file to call it on all pages -->

                        <!-- Blog Detail Start -->
                        
                
                <div class="col-md-4 pull-right">
                        	<?php
								if($image_url<>''){
								  	echo '<figure class="detail_figure">';
								 	echo '<img src="'.$image_url.'" >';
								  	echo '</figure>';
								}
								?>
                                
                     		<div class="portfolio-detail-sidebar">
                            <?php if($cs_xmlObject_team->var_cp_about<>''){ ?>
                             	<h6><?php _e('About','Statfort') ?></h6>
                                <p><?php echo $cs_xmlObject_team->var_cp_about; ?></p> 
                            <?php } ?>                               
                                <ul>
                                <?php if($cs_xmlObject_team->var_cp_team_email <> ''){ ?>
                                <li>
                                <span class="icon-stack pull-left"><em class="icon-circle icon-stack-base"></em><em class="fa fa-envelope-o"></em></span>
                                    <div class="text">
                                      <span><?php _e('Email','Statfort') ?></span>
                                      <p><?php echo $cs_xmlObject_team->var_cp_team_email ?></p>
                                  </div>
                                  </li>
                                <?php } ?>
                                <?php if($cs_xmlObject_team->var_cp_team_phone <> ''){ ?>
                                <li>
                                <span class="icon-stack pull-left"><em class="fa fa-phone"></em></span>
                                    <div class="text">
                                      <span><?php _e('Phone','Statfort') ?></span>
                                      <p><?php echo $cs_xmlObject_team->var_cp_team_phone;?></p>
                                  </div>
                                  </li>
                                <?php } ?>
                                <?php if($cs_xmlObject_team->var_cp_team_time <> ''){ ?>
                                <li>
                                <span class="icon-stack pull-left"><em class="fa fa-clock-o"></em></span>
                                    <div class="text">
                                      <span><?php _e('Time','Statfort') ?></span>
                                      <p><?php echo $cs_xmlObject_team->var_cp_team_time; ?></p>
                                  </div>
                                  </li>
                                <?php } ?>
                              </ul>
                    		</div>
					<!-- Share Post End -->
                    	</div>
                        
                 <div class="<?php echo $cs_layout; ?> pull-left">
							<!-- Blog Start -->
 							<!-- Blog Post Start -->
                            <div class="blog blog_detail">
                            <article <?php post_class(fnc_post_type($image_url)); ?>>

                                        <!-- Blog Post Thumbnail Start -->
							

                                <div class="detail_text rich_editor_text">
                                	<?php 
									the_content();
								  ?>
                                </div>
                                <div class="right-sec">
											  <?php cs_next_prev_post(); ?>
                                </div>
                            </article>
                            <!-- Post tags Section -->
                            
                            <!-- About Author Section Close -->
                           
						<?php //comments_template('', true); ?>
                     <!-- Blog Post End -->
                     </div>
               	</div>
                
		  		<?php endwhile;   endif;?>
                <!--Content Area End-->
<!-- Columns End -->
<!--Footer-->
<?php get_footer(); ?>
