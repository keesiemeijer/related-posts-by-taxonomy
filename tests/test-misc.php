<?php
/**
 * Tests for dependencies and various plugin functions
 */
class KM_RPBT_Misc_Tests extends WP_UnitTestCase {

	/**
	 * Utils object to create posts with terms to test with.
	 *
	 * @var object
	 */
	private $utils;

	private $boolean;


	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// Use the utils class to create posts with terms
		$this->utils = new RPBT_Test_Utils( $this->factory );
	}


	/**
	 * Test if posts are created with the factory class.
	 */
	function test_create_posts() {
		$posts = $this->utils->create_posts();
		$this->assertNotEmpty( $posts );
		return $posts;
	}


	/**
	 * Test if terms are created with the factory class.
	 *
	 * @depends test_create_posts
	 */
	function test_assign_taxonomy_terms( array $posts ) {
		$this->assertNotEmpty( $posts );
		$terms = $this->utils->assign_taxonomy_terms( $posts, 'category', 1 );
		$this->assertNotEmpty( $terms );
	}


	/**
	 * Test if posts and terms are created with the factory class.
	 *
	 * Other test methods that create posts depend on this function to succeed.
	 */
	function test_create_posts_with_terms() {
		$create_posts = $this->utils->create_posts_with_terms();
		//$create_posts = array();
		$this->assertNotEmpty( $create_posts );
		$this->assertCount( 5, $create_posts['posts'] );
		$this->assertCount( 5, $create_posts['tax1_terms'] );
		$this->assertCount( 5, $create_posts['tax2_terms'] );
	}


	/**
	 * Skip tests if the constant KM_RPBT_TEST_OUTPUT is set to false.
	 */
	function test_skip_output_tests() {
		if ( defined( 'KM_RPBT_TEST_OUTPUT' ) && !KM_RPBT_TEST_OUTPUT ) {
			$this->markTestSkipped();
		}
	}


	/**
	 * Test if default WordPress taxonomies exist.
	 */
	function test_get_post_taxonomies() {
		$this->assertEquals( array( 'category', 'post_tag', 'post_format' ), get_object_taxonomies( 'post' ) );
	}


	/**
	 * Test output from WordPress function get_posts_by_author_sql().
	 *
	 * Used in the km_rpbt_related_posts_by_taxonomy() function to replace 'post_type = 'post' with 'post_type IN ( ... )
	 */
	function test_get_posts_by_author_sql() {
		$where  = get_posts_by_author_sql( 'post' );
		$this->assertTrue( (bool) preg_match( "/post_type = 'post'/", $where ) );
	}


	/**
	 * Tests if debug filter is set to false (by default).
	 */
	function test_debug_filter() {
		add_filter( 'related_posts_by_taxonomy_debug', array( $this->utils, 'return_bool' ) );

		$plugin_defaults = Related_Posts_By_Taxonomy_Defaults::get_instance();
		$plugin_defaults->_setup();
		$this->assertFalse( $this->utils->boolean  );
		$this->utils->boolean = null;
	}


	/**
	 * Tests for functions that should not output anything.
	 *
	 * @depends test_create_posts_with_terms
	 * @depends test_skip_output_tests
	 */
	function test_empty_output() {

		$create_posts = $this->utils->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		$args       =  array( 'fields' => 'ids' );
		$taxonomies = array( 'category', 'post_tag' );

		ob_start();

		// these functions should not output anything.
		$_posts     = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$_template  = km_rpbt_related_posts_by_taxonomy_template( 'excerpts' );
		$_ids       = km_rpbt_related_posts_by_taxonomy_validate_ids( '1,2,1,string' );
		$_shortcode = km_rpbt_related_posts_by_taxonomy_shortcode( array( 'post_id' => $posts[0] ) );

		// The shortcode and thumbnail gallery have other tests for output in test-shortcode.php

		$out = ob_get_clean();

		$this->assertEmpty( $out );
	}


	/**
	 * Test if array with validated ids are returned.
	 */
	function test_km_rpbt_related_posts_by_taxonomy_validate_ids() {

		$ids = array( 1, false, 'string', 2, 0, 1, 3 );

		$validated_ids = km_rpbt_related_posts_by_taxonomy_validate_ids( $ids );
		$this->assertEquals( array( 1, 2, 3 ), $validated_ids );

		$ids = '1,string,2,0,###,2,3';
		$validated_ids = km_rpbt_related_posts_by_taxonomy_validate_ids( $ids );
		$this->assertEquals( array( 1, 2, 3 ), $validated_ids );
	}


	/**
	 * Test if correct template was found.
	 */
	function test_km_rpbt_related_posts_by_taxonomy_template() {

		$path = pathinfo( dirname(  __FILE__  ) );

		// get the excerpts template
		$template = km_rpbt_related_posts_by_taxonomy_template( 'excerpts' );
		$path1 = $path['dirname'] . '/templates/related-posts-excerpts.php';
		$this->assertEquals( $path1 , $template );

		// If no template is provided it should default to the links template.
		$template = km_rpbt_related_posts_by_taxonomy_template();
		$path2 = $path['dirname'] . '/templates/related-posts-links.php';
		$this->assertEquals( $path2 , $template );

		// Wrong templates should default to links template.
		$template = km_rpbt_related_posts_by_taxonomy_template( 'not-a-template' );
		$path3 = $path['dirname'] . '/templates/related-posts-links.php';
		$this->assertEquals( $path3 , $template );
	}
}
