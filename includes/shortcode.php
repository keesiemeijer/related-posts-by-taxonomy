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
 * @param string|array $atts {
 *     Optional. Arguments used by the shortcode.
 *
 *     @type int            $post_id          Post id to use for related posts query . Default empty (current post).
 *     @type string|array   $taxonomies       Taxonomies to use for related posts query. Default empty (all taxonomies).
 *     @type string|array   $post_types       Post types to use for related posts query. Defaut current post post type.
 *     @type int            $posts_per_page   How many related posts to display. Default 5.
 *     @type string         $order            Order to display related posts in.
 *                                            Accepts 'DESC', 'ASC' and 'RAND'. Default 'DESC'.
 *     @type string         $orderby          Order by post date or by date modified.
 *                                            Accepts 'post_date'and 'post_modified'. Default 'post_date'.
 *     @type string         $before_shortcode HTML to display before shortcode. Default `<div class="rpbt_shortcode">`.
 *     @type string         $after_shortcode  HTML to display before shortcode. Default `</div>`.
 *     @type string         $title            Title above related posts Default 'Related Posts'.
 *     @type string         $show_date        Whether to display the post date after the title. Default false.
 *     @type string         $before_title     HTML before title. Default `<h3>`.
 *     @type string         $after_title      HTML after title. Default `</h3>`.
 *     @type array|string   $terms            Terms to use for the related posts query. Array or comma separated
 *                                            list of term ids. It doesn't matter if the current post has these terms in common.
 *                                            Default empty.
 *     @type array|string   $include_terms    Terms to include for the related posts query. Array or comma separated
 *                                            list of term ids. Only includes terms in common with the current post.
 *                                            Default empty.
 *     @type array|string   $exclude_terms    Terms to exlude for the related posts query. Array or comma separated
 *                                            list of term ids. Default empty
 *     @type boolean        $related          If false the ` $include_terms` argument also includes terms
 *                                            not in common with the current post. Default true.
 *     @type array|string   $exclude_post     Exclude posts for the related posts query. Array or comma separated
 *                                            list of post ids. Default empty.
 *     @type string         $format           Format to display related posts.
 *                                            Accepts 'links', 'posts', 'excerpts' and 'thumbnails'. Default 'links'.
 *     @type string         $image_size       Image size used for the format thumbnails. Accepts default image sizes
 *                                            'thumbnail', 'medium', 'large', 'post-thumbnail' and the image sizes set by
 *                                            the current theme. Default 'thumbnail'
 *     @type int            $columns          The number of image columns for the thumbnail gallery. Default 3.
 *     @type string         $caption          Caption text for the post thumbnail.
 *                                            Accepts 'post_title', 'post_excerpt', 'attachment_caption', 'attachment_alt', or
 *                                            a custom string. Default 'post_title'
 *     @type boolean        $link_caption     Whether to link the caption to the related post. Default false.
 *     @type int            $limit_posts      Limit the posts to search related posts in. Default -1 (search in all posts).
 *     @type int            $limit_month      Limit the posts to the past months to search related posts in.
 *                                            Default empty (search in all posts).
 *     @type boolean        $public_only      Whether to exclude private posts in the related posts display, even if
 *                                            the current user has the capability to see those posts.
 *                                            Default false (include private posts)
 *     @type string|boolean $include_self     Whether to include the current post in the related posts results. The included
 *                                            post is ordered at the top. Use 'regular_order' to include the current post ordered by
 *                                            terms in common. Default false (exclude current post).
 *     @type string         $post_class       Add a class to the related post items. Default empty.
 * }
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

	// Filter and validate shortcode arguments.
	$atts = km_rpbt_filter_arguments( $atts, 'shortcode' );

	/* Un-filterable arguments */
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
	 * Fires after the related posts are displayed by the widget or shortcode.
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
 * @see km_rpbt_related_posts_by_taxonomy_shortcode()
 *
 * @since 2.1
 * @param array $related_posts Array with related post objects.
 * @param array $rpbt_args     Shortcode arguments.
 *                             See km_rpbt_related_posts_by_taxonomy_shortcode() for for more
 *                             information on accepted arguments.
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
