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
 * Check if the plugin supports a feature.
 *
 * @since  2.4.2
 *
 * @param string $support Feature
 * @return bool True if the feature is supported.
 */
function km_rpbt_plugin_supports( $support ) {
	$plugin = km_rpbt_plugin();
	return $plugin && $plugin->plugin_supports( $support );
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
		'post_types'     => 'post',
		'posts_per_page' => 5,
		'order'          => 'DESC',
		'fields'         => '',
		'limit_posts'    => -1,
		'limit_year'     => '',
		'limit_month'    => '',
		'orderby'        => 'post_date',
		'terms'          => '',
		'exclude_terms'  => '',
		'include_terms'  => '',
		'exclude_posts'  => '',
		'post_thumbnail' => false,
		'related'        => true,
		'public_only'    => false,
		'include_self'   => false,
	);
}

/**
 * Get related posts from database or cache.
 *
 * @since  2.4.2
 *
 * @param array $post_id The post id to get related posts for.
 * @param array|string $args Array of arguments.
 * @return array Array with related post objects.
 */
function km_rpbt_get_related_posts( $post_id, $args = array() ) {
	// Returns an array with arguments.
	$args = km_rpbt_sanitize_args( $args );

	$plugin = km_rpbt_plugin();
	if ( ! isset( $args['taxonomies'] ) ) {
		// Default to all taxonomies or catecories.
		$all_tax            = isset( $plugin->all_tax ) ? $plugin->all_tax : 'category';
		$args['taxonomies'] = km_rpbt_get_taxonomies( $all_tax );
	}

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

	/* restricted arguments */
	unset( $query_args['post_id'], $query_args['taxonomies'] );

	$cache = isset( $plugin->cache ) && $plugin->cache instanceof Related_Posts_By_Taxonomy_Cache;
	if ( $cache && ( isset( $args['cache'] ) && $args['cache'] ) ) {
		$related_posts = $plugin->cache->get_related_posts( $args );
	} else {
		/* get related posts */
		$related_posts = km_rpbt_related_posts_by_taxonomy( $post_id, $args['taxonomies'], $query_args );
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
	$ids = array( 'exclude_terms', 'include_terms', 'exclude_posts', 'terms' );
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
	$args['public_only']    = (bool) filter_var( $args['public_only'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

	if ( 'regular_order' !== $args['include_self'] ) {
		$args['include_self'] = (bool) filter_var( $args['include_self'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	}

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
