<?php
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

	$plugin_defaults = Related_Posts_By_Taxonomy_Defaults::get_instance();

	$defaults = array(
		// shortcode defaults
		'post_id' => '', 'taxonomies' => $plugin_defaults->all_tax, 'format' => 'links',
		'title' => __( 'Related Posts', 'related-posts-by-taxonomy' ),
		'before_title' => '', 'after_title' => '', 'image_size' => 'thumbnail', 'columns' => 3,
		'caption' => 'post_title',

		// km_rpbt_related_posts_by_taxonomy defaults
		'post_types' => '', 'posts_per_page' => 5, 'order' => 'DESC',
		'limit_posts' => -1, 'limit_year' => '',
		'limit_month' => '', 'orderby' => 'post_date',
		'exclude_terms' => '', 'include_terms' => '',  'exclude_posts' => '',
		'relation' => 'AND', // 'post_thumbnail' => '', 'fields' => 'all'
	);


	/**
	 * Set new default attributes.
	 *
	 * @since 0.2.1
	 *
	 * @param array   $defaults See $defaults above
	 */
	$defaults = apply_filters( 'related_posts_by_taxonomy_shortcode_defaults', $defaults );

	/**
	 * filter shortcode_atts_related_posts_by_tax
	 *
	 * hook can be used by WordPress >= 3.6
	 *
	 * @param array   $rpbt_args See $defaults above
	 */
	$rpbt_args = shortcode_atts( $defaults, $rpbt_args, 'related_posts_by_tax' );

	/**
	 * Filter default attributes (back compatibility).
	 *
	 * @param array   $rpbt_args See $defaults above
	 */
	$filtered_args = apply_filters( 'related_posts_by_taxonomy_shortcode_atts', $rpbt_args );

	/* make sure all defaults are present after filtering */
	$rpbt_args = array_merge( $rpbt_args, (array) $filtered_args );

	/* add type for use in templates */
	$rpbt_args['type'] = 'shortcode';

	/* validate filtered attributes */

	if ( '' === trim( $rpbt_args['post_id'] ) ) {
		$rpbt_args['post_id'] = get_the_ID();
	}

	if ( '' === trim( $rpbt_args['post_types'] ) ) {
		$post_types = get_post_type( $rpbt_args['post_id'] );
		$rpbt_args['post_types'] = ( $post_types ) ? $post_types : 'post';
	}

	if ( $rpbt_args['taxonomies'] == $plugin_defaults->all_tax ) {
		$rpbt_args['taxonomies'] = array_keys( $plugin_defaults->taxonomies );
	}

	$rpbt_args['post_thumbnail'] = '';
	if ( 'thumbnails' === $rpbt_args['format'] ) {
		$rpbt_args['post_thumbnail'] = 1;
	}

	if ( !in_array( $rpbt_args['image_size'], array_keys( $plugin_defaults->image_sizes ) ) ) {
		$rpbt_args['image_size'] = 'thumbnail';
	}

	/* public template variables */
	$image_size = $rpbt_args['image_size'];
	$rpbt_args['columns'] = absint( $rpbt_args['columns'] );
	$rpbt_args['columns'] = $columns = ( $rpbt_args['columns'] > 0 ) ? $rpbt_args['columns'] : 3;

	/* function km_rpbt_related_posts_by_taxonomy arguments */
	$function_args = $rpbt_args;

	/* restricted arguments */
	unset( $function_args['post_id'], $function_args['taxonomies'], $function_args['fields'] );

	/* get related posts */
	$related_posts = km_rpbt_related_posts_by_taxonomy( $rpbt_args['post_id'], $rpbt_args['taxonomies'], $function_args );

	/* clean up attributes before calling template */
	unset( $plugin_defaults, $defaults, $filtered_args, $post_types, $function_args );

	/* get template for related posts */
	$template = km_rpbt_related_posts_by_taxonomy_template( $rpbt_args['format'], $rpbt_args['type'] );

	if ( $template && !empty( $related_posts ) ) {

		ob_start();

		if ( $rpbt_args['title'] ) {
			echo $rpbt_args['before_title'] . $rpbt_args['title'] . $rpbt_args['after_title'];
		}

		global $post; // for setup_postdata( $post ) in $template
		require $template;
		wp_reset_postdata(); // clean up global $post;

		return ob_get_clean();
	}

	return '';
} // end km_rpbt_related_posts_by_taxonomy_shortcode()