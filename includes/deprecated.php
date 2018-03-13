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
 * @since 2.4.2 Deprecated
 *
 * @param array  $rpbt_args Widget arguments.
 * @param object $cache_obj This plugins cache object. Default null.
 * @return array Array with related post objects.
 */
function km_rpbt_shortcode_get_related_posts( $rpbt_args, $cache_obj = null ) {
	_deprecated_function( __FUNCTION__, '2.4.0', 'km_rpbt_get_related_posts()' );
	return km_rpbt_get_related_posts( $rpbt_args );
}
