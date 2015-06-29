 <?php
 	global $cs_node,$post,$cs_theme_option,$cs_counter_node,$cs_meta_page,$cs_video_width; 
 	if ( !isset($cs_node->cs_blog_num_post) || empty($cs_node->cs_blog_num_post) ) { $cs_node->cs_blog_num_post = -1; }
	if ( !isset($cs_node->cs_blog_orderby) || empty($cs_node->cs_blog_orderby) ) { $cs_node->cs_blog_orderby = 'DESC'; }
	$image_url = '';
	?>
    <div class="element_size_<?php echo $cs_node->blog_element_size; ?>">
    <?php	
		 	if ($cs_node->cs_blog_title <> '') { 
				echo'<header class="cs-heading-title">
					<h2 class="cs-section-title float-left">'.$cs_node->cs_blog_title.'</h2>';
					if(isset($cs_node->var_pb_blog_view_all) && $cs_node->var_pb_blog_view_all <> ''){
						 echo '<a class="btnshowmore float-right" href="'.$cs_node->var_pb_blog_view_all.'"> <em class="fa fa-long-arrow-right"></em>';
									if($cs_theme_option['trans_switcher'] == "on"){ _e('View All','Statfort'); }else{ echo $cs_theme_option['trans_view_all']; }
						 echo '</a>';
					}
                echo'</header>';
         	} 
		 ?>
	<div class="postlist blog <?php  echo $cs_node->cs_blog_view; ?> lightbox">
     	<!-- Blog Start -->
		<?php
            if (empty($_GET['page_id_all'])) $_GET['page_id_all'] = 1;
            $args = array('posts_per_page' => "-1", 'paged' => $_GET['page_id_all'], 'post_status' => 'publish');
			if(isset($cs_node->cs_blog_cat) && $cs_node->cs_blog_cat <> '' &&  $cs_node->cs_blog_cat <> '0'){
				$blog_category_array = array('category_name' => "$cs_node->cs_blog_cat");
				$args = array_merge($args, $blog_category_array);
			}
			
            $custom_query = new WP_Query($args);
            $post_count = $custom_query->post_count;
            $count_post = 0;
            // if ($cs_node->cs_blog_pagination == "Single Page") $cs_node->cs_blog_num_post = $cs_node->cs_blog_num_post;
            $args = array('posts_per_page' => "$cs_node->cs_blog_num_post", 'paged' => $_GET['page_id_all'], 'order' => "$cs_node->cs_blog_orderby");
			if(isset($cs_node->cs_blog_cat) && $cs_node->cs_blog_cat <> '' &&  $cs_node->cs_blog_cat <> '0'){
				$blog_category_array = array('category_name' => "$cs_node->cs_blog_cat");
				$args = array_merge($args, $blog_category_array);
				
			}
            $custom_query = new WP_Query($args);
            $cs_counter = 0;
				$custom_width = 980; 
				$custom_height = 408;	
				cs_meta_content_class();
				if( cs_meta_content_class() == "col-md-12"){
				if($cs_node->cs_blog_view == "blog-large"){$custom_width = 980; $custom_height = 408; }elseif($cs_node->cs_blog_view == "blog-medium"){  $custom_width = 230; $custom_height = 172; }	
				}elseif( cs_meta_content_class() == "col-md-9"){
					if($cs_node->cs_blog_view == "blog-large"){ $custom_width = 730; $custom_height = 346; }elseif($cs_node->cs_blog_view == "blog-medium"){  $custom_width = 230; $custom_height = 172; }	
				}
				
				if($cs_node->cs_blog_view == "blog-grid"){
					$custom_width = 230;
					$custom_height = 172;
					$width 	= 230;
					$height	= 172;
 					echo '<div class="latest-news fullwidth">';
					while ($custom_query->have_posts()) : $custom_query->the_post();
					$post_xml = get_post_meta($post->ID, "post", true);	
					if ( $post_xml <> "" ) {
						$cs_xmlObject = new SimpleXMLElement($post_xml);
						$post_view = $cs_xmlObject->post_thumb_view;
						$post_image = $cs_xmlObject->post_thumb_image;
						$post_featured_image = $cs_xmlObject->post_featured_image_as_thumbnail;
						$post_video = $cs_xmlObject->post_thumb_video;
						$post_audio = $cs_xmlObject->post_thumb_audio;
						$post_slider = $cs_xmlObject->post_thumb_slider;
 						$no_image = '';
						$image_url = cs_get_post_img_src($post->ID, $width, $height);
						$image_url_full = cs_get_post_img_src($post->ID, '' ,'');
						if($image_url == "" and $post_view == "Single Image"){
							$no_image = 'no-image';
						}
					}else{
						$post_view = '';
						$no_image = '';	
						$image_url_full = '';
					}
					
					if($image_url <> ''){ 
					?>
						<article>
                        <?php 
  							echo '<figure><a href="'.get_permalink().'" ><img src="'.$image_url.'" alt="" ></a></figure>';
                        ?>
                            <div class="text fullwidth">
								<ul class="post-categories">
                                	<?php
                                    	$before_cat = " ".__( '<li>','Statfort')."";
										$categories_list = get_the_term_list ( get_the_id(), 'category', $before_cat, ', ', '</li>' );
										if ( $categories_list ){
											printf( __( '%1$s', 'Statfort'),$categories_list );
										}
                                    ?>
                                   	
                                 </ul>
                                 <h2 class="cs-post-title">
                                	<a href="<?php the_permalink();?>" class="colrhvr">
										<?php echo substr(get_the_title(), 0, 20); if(strlen(get_the_title())>20) echo '...'; ?>
                                    </a>
                                </h2>
                                 <?php if($cs_node->cs_blog_description == "yes"){?>
                                 	<p><?php  cs_get_the_excerpt($cs_node->cs_blog_excerpt,false);?></p>
                            	<?php }?>
                                
                            </div>
                        </article>
				<?php
				  }
				endwhile;
				echo '</div><div class="clear"></div>';
				} else {
            	while ($custom_query->have_posts()) : $custom_query->the_post();
					$post_xml = get_post_meta($post->ID, "post", true);	
					if ( $post_xml <> "" ) {
						$cs_xmlObject = new SimpleXMLElement($post_xml);
						$post_view = $cs_xmlObject->post_thumb_view;
						$post_image = $cs_xmlObject->post_thumb_image;
						$post_featured_image = $cs_xmlObject->post_featured_image_as_thumbnail;
						$post_video = $cs_xmlObject->post_thumb_video;
						$post_audio = $cs_xmlObject->post_thumb_audio;
						$post_slider = $cs_xmlObject->post_thumb_slider;
 						$no_image = '';
						$custom_cls = '';
						if($cs_node->cs_blog_view == "blog-large"){
							$width 	=980;
							$height	=408;
						}else{
							$width 	=230;
							$height	=172;
						}	
						$image_url = cs_get_post_img_src($post->ID, $width, $height);
						$image_url_full = cs_get_post_img_src($post->ID, '' ,'');
						if($image_url == "" and $post_view == "Single Image"){
							$no_image = 'no-image';
						}
					}else{
						$post_view = '';
						$no_image = '';	
						$image_url_full = '';
					}	
					?>
                    <!-- Blog Post Start -->
                    <article <?php post_class(fnc_post_type($post_view ,$image_url)); ?>>
                    	 <?php
  								echo '<figure>';
  							 	if ( $post_view == "Slider"  and $post_slider <> ''){
                                 	cs_flex_slider($width, $height,$post_slider);
                                 }elseif($post_view == "Single Image"){
                                	if($image_url <> ''){ 
									echo '<a href="'.get_permalink().'" ><img src="'.$image_url.'" alt="" ></a>
										<figcaption>
											<a class="btnreadmore bgcolr" href=""> <em class="fa fa-long-arrow-right"></em>
											</a>
										</figcaption>';
									 }
                                }elseif($post_view == "Video"){
 									$url = parse_url($post_video);
									if($url['host'] == $_SERVER["SERVER_NAME"]){
 									$poster_url = '';
										if($post_featured_image=='on'){$poster_url = $image_url;}
										 if($image_url <> ''){ echo "<a href='".get_permalink()."'><img src=".$image_url." alt='' ></a>";}
									
                                    ?>
                                        <figcaption class="gallery">
                                                      <a data-toggle="modal" data-target="#myModal<?php echo $post->ID;?>"  onclick="cs_video_load('<?php echo get_template_directory_uri();?>', <?php echo $post->ID;?>, '<?php echo $post_video;?>','<?php echo $poster_url;?>');" href="#"><i class="fa fa-video-camera fa-2x"></i></a>
                                         </figcaption>
                                     
									<?php
									}else{
  									  	echo wp_oembed_get($post_video,array('height' =>$custom_height));
									}
  								}elseif($post_view == "Audio" and $post_audio <> ''){
 									if($image_url <> ''){ echo "<a href='".get_permalink()."'><img src=".$image_url." alt='' ></a>";
								}
 								?>
								<figcaption class="gallery">
                                    <div class="audiowrapp fullwidth">
                                        <audio style="width:100%;" src="<?php echo $post_audio; ?>" type="audio/mp3" controls="controls"></audio>
                                    </div>  
                                </figcaption>
								<?php
 								}
								echo '</figure>';
 							 ?>
                        <!-- Blog Post Thumbnail End -->
                        <div class="blog_text webkit">
                        <?php if($cs_node->cs_blog_view == "blog-large"){?>
                        <div class="calendar-date">
                                    <span><?php echo date_i18n('M',strtotime(get_the_date()));?></span>
                                    <time datetime="2014-12-01"><?php echo date('d',strtotime(get_the_date()));?></time>
                                </div>
                                <?php } ?>
                        	<div class="text">
                            	<h2 class="heading-color cs-post-title"> 
                                	<a href="<?php the_permalink(); ?>" class="colrhvr">
									<?php the_title(); ?>
                                    </a>
                                </h2>
                                     <ul class="post-options">
                                        <li>
                                            <i class="fa fa-user"></i>
                                            <?php printf( __('%s','Statfort'), '<a href="'.get_author_posts_url(get_the_author_meta('ID')).'" >'.get_the_author().'</a>' );?>
                                        </li>
                                        <?php cs_featured(); ?>
                                     	<li>
                                      	<?php
										  /* translators: used between list items, there is a space after the comma */
										  $before_cat = " ".__( '<i class="fa fa-list"></i>','Statfort')."";
										  $categories_list = get_the_term_list ( get_the_id(), 'category', $before_cat, ', ', '' );
										  	if ( $categories_list ){
												printf( __( '%1$s', 'Statfort'),$categories_list );
										  	}
										  	if ( comments_open() ) {  echo "<li><i class='fa fa-comment-o'></i>"; comments_popup_link( __( '0 Comment', 'Statfort' ) , __( '1 Comment', 'Statfort' ), __( '% Comments', 'Statfort' ) ); }
										?>
                           			</ul>
                                 <?php
								if($cs_node->cs_blog_description == "yes"){?>
                                 	<p><?php cs_get_the_excerpt($cs_node->cs_blog_excerpt,true);?></p>
                            <?php }?>
                             </div>
                         </div>
                     </article>
                     <?php if($post_view == "Video"){?>
                    <div class="modal fade" id="myModal<?php echo $post->ID;?>" tabindex="-1" role="dialog" aria-hidden="true"></div>
                    <?php }?>
                      <?php wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'Statfort' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) ); ?>
                    <!-- Blog Post End -->
               		<?php endwhile;  ?>
                 	<!-- Blog End -->
                <?php
				}
				echo '</div>';
                $qrystr = '';
               if ( $cs_node->cs_blog_pagination == "Show Pagination" and $post_count > $cs_node->cs_blog_num_post and $cs_node->cs_blog_num_post > 0 ) {
					if ( isset($_GET['page_id']) ) $qrystr = "&page_id=".$_GET['page_id'];
						echo cs_pagination($post_count, $cs_node->cs_blog_num_post,$qrystr);
                }
                 // pagination end
             ?>
	</div>