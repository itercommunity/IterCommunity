<?php

/*
 * Function to display the calendar to the screen
 * 
 * @since 2.0.0
 */
function gce_print_calendar( $feed_ids, $display = 'grid', $args = array(), $widget = false, $uid = null ) {
	
	// Set static unique ID for setting id attributes
	if( $uid == null ) {
		STATIC $uid = 1;
	}
	
	
	// Load scripts
	wp_enqueue_script( GCE_PLUGIN_SLUG . '-images-loaded' );
	wp_enqueue_script( GCE_PLUGIN_SLUG . '-qtip' );
	wp_enqueue_script( GCE_PLUGIN_SLUG . '-public' );
	
	$defaults = array(
			'title_text'      => '',
			'sort'            => 'asc',
			'grouped'         => 0,
			'month'           => null,
			'year'            => null,
			'widget'          => 0,
			'paging_interval' => null,
			'max_events'      => null,
			'start_offset'    => null,
			'paging_type'     => null,
			'paging'          => null,
			'max_num'         => null,
			'range_start'     => null,
			'show_tooltips'   => null
		);
	
	$args = array_merge( $defaults, $args );
	
	extract( $args );
	
	$ids = explode( '-', str_replace( ' ', '', $feed_ids ) );
	
	//Create new display object, passing array of feed id(s)
	$d = new GCE_Display( $ids, $title_text, $sort );
	$markup = '';
	$start = current_time( 'timestamp' );
	
	if( $widget ) {
		foreach( $ids as $f ) {
			$paging = get_post_meta( $f, 'gce_paging_widget', true );
			$old_paging[] = get_post_meta( $f, 'gce_paging', true );
			
			if( $paging ) {
				update_post_meta( $f, 'gce_paging', true );
			}
			
			$paging_interval = get_post_meta( $f, 'gce_widget_paging_interval', true );
		}
	}
	
	// If paging is not set then we need to set it now
	foreach( $ids as $id ) {
		if( $paging === null ) {
			$paging = get_post_meta( $id, 'gce_paging', true );
		}
		
		if( empty( $show_tooltips ) && $show_tooltips != 0 ) {
			$tooltips = get_post_meta( $id, 'gce_show_tooltips', true );
		} else {
			$tooltips = $show_tooltips;
		}
		
		if( ! empty( $tooltips ) && ( $tooltips === true || $tooltips == 'true' || $tooltips == '1' || $tooltips == 1 ) ) {
			$show_tooltips = 'true';
		} else {
			$show_tooltips = 'false';
		}
	}
	
	if( 'grid' == $display ) {
	
		global $localize;
		
		$target = 'gce-' . $uid;
		
		$localize[$target] = array( 
				'target_element' => $target,
				'feed_ids'       => $feed_ids,
				'title_text'     => $title_text,
				'type'           => ( $widget == 1 ? 'widget' : 'page' ),
				'show_tooltips'  => ( $show_tooltips == 'true' || $show_tooltips == '1' ? 'true' : 'false' )
			);
		
		if( $widget == 1 ) {
			$markup .= '<div class="gce-widget-grid gce-widget-' . esc_attr( $feed_ids ) . '" id="gce-' . $uid . '">';
		} else {
			$markup .= '<div class="gce-page-grid gce-page-grid-' . esc_attr( $feed_ids ) . '" id="gce-' . $uid . '">';
		}
		
		$markup .= $d->get_grid( $year, $month, $widget, $paging );
		$markup .= '</div>';

		
	} else if( 'list' == $display || 'list-grouped' == $display ) {
		
		if( $widget ) {
			$markup = '<div class="gce-widget-list gce-widget-list-' . esc_attr( $feed_ids ) . '" id="gce-' . $uid . '">' . $d->get_list( $grouped, ( $start + $start_offset ), $paging, $paging_interval, $start_offset, $max_events, $paging_type, $max_num ) . '</div>';
		} else {
			$markup = '<div class="gce-page-list gce-page-list-' . esc_attr( $feed_ids ) . '" id="gce-' . $uid . '">' . $d->get_list( $grouped, ( $start + $start_offset ), $paging, $paging_interval, $start_offset, $max_events, $paging_type ) . '</div>';
		}
	} else if( 'date-range-list' == $display ) {	
		
		$paging_interval = 'date-range';
		
		if( $widget ) {
			$markup = '<div class="gce-widget-list gce-widget-list-' . esc_attr( $feed_ids ) . '" id="gce-' . $uid . '">' . $d->get_list( $grouped, $range_start, false, $paging_interval, $start_offset, INF, $paging_type, $max_num ) . '</div>';
		} else {
			$markup = '<div class="gce-page-list gce-page-list-' . esc_attr( $feed_ids ) . '" id="gce-' . $uid . '">' . $d->get_list( $grouped, $range_start, false, $paging_interval, $start_offset, INF, $paging_type, INF ) . '</div>';
		}
	} elseif ( 'date-range-grid' == $display ) {
		
		global $localize;
		
		$target = 'gce-' . $uid;
		
		$localize[$target] = array( 
				'target_element' => $target,
				'feed_ids'       => $feed_ids,
				'title_text'     => $title_text,
				'type'           => ( $widget == 1 ? 'widget' : 'page' ),
				'show_tooltips'  => ( $show_tooltips == 'true' || $show_tooltips == '1' ? 'true' : 'false' )
			);
		
		if( $widget ) {
			$markup = '<div class="gce-widget-grid gce-widget-grid-' . esc_attr( $feed_ids ) . '" id="gce-' . $uid . '">' . $markup .= $d->get_grid( $year, $month, $widget, $paging ) . '</div>';
		} else {
			$markup = '<div class="gce-page-grid gce-page-grid-' . esc_attr( $feed_ids ) . '" id="gce-' . $uid . '">' . $markup .= $d->get_grid( $year, $month, $widget, $paging ) . '</div>';
		}
	}
	
	// Reset post meta
	if( $widget ) {
		$i = 0;
		foreach( $ids as $f ) {
			update_post_meta( $f, 'gce_paging', $old_paging[$i] );

			$i++;
		}
	}
	
	$uid++;
	
	return $markup;
}

