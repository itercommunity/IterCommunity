<?php
 
 
if ( ! current_user_can( 'activate_plugins' ) ) {
	show_admin_bar( false );
	add_filter('show_admin_bar', '__return_false');
}

add_action( 'wp_head', 'icc_override_toolbar_margin', 11 );
function icc_override_toolbar_margin() {
	if ( is_admin_bar_showing() ) { ?>
		<style type="text/css" media="screen">
			html { margin-top: 12px !important; }
			* html body { margin-top: 12px !important; }
		</style>
	<?php }
} 

function icc_front_scripts_enqueue() { 
	wp_enqueue_script('functions_ic_js',  get_stylesheet_directory_uri(). '/functions.js', '0', '', true); 
}
 
add_action('wp_enqueue_scripts', 'icc_front_scripts_enqueue'); 
 
?>