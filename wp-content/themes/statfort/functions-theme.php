<?php
// video short code
if ( ! function_exists( 'cs_video_page' ) ) {
	function cs_video_page(){
		global $cs_node;
		$html = '<div class="element_size_'.$cs_node->video_element_size.'">';
			$html .= wp_oembed_get( $cs_node->video_url, array('width'=>$cs_node->video_width, 'height'=>$cs_node->video_height) );
		$html .= '</div>';
		return $html;
	}
}
// image short code
if ( ! function_exists( 'cs_image_page' ) ) {
	function cs_image_page(){
		global $cs_node;
 
		$href = '';
		$html = '';
		if ($cs_node->image_lightbox == "yes") $href = $cs_node->image_source;
		if($cs_node->image_lightbox =="yes") $data_rel = 'data-rel="prettyPhoto"';
			else $data_rel = 'target="_blank"';
		
		if ( $cs_node->image_element_size <> "" ) { $html .= '<div class="element_size_'.$cs_node->image_element_size.'">'; }
			$html .= '<figure class="lightbox-single image-shortcode" style="float:left; width:'.$cs_node->image_width.'px; height:'.$cs_node->image_height.'px">';
				if ($cs_node->image_lightbox == "yes"){
				$html .= '<a class="'.$cs_node->image_style.'" href="'.$href.'" title="'.$cs_node->image_caption.'" '.$data_rel.'>';
				}
					$html .= '<img src="'.$cs_node->image_source.'" style="float:left; width:'.$cs_node->image_width.'px; height:'.$cs_node->image_height.'px" alt="" />';
				if ($cs_node->image_lightbox == "yes"){
				$html .= '</a>';
				}
				$html .= '<figcaption class="webkit">';
					$html .= '<h6>'.$cs_node->image_caption.'</h6>';
				$html .= '</figcaption>';
			$html .= '</figure>';
		if ( $cs_node->image_element_size <> "" ) { $html .= '</div>'; }
		return $html;
	}
}
// Divider shortcode use for sepratiion of page elements
if ( ! function_exists( 'cs_divider_page' ) ) { 
	function cs_divider_page(){
		global $cs_node;
		wp_enqueue_script('scrolltopcontrol_js', get_template_directory_uri() . '/scripts/frontend/scrolltopcontrol.js', '', '', true);
		$html = '<div class="devider element_size_'.$cs_node->divider_element_size.'>">';
		if($cs_node->divider_style <> "divider2"){
			$html .= '<div style="margin-top:'.$cs_node->divider_mrg_top.'px;margin-bottom:'.$cs_node->divider_mrg_bottom.'px; " class="' . $cs_node->divider_style . '">';
			if(isset($cs_node->divider_backtotop) && strtolower($cs_node->divider_backtotop)=='yes'){
				$html .= '<a href="#" class="gotop" id="back-top">'.__('Top','Statfort').'</a>';
			}
		}
		if($cs_node->divider_style == "divider2"){
			$html .= '<div style="margin-top:'.$cs_node->divider_mrg_top.'px;margin-bottom:'.$cs_node->divider_mrg_bottom.'px; " class="heading-seprator"><span class="heading-pattren"></span>';
			if(isset($cs_node->divider_backtotop) && strtolower($cs_node->divider_backtotop)=='yes'){
				$html .= '<a href="#" class="gotop" id="back-top">'.__('Top','Statfort').'</a>';
			}
		}

		$html .= '</div>';
		$html .= '</div>';
		return $html . '<div class="clear"></div>';
	}
}

// Column shortcode with 2/3/4 column option even you can use shortcode in column shortcode
if ( ! function_exists( 'cs_column_page' ) ) {
	function cs_column_page(){
		global $cs_node;
		$html = '<div class="element_size_'.$cs_node->column_element_size.' column">';
			$html .= do_shortcode($cs_node->column_text);
		$html .= '</div>';
		echo $html;
	}
}

