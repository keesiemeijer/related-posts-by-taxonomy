<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Returns array with validated taxonomy names.
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
 * Get all public taxonomies.
 *
 * @since 2.5.0
 *
 * @return array Array with all public taxonomies.
 */
function km_rpbt_get_public_taxonomies() {
	$plugin = km_rpbt_plugin();
	return isset( $plugin->all_tax ) ? km_rpbt_get_taxonomies( $plugin->all_tax ) : array();
}

/**
 * Get the terms from a post or from the arguments.
 *
 * @since  2.5.0
 *
 * @param int          $post_id    The post id to get terms for.
 * @param array|string $taxonomies The taxonomies to retrieve terms from.
 * @param string|array $args       {
 *     Optional. Arguments to get terms.
 *
 *     @type array|string   $terms            Array or comma separated list of term ids. if `$related is set to true` it only
 *                                            returns terms in the `$taxonomies` argument. Default empty.
 *     @type array|string   $include_terms    Array or comma separated list of term ids. If `$related is set to true it only
 *                                            includes terms also assigned to the `$post_id` argument. Default empty.
 *     @type boolean        $include_parents  Whether to include parent terms. Default false.
 *     @type boolean        $include_children Whether to include child terms. Default false.
 *     @type array|string   $exclude_terms    Array or comma separated list of term ids to exclude. Default empty
 *     @type boolean        $related          If false the `terms` and `$include_terms` terms are returned without
 *                                            checking taxonomies or post terms. Default true.
 * }
 * @return array Array with term ids.
 */
function km_rpbt_get_terms( $post_id, $taxonomies, $args = array() ) {
	$terms      = array();
	$post_id    = absint( $post_id );
	$taxonomies = km_rpbt_get_taxonomies( $taxonomies );
	$args       = km_rpbt_sanitize_args( $args );

	if ( $args['related'] && empty( $taxonomies ) ) {
		// Taxonomies are needed for related terms.
		return array();
	}

	if ( ! $args['related'] && ( $args['terms'] || $args['include_terms'] ) ) {
		// Unrelated terms
		$terms = $args['terms'] ? $args['terms'] : $args['include_terms'];
	} elseif ( $args['terms'] ) {
		// Filters out terms not in taxonomies.
		$terms = km_rpbt_get_term_objects(  $args['terms'], $taxonomies );
		$terms = ! empty( $terms ) ? wp_list_pluck( $terms, 'term_id' ) : array();
	} else {

		/**
		 * The post ID and taxonomies are validated above (set to empty value if invalid).
		 * wp_get_object_terms() returns an empty array if one argument is empty.
		 */
		$terms = wp_get_object_terms( $post_id, $taxonomies, array( 'fields' => 'ids', ) );
		$terms = ! is_wp_error( $terms ) ? $terms : array();

		// Only include terms from the post terms.
		if ( $args['related'] && $args['include_terms'] ) {
			$terms = array_intersect( $args['include_terms'], $terms );
		}
	}

	$parent_terms = array();
	if ( ! empty( $terms ) && $args['include_parents'] ) {
		$parent_terms = km_rpbt_get_hierarchy_terms( 'parents', $terms, $taxonomies );
	}

	$child_terms = array();
	if ( ! empty( $terms ) && $args['include_children'] ) {
		$child_terms = km_rpbt_get_hierarchy_terms( 'children', $terms, $taxonomies );
	}

	$terms = array_merge( $terms, $parent_terms );
	$terms = array_unique( array_merge( $terms, $child_terms ) );

	// Exclude terms.
	if ( ! empty( $terms ) && ! empty( $args['exclude_terms'] ) ) {
		$terms = array_diff( $terms , $args['exclude_terms'] );
	}

	return array_values( $terms );
}

