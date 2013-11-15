<?php
/**
 * Display for the shortcode [related_posts_by_tax]
 *
 * @since 0.1
 *
 * @uses km_rpbt_related_posts_by_taxonomy()
 * @uses km_rpbt_related_posts_by_taxonomy_template()
 *
 * @param string  $atts Attributes used by the shortcode
 * @return string Related posts html or empty string
 */
function km_rpbt_related_posts_by_taxonomy_shortcode( $atts ) {

	$plugin_defaults = Related_Posts_By_Taxonomy_Defaults::get_instance();
	$all_tax = (isset($plugin_defaults->all_tax)) ? $plugin_defaults->all_tax : 'all';

	$defaults = array(
		// shortcode defaults
		'post_id' => '', 'taxonomies' => $all_tax, 'format' => 'links',
		'title' => __( 'Related Posts', 'related-posts-by-taxonomy' ),
		'before_title' => '', 'after_title' => '', 'image_size' => 'thumbnail', 'columns' => 3,

		// km_rpbt_related_posts_by_taxonomy defaults
		'post_types' => 'post', 'posts_per_page' => 5, 'order' => 'DESC',
		'limit_posts' => -1, 'limit_year' => '',
		'limit_month' => '', 'orderby' => 'post_date',
		'exclude_terms' => '', 'exclude_posts' => '', 
		'common_terms' => true, // 'post_thumbnail' => '', 'fields' => 'all'
	);


	/* filter defaults before using them (since version 0.2.1). */
	$defaults = apply_filters( 'related_posts_by_taxonomy_shortcode_defaults', $defaults );

	/**
	 * filter hook: shortcode_atts_related_posts_by_tax
	 * filter attributes WordPress >= 3.6 after defaults are set
	 */
	$atts = shortcode_atts( $defaults, $atts, 'related_posts_by_tax' );

	/* filter attributes WordPress < 3.6  after defaults are set */
	$filtered_atts = apply_filters( 'related_posts_by_taxonomy_shortcode_atts', $atts );

	/* make sure all defaults are present after filtering */
	$atts = array_merge( $atts, (array) $filtered_atts );

	/* validate shortcode defaults */

	if ( '' == trim( $atts['post_id'] ) )
		$atts['post_id'] = get_the_ID();

	if ( $atts['taxonomies'] == $all_tax )
		$atts['taxonomies'] = array_keys( $plugin_defaults->taxonomies );

	if ( !in_array( $atts['format'], array_keys( $plugin_defaults->formats ) ) )
		$atts['format'] = 'links';

	$atts['post_thumbnail'] = false;
	if ( 'thumbnails' == $atts['format'] ) {

		$atts['post_thumbnail'] = true;

		if ( !in_array( $atts['image_size'], array_keys( $plugin_defaults->image_sizes ) ) )
			$atts['image_size'] = 'thumbnail';

		// set public variables $image_size and $columns
		$image_size = $atts['image_size'];
		$atts['columns'] = absint( $atts['columns'] );
		$atts['columns'] = $columns = ( $atts['columns'] > 0 ) ? $atts['columns'] : 3;
	}

	/* function arguments */
	
	$f_args = $atts;
	$f_defaults = array(
		'post_types', 'posts_per_page', 'order', // 'fields', 
		'limit_posts', 'limit_year', 'limit_month',
		'orderby', 'exclude_terms', 'exclude_posts',
		'post_thumbnail', 'common_terms'
	);

	/* remove arguments not needed for km_rpbt_related_posts_by_taxonomy() */
	foreach ( $f_args as $arg => $value ) {
		if ( !in_array( $arg, $f_defaults ) )
			unset( $f_args[ $arg ] );
	}

	/* get related posts */
	$related_posts = km_rpbt_related_posts_by_taxonomy( $atts['post_id'], $atts['taxonomies'], $f_args );

	/* clean up attributes for template */
	unset( $all_tax, $defaults, $filtered_atts, $plugin_defaults, $f_args, $f_defaults, $arg, $value );

	/* get template for related posts */
	$template = km_rpbt_related_posts_by_taxonomy_template( $atts['format'], 'shortcode' );

	if ( $template && !empty( $related_posts ) ) {

		ob_start();

		if ( $atts['title'] )
			echo $atts['before_title'] . $atts['title'] . $atts['after_title'];

		global $post; // for setup_postdata( $post ) in $template
		require $template;
		wp_reset_postdata(); // clean up for global $post;

		return ob_get_clean();
	}

	return '';
} // end km_rpbt_related_posts_by_taxonomy_shortcode()