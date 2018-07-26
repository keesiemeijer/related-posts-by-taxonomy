<?php
/**
 * Plugin feature setting types.
 *
 * @since  2.5.0
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
 * Get the features this plugin supports.
 *
 * Use the {@see related_posts_by_taxonomy_supports} filter to activate and
 * deactivate features in one go.
 *
 * @since  2.3.1
 *
 * @return Array Array with plugin support types.
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
	 * Supported features:
	 *
	 * - widget
	 * - shortcode
	 * - shortcode_hide_empty
	 * - widget_hide_empty
	 *
	 * Opt-in features
	 * - cache
	 * - display_cache_log
	 * - wp_rest_api
	 * - debug
	 *
	 * @since 2.3.1
	 *
	 * @param array $support Array with all supported and opt-in plugin features.
	 */
	$plugin = apply_filters( 'related_posts_by_taxonomy_supports', $supports );

	return array_merge( $supports, (array) $plugin );
}

/**
 * Returns default query vars for the related posts query.
 *
 * @since 2.5.0
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
 * Returns the default settings for a plugin feature.
 *
 * @since 2.2.2
 * @param string $type Type of feature settings. Accepts 'shortcode', 'widget, 'wp_rest_api', 'cache'.
 * @return array|false Array with default settings for a feature.
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

	// Default related posts query vars.
	$defaults = km_rpbt_get_query_vars();

	// There is no default  for post types.
	$defaults['post_types'] = '';

	// Common settings for the widget and shortcode and wp rest api.
	$settings = array(
		'post_id'        => '',
		'taxonomies'     => '',
		'title'          => __( 'Related Posts', 'related-posts-by-taxonomy' ),
		'format'         => 'links',
		'image_size'     => 'thumbnail',
		'columns'        => 3,
		'link_caption'   => false,
		'show_date'      => false,
		'caption'        => 'post_title',
		'post_class'     => '',
	);

	$settings = array_merge( $defaults, $settings );

	if ( ! $valid_type ) {
		return $settings;
	}

	$rest_api_type = ( 'wp_rest_api' === $type ) ? $type : '';

	// wp_rest_api settings are the same as a shortcode.
	$type = $rest_api_type ? 'shortcode' : $type;

	// Custom settings for the shortcode and rest api types.
	if ( 'shortcode' === $type ) {
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
 *                    See km_rpbt_get_related_posts() for for more
 *                    information on accepted arguments.
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

	// Booleans.
	$args = km_rpbt_validate_booleans( $args, $defaults );

	return $args;
}

/**
 * Validates an array or comma separated string with ids.
 *
 * Removes duplicates and "0" values.
 *
 * @since 2.5.0
 * @param string|array $ids Array or comma separated string with ids.
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
 * Validate a boolean value
 *
 * Returns true for true, 1, "1", "true", "on", "yes". Everything else return false.
 *
 * @since 2.5.1
 *
 * @param string|array $value Value to validate
 * @return boolean Boolean value
 */
function km_rpbt_validate_boolean( $value ) {
	return (bool) filter_var( $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
}

/**
 * Validate boolean values in arguments.
 *
 * @since 2.5.1
 *
 * @param array $args     Array with arguments.
 * @param array $defaults Array with default arguments.
 * @return array Array with validated boolean values
 */
function km_rpbt_validate_booleans( $args, $defaults ) {

	// The include_self argument can be a boolean or string 'regular_order'.
	if ( isset( $args['include_self'] ) && ( 'regular_order' === $args['include_self'] ) ) {
		// Do not check this value as a boolean
		$defaults['include_self'] = 'regular_order';
	}

	$booleans = array_filter( (array) $defaults, 'is_bool' );

	foreach ( array_keys( $booleans ) as $key ) {
		if ( isset( $args[ $key ] ) ) {
			$args[ $key ] = km_rpbt_validate_boolean( $args[ $key ] );
		}
	}
	return $args;
}
