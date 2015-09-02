<?php
/**
 * Tests for the km_rpbt_related_posts_by_taxonomy() function in functions.php.
 */
class KM_RPBT_Functions_Tests extends WP_UnitTestCase {

	private $utils;
	private $posts;
	private $tax_1_terms;
	private $tax_2_terms;
	private $taxonomies = array( 'category', 'post_tag' );

	function setUp() {
		parent::setUp();
		$this->utils = new RPBT_Test_Utils( $this->factory );
	}


	/**
	 * Helper function to create 5 posts with 5 terms from two taxonomies.
	 */
	function create_posts( $post_type = 'post', $tax1 = 'post_tag', $tax2 = 'category' ) {
		$posts = $this->utils->create_posts_with_terms( $post_type, $tax1, $tax2 );
		$this->posts       = $posts['posts'];
		$this->tax_1_terms = $posts['tax1_terms'];
		$this->tax_2_terms = $posts['tax2_terms'];
	}

	/**
	 * Test if km_rpbt_plugin() returns an object.
	 *
	 * Used in the km_rpbt_related_posts_by_taxonomy() function to replace 'post_type = 'post' with 'post_type IN ( ... )
	 */
	function test_km_rpbt_plugin() {
		$plugin = km_rpbt_plugin();
		$this->assertTrue( $plugin instanceof Related_Posts_By_Taxonomy_Defaults );
	}


