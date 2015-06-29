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


class ScriblioImporter
{
	public function import_insert_harvest( &$bibr, $enriched = 0 )
	{
		global $wpdb;

		$wpdb->get_results("REPLACE INTO $this->harvest_table
			( source_id, harvest_date, imported, content, enriched )
			VALUES ( '". $wpdb->escape( $bibr['_sourceid'] ) ."', NOW(), 0, '". $wpdb->escape( serialize( $bibr )) ."', ". absint( $enriched ) ." )" );

		wp_cache_set( $bibr['_sourceid'], time() + 2500000, 'scrib_harvested', time() + 2500000 );
	}

	public function import_post_exists( $idnumbers )
	{
		global $wpdb;

		$post_id = FALSE;
		$post_ids = $tt_ids = array();

		foreach( $idnumbers as $idnum )
			$tt_ids[] = get_term( (int) is_term( (string) $idnum['id'] ), $idnum['type'] );

		if( count( $tt_ids )){
			foreach( $tt_ids as $k => $tt_id )
				if( isset( $tt_id->term_taxonomy_id ))
					$tt_ids[ $k ] = (int) $tt_id->term_taxonomy_id;
				else
					unset( $tt_ids[ $k ] );

			if( !count( $tt_ids ))
				return( FALSE );

			$post_ids = $wpdb->get_col( "SELECT object_id, COUNT(*) AS hits
				FROM $wpdb->term_relationships
				WHERE term_taxonomy_id IN ('". implode( '\',\'', $tt_ids ) ."')
				GROUP BY object_id
				ORDER BY hits DESC
				LIMIT 100" );

			if( 1 < count( $post_ids ))
			{
				// de-index the duplicate posts
				// TODO: what if they have comments? What if others have linked to them?
				$this->import_deindex_post( $post_ids );

//				usleep( 250000 ); // give the database a moment to settle
			}

			foreach( $post_ids as $post_id )
				if( get_post( $post_id ))
					return( $post_id );
		}

		return( FALSE );
	}

	public function import_deindex_post( $post_ids )
	{
		// sets a post's status to draft so that it no longer appears in searches
		// TODO: need to find a better status to hide it from searches,
		// but not invalidate incoming links or remove comments
		global $wpdb;

		foreach( (array) $post_ids as $post_id )
		{
			$post_id = absint( $post_id );
			if( !$post_id )
				continue;

			// set the post to draft (TODO: use a WP function instead of writing to DB)
			$wpdb->get_results( "UPDATE $wpdb->posts SET post_status = 'draft' WHERE ID = $post_id" );

			// clear the post/page cache
			clean_page_cache( $post_id );
			clean_post_cache( $post_id );

			// do the post transition
			wp_transition_post_status( 'draft', 'publish', $post_id );
		}
	}

	public function import_insert_post( $bibr )
	{
//		return(1);
		global $wpdb, $bsuite;

		if( !defined( 'DOING_AUTOSAVE' ) )
			define( 'DOING_AUTOSAVE', TRUE ); // prevents revision tracking
		wp_defer_term_counting( TRUE ); // may improve performance
		remove_filter( 'content_save_pre', array( &$bsuite, 'innerindex_nametags' )); // don't build an inner index for catalog records
		remove_filter( 'publish_post', '_publish_post_hook', 5, 1 ); // avoids pinging links in catalog records
		remove_filter( 'save_post', '_save_post_hook', 5, 2 ); // don't bother
//		kses_remove_filters(); // don't kses filter catalog records
		define( 'WP_IMPORTING', TRUE ); // may improve performance by preventing exection of some unknown hooks

		$postdata = array();
		if( $this->import_post_exists( $bibr['_idnumbers'] )){
			$postdata['ID'] = $this->import_post_exists( $bibr['_idnumbers'] );

			$oldrecord = get_post_meta( $postdata['ID'], 'scrib_meditor_content', true );


//TODO: setting post title and content at this point works, but it ignores the opportunity to merge data from the existing record.

			$postdata['post_title'] = apply_filters( 'scrib_meditor_pre_title', strlen( get_post_field( 'post_title', $postdata['ID'] )) ? get_post_field( 'post_title', $postdata['ID'] ) : $bibr['_title'], $bibr );

			$postdata['post_content'] = apply_filters( 'scrib_meditor_pre_content', strlen( get_post_field( 'post_content', $postdata['ID'] )) ? get_post_field( 'post_content', $postdata['ID'] ) : $bibr['_body'], $bibr );

			if( isset( $bibr['_acqdate'] ))
				$postdata['post_date'] =
				$postdata['post_date_gmt'] =
				$postdata['post_modified'] =
				$postdata['post_modified_gmt'] = strlen( get_post_field( 'post_date', $postdata['ID'] )) ? get_post_field( 'post_date', $postdata['ID'] ) : $bibr['_acqdate'];

			$postdata['post_author'] = 1 < get_post_field( 'post_author', $postdata['ID'] ) ? get_post_field( 'post_author', $postdata['ID'] ) : $bibr['_userid'];
		}else{

			$postdata['post_title'] = apply_filters( 'scrib_meditor_pre_title', $bibr['_title'], $bibr );

			$postdata['post_content'] = apply_filters( 'scrib_meditor_pre_content', $bibr['_body'], $bibr );

			if( isset( $bibr['_acqdate'] ))
				$postdata['post_date'] =
				$postdata['post_date_gmt'] =
				$postdata['post_modified'] =
				$postdata['post_modified_gmt'] = $bibr['_acqdate'];

			$postdata['post_author'] = $bibr['_userid'];
		}

		$postdata['comment_status'] = get_option('default_comment_status');
		$postdata['ping_status'] 	= get_option('default_ping_status');
		$postdata['post_status'] 	= 'publish';
		$postdata['post_type'] 		= 'post';

		if( isset( $bibr['_icon'] ))
			$the_icon = $bibr['_icon'];

		$nsourceid = $bibr['_sourceid'];
		$ncategory = $bibr['_category'];

		unset( $bibr['_title'] );
		unset( $bibr['_acqdate'] );
		unset( $bibr['_idnumbers'] );
		unset( $bibr['_sourceid'] );
		unset( $bibr['_icon'] );
		unset( $bibr['_category'] );
		unset( $bibr['_userid'] );

		$postdata['post_excerpt'] = '';

		if( empty( $postdata['post_title'] ))
			return( FALSE );

//echo "<h2>Pre</h2>";
//print_r( $bibr );
//die;

		// sanitize the input record
		$bibr = $this->meditor_sanitize_input( $bibr );

//echo "<h2>Sanitized</h2>";
//print_r( $bibr );

		// merge it with the old record
		if( is_array( $oldrecord ))
			$bibr = $this->meditor_merge_meta( $oldrecord, $bibr, $nsourceid );

//echo "<h2>Merged</h2>";
//print_r( $bibr );

//print_r( $postdata );

		$post_id = wp_insert_post( $postdata ); // insert the post
		if( $post_id )
		{

			if( ! empty( $ncategory ))
				wp_set_object_terms( $post_id, $ncategory , 'category', FALSE );

			add_post_meta( $post_id, 'scrib_meditor_content', $bibr, TRUE ) or update_post_meta( $post_id, 'scrib_meditor_content', $bibr );

			do_action( 'scrib_meditor_save_record', $post_id, $bibr );

			if( isset( $the_icon )){
				if( is_array( $the_icon ))
					add_post_meta( $post_id, 'bsuite_post_icon', $the_icon, TRUE ) or update_post_meta( $post_id, 'bsuite_post_icon', $the_icon );
				else if( is_string( $the_icon ))
					$bsuite->icon_resize( $the_icon, $post_id, TRUE );
			}


			return( $post_id );
		}
		return(FALSE);
	}

	public function import_harvest_tobepublished_count()
	{
		global $wpdb;
		return( $wpdb->get_var( 'SELECT COUNT(*) FROM '. $this->harvest_table .' WHERE imported = 0' ));
	}

	public function import_harvest_publish()
	{
		global $wpdb;

		$interval = 25;

		if( isset( $_GET[ 'n' ] ) == false )
		{
			$n = 0;
		}
		else
		{
			$n = absint( $_GET[ 'n' ] );
		}

		$posts = $wpdb->get_results('SELECT * FROM '. $this->harvest_table .' WHERE imported = 0 ORDER BY enriched DESC LIMIT 0,'. $interval, ARRAY_A);

		if( is_array( $posts ))
		{
			echo "<p>Fetching records in batches of $interval...publishing them...making coffee. Please be patient.<br /><br /></p>";
			echo '<ol>';
			foreach( $posts as $post ) {
				set_time_limit( 900 );

				$r = array();
				$post_id = FALSE;

				$r = unserialize( $post['content'] );
				if( !count( $r ))
					continue;

				$post_id = $this->import_insert_post( $r );
				if( $post_id ){
					$wpdb->get_var( 'UPDATE '. $this->harvest_table .' SET imported = 1, content = "" WHERE source_id = "'. $post['source_id'] .'"' );
					echo '<li><a href="'. get_permalink( $post_id ) .'" target="_blank">'. get_the_title( $post_id ) .'</a> from '. $post['source_id'] .'</li>';
					flush();
				}else{
					$wpdb->get_var( 'UPDATE '. $this->harvest_table .' SET imported = -1 WHERE source_id = "'. $post['source_id'] .'"' );
					echo '<li>Failed to publish '. $post['source_id'] .'</li>';
				}
			}
			echo '</ol>';

			wp_defer_term_counting( FALSE ); // now update the term counts that we'd defered earlier

			?>
			<p><?php _e("If your browser doesn't start loading the next page automatically click this link:"); ?> <a href="?page=<?php echo plugin_basename( dirname( __FILE__ )); ?>/scriblio.php&command=<?php _e('Publish Harvested Records', 'Scriblio') ?>&n=<?php echo ( $n + $interval) ?>"><?php _e("Next Posts"); ?></a> </p>
			<script language='javascript'>
			<!--

			function nextpage() {
				location.href="?page=<?php echo plugin_basename( dirname( __FILE__ )); ?>/scriblio.php&command=<?php _e('Publish Harvested Records', 'Scriblio') ?>&n=<?php echo ( $n + $interval) ?>";
			}
			setTimeout( "nextpage()", 1250 );

			//-->
			</script>
			<?php
			echo '<p>'. $this->import_harvest_tobepublished_count() .' records remain to be published.</p>';
		} else {

			// update the term taxonomy counts
			$wpdb->get_results('
				UPDATE '. $wpdb->term_taxonomy .' tt
				SET tt.count = (
					SELECT COUNT(*)
					FROM '. $wpdb->term_relationships .' tr
					WHERE tr.term_taxonomy_id = tt.term_taxonomy_id
				)'
			);

			echo '<p>That&#039;s all folks. kthnxbye.</p>';
		}

		echo '<pre>';
		print_r( $wpdb->queries );
		echo '</pre>';
		?><?php echo get_num_queries(); ?> queries. <?php timer_stop(1); ?> seconds. <?php
	}

	public function import_harvest_passive(){
		global $wpdb, $bsuite;

		if( !$bsuite->get_lock( 'scrib_harvest_passive' ))
			return( FALSE );

		$posts = $wpdb->get_results('SELECT * FROM '. $this->harvest_table .' WHERE imported = 0 ORDER BY enriched DESC LIMIT 25', ARRAY_A);

		if( is_array( $posts )) {
			foreach( $posts as $post ) {
				set_time_limit( 900 );

				$r = unserialize( $post['content'] );
				if( !is_array( $r ))
					continue;

				$post_id = $this->import_insert_post( $r );

				if( $post_id ){
					$wpdb->get_var( 'UPDATE '. $this->harvest_table .' SET imported = 1, content = "" WHERE source_id = "'. $post['source_id'] .'"' );
				}else{
					$wpdb->get_var( 'UPDATE '. $this->harvest_table .' SET imported = -1 WHERE source_id = "'. $post['source_id'] .'"' );
				}
			}

			wp_defer_term_counting( FALSE ); // now update the term counts that we'd defered earlier

		}

		wp_defer_term_counting( FALSE ); // now update the term counts that we'd defered earlier
	}

	public function import_create_harvest_table()
	{
		global $wpdb, $bsuite;

		// create tables
		$charset_collate = '';
		if ( version_compare( mysql_get_server_info(), '4.1.0', '>=' ))
		{
			if ( ! empty( $wpdb->charset ))
				$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
			if ( ! empty( $wpdb->collate ))
				$charset_collate .= " COLLATE $wpdb->collate";
		}

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta("
			CREATE TABLE $this->harvest_table (
			source_id varchar(85) NOT NULL,
			harvest_date date NOT NULL,
			imported tinyint(1) default '0',
			content longtext NOT NULL,
			enriched tinyint(1) default '0',
			PRIMARY KEY  (source_id),
			KEY imported (imported),
			KEY enriched (enriched)
			) $charset_collate
		");

	}
}


// now instantiate this object
$meditor = new ScriblioImporter;