// tabs shortcode
if ( ! function_exists( 'cs_tabs_page' ) ) {
	function cs_tabs_page(){
		global $cs_node, $tab_counter;
		
		$html = "";
		
		$tabs_content = htmlspecialchars($cs_node->tabs_content);
		
		$tabs_content = html_entity_decode($tabs_content);
		
		$tabs_content = str_replace(array('quot;', 'amp;#8221;', 'amp;#8243;', 'lt;', 'gt;'), array('"', '"', '"', '<', '>'), $tabs_content);
				
		if ( $cs_node->tabs_element_size == "" ) {
			$html .= '<ul class="nav nav-tabs" id="myTab">';
			$cs_xmlObject = simplexml_load_string($tabs_content);
			$tabs_count = 0;
						
			foreach ($cs_xmlObject as $val) {
				if (!isset($val["icon"])){ $val["icon"] = '';}
				if (!isset($val["title"])){ $val["title"] = '';}
				$tabs_count++;
				if ( $val["active"] == "yes")
					$tab_active = " active";
				else
					$tab_active = "";
				$html .= '<li class="' . $tab_active . '"><a data-toggle="tab" href="#tab' . $tab_counter . $tabs_count . '"><i class="fa '.$val["icon"].'"></i> ' . $val["title"] . '</a></li>';
			}
			$html .= '</ul>';
			$html .= '<div class="tab-content">';
			$tabs_count = 0;
			foreach ($cs_xmlObject as $val) {
				$tabs_count++;
				if ( $val["active"] == "yes")
					$tab_active = " active";
				else
					$tab_active = "";
				$html .= '<div class="tab-pane fade in ' . $tab_active . '" id="tab' . $tab_counter . $tabs_count . '">' . $val . '</div>';
			}
			$html .= '</div>';
			$html = '<div class="tabs '.$cs_node->tabs_style.'">' . $html . '</div>';
		}
		return do_shortcode($html) . '<div class="clear"></div>';
	}
}
// Accrodian shortcode
if ( ! function_exists( 'cs_accordions_page' ) ) {
	function cs_accordions_page(){
		global $cs_node, $acc_counter;
		$acc_counter = rand(5, 15);
		$acc_counter++;
		$accordion_count = 0;
		
		$accordion_content = htmlspecialchars($cs_node->accordion_content);
		
		$accordion_content = html_entity_decode($accordion_content);
		
		$accordion_content = str_replace(array('quot;', 'amp;#8221;', 'amp;#8243;', 'lt;', 'gt;'), array('"', '"', '"', '<', '>'), $accordion_content);
				
		$html = "";
		if ( $cs_node->accordion_element_size == "" ) {
			$html .= '<div class="panel-group" id="accordion-' . $acc_counter . '">';
			$cs_xmlObject = new SimpleXMLElement($accordion_content);
			foreach ($cs_xmlObject as $cs_node) {
			if (!isset($cs_node["icon"])){ $cs_node["icon"] = '';}
			if (!isset($cs_node["title"])){ $cs_node["title"] = '';}
		
				$accordion_count++;
				if ($accordion_count == 1 && $cs_node["active"] == "yes")
						$class_active = " active";
					else
						$class_active = "";
						
				if ( $cs_node["active"] == "yes"){
					
 					$accordion_active = " in";
 
				}else{
					$accordion_active = "";
 
				}
				$html .= '<div class="panel panel-default"><div class="panel-heading">';
				$html .= '<i class="fa fa-question-sign fa fa-2"></i>';
				$html .= '<h4 class="panel-title">';
				$html .= '<a class="accordion-toggle backcolorhover '.$class_active .'" data-toggle="collapse" data-parent="#accordion-' . $acc_counter . '" href="#accordion-' . str_replace(" ", "", $accordion_count . $acc_counter) . '"><i class="fa '.$cs_node["icon"].'"></i> ' . $cs_node["title"] . '</a>';
				$html .= '</h4>';
				$html .= '</div>';
				$html .= '<div id="accordion-' . str_replace(" ", "", $accordion_count . $acc_counter) . '" class="accordion-body collapse ' . $accordion_active . '">';
				$html .= '<div class="panel-body"><p>' . $cs_node . '</p></div>';
				$html .= '</div>';
				$html .= '</div>';
			}
			$html .= '</div>';
		}
		return do_shortcode($html) . '<div class="clear"></div>';
	}
}
// Corlor Switcher for front end
function cs_color_switcher(){
	global $cs_theme_option;
 	if ( $cs_theme_option['color_switcher'] == "on" ) {

		if ( empty($_POST['patter_or_bg']) ){
			$_POST['patter_or_bg'] = '';
		}
		
		if ( empty($_POST['reset_color_txt']) ) { 
			$_POST['reset_color_txt'] = "";
		}
		else if ( $_POST['reset_color_txt'] == "1" ) {
			$_POST['layout_option'] = 'wrapper_boxed';
			$_POST['custome_pattern'] = "";
			$_POST['bg_img'] = "";
			$_POST['style_sheet'] = $cs_theme_option['custom_color_scheme'];
			$_POST['heading_color'] = $cs_theme_option['custom_color_scheme'];
 		}
		
		if ( $_POST['patter_or_bg'] == 0 ){
			$_SESSION['ar_sess_bg_img'] = '';
		}
		else if ( $_POST['patter_or_bg'] == 1 ){
			$_SESSION['ar_sess_custome_pattern'] = '';
		}
		
		if ( isset($_POST['layout_option']) ) {
			$_SESSION['ar_sess_layout_option'] = 'wrapper_boxed';
		}
		if ( isset($_POST['style_sheet']) ) {
			$_SESSION['ar_sess_style_sheet'] = $_POST['style_sheet'];
		}
		if ( isset($_POST['heading_color']) ) {
			$_SESSION['ar_sess_heading_color'] = $_POST['heading_color'];
		}
		if ( isset($_POST['custome_pattern']) ) {
			$_SESSION['ar_sess_custome_pattern'] = $_POST['custome_pattern'];
		}
		if ( isset($_POST['bg_img']) ) {
			$_SESSION['ar_sess_bg_img'] = $_POST['bg_img'];
		}

		//if ( empty($_SESSION['ar_sess_layout_option']) or $_POST['reset_color_txt'] == "1" ) { $_SESSION['ar_sess_layout_option'] = "wrapper"; }
		if ( empty($_SESSION['ar_sess_header_styles']) or $_POST['reset_color_txt'] == "1" ) { $_SESSION['ar_sess_header_styles'] = ""; }
		if ( empty($_SESSION['ar_sess_style_sheet']) or $_POST['reset_color_txt'] == "1" ) { $_SESSION['ar_sess_style_sheet'] = $cs_theme_option['custom_color_scheme']; }
		if ( empty($_SESSION['ar_sess_custome_pattern']) or $_POST['reset_color_txt'] == "1" ) { $_SESSION['ar_sess_custome_pattern'] = ""; }
		if ( empty($_SESSION['ar_sess_bg_img']) or $_POST['reset_color_txt'] == "1" ) { $_SESSION['ar_sess_bg_img'] = ""; }

		$theme_path = get_template_directory_uri();	
		wp_enqueue_style( 'wp-color-picker' );
		
		wp_enqueue_script('iris',admin_url( 'js/iris.min.js' ), array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),false, 1  );
		wp_enqueue_script('wp-color-picker',admin_url( 'js/color-picker.min.js' ),array( 'iris' ),false,1);
		$colorpicker_l10n = array(
			'clear' => 'Clear',
			'defaultString' => 'Default',
			'pick' => 'Select Color'
		);
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
?>

		<script type="text/javascript">
        jQuery(document) .ready(function($){
   			jQuery("#togglebutton").click(function(){
				jQuery("#sidebarmain").trigger('click')
				jQuery(this).toggleClass('btnclose');
				jQuery("#sidebarmain") .toggleClass('sidebarmain');
				return false; 
		   });
           $("#pattstyles li label") .click(function(){
			   var classname=$(".layoutoption li:first-child label") .hasClass("active"); 
				if(classname) { 
					alert("Please select Boxed View")
					return false; 
					
				} else {
					$("#backgroundimages li label") .removeClass("active");	
					$("#patter_or_bg") .attr("value","0");
					var ah = $(this) .find('input[type="radio"]') .val();
					$('body') .css({"background":"url(<?php echo $theme_path?>/images/pattern/pattern"+ah+".png)"});
				}
      });
      $("#backgroundimages li label") .click(function(){
		 var classname=$(".layoutoption li:first-child label") .hasClass("active"); 
			if(classname) { 
				alert("Please select Boxed View")
				return false; 
				
			} else {
				$("#patter_or_bg") .attr("value","1");
				$("#pattstyles li label") .removeClass("active");	
				var ah = $(this) .find('input[type="radio"]') .val();
				$('body') .css({"background":"url(<?php echo $theme_path?>/images/background/bg"+ah+".png) no-repeat center center / cover fixed"});
			}
	  
     });
   $("#backgroundimages li label,#pattstyles li label") .click(function(){
		var classname=$(".layoutoption li:first-child label") .hasClass("active"); 
		if(classname) {
			//alert("Please select Boxed View")
			return false; 
		}else {
		  $(this) .parents(".selectradio") .find("label") .removeClass("active");
		  $(this) .addClass("active");
	
		 }
    });
                $(".layoutoption li label") .click(function(){
					jQuery(".header-section").scrollToFixed();
    var th = $(this).find('input') .val();
    $("#wrappermain-pix") .attr('class','');
    $('#wrappermain-pix') .addClass(th);
                $(this) .parents(".selectradio") .find("label") .removeClass("active");
                $(this) .addClass("active");
     		jQuery(".top_strip").trigger('resize');
				para();
	 			parabg ();
                });
    
    $(".accordion-sidepanel .innertext") .hide();
    $(".accordion-sidepanel header") .click(function(){
     if ($(this) .next() .is(":visible")){
       $(".accordion-sidepanel .innertext") .slideUp(300);
       $(".accordion-sidepanel header") .removeClass("active");
       return false;
      }
    $(".accordion-sidepanel .innertext") .slideUp(300);
    $(".accordion-sidepanel header") .removeClass("active");
    $(this) .addClass("active");
    $(this).next() .slideDown(300);
     
    
    });
    
        });

	jQuery(document).ready(function($){
		$(".colorpicker-main").click(function(){
		$(this).find('.wp-color-result').trigger('click'); 
    });
	<!-- Color-->
	var cf = '.colr,#respond form p input[type="text"]:focus ~ .fa,div.woocommerce a:hover,.colrhvr:hover,nav.navigation ul > li:hover > a,.post-options li a:hover,.pagination > ul > .active > a,.pagination > ul > .active > a ,.services article:hover figure em ,.services article:hover h5,.widget_links ul li:hover a:before,.widget_links ul li:hover a /**/, .pagination ul li a:hover,.current-menu-item > a,blockquote:before,.flexslider figcaption h2 a'; 
	<!-- Background Color-->
var bc =".bgcolr,.bgcolrhvr:hover,.pagination > ul > .active > a:before,.widget_archive ul li:hover,.prevnext-post a:hover:before,.eventlisting article:hover figure,.address-info li:hover em.fa,.navigation ul ul a:hover,.wrapper:before,.wrapper:after, #respond form p button, .btnshare:hover,  .sortby li a:hover, .pagination ul li a:before, .address-info li:hover i,.tabs.horizontal .nav-tabs .active a:before,.protected-icon,.password_protected form input[type='submit'],.gallerysec ul li figure figcaption i,#undercontruction .countdownit span.countdown_section,.dropcap:first-letter, .dropcap p:first-letter, .dropcaptwo:first-letter,#wp-calendar caption,#respond form p input[type='submit'],#filter-list ul li a:hover,.onsale,.add_to_cart_button.button:hover,.woocommerce-pagination ul li span,.woocommerce-pagination ul li a:hover,.woocommerce-message:before,.woocommerce-error:before,.woocommerce-info:before,div.woocommerce .button:hover,.wpcf7 form p input[type='submit'],.widget_gallery ul li a:after,.widget_pages ul li a:hover,.widget_archive ul li:hover,.widget_recent_entries ul li:hover,.widget_recent_entries ul li:hover,.widget_recent_comments ul li:hover,.widget_links ul li:hover,.widget_meta ul li:hover,.widget_archive ul li:hover a,.widget_recent_entries ul li:hover a,.widget_recent_entries ul li:hover a,.widget_recent_comments ul li:hover a,.widget_links ul li:hover a,.widget_meta ul li:hover a,.widget_nav_menu ul li a:hover,#footer-widgets .widget_pages ul li a:hover,.widget_tag_cloud .tagcloud a:hover,.widget_categories ul li:hover,.event.eventlisting article .text .event-texttop ul li a:hover, .blog.blog-large article:hover .calendar-date,nav.navigation ul ul > li:hover > a,.blog .mejs-audio.mejs-container .mejs-controls,.blog article.cls-post-video figure figcaption a";
	<!-- Border Color-->
	var boc =".bdrcolr,.address-info li:hover em.fa ,.services article:hover figure em,#respond form p input:focus, #respond form p textarea:focus,.woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit,.woocommerce #content input.button,.woocommerce-page a.button,.woocommerce-page button.button,.woocommerce-page input.button,.woocommerce-page #respond input#submit,.woocommerce-page #content input.button,.woocommerce-info,.woocommerce-message, .woocommerce-error";
	<!-- Border Transparent Color-->
	var boc2 =".blockquote blockquote:before";
 	jQuery("#colorpickerwrapp span.col-box") .live("click",function(event) {
			//alert('test');
			var a = jQuery(this).data('color');
			//alert(a);
			jQuery("#bgcolor").val(a);
			jQuery('.wp-color-result').css('background-color', a);
			$("#stylecss") .remove();
			$("<style type='text/css' id='stylecss'>"+cf+"{color:"+a+" !important}"+bc+"{background-color:"+a+" !important}"+boc+"{border-color:"+a+" !important}"+boc2+"{border-color:transparent "+a+" !important}</style>").insertAfter("#wrappermain-pix");
			
			
			
			jQuery("#colorpickerwrapp span.col-box") .removeClass('active');
			jQuery(this).addClass("active");
		});
	jQuery('#themecolor .bg_color').wpColorPicker({
		change:function(event,ui){
			var a = ui.color.toString();
			$("#stylecss") .remove();
			$("<style type='text/css' id='stylecss'>"+cf+"{color:"+a+" !important}"+bc+"{background-color:"+a+" !important}"+boc+"{border-color:"+a+" !important}"+boc2+"{border-color:transparent "+a+" !important}</style>").insertAfter("#wrappermain-pix");
			} 
    	}); 
 	});
	
	
	function reset_color(){
		jQuery("#reset_color_txt").attr('value',"1")
		jQuery("#bgcolor").attr('value',"<?php echo $cs_theme_option['custom_color_scheme'];?>")
		jQuery("#color_switcher").submit();
	}
        </script>
        <div id="sidebarmain">
            <span id="togglebutton">&nbsp;</span>
            <div id="sidebar">
                <form method="post" id="color_switcher" action="">
                	<aside class="rowside">
      					<header><h4>Layout options</h4></header>
						
                        <label>Select Color Scheme</label>
                        <div id="colorpickerwrapp">
                            <?php $cs_color_array= array('#45b363','#339a74', '#1d7f5b', '#3fb0c3', '#2293a6', '#137d8f', '#9374ae', '#775b8f', '#dca13a', '#c46d32', '#c44732', '#c44d55', '#425660', '#292f32');
                            foreach($cs_color_array as $colors){
                                $active = '';
                                if($colors == $cs_theme_option['custom_color_scheme']){$active = 'active';}
                                echo '<span class="col-box '.$active.'" data-color="'.$colors.'" style="background: '.$colors.'"></span>';
                            }
                            ?>
                        </div>
                        
                        <label for="bgcolor" id="themecolor" class="colorpicker-main">
                        <img src="<?php echo $theme_path?>/images/admin/img-colorpan.png" alt="">
                        <h5>Theme Color</h5>
                        <input id="bgcolor" name="style_sheet" type="text" class="bg_color" value="<?php echo $_SESSION['ar_sess_style_sheet'];?>" /></label>
                        
                    </aside>
                    <div class="accordion-sidepanel">
                    <aside class="rowside">
                      <header>  <h4>Pattren Styles</h4></header>
                      <div class="innertext">
                      
                        <div id="pattstyles" class="itemstyles selectradio">
                            <ul>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="1")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern1.png" alt=""><input type="radio" name="custome_pattern" value="1"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="2")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern2.png" alt=""><input type="radio" name="custome_pattern" value="2"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="3")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern3.png" alt=""><input type="radio" name="custome_pattern" value="3"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="4")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern4.png" alt=""><input type="radio" name="custome_pattern" value="4"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="5")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern5.png" alt=""><input type="radio" name="custome_pattern" value="5"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="6")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern6.png" alt=""><input type="radio" name="custome_pattern" value="6"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="7")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern7.png" alt=""><input type="radio" name="custome_pattern" value="7"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="8")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern8.png" alt=""><input type="radio" name="custome_pattern" value="8"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="9")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern9.png" alt=""><input type="radio" name="custome_pattern" value="9"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="10")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern10.png" alt=""><input type="radio" name="custome_pattern" value="10"></label></li>
                                 <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="11")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern11.png" alt=""><input type="radio" name="custome_pattern" value="11"></label></li>
                                  <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="12")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern12.png" alt=""><input type="radio" name="custome_pattern" value="12"></label></li>
                                    <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="13")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern13.png" alt=""><input type="radio" name="custome_pattern" value="13"></label></li>
                                      <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="14")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern14.png" alt=""><input type="radio" name="custome_pattern" value="14"></label></li>
                                        <li><label <?php if($_SESSION['ar_sess_custome_pattern']=="15")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/pattern/pattern15.png" alt=""><input type="radio" name="custome_pattern" value="15"></label></li>
                               
                            </ul>
                        </div>
                        </div>
                    </aside>
                    <aside class="rowside">
                        <header><h4>Background Images</h4></header>
                        <div class="innertext">
                      
                        <div id="backgroundimages" class="selectradio">
                            <ul>
                            	<li><label <?php if($_SESSION['ar_sess_bg_img']=="1")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background1.png" alt=""><input type="radio" name="bg_img" value="1"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_bg_img']=="2")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background2.png" alt=""><input type="radio" name="bg_img" value="2"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_bg_img']=="3")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background3.png" alt=""><input type="radio" name="bg_img" value="3"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_bg_img']=="4")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background4.png" alt=""><input type="radio" name="bg_img" value="4"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_bg_img']=="5")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background5.png" alt=""><input type="radio" name="bg_img" value="5"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_bg_img']=="6")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background6.png" alt=""><input type="radio" name="bg_img" value="6"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_bg_img']=="7")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background7.png" alt=""><input type="radio" name="bg_img" value="7"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_bg_img']=="8")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background8.png" alt=""><input type="radio" name="bg_img" value="8"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_bg_img']=="9")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background9.png" alt=""><input type="radio" name="bg_img" value="9"></label></li>
                                <li><label <?php if($_SESSION['ar_sess_bg_img']=="10")echo "class='active'";?> ><img src="<?php echo $theme_path?>/images/background/background10.png" alt=""><input type="radio" name="bg_img" value="10"></label></li>
                               
                            </ul>
                        </div>
                        </div>
                    </aside>
                    </div>
                	<div class="buttonarea">
                    	<input type="submit" value="Apply" class="btn" />
                        <input type="hidden" name="patter_or_bg" id="patter_or_bg" value="1" />
                        <input type="hidden" name="reset_color_txt" id="reset_color_txt" value="" />
                    	<input type="reset" value="Reset" class="btn" onclick="javascript:reset_color()" />
                    </div>
            </form>
            </div>
        </div>