	/**
	 * test related posts for post type post
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_post_type_post() {
		$this->create_posts();
		$posts = $this->posts;

		// Test with a single taxonomy.
		$taxonomies = array( 'post_tag' );
		$args       = array( 'fields' => 'ids' );

		// test post 0
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[2], $posts[1], $posts[3] ), $rel_post0 );

		// test post 1
		$rel_post1 = km_rpbt_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[2], $posts[3] ), $rel_post1 );

		// test post 2
		$rel_post2 = km_rpbt_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[3] ), $rel_post2 );

		// test post 3
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[2] ), $rel_post3 );

		// Test with multiple taxonomies.
		$taxonomies = array( 'category', 'post_tag' );

		// test post 0
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[2], $posts[3] ), $rel_post0 );

		// test post 1
		$rel_post1 = km_rpbt_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[3], $posts[2] ), $rel_post1 );

		// test post 2
		$rel_post2 = km_rpbt_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[3] ), $rel_post2 );

		// test post 3
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[0], $posts[2] ), $rel_post3 );

		// test post 4
		$rel_post4 = km_rpbt_related_posts_by_taxonomy( $posts[4], $taxonomies, $args );
		$this->assertEmpty( $rel_post4 );
	}


	/**
	 * test related posts for custom post type and custom taxonomy.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_custom_post_type_and_custom_taxonomy() {

		register_post_type( 'rel_cpt', array( 'taxonomies' => array( 'post_tag', 'rel_ctax' ) ) );
		register_taxonomy( 'rel_ctax', 'rel_cpt' );

		$this->assertFalse( is_taxonomy_hierarchical( 'rel_ctax' ) );

		$this->create_posts( 'rel_cpt', 'post_tag', 'rel_ctax' );
		$posts = $this->posts;

		$args =  array( 'post_types' => array( 'rel_cpt', 'post' ), 'fields' => 'ids', );

		// Test with a single taxonomy.
		$taxonomies = array( 'rel_ctax' );

		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[1] ), $rel_post0 );

		// test post 1
		$rel_post1 = km_rpbt_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[3] ), $rel_post1 );

		// test post 2
		$rel_post2 = km_rpbt_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEmpty( $rel_post2 );

		// test post 3
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1] ), $rel_post3 );

		// Test with multiple taxonomies.
		$taxonomies = array( 'rel_ctax', 'post_tag' );

		// test post 0
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[2], $posts[3] ), $rel_post0 );

		// test post 1
		$rel_post1 = km_rpbt_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[3], $posts[2] ), $rel_post1 );

		// test post 2
		$rel_post2 = km_rpbt_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[3] ), $rel_post2 );

		// test post 3
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[0], $posts[2] ), $rel_post3 );

		// test post 4
		$rel_post4 = km_rpbt_related_posts_by_taxonomy( $posts[4], $taxonomies, $args );
		$this->assertEmpty( $rel_post4 );
	}


	/**
	 * Test invalid function arguments.
	 *  @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_invalid_arguments() {

		$this->create_posts();
		$posts = $this->posts;

		$args =  array( 'fields' => 'ids' );

		// Test single taxonomy.
		$taxonomies = array( 'post_tag' );

		// Not a post ID.
		$fail = km_rpbt_related_posts_by_taxonomy( 'not a post ID', $taxonomies, $args );
		$this->assertEmpty( $fail );

		// Non existant post ID.
		$fail2 = km_rpbt_related_posts_by_taxonomy( 9999999999, $taxonomies, $args );
		$this->assertEmpty( $fail2 );

		// Non existant taxonomy.
		$fail3 = km_rpbt_related_posts_by_taxonomy( $posts[0], 'not a taxonomy', $args );
		$this->assertEmpty( $fail3 );

		// Empty string should default to taxonomy 'category'.
		$fail4 = km_rpbt_related_posts_by_taxonomy( $posts[0], '', $args );
		$this->assertEquals( array( $posts[1] ), $fail4 );

		// No arguments should return an empty array.
		$fail5 = km_rpbt_related_posts_by_taxonomy();
		$this->assertEmpty( $fail5 );
	}


	/**
	 * Test exclude_terms argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_exclude_terms() {
		$this->create_posts();
		$args       = array( 'exclude_terms' => $this->tax_1_terms[2], 'fields' => 'ids' );
		$rel_post0  = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2] ), $rel_post0 );
	}


	/**
	 * Test include_terms argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_include_terms() {
		$this->create_posts();
		$args       = array( 'include_terms' => $this->tax_1_terms[0], 'fields' => 'ids' );
		$rel_post0  = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[2] ), $rel_post0 );
	}


	/**
	 * Test include_terms argument when related === false.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_include_terms_unrelated() {
		$this->create_posts();
		$args = array(
			'include_terms' => array(  $this->tax_2_terms[2], $this->tax_1_terms[3] ),
			'related'       => false,
			'fields'        => 'ids',
		);
		$rel_post0  = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[3], $this->posts[4] ), $rel_post0 );
	}


	/**
	 * Test related === false without include_terms.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_related() {
		$this->create_posts();
		$args = array(
			'related'       => false,
			'fields'        => 'ids',
		);
		$rel_post0  = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2], $this->posts[3] ), $rel_post0 );
	}


	/**
	 * Test exclude_posts function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_exclude_posts() {
		$this->create_posts();
		$args       = array( 'exclude_posts' => $this->posts[2], 'fields' => 'ids' );
		$rel_post0  = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}


	/**
	 * Test limit_posts function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_limit_posts() {
		$this->create_posts();
		$args      = array( 'limit_posts' => 2, 'fields' => 'ids' );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2] ), $rel_post0 );
	}


	/**
	 * Test posts_per_page function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_posts_per_page() {
		$this->create_posts();
		$args      = array( 'posts_per_page' => 1, 'fields' => 'ids' );
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $this->posts[3], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1] ), $rel_post3 );
	}


	/**
	 * Test fields function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_fields() {
		$this->create_posts();
		$_posts = get_posts( array( 'posts__in' => $this->posts, 'order' => 'post__in' ) );

		$slugs      = wp_list_pluck( $_posts, 'post_name' );
		$args       = array( 'fields' => 'slugs' );

		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $slugs[1], $slugs[2], $slugs[3] ), $rel_post0 );

		$titles     = wp_list_pluck( $_posts, 'post_title' );
		$args['fields'] = 'names';

		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $titles[1], $titles[2], $titles[3] ), $rel_post0 );
	}


	/**
	 * Test post_thumbnail function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_post_thumbnail() {
		$this->create_posts();

		// Fake post thumbnails for post 1 and 3
		add_post_meta( $this->posts[1], '_thumbnail_id' , 22 ); // fake attachment ID's
		add_post_meta( $this->posts[3], '_thumbnail_id' , 33 );

		$args       = array( 'post_thumbnail' => true, 'fields' => 'ids' );
		$rel_post0  = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}


	/**
	 * Test limit_month function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_limit_month() {
		$this->create_posts();
		$_posts = get_posts( array( 'posts__in' => $this->posts, 'order' => 'post__in' ) );

		list( $date, $time ) = explode( ' ', $_posts[2]->post_date );
		$mypost = array(
			'ID' =>  $this->posts[2],
			'post_date' => date( 'Y-m-d H:i:s', strtotime( $date .' -6 month' ) ),
		);
		wp_update_post( $mypost );

		$args       = array( 'limit_month' => 2, 'fields' => 'ids' );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

}