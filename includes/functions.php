<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
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

/**
 * Check if the plugin supports a feature.
 *
 * @since  2.5.0
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
	 * Filter whether to support a plugin feature.
	 *
	 * The dynamic portion of the hook name, `$type`, refers to the
	 * type of support.
	 *
	 * - widget
	 * - shortcode
	 * - shortcode_hide_empty
	 * - widget_hide_empty
	 * - cache
	 * - display_cache_log
	 * - wp_rest_api
	 * - debug
	 *
	 * @param bool $bool Add support if true. Default false
	 */
	return apply_filters( "related_posts_by_taxonomy_{$type}", (bool) $supports[ $type ] );
}

/**
 * Get related posts from the database or cache.
 *
 * Used by the widget, shortcode, and rest api.
 *
 * If the cache feature of this plugin is activated it tries to get the
 * related posts from the cache first. If not found in the cache they will be
 * cached before returning related posts
 *
 * If taxonomies are not set in the arguments it queries for
 * related posts in all public taxonomies.
 *
 * @since  2.5.0
 *
 * @param array        $post_id The post id to get related posts for.
 * @param string|array $args    {
 *     Optional. Arguments to get related posts.
 *
 *     @type string|array   $taxonomies       Taxonomies to use for related posts query. Array or comma separated
 *                                            list of taxonomy names. Default empty (all taxonomies).
 *     @type string|array   $post_types       Post types to use for related posts query. Array or comma separated
 *                                            list of post type names. Default 'post'.
 *     @type int            $posts_per_page   Number of related posts. Default 5.
 *     @type string         $order            Order of related posts. Accepts 'DESC', 'ASC' and 'RAND'. Default 'DESC'.
 *     @type string         $orderby          Order by post date or by date modified.
 *                                            Accepts 'post_date'and 'post_modified'. Default 'post_date'.
 *     @type string         $fields           Return full post objects, IDs, post titles or post slugs.
 *                                            Accepts 'all', 'ids', 'names' or 'slugs'. Default is 'all'.
 *     @type array|string   $terms            Terms to use for the related posts query. Array or comma separated
 *                                            list of term ids. The terms don't need to be assigned to the post to
 *                                            get related posts for. Default empty.
 *     @type array|string   $include_terms    Terms to include for the related posts query. Array or comma separated
 *                                            list of term ids. Only includes terms also assigned to the post to get
 *                                            related posts for. Default empty.
 *     @type array|string   $exclude_terms    Terms to exlude for the related posts query. Array or comma separated
 *                                            list of term ids. Default empty
 *     @type boolean        $related          If false the `$include_terms` argument also includes terms not assigned to
 *                                            the post to get related posts for. Default true.
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
 * }
 * @return array Array with related post objects.
 */
function km_rpbt_get_related_posts( $post_id, $args = array() ) {
	$plugin  = km_rpbt_plugin();
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return array();
	}

	// Check if any taxonomies are used for the query.
	$taxonomies = isset( $args['taxonomies'] ) ? $args['taxonomies'] : '';
	if ( ! $taxonomies ) {
		$args['taxonomies'] = km_rpbt_get_public_taxonomies();
	}

	// Sanitize arguments.
	$args = km_rpbt_sanitize_args( $args );

	// Set post_id the same as used for the $post_id parameter.
	$args['post_id'] = $post_id;

	/**
	 * Filter whether to use your own related posts.
	 *
	 * @since  2.5.0
	 *
	 * @param boolean|array $related_posts Return an array with (related) post objects to use your own
	 *                                     related post. This prevents the query for related posts by this plugin.
	 *                                     Default false (Let this plugin query for related posts).
	 *
	 * @param array         Array with widget or shortcode arguments.
	 */
	$related_posts = apply_filters( 'related_posts_by_taxonomy_pre_related_posts', false, $args );

	if ( is_array( $related_posts ) ) {
		return $related_posts;
	}

	if ( km_rpbt_plugin_supports( 'cache' ) && km_rpbt_is_cache_loaded() ) {
		// Get related posts from cache.
		$related_posts = $plugin->cache->get_related_posts( $args );
	} else {
		$query_args = $args;

		/* restricted arguments */
		unset( $query_args['post_id'], $query_args['taxonomies'] );

		/* get related posts */
		$related_posts = km_rpbt_query_related_posts( $args['post_id'], $args['taxonomies'], $query_args );
	}

	$related_posts = km_rpbt_add_post_classes( $related_posts, $args );

	return $related_posts;
}

/**
 * Get the terms from a post or from included terms.
 *
 * @since  2.5.0
 *
 * @param int          $post_id    The post id to get terms for.
 * @param array|string $taxonomies The taxonomies to retrieve terms from.
 * @param string|array $args       {
 *     Optional. Arguments to get terms.
 *
 *     @type array|string   $terms            Terms to use for the related posts query. Array or comma separated
 *                                            list of term ids. The terms don't need to be assigned to the post set by the
 *                                            `$post_id` argument. Default empty.
 *     @type array|string   $include_terms    Terms to include for the related posts query. Array or comma separated
 *                                            list of term ids. Only includes terms also assigned to the post set by the
 *                                            `$post_id` argument. Default empty.
 *     @type array|string   $exclude_terms    Terms to exlude for the related posts query. Array or comma separated
 *                                            list of term ids. Default empty
 *     @type boolean        $related          If false the `$include_terms` argument also includes terms not assigned to
 *                                            the post set by the `$post_id` argument. Default true.
 * }
 * @return array Array with term ids.
 */
function km_rpbt_get_terms( $post_id, $taxonomies, $args = array() ) {
	$terms = array();
	$args  = km_rpbt_sanitize_args( $args );

	if ( $args['terms'] ) {

		if ( ! $args['related'] ) {
			return $args['terms'];
		}

		$term_args = array(
			'include'  => $args['terms'],
			'taxonomy' => $taxonomies,
			'fields'   => 'ids',
		);

		// Filter out terms not assigned to the taxonomies
		$terms = get_terms( $term_args );

		return ! is_wp_error( $terms ) ? $terms : array();
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
 * Returns array with validated post type names.
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
 * Checks if the cache class is loaded
 *
 * @param object $plugin Related_Posts_By_Taxonomy_Cache object. Default null.
 * @return bool True if the cache class is loaded.
 */
function km_rpbt_is_cache_loaded() {
	$plugin = km_rpbt_plugin();
	return isset( $plugin->cache ) && $plugin->cache instanceof Related_Posts_By_Taxonomy_Cache;
}

/**
 * Public function to cache related posts.
 *
 * The opt-in cache feature needs to be activated to cache posts.
 *
 * @since 2.1
 * @since  2.5.0 Use empty string as default value for $taxonomies parameter.
 *
 * @param int          $post_id    The post id to cache related posts for.
 * @param array|string $taxonomies Taxonomies for the related posts query.
 * @param array|string $args       Optional arguments. See km_rpbt_query_related_posts() for more
 *                                 information on accepted arguments.
 * @return array|false Array with cached related posts objects or false if no posts where cached.
 */
function km_rpbt_cache_related_posts( $post_id, $taxonomies = '', $args = array() ) {
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
 * Call this function on the wp_load hook or later.
 *
 * @since 2.1
 * @return int|bool Returns number of deleted rows or false on failure.
 */
function km_rpbt_flush_cache() {

	$plugin = km_rpbt_plugin();

	// Check if the cache class is loaded and instantiated.
	if ( km_rpbt_is_cache_loaded() ) {
		return $plugin->cache->flush_cache();
	}

	return false;
}
