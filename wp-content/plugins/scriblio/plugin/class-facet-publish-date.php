<?php
/**
 * the (post) publish date facet class
 */
class Facet_Publish_Date implements Facet
{
	public $label = 'Date'; // used by container class Facets

	public $query_var = 'date-range';

	public $version = '1.0';

	// query term value slugs to start time strings (parseable by strtotime())
	public $query_slugs_to_times = array(
		'today'      => 'today',
		'yesterday'  => 'yesterday',
		'past-week'  => '-1 week',
		'past-month' => '-1 month',
		'past-year'  => '-1 year',
		'earlier'    => '1980-01-01',
	);

	// query val slugs to names
	public $slugs_to_names = array(
		'today'      => 'Today',
		'yesterday'  => 'Yesterday',
		'past-week'  => 'Past week',
		'past-month' => 'Past month',
		'past-year'  => 'Past year',
		'earlier'    => 'Earlier',
	);

	// query val slugs to descriptions
	public $slugs_to_descriptions = array(
		'today'      => 'Posts published today',
		'yesterday'  => 'Posts published yesterday',
		'past-week'  => 'Posts published in the past week',
		'past-month' => 'Posts published in the past month',
		'past-year'  => 'Posts published in the past year',
		'earlier'    => 'Posts pubished over a year ago',
	);

	// query val slugs to dates. this will be computed dynamically
	private $query_val_dates = NULL;

	private $cache_group = 'scriblio-facet-publish-date';
	private $ttl = 1207; // ~20 minutes

	private $selected_range = NULL;


	/**
	 * constructor
	 *
	 * @param string $name name of this publish date facet
	 */
	public function __construct( $name, $args, $facets_object )
	{
		$this->name = $name; // name should be exactly the name of the post field
		$this->args = $args;
		$this->facets = $facets_object;

		$this->labels = $this->facets->build_labels( 'date', 'dates' );

		// we're instantiated after "init" action, so add the rewrite tag
		// and rule here. add_rewrite_tag also adds our query var to
		// WP_Query's list of public query vars.
		add_rewrite_tag( '%'. $this->query_var .'%', '[^/]+' );
		add_rewrite_rule( $this->query_var .'/([^/]+)', 'index.php?'. $this->query_var .'=$matches[1]', 'top' );
	}//END __construct

	/**
	 * Facet::register_query_var interface implementation
	 *
	 * Associate a query var with this facet class
	 */
	public function register_query_var()
	{
		return $this->query_var;
	}//END register_query_var

	/**
	 * Facet::parse_query interface implementation.
	 *
	 * Parse the query terms to construct the selected date range term,
	 * and then update $wp_query with a date range query that corresponds
	 * to our query terms.
	 *
	 * @param string $query_terms the value of our query var
	 * @param WP_Query the query object
	 */
	public function parse_query( $query_terms, $wp_query )
	{
		// identify the terms in this query. we only accept one term value
		// at a time, but it may be a range (two dates separated by a colon)
		$terms = array_filter( array_map( 'trim', (array) preg_split( '/[,]/', $query_terms ) ) );

		$selected_range = explode( ':', $terms[0] );

		if ( 2 < count( $selected_range ) )
		{
			$selected_range = array_slice( $selected_range, 0, 2 );
		}

		// if we only have one date range spec, then it better be one of the
		// keys in $this->slugs_to_names
		if ( 1 == count( $selected_range ) && ! isset( $this->query_slugs_to_times[ $selected_range[0] ] ) )
		{
			// default to 1st key of $this->query_slugs_to_times ('today')
			$array_keys = array_keys( $this->query_slugs_to_times );
			$selected_range[0] = $array_keys[0];
		}

		// construct a date range term
		$date_term = array(
			'facet' => $this->name,
			'slug' => 'custom',
			'name' => 'Custom Date Range',
			'description' => 'Posts published in a user-defined date range',
			'range' => $selected_range,
		);

		// override some vars in $date_term if this is not a custom range term
		if ( 1 == count( $selected_range ) )
		{
			$date_term['slug'] = $selected_range[0];
			$date_term['name'] = $this->slugs_to_names[ $date_term['slug'] ];
			$date_term['description'] = $this->slugs_to_descriptions[ $date_term['slug'] ];
		}//END if

		$this->selected_range = array( $date_term['slug'] => (object) $date_term );

		// update wp_query
		$wp_query->query[ $this->query_var ] = $date_term['slug'];

		if ( 'yesterday' == $wp_query->query[ $this->query_var ] )
		{
			$yesterday = getdate( strtotime( 'yesterday' ) );
			$date_query = array(
				'year'  => $yesterday['year'],
				'month' => $yesterday['mon'],
				'day'   => $yesterday['mday'],
			);
		}
		elseif ( 'earlier' == $wp_query->query[ $this->query_var ] )
		{
			$date_query = array(
				'after'  => $this->query_slugs_to_times['earlier'],
				'before' => '-1 year -1 day',
			);
		}
		else
		{
			$date_query = array(
				'after'  => $this->query_slugs_to_times[ $wp_query->query[ $this->query_var ] ],
			);
		}

		// note: this wipes out any existing date query
		$wp_query->query_vars['date_query'] = array( $date_query );

		return $this->selected_range;
	}//END parse_query

