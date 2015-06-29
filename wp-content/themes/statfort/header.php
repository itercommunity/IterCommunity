<?php
global $cs_theme_option, $cs_position, $cs_page_builder, $cs_meta_page, $cs_node;
$cs_theme_option = get_option('cs_theme_option');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>" />
    <title>
	<?php
	    bloginfo('name'); ?> | 
    <?php 
		if ( is_home() or is_front_page() ) { bloginfo('description'); }
		else { wp_title(''); }
    ?>
    </title>
    <link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php 
            if ( is_singular() && get_option( 'thread_comments' ) )
            	wp_enqueue_script( 'comment-reply' );  
				cs_header_settings();
				wp_head(); 
        ?>
    </head>
	<body <?php body_class(); cs_bg_image(); cs_bgcolor_pattern(); ?> >
	  <?php
	  	$sidebars_widgets = get_option('sidebars_widgets');
              cs_custom_styles();
              cs_under_construction();
              cs_color_switcher(); 
        ?>
		<!-- Wrapper Start -->
		<div class="wrapper wrapper_boxed" id="wrappermain-pix">
			<?php
                cs_get_header();
            // Header sticky menu
            if(isset($cs_theme_option['header_sticky_menu']) and $cs_theme_option['header_sticky_menu'] == "on"){ 
 				?>
                <script type="text/javascript">
					jQuery(document).ready(function(){	
 						cs_menu_sticky();
					});
				</script>
                <?php
            } 
             ?>
            <?php 
                if (is_home() || is_front_page()) {
                    
				if(isset($cs_theme_option['announcement_blog_category']) and $cs_theme_option['announcement_blog_category'] <> ""){
					fnc_announcement();
			 }
			 ?>
                
                <?php 
				//Home page Slider Start
                    cs_get_home_slider();
                    //Home page Slider End 
				
				cs_services(); ?> 
                <?php
                  } else { 
				  $header_banner = '';
				  if(is_page()){
                       $cs_meta_page = cs_meta_page('cs_page_builder');
                       if (!empty($cs_meta_page)) {
						   $header_banner = $cs_meta_page->header_banner;
					   }
				  }
				//  echo $cs_theme_option['header_banner'];
                ?>
                <div class="breadcrumb" <?php if(isset($header_banner) && $header_banner <> ''){?>style=" background: url('<?php echo $header_banner;?>') no-repeat scroll center bottom / cover  rgba(0, 0, 0, 0)"<?php }?>>
                	<div class="container">
                    	<div class="breadcrumb-inner">
                        	<?php
							if(function_exists("is_shop") and 1==2 and is_shop()){
								$cs_shop_id = woocommerce_get_page_id( 'shop' );
								echo "<div class=\"subtitle\"><h1 class=\"cs-page-title\">".get_the_title($cs_shop_id)."</h1></div>";
							}else if(function_exists("is_shop") and 1==2 and !is_shop()){
								echo '<div class="subtitle">';
								get_subheader_title();
								echo '</div>';
							}else{
								echo '<div class="subtitle">';
								get_subheader_title();
								echo '</div>';
							}
							cs_header_breadcrums();
							?>
                         </div>
                    </div>
                </div>
                <div class="clear"></div>
                
				<?php 
                   /* Header Slider and Map Code start  */
                   if(is_page()){
                       $cs_meta_page = cs_meta_page('cs_page_builder');
                       if (!empty($cs_meta_page)) {
						   echo '<div class="header_element">';
                           foreach ( $cs_meta_page->children() as $cs_node ){ 
                           		if($cs_node->getName() == "map" and $cs_node->map_view == "header"){
                                	echo cs_map_page();
                                } elseif ($cs_node->getName() == "slider" and $cs_node->slider_view == "header" and $cs_node->slider_type != "Custom Slider") {
									get_template_part( 'page_slider', 'page' );
									$cs_position = 'absolute';
								}
                           }
						   echo '</div>';
                       }
                   }
                   /* Header Slider and Map Code End  */
                }                      
                ?>
				<div class="clear"></div>
