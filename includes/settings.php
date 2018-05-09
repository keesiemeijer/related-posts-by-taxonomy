<?php
/**
 * Setting types supported by this plugin.
 *
 * @since  2.4.2
 *
 * @return array Array with supported setting types.
 */
function km_rpbt_get_setting_types() {
	return array(
		'shortcode',
		'widget',
		'wp_rest_api',
		'cache',
	);
}

/**
 * Get the features this plugin supports
 *
 * @since  2.3.1
 *
 * @return Array Array with plugin support types
 */
function km_rpbt_get_plugin_supports() {
	$supports = array(
		'widget'               => true,
		'shortcode'            => true,
		'shortcode_hide_empty' => true,
		'widget_hide_empty'    => true,
		'cache'                => false,
		'display_cache_log'    => false,
		'wp_rest_api'          => false,
		'debug'                => false,
	);

	/**
	 * Filter plugin features.
	 *
	 * @since 2.3.1
	 *
	 * @param array $support Array with all supported plugin features.
	 */
	$plugin = apply_filters( 'related_posts_by_taxonomy_supports', $supports );

	return array_merge( $supports, (array) $plugin );
}

/**
 * Returns defaults for the related posts query.
 *
 * @since 2.4.2
 *
 * @return array Array with default query vars.
 */
function km_rpbt_get_query_vars() {
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
 * Returns default settings by type.
 *
 * @since 2.2.2
 * @param string $type Type of settings. Accepts 'shortcode', widget, 'wp_rest_api', 'cache'.
 * @return array|false Array with default settings by type 'shortcode', 'widget' or .
 */
function km_rpbt_get_default_settings( $type = '' ) {
	$setting_types = km_rpbt_get_setting_types();
	$valid_type    = in_array( $type, $setting_types );

	// Cache settings
	if ( $valid_type && ( 'cache' === $type ) ) {
		$settings = array(
			'expiration'     => DAY_IN_SECONDS * 5, // Five days.
			'flush_manually' => false,
			'display_log'    => km_rpbt_plugin_supports( 'display_cache_log' ),
		);

		return $settings;
	}

	// Default related posts query vars
	$defaults = km_rpbt_get_query_vars();

	// Common settings for the widget and shortcode and wp rest api.
	$settings = array(
		'post_id'        => '',
		'taxonomies'     => '',
		'title'          => __( 'Related Posts', 'related-posts-by-taxonomy' ),
		'format'         => 'links',
		'image_size'     => 'thumbnail',
		'columns'        => 3,
		'link_caption'   => false,
		'caption'        => 'post_title',
		'post_class'     => '',
	);

	$settings = array_merge( $defaults, $settings );

	// There is no default setting for post types.
	$settings['post_types'] = '';

	if ( ! $valid_type ) {
		return $settings;
	}

	$rest_api_type = ( 'wp_rest_api' === $type ) ? $type : '';

	// wp_rest_api settings are the same as a shortcode.
	$type  = $rest_api_type ? 'shortcode' : $type;

	// Custom settings for the shortcode and rest api types.
	if ( ( 'shortcode' === $type ) ) {
		$shortcode_args = array(
			'before_shortcode' => '<div class="rpbt_shortcode">',
			'after_shortcode'  => '</div>',
			'before_title'     => '<h3>',
			'after_title'      => '</h3>',
		);

		$settings = array_merge( $settings, $shortcode_args );
	}

	// Custom settings for the widget.
	if ( ( 'widget' === $type ) ) {
		$settings['random']            = false;
		$settings['singular_template'] = false;
	}

	// Custom settings for the WP rest API.
	if ( $rest_api_type ) {
		$settings['before_shortcode'] = "<div class=\"rpbt_{$rest_api_type}\">";
		$settings['after_shortcode']  = '</div>';
	}

	$settings['type'] = $rest_api_type ? $rest_api_type : $type;

	return $settings;
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
