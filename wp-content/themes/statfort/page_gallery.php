<?php
	global $cs_node,$cs_counter_node;
 	$count_post =0;
 	$gal_album_db = $cs_node->album;
	// galery slug to id start
		$args=array(
			'name' => $gal_album_db,
			'post_type' => 'cs_gallery',
			'post_status' => 'publish',
			'showposts' => 1,
		);
 		$get_posts = get_posts($args);
		if($get_posts){
			$gal_album_db = $get_posts[0]->ID;
		}
	// galery slug to id end
	$cs_meta_gallery_options = get_post_meta($gal_album_db, "cs_meta_gallery_options", true);
	if ( empty($_GET['page_id_all']) ) $_GET['page_id_all'] = 1;
	// pagination start
	if ( $cs_meta_gallery_options <> "" ) {
		$cs_xmlObject = new SimpleXMLElement($cs_meta_gallery_options);
		if ($cs_node->media_per_page > 0 ) {
			$limit_start = $cs_node->media_per_page * ($_GET['page_id_all']-1);
			$limit_end = $limit_start + $cs_node->media_per_page;
			$count_post = count($cs_xmlObject);
				if ( $limit_end > count($cs_xmlObject) ) 
					$limit_end = count($cs_xmlObject);
		}
		else {
			$limit_start = 0;
			$limit_end = count($cs_xmlObject);
			$count_post = count($cs_xmlObject);
		}
	}
 	?>
	<div class="element_size_<?php echo $cs_node->gallery_element_size; ?>">
    	<?php if ($cs_node->header_title <>  '' ) { ?>
    		<header class="heading">
        		<h2 class="section-title heading-color"><?php echo $cs_node->header_title; ?></h2>
        	</header>
   	 	<?php  } ?>
        		<?php
			if($cs_node->layout == 'gallery-masonry'){ 
			 	cs_enqueue_masonry_style_script();
			?>
 			  <script type="text/javascript">
              		jQuery(document).ready(function(){ 
 						var container = jQuery('#container<?php echo $cs_counter_node; ?>, .blog #container<?php echo $cs_counter_node;?>');
						jQuery(container).imagesLoaded( function(){
						jQuery(container).isotope({
						columnWidth: 10,
						itemSelector: '.box'
 						});
					});
					if (jQuery.browser.msie && navigator.userAgent.indexOf('Trident')!==-1){
						jQuery(container).isotope({
						columnWidth: 10,
						itemSelector: '.box'
 					});
				}	
 				});
               </script>
			  <div class="gallerysec gallery" id="container<?php echo $cs_counter_node; ?>">
             	<ul class="<?php echo $cs_node->layout; ?> lightbox clearfix">
				<?php
 					
     				if ( $cs_meta_gallery_options <> "" ) {
						for ( $i = $limit_start; $i < $limit_end; $i++ ) {
						$path = $cs_xmlObject->gallery[$i]->path;
						$title = $cs_xmlObject->gallery[$i]->title;
						$social_network = $cs_xmlObject->gallery[$i]->social_network;
						$use_image_as = $cs_xmlObject->gallery[$i]->use_image_as;
						$video_code = $cs_xmlObject->gallery[$i]->video_code;
						$link_url = $cs_xmlObject->gallery[$i]->link_url;
						$image_url_full = cs_attachment_image_src($path, 0, 0);
  						?>
						<li class="box photo">
                        	<!-- Gallery Listing Item Start -->
                            <div class="ms-box">
								<a data-title="<?php if ( $title <> "" ) { echo $title;}?>"  href="<?php if($use_image_as==1)echo $video_code; elseif($use_image_as==2) echo $link_url; else echo $image_url_full;?>" target="<?php if($use_image_as==2) echo '_blank'; ?>" data-rel="<?php if($use_image_as==1)echo "prettyPhoto";  elseif($use_image_as==2) echo ""; else echo "prettyPhoto[gallery1]"?>">
                            		<figure>
									<?php echo "<img src='".$image_url_full."' data-alt='".$title."' alt='' />"; ?>
                                    <figcaption>
                                     <span class="bghover"></span>
                                     <div class="text">
									   <?php 
											  if($use_image_as==1){
												  echo '<i class="fa fa-play fa-3x"></i>';
											  }elseif($use_image_as==2){
												  echo '<i class="fa fa-link fa-3x"></i>';	
											  }else{
												  echo '<i class="fa fa-picture-o fa-3x"></i>';
											  }
										  ?>
									</div>
                                    </figcaption>
                                    
                                	</figure>
                               </a>
                            	
							</div>
                        </li>
						<?php
					}
				}
			?>
            </ul>
            </div>
            <?php
		 
			}else{
			?>
			<div class="gallerysec gallery">
	            <ul class="<?php echo $cs_node->layout; ?> lightbox clearfix">
            <?php
            if ( $cs_meta_gallery_options <> "" ) {
                for ( $i = $limit_start; $i < $limit_end; $i++ ) {
                    $path = $cs_xmlObject->gallery[$i]->path;
                    $title = $cs_xmlObject->gallery[$i]->title;
                    $social_network = $cs_xmlObject->gallery[$i]->social_network;
                    $use_image_as = $cs_xmlObject->gallery[$i]->use_image_as;
                    $video_code = $cs_xmlObject->gallery[$i]->video_code;
                    $link_url = $cs_xmlObject->gallery[$i]->link_url;
 					$image_url = cs_attachment_image_src($path, 585, 440);
                    $image_url_full = cs_attachment_image_src($path, 0, 0);
 					?>
                    <li>
						<a data-title="<?php if ( $title <> "" ) { echo $title;}?>"  href="<?php if($use_image_as==1)echo $video_code; elseif($use_image_as==2) echo $link_url; else echo $image_url_full;?>" target="<?php if($use_image_as==2) echo '_blank'; ?>" data-rel="<?php if($use_image_as==1)echo "prettyPhoto";  elseif($use_image_as==2) echo ""; else echo "prettyPhoto[gallery1]"?>">							  
                      	<figure>
                            <?php echo "<img src='".$image_url."' data-alt='".$title."' alt='' />";  ?>
                            <figcaption>
                                 <span class="bghover"></span>
                                 <div class="text">
								   <?php 
										  if($use_image_as==1){
											  echo '<i class="fa fa-play"></i>';
										  }elseif($use_image_as==2){
											  echo '<i class="fa fa-link"></i>';	
										  }else{
											  echo '<i class="fa fa-picture-o"></i>';
										  }
									  ?>
								</div>
                                </figcaption>
                            
                        	</figure>
                        </a>
                    </li>
                    <?php
                }
            } 
            ?>
            </ul>
             </div>
            <?php } ?>
         
		<?php
         	// pagination start
       		$qrystr = '';
             if ( $cs_node->pagination == "Show Pagination" and $count_post > $cs_node->media_per_page and $cs_node->media_per_page > 0 ) {
                    $qrystr = '';
                    if ( isset($_GET['page_id']) ) $qrystr = "&page_id=".$_GET['page_id'];						
                echo cs_pagination( $count_post, $cs_node->media_per_page,$qrystr );
            }
         	// pagination end
    	?>
 </div>
 <div class="clear"></div>