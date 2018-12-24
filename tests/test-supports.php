<?php
/**
 * Tests for the plugin supports.
 *
 * @group Supports
 */
class KM_RPBT_Plugin_Supports_Tests extends KM_RPBT_UnitTestCase {

	function test_default_supports() {
		$expected = array(
			'widget'               => true,
			'shortcode'            => true,
			'shortcode_hide_empty' => true,
			'widget_hide_empty'    => true,
			'cache'                => false,
			'display_cache_log'    => false,
			'wp_rest_api'          => false,
			'debug'                => false,
			'lazy_loading'         => false,
		);

		$this->assertEquals( $expected, km_rpbt_get_plugin_supports() );
	}

	function test_cache_support() {
		$this->assertFalse( km_rpbt_plugin_supports( 'cache' ) );
	}

	function test_display_cache_log_support() {
		$this->assertFalse( km_rpbt_plugin_supports( 'display_cache_log' ) );
	}

	function test_wp_rest_api_support() {
		$this->assertFalse( km_rpbt_plugin_supports( 'wp_rest_api' ) );
	}

	function test_debug_support() {
		$this->assertFalse( km_rpbt_plugin_supports( 'debug' ) );
	}

	function test_widget_support() {
		$this->assertTrue( km_rpbt_plugin_supports( 'widget' ) );
	}

	function test_shortcode_support() {
		$this->assertTrue( km_rpbt_plugin_supports( 'shortcode' ) );
	}

	function test_shortcode_hide_empty_support() {
		$this->assertTrue( km_rpbt_plugin_supports( 'shortcode_hide_empty' ) );
	}

	function test_widget_hide_empty_support() {
		$this->assertTrue( km_rpbt_plugin_supports( 'widget_hide_empty' ) );
	}

	function test_lazy_loading_support() {
		$this->assertFalse( km_rpbt_plugin_supports( 'lazy_loading' ) );
	}

}
