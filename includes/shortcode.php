<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Callback function for the shortcode [related_posts_by_tax].
 *
 * The shortcode returns an empty string if it's is not supported by this plugin.
 *
 * @since 0.1
 *
 * @uses km_rpbt_query_related_posts()
 * @uses km_rpbt_get_template()
 *
 * @param string $atts Attributes used by the shortcode.
 * @return string Related posts html or empty string.
 */
function km_rpbt_related_posts_by_taxonomy_shortcode( $atts ) {

	/* for filter recursion (infinite loop) */
	static $recursing = false;

	if ( ! $recursing ) {
		$recursing = true;
	} else {
		return '';
	}

	if ( ! km_rpbt_plugin_supports( 'shortcode' ) ) {
		$recursing = false;
		return '';
	}

	$defaults = km_rpbt_get_default_settings( 'shortcode' );

	/**
	 * Filter default attributes.
	 *
	 * @since 0.2.1
	 *
	 * @param array $defaults See $defaults above
	 */
	$defaults = apply_filters( 'related_posts_by_taxonomy_shortcode_defaults', $defaults );

	/* Can also be filtered in WordPress > 3.5 (hook: shortcode_atts_related_posts_by_tax) */
	$atts = shortcode_atts( (array) $defaults, $atts, 'related_posts_by_tax' );

	/* Validates atts. Sets the post type and post id if not set in filters above */
	$validated_args = km_rpbt_validate_shortcode_atts( (array) $atts );

	/**
	 * Filter attributes.
	 *
	 * @param array $atts See $defaults above
	 */
	$atts = apply_filters( 'related_posts_by_taxonomy_shortcode_atts', $validated_args );
	$atts = array_merge( $validated_args, (array) $atts );

	/* Un-filterable arguments */
	$atts['type'] = 'shortcode';
	$atts['fields'] = '';

	// Get the related posts from database or cache.
	$related_posts = km_rpbt_get_related_posts( $atts['post_id'], $atts );

	/*
	 * Whether to hide the shortcode if no related posts are found.
	 * Set by the related_posts_by_taxonomy_shortcode_hide_empty filter.
	 * Default true.
	 */
	$hide_empty = (bool) km_rpbt_plugin_supports( 'shortcode_hide_empty' );

	$shortcode = '';
	if ( ! $hide_empty || ! empty( $related_posts ) ) {
		$shortcode = km_rpbt_shortcode_output( $related_posts, $atts );
	}

	/**
	 * Fires after the related posts are displayed
	 *
	 * @param string Display type, widget or shortcode.
	 */
	do_action( 'related_posts_by_taxonomy_after_display', 'shortcode' );

	$recursing = false;

	return $shortcode;
} // end km_rpbt_related_posts_by_taxonomy_shortcode()

/**
 * Returns shortcode output.
 *
 * @since 2.1
 * @param array $related_posts Array with related post objects.
 * @param array $rpbt_args     Shortcode settings.
 * @return string Shortcode output.
 */
function km_rpbt_shortcode_output( $related_posts, $rpbt_args ) {

	/* make sure all defaults are present */
	$rpbt_args = array_merge( km_rpbt_get_default_settings( $rpbt_args['type'] ), $rpbt_args );

	/* get the template depending on the format  */
	$template = km_rpbt_get_template( $rpbt_args['format'], $rpbt_args['type'] );

	if ( ! $template ) {
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

	$shortcode = '';
	if ( $output ) {
		$shortcode = $rpbt_args['before_shortcode'] . "\n" ;
		$shortcode .= trim( $rpbt_args['title'] ) . "\n";
		$shortcode .= $output . "\n";
		$shortcode .= $rpbt_args['after_shortcode'];
	}

	return trim( $shortcode );
}

/**
 * Validate shortcode attributes.
 *
 * @since 2.1
 * @param array $atts Array with shortcode attributes.
 * @return array Array with validated shortcode attributes.
 */
function km_rpbt_validate_shortcode_atts( $atts ) {
	$defaults = km_rpbt_get_default_settings( 'shortcode' );

	/* make sure all defaults are present */
	$atts = array_merge( $defaults, $atts );

	// Default to shortcode.
	$atts['type']  = 'shortcode';
	$atts['title'] = trim( $atts['title'] );

	if ( '' === trim( $atts['post_id'] ) ) {
		$atts['post_id'] = get_the_ID();
	}

	/* if no post type is set use the post type of the current post (new default since 0.3) */
	if ( empty( $atts['post_types'] ) ) {
		$post_type = get_post_type( $atts['post_id'] );
		$atts['post_types'] = $post_type ? array( $post_type ) : array( 'post' );
	}

	if ( 'thumbnails' === $atts['format'] ) {
		$atts['post_thumbnail'] = true;
	}

	// Convert (strings) to booleans.
	$atts['related']      = ( '' !== trim( $atts['related'] ) ) ? $atts['related'] : true;
	$atts['link_caption'] = ( '' !== trim( $atts['link_caption'] ) ) ? $atts['link_caption'] : false;
	$atts['public_only']  = ( '' !== trim( $atts['public_only'] ) ) ? $atts['public_only'] : false;
	$atts['show_date']    = ( '' !== trim( $atts['show_date'] ) ) ? $atts['show_date'] : false;

	$booleans = array_filter( $defaults, 'is_bool' );
	if ( 'regular_order' === $atts['include_self'] ) {
		unset( $booleans['include_self'] );
	} else {
		$atts['include_self']  = ( '' !== trim( $atts['include_self'] ) ) ? $atts['include_self'] : false;
	}

	$atts = km_rpbt_validate_booleans( $atts, array_keys( $booleans ) );

	return $atts;
}
