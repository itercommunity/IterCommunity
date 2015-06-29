<?php
	cs_slider_gallery_template_redirect();
	global $cs_node,$cs_theme_option,$cs_counter_node,$cs_video_width;
	$cs_node = new stdClass();
  	get_header();
	$cs_layout = '';
	if (have_posts()):
		while (have_posts()) : the_post();	
	$post_xml = get_post_meta($post->ID, "post", true);	
	if ( $post_xml <> "" ) {
		$cs_xmlObject = new SimpleXMLElement($post_xml);
		$cs_layout = $cs_xmlObject->sidebar_layout->cs_layout;
 		$cs_sidebar_left = $cs_xmlObject->sidebar_layout->cs_sidebar_left;
		$cs_sidebar_right = $cs_xmlObject->sidebar_layout->cs_sidebar_right;
		if ( $cs_layout == "left") {
			$cs_layout = "content-right col-md-9";
			$custom_height = 348;
 		}
		else if ( $cs_layout == "right" ) {
			$cs_layout = "content-left col-md-9";
			$custom_height = 348;
 		}
		else {
			$cs_layout = "col-md-12";
			$custom_height = 470;
		}
 	}else{
		$cs_layout = "col-md-12";
	}

			if ( $post_xml <> "" ) {
				$cs_xmlObject = new SimpleXMLElement($post_xml);
				$post_view = $cs_xmlObject->inside_post_thumb_view;
 				$post_video = $cs_xmlObject->inside_post_thumb_video;
				$post_audio = $cs_xmlObject->inside_post_thumb_audio;
				$post_slider = $cs_xmlObject->inside_post_thumb_slider;
 				$post_featured_image = $cs_xmlObject->inside_post_featured_image_as_thumbnail;
				$post_author_description =$cs_xmlObject->post_author_description;
				$width = 980;
				$height = 408;
				$image_url = cs_get_post_img_src($post->ID, $width, $height);
			}
			else {
				$cs_xmlObject = new stdClass();
				$post_view = '';
 				$post_video = '';
				$post_audio = '';
				$post_slider = '';
				$post_slider_type = '';
				$image_url = '';
				$width = 0;
				$height = 0;
				$image_id = 0;
				$custom_height = 470;
				$post_author_description = '';
				$cs_xmlObject->post_related_posts = '';
				$cs_xmlObject->related_posts_title = '';
				$cs_xmlObject->post_social_sharing = '';
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
                        <!--Left Sidebar Starts-->
                        <?php if ($cs_layout == 'content-right col-md-9'){ ?>
                            <div class="col-lg-3 col-md-3 col-sm-3"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar_left) ) : ?><?php endif; ?></div>
                        <?php } ?>
                        <!--Left Sidebar End-->
                        <!-- Blog Detail Start -->
                        <div class="<?php echo $cs_layout; ?>">
							<!-- Blog Start -->
 							<!-- Blog Post Start -->
                            <div class="blog blog_detail">
                            <article <?php post_class(fnc_post_type($post_view ,$image_url)); ?>>
                                <?php if(isset($post_view) and $post_view <> ''){
									
						
                                    if( $post_view == "Slider" and $post_slider <> ''){
                                        echo '<figure class="detail_figure">';
                                         cs_flex_slider($width, $height,$post_slider);
										   echo '</figure>';
                                     }elseif($post_view == "Single Image" && $image_url <> ''){ 
                                          echo '<figure class="detail_figure">';
										 echo '<img src="'.$image_url.'" >';
										  echo '</figure>';
                                       }elseif($post_view == "Video" and $post_video <> '' and $post_view <> ''){
                                          
										  $url = parse_url($post_video);
                                         if($url['host'] == $_SERVER["SERVER_NAME"]){?>
                                            <figure class="detail_figure">
                                            <video width="<?php echo $width;?>" class="mejs-wmp" height="100%"  style="width: 100%; height: 100%;" src="<?php echo $post_video ?>"  id="player1" poster="<?php if($post_featured_image == "on"){ echo $image_url; } ?>" controls="controls" preload="none"></video>
                                            </figure>
                                        <?php
                                        }else{
                                              echo wp_oembed_get($post_video,array('height' => $custom_height));
                                        }
                                     }elseif($post_view == "Audio" and $post_view <> ''){
                                          echo '<figure class="detail_figure">';
										 ?>
                                         <figcaption class="gallery">
                                            <div class="audiowrapp fullwidth">
                                                <audio style="width:100%;" src="<?php echo $post_audio; ?>" type="audio/mp3" controls="controls"></audio>
                                            </div>  
                                        </figcaption>
                                        <?php
                                       echo '</figure>';
                                    }
								
                                    ?>
                             <?php } ?>
                                <ul class="post-options">
                                	
                                    <li><i class="fa fa-calendar"></i><time><?php echo get_the_date(); ?></time></li>
                                    <li><i class="fa fa-user"></i><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php echo get_the_author(); ?></a></li>
                                    
									<?php 
										/* translators: used between list items, there is a space after the comma */
										$before_cat = "<li><i class='fa fa-align-justify'></i>";
										$categories_list = get_the_term_list ( get_the_id(), 'category', $before_cat, ', ', '</li>' );
										if ( $categories_list ){
											printf( __( '%1$s', 'Statfort'),$categories_list );
										} // End if categories 
									?>
                                    
                                    <?php 
                                        if ( comments_open() ) {  echo "<li><i class='fa fa-comment-o'></i>"; comments_popup_link( __( '0 Comment', 'Statfort' ) , __( '1 Comment', 'Statfort' ), __( '% Comment', 'Statfort' ) ); } ?>
                                        <li class="float-right">
                                        	<div class="right-sec">
											  <?php cs_next_prev_post(); ?>
                                            </div>
                                        
                                        </li>
                                </ul>
                                <div class="detail_text rich_editor_text">
                                	<?php the_content();

										wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'Statfort' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) );
								  ?>
                                </div>
                            </article>
                            <!-- Post tags Section -->
                            <div class="post-tags">
                                <ul>
                                    <?php 
										/* translators: used between list items, there is a space after the comma */
										$before_cat = "<li><i class='fa fa-tags'></i>";
										$categories_list = get_the_term_list ( get_the_id(), 'post_tag', $before_cat, ', ', '</li>' );
										if ( $categories_list ){
											printf( __( '%1$s', 'Statfort'),$categories_list );
										} // End if categories 
									?>
                                </ul>
                                <?php 
								if ($cs_xmlObject->post_social_sharing == "on"){
									cs_addthis_script_init_method();
								?>
                                <a class="addthis_button_compact share-post backcolrhover" href="#"><i class="fa fa-share-square-o"></i><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Sahre','Stat Fort');}else{ echo $cs_theme_option['trans_share_this_post']; } ?> </a>
                                <?php
								}
								?>
                                
                                
                            </div>
                            <!-- Post tags Section Close -->
                            
                            <!-- About Author Section -->
                            <?php
								if($post_author_description == 'on'){
									cs_author_description();
								}
							?>
                            
                            <!-- About Author Section Close -->
                            <?php if($cs_xmlObject->post_related_posts == 'on'){ ?>
                            <!--Related Blog Post Section-->
                            <div class="element_size_100">
                            	<?php if($cs_xmlObject->related_posts_title <> ''){ ?>
                                 <header class="cs-heading-title">
                                    <h2 class="cs-section-title"><?php echo $cs_xmlObject->related_posts_title; ?></h2>
                                 </header>
                                <?php } ?>
                                <div class="blog-grid blog-grid-view-2">
                                	<?php
									wp_reset_query();
									$custom_taxterms='';
									$custom_taxterms = wp_get_object_terms( $post->ID, array('category', 'post_tag'), array('fields' => 'ids') );
									// arguments
									$args = array(
										'post_type' => 'post',
										'post_status' => 'publish',
										'posts_per_page' => 3, // you may edit this number
										'orderby' => 'DESC',
										'tax_query' => array(
											'relation' => 'OR',
											array(
												'taxonomy' => 'post_tag',
												'field' => 'id',
												'terms' => $custom_taxterms
											),
											array(
												'taxonomy' => 'category',
												'field' => 'id',
												'terms' => $custom_taxterms
											)
										),
										'post__not_in' => array ($post->ID),
									); 
									$custom_query = new WP_Query($args);
									$counter_posts_db = 0;
									if($custom_query->have_posts()):
									while ( $custom_query->have_posts()) : $custom_query->the_post();
										 $cs_album = get_post_meta($post->ID, "post", true);
										 if ( $cs_album <> "" ) {
											  $cs_xmlObject = new SimpleXMLElement($cs_album);
										 }
										$counter_posts_db++;
										$width 	= 230;
										$height	= 172;
										$cats = array();
										$image_url = cs_get_post_img_src($post->ID, $width, $height);                    
									?>
                                    <article>
 										<?php 
                                            if($image_url <> ''){
												echo "<figure><img src=".$image_url." alt='' ></figure>";
											} else {
												echo '<figure><img src="' . get_template_directory_uri() . '/images/Dummy.jpg" alt="" /></figure>';
											}
                                        ?>
                                         <div class="text">
                                            <h2 class="cs-post-title"><a href="<?php the_permalink(); ?>" class="colrhvr">
											<?php echo substr(get_the_title(),0,20); if(strlen(get_the_title()) > 20) { echo "...";} ?></a></h2>
                                            <p style="display: none;"><?php cs_get_the_excerpt(60,false);?>...</p>
                                        </div>
                                    </article>
                                    <?php endwhile; endif;
									wp_reset_query();
									?> 
                                </div>
                            </div>
                            <!--Related Blog Post Section Close-->
                            <?php } ?>
                           
						<?php comments_template('', true); ?>
                     <!-- Blog Post End -->
                     </div>
               	</div>
		  		<?php endwhile;   endif;?>
                <!--Content Area End-->
                <!--Right Sidebar Starts-->
                <?php if ( $cs_layout  == 'content-left col-md-9'){ ?>
                	<div class="col-lg-3 col-md-3 col-sm-3"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar_right) ) : ?><?php endif; ?></div>
                <?php } ?>
<!-- Columns End -->
<!--Footer-->
<?php get_footer(); ?>
