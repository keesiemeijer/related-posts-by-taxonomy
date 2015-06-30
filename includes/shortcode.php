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

	$plugin_defaults = Related_Posts_By_Taxonomy_Defaults::get_instance();

	$defaults = array(

		// shortcode defaults
		'post_id' => '', 'taxonomies' => $plugin_defaults->all_tax,
		'before_shortcode' => '<div class="rpbt_shortcode">', 'after_shortcode' => '</div>',
		'before_title' => '<h3>', 'after_title' => '</h3>',
		'title' => __( 'Related Posts', 'related-posts-by-taxonomy' ),
		'format' => 'links',
		'image_size' => 'thumbnail', 'columns' => 3,
		'caption' => 'post_title',


		// km_rpbt_related_posts_by_taxonomy defaults
		'post_types' => '', 'posts_per_page' => 5, 'order' => 'DESC',
		'limit_posts' => -1, 'limit_year' => '',
		'limit_month' => '', 'orderby' => 'post_date',
		'exclude_terms' => '', 'include_terms' => '',  'exclude_posts' => '',
		'related' => '', // 'post_thumbnail' => '', 'fields' => 'all'
	);

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

	/**
	 * Filter defaults.
	 *
	 * @param array   $rpbt_args See $defaults above
	 */
	$filtered_args = apply_filters( 'related_posts_by_taxonomy_shortcode_atts', $rpbt_args );

	/* make sure all defaults are present after filtering */
	$rpbt_args = array_merge( $rpbt_args, (array) $filtered_args );

	/* add type for use in templates */
	$rpbt_args['type'] = 'shortcode';

	$rpbt_args['title'] = trim( $rpbt_args['title'] );

	/* validate filtered attributes */

	if ( '' === trim( $rpbt_args['post_id'] ) ) {
		$rpbt_args['post_id'] = get_the_ID();
	}

	/* if no post type is set use the post type of the current post (new default since 0.3) */
	if ( '' === trim( $rpbt_args['post_types'] ) ) {
		$post_types = get_post_type( $rpbt_args['post_id'] );
		$rpbt_args['post_types'] = ( $post_types ) ? $post_types : 'post';
	}

	if ( $rpbt_args['taxonomies'] === $plugin_defaults->all_tax ) {
		$rpbt_args['taxonomies'] = array_keys( $plugin_defaults->taxonomies );
	}

	$rpbt_args['post_thumbnail'] = '';
	if ( 'thumbnails' === $rpbt_args['format'] ) {
		$rpbt_args['post_thumbnail'] = 1;
	}

	/* public template variables $image_size && $columns (deprecated in version 0.3) */
	$image_size = $rpbt_args['image_size'];
	$columns = absint( $rpbt_args['columns'] );

	// convert 'related' string to boolean.
	$rpbt_args['related'] = ( '' !== trim( $rpbt_args['related'] ) ) ? $rpbt_args['related'] : true;
	$rpbt_args['related'] = filter_var( $rpbt_args['related'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

	// non boolean used, default to true
	$rpbt_args['related'] = ( $rpbt_args['related'] === NULL ) ? true : $rpbt_args['related'];

	$function_args = $rpbt_args;

	/* restricted arguments */
	unset( $function_args['post_id'], $function_args['taxonomies'], $function_args['fields'] );

	if(isset($rpbt_args['cache']) && $rpbt_args['cache']) {
	
		$related_posts = $plugin_defaults->cache->get_related_posts( $rpbt_args );
	} else {
		/* get related posts */
		$related_posts = km_rpbt_related_posts_by_taxonomy( $rpbt_args['post_id'], $rpbt_args['taxonomies'], $function_args );
	}

	/* clean up variables before calling the template */
	unset( $plugin_defaults, $defaults, $filtered_args, $post_types, $function_args );

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