<?php
	}
}
function cs_custom_styles() {
	global $cs_theme_option;
 	if ( isset($_POST['style_sheet']) ) {
		$_SESSION['ar_sess_style_sheet'] = $_POST['style_sheet'];
		$cs_color_scheme = $_SESSION['ar_sess_style_sheet'];
	}
	elseif (isset($_SESSION['ar_sess_style_sheet']) and $_SESSION['ar_sess_style_sheet'] <> '') {
		$cs_color_scheme = $_SESSION['ar_sess_style_sheet'];
	}
	else{
		$cs_color_scheme = $cs_theme_option['custom_color_scheme'];
		
	}
if(!isset($cs_color_scheme)) $cs_color_scheme ='#409f74';
 ?>
	<style type="text/css" >
/* -- Theme Color -- */
.colr,#respond form p input[type="text"]:focus ~ .fa,div.woocommerce a:hover,.colrhvr:hover,nav.navigation ul > li:hover > a,.post-options li a:hover,.pagination > ul > .active > a,.pagination > ul > .active > a ,.services article:hover figure em ,.services article:hover h5,.widget_links ul li:hover a:before,.widget_links ul li:hover a /**/, .pagination ul li a:hover ,.current-menu-item > a,.current-menu-parent > a,.pagination ul li a.active,blockquote:before,.flexslider figcaption h2 a {
	color:<?php echo $cs_color_scheme; ?> !important;
}
.bgcolr,.bgcolrhvr:hover,.pagination > ul > .active > a:before,.widget_archive ul li:hover,.prevnext-post a:hover:before,.eventlisting article:hover figure,.address-info li:hover em.fa,.navigation ul ul a:hover,.wrapper:before,.wrapper:after /**/, #respond form p button, .btnshare:hover,  .sortby li a:hover, .pagination ul li a:before, .address-info li:hover i,.tabs.horizontal .nav-tabs .active a:before,
.protected-icon,.password_protected form input[type="submit"],.gallerysec ul li figure figcaption i,#undercontruction .countdownit span.countdown_section,.dropcap:first-letter, .dropcap p:first-letter, .dropcaptwo:first-letter,#wp-calendar caption,#respond form p input[type="submit"],
#filter-list ul li a:hover,.onsale,.add_to_cart_button.button:hover,.woocommerce-pagination ul li span,.woocommerce-pagination ul li a:hover,.woocommerce-message:before,.woocommerce-error:before,.woocommerce-info:before,div.woocommerce .button:hover,.wpcf7 form p input[type="submit"],.widget_gallery ul li a:after,
.widget_pages ul li a:hover,.widget_archive ul li:hover,.widget_recent_entries ul li:hover,.widget_recent_entries ul li:hover,.widget_recent_comments ul li:hover,.widget_links ul li:hover,.widget_meta ul li:hover,.widget_archive ul li:hover a,.widget_recent_entries ul li:hover a,.widget_recent_entries ul li:hover a,.widget_recent_comments ul li:hover a,.widget_links ul li:hover a,.widget_meta ul li:hover a,.widget_nav_menu ul li a:hover,#footer-widgets .widget_pages ul li a:hover,.widget_tag_cloud .tagcloud a:hover,.widget_categories ul li:hover,.event.eventlisting article .text .event-texttop ul li a:hover, .blog.blog-large article:hover .calendar-date,nav.navigation  ul ul  li.current-menu-item:hover >  a,nav.navigation ul ul > li:hover > a,.blog .mejs-audio.mejs-container .mejs-controls,.blog article.cls-post-video figure figcaption a{
	background-color:<?php echo $cs_color_scheme; ?> !important;
}
.bdrcolr,.address-info li:hover em.fa ,.services article:hover figure em, /**/ #respond form p input:focus, #respond form p textarea:focus,
.woocommerce a.button, .woocommerce button.button, .woocommerce input.button, .woocommerce #respond input#submit,
.woocommerce #content input.button,.woocommerce-page a.button,.woocommerce-page button.button,.woocommerce-page input.button,.woocommerce-page #respond input#submit,.woocommerce-page #content input.button,.woocommerce-info,.woocommerce-message, .woocommerce-error{
	border-color:<?php echo $cs_color_scheme; ?> !important;
}
.blockquote blockquote:before {
	border-color: transparent <?php echo $cs_color_scheme; ?> !important;
}
.contactus #respond .right-col p.form-submit button:hover:before{
	border-color: transparent transparent  <?php echo $cs_color_scheme; ?> transparent  !important;
}
.widget_countdown figure figcaption .cs-post-title a,.widget-latest-news article h5 a:hover  {
	box-shadow: -10px 0 0 0 #409f74,10px 0 0 0 <?php echo $cs_color_scheme; ?>;
}
</style>
<?php 
}
/*
 * Ccustom Header Styles 
 */
