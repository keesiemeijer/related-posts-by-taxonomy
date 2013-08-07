<?php
/**
 * Display for the shortcode [related_posts_by_tax]
 *
 * @since 0.1
 *
 * @uses km_rpbt_related_posts_by_taxonomy()
 * @uses km_rpbt_related_posts_by_taxonomy_template()
 *
 * @param string $atts Attributes used by the shortcode
 * @return string Related posts html or empty string
 */
function km_rpbt_related_posts_by_taxonomy_shortcode( $atts ) {
	// todo test if 'related_posts_by_taxonomy' can be added to shortcode_atts in 3.5 and 3.4
	$defaults = array(

		/* shortcode attributes */
		'post_id' => '', 'taxonomies' => 'all', 'format' => 'links',
		'title' => __( 'Related Posts', 'related-posts-by-taxonomy' ),
		'before_title' => '', 'after_title' => '',

		/* attributes for km_rpbt_related_posts_by_taxonomy() */
		'post_types' => 'post', 'posts_per_page' => 5, 'order' => 'DESC',
		'limit_posts' => -1, 'limit_year' => '',
		'limit_month' => '', 'orderby' => 'post_date',
		'exclude_terms' => '', 'exclude_posts' => '',

	);

	$args = shortcode_atts( $defaults , $atts, 'related_posts_by_taxonomy' );
	extract( $args );

	/* shortcode is expected to run inside the loop */
	if ( trim( $post_id ) == '' )
		$args['post_id'] = get_the_ID();

	if ( $taxonomies == 'all' )
		$args['taxonomies'] = array_keys( get_taxonomies( array( 'public' => true ), 'names', 'and' ) );

	if ( !in_array( $format, array( 'links', 'posts', 'excerpts', 'thumbnails' ) ) )
		$args['format'] = 'links';

	$args['post_thumbnail'] = false;
	if ( 'thumbnails' == $format )
		$args['post_thumbnail'] = true;

	$filtered_args = apply_filters( 'related_posts_by_taxonomy_shortcode_atts', $args );
	$args = array_merge( $args, (array) $filtered_args );

	$post_id = $args['post_id'];
	$taxonomies = $args['taxonomies'];
	$format = $args['format'];
	$before_title = $args['before_title'];
	$title = $args['title'];
	$after_title = $args['after_title'];



	/* remove keys not needed for km_rpbt_related_posts_by_taxonomy() */
	$unset_keys = array( 'post_id', 'taxonomies', 'format', 'title', 'before_title', 'after_title', 'fields' );
	foreach ( $unset_keys as $key )
		unset( $args[ $key ] );

	/* get related posts */
	$related_posts = km_rpbt_related_posts_by_taxonomy( $post_id, $taxonomies, $args );

	/* get template for related posts */
	$template = km_rpbt_related_posts_by_taxonomy_template( $format, 'shortcode' );

	if ( $template && !empty( $related_posts ) ) {

		ob_start();

		if ( $title )
			echo $before_title . $title . $after_title;

		global $post; // for setup_postdata( $post ) in $template
		require $template;
		wp_reset_postdata(); // clean up for global $post;

		return ob_get_clean();
	}

	return '';
} // end km_rpbt_related_posts_by_taxonomy_shortcode()