	/**
	 * generate a cache key based on $base, taking into account our version
	 * number and whether scriblio()->cachebuster is set or not.
	 *
	 * @param string $base the base of our cache key
	 * @param bool $hash hash the resulting key if TRUE
	 * @return string a unique cache key based on $base and our plugin version
	 */
	public function get_cache_key( $base, $hash = TRUE )
	{
		$key = ( $hash ? md5( $base ) : $base ) . '|' . $this->version;
		return $key . ( scriblio()->cachebuster ? 'CACHEBUSTER' : '' );
	}//END get_cache_key

	/**
	 * Facet::get_terms_in_corpus interface implementation
	 */
	public function get_terms_in_corpus()
	{
		if ( isset( $this->terms_in_corpus ) )
		{
			return $this->terms_in_corpus;
		}

		$cache_key = $this->get_cache_key( 'terms-in-corpus', FALSE );

		if ( ! $this->terms_in_corpus = wp_cache_get( $cache_key, $this->cache_group ) )
		{
			global $wpdb;

			$terms = $wpdb->get_results(
				'SELECT DATE( post_date_gmt) AS date, COUNT(*) AS hits
				FROM ' . $wpdb->posts . '
				WHERE post_status = "publish"
					GROUP BY date
					ORDER BY date DESC
					LIMIT 1000
				/* generated in Facet_Publish_Date::get_terms_in_corpus() */'
			);

			$this->terms_in_corpus = $this->sort_date_terms_to_range_terms( $terms );

			wp_cache_set( $cache_key, $this->terms_in_corpus, $this->cache_group, $this->ttl );
		}//END if

		return $this->terms_in_corpus;
	}//END get_terms_in_corpus

	/**
	 * Facet::get_terms_in_found_set interface implementation
	 *
	 * execute a SQL query to pull all the "terms" we're interested in from
	 * the matched post set. the terms here are the dates of these posts
	 * and counts of how many posts were published on each date. the results
	 * are cached to minimize the number of DB calls we have to make.
	 */
	public function get_terms_in_found_set()
	{
		if ( isset( $this->terms_in_found_set ) )
		{
			return $this->terms_in_found_set;
		}

		$matching_post_ids = $this->facets->get_matching_post_ids();

		// if there aren't any matching post ids, we don't need to query
		if ( empty( $matching_post_ids ) )
		{
			return array();
		}

		$cache_key = $this->get_cache_key( serialize( $matching_post_ids ) );

		if ( ! $this->terms_in_found_set = wp_cache_get( $cache_key, $this->cache_group ) )
		{
			global $wpdb;

			$terms = $wpdb->get_results(
				'SELECT DATE( post_date_gmt) AS date, COUNT(*) AS hits
				FROM '. $wpdb->posts .'
				WHERE ID IN ('. implode( ',', $matching_post_ids ) .')
					GROUP BY date
					ORDER BY date DESC
					LIMIT 1000
				/* generated in Facet_Publish_Date::get_terms_in_found_set() */'
			);

			$this->terms_in_found_set = $this->sort_date_terms_to_range_terms( $terms );

//			wp_cache_set( $cache_key, $this->terms_in_found_set, $this->cache_group, $this->ttl );
		}//END if

		return $this->terms_in_found_set;
	}//END get_terms_in_found_set