if ( ! function_exists( 'cs_get_header' ) ) { 
	function cs_get_header() {
	
		global $post, $cs_theme_option;
	
	?>
	
	<!-- Header Start -->
	
		<header id="header" class="headermain fullwidth">
	  
			<!-- Main Header -->
	
			<div id="mainheader" class="fullwidth">
            
          
	
				<div class="container">
	
					<?php 
	
						if(isset($cs_theme_option['header_logo']) and $cs_theme_option['header_logo'] == 'on'){
	
							//<!-- Logo Section -->
	
							echo '<div id="logo" class="float-left">';
	
								cs_logo();
	
							echo '</div>';
	
							//<!-- Logo Section Close -->
	
						}else{
							echo '<div id="logo" class="float-left">';	
								cs_logo();	
							echo '</div>';	
						}
	
					?>
					<!-- Right Header -->
	
					<div id="rightheader" class="flaot-right">
						<?php 
							if(isset($cs_theme_option['header_cart']) and $cs_theme_option['header_cart'] == 'on'){ cs_woocommerce_header_cart(); } 
						?>
						<?php if(isset($cs_theme_option['header_search']) and $cs_theme_option['header_search'] == 'on'){ ?>
						
						<!-- SearcH Area -->
			
						<form action="<?php echo home_url() ?>" id="searchform" method="get" role="search">
	
						<div class="searcharea float-right">
	
							<a href="#searchbox" class="btnsearch"><em class="fa fa-search"></em></a>
	
							<div id="searchbox">
	
								<input type="text" name="s" value="<?php _e('Search for:', "Statfort"); ?>"><button type="submit" class="bgcolr"><em class="fa fa-search"></em></button>
	
							</div>
	
						</div>
	
						</form>
	
						<!-- SearcH Area Close-->
						
						<?php } ?>
						
						<!-- Navigation  -->
	
						<nav class="navigation float-right">
	
							<?php cs_navigation('main-menu'); ?>
	
						</nav>
	
						<!-- Navigation Close -->
	
						
	
					</div>
	
					<!-- Right Header Close -->
	
	
	
				</div>
	
			</div>
	
			<!-- Main Header Close -->
	
		</header>
	
		<!-- Header Close -->
	
	<?php
	}
}

