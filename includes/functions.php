<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

include_once 'query.php';

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

/**
 * Check if the plugin supports a feature.
 *
 * @since  2.4.2
 *
 * @param string $type Type of feature.
 * @return bool True if the feature is supported.
 */
function km_rpbt_plugin_supports( $type ) {
	$supports = km_rpbt_get_plugin_supports();

	if ( ! in_array( $type , array_keys( $supports ) ) ) {
		return false;
	}

	/**
	 * Filter whether to support cache, wp_rest_api or debug.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the
	 * type type of support ('cache', 'wp_rest_api', 'etc).
	 *
	 * @param bool $bool Add support if true. Default false
	 */
	return apply_filters( "related_posts_by_taxonomy_{$type}", (bool) $supports[ $type ] );
}

/**
 * Get related posts from database or cache.
 *
 * @since  2.4.2
 *
 * @param array        $post_id The post id to get related posts for.
 * @param array|string $args    Array of arguments.
 * @return array Array with related post objects.
 */
function km_rpbt_get_related_posts( $post_id, $args = array() ) {
	$plugin = km_rpbt_plugin();

	// Check if taxonomies are set.
	if ( ! ( isset( $args['taxonomies'] ) && $args['taxonomies'] ) ) {

		// Default to all taxonomies.
		$args['taxonomies'] = km_rpbt_get_all_taxonomies();
	}

	// Returns an array with sanitized arguments.
	$args = km_rpbt_sanitize_args( $args );

	// Set post_id the same as used for the $post_id parameter
	$args['post_id'] = absint( $post_id );

	$query_args = $args;

	/**
	 * Filter whether to use your own related posts.
	 *
	 * @since  2.4.2
	 *
	 * @param boolean|array $related_posts Array with (related) post objects.
	 *                                     Default false (Don't use your own related posts).
	 *                                     Use empty array to not retrieve related posts from the database.
	 *
	 * @param array         Array with widget or shortcode arguments.
	 */
	$related_posts = apply_filters( 'related_posts_by_taxonomy_pre_related_posts', false, $args );

	if ( is_array( $related_posts ) ) {
		return $related_posts;
	}

	if ( km_rpbt_plugin_supports( 'cache' ) && km_rpbt_is_cache_loaded( $plugin ) ) {
		// Get related posts from cache.
		$related_posts = $plugin->cache->get_related_posts( $args );
	} else {
		/* restricted arguments */
		unset( $query_args['post_id'], $query_args['taxonomies'] );

		/* get related posts */
		$related_posts = km_rpbt_query_related_posts( $args['post_id'], $args['taxonomies'], $query_args );
	}

	$related_posts = km_rpbt_add_post_classes( $related_posts, $args );

	return $related_posts;
}

/**
 * Get the terms from the post or from included terms
 *
 * @since  2.4.2
 *
 * @param int          $post_id    The post id to get terms for.
 * @param array|string $taxonomies The taxonomies to retrieve terms from.
 * @param array|string $args       Optional. Change what is returned.
 * @return array Array with term ids.
 */
function km_rpbt_get_terms( $post_id, $taxonomies, $args = array() ) {
	$terms = array();
	$args   = km_rpbt_sanitize_args( $args );
	if ( $args['terms'] ) {
		// Return sanitized terms.
		return $args['terms'];
	}

	if ( ! $args['related'] && ! empty( $args['include_terms'] ) ) {
		// Not related, use included term ids as is.
		$terms = $args['include_terms'];
	} else {

		// Post terms.
		$terms = wp_get_object_terms(
			$post_id, $taxonomies, array(
				'fields' => 'ids',
			)
		);

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
		$terms = array_values( array_diff( $terms , $args['exclude_terms'] ) );
	}

	return $terms;
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
 * Get all public taxonomies found by this plugin.
 *
 * @since 2.4.2
 *
 * @return array Array with all public taxonomies.
 */
function km_rpbt_get_all_taxonomies() {
	$plugin = km_rpbt_plugin();
	return isset( $plugin->all_tax ) ? km_rpbt_get_taxonomies( $plugin->all_tax ) : array();
}

/**
 * Validates ids.
 * Checks if ids is a comma separated string or an array with ids.
 *
 * @since 2.4.2
 * @param string|array $ids Comma separated list or array with ids.
 * @return array Array with postive integers
 */
function km_rpbt_validate_ids( $ids ) {

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

	$defaults = km_rpbt_get_query_vars();
	$args     = wp_parse_args( $args, $defaults );

	// Arrays with strings.
	if ( isset( $args['taxonomies'] ) ) {
		$args['taxonomies'] = km_rpbt_get_taxonomies( $args['taxonomies'] );
	}

	$post_types         = km_rpbt_get_post_types( $args['post_types'] );
	$args['post_types'] = ! empty( $post_types ) ? $post_types : array( 'post' );

	// Arrays with integers.
	$ids = array( 'exclude_terms', 'include_terms', 'exclude_posts', 'terms' );
	foreach ( $ids as $id ) {
		$args[ $id ] = km_rpbt_validate_ids( $args[ $id ] );
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
	$args['public_only']    = (bool) filter_var( $args['public_only'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

	if ( 'regular_order' !== $args['include_self'] ) {
		$args['include_self'] = (bool) filter_var( $args['include_self'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	}

	return $args;
}

/**
 * Checks if the cache class is loaded
 *
 * @param object $plugin Related_Posts_By_Taxonomy_Cache object. Default null.
 * @return bool True if the cache class is loaded.
 */
function km_rpbt_is_cache_loaded( $plugin = null ) {
	if ( ! $plugin ) {
		$plugin = km_rpbt_plugin();
	}

	return isset( $plugin->cache ) && $plugin->cache instanceof Related_Posts_By_Taxonomy_Cache;
}

/**
 * Public function to cache related posts.
 * Uses the same arguments as the km_rpbt_query_related_posts() function.
 *
 * @since 2.1
 * @param int          $post_id    The post id to cache related posts for.
 * @param array|string $taxonomies The taxonomies to cache related posts from.
 * @param array|string $args       Optional. Cache arguments.
 * @return array Array with cached related posts objects or false if no posts where cached.
 */
function km_rpbt_cache_related_posts( $post_id = 0, $taxonomies = 'category', $args = array() ) {
	// Check if cache is loaded.
	if ( ! ( km_rpbt_plugin_supports( 'cache' ) && km_rpbt_is_cache_loaded() ) ) {
		return false;
	}

	// Add post id and taxonomies to arguments.
	$args['post_id']    = $post_id;
	$args['taxonomies'] = $taxonomies;

	// Caches related posts if not in cache.
	return km_rpbt_get_related_posts( $post_id, $args );
}

/**
 * Public function to flush the persistent cache.
 *
 * Note: This function doesn't check if the plugin supports the cache.
 *
 * @since 2.1
 * @return int|bool Returns number of deleted rows or false on failure.
 */
function km_rpbt_flush_cache() {

	$plugin = km_rpbt_plugin();

	// Check if the cache class is loaded and instantiated.
	if ( km_rpbt_is_cache_loaded( $plugin ) ) {
		return $plugin->cache->flush_cache();
	}

	return false;
}