	/**
	 * Facet::get_terms_in_post interface implementation
	 *
	 * @param int $post_id
	 * @return mixed
	 */
	public function get_terms_in_post( $post_id = FALSE )
	{
		if ( ! $post_id )
		{
			$post_id = get_the_ID();
		}

		if ( ! $post_id )
		{
			return FALSE;
		}

		if ( ! $post = get_post( $post_id ) )
		{
			return FALSE;
		}

		$date_str = gmdate( 'Y-m-d', $post->post_date_gmt );

		$this->terms_in_post[] = (object) array(
			'facet' => $this->name,
			'slug' => $date_str,
			'name' => $date_str,
			'description' => '',
			'count' => $this->get_post_count_by_date( $post ),
		);

		return $this->terms_in_post;
	}//END get_terms_in_post

	/**
	 * Facet::selected interface implementation
	 *
	 * check if $term is already selected or not
	 *
	 * @param mixed $term the term (date range) to check
	 * @return bool return TRUE if $term is selected, FALSE if not.
	 */
	public function selected( $term )
	{
		return( isset( $this->selected_range[ ( is_object( $term ) ? $term->slug : $term ) ] ) );
	}//END selected

	/**
	 * Facet::queryterm_add interface implementation
	 *
	 * Since we can only have one date range at a time, we clear out the
	 * current selection if it's not empty, before we add $term to it.
	 *
	 * @param object $term the term to add
	 * @param array $current the current list of selected terms
	 */
	public function queryterm_add( $term, $current )
	{
		if ( ! empty( $current ) )
		{
			$current = array();
		}
		$current[ $term->slug ] = $term;

		return $current;
	}//END queryterm_add

	/**
	 * Facet::queryterm_remove interface implementation
	 */
	public function queryterm_remove( $term, $current )
	{
		if ( isset( $current[ $term->slug ] ) )
		{
			unset( $current[ $term->slug ] );
		}
		return $current;
	}//END queryterm_remove

	/**
	 * Facet::permalink interface implementation
	 */
	public function permalink( $terms )
	{
		if ( empty( $terms ) )
		{
			return;
		}

		$array_keys = array_keys( $terms );
		return home_url( '/' . $this->query_var . '/' . $array_keys[0] . '/' );
	}//END permalink

	/**
	 * @param object $post a post object whose publish date we'll use
	 *  to find the number of posts also published on the same day
	 * @return mixed the number of posts published on the same day as $post,
	 *  or FALSE if we get an error
	 */
	public function get_post_count_by_date( $post )
	{
		// check cache first
		$cache_key = $this->get_cache_key( $post->ID );

		if ( $count = wp_cache_get( $cache_key, $this->cache_group ) )
		{
			return $count;
		}

		global $wpdb;

		$count = $wpdb->get_results(
			'SELECT COUNT( * ) AS count
			FROM '. $wpdb->posts .'
			WHERE DATE( post_date_gmt ) = "' . gmdate( 'Y-m-d', $post->post_date_gmt ) . '"
			/* generated in Facet_Publish_Date::get_post_count_by_date() */'
		);

		// set cache
		wp_cache_set( $cache_key, $count, $this->cache_group, $this->ttl );

		return $count;
	}//END get_post_count_by_date

	/**
	 * Compute the actual start date for each of our date ranges we support.
	 *
	 * @param string $slug the date range slug. if set we'll return the
	 *  corresponding date for that slug. if not then we'll return all
	 *  the date range slugs and corresponding dates.
	 * @return mixed all the supported query var value slugs (keys of
	 *  $this->query_slugs_to_times) as timestamps, or FALSE if we get
	 *  an invalid $range request.
	 */
	public function get_query_val_dates( $range = NULL )
	{
		if ( empty( $this->query_val_dates ) )
		{
			foreach ( $this->query_slugs_to_times as $slug => $time_str )
			{
				$this->query_val_dates[ $slug ] = gmdate( 'Y-m-d', strtotime( $time_str ) );
			}//END foreach
		}//END if

		if ( empty( $range ) )
		{
			return $this->query_val_dates;
		}
		elseif ( isset( $this->query_val_dates[ $range ] ) )
		{
			return $this->query_val_dates[ $range ];
		}

		return FALSE;
	}//END get_query_val_dates

