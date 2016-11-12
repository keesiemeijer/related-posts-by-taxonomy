<?php

/**
 * Tests for deprecated.php
 */
class KM_RPBT_Deprecated_Tests extends WP_UnitTestCase {

	/**
	 * Tests for deprecated function km_rpbt_get_shortcode_atts().
	 *
	 * @expectedDeprecated km_rpbt_get_shortcode_atts
	 */
	function test_get_shortcode_atts() {
		$atts     = km_rpbt_get_shortcode_atts();
		$expected = km_rpbt_get_default_settings( 'shortcode' );

		$this->assertEquals( $expected, $atts );
	}
}