// Custom excerpt function 
if ( ! function_exists( 'cs_get_the_excerpt' ) ) { 
	function cs_get_the_excerpt($limit,$readmore = '') {
	
		global $cs_theme_option;
	
		$get_the_excerpt = trim(preg_replace('/<a[^>]*>(.*)<\/a>/iU', '', get_the_excerpt()));
	
		echo substr($get_the_excerpt, 0, "$limit");
	
		if (strlen($get_the_excerpt) > "$limit") {
	
			if($readmore == "true"){
	
				echo '... <a href="' . get_permalink() . '" class="cs-read-more colr">' .$cs_theme_option['trans_read_more'] . '</a>';
	
			}
		}
	}
}

// Flexslider function


if ( ! function_exists( 'cs_flex_slider' ) ) {
	function cs_flex_slider($width,$height,$slider_id){
		global $cs_node,$cs_theme_option,$cs_counter_node;
		$cs_counter_node++;
		if($slider_id == ''){
			$slider_id = $cs_node->slider;
		}
		if($cs_theme_option['flex_auto_play'] == 'on'){$auto_play = 'true';}
			else if($cs_theme_option['flex_auto_play'] == ''){$auto_play = 'false';}
			$cs_meta_slider_options = get_post_meta("$slider_id", "cs_meta_slider_options", true); 
		?>
		<!-- Flex Slider -->
		<div id="flexslider<?php echo $cs_counter_node; ?>">
		  <div class="flexslider">
			  <ul class="slides">
				<?php 
					$cs_counter = 1;
					$cs_xmlObject_flex = new SimpleXMLElement($cs_meta_slider_options);
					foreach ( $cs_xmlObject_flex->children() as $as_node ){
						
 						$image_url = cs_attachment_image_src($as_node->path,$width,$height); 
						?>
                        <li>
                            <figure>
                                <img src="<?php echo $image_url ?>" alt="">   
                                <?php 
								if($as_node->title != '' && $as_node->description != '' || $as_node->title != '' || $as_node->description != ''){ 
								?>         
                                <figcaption>
                                	<div class="container">
                                     	<h2 class="colr">
											<?php 
												if($as_node->link <> ''){ 
			
													 echo '<a href="'.$as_node->link.'" target="'.$as_node->link_target.'">' . $as_node->title . '</a>';
			
												} else {
			
													echo $as_node->title;
			
												}?>
											</h2>
                                             <p>
        
                                                <?php
        
                                                    echo substr($as_node->description, 0, 220);
        
                                                    if ( strlen($as_node->description) > 220 ) echo "...";
        
                                                ?>
        
                                            </p>
									</div>
                                </figcaption>
                              <?php }?>
                            </figure>
        
                        </li>
					<?php 
					$cs_counter++;
					}
				?>
			  </ul>
		  </div>
		</div>
		<?php cs_enqueue_flexslider_script(); ?>
		<!-- Slider height and width -->
		<!-- Flex Slider Javascript Files -->
		<script type="text/javascript">
			jQuery(window).load(function(){
				var speed = <?php echo $cs_theme_option['flex_animation_speed']; ?>; 
				var slidespeed = <?php echo $cs_theme_option['flex_pause_time']; ?>;
				jQuery('#flexslider<?php echo $cs_counter_node; ?> .flexslider').flexslider({
					animation: "<?php echo $cs_theme_option['flex_effect']; ?>", // fade
					slideshow: <?php echo $auto_play;?>,
					slideshowSpeed:speed,
					animationSpeed:slidespeed,
					prevText:"<em class='fa fa-long-arrow-up'></em>",
					nextText:"<em class='fa fa-long-arrow-down'></em>",
					start: function(slider) {
						jQuery('.flexslider').fadeIn();
					}
 
				});
  
			});
		</script>
	<?php
	}
}

// Get post meta in xml form
function cs_meta_page($meta) {
    global $cs_meta_page;
    $meta = get_post_meta(get_the_ID(), $meta, true);
    if ($meta <> '') {
        $cs_meta_page = new SimpleXMLElement($meta);
        return $cs_meta_page;
    }
}

function cs_meta_shop_page($meta, $id) {
    global $cs_meta_page;
    $meta = get_post_meta($id, $meta, true);
    if ($meta <> '') {
        $cs_meta_page = new SimpleXMLElement($meta);
        return $cs_meta_page;
    }
}

