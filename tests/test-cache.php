<?php
/**
 * Tests for the widget in /includes/widget.php
 */
class KM_RPBT_Cache_Tests extends WP_UnitTestCase {

	/**
	 * Utils object to create posts with terms.
	 *
	 * @var object
	 */
	private $utils;

	public $args = null;


	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// Use the utils class to create posts with terms.
		$this->utils = new RPBT_Test_Utils( $this->factory );
	}


	/**
	 * Test cache.
	 */
	function test_cache_with_shortcode_in_post_content() {
		global $wpdb;

		// Activate cache
		add_filter( 'related_posts_by_taxonomy_cache', '__return_true' );

		$plugin_defaults = Related_Posts_By_Taxonomy_Defaults::get_instance();
		$plugin_defaults->_setup();

		$create_posts = $this->utils->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// Add a shortcode to cache.
		wp_update_post( array(
				'ID'          => $posts[0],
				'post_content' => '[related_posts_by_tax]',
			)
		);

		// Go to the single post page
		$this->go_to( get_permalink( $posts[0] ) );

		// Check if cache class exists.
		$cache = class_exists( 'Related_Posts_By_Taxonomy_Cache' );
		$this->assertTrue( $cache  );

		$cache_query = "SELECT $wpdb->postmeta.meta_key FROM $wpdb->postmeta WHERE meta_key LIKE '_rpbt_related_posts%'";

		$meta = $wpdb->get_var( $cache_query );

		// Cache should be empty.
		$this->assertEmpty( $meta  );

		// Trigger cache.
		ob_start();
		the_post();
		the_content();
		$content = ob_get_clean();

		$meta_key = $wpdb->get_var( $cache_query );

		// Cache should be set for the shortcode in $post[0] content.
		$this->assertNotEmpty( $meta_key );

		// Get related post ids for $post[0] from cache.
		// This also tests if the cache is set for the right post ID
		$cache_ids = get_post_meta( $posts[0], $meta_key, true );
		unset( $cache_ids['rpbt_current'] );
		$cache_ids = array_keys( $cache_ids );
		$this->assertNotEmpty( $cache_ids );

		// Get related post ids with function.
		$args = array( 'fields' => 'ids' );
		$taxonomies = array_keys( $plugin_defaults->taxonomies );
		$related = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );

		$this->assertEquals( $cache_ids, $related );
	}

}