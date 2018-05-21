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
 * Get the related posts used by the shortcode.
 *
 * @since 2.3.2
 * @since 2.5.0 Deprecated
 *
 * @param array  $rpbt_args Widget arguments.
 * @param object $cache_obj This plugins cache object. Default null.
 * @return array Array with related post objects.
 */
function km_rpbt_shortcode_get_related_posts( $rpbt_args, $cache_obj = null ) {
	_deprecated_function( __FUNCTION__, '2.4.0', 'km_rpbt_get_related_posts()' );
	return km_rpbt_get_related_posts( $rpbt_args );
}

/**
 * Gets related posts by taxonomy.
 *
 * @since 0.1
 * @since 2.5.0 Deprecated
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
 * @since 2.5.0 Deprecated
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
 * @since 2.5.0 Deprecated
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
 * @since 2.5.0 Deprecated
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
 * @since 2.5.0 Deprecated
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
 * @since 2.5.0 Deprecated
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
 * @since 2.5.0 Deprecated
 */
function km_rpbt_related_posts_by_taxonomy_widget() {
	_deprecated_function( __FUNCTION__, '2.5.0', 'Related_Posts_By_Taxonomy_Plugin::widget_init()' );
	$widget = new Related_Posts_By_Taxonomy_Plugin();
	$widget->widget_init();
}
