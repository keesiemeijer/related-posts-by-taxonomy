<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The query for related posts.
 *
 * @since 2.5.0
 *
 * @global object       $wpdb
 *
 * @param int          $post_id    The post id to get related posts for.
 * @param array|string $taxonomies The taxonomies to use for the related posts query. default 'category'.
 * @param string|array $args       {
 *     Optional. Query variables to get related posts.
 *
 *     @type string|array   $post_types       Post types to use for related posts query. Array or comma separated
 *                                            list of post type names. Default 'post'.
 *     @type int            $posts_per_page   Number of related posts. Default 5.
 *     @type string         $order            Order of related posts. Accepts 'DESC', 'ASC' and 'RAND'. Default 'DESC'.
 *     @type string         $orderby          Order by post date or by date modified.
 *                                            Accepts 'post_date'and 'post_modified'. Default 'post_date'.
 *     @type string         $fields           Return full post objects, IDs, post titles or post slugs.
 *                                            Accepts 'all', 'ids', 'names' or 'slugs'.  Default is 'all'.
 *     @type array|string   $include_terms    Terms to use for the related posts query. Array or comma separated list of
 *                                            term ids. Default empty (query by the terms of the current post).
 *     @type boolean        $include_parents  Whether to include parent terms in the query for related posts. Default false.
 *     @type boolean        $include_children Whether to include child terms in the query for related posts. Default false.
 *     @type array|string   $exclude_terms    Terms to exlude for the related posts query. Array or comma separated
 *                                            list of term ids. Default empty
 *     @type array|string   $exclude_post     Exclude posts for the related posts query. Array or comma separated
 *                                            list of post ids. Default empty.
 *     @type int            $limit_posts      Limit the posts to search related posts in. Default -1 (search in all posts).
 *     @type int            $limit_month      Limit the posts to the past months to search related posts in.
 *     @type boolean        $post_thumbnail   Whether to query for related posts with a featured image only. Default false.
 *     @type boolean        $public_only      Whether to exclude private posts in the related posts display, even if
 *                                            the current user has the capability to see those posts.
 *                                            Default false (include private posts)
 *     @type string|boolean $include_self     Whether to include the current post in the related posts results. The included
 *                                            post is ordered at the top. Use 'regular_order' to include the current post ordered by
 *                                            terms in common. Default false (exclude current post).
 *     @type string         $meta_key         Meta key.
 *     @type string         $meta_value       Meta value.
 *     @type string         $meta_compare     MySQL operator used for comparing the $meta_value. Accepts '=',
 *                                            '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE',
 *                                            'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'REGEXP',
 *                                            'NOT REGEXP', 'RLIKE', 'EXISTS' or 'NOT EXISTS'.
 *                                            Default is 'IN' when `$meta_value` is an array, '=' otherwise.
 *     @type string         $meta_type        MySQL data type that the meta_value column will be CAST to for
 *                                            comparisons. Accepts 'NUMERIC', 'BINARY', 'CHAR', 'DATE',
 *                                            'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', or 'UNSIGNED'.
 *                                            Default is 'CHAR'.
 * }
 * @return array Array with post objects. Empty array if no related posts found.
 */
