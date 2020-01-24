<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns default shortcode atts
 *
 * @deprecated 2.2.2
 *
 * @since 2.1
 * @return array Array with default shortcode atts
 */
function km_rpbt_get_shortcode_atts() {

	_deprecated_function( __FUNCTION__, '2.2.2', 'km_rpbt_get_default_settings' );

	if ( $plugin = km_rpbt_plugin() ) {
		return km_rpbt_get_default_settings( 'shortcode' );
	}

	return array();
}

/**
 * Returns shortcode output.
 *
 * @see km_rpbt_related_posts_by_taxonomy_shortcode()
 *
 * @deprecated 2.6.0 Use km_rpbt_get_related_posts_html() instead.
 *
 * @since 2.1
 * @param array $related_posts Array with related post objects.
 * @param array $rpbt_args     Shortcode arguments.
 *                             See km_rpbt_related_posts_by_taxonomy_shortcode() for more
 *                             information on accepted arguments.
 * @return string Shortcode output.
 */
function km_rpbt_shortcode_output( $related_posts, $rpbt_args ) {
	_deprecated_function( __FUNCTION__, '2.6.0', 'km_rpbt_get_related_posts_html()' );

	return km_rpbt_get_related_posts_html( $related_posts, $rpbt_args );
}
/**
 * Get the related posts used by the shortcode.
 *
 * @since 2.3.2
 * @deprecated 2.5.0 Use km_rpbt_get_related_posts() instead.
 *
 * @param array  $rpbt_args Widget arguments.
 * @param object $cache_obj This plugins cache object. Default null.
 * @return array Array with related post objects.
 */
function km_rpbt_shortcode_get_related_posts( $rpbt_args, $cache_obj = null ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'km_rpbt_get_related_posts()' );
	return km_rpbt_get_related_posts( $rpbt_args );
}

/**
 * Gets related posts by taxonomy.
 *
 * @since 0.1
 * @deprecated 2.5.0 Use km_rpbt_query_related_posts() instead.
 *
 * @global object       $wpdb
 *
 * @param int          $post_id    The post id to get related posts for.
 * @param array|string $taxonomies The taxonomies to retrieve related posts from.
 * @param array|string $args       Optional. Change what is returned.
 * @return array                   Empty array if no related posts found. Array with post objects.
 */
function km_rpbt_related_posts_by_taxonomy( $post_id = 0, $taxonomies = 'category', $args = '' ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'km_rpbt_query_related_posts()' );

	return km_rpbt_query_related_posts( $post_id, $taxonomies, $args );
}

/**
 * Returns default arguments.
 *
 * @since 2.1
 * @deprecated 2.5.0 Use km_rpbt_get_query_vars() instead.
 *
 * @return array Array with default arguments.
 */
function km_rpbt_get_default_args() {
	_deprecated_function( __FUNCTION__, '2.5.0', 'km_rpbt_get_query_vars()' );

	return km_rpbt_get_query_vars();
}

/**
 * Validates ids.
 * Checks if ids is a comma separated string or an array with ids.
 *
 * @since 0.2
 * @deprecated 2.5.0 Use km_rpbt_validate_ids() instead.
 *
 * @param string|array $ids Comma separated list or array with ids.
 * @return array Array with postive integers
 */
function km_rpbt_related_posts_by_taxonomy_validate_ids( $ids ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'km_rpbt_validate_ids()' );

	return km_rpbt_validate_ids( $ids );
}

/**
 * Gets the template used for display of related posts
 *
 * @since 0.1
 * @deprecated 2.5.0 Use km_rpbt_get_template() instead.
 *
 * Used by widget and shortcode
 *
 * @param string $format The format to get the template for.
 * @param string $type   Supplied by widget or shortcode.
 * @return mixed False on failure, template file path on success.
 */
function km_rpbt_related_posts_by_taxonomy_template( $format = false, $type = false ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'km_rpbt_get_template()' );

	return km_rpbt_get_template( $format, $type );
}

/**
 * Display of the related post link.
 *
 * @since 2.4.0
 * @deprecated 2.5.0 Use km_rpbt_post_link() instead.
 *
 * @param object $post       Post object.
 * @param bool   $title_attr Whether to use a title attribute in the link. Default false.
 */
function km_rpbt_post_title_link( $post, $title_attr = false ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'km_rpbt_post_link()' );

	return km_rpbt_post_link( $post, $title_attr );
}

/**
 * Get the related $post link.
 *
 * @since 2.4.0
 * @deprecated 2.5.0 Use km_rpbt_get_post_link() instead.
 *
 * @param object $post       Post object.
 * @param bool   $title_attr Whether to use a title attribute in the link. Default false.
 * @return string Related post link HTML.
 */
function km_rpbt_get_related_post_title_link( $post, $title_attr = false ) {
	_deprecated_function( __FUNCTION__, '2.5.0', 'km_rpbt_get_post_link()' );

	return km_rpbt_get_post_link( $post, $title_attr );
}

/**
 * Registers the related posts by taxonomy widget.
 *
 * @since 0.1
 * @deprecated 2.5.0 Use Related_Posts_By_Taxonomy_Plugin::widget_init() instead.
 */
function km_rpbt_related_posts_by_taxonomy_widget() {
	_deprecated_function( __FUNCTION__, '2.5.0', 'Related_Posts_By_Taxonomy_Plugin::widget_init()' );
	$widget = new Related_Posts_By_Taxonomy_Plugin();
	$widget->widget_init();
}

/**
 * Add CSS classes to (related) post objects.
 *
 * This function is used after retrieving the related posts from the database or cache.
 *
 * Use the {@see 'related_posts_by_taxonomy_post_class'} filter to add post classes on a
 * post per post basis
 *
 * @since 2.4.0
 * @deprecated 2.6.0
 *
 * @param array        $related_posts Array with (related) post objects.
 * @param array|string $args          Widget or shortcode arguments.
 *                                    See km_rpbt_get_related_posts() for more
 *                                    information on accepted arguments.
 * @return array Array with related post objects with classes added.
 */
function km_rpbt_add_post_classes( $related_posts, $args = '' ) {
	_deprecated_function( __FUNCTION__, '2.6.0' );

	if ( ! is_array( $related_posts ) ) {
		return $related_posts;
	}

	foreach ( array_values( $related_posts ) as $index => $post ) {
		if ( ! is_object( $post ) ) {
			continue;
		}

		$related_posts[ $index ]->rpbt_post_class = km_rpbt_get_post_classes( $post, $args );
	}

	return $related_posts;
}
