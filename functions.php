<?php
/**
 * Gets related posts by taxonomy.
 *
 * @since 0.1
 *
 * @param int (required) $post_id
 * @param array|string (required) $taxonomies The taxonomies to retrieve related posts from
 * @param array|string $args Change what is returned
 * @return array Empty array if no related posts found. Array with post objects.
 */
function km_rpbt_related_posts_by_taxonomy( $post_id = 0, $taxonomies = 'category', $args = '' ) {
	global $wpdb;

	$post_id = absint( $post_id );

	if ( !$post_id )
		return;

	$defaults = array(
		'post_types' => 'post', 'posts_per_page' => 5, 'order' => 'DESC',
		'fields' => 'all', 'limit_posts' => -1, 'limit_year' => '',
		'limit_month' => '', 'orderby' => 'post_date', 'exclude_terms' => '',
		'exclude_posts' => '', 'post_thumbnail' => ''
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$taxonomies = ( !empty( $taxonomies ) ) ? $taxonomies : 'category';

	if ( !is_array( $taxonomies ) )
		$taxonomies = array_unique( explode( ',', (string) $taxonomies ) );

	$terms = wp_get_object_terms( $post_id, $taxonomies, array( 'fields' => 'ids' ) );

	if ( is_wp_error( $terms ) || empty( $terms ) )
		return array();

	$exclude_terms = km_rpbt_related_posts_by_taxonomy_validate_ids( $exclude_terms );
	$terms = array_diff( $terms , $exclude_terms );

	if ( empty( $terms ) )
		return array();

	$term_ids_sql = implode( ', ', $terms );


	$exclude_posts  = km_rpbt_related_posts_by_taxonomy_validate_ids( $exclude_posts );
	$exclude_posts[] = $post_id;
	$exclude_posts   = array_unique( $exclude_posts );

	if ( empty( $exclude_posts ) )
		return array();

	$post_ids_sql = trim( implode( ', ', $exclude_posts ), ', ' );

	switch ( strtoupper( (string) $order ) ) {
	case 'ASC':  $order_sql = 'ASC'; break;
	case 'RAND': $order_sql = 'RAND()'; break;
	default:     $order_sql = 'DESC'; break;
	}

	$limit_sql = '';
	if ( -1 !== $limit_posts ) {
		$limit_posts = absint( $limit_posts );
		if ( $limit_posts )
			$limit_sql = ' LIMIT 0,' . $limit_posts;
	}

	$date_format = strtolower( (string) $orderby );
	if ( in_array( $orderby, array( 'post_date', 'post_modified' ) ) )
		$date_format = $orderby;
	else
		$date_format = 'post_date';

	$date_sql = '';
	$limit_year = absint( $limit_year );
	$limit_month = absint( $limit_month );
	if ( $limit_year || $limit_month  ) {
		/* year takes precedence over month */
		$time_limit  = ( $limit_year ) ? $limit_year : $limit_month ;
		$time_string = ( $limit_year ) ? 'year' : 'month';
		$last_date   = date( 'Y-m-t', strtotime( "now" ) );
		$first_date  = date( 'Y-m-d', strtotime( "$last_date -$time_limit $time_string" ) );
		$date_sql    = " AND $wpdb->posts.$date_format > '$first_date 23:59:59' AND $wpdb->posts.$date_format <= '$last_date 23:59:59'";
		$limit_sql = ''; // limit by date takes precedence over limit by posts
	}

	$post_types = ( !empty( $post_types ) ) ? $post_types : 'post';

	if ( !is_array( $post_types ) )
		$post_types = array_unique( explode( ',', (string) $post_types ) );

	$post_type_arr = array();
	foreach ( (array) $post_types as $type ) {
		$post_type_obj = get_post_type_object( $type );
		if ( $post_type_obj )
			$post_type_arr[] = $type;
	}
	$post_types = ( !empty( $post_type_arr ) ) ? $post_type_arr : array( 'post' );

	// where sql (post type and post status)
	if ( count( $post_types ) > 1 ) {
		$where         = get_posts_by_author_sql( 'post' );
		$post_type_sql = "'" . implode( "', '", $post_types ) . "'";
		$where_sql     = preg_replace( "/post_type = 'post'/", "post_type IN ($post_type_sql)", $where );
	} else {
		$where_sql = get_posts_by_author_sql( $post_types[0] );
	}

	// fields sql
	switch ( (string) $fields ) {
	case 'ids':   $select_sql = "$wpdb->posts.ID"; break;
	case 'names': $select_sql = "$wpdb->posts.post_title"; break;
	case 'slugs': $select_sql = "$wpdb->posts.post_name"; break;
	default: $select_sql = "$wpdb->posts.*"; break; // all fields
	}

	// sql for order not RAND
	$termcount_sql = $post_date_sql = $having_sql = '';
	if ( $order_sql != 'RAND()' ) {
		$termcount_sql = " , count(distinct tr.term_taxonomy_id) as termcount";
		$having_sql = "GROUP BY $wpdb->posts.ID
               HAVING SUM(CASE WHEN tt.term_id IN ($term_ids_sql) THEN 1 ELSE 0 END) > 0";
		$post_date_sql = " $wpdb->posts.$date_format";
	}

	// post thumbnail sql
	$meta_join_sql = $meta_where_sql = '';
	if ( $post_thumbnail ) {
		$meta_query = array( array( 'key' => '_thumbnail_id' ) );
		$meta = get_meta_sql( $meta_query, 'post', $wpdb->posts, 'ID' );
		$meta_join_sql = ( isset( $meta['join'] ) && $meta['join'] ) ? $meta['join'] : '';
		$meta_where_sql = ( isset( $meta['where'] ) && $meta['where'] ) ? $meta['where'] : '';

		if ( ( '' == $meta_join_sql ) || ( '' == $meta_join_sql ) )
			$meta_join_sql = $meta_where_sql = '';
	}

	$query = "SELECT $select_sql$termcount_sql FROM $wpdb->posts INNER JOIN {$wpdb->term_relationships} tr ON ($wpdb->posts.ID = tr.object_id) INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)$meta_join_sql $where_sql AND $wpdb->posts.ID NOT IN ($post_ids_sql)$date_sql AND ( tt.term_id IN ($term_ids_sql) )$meta_where_sql $having_sql ORDER BY$post_date_sql $order_sql$limit_sql";

	$last_changed = wp_cache_get( 'last_changed', 'posts' );
	if ( ! $last_changed ) {
		$last_changed = microtime();
		wp_cache_set( 'last_changed', $last_changed, 'posts' );
	}

	$key = md5( $query );
	$key = "get_related_taxonomy_posts:$key:$last_changed";
	if ( ! $results = wp_cache_get( $key, 'posts' ) ) {
		$results = $wpdb->get_results( $query );
		wp_cache_set( $key, $results, 'posts' );
	}

	if ( $results ) {
		if ( $order_sql != 'RAND()' ) {
			/* add score to order by termcount AND post_date */
			for ( $i=0; $i < count( $results ) ; $i++ ) {
				$results[ $i ]->score = array( $results[ $i ]->termcount, $i );
			}
			/* order related posts */
			if ( function_exists( 'km_rpbt_related_posts_by_taxonomy_cmp' ) )
				uasort(  $results, 'km_rpbt_related_posts_by_taxonomy_cmp' );
		}

		switch ( (string) $fields ) {
		case 'ids': $pluck = 'ID'; break;
		case 'names': $pluck = 'post_title'; break;
		case 'slugs': $pluck = 'post_name'; break;
		}

		$results = ( isset( $pluck ) ) ? wp_list_pluck( $results, $pluck ) : $results;
		$posts_per_page = ( absint( $posts_per_page ) > 0 ) ? absint( $posts_per_page ) : 5;
		$results = array_slice( $results, 0, $posts_per_page );

	} else {
		$results = array();
	}

	return apply_filters( 'related_posts_by_taxonomy', $results, $post_id, $taxonomies, $args );
}


/**
 * comparison function to sort the related posts by common terms and post date order.
 *
 * @since 0.1
 *
 * @param array $item1
 * @param array $item2
 * @return int
 */
function km_rpbt_related_posts_by_taxonomy_cmp( $item1, $item2 ) {
	if ( $item1->score[0] != $item2->score[0] ) {
		return $item1->score[0] < $item2->score[0] ? 1 : -1;
	} else {
		return $item1->score[1] < $item2->score[1] ? -1 : 1; // DESC
	}
}


/**
 * Validates ids.
 * Checks if its a comma separated string or an array with ids
 *
 * @since 0.2
 *
 * @param string|array Comma seperated list or array with ids
 * @return array Array with postive integers
 */
function km_rpbt_related_posts_by_taxonomy_validate_ids( $ids ) {

	if ( !is_array( $ids ) ) {
		/* allow positive integers, 0 and commas only */
		$ids = preg_replace( '/[^0-9,]/', '', (string) $ids );
		/* convert string to array */
		$ids = explode( ',', $ids );
	}

	/* convert to integers and remove 0 values */
	$ids = array_filter( array_map( 'intval', (array) $ids ) );
	/* remove duplicates */
	$ids = array_unique( $ids );

	return $ids;
}