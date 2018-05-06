<?php
/**
 * Tests for the plugin supports.
 */
class KM_RPBT_Plugin_Supports_Tests extends KM_RPBT_UnitTestCase {

	function test_default_supports() {
		$plugin = km_rpbt_plugin();

		$expected = array(
			'widget'               => true,
			'shortcode'            => true,
			'shortcode_hide_empty' => true,
			'widget_hide_empty'    => true,
			'cache'                => false,
			'display_cache_log'    => false,
			'wp_rest_api'          => false,
			'debug'                => false,
		);

		$this->assertEquals( $expected, $plugin->get_plugin_supports() );
	}

	function test_cache_support() {
		$plugin = km_rpbt_plugin();
		$this->assertFalse( $plugin->plugin_supports( 'cache' ) );
	}

	function test_display_cache_log_support() {
		$plugin = km_rpbt_plugin();
		$this->assertFalse( $plugin->plugin_supports( 'display_cache_log' ) );
	}

	function test_wp_rest_api_support() {
		$plugin = km_rpbt_plugin();
		$this->assertFalse( $plugin->plugin_supports( 'wp_rest_api' ) );
	}

	function test_debug_support() {
		$plugin = km_rpbt_plugin();
		$this->assertFalse( $plugin->plugin_supports( 'debug' ) );
	}

	function test_widget_support() {
		$plugin = km_rpbt_plugin();
		$this->assertTrue( $plugin->plugin_supports( 'widget' ) );
	}

	function test_shortcode_support() {
		$plugin = km_rpbt_plugin();
		$this->assertTrue( $plugin->plugin_supports( 'shortcode' ) );
	}

	function test_shortcode_hide_empty_support() {
		$plugin = km_rpbt_plugin();
		$this->assertTrue( $plugin->plugin_supports( 'shortcode_hide_empty' ) );
	}

	function test_widget_hide_empty_support() {
		$plugin = km_rpbt_plugin();
		$this->assertTrue( $plugin->plugin_supports( 'widget_hide_empty' ) );
	}

}
