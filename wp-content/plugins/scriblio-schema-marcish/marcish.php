<?php

class ScribSchemaMarcish {
	public function __construct(){

		$this->path_web = plugins_url( basename( dirname( __FILE__ )));

		register_activation_hook( __FILE__ , array( &$this, 'activate' ));

		add_action( 'init', array( &$this , 'init' ));
		add_action( 'wp_footer', array( &$this, 'wp_footer_js' ));
		add_action( 'admin_menu', array( &$this, 'admin_menu_hook' ));

		add_shortcode('scrib_bookjacket', array( &$this, 'shortcode_bookjacket' ));
		add_shortcode('scrib_availability', array( &$this, 'shortcode_availability' ));

		wp_register_script( 'scrib-googlebook', $this->path_web . '/scrib.googlebook.js', array('jquery'), '20080422' );
		wp_enqueue_script( 'scrib-googlebook' );

		wp_register_style( 'scrib-display', $this->path_web .'/display.css' );
		wp_enqueue_style( 'scrib-display' );

	}

	public function init()
	{
		$this->options = get_option('scrib_marcish_opts');

		// upgrade old versions
		if( 290 > $this->options['version'] )
			$this->upgrade( $this->options['version'] );

		$this->register();
	}

	public function upgrade( $version )
	{

		global $wpdb;

		static $nonced = FALSE;
		if( $nonced )
			return;

		if( 1 > $version )
		{
			// set the taxonomies
			$options = get_option( 'scrib_taxonomies' );
	
			$options['name'] = array_merge( (array) $options['name'] , 
				array(
					'asin' => 'ASIN',
					'award' => 'Award',
					'category' => 'Categories',
					'cm' => 'Month Created',
					'collection' => 'Collection',
					'creator' => 'Creator',
					'cy' => 'Year Created',
					'ean' => 'EAN',
					'exhibit' => 'Exhibit',
					'format' => 'Format',
					'genre' => 'Genre',
					'isbn' => 'ISBN',
					'issn' => 'ISSN',
					'lang' => 'Language',
					'lccn' => 'LCCN',
					'oclc' => 'OCLC',
					'nom' => 'Title',
					'partial' => 'Partial Term',
					'person' => 'Person',
					'place' => 'Place',
					'post_tag' => 'Post Tags',
					'readinglevel' => 'Reading Level',
					'sd' => 'Subject Day',
					'sm' => 'Subject Month',
					'subject' => 'Subject',
					'sy' => 'Subject Year',
					'time' => 'Time',
				)
			);
	
			$options['search'] = array_merge( (array) $options['search'] , 
				array(
					'asin',
					'award',
					'category',
					'cm',
					'collection',
					'creator',
					'cy',
					'ean',
					'exhibit',
					'format',
					'genre',
					'genre',
					'isbn',
					'issn',
					'lang',
					'lccn',
					'oclc',
					'nom',
					'partial',
					'person',
					'place',
					'post_tag',
					'readinglevel',
					'sd',
					'sm',
					'subject',
					'sy',
					'time',
				)
			);
	
			$options['related'] = array_merge( (array) $options['related'] , 
				array(
					'award',
					'creator',
					'exhibit',
					'genre',
					'person',
					'place',
					'subject',
					'time',
				)
			);
	
			$options['suggest'] = array_merge( (array) $options['suggest'] , 
				array(
					'award',
					'collection',
					'creator',
					'cy',
					'exhibit',
					'genre',
					'nom',
					'partial',
					'person',
					'place',
					'readinglevel',
					'subject',
					'time',
				)
			);
	
			update_option( 'scrib_taxonomies' , $options );

			// set the categories
			$options = get_option( 'scrib_categories' );

			$cat_cat = wp_insert_term( 'Catalog' , 'category' , array( 'description' => 'Catalog entries' ));

			$options['browse'] = array_unique( array_merge( (array) $options['browse'] , 
				array(
					(string) absint( $cat_cat['term_taxonomy_id'] )
				)
			));

			$options['hide'] = array_unique( array_merge( (array) $options['hide'] , 
				array(
					(string) absint( $cat_cat['term_taxonomy_id'] )
				)
			));

			update_option( 'scrib_categories', $options );


			// set the browse base
			$options = get_option( 'scrib_opts' );

			if( empty( $options['searchprompt'] ))
				$options['searchprompt'] = 'Books, movies, music';

			if( empty( $options['facetfound'] ))
				$options['facetfound'] = 1000;

			// setup the browse page, if it doesn't exist
			if( empty( $options['browseid'] ) || $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE ID = ". absint( $options['browseid'] ) .' AND post_status = "publish" AND post_type = "page" ') == FALSE )
			{
				// create the default browse page
				$postdata['post_title'] = __( 'Browse' );
				$postdata['post_name'] = __( 'browse' );
				$postdata['comment_status'] = 0;
				$postdata['ping_status'] 	= 0;
				$postdata['post_status'] 	= 'publish';
				$postdata['post_type'] 		= 'page';
				$postdata['post_content']	=  __( 'Browse new titles.' );
				$postdata['post_excerpt']	= __( 'Browse new titles.' );
				$postdata['post_author'] = 0;
				$post_id = wp_insert_post( $postdata ); // insert the post
	
				// set the options with this new page
				$options['browseid'] = (int) $post_id;
			}

			update_option( 'scrib_opts', $options );
		}

		// make sure we clean up the rewrite rules
		global $wp_rewrite;
		$wp_rewrite->flush_rules();

		// reload the new options and set the updated version number
		$this->options = get_option('scrib_marcish_opts');
		$this->options['version'] = 290;
		update_option( 'scrib_marcish_opts' , $this->options );

		$nonced = TRUE;
	}

	public function wp_footer_js(){
		$this->availability_gbslink();
	}

	public function shortcode_bookjacket( $arg, $content = '' ){
		// [scrib_bookjacket]<img... />[/scrib_bookjacket]
		global $id, $bsuite;

		if( !is_singular() ){
			return('<a href="'. get_permalink( $id ) .'">'. $content .'</a>');
		}else{
			preg_match( '/src="([^"]+)?"/', $content, $matches );
			return( '<a href="'. $matches[1] .'" title="'. attribute_escape( strip_tags( get_the_title( $post_id ))) .'">'. $bsuite->icon_get_h( $id, 's' ) .'</a>');
		}
	}

	public function the_related_bookjackets($before = '<li>', $after = '</li>') {
		global $post, $bsuite;
		$report = FALSE;

		$id = (int) $post->ID;
		if ( !$id )
			return FALSE;

		$posts = array_slice( $bsuite->bsuggestive_getposts( $id ), 0, 10 );
		if($posts){
			$report = '';
			foreach($posts as $post_id){
				$url = get_permalink($post_id);
				$linktext = trim( substr( strip_tags(get_the_title($post_id)), 0, 45));
				if( $linktext <> get_the_title($post_id) )
					$linktext .= __('...');
				$report .= $before ."<a href='$url'>". $bsuite->icon_get_h( $post_id, 's' ) . "</a><h4><a href='$url'>$linktext</a></h4>". $after;
			}
		}
		return($report);
	}

	public function admin_menu_hook(){
		wp_register_style( 'scrib-marcish-editor', $this->path_web .'/editor.css' );
		wp_enqueue_style( 'scrib-marcish-editor' );

		wp_register_script( 'scrib-marcish-editor', $this->path_web . '/editor.js', array('scrib-editor'), '1' );
		wp_enqueue_script( 'scrib-marcish-editor' );

		add_submenu_page('post-new.php', 'Add New Bibliographic/Archive Record', 'New Catalog Record', 'edit_posts',  'post-new.php?scrib_meditor_form=marcish' );

		add_action( 'scrib_meditor_form_marcish', array( &$this, 'admin_activatejs' ));
	}

	public function admin_activatejs(){
		add_action( 'admin_footer' , array( &$this, 'admin_footerjs' ));
	}

	public function admin_footerjs(){
?>
		<script type="text/javascript">
			scrib_meditor_marcish();
		</script>
<?php
	}

	public function register( ){
		global $scrib;

		$subject_types = array(
			'subject' => 'Topical Term',
			'genre' => 'Genre',
			'person' => 'Person/Character',
			'place' => 'Place',
			'time' => 'Time',
			'department' => 'Department',
			'tag' => 'Tag',
			'exhibit' => 'Exhibit',
			'award' => 'Award',
			'readinglevel' => 'Reading Level',
		);

		$scrib->meditor_register( 'marcish',
			array(
				'_title' => 'Bibliographic and Archive Item Record',
				'_elements' => array(
					'title' => array(
						'_title' => 'Additional Titles',
						'_description' => 'Alternate titles or additional forms of this title. Think translations, uniform, and series titles (<a href="http://about.scriblio.net/wiki/meditor/marcish/title" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'a' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'title',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'attribution' => array(
						'_title' => 'Attribution',
						'_description' => 'The statement of responsibility for this work (<a href="http://about.scriblio.net/wiki/meditor/marcish/attribution" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => FALSE,
						'_elements' => array(
							'a' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'creator' => array(
						'_title' => 'Creator',
						'_description' => 'Authors, editors, producers, and others that contributed to the creation of this work (<a href="http://about.scriblio.net/wiki/meditor/marcish/creator" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'name' => array(
								'_title' => 'Name',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'creator',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'role' => array(
								'_title' => 'Role',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'subject' => array(
						'_title' => 'Subject',
						'_description' => 'Words and phrases that descripe the content of the work (<a href="http://about.scriblio.net/wiki/meditor/marcish/subject" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'a_type' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => $subject_types,
									'_default' => 'subject',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'a' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'subject',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'b_type' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => $subject_types,
									'_default' => 'subject',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'b' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'subject',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'c_type' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => $subject_types,
									'_default' => 'subject',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'c' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'subject',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'd_type' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => $subject_types,
									'_default' => 'subject',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'd' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'subject',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'e_type' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => $subject_types,
									'_default' => 'subject',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'e' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'subject',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'f_type' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => $subject_types,
									'_default' => 'subject',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'f' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'subject',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'g_type' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => $subject_types,
									'_default' => 'subject',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'g' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'subject',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'dictionary' => array(
								'_title' => 'Dict.',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'subject_date' => array(
						'_title' => 'Date Coverage',
						'_description' => 'A calendar representation of the content of the work (<a href="http://about.scriblio.net/wiki/meditor/marcish/subject_date" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'y' => array(
								'_title' => 'Year',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'absint',
							),
							'm' => array(
								'_title' => 'Month',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_month' ),
							),
							'd' => array(
								'_title' => 'Day',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_day' ),
							),
							'c' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => array(
										'exact' => 'Exactly',
										'approx' => 'Approximately',
										'before' => 'Before',
										'after' => 'After',
										'circa' => 'Circa',
										'decade' => 'Within Decade',
										'century' => 'Within Century',
									),
									'_default' => 'exact',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'subject_geo' => array(
						'_title' => 'Geographic Coverage',
						'_description' => 'A geographic coordinate representation of the content of the work (<a href="http://about.scriblio.net/wiki/meditor/marcish/subject_geo" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'point_lat' => array(
								'_title' => 'Latitude',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'point_lon' => array(
								'_title' => 'Longitude',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'bounds' => array(
								'_title' => 'Bounds',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'name' => array(
								'_title' => 'Name',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'callnumbers' => array(
						'_title' => 'Call Number',
						'_description' => 'The LC or Dewey call number and location for this work (<a href="http://about.scriblio.net/wiki/meditor/marcish/callnumbers" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'type' => array(
								'_title' => 'Type',
								'_input' => array(
									'_type' => 'select',
									'_values' => array(
										'lc' => 'LC',
										'dewey' => 'Dewey',
									),
									'_default' => 'dewey',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'number' => array(
								'_title' => 'Number',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'location' => array(
								'_title' => 'Location',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'text' => array(
						'_title' => 'Textual Content',
						'_description' => 'A description, transcription, translation, or other long-form textual content related to the work (<a href="http://about.scriblio.net/wiki/meditor/marcish/text" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'type' => array(
								'_title' => 'Type',
								'_input' => array(
									'_type' => 'select',
									'_values' => array(
										'description' => 'Description',
										'transcription' => 'Transcription',
										'translation' => 'Translation',
										'contents' => 'Contents',
										'review' => 'Review',
										'notes' => 'Notes',
										'firstwords' => 'First Words',
										'lastwords' => 'Last Words',
										'dedication' => 'Dedication',
										'quotes' => 'Notable Quotations',
										'sample' => 'Sample',
									),
									'_default' => 'description',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'lang' => array(
								'_title' => 'Language',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'content' => array(
								'_title' => 'Content',
								'_input' => array(
									'_type' => 'textarea',
								),
								'_sanitize' => 'wp_filter_kses',
							),
							'notes' => array(
								'_title' => 'Notes',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'published' => array(
						'_title' => 'Publication Info',
						'_description' => 'Publication info (<a href="http://about.scriblio.net/wiki/meditor/marcish/published" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'cy' => array(
								'_title' => 'Year',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'absint',
							),
							'cm' => array(
								'_title' => 'Month',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_month' ),
							),
							'cd' => array(
								'_title' => 'Day',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_day' ),
							),
							'cc' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => array(
										'nodate' => 'Undated',
										'exact' => 'Exactly',
										'approx' => 'Approximately',
										'before' => 'Before',
										'after' => 'After',
										'circa' => 'Circa',
										'decade' => 'Within Decade',
										'century' => 'Within Century',
									),
									'_default' => 'exact',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'edition' => array(
								'_title' => 'Edition',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'lang' => array(
								'_title' => 'Language',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'publisher' => array(
								'_title' => 'Publisher',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'copyright' => array(
								'_title' => 'Copyright',
								'_input' => array(
									'_type' => 'select',
									'_values' => array(
										'uc' => 'Uncertain',
										'c' => 'Copyrighted',
										'cc' => 'Creative Commons',
										'pd' => 'Public Domain',
									),
									'_default' => 'uc',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'copyright_note' => array(
								'_title' => 'Note',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'description_physical' => array(
						'_title' => 'Physical Description',
						'_description' => 'Physical description (<a href="http://about.scriblio.net/wiki/meditor/marcish/description_physical" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'dw' => array(
								'_title' => 'Width',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'absint',
							),
							'dh' => array(
								'_title' => 'Height',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'absint',
							),
							'dd' => array(
								'_title' => 'Depth',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'absint',
							),
							'du' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => array(
										'inch' => 'Inches',
										'cm' => 'Centimeters',
									),
									'_default' => 'inches',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'wv' => array(
								'_title' => 'Weight',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'absint',
							),
							'wu' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => array(
										'ounce' => 'Ounces',
										'pound' => 'Pounds',
										'g' => 'Grams',
										'kg' => 'Kilograms',
									),
									'_default' => 'ounce',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'duration' => array(
								'_title' => 'Length',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'absint',
							),
							'duration_units' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'select',
									'_values' => array(
										'pages' => 'Pages',
										'minutes' => 'Minutes',
									),
									'_default' => 'pages',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'cv' => array(
								'_title' => 'Cost',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'absint',
							),
							'cu' => array(
								'_title' => 'Currency',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses' ,
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses' ,
							),
						),
					),
					'linked_urls' => array(
						'_title' => 'Linked URL',
						'_description' => 'Web links (<a href="http://about.scriblio.net/wiki/meditor/marcish/linked_urls" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'name' => array(
								'_title' => 'Name',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'href' => array(
								'_title' => 'href',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'sanitize_url',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'format' => array(
						'_title' => 'Format',
						'_description' => 'Format (<a href="http://about.scriblio.net/wiki/meditor/marcish/format" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'a' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'format',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'b' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'format',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'c' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'format',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'd' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'format',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'e' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'format',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'f' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'format',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'g' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_suggest' => 'format',
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'dictionary' => array(
								'_title' => 'Dict.',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'idnumbers' => array(
						'_title' => 'Standard Numbers',
						'_description' => 'ISBNs, ISSNs, and other numbers identifying the work (<a href="http://about.scriblio.net/wiki/meditor/marcish/idnumbers" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'type' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'id' => array(
								'_title' => '',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'suppress' => array(
								'_title' => 'Suppress',
								'_input' => array(
									'_type' => 'checkbox',
								),
								'_sanitize' => 'absint',
							),
							'src' => array(
								'_title' => 'Source',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'source' => array(
						'_title' => 'Archival Source',
						'_description' => 'Where did this work come from (for archive records) (<a href="http://about.scriblio.net/wiki/meditor/marcish/source" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => FALSE,
						'_elements' => array(

							'file' => array(
								'_title' => 'File Name',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'dy' => array(
								'_title' => 'Digitized Year',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'absint',
							),
							'dm' => array(
								'_title' => 'Month',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_month' ),
							),
							'dd' => array(
								'_title' => 'Day',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_day' ),
							),
							'box' => array(
								'_title' => 'Box Number',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'folder' => array(
								'_title' => 'Folder Number',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'collection' => array(
								'_title' => 'Collection',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'collection_num' => array(
								'_title' => 'Collection Number',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
							'publisher' => array(
								'_title' => 'Publisher',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => 'wp_filter_nohtml_kses',
							),
						),
					),
					'related' => array(
						'_title' => 'Related Records',
						'_description' => 'The relationship of this work to other works (<a href="http://about.scriblio.net/wiki/meditor/marcish/related" title="More information at the Scriblio website.">more info</a>).',
						'_repeatable' => TRUE,
						'_elements' => array(
							'rel' => array(
								'_title' => 'Relationship',
								'_input' => array(
									'_type' => 'select',
									'_values' => array(
										'parent' => 'Parent',
										'child' => 'Child',
										'next' => 'Next In Series/Next Page',
										'previous' => 'Previous In Series/Previous Page',
										'reverse' => 'Reverse Side',
										'duplicate' => 'Duplicate',
									),
									'_default' => 'exact',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_selectlist' ),
							),
							'record' => array(
								'_title' => 'Record',
								'_input' => array(
									'_type' => 'text',
									'_autocomplete' => 'off',
								),
								'_sanitize' => array( &$scrib, 'meditor_sanitize_related' ),
							),
						),
					),
					'addrecord' => array(
						'_title' => 'Add New Record',
						'_description' => '<a href="http://about.scriblio.net/wiki/meditor/marcish/addrecord" title="More information at the Scriblio website.">More info</a>.',
						'_repeatable' => FALSE,
						'_elements' => array(
							'a' => array(
								'_title' => '',
								'_input' => array(
									'_type' => '_function',
									'_function' => array( &$scrib, 'meditor_add_related_commandlinks' ),
								),
							),
						),
					),
				),
				'_relationships' => array(
					'parent' => array( '_title' => 'Parent' , '_rel_inverse' => 'child' ),
					'child' => array( '_title' => 'Child' , '_rel_inverse' => 'parent' ),
					'next' => array( '_title' => 'Next In Series/Next Page' , '_rel_inverse' => 'previous' ),
					'previous' => array( '_title' => 'Previous In Series/Previous Page' , '_rel_inverse' => 'next' ),
					'reverse' => array( '_title' => 'Reverse Side' , '_rel_inverse' => 'reverse' ),
					'sibling' => array( '_title' => 'Sibling' , '_rel_inverse' => FALSE ),
				),
			)
		);

		// taxonomies
		$taxes = array(
			'creator' 		=> __( 'Creator' , 'Scrib' ),
			'nom' 			=> __( 'Title' , 'Scrib' ),
			'lang' 			=> __( 'Language' , 'Scrib' ),
			'cy' 			=> __( 'Year Created' , 'Scrib' ),
			'cm' 			=> __( 'Month Created' , 'Scrib' ),
			'format' 		=> __( 'Format' , 'Scrib' ),
			'subject' 		=> __( 'Subject' , 'Scrib' ),
			'genre' 		=> __( 'Genre' , 'Scrib' ),
			'person' 		=> __( 'Person' , 'Scrib' ),
			'place' 		=> __( 'Place' , 'Scrib' ),
			'time' 			=> __( 'Time' , 'Scrib' ),
			'exhibit' 		=> __( 'Exhibit' , 'Scrib' ),
			'award' 		=> __( 'Award' , 'Scrib' ),
			'readinglevel' 	=> __( 'Reading Level' , 'Scrib' ),
			'sy' 			=> __( 'Subject Year' , 'Scrib' ),
			'sm' 			=> __( 'Subject Month' , 'Scrib' ),
			'sd' 			=> __( 'Subject Day' , 'Scrib' ),
			'collection' 	=> __( 'Collection' , 'Scrib' ),
			'isbn' 			=> __( 'ISBN' , 'Scrib' ),
			'issn' 			=> __( 'ISSN' , 'Scrib' ),
			'lccn' 			=> __( 'LCCN' , 'Scrib' ),
			'asin' 			=> __( 'ASIN' , 'Scrib' ),
			'ean' 			=> __( 'EAN' , 'Scrib' ),
			'oclc' 			=> __( 'OCLC' , 'Scrib' ),
			'partial' 		=> __( 'Partial Term' , 'Scrib' ),
		);

		foreach( $taxes as $k => $v )
		{
			register_taxonomy( $k, 'post', 
				array( 
					'hierarchical' => FALSE, 
					'update_count_callback' => '_update_post_term_count', 
					'rewrite' => TRUE, 
					'query_var' => $k,
					'label' => $v,
				)
			);
		
		}

		// actions and filters for marcish form
		add_filter('bsuite_post_icon', array( &$this, 'the_bsuite_post_icon' ), 5, 2);

		add_filter('scrib_meditor_pre_excerpt', array(&$this, 'pre_excerpt'), 1, 2);
//		add_filter('scrib_meditor_pre_content', array(&$this, 'pre_content'), 1, 2); 
			// replaced with searchsmart_content filter, yields smaller databases
		add_filter( 'bsuite_searchsmart_content', array(&$this, 'searchsmart_content'), 7, 2);

		add_filter( 'the_content', array(&$this, 'the_content'), 1);
		add_filter( 'the_content_rss', array(&$this, 'the_excerpt_rss'), 11);
		add_filter( 'the_excerpt', array(&$this, 'the_excerpt'), 1);
		add_filter( 'the_excerpt_rss', array(&$this, 'the_excerpt_rss'), 11);

		add_filter('scrib_availability_excerpt', array(&$this, 'availability'), 10, 3);
		add_filter('scrib_availability_content', array(&$this, 'availability'), 10, 3);
		add_filter('the_author', array( &$this, 'the_author_filter' ), 1);
		add_filter('author_link', array( &$this, 'author_link_filter' ), 1);

		add_action('scrib_meditor_save_record', array(&$this, 'save_record'), 1, 2);

		add_filter('scrib_meditor_add_parent', array(&$this, 'add_parent'), 1, 2);
		add_filter('scrib_meditor_add_child', array(&$this, 'add_child'), 1, 2);
		add_filter('scrib_meditor_add_next', array(&$this, 'add_next'), 1, 2);
		add_filter('scrib_meditor_add_previous', array(&$this, 'add_previous'), 1, 2);
		add_filter('scrib_meditor_add_reverse', array(&$this, 'add_reverse'), 1, 2);
		add_filter('scrib_meditor_add_sibling', array(&$this, 'add_sibling'), 1, 2);
	}

	public function the_bsuite_post_icon( &$input, $id ) {
		if( is_array( $input ))
			return( $input );

		if( $id && ( $r = get_post_meta( $id, 'scrib_meditor_content', true )) && is_array( $r['marcish'] )){
			$title = trim( $r['marcish']['title'][0]['a'] );
			if( strpos( $title, ':', 5 ))
				$title = substr( $title, 0, strpos( $title, ':', 5 ));
			$attrib = trim( $r['marcish']['attribution'][0]['a'] );
			if( strpos( $attrib, ';', 5 ))
				$attrib = substr( $attrib, 0, strpos( $attrib, ';', 5 ));
			return( array(
				't' => array(
					'file' => dirname( __FILE__ ) .'/img/post_icon_default/s.jpg',
					'url' => 'http://api.scriblio.net/v01a/fakejacket/'. urlencode( $title ) .'?author='. urlencode( $attrib ) .'&size=1',
					'w' => '75',
					'h' => '100',
					),
				's' => array(
					'file' => dirname( __FILE__ ) .'/img/post_icon_default/s.jpg',
					'url' => 'http://api.scriblio.net/v01a/fakejacket/'. urlencode( $title ) .'?author='. urlencode( $attrib ) .'&size=2',
					'w' => '100',
					'h' => '132',
					),
				'm' => array(
					'file' => dirname( __FILE__ ) .'/img/post_icon_default/m.jpg',
					'url' => 'http://api.scriblio.net/v01a/fakejacket/'. urlencode( $title ) .'?author='. urlencode( $attrib ) .'&size=3',
					'w' => '135',
					'h' => '180',
					),
				'l' => array(
					'file' => dirname( __FILE__ ) .'/img/post_icon_default/l.jpg',
					'url' => 'http://api.scriblio.net/v01a/fakejacket/'. urlencode( $title ) .'?author='. urlencode( $attrib ) .'&size=4',
					'w' => '240',
					'h' => '320',
					),
				'b' => array(
					'file' => dirname( __FILE__ ) .'/img/post_icon_default/b.jpg',
					'url' => 'http://api.scriblio.net/v01a/fakejacket/'. urlencode( $title ) .'?author='. urlencode( $attrib ) .'&size=5',
					'w' => '500',
					'h' => '665',
					),
				)
			);
		}

//http://api.scriblio.net/v01a/fakejacket/This+Land+Is+Their+Land?author=Barbara+Ehrenreich.&size=4&style=4

	}

	public function parse_parts( &$r ){
		global $scrib;

		$parsed = array();

		// do up the subjects
		$spare_keys = array( 'a', 'b', 'c', 'd', 'e', 'f', 'g' );
		$type_counts = array();
		foreach( $r['subject'] as $temp ){
			$subjline = array();
			foreach( $spare_keys as $spare_key ){
				if( !empty(  $temp[ $spare_key ] ))
				{
					if( 'subject' <> $temp[ $spare_key .'_type' ] )
						$parsed[ $temp[ $spare_key .'_type' ] ][] = array( 
							'type' => $temp[ $spare_key .'_type' ], 
							'value' => $temp[ $spare_key ], 
							'dictionary' => $temp[ 'dictionary' ], 
							'src' => $temp[ 'src' ], 
						);

					switch( $temp[ $spare_key .'_type' ] )
					{
						case 'subject';
						case 'genre';
						case 'person';
						case 'place';
						case 'time';
						case 'department';
						case 'tag';
							$parsed['subjkey'][] = array( 
								'type' => $temp[ $spare_key .'_type' ], 
								'value' => $temp[ $spare_key ],
								'dictionary' => $temp[ 'dictionary' ], 
								'src' => $temp[ 'src' ], 
							);
							$subjline[] = array( 
								'type' => $temp[ $spare_key .'_type' ], 
								'value' => $temp[ $spare_key ],
								'dictionary' => $temp[ 'dictionary' ], 
								'src' => $temp[ 'src' ], 
							);

							$type_counts[ $temp[ $spare_key .'_type' ] ] ++;

							break;
					}
				}
			}
			if( count( $subjline ) && ( 0 < $type_counts['subject'] || 1 < count( $type_counts )))
				$parsed['subject'][] = $subjline;

			$type_counts = array();
		}

		// unique the whole batch so far
		foreach( $parsed as $k => $v )
			$parsed[ $k ] = $scrib->array_unique_deep( $v );

		// get the related records
		foreach( $r['related'] as $temp )
			$parsed['related'][ $temp['rel'] ][] = $temp['record'];

		// get the standard numbers
		foreach( $r['idnumbers'] as $temp )
			$parsed['idnumbers'][ $temp['type'] ][] = $temp['id'];

		// unique those numbers
		foreach( $parsed['idnumbers'] as $k => $v )
			$parsed['idnumbers'][ $k ] = $scrib->array_unique_deep( $v );

		// get the various text fields
		foreach( $r['text'] as $temp )
			$parsed[ $temp['type'] ][] = wpautop( convert_chars( wptexturize( $temp['content'] )));

		return( $parsed );
	}

	public function pre_excerpt( $content, $r ) {
		if( isset( $r['marcish'] ))
			return( $this->parse_excerpt( $r['marcish'] ));
		return( $content );
	}

	public function pre_content( $content, $r ) {
		if( isset( $r['marcish'] ))
			return $this->parse_words( $r['marcish'] );
		return( $content );
	}

	public function searchsmart_content( $content , $id )
	{
		if ( $id && ( $r = get_post_meta( $id, 'scrib_meditor_content', true )) && is_array( $r['marcish'] ))
			$content .= "\n". $this->parse_words( $r['marcish'] );

		return $content;
	}

	public function the_excerpt( $content ){
		global $id;
		if( $id && ( $r = get_post_meta( $id, 'scrib_meditor_content', true )) && is_array( $r['marcish'] ))
			if( is_feed() )
				return( $this->parse_excerpt_rss( $r['marcish'] ));
			else
				return( $this->parse_excerpt( $r['marcish'] ));

		return( $content );
	}

	public function parse_excerpt( &$r ){
		global $id, $bsuite, $scrib;

		$parsed = $this->parse_parts( $r );
		$result = '<ul class="marcish summaryrecord">';

		$result .= '<li class="image"><a href="'. get_permalink( $id ) .'" rel="bookmark" title="Permanent Link to '. attribute_escape( get_the_title( $id )) .'">'. $bsuite->icon_get_h( $id, 's', TRUE ) .'</a></li>';

		if( isset( $r['attribution'][0]['a'] ))
			$result .= '<li class="attribution"><h3>Attribution</h3>'. $r['attribution'][0]['a'] .'</li>';

		$pubdeets = array();
		if( isset( $r['format'][0]['a'] ))
			$pubdeets[] = '<span class="format">'. $r['format'][0]['a'] .'</span>';

		if( isset( $r['published'][0]['edition'] ))
			$pubdeets[] = '<span class="edition">'. $r['published'][0]['edition'] .'</span>';

		if( isset( $r['published'][0]['publisher'] ))
			$pubdeets[] = '<span class="publisher">'. $r['published'][0]['publisher'] .'</span>';

		if( isset( $r['published'][0]['cy'] ))
			$pubdeets[] = '<span class="pubyear">'. $r['published'][0]['cy'] .'</span>';

		if( count( $pubdeets ))
			$result .= '<li class="publication_details"><h3>Publication Details</h3>'. implode( '<span class="meta-sep">, </span>', $pubdeets ) .'</li>';

		if( isset( $r['linked_urls'][0]['href'] )){
			$result .= '<li class="linked_urls">'. ( 1 < count( $r['linked_urls'] ) ? '<h3>Links</h3>' : '<h3>Link</h3>' ) .'<ul>';
			foreach( $r['linked_urls'] as $temp )
				$result .= '<li><a href="' . $temp['href'] .'" title="go to this linked website">' . $temp['name'] .'</a></li>';
			$result .= '</ul></li>';
		}

		if( isset( $parsed['description'][0] )){
			$result .= '<li class="description"><h3>Description</h3>' . $parsed['description'][0] .'</li>';
		}

		if( isset( $parsed['subjkey'][0] )){
			$tags = array();
			foreach( $parsed['subjkey'] as $temp )
			{
				switch( $temp['type'] )
				{
					case 'subject':
					case 'genre':
						$tags[] = '<a href="'. $scrib->get_tag_link( array( 'taxonomy' => $temp['type'], 'slug' => urlencode( $temp['value'] ))).'" rel="tag">' . $temp['value'] . '</a>';
						break;
				}
			}

			// authors or, er, creators
			if( isset( $r['creator'][0]['name'] ))
				foreach( $r['creator'] as $temp )
					$tags[] = '<a href="'. $scrib->get_tag_link( array( 'taxonomy' => 'creator', 'slug' => urlencode( $temp['name'] ))).'" rel="tag">' . $temp['name'] . '</a>';

			$result .= '<li class="tags"><h3>Tags</h3> '. implode( ' &middot; ', $tags ) .'</li>';
		}

		if( is_array( $parsed['idnumbers'] ))
			$result .= '<li class="availability"><h3>Availability</h3><ul>'. apply_filters( 'scrib_availability_excerpt', '', $id, $parsed['idnumbers']) .'</ul></li>';

		$result .= '</ul>';
		$result = convert_chars( wptexturize( $result ));

		return $result ;
	}

	public function the_excerpt_rss( $content ){
		global $id;
		if( $id && ( $r = get_post_meta( $id, 'scrib_meditor_content', true )) && is_array( $r['marcish'] ))
			return( $this->parse_excerpt_rss( $r['marcish'] ));

		return( $content );
	}

	public function parse_excerpt_rss( &$r ){
		global $id, $bsuite, $scrib;

		$parsed = $this->parse_parts( $r );
		$result = '<ul class="marcish summaryrecord">';

		$result .= '<li class="image"><a href="'. get_permalink( $id ) .'" rel="bookmark" title="Permanent Link to '. attribute_escape( get_the_title( $id )) .'">'. $bsuite->icon_get_h( $id, 's' ) .'</a></li>';

		if( isset( $r['attribution'][0]['a'] ))
			$result .= '<li class="attribution">'. $r['attribution'][0]['a'] .'</li>';

		$pubdeets = array();
		if( isset( $r['format'][0]['a'] ))
			$pubdeets[] = '<span class="format">'. $r['format'][0]['a'] .'</span>';

		if( isset( $r['published'][0]['edition'] ))
			$pubdeets[] = '<span class="edition">'. $r['published'][0]['edition'] .'</span>';

		if( isset( $r['published'][0]['publisher'] ))
			$pubdeets[] = '<span class="publisher">'. $r['published'][0]['publisher'] .'</span>';

		if( isset( $r['published'][0]['cy'] ))
			$pubdeets[] = '<span class="pubyear">'. $r['published'][0]['cy'] .'</span>';

		if( count( $pubdeets ))
			$result .= '<li class="publication_details">'. implode( '<span class="meta-sep">, </span>', $pubdeets ) .'</li>';

		if( isset( $r['linked_urls'][0]['href'] )){
			$result .= '<li class="linked_urls"><ul>';
			foreach( $r['linked_urls'] as $temp )
				$result .= '<li><a href="' . $temp['href'] .'" title="go to this linked website">' . $temp['name'] .'</a></li>';
			$result .= '</ul></li>';
		}

		if( isset( $parsed['description'][0] )){
			$result .= '<li class="description">' . $parsed['description'][0] .'</li>';
		}

		if( isset( $parsed['subjkey'][0] )){
			$tags = array();
			foreach( $parsed['subjkey'] as $temp )
				$tags[] = '<a href="'. $scrib->get_tag_link( array( 'taxonomy' => $temp['type'], 'slug' => urlencode( $temp['value'] ))).'" rel="tag">' . $temp['value'] . '</a>';


			// authors or, er, creators
			if( isset( $r['creator'][0]['name'] ))
				foreach( $r['creator'] as $temp )
					$tags[] = '<a href="'. $scrib->get_tag_link( array( 'taxonomy' => 'creator', 'slug' => urlencode( $temp['name'] ))).'" rel="tag">' . $temp['name'] . '</a>';

			$result .= '<li class="tags">'. implode( ' &middot; ', $tags ) .'</li>';
		}

		$result .= '</ul>';
		$result = convert_chars( wptexturize( $result ));

		return $result ;
	}

	public function the_content( $content ){
		global $id;
		if( $id && ( $r = get_post_meta( $id, 'scrib_meditor_content', true )) && is_array( $r['marcish'] ))
			if( is_feed() )
				return( $this->parse_excerpt_rss( $r['marcish'] ));
			else
				return( $this->parse_content( $r['marcish'] ));

		return( $content );
	}

	public function parse_content( &$r ){
		global $id, $bsuite, $scrib;
		$parsed = $this->parse_parts( $r );

		$result = '<ul class="marcish fullrecord">';

		$result .= '<li class="image">'. $bsuite->icon_get_h( $id, 's' ) .'</li>';

		if( isset( $r['title'][0]['a'] )){
			$result .= '<li class="title">'. ( 1 < count( $r['title'] ) ? '<h3>Titles</h3>' : '<h3>Title</h3>') .'<ul>';
			foreach( $r['title'] as $temp ){
				$result .= '<li>' . $temp['a'] . '</li>';
			}
			$result .= '</ul></li>';
		}

		if( isset( $r['attribution'][0]['a'] ))
			$result .= '<li class="attribution"><h3>Attribution</h3>'. $r['attribution'][0]['a'] .'</li>';

		$pubdeets = array();
		if( isset( $r['format'][0]['a'] ))
			$pubdeets[] = '<span class="format">'. $r['format'][0]['a'] .'</span>';

		if( isset( $r['published'][0]['edition'] ))
			$pubdeets[] = '<span class="edition">'. $r['published'][0]['edition'] .'</span>';

		if( isset( $r['published'][0]['publisher'] ))
			$pubdeets[] = '<span class="publisher">'. $r['published'][0]['publisher'] .'</span>';

		if( isset( $r['published'][0]['cy'] ))
			$pubdeets[] = '<span class="pubyear">'. $r['published'][0]['cy'] .'</span>';

		if( count( $pubdeets ))
			$result .= '<li class="publication_details"><h3>Publication Details</h3>'. implode( '<span class="meta-sep">, </span>', $pubdeets ) .'</li>';

		if( is_array( $parsed['idnumbers'] ))
			$result .= '<li class="availability"><h3>Availability</h3><ul>'. apply_filters( 'scrib_availability_content', '', $id, $parsed['idnumbers']) .'</ul></li>';

		if( isset( $r['callnumbers'][0]['number'] )){
			$result .= '<li class="callnumber">'. ( 1 < count( $r['callnumbers'] ) ? '<h3>Call Numbers</h3>' : '<h3>Call Number</h3>') .'<ul>';
			foreach( $r['callnumbers'] as $temp )
				$result .= '<li class="call-number-'. $temp['type'] .'">' . $temp['number'] .' ('. $temp['type'] .')'. ( isset( $temp['location'] ) ? ', '. $temp['location'] : '' ) .'</li>';
			$result .= '</ul></li>';
		}

		if( isset( $r['linked_urls'][0]['href'] )){
			$result .= '<li class="linked_urls">'. ( 1 < count( $r['linked_urls'] ) ? '<h3>Links</h3>' : '<h3>Link</h3>' ) .'<ul>';
			foreach( $r['linked_urls'] as $temp )
				$result .= '<li><a href="' . $temp['href'] .'" title="go to this linked website">' . $temp['name'] .'</a></li>';
			$result .= '</ul></li>';
		}

		if( isset( $parsed['description'][0] )){
			$result .= '<li class="description"><h3>Description</h3>' . $parsed['description'][0] .'</li>';
		}

		// authors or, er, creators
		if( isset( $r['creator'][0]['name'] )){
			$result .= '<li class="creator">'. ( 1 < count( $r['creator'] ) ? '<h3>Authors</h3>' : '<h3>Author</h3>') .'<ul>';
			foreach( $r['creator'] as $temp ){
				$result .= '<li><a href="'. $scrib->get_tag_link( array( 'taxonomy' => 'creator', 'slug' => urlencode( $temp['name'] ))).'" rel="tag">' . $temp['name'] . '</a>' . ( 'Author' <> $temp['role'] ? ', ' . $temp['role'] : '' ) .'</li>';
			}
			$result .= '</ul></li>';
		}

//print_r( $parsed );

		if( isset( $parsed['genre'] )){
			$result .= '<li class="genre"><h3>Genre</h3><ul>';
			foreach( $parsed['genre'] as $temp )
				$result .= '<li><a href="'. $scrib->get_tag_link( array( 'taxonomy' => $temp['type'], 'slug' => urlencode( $temp['value'] ))).'" rel="tag">' . $temp['value'] . '</a></li>';
			$result .= '</ul></li>';
		}
		if( isset( $parsed['person'] )){
			$result .= '<li class="person"><h3>People and Characters</h3><ul>';
			foreach( $parsed['person'] as $temp )
				$result .= '<li><a href="'. $scrib->get_tag_link( array( 'taxonomy' => $temp['type'], 'slug' => urlencode( $temp['value'] ))).'" rel="tag">' . $temp['value'] . '</a></li>';
			$result .= '</ul></li>';
		}
		if( isset( $parsed['place'] )){
			$result .= '<li class="place"><h3>Place</h3><ul>';
			foreach( $parsed['place'] as $temp )
				$result .= '<li><a href="'. $scrib->get_tag_link( array( 'taxonomy' => $temp['type'], 'slug' => urlencode( $temp['value'] ))).'" rel="tag">' . $temp['value'] . '</a></li>';
			$result .= '</ul></li>';
		}
		if( isset( $parsed['time'] )){
			$result .= '<li class="time"><h3>Time</h3><ul>';
			foreach( $parsed['time'] as $temp )
				$result .= '<li><a href="'. $scrib->get_tag_link( array( 'taxonomy' => $temp['type'], 'slug' => urlencode( $temp['value'] ))).'" rel="tag">' . $temp['value'] . '</a></li>';
			$result .= '</ul></li>';
		}
		if( isset( $parsed['subject'] )){
			$result .= '<li class="subject"><h3>Subject</h3><ul>';
			foreach( $parsed['subject'] as $temp ){
				$temptext = $templink = array();
				foreach( $temp as $temptoo ){
					$templink[ $temptoo['type'] ][] = $temptoo['value'];
					$temptext[] = '<span>'. $temptoo['value'] . '</span>';
				}
				$result .= '<li><a href="'. $scrib->get_search_link( $templink ) .'" title="Search for other items matching this subject.">'. implode( ' &mdash; ', $temptext ) .'</a></li>';
			}
			$result .= '</ul></li>';
		}

		// do the notes and contents
		if( isset( $parsed['notes'] )){
			$result .= '<li class="notes"><h3>Notes</h3><ul>';
			foreach( $parsed['notes'] as $temp )
				$result .= '<li>' . $temp . '</li>';
			$result .= '</ul></li>';
		}

		if( isset( $parsed['contents'][0] )){
			$result .= '<li class="contents"><h3>Contents</h3>' . $parsed['contents'][0] .'</li>';
		}


		// handle most of the standard numbers
		if( isset( $parsed['idnumbers']['isbn'] )){
			$result .= '<li class="isbn"><h3>ISBN</h3><ul>';
			foreach( $parsed['idnumbers']['isbn'] as $temp )
				$result .= '<li id="isbn-'. strtolower( $temp ) .'">'. strtolower( $temp ) . '</li>';
			$result .= '</ul></li>';
		}
		if( isset( $parsed['idnumbers']['issn'] )){
			$result .= '<li class="issn"><h3>ISSN</h3><ul>';
			foreach( $parsed['idnumbers']['issn'] as $temp )
				$result .= '<li id="issn-'. strtolower( $temp ) .'">'. strtolower( $temp ) . '</li>';
			$result .= '</ul></li>';
		}
		if( isset( $parsed['idnumbers']['lccn'] )){
			$result .= '<li class="lccn"><h3>LCCN</h3><ul>';
			foreach( $parsed['idnumbers']['lccn'] as $temp )
				$result .= '<li id="lccn-'. $temp .'"><a href="http://lccn.loc.gov/'. urlencode( $temp ) .'?referer=scriblio" rel="tag">'. $temp .'</a></li>';
			$result .= '</ul></li>';
		}
		if( isset( $parsed['idnumbers']['olid'] )){
			$result .= '<li class="olid"><h3>Open Library ID</h3><ul>';
			foreach( $parsed['idnumbers']['lccn'] as $temp )
				$result .= '<li id="olid-'. $temp .'" ><a href="http://openlibrary.org'. $temp .'?referer=scriblio" rel="tag">'. $temp .'</a></li>';
			$result .= '</ul></li>';
		}

		if( $temp = $bsuite->bsuggestive_bypageviews_the_related() )
			$result .= '<li class="related_othersbrowsed"><h3>People Who Looked At This Also Looked At</h3><ul>'. $temp .'</ul></li>';

		if( $temp = $bsuite->bsuggestive_the_related() )
			$result .= '<li class="related_similar"><h3>Similar Items</h3><ul>'. $temp .'</ul></li>';

		$result .= '</ul>';
		$result = convert_chars( wptexturize( $result ));

		return $result ;
	}

	public function parse_words( &$r ){
		$parsed = $this->parse_parts( $r );

		$result = '';
		if( isset( $r['title'][0]['a'] ))
			foreach( $r['title'] as $temp )
				$result .= $temp['a'] . "\n";

		if( isset( $r['attribution'][0]['a'] ))
			$result .= $r['attribution'][0]['a'] ."\n";

		if( isset( $r['callnumbers'][0]['number'] ))
			foreach( $r['callnumbers'] as $temp )
				$result .= $temp['number'] ."\n";

		if( isset( $r['creator'][0]['name'] ))
			foreach( $r['creator'] as $temp )
				$result .= $temp['name'] ."\n";

		if( isset( $parsed['subject'] )){
			foreach( $parsed['subject'] as $temp ){
				$temptext = array();
				foreach( $temp as $temptoo )
					$temptext[] = $temptoo['value'];
				$result .= implode( ' -- ', $temptext ) ."\n";
			}
		}


		foreach( $r['text'] as $temp )
			$result .= $temp['content'] ."\n";

		foreach( $r['idnumbers'] as $temp )
			$result .= $temp['id'] ."\n";

		return( wp_filter_nohtml_kses( $result ));
	}

	public function the_author_filter( $content ){
		global $id;

		if( $id && ( $r = get_post_meta( $id, 'scrib_meditor_content', true )) && isset( $r['marcish']['attribution'][0]['a'] ))
			return( $r['marcish']['attribution'][0]['a'] );
		else
			return( $content );
	}

	public function author_link_filter( $content ){
		global $id, $scrib;

		if( $id && ( $r = get_post_meta( $id, 'scrib_meditor_content', true )) && is_array( $r['marcish']['creator'] )){
			$terms = wp_get_object_terms( $id, 'creator' );
			foreach( $terms as $term )
				$tag['creator'][] = $term->name;

			return( $scrib->get_search_link( $tag ));
		}else{
			return( $content );
		}
	}


	public function save_record( $post_id , $r ) {
		global $scrib;

		$stopwords = array( 'and', 'the', 'new', 'use', 'for', 'united', 'states' );

		$facets = array();
		if ( is_array( $r['marcish'] )){

			$facets['partial'] =
			$facets['nom'] =
			$facets['creator'] =
			$facets['lang'] =
			$facets['cy'] =
			$facets['cm'] =
			$facets['format'] =
			$facets['subject'] =
			$facets['genre'] =
			$facets['person'] =
			$facets['place'] =
			$facets['time'] =
			$facets['exhibit'] =
			$facets['award'] =
			$facets['readinglevel'] =
			$facets['sy'] =
			$facets['sm'] =
			$facets['sd'] =
			$facets['collection'] =
			$facets['sourceid'] =
			$facets['isbn'] =
			$facets['issn'] =
			$facets['lccn'] =
			$facets['asin'] =
			$facets['ean'] =
			$facets['olid'] =
			$facets['oclc'] = array();


			$parsed = $this->parse_parts( $r['marcish'] );

			// creators
			if( isset( $r['marcish']['creator'][0] )){
				foreach( $r['marcish']['creator'] as $temp )
					if( !empty( $temp['name'] )){
						$facets['creator'][] = $temp['name'];

						if( $tempsplit = preg_split( '/[ |,|;|-]/', $temp['name'] ))
							foreach( $tempsplit as $tempsplittoo )
								if( !empty( $tempsplittoo ) && !is_numeric( $tempsplittoo ) && ( 2 < strlen( $tempsplittoo )) && ( !in_array( strtolower( $tempsplittoo ), $stopwords )))
									$facets['partial'][] = $scrib->meditor_sanitize_punctuation( $tempsplittoo );
					}
			}

			// Title
			if( isset( $r['marcish']['title'][0] )){
				foreach( $r['marcish']['title'] as $temp ){
					$facets['nom'][] = $temp['a'];
					$facets['nom'][] = $scrib->meditor_strip_initial_articles( $temp['a'] );
				}
			}

			// Language
			if( isset( $r['marcish']['published'][0]['lang'] ))
				$facets['lang'][] = $r['marcish']['published'][0]['lang'];

			// Dates
			if( isset( $r['marcish']['published'][0]['cy'] )){
				$facets['cy'][] = $r['marcish']['published'][0]['cy'];
				$facets['cy'][] = substr( $r['marcish']['published'][0]['cy'], 0, -1 ) .'0s';
				$facets['cy'][] = substr( $r['marcish']['published'][0]['cy'], 0, -2 ) .'00s';
			}
			if( isset( $r['marcish']['published'][0]['cm'] ))
				$facets['cm'][] = date( 'F', strtotime( '2008-'. $r['marcish']['published'][0]['cm'] .'-01' ));

			if( isset( $r['marcish']['subject_date'][0] )){
				foreach( $r['marcish']['subject_date'] as $temp ){
					if( isset( $temp['y'] )){
						$facets['sy'][] = $temp['y'];
						$facets['sy'][] = substr( $temp['y'], 0, -1 ) .'0s';
						$facets['sy'][] = substr( $temp['y'], 0, -2 ) .'00s';
					}
					if( isset( $temp['m'] ))
						$facets['sm'][] = date( 'F', strtotime( '2008-'. $temp['m'] .'-01' ));
					if( isset( $temp['d'] ))
						$facets['sd'][] = date( 'F', strtotime( '2008-'. $temp['m'] .'-01' ));
				}
			}

			// Subjects
			if( isset( $parsed['subjkey'][0] )){
				foreach( $parsed['subjkey'] as $sk => $sv ){
					$facets[ $sv['type'] ][] = $sv['value'];
//					$facets['partial'][] = $sv['value'];

					if( $tempsplit = preg_split( '/[ |,|;|-]/', $sv['value'] ))
						foreach( $tempsplit as $tempsplittoo )
							if( !empty( $tempsplittoo )
								&& !is_numeric( $tempsplittoo )
								&& ( 2 < strlen( $tempsplittoo ))
								&& ( !in_array( strtolower( $tempsplittoo ), $stopwords )))
									$facets['partial'][] = $scrib->meditor_sanitize_punctuation( $tempsplittoo );
				}
			}

			// Standard Numbers
			if ( isset( $parsed['idnumbers']['sourceid'] ))
				$facets['sourceid'] = $parsed['idnumbers']['sourceid'];
			if ( isset( $parsed['idnumbers']['lccn'] ))
				$facets['lccn'] = $parsed['idnumbers']['lccn'];
			if ( isset( $parsed['idnumbers']['isbn'] ))
				$facets['isbn'] = $parsed['idnumbers']['isbn'];
			if ( isset( $parsed['idnumbers']['issn'] ))
				$facets['issn'] = $parsed['idnumbers']['issn'];
			if ( isset( $parsed['idnumbers']['asin'] ))
				$facets['asin'] = $parsed['idnumbers']['asin'];
			if ( isset( $parsed['idnumbers']['olid'] ))
				$facets['olid'] = $parsed['idnumbers']['olid'];

			foreach( $r['marcish']['idnumbers'] as $temp ){
				switch( $temp['type'] ) {
					case 'sourceid' :
					case 'isbn' :
					case 'issn' :
					case 'lccn' :
					case 'asin' :
					case 'ean' :
					case 'oclc' :
						if( !empty( $temp['id'] ))
							$facets[ $temp['type'] ][] = $temp['id'];
						break;
				}
			}

			// Format
			if( isset( $r['marcish']['format'][0] ))
				foreach( $r['marcish']['format'] as $temp ){
					unset( $temp['src'] );
					foreach( $temp as $temptoo )
						if( !empty( $temptoo ))
							$facets['format'][] = $temptoo;
				}

			// Related records
			if( isset( $r['marcish']['related'][0]['record'] ))
				foreach( $r['marcish']['related'] as $temp )
					$this->update_related( $post_id, $temp );

			// Collection
			if( isset( $r['marcish']['source'][0]['collection'] ))
				$facets['collection'][] = $r['marcish']['source'][0]['collection'];
		}

		if ( count( $facets )){
			foreach( $facets as $taxonomy => $tags ){

				if( 'tag' == $taxonomy )
					$taxonomy = 'post_tag';

				if( 'post_tag' == $taxonomy ){
					wp_set_post_tags( $post_id, $tags, TRUE );
					continue;
				}

				wp_set_object_terms( $post_id, array_unique( array_filter( $tags )), $taxonomy, FALSE );
			}
		}
	}

	public function update_related( &$from_post_id, &$rel ) {
		global $scrib;
	
		if( absint( $rel['record'] ) && ( $r = get_post_meta( absint( $rel['record'] ), 'scrib_meditor_content', TRUE )) && ( is_array( $r['marcish'] )) ){
			if( is_string( $r ))
				$r = unserialize( $r );

			if( $scrib->meditor_forms['marcish']['_relationships'][ $rel['rel'] ]['_rel_inverse'] ){
				$r['marcish']['related'][] = array( 'rel' => $scrib->meditor_forms['marcish']['_relationships'][ $rel['rel'] ]['_rel_inverse'], 'record' => (string) $from_post_id );

				$r['marcish']['related'] = $scrib->array_unique_deep( $r['marcish']['related'] );

				update_post_meta( absint( $rel['record'] ), 'scrib_meditor_content', $r );
			}
		}
	}

	public function add_parent( &$r, &$from ) {
		// the new record is the parent, the old record is the child
		if ( is_array( $r['marcish'] )){
			unset( $r['marcish']['title'] );
			unset( $r['marcish']['text'] );
			unset( $r['marcish']['source']['file'] );

			unset( $r['marcish']['related'] );

			$r['marcish']['related'][0] = array( 'rel' => 'child', 'record' => $from);
		}
		return( $r );
	}

	public function add_child( &$r, &$from ) {
		// the new record is the child, the old record is the parent
		if ( is_array( $r['marcish'] )){
			unset( $r['marcish']['title'] );
			unset( $r['marcish']['text'] );
			unset( $r['marcish']['source']['file'] );

			unset( $r['marcish']['related'] );

			$r['marcish']['related'][0] = array( 'rel' => 'parent', 'record' => $from);
		}
		return( $r );
	}

	public function add_next( &$r, &$from ) {
		// the new record is the next page in a series, the old record is the previous
		if ( is_array( $r['marcish'] )){
			unset( $r['marcish']['title'] );
			unset( $r['marcish']['text'] );
			unset( $r['marcish']['source']['file'] );

			unset( $r['marcish']['related'] );

			$r['marcish']['related'][0] = array( 'rel' => 'previous', 'record' => $from);
		}
		return( $r );
	}

	public function add_previous( &$r, &$from ) {
		// the new record is the previous page in a series, the old record is the next
		if ( is_array( $r['marcish'] )){
			unset( $r['marcish']['title'] );
			unset( $r['marcish']['text'] );
			unset( $r['marcish']['source']['file'] );

			unset( $r['marcish']['related'] );

			$r['marcish']['related'][0] = array( 'rel' => 'next', 'record' => $from);
		}
		return( $r );
	}

	public function add_reverse( &$r, &$from ) {
		// the new record is the reverse, the old record is the reverse
		if ( is_array( $r['marcish'] )){
			unset( $r['marcish']['title'] );
			unset( $r['marcish']['text'] );
			unset( $r['marcish']['source']['file'] );

			unset( $r['marcish']['related'] );

			$r['marcish']['related'][0] = array( 'rel' => 'reverse', 'record' => $from);
		}
		return( $r );
	}

	public function add_sibling( &$r, &$from ) {
		// the new record is the reverse, the old record is the reverse
		if ( is_array( $r['marcish'] )){
			unset( $r['marcish']['title'] );
			unset( $r['marcish']['text'] );
			unset( $r['marcish']['source']['file'] );

			unset( $r['marcish']['related'] );
		}
		return( $r );
	}

	public function availability( &$content, $post_id, &$idnumbers ) {
		if( isset( $idnumbers['issn'][0] ))
			$gbs_key = 'issn:'. $idnumbers['issn'][0];
		else if( isset( $idnumbers['isbn'][0] ))
			$gbs_key = 'isbn:'. $idnumbers['isbn'][0];
		else if( isset( $idnumbers['lccn'][0] ))
			$gbs_key = 'lccn:'. $idnumbers['lccn'][0];

		if( $gbs_key ){
			$this->gbs_keys[] = $gbs_key;

			return( $content . '<li id="gbs_'. str_replace( array(':', ' '), '_', $gbs_key ) .'" class="gbs_link"></li>' );
		}

		return( $content );
	}

	public function availability_gbslink(){
		if( count( $this->gbs_keys ))
			echo '<script src="http://books.google.com/books?bibkeys='. urlencode( implode( ',', array_unique( $this->gbs_keys ))) .'&jscmd=viewapi&callback=jQuery.GBDisplay"></script>';
	}

	public function textthis(){
		global $post;

		/* get the SMS config */
		require_once(ABSPATH . PLUGINDIR .'/'. plugin_basename(dirname(__FILE__)) .'/conf_sms.php');

		/* prepare the SMS message */
		$sms[] = $scribsms_content_pre . "\n";
		$sms[] = strlen( $post->post_title ) > 30 ? trim( substr( $post->post_title, 0, 30 )) . '...' : trim( substr( $post->post_title, 0, 30 ));
		if( ( $sourceid = wp_get_object_terms( $post->ID, 'sourceid' )) && ( count( wp_get_object_terms( $post->ID, 'sourceid' ))));
			$sms[] = scrib_availability( $sourceid[0]->name ) . "\n";
		$sms[] = get_permalink( $post->ID ) . "\n";
		$sms = substr( implode( array_filter( array_map( 'trim', $sms )), "\n" ), 0, 450 );

		/* create the replacement post content */
		$content = '<br />';

		/* send the message if we have a destination phone number */
		if( isset( $_POST['textthis_smsto'] ) && strlen( ereg_replace( '[^0-9]', '', $_POST['textthis_smsto'] )) == 11 ){
			$mysms = new bSuite_sms( $scribsms_api_id, $scribsms_user, $scribsms_pass );
			if( $mysms->send( $sms, ereg_replace( '[^0-9]', '', $_POST['textthis_smsto'] )))
				$content .= '<h3 class="notice">Success! Your message was sent to '. ereg_replace( '[^0-9]', '', $_REQUEST['textthis_smsto'] ) .'.</h3>';
			else
				$content .= '<h3 class="error">Error: there was an error sending the message.</h3>';

//print_r( $mysms );
//echo $mysms->querymsg( $mysms->last_id );
		}else if( isset( $_REQUEST['textthis_smsto'] )){
			$content .= '<h3 class="error">Error: please enter a complete phone number.</h3>';
		}

		/* create the form to input the destination number */
		$content .= '
<h3>Send information about this item as an SMS text message.</h3>
<form id="textthis_form" name="textthis_form" action="'. add_query_arg( 'textthis', '1', get_permalink( $post->ID )) .'" method="post">
<p><label for="textthis_smsto">Your cell phone number: <input id="textthis_smsto" name="textthis_smsto" type="text" value="1-XXX-XXX-XXXX" /></label>
<input id="textthis_submit" name="textthis_submit" type="submit" value="Send it!" /></p>
</form>

<h3>Message Preview</h3>
<blockquote><pre>'. $sms .'</pre></blockquote>

<h3>Please Note</h3>
<p>Sending messages is free, but your mobile service provider may charge you to receive the messages. Please check your plan details before continuing.</p>
<p>Unfortunately, you cannot reply to any <a href="http://en.wikipedia.org/wiki/Short_message_service">SMS text messages</a> you receive using this service.</p>
<p>Messaging services are provided by <a href="http://www.clickatell.com/">Clickatell</a> and are subject to their <a href="http://www.clickatell.com/company/privacy.php">privacy policy</a>.</p>
		';
		return( $content );
	}

	public function textthis_redirect(){
		global $wp_query;

		if( !empty( $_REQUEST['textthis'] ) && !empty( $wp_query->query_vars['p'] )){
			if( !$textthis = $this->textthis() )
				return( FALSE );

			if(!ereg( '^'.__('Text This', 'Scrib'), $wp_query->post->post_title ))
				$wp_query->post->post_title = $wp_query->posts[0]->post_title = __('Text This', 'Scrib') .': '. $wp_query->post->post_title;

			$wp_query->post->post_content = $textthis;
			$wp_query->posts[0]->post_content = $textthis;

			$wp_query->post->comment_status = 'closed';
			$wp_query->posts[0]->comment_status = 'closed';
		}
	}
	// end sharelinks related functions

	public function is_scrib(){
		global $id;
		if( $id && ( $r = get_post_meta( $id, 'scrib_meditor_content', true )) && is_array( $r['marcish'] ))
			return(TRUE);
		else
			return(FALSE);
	}
}

// now instantiate this object
$scrib_marcish = & new ScribSchemaMarcish;


function is_scrib( $post_id = '' ) {
	global $scrib_marcish;
	return( $scrib_marcish->is_scrib( $post_id ) );
}

function scrib_the_related(){
	global $scrib_marcish;
	echo $scrib_marcish->the_related_bookjackets();
}
