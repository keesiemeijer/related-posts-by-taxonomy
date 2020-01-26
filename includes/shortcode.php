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
 *     @type array|string   $include_terms    Terms to use for the related posts query. Array or comma separated list of
 *                                            term ids. Default empty (query by the terms of the current post).
 *     @type boolean        $include_parents  Whether to include parent terms in the query for related posts. Default false.
 *     @type boolean        $include_children Whether to include child terms in the query for related posts. Default false.
 *     @type array|string   $exclude_terms    Terms to exlude for the related posts query. Array or comma separated
 *                                            list of term ids. Default empty
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
 *     @type string         $meta_key         Meta key.
 *     @type string|array   $meta_value       Meta value.
 *     @type string         $meta_compare     MySQL operator used for comparing the $meta_value. Accepts '=',
 *                                            '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE',
 *                                            'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'REGEXP',
 *                                            'NOT REGEXP', 'RLIKE', 'EXISTS' or 'NOT EXISTS'.
 *                                            Default is 'IN' when `$meta_value` is an array, '=' otherwise.
 *     @type string         $meta_type        MySQL data type that the meta_value column will be CAST to for
 *                                            comparisons. Accepts 'NUMERIC', 'BINARY', 'CHAR', 'DATE',
 *                                            'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', or 'UNSIGNED'.
 *                                            Default is 'CHAR'.
 * }
 * @return string Related posts html or empty string.
 */
function km_rpbt_related_posts_by_taxonomy_shortcode( $args ) {
	// Empty string is returned if no args were added in the shortcode
	$args = is_array( $args ) ? $args : array();

	$settings = km_rpbt_get_default_settings( 'shortcode' );

	/**
	 * Filter default shortcode attributes.
	 *
	 * @since 0.2.1
	 *
	 * @param array $defaults Default feature arguments. See km_rpbt_related_posts_by_taxonomy_shortcode() for
	 *                        for more information about default feature arguments.
	 */
	$defaults = apply_filters( "related_posts_by_taxonomy_shortcode_defaults", $settings );
	$defaults = array_merge( $settings, (array) $defaults );

	// Filter hook shortcode_atts_related_posts_by_tax.
	$args = shortcode_atts( $defaults, $args, 'related_posts_by_tax' );

	$args['type'] = 'shortcode';
	$args = km_rpbt_validate_shortcode_atts( $args );

	/**
	 * Filter validated shortcode arguments.
	 *
	 * @since 0.1
	 *
	 * @param array $args Shortcode arguments. See km_rpbt_related_posts_by_taxonomy_shortcode() for
	 *                    for more information about feature arguments.
	 */
	$args = apply_filters( "related_posts_by_taxonomy_shortcode_atts", $args );
	$args = array_merge( $defaults, (array) $args );

	$args['type'] = 'shortcode';

	return km_rpbt_get_feature_html( 'shortcode', $args );
}

/**
 * Validate shortcode arguments.
 *
 * Converts string booleans to real booleans.
 *
 * @see km_rpbt_related_posts_by_taxonomy_shortcode()
 *
 * @since 2.1
 * @param array $atts Array with shortcode arguments.
 *                    See km_rpbt_related_posts_by_taxonomy_shortcode() for more
 *                    information on accepted arguments.
 * @return array Array with validated shortcode arguments.
 */
function km_rpbt_validate_shortcode_atts( $atts ) {
	$defaults = km_rpbt_get_default_settings( 'shortcode' );
	$atts     = km_rpbt_validate_args( $atts );

	// Get allowed fields for use in templates
	$atts['fields'] = km_rpbt_get_template_fields( $atts );

	// Convert (strings) to booleans or use defaults.
	$atts['link_caption']     = ( '' !== trim( $atts['link_caption'] ) ) ? $atts['link_caption'] : false;
	$atts['public_only']      = ( '' !== trim( $atts['public_only'] ) ) ? $atts['public_only'] : false;
	$atts['show_date']        = ( '' !== trim( $atts['show_date'] ) ) ? $atts['show_date'] : false;
	$atts['include_parents']  = ( '' !== trim( $atts['include_parents'] ) ) ? $atts['include_parents'] : false;
	$atts['include_children'] = ( '' !== trim( $atts['include_children'] ) ) ? $atts['include_children'] : false;

	// Deprecated argument changed from boolean to null (back compat)
	$atts['related'] = ( '' !== trim( $atts['related'] ) ) ? $atts['related'] : null;

	if ( 'regular_order' !== $atts['include_self'] ) {
		$atts['include_self']  = ( '' !== trim( $atts['include_self'] ) ) ? $atts['include_self'] : false;
	}

	return km_rpbt_validate_booleans( $atts, $defaults );
}
