<?php

/**
 * Tests for uninstall.php
 */
class KM_RPBT_Uninstall_Tests extends KM_RPBT_UnitTestCase {

	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();
		delete_transient( 'rpbt_related_posts_flush_cache' );
	}

	function tearDown() {
		remove_filter( 'related_posts_by_taxonomy_cache', '__return_true' );
	}

	/**
	 * Tests if debug filter is set to false (by default).
	 *
	 * @depends KM_RPBT_Functions_Tests::test_km_rpbt_plugin
	 */
	function test_uninstall() {
		global $wpdb;
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		add_filter( 'related_posts_by_taxonomy_cache', '__return_true' );
		$plugin = km_rpbt_plugin();

		// Setup (again) with cache layer activated.
		$plugin->_setup();

		// Transient should be set.
		$transient = get_transient( 'rpbt_related_posts_flush_cache' );
		$this->assertSame( 1, $transient );

		// Cache should be empty.
		$this->assertEmpty( $this->get_cache_meta_key() );

		// Cache related posts.
		$args = array( 'fields' => 'ids' );
		$taxonomies = array( 'post_tag' );
		$related_posts = km_rpbt_cache_related_posts( $posts[1], $taxonomies, $args );

		// Test if cached related posts exists.
		$this->assertNotEmpty( $this->get_cache_meta_key() );

		define( 'WP_UNINSTALL_PLUGIN', RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'related-posts-by-taxonomy.php' );
		include RELATED_POSTS_BY_TAXONOMY_PLUGIN_DIR . 'uninstall.php';

		$transient = get_transient( 'rpbt_related_posts_flush_cache' );

		// Transient should be deleted.
		$this->assertFalse( $transient );

		// Cache should be empty.
		$this->assertEmpty( $this->get_cache_meta_key() );
	}
}