// pages sidebar
if ( ! function_exists( 'cs_meta_sidebar' ) ) { 
	function cs_meta_sidebar(){
		global $cs_meta_page;
		if ( $cs_meta_page->sidebar_layout->cs_layout <> '' and $cs_meta_page->sidebar_layout->cs_layout == 'right') {
			 echo "<aside class='sidebar-right span3'><div class='column'>";
			if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_meta_page->sidebar_layout->cs_sidebar_right) ) : endif;
			echo "</div></aside>";
		}
		else if ( $cs_meta_page->sidebar_layout->cs_layout <> '' and $cs_meta_page->sidebar_layout->cs_layout == 'left'  ) {
			echo "<aside class='sidebar-left span3'><div class='column'>";
			if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_meta_page->sidebar_layout->cs_sidebar_left) ) : endif;
			echo "</div></aside>";
		}
	}
}
// content class
if ( ! function_exists( 'cs_meta_content_class' ) ) {
	function cs_meta_content_class(){
		global $cs_meta_page,$cs_video_width;
		if ( $cs_meta_page->sidebar_layout->cs_layout == '' or $cs_meta_page->sidebar_layout->cs_layout == 'none' ) {
			$content_class = "col-md-12";
			$cs_video_width = 1170;
		}
		else if ( $cs_meta_page->sidebar_layout->cs_layout <> '' and $cs_meta_page->sidebar_layout->cs_layout == 'right' ) {
			$content_class = "col-md-9";
			$cs_video_width = 870;
		}
		else if ( $cs_meta_page->sidebar_layout->cs_layout <> '' and $cs_meta_page->sidebar_layout->cs_layout == 'left' ) {
			$content_class = "col-md-9";
			$cs_video_width = 870;
		}
		else if ( $cs_meta_page->sidebar_layout->cs_layout <> '' and ($cs_meta_page->sidebar_layout->cs_layout == 'both' or $cs_meta_page->sidebar_layout->cs_layout == 'both_left' or $cs_meta_page->sidebar_layout->cs_layout == 'both_right')) {
			$content_class = "col-md-6";
			$cs_video_width = 570;
		}else{
			$content_class = "col-md-12";
		}
		return $content_class;
	}
}
// sidebar class
if ( ! function_exists( 'cs_meta_sidebar_class' ) ) {
	function cs_meta_sidebar_class(){
		global $cs_meta_page;
		if ( $cs_meta_page->sidebar_layout->cs_layout <> '' and $cs_meta_page->sidebar_layout->cs_layout == 'right' ) {
			echo "sidebar-right col-md-3";
		}
		else if ( $cs_meta_page->sidebar_layout->cs_layout <> '' and $cs_meta_page->sidebar_layout->cs_layout == 'left' ) {
			echo "sidebar-left col-md-3";
		}
	}
}
// Content pages Meta Class
if ( ! function_exists( 'cs_default_pages_meta_content_class' ) ) { 
	function cs_default_pages_meta_content_class($layout){
		if ( $layout == '' or $layout == 'none' ) {
			echo "span12";
		}
		else if ( $layout <> '' and $layout == 'right' ) {
			echo "content-left col-md-9";
		}
		else if ( $layout <> '' and $layout == 'left' ) {
			echo "content-right col-md-9";
		}
		else if ( $layout <> '' and $layout == 'both' ) {
			echo "content-right col-md-6";
		}
	}	
}
// Default pages sidebar class
if ( ! function_exists( 'cs_default_pages_sidebar_class' ) ) { 
	function cs_default_pages_sidebar_class($layout){
		if ( $layout <> '' and $layout == 'right' ) {
			echo "sidebar-right col-md-3";
		}
		else if ( $layout <> '' and $layout == 'left' ) {
			echo "sidebar-left col-md-3";
		}
	}
}
// Default page sidebar
function cs_default_pages_sidebar(){
	global $cs_theme_option;
  	if ( $cs_theme_option['cs_layout'] <> '' and $cs_theme_option['cs_layout'] == 'right' ) {
		if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_theme_option['cs_sidebar_right']) ) : endif;
	}
	else if ( $cs_theme_option['cs_layout'] <> '' and $cs_theme_option['cs_layout'] == 'left' ) {
		if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar($cs_theme_option['cs_sidebar_left']) ) : endif;
	}
 }
// custom pagination start
if ( ! function_exists( 'cs_pagination' ) ) {
	function cs_pagination($total_records, $per_page, $qrystr = '') {
		$html = '';
		$dot_pre = '';
		$dot_more = '';
		$total_page = ceil($total_records / $per_page);
		$loop_start = $_GET['page_id_all'] - 2;
		$loop_end = $_GET['page_id_all'] + 2;
		if ($_GET['page_id_all'] < 3) {
			$loop_start = 1;
			if ($total_page < 5)
				$loop_end = $total_page;
			else
				$loop_end = 5;
		}
		else if ($_GET['page_id_all'] >= $total_page - 1) {
			if ($total_page < 5)
				$loop_start = 1;
			else
				$loop_start = $total_page - 4;
			$loop_end = $total_page;
		}
		$html .= "<nav class='pagination'><ul>";
		if ($_GET['page_id_all'] > 1)
			$html .= "<li class='prev'><a class='fa fa-angle-left fa-2' href='?page_id_all=" . ($_GET['page_id_all'] - 1) . "$qrystr' >".__('&laquo; Previous', 'Statfort')."</a></li>";
		if ($_GET['page_id_all'] > 3 and $total_page > 5)
			$html .= "<li><a href='?page_id_all=1$qrystr'>1</a></li>";
		if ($_GET['page_id_all'] > 4 and $total_page > 6)
			$html .= "<li> <a>. . .</a> </li>";
		if ($total_page > 1) {
			for ($i = $loop_start; $i <= $loop_end; $i++) {
				if ($i <> $_GET['page_id_all'])
					$html .= "<li><a href='?page_id_all=$i$qrystr'>" . $i . "</a></li>";
				else
					$html .= "<li><a class='active'>" . $i . "</a></li>";
			}
		}
		if ($loop_end <> $total_page and $loop_end <> $total_page - 1)
			$html .= "<li> <a>. . .</a> </li>";
		if ($loop_end <> $total_page)
			$html .= "<li><a href='?page_id_all=$total_page$qrystr'>$total_page</a></li>";
		if ($_GET['page_id_all'] < $total_records / $per_page)
			$html .= "<li class='next'><a class='fa fa-angle-right icon' href='?page_id_all=" . ($_GET['page_id_all'] + 1) . "$qrystr' >".__('Next &raquo;', 'Statfort')."</a></li>";
		$html .= "</ul></nav>";
		return $html;
	}
}
// pagination end

