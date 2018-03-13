<?php

/**
 * Tests for debug.php
 */
class KM_RPBT_Debug_Tests extends KM_RPBT_UnitTestCase {

	/**
	 * Tests if debug filter is set to false (by default).
	 *
	 * @depends KM_RPBT_Functions_Tests::test_km_rpbt_plugin
	 */
	function test_debug_filter() {
		add_filter( 'related_posts_by_taxonomy_debug', array( $this, 'return_first_argument' ) );

		$plugin = km_rpbt_plugin();
		$plugin->_setup();
		$this->assertFalse( $this->arg  );
		$this->arg = null;
	}
}