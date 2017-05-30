<?php

// Exit if accessed directly.
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
 * @param int          $post_id    The post id to get related posts for.
 * @param array|string $taxonomies The taxonomies to retrieve related posts from.
 * @param array|string $args       Optional. Change what is returned.
 * @return array                   Empty array if no related posts found. Array with post objects.
 */
function km_rpbt_related_posts_by_taxonomy( $post_id = 0, $taxonomies = 'category', $args = '' ) {
	global $wpdb;

	// Get valid taxonomies.
	$taxonomies = km_rpbt_get_taxonomies( $taxonomies );

	if ( ! absint( $post_id ) || empty( $taxonomies ) ) {
		return array();
	}

	$args  = km_rpbt_sanitize_args( $args );
	$terms = array();

	if ( ! $args['related'] && ! empty( $args['include_terms'] ) ) {
		// Related, use included term ids.
		$terms = $args['include_terms'];
	} else {

		// Related and not related terms.
		$terms = wp_get_object_terms( $post_id, $taxonomies, array( 'fields' => 'ids' ) );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array();
		}

		// Only use included terms from the post terms.
		if ( $args['related'] && ! empty( $args['include_terms'] ) ) {
			$terms = array_values( array_intersect( $args['include_terms'], $terms ) );
		}
	}

	// Exclude terms.
	if ( empty( $args['include_terms'] ) ) {
		$terms    = array_values( array_diff( $terms , $args['exclude_terms'] ) );
	}

	if ( empty( $terms ) ) {
		return array();
	}

	$args['related_terms'] = $terms;

	// Term ids sql.
	if ( count( $terms ) > 1 ) {
		$term_ids_sql = 'tt.term_id IN (' . implode( ', ', $terms ) . ')';
	} else {
		$term_ids_sql = ( isset( $terms[0] ) ) ? 'tt.term_id = ' . $terms[0] : 'tt.term_id = 0';
	}

	// Add current post ID to exclude.
	$args['exclude_posts'][] = $post_id;
	$args['exclude_posts']   = array_unique( $args['exclude_posts'] );

	// Post ids sql.
	$post_ids_sql = "AND $wpdb->posts.ID";
	if ( count( $args['exclude_posts'] ) > 1 ) {
		$post_ids_sql .= ' NOT IN (' . implode( ', ', $args['exclude_posts'] ) . ')';
	} else {
		$post_ids_sql .= " != $post_id";
	}

	// Default to post type post if no post types are found.
	$args['post_types'] = ( ! empty( $args['post_types'] ) ) ? $args['post_types'] : array( 'post' );

	// Where sql (post types and post status).
	if ( count( $args['post_types'] ) > 1 ) {
		$where         = get_posts_by_author_sql( 'post' );
		$post_type_sql = "'" . implode( "', '", $args['post_types'] ) . "'";
		$where_sql     = preg_replace( "/post_type = 'post'/", "post_type IN ($post_type_sql)", $where );
	} else {
		$where_sql = get_posts_by_author_sql( $args['post_types'][0] );
	}

	$order_by_rand = false;

	// Order sql.
	switch ( strtoupper( (string) $args['order'] ) ) {
		case 'ASC':  $order_sql = 'ASC'; break;
		case 'RAND': $order_sql = 'RAND()'; $order_by_rand = true; break;
		default:     $order_sql = 'DESC'; break;
	}

	$allowed_fields = array( 'ids' => 'ID', 'names' => 'post_title', 'slugs' => 'post_name' );

	// Select sql.
	$fields = strtolower( (string) $args['fields'] );
	if ( in_array( $fields, array_keys( $allowed_fields ) ) ) {
		$select_sql = "$wpdb->posts." . $allowed_fields[ $fields ];
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

	if ( ! $order_by_rand ) {
		if ( $args['related'] ) {
			// Related terms count sql.
			$select_sql .= ' , count(distinct tt.term_taxonomy_id) as termcount';
		}
		$order_by_sql = "$wpdb->posts.$orderby";
	}

	// Post thumbnail sql.
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

	$select_sql   = isset( $clauses['select_sql'] )   ? $clauses['select_sql']   : '';
	$join_sql     = isset( $clauses['join_sql'] )     ? $clauses['join_sql']     : '';
	$where_sql    = isset( $clauses['where_sql'] )    ? $clauses['where_sql']    : '';
	$group_by_sql = isset( $clauses['group_by_sql'] ) ? $clauses['group_by_sql'] : '';
	$order_by_sql = isset( $clauses['order_by_sql'] ) ? $clauses['order_by_sql'] : '';
	$limit_sql    = isset( $clauses['limit_sql'] )    ? $clauses['limit_sql']    : '';

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

		if ( ! $order_by_rand && $args['related'] ) {

			/* add (termcount) score and key to results */
			for ( $i = 0; $i < count( $results ); $i++ ) {
				$results[ $i ]->score = array( $results[ $i ]->termcount, $i );
			}

			/* order related posts */
			uasort( $results, 'km_rpbt_related_posts_by_taxonomy_cmp' );

			/* add termcount to args so we can use it later */
			$termcount = wp_list_pluck( array_values( $results ), 'score' );
			foreach ( $termcount as $key => $count ) {
				$termcount[ $key ] = $count[0];
			}
			$args['termcount'] = $termcount;
		}

		$results = array_values( $results );

		if ( in_array( $fields, array_keys( $allowed_fields ) ) ) {
			$results = wp_list_pluck( $results, $allowed_fields[ $fields ] );
		}

		if ( -1 !== (int) $args['posts_per_page'] ) {
			$posts_per_page = absint( $args['posts_per_page'] );
			$posts_per_page = ( $posts_per_page ) ? $posts_per_page : 5;
			$results = array_slice( $results, 0, $posts_per_page );
			if ( isset( $args['termcount'] ) && $args['termcount'] ) {
				$args['termcount'] = array_slice( $args['termcount'], 0, $posts_per_page );
			}
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
 * Returns default arguments.
 *
 * @since 2.1
 *
 * @return array Array with default arguments.
 */
function km_rpbt_get_default_args() {

	return array(
		'post_types' => 'post', 'posts_per_page' => 5, 'order' => 'DESC',
		'fields' => '', 'limit_posts' => -1, 'limit_year' => '',
		'limit_month' => '', 'orderby' => 'post_date',
		'exclude_terms' => '', 'include_terms' => '',  'exclude_posts' => '',
		'post_thumbnail' => false, 'related' => true,
	);
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


/**
 * Returns array with validated post types.
 *
 * @since 2.2
 * @param string|array $post_types Comma separated list or array with post type names.
 * @return array Array with validated post types.
 */
function km_rpbt_get_post_types( $post_types = '' ) {

	// Create array with unique values.
	$post_types = km_rpbt_get_comma_separated_values( $post_types );

	// Sanitize post type names and remove duplicates after sanitation.
	$post_types = array_unique( array_map( 'sanitize_key', (array) $post_types ) );

	return array_values( array_filter( $post_types, 'post_type_exists' ) );
}


/**
 * Returns array with validated taxonomies.
 *
 * @since 2.2
 * @param string|array $taxonomies Taxonomies.
 * @return array        Array with taxonomy names.
 */
function km_rpbt_get_taxonomies( $taxonomies ) {

	$plugin  = km_rpbt_plugin();

	if ( $plugin && ( $taxonomies === $plugin->all_tax ) ) {
		$taxonomies = array_keys( $plugin->taxonomies );
	}

	$taxonomies = km_rpbt_get_comma_separated_values( $taxonomies );

	return array_values( array_filter( $taxonomies, 'taxonomy_exists' ) );
}


/**
 * Validates ids.
 * Checks if ids is a comma separated string or an array with ids.
 *
 * @since 0.2
 * @param string|array $ids Comma separated list or array with ids.
 * @return array Array with postive integers
 */
function km_rpbt_related_posts_by_taxonomy_validate_ids( $ids ) {

	if ( ! is_array( $ids ) ) {
		/* allow positive integers, 0 and commas only */
		$ids = preg_replace( '/[^0-9,]/', '', (string) $ids );
		/* convert string to array */
		$ids = explode( ',', $ids );
	}

	/* convert to integers and remove 0 values */
	$ids = array_filter( array_map( 'intval', (array) $ids ) );

	return array_values( array_unique( $ids ) );
}


/**
 * Sanitizes comma separetad values.
 * Returns an array.
 *
 * @since 2.2
 * @param string|array $value Comma seperated value or array with values.
 * @return array       Array with unique array values
 */
function km_rpbt_get_comma_separated_values( $value ) {

	if ( ! is_array( $value ) ) {
		$value = explode( ',', (string) $value );
	}

	return array_values( array_filter( array_unique( array_map( 'trim', $value ) ) ) );
}


/**
 * Returns sanitized arguments.
 *
 * @since 2.1
 * @param array $args Arguments to be sanitized.
 * @return array Array with sanitized arguments.
 */
function km_rpbt_sanitize_args( $args ) {

	$defaults = km_rpbt_get_default_args();
	$args     = wp_parse_args( $args, $defaults );

	// Arrays with strings.
	if ( isset( $args['taxonomies'] ) ) {
		$args['taxonomies'] = km_rpbt_get_taxonomies( $args['taxonomies'] );
	}

	$post_types         = km_rpbt_get_post_types( $args['post_types'] );
	$args['post_types'] = ! empty( $post_types ) ? $post_types : array( 'post' );

	// Arrays with integers.
	$ids = array( 'exclude_terms', 'include_terms', 'exclude_posts' );
	foreach ( $ids as $id ) {
		$args[ $id ] = km_rpbt_related_posts_by_taxonomy_validate_ids( $args[ $id ] );
	}

	// Strings.
	$args['fields']  = is_string( $args['fields'] ) ? $args['fields'] : '';
	$args['orderby'] = is_string( $args['orderby'] ) ? $args['orderby'] : '';
	$args['order']   = is_string( $args['order'] ) ? $args['order'] : '';

	// Integers.
	$args['limit_year']     = absint( $args['limit_year'] );
	$args['limit_month']    = absint( $args['limit_month'] );
	$args['limit_posts']    = (int) $args['limit_posts'];
	$args['posts_per_page'] = (int) $args['posts_per_page'];

	if ( isset( $args['post_id'] ) ) {
		$args['post_id'] = absint( $args['post_id'] );
	}

	// Booleans
	// True for true, 1, "1", "true", "on", "yes". Everything else return false.
	$args['related']        = (bool) filter_var( $args['related'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	$args['post_thumbnail'] = (bool) filter_var( $args['post_thumbnail'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

	return $args;
}


/**
 * Public function to cache related posts.
 * Uses the same arguments as the km_rpbt_related_posts_by_taxonomy() function.
 *
 * @since 2.1
 * @param int          $post_id    The post id to cache related posts for.
 * @param array|string $taxonomies The taxonomies to cache related posts from.
 * @param array|string $args       Optional. Cache arguments.
 * @return array Array with cached related posts objects or false if no posts where cached.
 */
function km_rpbt_cache_related_posts( $post_id = 0, $taxonomies = 'category', $args = '' ) {

	$plugin = km_rpbt_plugin();

	// Check if the Cache class is instantiated.
	if ( $plugin && ! ( $plugin->cache instanceof Related_Posts_By_Taxonomy_Cache ) ) {
		return false;
	}

	// Add post id and taxonomies to arguments.
	$args['post_id']    = $post_id;
	$args['taxonomies'] = $taxonomies;

	// Cache related posts if not in cache.
	return $plugin->cache->get_related_posts( $args );
}


/**
 * Public function to flush the persistent cache.
 *
 * @since 2.1
 * @return int|bool Returns number of deleted rows or false on failure.
 */
function km_rpbt_flush_cache() {

	$plugin = km_rpbt_plugin();

	// Check if the cache class is instantiated.
	if ( $plugin && ( $plugin->cache instanceof Related_Posts_By_Taxonomy_Cache ) ) {
		return $plugin->cache->flush_cache();
	}

	return false;
}

/**
 * Returns the default settings.
 *
 * @since 2.2.2
 * @param unknown $type Type of settings. Accepts 'shortcode', widget, 'all'.
 * @return array|false Array with default settings by type 'shortcode', 'widget' or 'all'.
 */
function km_rpbt_get_default_settings( $type = '' ) {
	$plugin = km_rpbt_plugin();

	if ( ! $plugin ) {
		return false;
	}

	return $plugin->get_default_settings( $type );
}


/**
 * Returns plugin defaults instance or false.
 *
 * @since 2.1
 * @return Object|false Related_Posts_By_Taxonomy_Defaults instance or false.
 */
function km_rpbt_plugin() {
	if ( class_exists( 'Related_Posts_By_Taxonomy_Defaults' ) ) {
		return Related_Posts_By_Taxonomy_Defaults::get_instance();
	}

	return false;
}