/**
* AJAX function for grid pagination
* 
* @since 2.0.0
*/
function gce_ajax() {
	
	$uid    = esc_html( $_POST['gce_uid'] );
	$ids    = esc_html( $_POST['gce_feed_ids'] );
	$title  = esc_html( $_POST['gce_title_text'] );
	$month  = esc_html( $_POST['gce_month'] );
	$year   = esc_html( $_POST['gce_year'] );
	$paging = esc_html( $_POST['gce_paging'] );
	$type   = esc_html( $_POST['gce_type'] );
	   
	 // Split ID and pass as UID to function
	$uid = explode( '-', $uid );

	$title = ( 'null' == $title ) ? null : $title;

	$args = array(
		   'title_text' => $title,
		   'month'      => $month,
		   'year'       => $year,
		   'paging'     => $paging
	   );

	   if ( 'page' == $type ) {
		   echo gce_print_calendar( $ids, 'grid', $args, 0, $uid[1] );
	   } elseif ( 'widget' == $type ) {
		   $args['widget'] = 1;
		   echo gce_print_calendar( $ids, 'grid', $args, 1, $uid[1] );
	   }
	   
   die();
}
add_action( 'wp_ajax_nopriv_gce_ajax', 'gce_ajax' );
add_action( 'wp_ajax_gce_ajax', 'gce_ajax' );


/**
* AJAX function for grid pagination
* 
* @since 2.0.0
*/
function gce_ajax_list() {
	
	$grouped          = esc_html( $_POST['gce_grouped'] );
	$start            = esc_html( $_POST['gce_start'] );
	$ids              = esc_html( $_POST['gce_feed_ids'] );
	$title_text       = esc_html( $_POST['gce_title_text'] );
	$sort             = esc_html( $_POST['gce_sort'] );
	$paging           = esc_html( $_POST['gce_paging'] );
	$paging_interval  = esc_html( $_POST['gce_paging_interval'] );
	$paging_direction = esc_html( $_POST['gce_paging_direction'] );
	$start_offset     = esc_html( $_POST['gce_start_offset'] );
	$paging_type      = esc_html( $_POST['gce_paging_type'] );
	
	if( $paging_direction == 'back' ) {
		if( $paging_type == 'month' ) {

			$this_month = mktime( 0, 0, 0, date( 'm', $start ) - 1, 1, date( 'Y', $start ) );
			$prev_month = mktime( 0, 0, 0, date( 'm', $start ) - 2, 1, date( 'Y', $start ) );
			$prev_interval_days = date( 't', $prev_month );
			$month_days = date( 't', $this_month );
			
			$int = $month_days + $prev_interval_days;
			$int = $int * 86400;
		
			$start = $start - ( $int );

			$changed_month_days = date( 't', $start );
			$paging_interval = $changed_month_days * 86400;
		} else {
			$start = $start - ( $paging_interval * 2 );
		}
		
	} else {
		if( $paging_type == 'month' ) {
			$days_in_month = date( 't', $start );
			$paging_interval = 86400 * $days_in_month;
		}
	}
	
	$d = new GCE_Display( explode( '-', $ids ), $title_text, $sort  );

	echo $d->get_list( $grouped, $start, $paging, $paging_interval, $start_offset );
	
	die();
}
add_action( 'wp_ajax_nopriv_gce_ajax_list', 'gce_ajax_list' );
add_action( 'wp_ajax_gce_ajax_list', 'gce_ajax_list' );


function gce_feed_content( $content ) {
	global $post;
	
	if( $post->post_type == 'gce_feed' ) {
		$content = '[gcal id="' . esc_attr( $post->ID ) . '"]';
	}
	
	return $content;
}
add_filter( 'the_content', 'gce_feed_content' );

/**
 * Google Analytics campaign URL.
 *
 * @since   2.0.0
 *
 * @param   string  $base_url Plain URL to navigate to
 * @param   string  $source   GA "source" tracking value
 * @param   string  $medium   GA "medium" tracking value
 * @param   string  $campaign GA "campaign" tracking value
 * @return  string  $url      Full Google Analytics campaign URL
 */
function gce_ga_campaign_url( $base_url, $source, $medium, $campaign ) {
	// $medium examples: 'sidebar_link', 'banner_image'

	$url = esc_url( add_query_arg( array(
		'utm_source'   => $source,
		'utm_medium'   => $medium,
		'utm_campaign' => $campaign
	), $base_url ) );

	return esc_url( $url );
}

/**
 * Function to convert date format mm/dd/YYYY to unix timestamp
 */
function gce_date_unix( $date ) {
	$date = explode( '/', $date );
			
	$month = $date[0];
	$day   = $date[1];
	$year  = $date[2];

	$timestamp = mktime( 0, 0, 0, $month, $day, $year );
	
	return $timestamp;
}
