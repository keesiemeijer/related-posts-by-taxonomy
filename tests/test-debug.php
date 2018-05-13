<?php

/**
 * Tests for debug.php
 */
class KM_RPBT_Debug_Tests extends KM_RPBT_UnitTestCase {

	/**
	 * Tests if debug filter is set to false (by default).
	 */
	function test_debug_filter_false() {
		add_filter( 'related_posts_by_taxonomy_debug', array( $this, 'return_first_argument' ) );

		// Setup plugin.
		$cache = new Related_Posts_By_Taxonomy_Plugin();
		$cache->debug_init();

		$this->assertFalse( $this->arg  );
		$this->arg = null;
	}

	/**
	 * Tests if debug filter is set to true.
	 */
	function test_debug_true() {
		$this->assertFalse( class_exists( 'Related_Posts_By_Taxonomy_Debug' )  );

		add_filter( 'related_posts_by_taxonomy_debug', '__return_true' );

		// Setup plugin with debug activated.
		$cache = new Related_Posts_By_Taxonomy_Plugin();
		$cache->debug_init();

		$this->assertTrue( class_exists( 'Related_Posts_By_Taxonomy_Debug' )  );
	}
}