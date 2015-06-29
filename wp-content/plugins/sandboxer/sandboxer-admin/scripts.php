<?

if (isset($_GET['page']) && $_GET['page'] == 'sandboxer-admin/options.php' ) {
	// add_action('init', 'wprtp_register_scripts');
}


function sboxr_register_scripts() {
	global $wp_version;

	if ( version_compare($wp_version, "3.3.1", ">" ) ) {  
		wp_enqueue_script( 'jquery' );
	} else {	
		wp_deregister_script( 'jquery' );
		wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
		wp_enqueue_script( 'jquery' );	
	}
	// wp_enqueue_script( 'add-remove', plugins_url('sandboxer-admin/script.js',dirname(__FILE__)),array('jquery'));
}

?>