// Social network
if ( ! function_exists( 'cs_social_network' ) ) {
	function cs_social_network($icon_type='',$tooltip = ''){
		global $cs_theme_option;
		global $cs_theme_option;
		$tooltip_data='';
		if($icon_type=='large'){
			$icon = 'fa fa-2x';
		} else {
			$icon = 'icon';
		}
			if(isset($tooltip) && $tooltip <> ''){
				$tooltip_data='data-placement-tooltip="tooltip"';
			}
		if ( isset($cs_theme_option['social_net_url']) and count($cs_theme_option['social_net_url']) > 0 ) {
						$i = 0;
						foreach ( $cs_theme_option['social_net_url'] as $val ){
							?>
					<?php if($val != ''){?><a title="<?php echo $cs_theme_option['social_net_tooltip'][$i];?>" href="<?php echo $val;?>" data-original-title="<?php echo $cs_theme_option['social_net_tooltip'][$i];?>" data-placement="top" <?php echo $tooltip_data;?> class="colrhover"  target="_blank"><?php if($cs_theme_option['social_net_awesome'][$i] <> '' && isset($cs_theme_option['social_net_awesome'][$i])){?> 
                    <span class="fa-stack fa-lg">
                        <em class="fa fa-square fa-stack-2x"></em>
                        <em class="<?php echo $cs_theme_option['social_net_awesome'][$i];?> <?php echo $icon;?> fa-stack-1x fa-inverse fa"></em>
                    </span>
					
					<?php } else {?><img src="<?php echo $cs_theme_option['social_net_icon_path'][$i];?>" alt="<?php echo $cs_theme_option['social_net_tooltip'][$i];?>" /><?php }?></a><?php }
							
						$i++;}
		}
		
	}
}
if ( ! function_exists( 'cs_social_network_widget' ) ) {
	function cs_social_network_widget($icon_type='',$tooltip = ''){
		global $cs_theme_option;
		global $cs_theme_option;
		$tooltip_data='';
		if($icon_type=='large'){
			$icon = 'fa fa-2x';
		} else {
			$icon = '';
		}
			if(isset($tooltip) && $tooltip <> ''){
				$tooltip_data='data-placement-tooltip="tooltip"';
			}
		if ( isset($cs_theme_option['social_net_url']) and count($cs_theme_option['social_net_url']) > 0 ) {
						$i = 0;
						foreach ( $cs_theme_option['social_net_url'] as $val ){
							?>
					<?php if($val != ''){?><a title="" href="<?php echo $val;?>" data-original-title="<?php echo $cs_theme_option['social_net_tooltip'][$i];?>" data-placement="top" <?php echo $tooltip_data;?> target="_blank"><?php if($cs_theme_option['social_net_awesome'][$i] <> '' && isset($cs_theme_option['social_net_awesome'][$i])){?> 

                        <em class="fa <?php echo $cs_theme_option['social_net_awesome'][$i];?>"></em>

					
					<?php } else {?><img src="<?php echo $cs_theme_option['social_net_icon_path'][$i];?>" alt="<?php echo $cs_theme_option['social_net_tooltip'][$i];?>" /><?php }?><?php echo $cs_theme_option['social_net_tooltip'][$i];?> </a><?php }
							
						$i++;}
		}
		
	}
}
// Post image attachment function
function cs_attachment_image_src($attachment_id, $width, $height) {
    $image_url = wp_get_attachment_image_src($attachment_id, array($width, $height), true);
     if ($image_url[1] == $width and $image_url[2] == $height)
        ;
    else
        $image_url = wp_get_attachment_image_src($attachment_id, "full", true);
    	$parts = explode('/uploads/',$image_url[0]);
		if ( count($parts) > 1 ) return $image_url[0];
}
// Post image attachment source function
function cs_get_post_img_src($post_id, $width, $height) {
    if(has_post_thumbnail()){
		$image_id = get_post_thumbnail_id($post_id);
		$image_url = wp_get_attachment_image_src($image_id, array($width, $height), true);
		if ($image_url[1] == $width and $image_url[2] == $height) {
			return $image_url[0];
		} else {
			$image_url = wp_get_attachment_image_src($image_id, "full", true);
			return $image_url[0];
		}
	}
}
// Get Post image attachment
function cs_get_post_img($post_id, $width, $height) {
    $image_id = get_post_thumbnail_id($post_id);
    $image_url = wp_get_attachment_image_src($image_id, array($width, $height), true);
    if ($image_url[1] == $width and $image_url[2] == $height) {
        return get_the_post_thumbnail($post_id, array($width, $height));
    } else {
        return get_the_post_thumbnail($post_id, "full");
    }
}
// Get Main background
function cs_bg_image(){
	global $cs_theme_option;
	$bg_img = '';
	if ( isset($_POST['bg_img']) ) {
		$_SESSION['ar_sess_bg_img'] = $_POST['bg_img'];
		$bg_img = get_template_directory_uri()."/images/background/bg".$_SESSION['ar_sess_bg_img'].".png";
	}
	else if ( isset($_SESSION['ar_sess_bg_img']) and !empty($_SESSION['ar_sess_bg_img'])){
		$bg_img = get_template_directory_uri()."/images/background/bg".$_SESSION['ar_sess_bg_img'].".png";
	}
	else {
		if (isset($cs_theme_option['bg_img_custom']) and $cs_theme_option['bg_img_custom'] == "" ) {
			if (isset($cs_theme_option['bg_img']) and $cs_theme_option['bg_img'] <> 0 ){
				$bg_img = get_template_directory_uri()."/images/background/bg".$cs_theme_option['bg_img'].".png";
			}
		}
		else { 
			$bg_img = $cs_theme_option['bg_img_custom'];
		}
	}
	if ( $bg_img <> "" ) {
		echo ' style="background:url('.$bg_img.') ' . $cs_theme_option['bg_repeat'] . ' top ' . $cs_theme_option['bg_position'] . ' ' . $cs_theme_option['bg_attach'].'"';
	}
}
// Main wrapper class function
function cs_wrapper_class(){
	global $cs_theme_option;
	if ( isset($_POST['layout_option']) ) {
		echo $_SESSION['ar_sess_layout_option'] = $_POST['layout_option'];
	}
	elseif ( isset($_SESSION['ar_sess_layout_option']) and !empty($_SESSION['ar_sess_layout_option'])){
		echo $_SESSION['ar_sess_layout_option'];
	}
	else {
		echo $cs_theme_option['layout_option'];
		$_SESSION['ar_sess_layout_option']='';
	}
}
// Get Background color Pattren
function cs_bgcolor_pattern(){
	global $cs_theme_option;
	// pattern start
	$pattern = '';
	$bg_color = '';
	if ( isset($_POST['custome_pattern']) ) {
		$_SESSION['ar_sess_custome_pattern'] = $_POST['custome_pattern'];
		$pattern = get_template_directory_uri()."/images/pattern/pattern".$_SESSION['ar_sess_custome_pattern'].".png";
	}
	else if ( isset($_SESSION['ar_sess_custome_pattern']) and !empty($_SESSION['ar_sess_custome_pattern'])){
		$pattern = get_template_directory_uri()."/images/pattern/pattern".$_SESSION['ar_sess_custome_pattern'].".png";
	}
	else {
		if (isset($cs_theme_option['custome_pattern']) and $cs_theme_option['custome_pattern'] == "" ) {
			if (isset($cs_theme_option['pattern_img']) and $cs_theme_option['pattern_img'] <> 0 ){
				$pattern = get_template_directory_uri()."/images/pattern/pattern".$cs_theme_option['pattern_img'].".png";
			}
		}
		else { 
			$pattern = $cs_theme_option['custome_pattern'];
		}
	}
	// pattern end
	// bg color start
	if ( isset($_POST['bg_color']) ) {
		$_SESSION['ar_sess_bg_color'] = $_POST['bg_color'];
		$bg_color = $_SESSION['ar_sess_bg_color'];
	}
	else if ( isset($_SESSION['ar_sess_bg_color']) ){
		$bg_color = $_SESSION['ar_sess_bg_color'];
	}
	else {
		$bg_color = $cs_theme_option['bg_color'];
	}
	// bg color end
	if($bg_color <> '' or $pattern <> ''){
		echo ' style="background:'.$bg_color.' url('.$pattern.')" ';
	}
}


