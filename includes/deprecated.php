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
