<?php

/*  Copyright 2005-2012  Casey Bisson

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

error_reporting(E_ERROR);
//error_reporting(E_ALL);


class Meditor
{

	var $forms = array();
	var $initial_articles = array( '/^a /i','/^an /i','/^da /i','/^de /i','/^the /i','/^ye /i' );

	function __construct()
	{
		global $wpdb;

		// establish web path to this plugin's directory
		$this->path_web = plugins_url( basename( dirname( __FILE__ )));

		// register WordPress hooks
		add_action('init', array(&$this, 'init'));

		add_action('wp_ajax_meditor_suggest_tags', array( &$this, 'suggest_tags' ));

		add_action('admin_menu', array( &$this, 'admin_menu_hook' ));

		add_action('save_post', array(&$this, 'save_post'), 2, 2);
		add_filter('pre_post_title', array(&$this, 'pre_save_filters'));
		add_filter('pre_post_excerpt', array(&$this, 'pre_save_filters'));
		add_filter('pre_post_content', array(&$this, 'pre_save_filters'));

		add_action('wp_footer', array(&$this, 'wp_footer_js'));
		// end register WordPress hooks
	}

	function init()
	{
	}

	function admin_menu_hook() {
		// register the meditor box in the post and page editors
		add_meta_box('scrib_meditor_div', __('Scriblio Metadata Editor'), array( &$this, 'metabox' ), 'post', 'advanced', 'high');
		add_meta_box('scrib_meditor_div', __('Scriblio Metadata Editor'), array( &$this, 'metabox' ), 'page', 'advanced', 'high');

		wp_register_style( 'scrib-editor', $this->path_web .'/css/editor.css' );
		wp_enqueue_style( 'scrib-editor' );

		wp_register_script( 'scrib-editor', $this->path_web . '/js/editor.js', array('jquery-ui-sortable'), '1' );
		wp_enqueue_script( 'scrib-editor' );

		wp_register_script( 'jquery-tabindex', $this->path_web . '/js/jquery.keyboard-a11y.js', array('jquery'), '1' );
		wp_enqueue_script( 'jquery-tabindex' );
	}

	function metabox()
	{
		global $post_ID;
		if( $post_ID && ( $data = get_post_meta( $post_ID, 'scrib_meditor_content', true )) )
		{
			if( is_string( $data ))
				$data = unserialize( $data );

			foreach( $data as $handle => $val )
				if( isset( $this->forms[ $handle ] ))
					$this->form( $handle, $this->forms[ $handle ], $val );

		}
		else if( isset( $this->forms[ $_GET['scrib_meditor_form'] ] ))
		{
			$this->form( $_GET['scrib_meditor_form'], $this->forms[ $_GET['scrib_meditor_form'] ] );

		}
		else if( absint( $_GET['scrib_meditor_from'] ) && ( $data = get_post_meta( absint( $_GET['scrib_meditor_from'] ), 'scrib_meditor_content', true )) )
		{
			if( !empty( $_GET['scrib_meditor_add'] ))
				$data = apply_filters( 'scrib_meditor_add_'. preg_replace( '/[^a-z0-9]/i', '', $_GET['scrib_meditor_add'] ), $data, absint( $_GET['scrib_meditor_from'] ));
			foreach( $data as $handle => $val )
				if( isset( $this->forms[ $handle ] ))
					$this->form( $handle, $this->forms[ $handle ], $val );
		}
	}

	function form( $handle, &$prototype, &$data = array() )
	{
		add_action( 'admin_footer', array( &$this, 'footer_activatejs' ));

		echo '<ul id="scrib_meditor">';
		foreach( $prototype['_elements'] as $key => $val )
		{
			$val = is_array( $data[ $key ] ) ? $data[ $key ] : array( array() );
			echo '<li id="scrib_meditor-'. $handle .'-'. $key .'" class="fieldset_title">'.  ( $prototype['_elements'][ $key ]['_title'] ? '<h2>'. $prototype['_elements'][ $key ]['_title'] .'</h2>' : '' ) . ( $prototype['_elements'][ $key ]['_description'] ? '<div class="description">'. $prototype['_elements'][ $key ]['_description'] .'</div>' : '' ) .'<ul  class="fieldset_title'. ( $prototype['_elements'][ $key ]['_repeatable'] ? ' sortable">' : '">' );
			foreach( $val as $ordinal => $row )
				$this->form_sub( $handle, $prototype['_elements'][ $key ], $row, $key, $ordinal );
			echo '</ul></li>';
		}
		echo '</ul><p class="scrib_meditor_end" />';

		do_action( 'scrib_meditor_form_'. $handle );
	}

	function form_sub( $handle, $prototype, $data, $fieldset, $ordinal )
	{
		static $tabindex = 1;

		echo '<li class="fieldset '. ( $prototype['_repeatable'] ? 'repeatable ' : '' ) . $handle .' '. $fieldset .'"><ul class="fieldset">';
		foreach( $prototype['_elements'] as $key => $val ){
			$id =  "scrib_meditor-$handle-$fieldset-$ordinal-$key";
			$name =  "scrib_meditor[$handle][$fieldset][$ordinal][$key]";

			$val = $data[ $key ] ? stripslashes( $data[ $key ] ) : $prototype['_elements'][ $key ]['_input']['_default'];

			echo '<li class="field '. $handle .' '. $fieldset .' '. $key .'">'. ( $prototype['_elements'][ $key ]['_title'] ? '<label for="'. $id .'">'. $prototype['_elements'][ $key ]['_title'] .'</label>' : '<br />');

			switch( $prototype['_elements'][ $key ]['_input']['_type'] )
			{

				case 'select':
					echo '<select name="'. $name .'" id="'. $id .'" tabindex="'. $tabindex .'">';
					foreach( $prototype['_elements'][ $key ]['_input']['_values'] as $selval => $selname )
						echo '<option '. ( $selval == $val ? 'selected="selected" ' : '' ) .'value="'. $selval .'">'. $selname .'</option>';
					echo '</select>';
					break;

				case 'checkbox':
					echo '<input type="checkbox" name="'. $name .'" id="'. $id .'" value="1"'. ( $val ? ' checked="checked"' : '' ) .'  tabindex="'. $tabindex .'" />';
					break;

				case 'textarea':
					echo '<textarea name="'. $name .'" id="'. $id .'"  tabindex="'. $tabindex .'">'. format_to_edit( $val ) .'</textarea>';
					break;

				case '_function':
					if( is_callable( $prototype['_elements'][ $key ]['_input']['_function'] ))
						call_user_func( $prototype['_elements'][ $key ]['_input']['_function'] , $val, $handle, $id, $name );
					else
						_e( 'the requested function could not be called' );
					break;

				case 'text':
				default:
					echo '<input type="text" name="'. $name .'" id="'. $id .'" value="'. format_to_edit( $val ) .'"  '. ( $prototype['_elements'][ $key ]['_input']['autocomplete'] == 'off' ? 'autocomplete="off"' : '' ) .' tabindex="'. $tabindex .'" />';

			}
			echo '</li>';

			if( isset( $prototype['_elements'][ $key ]['_suggest'] ) )
				$this->suggest_js[ $handle .'-'. $fieldset .'-'. $key ] = 'jQuery("#scrib_meditor-'. $handle .'-'. $fieldset .' li.'. $key .' input").suggest( "admin-ajax.php?action=meditor_suggest_tags&tax='. $prototype['_elements'][ $key ]['_suggest'] .'", { delay: 500, minchars: 2 } );';

			$tabindex++;
		}
		echo '</ul></li>';

	}

	function add_related_commandlinks( $null, $handle )
	{
		global $post_ID;
		if( $post_ID )
		{
			echo '<p id="scrib_meditor_addrelated">';
			foreach( $this->forms[ $handle ]['_relationships'] as $rkey => $relationship )
				echo '<a href="'. admin_url( 'post-new.php?scrib_meditor_add='. $rkey .'&scrib_meditor_from='. $post_ID ) .'">+ '. $relationship['_title'] .'</a> &nbsp; ';
			echo '</p>';
		}
		else
		{
			echo '<p id="scrib_meditor_addrelated_needsid">'. __( 'Save this record before attempting to add a related record.', 'scrib' ) .'</p>';
		}
	}

	function save_post($post_id, $post)
	{
		if ( $post_id && is_array( $_REQUEST['scrib_meditor'] ))
		{

			// make sure meta is added to the post, not a revision
			if ( $the_post = wp_is_post_revision( $post_id ))
				$post_id = $the_post;

			$record = is_array( get_post_meta( $post_id, 'scrib_meditor_content', true )) ? get_post_meta( $post_id, 'scrib_meditor_content', true ) : array();

			if( is_array( $_REQUEST['scrib_meditor'] )){
				foreach( $_REQUEST['scrib_meditor'] as $key => &$val )
					unset( $record[ $this->input->form_key ] );

				$record = $this->merge_meta( $record, $this->sanitize_input( $_REQUEST['scrib_meditor'] ));

				add_post_meta( $post_id, 'scrib_meditor_content', $record, TRUE ) or update_post_meta( $post_id, 'scrib_meditor_content', $record );

				do_action( 'scrib_meditor_save_record', $post_id, $record );
			}

/*
			foreach( $_REQUEST['scrib_meditor'] as $this->input->form_key => $this->input->form ){
				unset( $record[ $this->input->form_key ] );
				foreach( $this->input->form as $this->input->group_key => $this->input->group )
					foreach( $this->input->group as $this->input->iteration_key => $this->input->iteration )
						foreach( $this->input->iteration as $this->input->key => $this->input->val ){
							if( is_callable( $this->forms[ $this->input->form_key ]['_elements'][ $this->input->group_key ]['_elements'][ $this->input->key ]['_sanitize'] )){
								$filtered = FALSE;

								$filtered = call_user_func( $this->forms[ $this->input->form_key ]['_elements'][ $this->input->group_key ]['_elements'][ $this->input->key ]['_sanitize'] , stripslashes( $this->input->val ));

								if( !empty( $filtered ))
									$record[ $this->input->form_key ][ $this->input->group_key ][ $this->input->iteration_key ][ $this->input->key ] = stripslashes( $filtered );
							}else{
								if( !empty( $record[ $this->input->form_key ][ $this->input->group_key ][ $this->input->key ][ $this->input->iteration_key ][ $this->input->key ] ))
									$record[ $this->input->form_key ][ $this->input->group_key ][ $this->input->key ][ $this->input->iteration_key ][ $this->input->key ] = stripslashes( $this->input->val );
							}
						}
			}

			add_post_meta( $post_id, 'scrib_meditor_content', $record, TRUE ) or update_post_meta( $post_id, 'scrib_meditor_content', $record );

			do_action( 'scrib_meditor_save_record', $post_id, $record );
*/
		}
	}

	function merge_meta( $orig = array(), $new = array(), $nsourceid = FALSE )
	{

		$orig = apply_filters( 'scrib_meditor_premerge_old', $orig , $nsourceid );
		$new = apply_filters( 'scrib_meditor_premerge_new', $new , $nsourceid );

		if( $forms = array_intersect( array_keys( $orig ), array_keys( $new )))
		{
			$return = array();
			foreach( $forms as $form )
			{
				$sections = array_unique( array_merge( array_keys( $orig[ $form ] ), array_keys( $new[ $form ] )));

				foreach( $sections as $section )
				{
					// preserve the bits that are to be suppressed
					$suppress = array();
					foreach( $orig[ $form ][ $section ] as $key => $val )
						if( $val['suppress'] )
							$suppress[ $form ][ $section ][ $key ] = $val;

					// remove metadata that's sourced from the new sourceid
					if( $nsourceid )
						foreach( $orig[ $form ][ $section ] as $key => $val )
							if( isset( $val['src'] ) && ( $val['src'] == $nsourceid ))
								unset( $orig[ $form ][ $section ][ $key ] );

					$return[ $form ][ $section ] = $this->array_unique_deep( array_merge( count( $new[ $form ][ $section ] ) ? $new[ $form ][ $section ] : array() , count( $orig[ $form ][ $section ] ) ? $orig[ $form ][ $section ] : array() , $suppress ));
				}
			}

			if( $diff = array_diff( array_keys( $orig ), array_keys( $new )))
			{
				foreach( $diff as $form )
					$return[ $form ] = array_merge( is_array( $orig[ $form ] ) ? $orig[ $form ] : array(), is_array( $new[ $form ] ) ? $new[ $form ] : array() );
			}

			return( $return );

		}
		else
		{
			return( array_merge( is_array( $orig ) ? $orig : array(), is_array( $new ) ? $new : array() ));
		}
	}

	function sanitize_input( &$input )
	{
		$record = array();
		foreach( $input as $this->input->form_key => $this->input->form )
		{
			foreach( $this->input->form as $this->input->group_key => $this->input->group )
			{
				foreach( $this->input->group as $this->input->iteration_key => $this->input->iteration )
				{
					foreach( $this->input->iteration as $this->input->key => $this->input->val )
					{
						if( is_callable( $this->forms[ $this->input->form_key ]['_elements'][ $this->input->group_key ]['_elements'][ $this->input->key ]['_sanitize'] ))
						{
							$filtered = FALSE;

							$filtered = call_user_func( $this->forms[ $this->input->form_key ]['_elements'][ $this->input->group_key ]['_elements'][ $this->input->key ]['_sanitize'] , stripslashes( $this->input->val ));

							if( !empty( $filtered ))
								$record[ $this->input->form_key ][ $this->input->group_key ][ $this->input->iteration_key ][ $this->input->key ] = stripslashes( $filtered );
						}
						else
						{
							if( !empty( $record[ $this->input->form_key ][ $this->input->group_key ][ $this->input->key ][ $this->input->iteration_key ][ $this->input->key ] ))
								$record[ $this->input->form_key ][ $this->input->group_key ][ $this->input->key ][ $this->input->iteration_key ][ $this->input->key ] = stripslashes( $this->input->val );
						}
					}
				}
			}
		}

		return apply_filters( 'scrib_meditor_sanitize_record', $record );
	}

	function sanitize_month( $val )
	{
		if( !is_numeric( $val ) && !empty( $val ))
		{
			if( strtotime( $val .' 2008' ))
				return( date( 'm', strtotime( $val .' 2008' )));
		}
		else
		{
			$val = absint( $val );
			if( $val > 0 &&  $val < 13 )
				return( $val );
		}
		return( FALSE );
	}

	function sanitize_day( $val )
	{
		$val = absint( $val );
		if( $val > 0 &&  $val < 32 )
			return( $val );
		return( FALSE );
	}

	function sanitize_selectlist( $val ){
		if( array_key_exists( $val, $this->forms[ $this->input->form_key ]['_elements'][ $this->input->group_key ]['_elements'][ $this->input->key ]['_input']['_values'] ))
			return( $val );

		return( FALSE );
	}

	function sanitize_punctuation( $str )
	{
		// props to K. T. Lam of HKUST

		$str = html_entity_decode( $str );

/*
		//strip html entities, i.e. &#59;
		$htmlentity = '\&\#\d\d\;';
		$lead_htmlentity_pattern = '/^'.$htmlentity.'/';
		$trail_htmlentity_pattern = '/'.$htmlentity.'$/';
		$str = preg_replace($lead_htmlentity_pattern, '', preg_replace($trail_htmlentity_pattern, '', $str));
*/

		//strip ASCII punctuations
		$puncts = '\s\~\!\@\#\$\%\^\&\*\_\+\`\-\=\{\}\|\[\]\\\:\"\;\'\<\>\?\,\.\/';
		$lead_puncts_pattern = '/^['.$puncts.']+/';
		$trail_puncts_pattern = '/['.$puncts.']+$/';
		$str = preg_replace($trail_puncts_pattern, '', preg_replace($lead_puncts_pattern, '', $str));

		//strip repeated white space
		$puncts_pattern = '/[\s]+/';
		$str = preg_replace( $puncts_pattern, ' ', $str );

		//strip white space before punctuations
		$puncts_pattern = '/[\s]+([\~\!\@\#\$\%\^\&\*\_\+\`\-\=\{\}\|\[\]\\\:\"\;\'\<\>\?\,\.\/])+/';
		$str = preg_replace( $puncts_pattern, '\1', $str );

		//Strip ()
		$both_pattern = '/^[\(]([^\(|\)]+)[\)]$/';
		$trail_pattern = '/^([^\(]+)[\)]$/';
		$lead_pattern = '/^[\(]([^\)]+)$/';
		$str = preg_replace($lead_pattern, '\\1', preg_replace($trail_pattern,'\\1', preg_replace($both_pattern, '\\1', $str)));

		return $str;
	}

	function sanitize_related( $val )
	{
		if( is_numeric( $val ) && get_permalink( absint( $val )) )
			return( absint( $val ) );

		if( $url = sanitize_url( $val) ){
			if( $post_id = url_to_postid( $url ) )
				return( $post_id );
			else
				return( $url );
		}

		return( FALSE );
	}

	function strip_initial_articles( $content )
	{
		// TODO: add more articles, such as those from here: http://www.loc.gov/marc/bibliographic/bdapndxf.html
		return( preg_replace( $this->initial_articles, '', $content ));
	}

	function pre_save_filters( $content )
	{
		if ( is_array( $_REQUEST['scrib_meditor'] )){
			switch( current_filter() ){
				case 'pre_post_title':
					return( apply_filters( 'scrib_meditor_pre_title', $content, $_REQUEST['scrib_meditor'] ));
					break;

				case 'pre_post_excerpt':
					return( apply_filters( 'scrib_meditor_pre_excerpt', $content, $_REQUEST['scrib_meditor'] ));
					break;

				case 'pre_post_content':
				default:
					return( apply_filters( 'scrib_meditor_pre_content', $content, $_REQUEST['scrib_meditor'] ));
			}
		}
		return( $content );
	}



	function register_menus()
	{
		if( ( 'post-new.php' == basename( $_SERVER['PHP_SELF'] )) && ( isset( $_GET['posted'] ) ) && ( !isset( $_GET['scrib_meditor_add'] ) ) && ( $form = key( get_post_meta( $_GET['posted'], 'scrib_meditor_content', true )) ) )
		{
				$_GET['scrib_meditor_add'] = 'sibling';
				$_GET['scrib_meditor_from'] = $_GET['posted'];
				die( wp_redirect( admin_url( 'post-new.php' ) .'?'. http_build_query( $_GET ) ));
		}
	}

	function register( $handle , $prototype )
	{
		add_action( 'admin_menu', array( &$this, 'register_menus' ));

		if( isset( $this->forms[ $handle ] ))
			return( FALSE );
		$this->forms[ $handle ] = $prototype;
	}

	function unregister( $handle )
	{
		if( !isset( $this->forms[ $handle ] ))
			return( FALSE );
		unset( $this->forms[ $handle ] );
	}

	function footer_activatejs()
	{
?>
		<script type="text/javascript">
			scrib_meditor();
		</script>

		<script type="text/javascript">
			jQuery(function() {
				<?php echo implode( "\n\t\t\t\t", $this->suggest_js ) ."\n"; ?>
			});
		</script>
<?php
	}

	function make_content_closable( $content )
	{
		return( '<div class="inside">'. $content .'</div>');
	}

	function suggest_tags()
	{
		if ( isset( $_GET['tax'] ))
		{
			$taxonomy = explode(',', $_GET['tax'] );
			$taxonomy = array_filter( array_map( 'sanitize_title', array_map( 'trim', $taxonomy )));
		}
		else
		{
			$taxonomy = $this->taxonomies_for_suggest;
		}

		$s = sanitize_title( trim( $_REQUEST['q'] ));
		if ( strlen( $s ) < 2 )
			$s = '';

		$cachekey = md5( $s . implode( $taxonomy ));

		if( !$suggestion = wp_cache_get( $cachekey , 'scrib_suggest_meditor' ))
		{
			if ( empty( $s ) )
			{
				foreach( get_terms( $taxonomy, array( 'number' => 25, 'orderby' => 'count', 'order' => 'DESC' ) ) as $term )
					$suggestion[] = $term->name;

				$suggestion = implode( $suggestion, "\n" );
			}
			else
			{
				global $wpdb;

				$suggestion = implode( array_unique( $wpdb->get_col( "SELECT t.name, ((( 100 - t.len ) + 1 ) * tt.count ) AS hits
					FROM
					(
						SELECT term_id, name, LENGTH(name) AS len
						FROM $wpdb->terms
						WHERE slug LIKE ('" . $s . "%')
						ORDER BY len ASC
						LIMIT 100
					) t
					JOIN $wpdb->term_taxonomy AS tt ON tt.term_id = t.term_id
					WHERE tt.taxonomy IN('" . implode( "','", $taxonomy ). "')
					AND tt.count > 0
					ORDER BY hits DESC
					LIMIT 11;
				")), "\n" );
			}

			wp_cache_set( $cachekey , $suggestion, 'scrib_suggest_meditor', 1800 );
		}

		echo $suggestion;
		die;
	}

	function array_unique_deep( $array )
	{
		$uniquer = array();
		foreach( $array as $val ){
			$key = $val;
			if( is_array( $val )){
				if( isset( $key['src'] ))
					unset( $key['src'] );
				if( isset( $key['suppress'] ))
					unset( $key['suppress'] );
			}

			$uniquer[ md5( strtolower( serialize( $key ))) ] = $val;
		}
		return( array_values( $uniquer ));
	}
}


// now instantiate this object
$meditor = new Meditor;