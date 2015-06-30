<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
		'limit_month' => '', 'orderby' => 'post_date',
		'exclude_terms' => '', 'include_terms' => '',  'exclude_posts' => '',
		'post_thumbnail' => '', 'related' => true,
	);

	$args = wp_parse_args( $args, $defaults );

	$taxonomies = ( !empty( $taxonomies ) ) ? $taxonomies : array( 'category' );

	if ( !is_array( $taxonomies ) ) {
		$taxonomies = array_unique( explode( ',', (string) $taxonomies ) );
	}

	$terms = array();

	// validates ids and returns an array
	$included = km_rpbt_related_posts_by_taxonomy_validate_ids( $args['include_terms'] );

	if ( !$args['related'] && !empty( $included ) ) {
		// related, use included term ids
		$terms = $included;
	} else {

		// related and not related terms
		$terms = wp_get_object_terms( $post_id, array_map( 'trim', (array) $taxonomies ), array( 'fields' => 'ids' ) );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		// only use included terms from the post terms
		if ( $args['related'] && !empty( $included ) ) {
			$terms = array_values( array_intersect( $included, $terms ) );
		}
	}

	// exclude terms
	if ( empty( $included ) ) {
		// validates ids and returns an array
		$excluded = km_rpbt_related_posts_by_taxonomy_validate_ids( $args['exclude_terms'] );
		$terms    = array_values( array_diff( $terms , $excluded ) );
	}

	if ( empty( $terms ) ) {
		return array();
	}

	$args['related_terms'] = $terms;

	// term ids sql
	if ( count( $terms ) > 1 ) {
		$term_ids_sql = "tt.term_id IN (" . implode( ', ', $terms ) . ")";
	} else {
		$term_ids_sql = ( isset( $terms[0] ) ) ? "tt.term_id = " .  $terms[0] : "tt.term_id = 0";
	}

	// validates ids and returns an array
	$exclude_posts  = km_rpbt_related_posts_by_taxonomy_validate_ids( $args['exclude_posts'] );

	// add current post ID to exclude
	$exclude_posts[] = $post_id;
	$exclude_posts   = array_unique( $exclude_posts );

	// post ids sql
	$post_ids_sql = "AND $wpdb->posts.ID";
	if ( count( $exclude_posts ) > 1 ) {
		$post_ids_sql .= " NOT IN (" . implode( ', ', $exclude_posts ) .")";
	} else {
		$post_ids_sql .= " != $post_id";
	}

	// post types
	if ( !is_array( $args['post_types'] ) ) {
		$args['post_types'] = explode( ',', (string) $args['post_types'] );
	}

	// sanitize post type names and remove duplicates
	$post_types = array_unique( array_map( 'sanitize_key', (array) $args['post_types'] ) );
	$post_types = array_filter( $post_types, 'post_type_exists' );

	// default to post type post if no post types are found
	$post_types = ( !empty( $post_types ) ) ? $post_types : array( 'post' );

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
	switch ( strtoupper( (string) $args['order'] ) ) {
	case 'ASC':  $order_sql = 'ASC'; break;
	case 'RAND': $order_sql = 'RAND()'; $order_by_rand = true; break;
	default:     $order_sql = 'DESC'; break;
	}

	$allowed_fields = array( 'ids' => 'ID', 'names' => 'post_title', 'slugs' => 'post_name' );

	// select sql
	$fields = strtolower( (string) $args['fields'] );
	if ( in_array( $fields, array_keys( $allowed_fields ) ) ) {
		$select_sql = "$wpdb->posts." . $allowed_fields[ $fields ];
	} else {
		// not an allowed field - return full post objects
		$select_sql = "$wpdb->posts.*";
	}

	// limit sql
	$limit_sql = '';
	if ( -1 !== (int) $args['limit_posts'] ) {
		$limit_posts = absint( $args['limit_posts'] );
		if ( $limit_posts ) {
			$limit_sql = 'LIMIT 0,' . $limit_posts;
		}
	}

	$orderby = strtolower( (string) $args['orderby'] );
	if ( !in_array( $orderby, array( 'post_date', 'post_modified' ) ) ) {
		$orderby = 'post_date';
	}

	// limit date sql
	$limit_date_sql = '';
	$limit_year = absint( $args['limit_year'] );
	$limit_month = absint( $args['limit_month'] );
	if ( $limit_year || $limit_month  ) {
		// year takes precedence over month
		$time_limit  = ( $limit_year ) ? $limit_year : $limit_month;
		$time_string = ( $limit_year ) ? 'year' : 'month';
		$last_date = date( 'Y-m-t', strtotime( "now" ) );
		$first_date  = date( 'Y-m-d', strtotime( "$last_date -$time_limit $time_string" ) );
		$limit_date_sql    = " AND $wpdb->posts.$orderby > '$first_date 23:59:59' AND $wpdb->posts.$orderby <= '$last_date 23:59:59'";
		$limit_sql = ''; // limit by date takes precedence over limit by posts
	}

	$order_by_sql = '';
	$group_by_sql = "$wpdb->posts.ID";

	if ( !$order_by_rand ) {
		if ( $args['related'] ) {
			// sql for related terms count
			$select_sql .= " , count(distinct tt.term_taxonomy_id) as termcount";
		}
		$order_by_sql = "$wpdb->posts.$orderby";
	}

	// post thumbnail sql
	$meta_join_sql = $meta_where_sql = '';
	if ( $args['post_thumbnail'] ) {
		$meta_query = array( array( 'key' => '_thumbnail_id' ) );
		$meta = get_meta_sql( $meta_query, 'post', $wpdb->posts, 'ID' );
		$meta_join_sql = ( isset( $meta['join'] ) && $meta['join'] ) ? $meta['join'] : '';
		$meta_where_sql = ( isset( $meta['where'] ) && $meta['where'] ) ? $meta['where'] : '';

		if ( ( '' === $meta_join_sql ) || ( '' === $meta_join_sql ) ) {
			$meta_join_sql = $meta_where_sql = '';
		}
	}

	$pieces   = array( 'select_sql', 'join_sql', 'where_sql', 'group_by_sql', 'order_by_sql', 'limit_sql' );

	/**
	 * Filter the SELECT clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string  $select_sql The SELECT clause of the query.
	 */
	$select_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_fields', array( $select_sql, $post_id, $taxonomies, $args ) );

	$join_sql  = "INNER JOIN {$wpdb->term_relationships} tr ON ($wpdb->posts.ID = tr.object_id)";
	$join_sql .= " INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id){$meta_join_sql}";

	/**
	 * Filter the JOIN clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string  $join_sql The JOIN clause of the query.
	 */
	$join_sql  = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_join', array( $join_sql, $post_id, $taxonomies, $args ) );

	$where_sql = "{$where_sql} {$post_ids_sql}{$limit_date_sql} AND ( $term_ids_sql ){$meta_where_sql}";

	/**
	 * Filter the WHERE clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string  $where The WHERE clause of the query.
	 */
	$where_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_where', array( $where_sql, $post_id, $taxonomies, $args ) );

	/**
	 * Filter the GROUP BY clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string  $groupby The GROUP BY clause of the query.
	 */
	$group_by_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_groupby', array( $group_by_sql, $post_id, $taxonomies, $args ) );

	$order_by_sql = "{$order_by_sql} {$order_sql}";

	/**
	 * Filter the ORDER BY clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string  $orderby The ORDER BY clause of the query.
	 */
	$order_by_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_orderby', array( $order_by_sql, $post_id, $taxonomies, $args ) );

	/**
	 * Filter the LIMIT clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string  $limits The LIMIT clause of the query.
	 */
	$limit_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_limits', array( $limit_sql, $post_id, $taxonomies, $args ) );

	/**
	 * Filter all query clauses at once, for convenience.
	 *
	 * Covers the WHERE, GROUP BY, JOIN, ORDER BY,
	 * fields (SELECT), and LIMITS clauses.
	 *
	 * @since 0.3.1
	 *
	 * @param array   $pieces The list of clauses for the query.
	 */
	$clauses = (array) apply_filters_ref_array( 'related_posts_by_taxonomy_posts_clauses', array( compact( $pieces ), $post_id, $taxonomies, $args ) );

	foreach ( $pieces as $piece ) {
		$$piece = isset( $clauses[ $piece ] ) ? $clauses[ $piece ] : '';
	}

	if ( !empty( $group_by_sql ) ) {
		$group_by_sql = 'GROUP BY ' . $group_by_sql;
	}

	if ( !empty( $order_by_sql ) ) {
		$order_by_sql = 'ORDER BY ' . $order_by_sql;
	}

	$query = "SELECT {$select_sql} FROM $wpdb->posts {$join_sql} {$where_sql} {$group_by_sql} {$order_by_sql} {$limit_sql}";

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

		if ( !$order_by_rand && $args['related'] ) {

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

		if ( -1 !== (int) $args['posts_per_page'] ) {
			$posts_per_page = absint( $args['posts_per_page'] );
			$posts_per_page = ( $posts_per_page ) ? $posts_per_page : 5;
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

	return array_values( array_unique( $ids ) );
}