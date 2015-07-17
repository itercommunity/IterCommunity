<?php

add_action( 'wp_dashboard_setup', 'afo_news_dashboard_widget' );

if (!function_exists('ap_news_style')) {
	function ap_news_style(){
		echo '<style>.ap-news span{color:#bdbdbd;}.ap-news li{border-bottom:1px solid #bdbdbd;}</style>';
	}
}

if (!function_exists('afonews_dashboard_widget_function')) {
	function afonews_dashboard_widget_function() {
		ap_news_style();
		if(!isset($_SESSION['ap_news'])){
			$news = file_get_contents('http://www.aviplugins.com/api/news.php');
			$_SESSION['ap_news'] = $news;
		} else {
			$news = $_SESSION['ap_news'];
		}
		$news = json_decode($news);
		if(is_array($news)){
			echo '<ul class="ap-news">';
			foreach($news as $key => $value){
				echo '<li>
				<h4>'.$value->title.'</h4>
				<span>'.$value->date.'</span>
				<p>'.html_entity_decode($value->desc).'</p>
				</li>';
			}
			echo '</ul>';
		}
	}
}

if (!function_exists('afo_news_dashboard_widget')) {
	function afo_news_dashboard_widget() {
		wp_add_dashboard_widget( 
		'afonews_dashboard_widget', 
		'aviplugins.com', 
		'afonews_dashboard_widget_function' 
		);
		global $wp_meta_boxes;
		$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
		$afonews_dashboard_widget = array( 'afonews_dashboard_widget' => $normal_dashboard['afonews_dashboard_widget'] );
		unset( $normal_dashboard['afonews_dashboard_widget'] );
		$sorted_dashboard = array_merge( $afonews_dashboard_widget, $normal_dashboard );
		$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	} 
}