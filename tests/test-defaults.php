<?php
/**
 * Tests for class-defaults.php
 *
 * @group Defaults
 */
class KM_RPBT_Defaults_Tests extends KM_RPBT_UnitTestCase {

	function tearDown() {
		unregister_taxonomy_for_object_type( 'category', 'page' );
		unregister_taxonomy_for_object_type( 'ctax', 'cpt' );

		// Reset plugin instance
		$plugin = km_rpbt_plugin();
		$plugin->_setup();
	}

	function test_default_post_type() {
		$plugin = km_rpbt_plugin();
		$plugin->_setup();

		$this->assertSame( array( 'post' ), array_keys( $plugin->post_types ) );
	}

	function test_default_taxonomies() {
		$plugin = km_rpbt_plugin();
		$plugin->_setup();

		$taxonomies = array_keys( $plugin->taxonomies );
		$expected = array( 'category', 'post_tag', 'post_format' );
		sort( $taxonomies );
		sort( $expected );
		$this->assertSame( $expected, $taxonomies );
	}

	function test_post_types() {
		$plugin = km_rpbt_plugin();
		$plugin->_setup();

		$this->assertTrue( ! array_key_exists( 'page', $plugin->post_types ) );
	}

	function test_post_type_page_taxonomies() {
		$registered = register_taxonomy_for_object_type( 'category', 'page' );
		$this->assertTrue( $registered );

		$plugin = km_rpbt_plugin();
		$plugin->_setup();

		$this->assertTrue( array_key_exists( 'page', $plugin->post_types ) );
	}

	function test_post_types_taxonomies() {
		$plugin = km_rpbt_plugin();

		register_post_type( 'cpt', array( 'public' => true ) );

		// Custom post type cpt does not have taxonomies
		$this->assertSame( array( 'post' ), array_keys( $plugin->post_types ) );

		register_taxonomy( 'ctax', 'cpt' );
		$plugin->_setup();

		// Now the custom post type cpt has taxonomies
		$this->assertSame( array( 'post', 'cpt' ), array_keys( $plugin->post_types ) );
	}
}
