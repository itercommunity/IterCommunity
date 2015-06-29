<?php
	get_header();
	global  $cs_theme_option; 
 	if(isset($cs_theme_option['cs_layout'])){ $cs_layout = $cs_theme_option['cs_layout']; }else{ $cs_layout = '';} 
?>
<div role="main" id="main">
	<div class="container columns"> 
    	<div class="row">
    		<?php
    			if ( $cs_layout <> '' and $cs_layout  <> "none" and $cs_layout  == 'left' or $cs_layout  == 'both') :  ?>
					<aside class="left-content col-md-3">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_theme_option['cs_sidebar_left']) ) : endif; ?>
					</aside>
   			<?php endif; ?>	
        	<div class="<?php cs_default_pages_meta_content_class( $cs_layout ); ?>">
             	<div class="postlist blog blog-medium lightbox">
                 <!-- Blog Post Start -->
                 <?php
               		if ( have_posts() ) : 
						 while ( have_posts() ) : the_post();
						 $width 	=230;
						  $height	=172;
						  $image_url = cs_get_post_img_src($post->ID, $width, $height);

						 ?>	
                			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> >
                            <?php if($image_url<>''){ ?>
                             <figure><a href=""><img src="<?php echo $image_url; ?>" alt=""></a>
                                  <figcaption>
                                      <a class="btnreadmore bgcolr" href="<?php the_permalink(); ?>"> <em class="fa fa-long-arrow-right"></em>
                                      </a>
                            </figcaption>
                            </figure>
                            <?php } ?>
								<!-- Text Start -->
								<div class="blog_text webkit">
									<div class="text">
 										<h2 class="heading-color cs-post-title"><a href="<?php the_permalink(); ?>" class="colrhvr"><?php the_title(); ?></a></h2>
										<?php cs_posted_on(); ?>  
                                		<p><?php echo cs_get_the_excerpt(255,true); ?></p>
									</div>
 									
								</div>
								<!-- Text End -->
														   
							</article>
						<?php  
						endwhile;   
					else:
						fnc_no_result_found(); 
					endif;
     				
					?>
               	</div>
                <?php
                	$qrystr = '';
                    // pagination start
					if ($wp_query->found_posts > get_option('posts_per_page')) {

							if ( isset($_GET['s']) ) $qrystr = "&s=".$_GET['s'];
							if ( isset($_GET['page_id']) ) $qrystr .= "&page_id=".$_GET['page_id'];
							echo cs_pagination($wp_query->found_posts,get_option('posts_per_page'), $qrystr);
					}
					// pagination end
             	?>                    
             </div>
			<?php
                if ( $cs_layout <> '' and $cs_layout  <> "none" and $cs_layout  == 'right' or $cs_layout  == 'both') :  ?>
                    <aside class="left-content col-md-3">
						<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_theme_option['cs_sidebar_left']) ) : endif; ?>
					</aside>
            <?php endif; ?>	
<?php get_footer();?>
<!-- Columns End -->