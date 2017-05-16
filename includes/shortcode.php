<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_shortcode( 'related_posts_by_tax', 'km_rpbt_related_posts_by_taxonomy_shortcode' );

/**
 * Callback function for the shortcode [related_posts_by_tax].
 *
 * @since 0.1
 *
 * @uses km_rpbt_related_posts_by_taxonomy()
 * @uses km_rpbt_related_posts_by_taxonomy_template()
 *
 * @param string $rpbt_args Attributes used by the shortcode.
 * @return string Related posts html or empty string.
 */
function km_rpbt_related_posts_by_taxonomy_shortcode( $rpbt_args ) {

	/* for filter recursion (infinite loop) */
	static $recursing = false;

	if ( ! $recursing ) {
		$recursing = true;
	} else {
		return '';
	}

	$plugin = km_rpbt_plugin();

	if ( ! $plugin ) {
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
	$rpbt_args = shortcode_atts( (array) $defaults, $rpbt_args, 'related_posts_by_tax' );

	/* Validates atts. Sets the post type and post id if not set in filters above */
	$validated_args = km_rpbt_validate_shortcode_atts( (array) $rpbt_args );

	/**
	 * Filter attributes.
	 *
	 * @param array $rpbt_args See $defaults above
	 */
	$rpbt_args = apply_filters( 'related_posts_by_taxonomy_shortcode_atts', $validated_args );
	$rpbt_args = array_merge( $validated_args, (array) $rpbt_args );

	/* Not filterable */
	$rpbt_args['type'] = 'shortcode';

	$function_args = $rpbt_args;

	/* restricted arguments */
	unset( $function_args['post_id'], $function_args['taxonomies'], $function_args['fields'] );

	$cache = $plugin->cache instanceof Related_Posts_By_Taxonomy_Cache;

	if ( $cache && ( isset( $rpbt_args['cache'] ) && $rpbt_args['cache'] ) ) {
		$related_posts = $plugin->cache->get_related_posts( $rpbt_args );
	} else {
		/* get related posts */
		$related_posts = km_rpbt_related_posts_by_taxonomy( $rpbt_args['post_id'], $rpbt_args['taxonomies'], $function_args );
	}

	/**
	 * Filter whether to hide the widget if no related posts are found.
	 *
	 * @since 0.1
	 *
	 * @param bool $hide Whether to hide the shortcode if no related posts are found.
	 *                      Defaults to true.
	 */
	$hide_empty = (bool) apply_filters( 'related_posts_by_taxonomy_shortcode_hide_empty', true );

	$shortcode = '';
	if ( ! $hide_empty || ! empty( $related_posts ) ) {
		$shortcode = km_rpbt_shortcode_output( $related_posts, $rpbt_args );
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
	$template = km_rpbt_related_posts_by_taxonomy_template( $rpbt_args['format'], $rpbt_args['type'] );

	if ( ! $template ) {
		return '';
	}

	if ( $rpbt_args['title'] ) {
		$rpbt_args['title'] = $rpbt_args['before_title'] . $rpbt_args['title'] . $rpbt_args['after_title'];
	}

	global $post; // Used for setup_postdata() in templates.

	/* public template variables */
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

	/* make sure all defaults are present */
	$atts = array_merge( km_rpbt_get_default_settings( 'shortcode' ), $atts );

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
	$atts['related']      = (bool) filter_var( $atts['related'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
	$atts['link_caption'] = ( '' !== trim( $atts['link_caption'] ) ) ? $atts['link_caption'] : false;
	$atts['link_caption'] = (bool) filter_var( $atts['link_caption'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

	return $atts;
}
