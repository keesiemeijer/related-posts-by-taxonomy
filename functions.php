<?php
/**
 * Gets related posts by taxonomy.
 *
 * @since 0.1
 *
 * @global object       $wpdb
 *
 * @param int     $post_id    The post id to get related posts for.
 * @param array|string $taxonomies The taxonomies to retrieve related posts from
 * @param array|string $args       Optional. Change what is returned
 * @return array                    Empty array if no related posts found. Array with post objects.
 */
function km_rpbt_related_posts_by_taxonomy( $post_id = 0, $taxonomies = 'category', $args = '' ) {
	global $wpdb;

	if ( !absint( $post_id ) ) {
		return array();
	}

	$defaults = array(
		'post_types' => 'post', 'posts_per_page' => 5, 'order' => 'DESC',
		'fields' => '', 'limit_posts' => -1, 'limit_year' => '',
		'limit_month' => '', 'limit_date_from' => '', 'orderby' => 'post_date',
		'exclude_terms' => '', 'include_terms' => '',  'exclude_posts' => '',
		'post_thumbnail' => '', 'relation' => 'AND',
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	$taxonomies = ( !empty( $taxonomies ) ) ? $taxonomies : array( 'category' );

	if ( !is_array( $taxonomies ) ) {
		$taxonomies = array_unique( explode( ',', (string) $taxonomies ) );
	}

	$terms = wp_get_object_terms( $post_id, $taxonomies, array( 'fields' => 'ids' ) );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return array();
	}

	if ( !empty( $include_terms ) ) {
		$include_terms = km_rpbt_related_posts_by_taxonomy_validate_ids( $include_terms );
		$terms = array_values( array_intersect( $include_terms, $terms ) );
	} else {
		$exclude_terms = km_rpbt_related_posts_by_taxonomy_validate_ids( $exclude_terms );
		$terms = array_values( array_diff( $terms , $exclude_terms ) );
	}

	if ( empty( $terms ) ) {
		return array();
	}

	// term ids sql
	if ( count( $terms ) > 1 ) {
		$term_ids_sql = "tt.term_id IN (" . implode( ', ', $terms ) . ")";
	} else {
		$term_ids_sql = ( isset( $terms[0] ) ) ? "tt.term_id = " .  $terms[0] : "tt.term_id = 0";
	}

	// validates ids and returns an array
	$exclude_posts  = km_rpbt_related_posts_by_taxonomy_validate_ids( $exclude_posts );

	$exclude_posts[] = $post_id;
	$exclude_posts   = array_unique( $exclude_posts );

	// post ids sql
	$post_ids_sql = "AND $wpdb->posts.ID";
	if ( count( $exclude_posts ) > 1 ) {
		$post_ids_sql .= " NOT IN (" . implode( ', ', $exclude_posts ) .")";
	} else {
		$post_ids_sql .= " != $post_id";
	}

	$post_types = ( !empty( $post_types ) ) ? $post_types : array( 'post' );

	if ( !is_array( $post_types ) ) {
		$post_types = array_unique( explode( ',', (string) $post_types ) );
	}

	$post_type_arr = array();
	foreach ( (array) $post_types as $type ) {
		$post_type_obj = get_post_type_object( $type );
		if ( $post_type_obj ) {
			$post_type_arr[] = $type;
		}
	}
	$post_types = ( !empty( $post_type_arr ) ) ? $post_type_arr : array( 'post' );

	// where sql (post types and post status)
	if ( count( $post_types ) > 1 ) {
		$where         = get_posts_by_author_sql( 'post' );
		$post_type_sql = "'" . implode( "', '", $post_types ) . "'";
		$where_sql     = preg_replace( "/post_type = 'post'/", "post_type IN ($post_type_sql)", $where );
	} else {
		$where_sql = get_posts_by_author_sql( $post_types[0] );
	}

	$order_by_rand = false;

	// order sql
	switch ( strtoupper( (string) $order ) ) {
	case 'ASC':  $order_sql = 'ASC'; break;
	case 'RAND': $order_sql = 'RAND()'; $order_by_rand = true; break;
	default:     $order_sql = 'DESC'; break;
	}

	$allowed_fields = array( 'ids' => 'ID', 'names' => 'post_title', 'slugs' => 'post_name' );

	// select sql
	$fields = strtolower( (string) $fields );
	if ( in_array( $fields, array_keys( $allowed_fields ) ) ) {
		$select_sql = "$wpdb->posts." . $allowed_fields[ $fields ];
	} else {
		// not an allowed field - return full post objects
		$select_sql = "$wpdb->posts.*";
	}

	// limit sql
	$limit_sql = '';
	if ( -1 !== (int) $limit_posts ) {
		$limit_posts = absint( $limit_posts );
		if ( $limit_posts ) {
			$limit_sql = ' LIMIT 0,' . $limit_posts;
		}
	}

	$relation = strtoupper( (string) $relation );
	if ( !in_array( $relation, array( 'AND', 'OR' ) ) ) {
		$relation = 'AND';
	}

	$orderby = strtolower( (string) $orderby );
	if ( !in_array( $orderby, array( 'post_date', 'post_modified' ) ) ) {
		$orderby = 'post_date';
	}

	// limit date sql
	$limit_date_sql = '';
	$limit_year = absint( $limit_year );
	$limit_month = absint( $limit_month );
	if ( $limit_year || $limit_month  ) {
		// year takes precedence over month
		$time_limit  = ( $limit_year ) ? $limit_year : $limit_month ;
		$time_string = ( $limit_year ) ? 'year' : 'month';
		$limit_date = "now";
		if ( 'post_date' === $limit_date_from ) {
			$limit_date = get_post( $id = $post_id );
			$limit_date = ( isset( $limit_date->post_date ) ) ? $limit_date->post_date : "now";
		}
		$last_date = date( 'Y-m-t', strtotime( $limit_date ) );
		$first_date  = date( 'Y-m-d', strtotime( "$last_date -$time_limit $time_string" ) );
		$limit_date_sql    = " AND $wpdb->posts.$orderby > '$first_date 23:59:59' AND $wpdb->posts.$orderby <= '$last_date 23:59:59'";
		$limit_sql = ''; // limit by date takes precedence over limit by posts
	}

	$order_by_sql = '';
	$group_by_sql = "GROUP BY $wpdb->posts.ID";

	if ( !$order_by_rand ) {
		if ( 'AND' === $relation ) {
			// sql for most terms in common
			$select_sql .= " , count(distinct tr.term_taxonomy_id) as termcount";
			$group_by_sql .= " HAVING SUM(CASE WHEN {$term_ids_sql} THEN 1 ELSE 0 END) > 0";
		}
		$order_by_sql = " $wpdb->posts.$orderby";
	}

	// post thumbnail sql
	$meta_join_sql = $meta_where_sql = '';
	if ( $post_thumbnail ) {
		$meta_query = array( array( 'key' => '_thumbnail_id' ) );
		$meta = get_meta_sql( $meta_query, 'post', $wpdb->posts, 'ID' );
		$meta_join_sql = ( isset( $meta['join'] ) && $meta['join'] ) ? $meta['join'] : '';
		$meta_where_sql = ( isset( $meta['where'] ) && $meta['where'] ) ? $meta['where'] : '';

		if ( ( '' === $meta_join_sql ) || ( '' === $meta_join_sql ) ) {
			$meta_join_sql = $meta_where_sql = '';
		}
	}

	$query = "SELECT {$select_sql} FROM $wpdb->posts INNER JOIN {$wpdb->term_relationships} tr ON ($wpdb->posts.ID = tr.object_id) INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id){$meta_join_sql} {$where_sql} {$post_ids_sql}{$limit_date_sql} AND ( $term_ids_sql ){$meta_where_sql} {$group_by_sql} ORDER BY{$order_by_sql} {$order_sql}{$limit_sql}";
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

		if ( !$order_by_rand && ( 'AND' === $relation ) ) {

			/* add (termcount) score and key to results */
			for ( $i=0; $i < count( $results ) ; $i++ ) {
				$results[ $i ]->score = array( $results[ $i ]->termcount, $i );
			}

			/* order related posts */
			uasort(  $results, 'km_rpbt_related_posts_by_taxonomy_cmp' );
		}

		$results = array_values( $results );

		if ( in_array( $fields, array_keys( $allowed_fields ) ) ) {
			$results = wp_list_pluck( $results, $allowed_fields[ $fields ] );
		}

		if ( -1 !== (int) $posts_per_page ) {
			$posts_per_page = ( absint( $posts_per_page ) ) ? absint( $posts_per_page ) : 5;
			$results = array_slice( $results, 0, $posts_per_page );
		}

	} else {
		$results = array();
	}

	/**
	 * Filter related_posts_by_taxonomy.
	 *
	 * @since 0.1
	 *
	 * @param array   $results    Related posts. Array with Post objects or post IDs or post titles or post slugs.
	 * @param int     $post_id    Post id used to get the related posts.
	 * @param array   $taxonomies Taxonomies used to get the related posts.
	 * @param array   $args       Function arguments used to get the related posts.
	 */
	return apply_filters( 'related_posts_by_taxonomy', $results, $post_id, $taxonomies, $args );
}


/**
 * comparison function to sort the related posts by common terms and post date order.
 *
 * @since 0.1
 *
 * @param array   $item1
 * @param array   $item2
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
 * Checks if ids is a comma separated string or an array with ids
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