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
 * See the {@see 'related_posts_by_taxonomy_supports'} filter which
 * features are supported by default and which are opt-in
 *
 * @since  2.5.0
 *
 * @param string $type Type of feature.
 * @return bool True if the feature is supported.
 */
function km_rpbt_plugin_supports( $feature ) {
	$supports = km_rpbt_get_plugin_supports();

	if ( ! in_array( $feature, array_keys( $supports ) ) ) {
		return false;
	}

	/**
	 * Filter whether to support a plugin feature.
	 *
	 * The dynamic portion of the hook name, `$feature`, refers to the
	 * type of support.
	 *
	 * - widget
	 * - widget_hide_empty
	 * - shortcode
	 * - shortcode_hide_empty
	 * - cache
	 * - display_cache_log
	 * - wp_rest_api
	 * - id_query
	 * - lazy_loading
	 * - debug
	 *
	 * @since 2.5.0
	 *
	 * @param bool $bool Add support if true. Default false
	 */
	return apply_filters( "related_posts_by_taxonomy_{$feature}", (bool) $supports[ $feature ] );
}

/**
 * Get related posts from the database or cache.
 *
 * Used by the widget, shortcode and rest api.
 *
 * If the cache is activated it tries to get the related posts from the cache first.
 * If not found in the cache they will be cached before returning related posts
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
 *     @type string         $post_class       Class for the related post items. Default empty.
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
 * @return array Array with related post objects.
 */
