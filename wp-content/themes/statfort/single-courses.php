<?php
get_header(); 
	global $node,$cs_theme_option;
	$cs_layout = '';
	if ( have_posts() ) while ( have_posts() ) : the_post();
	$post_xml = get_post_meta($post->ID, "cs_course", true);	
	if ( $post_xml <> "" ) {
		$cs_xmlObject = new SimpleXMLElement($post_xml);
 		$cs_layout = $cs_xmlObject->sidebar_layout->cs_layout;
 		$cs_sidebar_left = $cs_xmlObject->sidebar_layout->cs_sidebar_left;
		$cs_sidebar_right = $cs_xmlObject->sidebar_layout->cs_sidebar_right;
		$course_date = $cs_xmlObject->course_date;
		$course_duration = $cs_xmlObject->course_duration;
		$course_apply = $cs_xmlObject->course_apply;
		$course_phone = $cs_xmlObject->course_phone;
		$course_fax = $cs_xmlObject->course_fax;
		$course_web_url = $cs_xmlObject->course_web_url;
		$course_email = $cs_xmlObject->course_email;
		if(isset($cs_xmlObject->var_cp_team_members) && $cs_xmlObject->var_cp_team_members <> ''){
			$var_cp_team_members = $cs_xmlObject->var_cp_team_members;
			if ($var_cp_team_members)
			{
				$var_cp_team_members = explode(",", $var_cp_team_members);
			}
		} else {
			$var_cp_team_members = array();
		}
		$course_social = $cs_xmlObject->course_social;
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
  	}
	else {
		$sub_title = '';
		$course_date = '';
		$course_duration = '';
		$course_apply = '';
		$course_phone = '';
		$course_fax = '';
		$course_web_url = '';
		$course_email = '';
		$course_social = '';
		$var_cp_team_members = array();
	}
	$width = 980;
	$height = 408;
	$image_url = cs_get_post_img_src($post->ID, $width, $height);
	?>
   	<!-- Columns Start -->
    <div class="clear"></div>
    <!-- Content Section Start -->
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
                        <div class="course-detail">
                            <article>
                            	<?php if($image_url <> ''){?>
                            		<figure class="detail_figure"><img src="<?php echo $image_url;?>" alt="<?php echo get_the_title();?>"></figure>
                                <?php }?>
                                <ul class="post-options">
                                    <li><i class="fa fa-calendar"></i><time><?php echo get_the_date(); ?></time></li>
                                    <li><i class="fa fa-user"></i><a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php echo get_the_author(); ?></a></li>
                                    <?php 
										$before_cat ='<li><i class="fa fa-align-justify"></i>';
										$categories_list = get_the_term_list ( get_the_id(), 'course-category', $before_cat, ', ', '</li>' );
										if ( $categories_list ): printf( __( '%1$s', 'Statford'),$categories_list ); endif;
									?>
                                    <?php 
                                        if ( comments_open() ) {  echo "<li><i class='fa fa-comment-o'></i>"; comments_popup_link( __( '0 Comment', 'Statfort' ) , __( '1 Comment', 'Statfort' ), __( '% Comments', 'Statfort' ) ); } ?>
                                </ul>
                                <div class="detail_text rich_editor_text">
                                	<div class="addmition-info">
                                    	<h2 class="header"><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('For Admission','Statfort'); }else{ echo $cs_theme_option['trans_course_admission']; } ?></h2>
                                        <div class="text">
                                        	<h6><?php echo $cs_xmlObject->var_cp_dept_name;?></h6>
                                            <?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Starts','Statfort'); }else{ echo $cs_theme_option['trans_course_start_from']; } ?>: <?php echo date('d F, Y',strtotime($cs_xmlObject->course_date));?><br/>
                                            <?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Duration','Statfort'); }else{ echo $cs_theme_option['trans_course_duration']; } ?> : <?php echo $cs_xmlObject->course_duration;?><br/>
                                            <?php
                                                    $var_cp_team_members = $cs_xmlObject->var_cp_team_members;
                                                    if ($var_cp_team_members)
                                                    {
														$count_members = 0;
                                                        $var_cp_team_members = explode(",", $var_cp_team_members);
                                                        if(count($var_cp_team_members)>0){?>
                                                        <?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Instructors','Statfort'); }else{ echo $cs_theme_option['trans_course_instructor']; } ?>:
                                                            <?php
															foreach($var_cp_team_members as $speakers){
																$count_members++;
                                                                echo get_the_title((int) $speakers);
																if($count_members<count($var_cp_team_members)){
																	echo ', ';	
																}
                                                            }
                                                            echo '<br/>';
                                                        }
                                                    }
                                                    ?>
                                        	<?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Phone','Statfort'); }else{ echo $cs_theme_option['trans_course_phone']; } ?> : <?php echo $cs_xmlObject->course_phone;?><br/>
                                            <?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Fax','Statfort'); }else{ echo $cs_theme_option['trans_course_fax']; } ?>: <?php echo $cs_xmlObject->course_fax;?><br/>
                                           <?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Email','Statfort'); }else{ echo $cs_theme_option['trans_course_email']; } ?> : <a href="mailto:<?php echo $cs_xmlObject->course_email;?>"><?php echo $cs_xmlObject->course_email;?></a><br/>
                                            <a href="<?php echo cs_addhttp($cs_xmlObject->course_web_url);?>" target="_blank"><?php echo cs_remove_http($cs_xmlObject->course_web_url);?></a>
                                        </div>
                                    </div>
                                	<?php the_content();
					  				 wp_link_pages( array( 'before' => '<div class="page-links"><span class="page-links-title">' . __( 'Pages:', 'Statfort' ) . '</span>', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>' ) );?>
                                </div>
                                
                            </article>
                            <?php if(count($cs_xmlObject->subject )>0){?>
                                	<!--Tabs Style Start-->        
                                    <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Course Title','Statfort'); }else{ echo $cs_theme_option['trans_course_course_title']; } ?></th>
                                            <th><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Instructor','Statfort'); }else{ echo $cs_theme_option['trans_course_instructor']; } ?></th>
                                            <th><?php  if($cs_theme_option['trans_switcher']== "on"){ _e('Credit Hours','Statfort'); }else{ echo $cs_theme_option['trans_course_credit_hours']; } ?></th>
                                         </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            if ( $post_xml <> "" ) {
                                                foreach ( $cs_xmlObject->subject as $subject ){
                                                        echo '<tr>
                                                            <td>'. $subject->subject_title.'</td>
                                                            <td>'.$subject->subject_instructor.'</td>
                                                            <td>'.$subject->subject_credit_hours.'</td>
                                                        </tr>';	
                                                }
                                            }
                                        ?>
                                       </tbody>
                                </table>
                                <!--Tabs Style Close--> 
                                <?php }?>
                            <!-- Post tags Section -->
                            <div class="post-tags">
                                <?php
									/* translators: used between list items, there is a space after the comma */
									$before_tag = "<ul><li>".__( 'Tags','Statfort' ).": ";
									$tags_list = get_the_term_list ( get_the_id(), 'course-tag',$before_tag, ', ', '</li></ul>' );
									if ( $tags_list){
										printf( __( '%1$s', 'Statfort' ),$tags_list ); 
									} // End if categories 
								if ($course_social == "on"){
									cs_addthis_script_init_method();
									?>
                                <a href="#" class="share-post addthis_button_compact"><i class="fa fa-share-square-o"></i><?php if($cs_theme_option['trans_switcher'] == "on"){ _e('Share Now','Statfort');}else{ echo $cs_theme_option['trans_share_this_post']; } ?> </a>
                                <?php }?>
                            </div>
                            <!-- Post tags Section Close -->
                            <!-- Comments Section Start -->
                            <?php comments_template('', true); ?>
                            <!-- Comments Section Ends -->
                        </div>
                   </div>
             </div>
            <!---------Right Sidebar Starts----------->
            <?php if ( $cs_layout  == 'content-left col-md-9'){ ?>
                <aside class="sidebar-right col-md-3"><?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_sidebar_right) ) : ?><?php endif; ?></aside>
            <?php wp_reset_query();} ?>
            <!---------Right Sidebar End----------->                                                           
 <?php 
 endwhile;
 get_footer(); ?>