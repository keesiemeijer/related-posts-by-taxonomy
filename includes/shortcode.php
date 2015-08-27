<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Display for the shortcode [related_posts_by_tax]
 *
 * @since 0.1
 *
 * @uses km_rpbt_related_posts_by_taxonomy()
 * @uses km_rpbt_related_posts_by_taxonomy_template()
 *
 * @param string  $rpbt_args Attributes used by the shortcode
 * @return string Related posts html or empty string
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

	if ( !$plugin ) {
		return '';
	}

	$defaults = $_defaults = array(

		// shortcode defaults
		'post_id' => '', 'taxonomies' => $plugin->all_tax,
		'before_shortcode' => '<div class="rpbt_shortcode">', 'after_shortcode' => '</div>',
		'before_title' => '<h3>', 'after_title' => '</h3>',
		'title' => __( 'Related Posts', 'related-posts-by-taxonomy' ),
		'format' => 'links',
		'image_size' => 'thumbnail', 'columns' => 3,
		'caption' => 'post_title', 'type' => 'shortcode',
	);


	/* add default args to shortcode args */
	$defaults = array_merge( km_rpbt_get_default_args(), $defaults );

	/**
	 * Set new default attributes.
	 *
	 * @since 0.2.1
	 *
	 * @param array   $defaults See $defaults above
	 */
	$defaults = apply_filters( 'related_posts_by_taxonomy_shortcode_defaults', $defaults );

	/* Can be filtered in WordPress > 3.5 (hook: shortcode_atts_related_posts_by_tax) */
	$rpbt_args = shortcode_atts( $defaults, $rpbt_args, 'related_posts_by_tax' );

	/* Validate attributes */
	$rpbt_args = km_rpbt_validate_shortcode_atts( $rpbt_args, $_defaults );

	/**
	 * Filter defaults.
	 *
	 * @param array   $rpbt_args See $defaults above
	 */
	$rpbt_args = apply_filters( 'related_posts_by_taxonomy_shortcode_atts', $rpbt_args );


	/* Validate once more */
	$rpbt_args = km_rpbt_validate_shortcode_atts( $rpbt_args, $_defaults );

	/* public template variables */
	$image_size = $rpbt_args['image_size']; // deprecated in version 0.3
	$columns = absint( $rpbt_args['columns'] ); // deprecated in version 0.3

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

	/* clean up variables before calling the template */
	unset( $plugin, $defaults, $atts_filter, $post_types, $function_args, $cache );

	/**
	 * Filter whether to hide the widget if no related posts are found.
	 *
	 * @since 0.1
	 *
	 * @param bool    $hide Whether to hide the shortcode if no related posts are found.
	 *                      Defaults to true.
	 */
	$hide_empty = (bool) apply_filters( 'related_posts_by_taxonomy_shortcode_hide_empty', true );

	$rpbt_shortcode = $shortcode = '';

	if ( !$hide_empty || !empty( $related_posts ) ) {

		/* get the template depending on the format  */
		$template = km_rpbt_related_posts_by_taxonomy_template( $rpbt_args['format'], 'shortcode' );


		if ( $rpbt_args['title'] ) {
			$rpbt_args['title'] = $rpbt_args['before_title'] . $rpbt_args['title'] . $rpbt_args['after_title'];
		}

		if ( $template ) {
			global $post; // used for setup_postdata() in templates
			ob_start();
			require $template;
			$shortcode = ob_get_clean();
			$shortcode = trim( $shortcode );
			wp_reset_postdata(); // clean up global $post variable;
		}

		if ( $shortcode ) {
			$rpbt_shortcode = $rpbt_args['before_shortcode'] . "\n" ;
			$rpbt_shortcode .= trim( $rpbt_args['title'] ) . "\n";
			$rpbt_shortcode .= $shortcode . "\n";
			$rpbt_shortcode .= $rpbt_args['after_shortcode'];
		}
	}

	/**
	 * After the related posts are displayed
	 *
	 * @param string  Display type, widget or shortcode.
	 */
	do_action( 'related_posts_by_taxonomy_after_display', 'shortcode' );

	$recursing = false;

	return trim( $rpbt_shortcode );
} // end km_rpbt_related_posts_by_taxonomy_shortcode()


/**
 * Validate shortcode attributes.
 *
 * @since 2.1
 * @param array   $rpbt_args Array with shortcode attributes.
 * @return array Array with validated shortcode attributes.
 */
function km_rpbt_validate_shortcode_atts( $rpbt_args, $defaults ) {

	$plugin = km_rpbt_plugin();

	if ( !$plugin || !is_array( $rpbt_args ) ) {
		return $rpbt_args;
	}

	/* make sure all defaults are present after filtering */
	$rpbt_args = array_merge( $defaults, $rpbt_args );

	// default to shortcode
	$rpbt_args['type']  = 'shortcode';

	$rpbt_args['title'] = trim( $rpbt_args['title'] );

	if ( '' === trim( $rpbt_args['post_id'] ) ) {
		$rpbt_args['post_id'] = get_the_ID();
	}

	if ( $rpbt_args['taxonomies'] === $plugin->all_tax ) {
		$rpbt_args['taxonomies'] = array_keys( $plugin->taxonomies );
	}

	/* if no post type is set use the post type of the current post (new default since 0.3) */
	if ( empty( $rpbt_args['post_types'] ) ) {
		$post_types = get_post_type( $rpbt_args['post_id'] );
		$rpbt_args['post_types'] = ( $post_types ) ? $post_types : array( 'post' );
	}

	$rpbt_args['post_thumbnail'] = false;
	if ( 'thumbnails' === $rpbt_args['format'] ) {
		$rpbt_args['post_thumbnail'] = true;
	}

	// convert 'related' string to boolean true if empty.
	$rpbt_args['related'] = ( '' !== trim( $rpbt_args['related'] ) ) ? $rpbt_args['related'] : true;

	return $rpbt_args;
}