function km_rpbt_get_related_posts( $post_id, $args = array() ) {
	$plugin  = km_rpbt_plugin();
	$post_id = absint( $post_id );

	if ( ! $post_id ) {
		return array();
	}
	// Sanitize arguments.
	$args = km_rpbt_sanitize_args( $args );

	// Check if any taxonomies are used for the query.
	$taxonomies = isset( $args['taxonomies'] ) ? $args['taxonomies'] : '';
	if ( ! $taxonomies ) {
		$args['taxonomies'] = km_rpbt_get_public_taxonomies();
	}

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
 * Related posts feature HTML.
 *
 * @since  2.6.0
 *
 * @param string $feature Type of feature.
 * @param array  $args    See km_rpbt_related_posts_by_taxonomy_shortcode() for for more
 *                                    information on accepted arguments.
 * @return string feature html or empty string.
 */
function km_rpbt_get_feature_html( $feature, $args = array() ) {
	$feature_support = km_rpbt_plugin_supports( $feature );
	$feature_type    = km_rpbt_is_valid_settings_type( $feature );
	$args['type']    = $feature;
	$html            = '';

	if ( ! ( $feature_type && $feature_support ) ) {
		return '';
	}

	$defaults = km_rpbt_get_default_settings( $feature );
	$args     = array_merge( $defaults, $args );

	// Get allowed fields for use in templates
	$args['fields'] = km_rpbt_get_template_fields( $args );

	if ( km_rpbt_plugin_supports( 'lazy_loading' ) ) {
		return km_rpbt_get_lazy_loading_html( $args );
	}

	// Get the related posts from database or cache.
	$related_posts = km_rpbt_get_related_posts( $args['post_id'], $args );
	$hide_empty    = km_rpbt_plugin_supports( "{$feature}_hide_empty" );

	if ( ! $hide_empty || ! empty( $related_posts ) ) {
		$html = km_rpbt_get_related_posts_html( $related_posts, $args );
	}

	/**
	 * Fires after the related posts are displayed.
	 *
	 * @since  1.0.0
	 *
	 * @param string Display type, widget or shortcode.
	 */
	do_action( 'related_posts_by_taxonomy_after_display', $feature );

	return $html;
}

/**
 * Get the related posts HTML.
 *
 * @since 2.6.0
 *
 * @param array $related_posts Array with related post objects.
 * @param array $args          See km_rpbt_related_posts_by_taxonomy_shortcode() for for more
 *                             information on accepted arguments.
 * @return string Related posts HTML
 */
function km_rpbt_get_related_posts_html( $related_posts, $rpbt_args ) {
	static $recursing = false;

	/* Check for filter recursion (infinite loop) */
	if ( ! $recursing ) {
		$recursing = true;
	} else {
		return '';
	}

	$rpbt_args['type'] = km_rpbt_get_settings_type( $rpbt_args );
	$rpbt_args = array_merge( km_rpbt_get_default_settings( $rpbt_args['type'] ), $rpbt_args );

	/* get the template depending on the format  */
	$template = km_rpbt_get_template( $rpbt_args['format'], $rpbt_args['type'] );
	if ( ! $template ) {
		$recursing = false;
		return '';
	}

	if ( $rpbt_args['title'] ) {
		$rpbt_args['title'] = $rpbt_args['before_title'] . $rpbt_args['title'] . $rpbt_args['after_title'];
	}

	global $post; // Used for setup_postdata() in templates.

	/* public template variables (back-compat) */
	$image_size = $rpbt_args['image_size']; // deprecated in version 0.3.
	$columns    = absint( $rpbt_args['columns'] ); // deprecated in version 0.3.

	ob_start();
	require $template;
	$output = ob_get_clean();
	$output = trim( $output );
	wp_reset_postdata(); // Clean up global $post variable.

	$before = "before_{$rpbt_args['type']}";
	$after  = "after_{$rpbt_args['type']}";

	$html = '';
	if ( $output ) {
		$html =  isset( $rpbt_args[ $before ] ) ? $rpbt_args[ $before ]  . "\n" : '';
		$html .= trim( $rpbt_args['title'] ) . "\n";
		$html .= $output . "\n";
		$html .=  isset( $rpbt_args[ $after ] ) ? $rpbt_args[ $after ]  . "\n" : '';
	}

	$recursing = false;
	return trim( $html );
}

/**
 * Get the HTML for the lazy loading feature.
 *
 * Returns a HTML div with the (widget or shortcode ) arguments added to
 * the `data-rpbt_args` HTML attribute.
 *
 * The data attribute is used by Javascript to query
 * related posts with Ajax after the page is loaded.
 *
 * The HTML can be filtered with the {@see 'related_posts_by_taxonomy_lazy_loading_html'} filter.
 *
 * @since 2.6.0
 * @param array $args See km_rpbt_related_posts_by_taxonomy_shortcode() arguments.
 * @return string Related posts HTML div with data arguments.
 */
function km_rpbt_get_lazy_loading_html( $args ) {
	$type     = km_rpbt_get_settings_type( $args );
	$defaults = km_rpbt_get_default_settings( $type );
	$args     = array_merge( $defaults, $args );

	/**
	 * Filter placeholder HTML while loading posts with the lazy loading feature.
	 *
	 * @since  2.6.0
	 *
	 * @param string $content HTML that will be displayed while loading posts. Default empty string.
	 * @param array  $args    See km_rpbt_related_posts_by_taxonomy_shortcode() arguments.
	 */
	$html = apply_filters( 'related_posts_by_taxonomy_lazy_loading_html', '', $args );
	$html = is_string( $html ) ? $html : '';

	// Remove default values to keep the HTML data attribute small.
	foreach ( $defaults as $key => $value ) {
		if ( array_key_exists( $key, $args ) && ( $value === $args[ $key ] ) ) {
			unset( $args[ $key ] );
		}
	}

	// Add type back
	$args['type'] = $type;
	$data         = htmlspecialchars( json_encode( $args ), ENT_QUOTES, 'UTF-8' );
	$data_html    = "<div class='rpbt-related-posts-lazy-loading' data-rpbt_args='{$data}'>\n";

	return $data_html . $html . "</div>\n";
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
 * Get the values from a comma separated string.
 *
 * Removes duplicates and empty values.
 *
 * @since 2.2
 * @param string|array $value Comma seperated string or array with values.
 * @return array       Array with unique array values
 */
function km_rpbt_get_comma_separated_values( $value, $filter = 'string' ) {
	if ( ! is_array( $value ) ) {
		$value = explode( ',', (string) $value );
	}

	return array_values( array_filter( array_unique( array_map( 'trim', $value ) ) ) );
}

/**
 * Checks if the cache class is loaded.
 *
 * @since  2.5.0
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
 * The opt-in cache feature needs to be activated (with a filter) to cache posts.
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
