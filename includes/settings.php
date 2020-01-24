<?php
/**
 * Get plugin feature setting types.
 *
 * Returns feature types that return or display related posts.
 *
 * - shortcode
 * - widget
 * - wp_rest_api
 *
 * @since 2.5.0
 *
 * @return array Array with supported setting types.
 */
function km_rpbt_get_setting_types() {
	return array(
		'shortcode',
		'widget',
		'wp_rest_api',
	);
}

/**
 * Check if a type is a valid plugin setting type.
 *
 * @since 2.6.0
 *
 * @param string $type Plugin feature settings type.
 * @return boolean       True if it's valid settings type.
 */
function km_rpbt_is_valid_settings_type( $type ) {
	return is_string( $type ) && in_array( $type, km_rpbt_get_setting_types() );
}

/**
 * Get the type of feature from arguments.
 *
 * @since 2.6.0
 *
 * @param array $args Arguments.
 * @return string Feature type or empty string if no valid settings type was found in the arguments.
 */
function km_rpbt_get_settings_type( $args ) {
	if ( isset( $args['type'] ) && km_rpbt_is_valid_settings_type( $args['type'] ) ) {
		return $args['type'];
	}
	return '';
}

/**
 * Get the allowed fields arguments for use in templates.
 *
 * Fields '' or 'ids' are allowed for use in templates.
 * The query for related posts returns post objects or ids with these fields.
 *
 * @since 2.6.0
 *
 * @param array $args Arguments to get the fields from.
 * @return string Arguments with fields argument set to '' or 'ids'.
 */
function km_rpbt_get_template_fields( $args ) {
	$fields   = isset( $args['fields'] ) ? $args['fields'] : '';
	$id_query = km_rpbt_plugin_supports( 'id_query' ) || ( 'ids' === $fields );
	return $id_query ? 'ids' : '';
}

/**
 * Get all the features this plugin supports.
 *
 * See the {@see related_posts_by_taxonomy_supports} filter for the
 * supported and opt-in features.
 *
 * @since 2.3.1
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
		'id_query'             => false,
		'lazy_loading'         => false,
		'debug'                => false,
	);

	/**
	 * Filter all supported plugin features at once, for convenience.
	 *
	 * Supported plugin features:
	 *
	 * - widget
	 * - shortcode
	 * - shortcode_hide_empty
	 * - widget_hide_empty
	 *
	 * Opt-in features
	 *
	 * - cache
	 * - display_cache_log
	 * - wp_rest_api
	 * - id_query
	 * - lazy_loading
	 * - debug
	 *
	 * @since 2.3.1
	 *
	 * @param array $supports Array with all supported and opt-in plugin features.
	 */
	$plugin = apply_filters( 'related_posts_by_taxonomy_supports', $supports );

	return array_merge( $supports, (array) $plugin );
}

/**
 * Returns default query vars for the related posts query.
 *
 * @since 2.5.0
 *
 * @see km_rpbt_query_related_posts()
 *
 * @return array Array with default query vars.
 */
function km_rpbt_get_query_vars() {
	return array(
		'post_types'       => 'post',
		'posts_per_page'   => 5,
		'order'            => 'DESC',
		'orderby'          => 'post_date',
		'fields'           => '',
		'include_terms'    => '',
		'exclude_terms'    => '',
		'include_parents'  => false,
		'include_children' => false,
		'exclude_posts'    => '',
		'limit_posts'      => -1,
		'limit_month'      => '',
		'post_thumbnail'   => false,
		'public_only'      => false,
		'include_self'     => false,
		'meta_key'         => '',
		'meta_value'       => '',
		'meta_compare'     => '',
		'meta_type'        => '',

		// Deprecated arguments (can still be used)
		'terms'            => '',
		'related'          => null,
		'limit_year'       => '',
	);
}

/**
 * Returns the default settings for a plugin feature.
 *
 * @see km_rpbt_get_setting_types()
 *
 * @since 2.2.2
 * @param string $type Type of feature settings. See km_rpbt_get_setting_types() for the
 *                     setting types that are supported.
 * @return array Array with default settings for a feature.
 */
function km_rpbt_get_default_settings( $type = '' ) {
	$valid_type = km_rpbt_is_valid_settings_type( $type );
	$type       = $valid_type ? $type : '';
	$selector   = $type ? $type : 'related_posts';

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
		'gallery_format' => '',
		'columns'        => 3,
		'caption'        => 'post_title',
		'link_caption'   => false,
		'show_date'      => false,
		'post_class'     => '',

		// back compat: double quoted class attribute
		"before_{$selector}" => '<div class="rpbt_' . $selector . '">',
		"after_{$selector}"  => '</div>',
		'before_title'       => '<h3>',
		'after_title'        => '</h3>',
	);

	$settings = array_merge( $defaults, $settings );

	if ( 'widget' === $type ) {
		// Custom settings for the widget.
		$settings['random']            = false;
		$settings['singular_template'] = false;
	}

	$settings['type'] = $type;

	return $settings;
}

/**
 * Returns sanitized arguments.
 *
 * @since 2.1
 * @param array $args Arguments to be sanitized.
 *                    See km_rpbt_get_related_posts() for more
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
	$args['fields']       = is_string( $args['fields'] ) ? $args['fields'] : '';
	$args['orderby']      = is_string( $args['orderby'] ) ? $args['orderby'] : '';
	$args['order']        = is_string( $args['order'] ) ? $args['order'] : '';
	$args['meta_key']     = is_string( $args['meta_key'] ) ? $args['meta_key'] : '';
	$args['meta_compare'] = is_string( $args['meta_compare'] ) ? $args['meta_compare'] : '';
	$args['meta_type']    = is_string( $args['meta_type'] ) ? $args['meta_type'] : '';
	// Note: meta_value is sanitized by WordPress (mixed value).

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
 * Validate arguments in common with all plugin features.
 *
 * @since 2.6.0
 *
 * @param array  $args Array with common arguments.
 * @param string $type Type of plugin feature arguments.
 * @return array Validated arguments.
 */
function km_rpbt_validate_args( $args ) {
	$type     = km_rpbt_get_settings_type( $args );
	$defaults = km_rpbt_get_default_settings( $type );

	/* make sure all defaults are present */
	$args = array_merge( $defaults, $args );
	$args['title'] = trim( $args['title'] );

	if ( empty( $args['post_id'] ) ) {
		$args['post_id'] = get_the_ID();
	}

	/* If no post type is set use the post type of the current post */
	if ( empty( $args['post_types'] ) ) {
		$post_type = get_post_type( $args['post_id'] );
		$args['post_types'] = $post_type ? array( $post_type ) : array( 'post' );
	}

	if ( 'thumbnails' === $args['format'] ) {
		$args['post_thumbnail'] = true;
	}

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
 * Validate a boolean value
 *
 * Returns true for true, "true", 1, "1", "on", "yes". Everything else return false.
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

	// If deprecated argument is not null treat it like a boolean (back compat).
	if ( isset( $args['related'] ) && ! is_null( $args['related'] ) ) {
		$defaults['related'] = true;
	}

	$booleans = array_filter( (array) $defaults, 'is_bool' );

	foreach ( array_keys( $booleans ) as $key ) {
		if ( isset( $args[ $key ] ) ) {
			$args[ $key ] = km_rpbt_validate_boolean( $args[ $key ] );
		}
	}
	return $args;
}