	/**
	 * build a range term object from a slug
	 *
	 * @param string $range_slug this should be a key in $this->query_slugs_to_times
	 * @param string $min_date if not NULL, then only return a range term if
	 *  its start_time is before or equal to this date.
	 * @return mixed a range term array, or FALSE if $range_slug is not valid
	 *  or if its start date is after $min_date
	 */
	public function get_range_term( $range_slug, $min_date = NULL )
	{
		if ( ! isset( $this->query_slugs_to_times[ $range_slug ] ) )
		{
			return FALSE;
		}

		// timestamp at the start of the range
		$start_time = strtotime( $this->get_query_val_dates( $range_slug ) );

		if ( ! empty( $min_date ) && $start_time > strtotime( $min_date ) )
		{
			return FALSE;
		}

		return array(
			'facet'       => $this->name,
			'slug'        => $range_slug,
			'name'        => $this->slugs_to_names[ $range_slug ],
			'description' => $this->slugs_to_descriptions[ $range_slug ],
			'count'       => 0,
			'start_time'  => $start_time,
		);
	}//END get_range_term

	/**
	 * Sort $terms into a list of "range terms". Each term in $terms has a
	 * date string and hits count and the list is sorted in descending date
	 * order. We iterate over $terms and sum the hits in all terms that
	 * belong to each date range defined in $this->query_slugs_to_times.
	 *
	 * @param array $terms a list of date terms
	 * @return array list of "date range" terms with counts accumulated from
	 *  counts in $terms
	 */
	public function sort_date_terms_to_range_terms( $terms )
	{
		$total = 0; // total post count
		$results = array();

		// get the date ranges to sort into
		$range_slugs = array_keys( $this->query_slugs_to_times );
		if ( empty( $range_slugs ) )
		{
			return $results;
		}

		$range_slug = array_shift( $range_slugs );
		$range_term = $this->get_range_term( $range_slug );

		foreach ( $terms as $term )
		{
			$total += $term->hits; // tracks total post count so far

			$term_datetime = strtotime( $term->date );

			if ( $range_term['start_time'] <= $term_datetime )
			{
				// term is still within the current range. increment its count
				$range_term['count'] += $term->hits;
				continue;
			}

			// since terms are sorted in descending date order, once
			// we find a term whose date is beyond $range_term['start_time'],
			// it's time to advance to the next date range
			//
			// but first save the date range we just built
			if ( 0 < $range_term['count'] )
			{
				$results[] = (object) $range_term;
			}

			$range_term = NULL;
			$range_time = 0;

			// get the next range term we can use
			do
			{
				$range_slug = array_shift( $range_slugs );
			}
			while ( ! empty( $range_slug ) && ! $range_term = $this->get_range_term( $range_slug, $term->date ) );

			if ( empty( $range_slug ) )
			{
				break; // did not find any other term in our date ranges
			}

			// restart the total for the 'earlier' range, since it excludes
			// posts in other date ranges. remember to include the hits count
			// for the current $term.
			if ( 'earlier' == $range_slug )
			{
				$total = $term->hits;
			}

			// set up for the next date range term. if we're working with
			// 'today' or 'yesterday', then we don't start with the cumulative
			// count since these two ranges only include posts published
			// within the ranges. but for others we do want to include
			// the cumulative count since they include posts from now until
			// the beginning of the range. e.g. past-week includes posts
			// published between now and a week before today, so it includes
			// posts published today and yesterday as well.
			$range_term['count'] = ( 'today' == $range_slug || 'yesterday' == $range_slug ) ? $term->hits : $total;
		}//END foreach

		if ( ! empty( $range_term ) )
		{
			$results[] = (object) $range_term;
		}

		return $results;
	}//END sort_date_terms_to_range_terms
}//END class