function km_rpbt_query_related_posts( $post_id, $taxonomies = 'category', $args = '' ) {
	global $wpdb;

	$post_id    = absint( $post_id );
	$taxonomies = km_rpbt_get_taxonomies( $taxonomies );
	$args       = km_rpbt_sanitize_args( $args );
	$terms      = km_rpbt_get_terms( $post_id, $taxonomies, $args );

	$args['related_terms'] = $terms;
	$args['termcount']     = array();
	$args['post_id']       = $post_id;
	$args['taxonomies']    = $taxonomies;

	// Sort arrays for WP object cache key.
	$args = km_rpbt_nested_array_sort( $args );

	/**
	 * Filter whether to use your own related posts.
	 *
	 * @since 2.5.0
	 *
	 * @param null|array $related_posts Array or null. Prevent the query for related posts by
	 *                                  returning an array (with post objects or ids).
	 *                                  Default null (do the query for related posts).
	 * @param array      $args          Array with query arguments.
	 */
	$related_posts = apply_filters( 'related_posts_by_taxonomy_pre_related_posts', null, $args );
	if ( is_array( $related_posts ) ) {
		return $related_posts;
	}

	if ( ! $post_id || empty( $terms ) ) {
		// Invalid post ID, invalid taxonomies, or no terms found.
		return array();
	}

	// Back compat for filters.
	unset( $args['post_id'], $args['taxonomies'] );

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
		// Month takes precedence over year.
		$time_limit  = ( $args['limit_month'] ) ? $args['limit_month'] : $args['limit_year'];
		$time_string = ( $args['limit_month'] ) ? 'month' : 'year';
		$last_date = date( 'Y-m-t', strtotime( 'now' ) );
		$first_date  = date( 'Y-m-d', strtotime( "$last_date -$time_limit $time_string" ) );
		$limit_date_sql = " AND $wpdb->posts.$orderby > '$first_date 23:59:59' AND $wpdb->posts.$orderby <= '$last_date 23:59:59'";
		$limit_sql = '';
	}

	$order_by_sql = '';
	$group_by_sql = "$wpdb->posts.ID";

	// Order included post at the top.
	if ( true === $args['include_self'] ) {
		$order_by_sql .= "CASE WHEN $wpdb->posts.ID = $post_id THEN 1 ELSE 2 END, ";
	}

	if ( ! $order_by_rand ) {
		$select_sql .= ' , count(distinct tt.term_taxonomy_id) as termcount';
		$order_by_sql .= "$wpdb->posts.$orderby";
	}

	$meta_query = new WP_Meta_Query();
	$meta_query->parse_query_vars( $args );
	$meta_query = is_array( $meta_query->queries ) ? $meta_query->queries : array();

	// Default to AND.
	if ( isset( $meta_query['relation'] ) ) {
		$meta_query['relation'] = 'AND';
	}

	if ( $args['post_thumbnail'] ) {
		$meta_query[] = array( 'key' => '_thumbnail_id' );
	}

	/**
	 * Limit the related posts query by post meta query.
	 *
	 * The default relation of the meta query is 'AND'.
	 *
	 * @since 2.6.0
	 *
	 * @param array $meta_query Meta query. Array of meta query arguments.
	 * @param int   $post_id    Post ID.
	 * @param array $taxonomies Array of Taxonomy names.
	 * @param array $args       Related posts query arguments.
	 */
	$meta_query = apply_filters( 'related_posts_by_taxonomy_posts_meta_query', $meta_query, $post_id, $taxonomies, $args );
	$meta_query = is_array( $meta_query ) ? $meta_query : array();

	$meta_join_sql = $meta_where_sql = '';
	if ( ! empty( $meta_query ) ) {
		$meta = get_meta_sql( $meta_query, 'post', $wpdb->posts, 'ID' );
		$meta_join_sql = ( isset( $meta['join'] ) && $meta['join'] ) ? $meta['join'] : '';
		$meta_where_sql = ( isset( $meta['where'] ) && $meta['where'] ) ? $meta['where'] : '';

		if ( ( '' === $meta_join_sql ) || ( '' === $meta_where_sql ) ) {
			$meta_join_sql = $meta_where_sql = '';
		}
	}

	$pieces   = array( 'select_sql', 'join_sql', 'where_sql', 'group_by_sql', 'order_by_sql', 'limit_sql' );

	/**
	 * Filter the SELECT clause of the related posts query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $select_sql The SELECT clause of the related posts query.
	 * @param int    $post_id    Post ID.
	 * @param array  $taxonomies Array of Taxonomy names.
	 * @param array  $args       Related posts query arguments.
	 */
	$select_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_fields', array( $select_sql, $post_id, $taxonomies, $args ) );

	$join_sql  = "INNER JOIN {$wpdb->term_relationships} tr ON ($wpdb->posts.ID = tr.object_id)";
	$join_sql .= " INNER JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id){$meta_join_sql}";

	/**
	 * Filter the JOIN clause of the related posts query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $join_sql   The JOIN clause of the related posts query.
	 * @param int    $post_id    Post ID.
	 * @param array  $taxonomies Array of Taxonomy names.
	 * @param array  $args       Related posts query arguments.
	 */
	$join_sql  = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_join', array( $join_sql, $post_id, $taxonomies, $args ) );

	$where_sql = "{$where_sql} {$post_ids_sql}{$limit_date_sql} AND ( $term_ids_sql ){$meta_where_sql}";

	/**
	 * Filter the WHERE clause of the related posts query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $where      The WHERE clause of the related posts query.
	 * @param int    $post_id    Post ID.
	 * @param array  $taxonomies Array of Taxonomy names.
	 * @param array  $args       Related posts query arguments.
	 */
	$where_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_where', array( $where_sql, $post_id, $taxonomies, $args ) );

	/**
	 * Filter the GROUP BY clause of the related posts query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $groupby    The GROUP BY clause of the related posts query.
	 * @param int    $post_id    Post ID.
	 * @param array  $taxonomies Array of Taxonomy names.
	 * @param array  $args       Related posts query arguments.
	 */
	$group_by_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_groupby', array( $group_by_sql, $post_id, $taxonomies, $args ) );

	$order_by_sql = "{$order_by_sql} {$order_sql}";

	/**
	 * Filter the ORDER BY clause of the related posts query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $orderby    The ORDER BY clause of the related posts query.
	 * @param int    $post_id    Post ID.
	 * @param array  $taxonomies Array of Taxonomy names.
	 * @param array  $args       Related posts query arguments.
	 */
	$order_by_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_orderby', array( $order_by_sql, $post_id, $taxonomies, $args ) );

	/**
	 * Filter the LIMIT clause of the related posts query.
	 *
	 * @since 0.3.1
	 *
	 * @param string $limits     The LIMIT clause of the related posts query.
	 * @param int    $post_id    Post ID.
	 * @param array  $taxonomies Array of Taxonomy names.
	 * @param array  $args       Related posts query arguments.
	 */
	$limit_sql = apply_filters_ref_array( 'related_posts_by_taxonomy_posts_limits', array( $limit_sql, $post_id, $taxonomies, $args ) );

	/**
	 * Filter all related posts query clauses at once, for convenience.
	 *
	 * Covers the WHERE, GROUP BY, JOIN, ORDER BY,
	 * fields (SELECT), and LIMITS clauses.
	 *
	 * @since 0.3.1
	 *
	 * @param array $pieces     The list of clauses for the related posts query.
	 * @param int   $post_id    Post ID.
	 * @param array $taxonomies Array of Taxonomy names.
	 * @param array $args       Related posts query arguments.
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
		if ( ! $order_by_rand ) {

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
	 * Filter related posts retrieved from the database.
	 *
	 * @since 0.1
	 *
	 * @param array $results    Related posts. Array with Post objects or post IDs or post titles or post slugs.
	 * @param int   $post_id    Post id used to get the related posts.
	 * @param array $taxonomies Taxonomies used to get the related posts.
	 * @param array $args       Arguments used to query the related posts.
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
