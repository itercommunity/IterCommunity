<?php
$strDate=rssmi_get_default_date_format();
$readable .=  '<div id="wprssmi_center_box">';
$readable .=  '<div id="wprssmi_main_container">';
$readable .=  '<ul id="wprssmi_rss_list">';
	
	
	//  don't mess with this php code 
	foreach($myarray as $items) {

	if ($pag!==1){ 	
		$total = $total +1;
		if ($maxperPage>0 && $total>=$maxperPage) break;
	}
	
	// VIDEO CHECK
	if ($targetWindow==0){
		$getVideoArray=rssmi_video($items["mylink"],$targetWindow);
		$openWindow=$getVideoArray[1];
		$items["mylink"]=$getVideoArray[0];

	}
	

	$openWindow=rssmi_lightbox_filter($items["mylink"],$targetWindow); //Lightbox filter
	$idnum=$idnum +1;
	//  END don't mess with this php code 
	

	$readable .= '<li class="item">';
	
        $readable .=     '<div class="item_inner">';
            	
            	//  domain-specific boxes -
            	            	
         $readable .=  ' <div class="blog_container">';
        $readable .=      '	<p class="blog_title"><a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').' style="color:'.$anchorcolor.'">'.$items["mytitle"].'</a></p>';
         $readable .=     '	<p>'.showexcerpt($items["mydesc"],0,$openWindow,0,$items["mylink"],$adjustImageSize,$float,$noFollow,$items["myimage"]).'</p>';
         $readable .=     '	<p><a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').' style="color:'.$anchorcolor.'"><i>Continue reading post from '.$items["myGroup"].'</i></a></p>';
          $readable .=    '	</div>';
            	 
				            	
          $readable .=    '	</div>';
          $readable .=    '	<p class="date">'.date_i18n($strDate,$items["mystrdate"]).' | <a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').' style="color:'.$anchorcolor.'">Visit source ›</a></p>';

         $readable .=    ' </li>';

}  	//  don't mess with this php code 

						


$readable .=  '</ul></div></div>';

?>