/**
 * Get all parent or child terms.
 *
 * If the taxonomies argument is empty it returns parents for all terms.
 * If taxonomies are provided it only returns parents from terms in the taxonomies.
 *
 * @since  2.6.1
 *
 * @param string       $tree_type  Type of hierarchy tree. Accepts 'parents' or 'children'.
 * @param array|string $terms      Array or comma separated list of term ids.
 * @param array|string $taxonomies Array or comma separated list of taxonomy names. Default empty.
 * @return array Array with term ids
 */
function km_rpbt_get_hierarchy_terms( $tree_type, $terms, $taxonomies = '' ) {
	if ( ! $terms ||  ! in_array( $tree_type, array( 'parents', 'children' ) ) ) {
		return array();
	}

	$taxonomies = km_rpbt_get_taxonomies( $taxonomies );
	$terms      = km_rpbt_get_term_objects( $terms, $taxonomies );

	$tree_terms = array();
	foreach ( $terms as $term ) {
		if ( ! is_taxonomy_hierarchical( $term->taxonomy ) ) {
			continue;
		}

		if ( 'parents' === $tree_type ) {
			$tree = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );
		} else {
			$tree = get_term_children( $term->term_id, $term->taxonomy );
			$tree = ! is_wp_error( $tree ) ? $tree : array();
		}

		/**
		 * Filter parent or child terms.
		 *
		 * @since  2.6.1
		 *
		 * @param array $tree       Parent or child term ids.
		 * @param int   $term_id    Term id.
		 * @param array $taxonomies Term taxonomy.
		 * @param array $tree_type  Hierarchy tree type 'parents' or 'children'.
		 */
		$tree = apply_filters( 'related_posts_by_taxonomy_get_terms_hierarchy', $tree, $tree_type, $term->term_id, $term->taxonomy );

		if ( $tree && is_array( $tree ) ) {
			$tree_terms = array_merge( $tree_terms, $tree );
		}
	}

	return km_rpbt_validate_ids( $tree_terms );
}

/**
 * Get term objects from term ids.
 *
 * Only the `term_id` and `taxonomy` properties are included in the term objects.
 *
 * If the taxonomies argument is empty it returns all terms.
 * If taxonomies are provided it only returns terms from the taxonomies.
 *
 * @since 2.6.1
 *
 * @param array|string $terms      Array or comma separated list of term ids.
 * @param array|string $taxonomies Array or comma separated list of taxonomy names. Default empty.
 * @return array Array with term objects.
 */
function km_rpbt_get_term_objects( $terms, $taxonomies = '' ) {
	global $wpdb;

	$terms      = km_rpbt_validate_ids( $terms );
	$taxonomies = km_rpbt_get_taxonomies( $taxonomies );

	if ( ! $terms ) {
		return array();
	}

	$tax_sql    = '';
	if ( ! empty( $taxonomies ) ) {
		$taxonomies = array_map( 'esc_sql', $taxonomies );
		$taxonomies = implode( "', '", $taxonomies );
		$tax_sql    = "tt.taxonomy IN ('{$taxonomies}')";
	}

	$terms_sql  = implode( ', ', $terms );
	$select_sql = "SELECT t.term_id, tt.taxonomy FROM {$wpdb->terms} AS t";
	$join_sql   = "INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id";
	$where_sql  = $tax_sql ? "WHERE {$tax_sql} AND " : 'WHERE ';
	$where_sql .= "t.term_id IN ({$terms_sql})";

	$query = "{$select_sql} {$join_sql} {$where_sql}";

	$key          = md5( $query );
	$last_changed = wp_cache_get_last_changed( 'km_rpbt_terms' );
	$cache_key    = "km_rpbt_get_term_objects:$key:$last_changed";
	$cache        = wp_cache_get( $cache_key, 'km_rpbt_terms' );

	if ( false !== $cache ) {
		$results = $cache;
	} else {
		$results = $wpdb->get_results( $query );
		wp_cache_set( $cache_key, $results, 'km_rpbt_terms' );
	}

	return is_array( $results ) ? $results : array();
}
