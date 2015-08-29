<?php

/**
 * Tests for debug.php
 */
class KM_RPBT_Debug_Tests extends WP_UnitTestCase {

	/**
	 * Utils object to create posts with terms.
	 *
	 * @var object
	 */
	private $utils;


	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// Use the utils class to create posts with terms.
		$this->utils = new RPBT_Test_Utils( $this->factory );
	}


	/**
	 * Tests if debug filter is set to false (by default).
	 *
	 * @depends KM_RPBT_Functions_Tests::test_km_rpbt_plugin
	 */
	function test_debug_filter() {
		add_filter( 'related_posts_by_taxonomy_debug', array( $this->utils, 'return_bool' ) );

		$plugin = km_rpbt_plugin();
		$plugin->_setup();
		$this->assertFalse( $this->utils->boolean  );
		$this->utils->boolean = null;
	}
}