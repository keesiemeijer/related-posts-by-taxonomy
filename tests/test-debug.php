<?php
/**
 * Tests for debug.php
 *
 * @group Debug
 */
class KM_RPBT_Debug_Tests extends KM_RPBT_UnitTestCase {

	public $debug;

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
		$this->assertFalse( class_exists( 'Related_Posts_By_Taxonomy_Debug', false )  );

		add_filter( 'related_posts_by_taxonomy_debug', '__return_true' );

		// Setup plugin with debug activated.
		$cache = new Related_Posts_By_Taxonomy_Plugin();
		$cache->debug_init();

		$this->assertTrue( class_exists( 'Related_Posts_By_Taxonomy_Debug', false )  );
	}

	function setup_debug() {
		add_filter( 'related_posts_by_taxonomy_debug', '__return_true' );
		$user_id = $this->factory->user->create(
			array(
				'role' => 'administrator',
			)
		);

		if( is_multisite() ) {
			grant_super_admin( $user_id );
		}

		wp_set_current_user( $user_id );

		// Setup plugin with debug activated.
		$this->debug = new Related_Posts_By_Taxonomy_Debug();
	}

	/**
	 * Tests debug results
	 */
	function test_debug_terms() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];
		$terms        = $create_posts['tax1_terms'];

		$this->setup_debug();

		// Do shortcode to get debug results
		do_shortcode( "[related_posts_by_tax post_id='{$posts[0]}' taxonomies='post_tag']" );
		$debug = $this->debug->results[0];

		$this->assertSame( 'post_tag', $debug['taxonomies used for query'] );

		$term_names = get_terms( array( 'taxonomy' => 'post_tag', 'fields' => 'names' ) );
		$term_names = implode( ', ', array( $term_names[0], $term_names[1], $term_names[2] ) );

		$terms_used = $debug['terms used for query'];
		$this->assertSame( $term_names, $terms_used );
	}

	function test_debug_query() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];
		$terms        = $create_posts['tax1_terms'];

		$this->setup_debug();

		// Do shortcode to get debug results
		do_shortcode( "[related_posts_by_tax post_id='{$posts[0]}' taxonomies='post_tag']" );
		$debug = $this->debug->results[0];

		global $wpdb;
		$this->assertNotEmpty($wpdb->prefix);

		// Prefix should not be in query.
		$query = $debug['related posts query'];
		$this->assertTrue( false === strrpos( $query, $wpdb->prefix ) );

		$sql_terms = "AND ( tt.term_id IN ({$terms[0]}, {$terms[1]}, {$terms[2]}) )";
		$this->assertTrue( false !== strrpos( $query, $sql_terms ) );
	}

	function test_debug_posts_found(){
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		$this->setup_debug();

		// Do shortcode to get debug results
		do_shortcode( "[related_posts_by_tax post_id='{$posts[0]}' taxonomies='post_tag']" );
		$debug = $this->debug->results[0];

		$this->assertSame( $posts[0], $debug['current post id'] );

		$found = $debug['related post ids found'];
		$this->assertEquals( implode( ', ', array( $posts[2], $posts[1], $posts[3] ) ), $found );
	}

	function test_debug_link(){
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		$this->setup_debug();

		// Do shortcode to get debug results
		do_shortcode( "[related_posts_by_tax post_id='{$posts[0]}' taxonomies='post_tag']" );
		$debug = $this->debug->results[0];

		$debug_link = '(<a href="#rpbt-shortcode-debug-1">Debug Shortcode</a>)';

		$this->assertSame( $debug_link, $debug['debug_link'] );
	}
}
