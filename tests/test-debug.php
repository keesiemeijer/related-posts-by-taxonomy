<?php

/**
 * Tests for debug.php
 */
class KM_RPBT_Debug_Tests extends KM_RPBT_UnitTestCase {

	/**
	 * Tests if debug filter is set to false (by default).
	 */
	function test_debug_filter() {
		add_filter( 'related_posts_by_taxonomy_debug', array( $this, 'return_first_argument' ) );

		// Setup plugin with cache activated.
		$cache = new Related_Posts_By_Taxonomy_Plugin();
		$cache->_setup();

		$this->assertFalse( $this->arg  );
		$this->arg = null;
	}
}