// custom sidebar start
$cs_theme_option = get_option('cs_theme_option');
if ( isset($cs_theme_option['sidebar']) and !empty($cs_theme_option['sidebar'])) {
	foreach ( $cs_theme_option['sidebar'] as $sidebar ){
		//foreach ( $parts as $val ) {
		register_sidebar(array(
			'name' => $sidebar,
			'id' => $sidebar,
			'description' => 'This widget will be displayed on right side of the page.',
			'before_widget' => '<div class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<header class="cs-heading-title"><h2 class="cs-section-title">',
			'after_title' => '</h2></header>'
		));
	}
}
// custom sidebar end
//footer widget
register_sidebar( array(
	'name' => 'Footer Widget',
	'id' => 'footer-widget',
	'description' => 'This Widget Show the Content in Footer Area.',
	'before_widget' => '<div class="widget %2$s">',
	'after_widget' => '</div>',
	'before_title' => '<header class="cs-heading-title"><h2 class="cs-section-title">',
	'after_title' => '</h2></header>'
) );
//primary widget
register_sidebar( array(
		'name'          => __( 'Primary Sidebar', 'Faith' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Main sidebar that appears on the right.', 'Faith' ),
  		'before_widget' => '<div class="widget %2$s">',
 		'after_widget' => '</div>',
 		'before_title' => '<header class="cs-heading-title"><h2 class="cs-section-title">',
 		'after_title' => '</h2></header>'
	) );
If (!function_exists('cs_comment')) :
     /**
     * Template for comments and pingbacks.
     *
     * To override this walker in a child theme without modifying the comments template
     * simply create your own cs_comment(), and that function will be used instead.
     *
     * Used as a callback by wp_list_comments() for displaying the comments.
     *
     */
	function cs_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	$args['reply_text'] = 'reply';
 	switch ( $comment->comment_type ) :
		case '' :
	?>
	<li  <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
 		<div class="thumblist" id="comment-<?php comment_ID(); ?>">
        	<ul>
                <li>
                    <figure>
                        <a href="#"><?php echo get_avatar( $comment, 50 ); ?></a>
                    </figure>
                     <div class="text">
                      <header>
                            <?php printf( __( '%s', 'Statfort' ), sprintf( '<h5>%s</h5>', get_comment_author_link() ) ); ?>
                            <?php
                            	/* translators: 1: date, 2: time */
                                printf( __( '<time>%1$s</time>', 'Statfort' ), get_comment_date().' - '.get_comment_time()); ?>
							<?php edit_comment_link( __( '(Edit)', 'Statfort' ), ' ' );?>
                            <?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                            <?php if ( $comment->comment_approved == '0' ) : ?>
                                <div class="comment-awaiting-moderation colr"><?php _e( 'Your comment is awaiting moderation.', 'Statfort' ); ?></div>
                            <?php endif; ?>
                      </header>
                      <?php comment_text(); ?>
                    </div>
                </li>
            </ul>
        </div>
     </li>
	<?php
		break;
		case 'pingback'  :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php comment_author_link(); ?><?php edit_comment_link( __( '(Edit)', 'Statfort' ), ' ' ); ?></p>
	<?php
		break;
		endswitch;
	}
 	endif;

 /* Under Construction Page */
if ( ! function_exists( 'cs_under_construction' ) ) {
	function cs_under_construction(){ 
		global $cs_theme_option, $post;
		if(isset($post)){ $post_name = $post->post_name;  }else{ $post_name = ''; }
		if ( ($cs_theme_option['under-construction'] == "on" and !(is_user_logged_in())) or $post_name == "pf-under-construction") { 
		?>
		<div id="wrappermain-pix" class="wrapper wrapper_boxed undercunst-box">		
		<div class="bottom_strip">
				<div class="container">
					<div class="logo">
						<?php if(isset($cs_theme_option['showlogo']) and $cs_theme_option['showlogo'] == "on"){ cs_logo(); }else{ cs_logo(); } ?>
					</div>
				</div>
			</div>
		<div id="undercontruction">
		<div id="midarea">
			<?php echo '<p>'.htmlspecialchars_decode($cs_theme_option['under_construction_text']).'</p>';
				 $launch_date = $cs_theme_option['launch_date'];
				 $year = date_i18n("Y", strtotime($launch_date));
				 $month = date_i18n("m", strtotime($launch_date));
				 $month_event_c = date_i18n("M", strtotime($launch_date));							
				 $day = date_i18n("d", strtotime($launch_date));
				 $time_left = date_i18n("H,i,s", strtotime($launch_date));
				
			?>
			<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/scripts/frontend/jquery.countdown.js"></script>
			 <script type="text/javascript">
				//Countdown callback function
				jQuery(function () {
					var austDay = new Date();
					austDay = new Date(<?php echo $year; ?>,<?php echo $month; ?>-1,<?php echo $day; ?>);
					jQuery('#defaultCountdown').countdown({until: austDay});
					jQuery('#year').text(austDay.getFullYear());
				});

				</script>
			<div class="countdown styled"></div>
			<div class="countdownit">
				<div id="defaultCountdown"></div>
			</div>
			
		</div>
		</div>
			
		<!-- Footer Widgets Start -->
		<footer>
			<!-- Container Start -->
				 <!-- Social Network Start -->
				<?php if($cs_theme_option['socialnetwork'] == "on"){  
					cs_social_network();
				} ?> 
				<!-- Social Network End -->
			<!-- Container End -->
		</footer>
		<!-- Footer Start -->
         <div class="clear"></div>
		</div>
	 <?php die();
	 }
	}
}

/* new functions */

function iter_theme_add_friend_link_text($button) {
	/*
	switch ( $button['id'] ) {
		case 'pending' :
			$button['link_text'] = 'x Pending';
			$button['link_title'] = 'Cancel friend request';
			$button['link_class'] .= ' btn btn-xs btn-warning';
		break;
		case 'is_friend' :
			$button['link_text'] = '- Unfriend';
			$button['link_class'] .= ' btn btn-xs btn-danger';
		break;
		default:
			$button['link_text'] = '+ Friend';
			$button['link_title'] = 'Send friend request';
			$button['link_class'] .= ' btn btn-xs btn-success';
	}
	*/
	return $button;
}

add_filter('bp_get_add_friend_button', 'iter_theme_add_friend_link_text');

/*
function iter_theme_member_permalink() {
	global $members_template;
	print "<pre>".print_r(get_defined_vars(), TRUE)."</pre>";
}

add_filter('bp_member_permalink', 'iter_theme_member_permalink');
*/
