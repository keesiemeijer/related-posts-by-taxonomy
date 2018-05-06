<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Queries for related posts by taxonomy.
 *
 * @since 2.4.2
 *
 * @global object       $wpdb
 *
 * @param int          $post_id    The post id to get related posts for.
 * @param array|string $taxonomies The taxonomies to retrieve related posts from.
 * @param array|string $args       Optional. Change what is returned.
 * @return array Array with post objects. Empty array if no related posts found.
 */
function km_rpbt_query_related_posts( $post_id = 0, $taxonomies = 'category', $args = '' ) {
	global $wpdb;

	// Get valid taxonomies.
	$taxonomies = km_rpbt_get_taxonomies( $taxonomies );

	if ( ! absint( $post_id ) || empty( $taxonomies ) ) {
		return array();
	}

	$args  = km_rpbt_sanitize_args( $args );
	$terms = km_rpbt_get_terms( $post_id, $taxonomies, $args );

	if ( empty( $terms ) ) {
		return array();
	}

	$args['related_terms'] = $terms;
	$args['termcount']     = array();

	// Term ids sql.
	if ( count( $terms ) > 1 ) {
		$term_ids_sql = 'tt.term_id IN (' . implode( ', ', $terms ) . ')';
	} else {
		$term_ids_sql = ( isset( $terms[0] ) ) ? 'tt.term_id = ' . $terms[0] : 'tt.term_id = 0';
	}

	if ( ! $args['include_self'] ) {
		// Add current post ID to exclude.
		$args['exclude_posts'][] = $post_id;
	}
	$args['exclude_posts'] = array_unique( $args['exclude_posts'] );

	$post_ids_sql = '';
	if ( $args['exclude_posts'] ) {
		// Post ids sql.
		$post_ids_sql = "AND $wpdb->posts.ID";
		$post_ids_sql .= ' NOT IN (' . implode( ', ', $args['exclude_posts'] ) . ')';
	}

	// Default to post type post if no post types are found.
	$args['post_types'] = ( ! empty( $args['post_types'] ) ) ? $args['post_types'] : array( 'post' );

	// Where sql (post types and post status).
	if ( count( $args['post_types'] ) > 1 ) {
		$where         = get_posts_by_author_sql( 'post', true, null, $args['public_only'] );
		$post_type_sql = "'" . implode( "', '", $args['post_types'] ) . "'";
		$where_sql     = preg_replace( "/post_type = 'post'/", "post_type IN ($post_type_sql)", $where );
	} else {
		$where_sql = get_posts_by_author_sql( $args['post_types'][0], true, null, $args['public_only'] );
	}

	$order_by_rand = false;

	// Order sql.
	switch ( strtoupper( (string) $args['order'] ) ) {
		case 'ASC':
			$order_sql = 'ASC';
			break;
		case 'RAND':
			$order_sql = 'RAND()';
			$order_by_rand = true;
			break;
		default:
			$order_sql = 'DESC';
			break;
	}

	$allowed_fields = array(
		'ids' => 'ID',
		'names' => 'post_title',
		'slugs' => 'post_name',
	);

	// Select sql.
	$fields = strtolower( (string) $args['fields'] );
	if ( in_array( $fields, array_keys( $allowed_fields ) ) ) {
		$select_sql = "$wpdb->posts." . $allowed_fields[ $fields ];
		if ( 'ids' !== $fields ) {
			$select_sql .= ", $wpdb->posts.ID";
		}
	} else {
		// Not an allowed field - return full post objects.
		$select_sql = "$wpdb->posts.*";
	}

	// Limit sql.
	$limit_sql = '';
	if ( -1 !== (int) $args['limit_posts'] ) {
		$limit_posts = absint( $args['limit_posts'] );
		if ( $limit_posts ) {
			$limit_sql = 'LIMIT 0,' . $limit_posts;
		}
	}

	$orderby = strtolower( (string) $args['orderby'] );
	if ( ! in_array( $orderby, array( 'post_date', 'post_modified' ) ) ) {
		$orderby = 'post_date';
	}

	// Limit date sql.
	$limit_date_sql = '';
	if ( $args['limit_year'] || $args['limit_month'] ) {
		// Year takes precedence over month.
		$time_limit  = ( $args['limit_year'] ) ? $args['limit_year'] : $args['limit_month'];
		$time_string = ( $args['limit_year'] ) ? 'year' : 'month';
		$last_date = date( 'Y-m-t', strtotime( 'now' ) );
		$first_date  = date( 'Y-m-d', strtotime( "$last_date -$time_limit $time_string" ) );
		$limit_date_sql    = " AND $wpdb->posts.$orderby > '$first_date 23:59:59' AND $wpdb->posts.$orderby <= '$last_date 23:59:59'";
		$limit_sql = '';
	}

	$order_by_sql = '';
	$group_by_sql = "$wpdb->posts.ID";

	// Order included post at the top.
	if ( true === $args['include_self'] ) {
		$order_by_sql .= "CASE WHEN $wpdb->posts.ID = $post_id THEN 1 ELSE 2 END, ";
	}

	if ( ! $order_by_rand ) {
		if ( $args['related'] ) {
			// Related terms count sql.
			$select_sql .= ' , count(distinct tt.term_taxonomy_id) as termcount';
		}
		$order_by_sql .= "$wpdb->posts.$orderby";
	}

	// Post thumbnail sql.
	$meta_join_sql = $meta_where_sql = '';
	if ( $args['post_thumbnail'] ) {
		$meta_query = array(
			array(
				'key' => '_thumbnail_id',
			),
		);
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
	 * @param string $select_sql The SELECT clause of the query.
	 */
	$select_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_fields', array( $select_sql, $post_id, $taxonomies, $args ) );

	$join_sql  = "INNER JOIN {$wpdb->term_relationships} tr ON ($wpdb->posts.ID = tr.object_id)";
	$join_sql .= " INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id){$meta_join_sql}";

	/**
	 * Filter the JOIN clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $join_sql The JOIN clause of the query.
	 */
	$join_sql  = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_join', array( $join_sql, $post_id, $taxonomies, $args ) );

	$where_sql = "{$where_sql} {$post_ids_sql}{$limit_date_sql} AND ( $term_ids_sql ){$meta_where_sql}";

	/**
	 * Filter the WHERE clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $where The WHERE clause of the query.
	 */
	$where_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_where', array( $where_sql, $post_id, $taxonomies, $args ) );

	/**
	 * Filter the GROUP BY clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $groupby The GROUP BY clause of the query.
	 */
	$group_by_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_groupby', array( $group_by_sql, $post_id, $taxonomies, $args ) );

	$order_by_sql = "{$order_by_sql} {$order_sql}";

	/**
	 * Filter the ORDER BY clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $orderby The ORDER BY clause of the query.
	 */
	$order_by_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_orderby', array( $order_by_sql, $post_id, $taxonomies, $args ) );

	/**
	 * Filter the LIMIT clause of the query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $limits The LIMIT clause of the query.
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
	 * @param array $pieces The list of clauses for the query.
	 */
	$clauses = (array) apply_filters_ref_array( 'related_posts_by_taxonomy_posts_clauses', array( compact( $pieces ), $post_id, $taxonomies, $args ) );

	$select_sql   = isset( $clauses['select_sql'] ) ? $clauses['select_sql'] : '';
	$join_sql     = isset( $clauses['join_sql'] ) ? $clauses['join_sql'] : '';
	$where_sql    = isset( $clauses['where_sql'] ) ? $clauses['where_sql'] : '';
	$group_by_sql = isset( $clauses['group_by_sql'] ) ? $clauses['group_by_sql'] : '';
	$order_by_sql = isset( $clauses['order_by_sql'] ) ? $clauses['order_by_sql'] : '';
	$limit_sql    = isset( $clauses['limit_sql'] ) ? $clauses['limit_sql'] : '';

	if ( ! empty( $group_by_sql ) ) {
		$group_by_sql = 'GROUP BY ' . $group_by_sql;
	}

	if ( ! empty( $order_by_sql ) ) {
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

		/* Order the related posts */
		if ( ! $order_by_rand && $args['related'] ) {

			/* Add the (termcount) score and key to results for ordering*/
			for ( $i = 0; $i < count( $results ); $i++ ) {
				$results[ $i ]->score = array( $results[ $i ]->termcount, $i );
			}

			/* Order the related posts */
			uasort( $results, 'km_rpbt_related_posts_by_taxonomy_cmp' );
		}

		$results = array_values( $results );

		/* Get the number of related posts */
		if ( -1 !== (int) $args['posts_per_page'] ) {
			$posts_per_page = absint( $args['posts_per_page'] );
			$posts_per_page = ( $posts_per_page ) ? $posts_per_page : 5;
			$results = array_slice( $results, 0, $posts_per_page );
		}

		// Add default values.
		for ( $i = 0; $i < count( $results ); $i++ ) {
			if ( isset( $results[ $i ]->termcount ) ) {
				$args['termcount'][] = $results[ $i ]->termcount;
			}

			$results[ $i ]->rpbt_current    = $post_id;
			$results[ $i ]->rpbt_post_class = '';
			$results[ $i ]->rpbt_type       = isset( $args['type'] ) ? $args['type'] : '';
		}

		if ( in_array( $fields, array_keys( $allowed_fields ) ) ) {
			/* Get the field used in the query */
			$results = wp_list_pluck( $results, $allowed_fields[ $fields ] );
		}
	} else {
		$results = array();
	}

	/**
	 * Filter related_posts_by_taxonomy.
	 *
	 * @since 0.1
	 *
	 * @param array $results    Related posts. Array with Post objects or post IDs or post titles or post slugs.
	 * @param int   $post_id    Post id used to get the related posts.
	 * @param array $taxonomies Taxonomies used to get the related posts.
	 * @param array $args       Function arguments used to get the related posts.
	 */
	return apply_filters( 'related_posts_by_taxonomy', $results, $post_id, $taxonomies, $args );
}

/**
 * Comparison function to sort the related posts by common terms and post date order.
 *
 * @since 0.1
 *
 * @param array $item1 Item 1.
 * @param array $item2 Item 2.
 * @return int
 */
function km_rpbt_related_posts_by_taxonomy_cmp( $item1, $item2 ) {
	if ( $item1->score[0] != $item2->score[0] ) {
		return $item1->score[0] < $item2->score[0] ? 1 : -1;
	} else {
		return $item1->score[1] < $item2->score[1] ? -1 : 1;
	}
}
