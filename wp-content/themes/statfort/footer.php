			   
             </div>
            <!-- Row End -->
        </div>
	</div>
    <!-- Content Section End -->
    <div class="clear"></div>
    
		<?php global $cs_theme_option;
			if(isset($cs_theme_option['show_partners'])){
				if($cs_theme_option['show_partners'] == "all"){
					echo cs_show_partner();
				}elseif($cs_theme_option['show_partners'] == "home"){
					if(is_home() || is_front_page()){
						echo cs_show_partner();
					}
				}
			}
		?>   
             <!-- Footer Widgets Start -->
             <div id="footer-widgets" class="fullwidth" <?php if(isset($cs_theme_option['footer_bgimg'])){?>style=" background: url('<?php echo $cs_theme_option['footer_bgimg'];?>') no-repeat scroll center bottom / cover  rgba(0, 0, 0, 0)"<?php }?>>
                <!-- Container Start -->
                <div class="container">
                    <!-- Footer Widgets Start -->
                    <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('footer-widget')) : ?><?php endif; ?>
                    <!-- Footer Widgets End -->
                </div>
                <!-- Container End -->
                <footer id="footer">
                    <div class="container">
                    	<?php if(isset($cs_theme_option['footer_logo']) and $cs_theme_option['footer_logo'] <> ''){?>
                            <header>
	                            <a href="<?php echo home_url(); ?>">
    	                            <img src="<?php echo $cs_theme_option['footer_logo']; ?>" alt="<?php echo bloginfo('name'); ?>">        
        	                    </a>
                            </header>
                        <?php }elseif(!isset($cs_theme_option)){ ?>
							<header>
	                            <a href="<?php echo home_url(); ?>">
    	                            <img src="<?php echo get_template_directory_uri();?>/images/footer-logo.png" alt="<?php echo bloginfo('name'); ?>">        
        	                    </a>
                            </header>
						<?php }?>
                        <p class="copright">
                            <?php 
								if(isset($cs_theme_option['copyright']) and $cs_theme_option['copyright'] <> ''){
									echo do_shortcode(htmlspecialchars_decode($cs_theme_option['copyright'])); 
								}else{ 
								?>
                            		<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twentyfourteen' ) ); ?>">
										<?php echo "&copy; ".gmdate("Y"). " ".get_option('blogname')." Wordpress All rights reserved";  ?>
                                    </a>
                            <?php }?> 
							<?php if(isset($cs_theme_option['powered_by'])){ echo do_shortcode(htmlspecialchars_decode($cs_theme_option['powered_by'])); } ?>
                         
                         </p>
                          <!-- Language Section Start -->

                            <div class="language-sec">

                                <!-- Wp Language Start -->

                                

                                 <?php 

                                 if(isset($cs_theme_option['header_languages'])){

                                     if(isset($cs_theme_option['header_languages']) && $cs_theme_option['header_languages'] == 'on'){

                                        do_action('icl_language_selector');

                                     }

                                 }

                                ?>

                            </div>

                            <!-- Language Section End -->
                        <a class="back-to-top bgcolrhvr" id="btngotop" href=""><em class="fa fa-chevron-up"></em></a>
                    </div>
                </footer>
              </div>
            <!-- Footer Start -->
      <div class="clear"></div>
</div>
<!-- Wrapper End -->
<?php 
	cs_footer_settings();
	wp_footer();	
?>
</body>
</html>