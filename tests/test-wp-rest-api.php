<?php
/**
 * Tests for the WordPress REST API in wp-rest-api.php
 */
class KM_RPBT_WP_REST_API extends WP_UnitTestCase {

	private $utils;
	private $posts;
	private $tax_1_terms;
	private $tax_2_terms;
	private $taxonomies = array( 'category', 'post_tag' );

	function setUp() {
		parent::setUp();
		$this->utils = new RPBT_Test_Utils( $this->factory );
		add_filter( 'related_posts_by_taxonomy_wp_rest_api', '__return_true' );
	}

	function tearDown() {
		remove_filter( 'related_posts_by_taxonomy_wp_rest_api', '__return_true' );
		remove_filter( 'related_posts_by_taxonomy_cache', array( $this->utils, 'return_bool' ) );
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
	 * Returns related posts with the WordPress REST API.
	 *
	 * @param int          $post_id    The post id to get related posts for.
	 * @param array|string $taxonomies The taxonomies to retrieve related posts from.
	 * @param array|string $args       Optional. Change what is returned.
	 * @return array|string            Empty array if no related posts found. Array with post objects, or error code returned by the request.
	 */
	function rest_related_posts_by_taxonomy( $post_id = 0, $taxonomies = 'category', $args = '' ) {

		$request = new WP_REST_Request( 'GET', '/related-posts-by-taxonomy/v1/posts/' . $post_id );
		$request->set_param( 'taxonomies', $taxonomies );
		$args    = is_array( $args ) ? $args : array( $args );
		foreach ( $args as $key => $value ) {
			$request->set_param( $key, $value );
		}

		$response = rest_do_request( $request );
		$data = $response->get_data();

		if ( isset( $data['code'] ) ) {
			return $data['code'];
		}

		return $data['posts'];
	}

	/**
	 * Tests if wp_rest_api filter is set to false (by default).
	 *
	 * @depends KM_RPBT_Functions_Tests::test_km_rpbt_plugin
	 */
	function test_wp_rest_Api_filter() {
		// Added by setUp().
		remove_filter( 'related_posts_by_taxonomy_wp_rest_api', '__return_true' );

		$plugin = km_rpbt_plugin();
		$this->assertFalse( $plugin->plugin_supports( 'wp_rest_api' ) );
	}

	/**
	 * Test if the Related_Posts_By_Taxonomy_Rest_API class is loaded
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_wp_rest_api_class_is_loaded() {
		$plugin = km_rpbt_plugin();
		global $wp_rest_server;
		$wp_rest_server = new Spy_REST_Server;
		do_action( 'rest_api_init' );
		$plugin->_setup_wp_rest_api();
		$this->assertTrue( class_exists( 'Related_Posts_By_Taxonomy_Rest_API' ) );
		$wp_rest_server = null;
	}

	/**
	 * Test related posts for post type post.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_post_type_post() {
		$this->create_posts();
		$posts = $this->posts;

		// Test with a single taxonomy.
		$taxonomies = array( 'post_tag' );
		$args       = array( 'fields' => 'ids' );

		// Test post 0.
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[2], $posts[1], $posts[3] ), $rel_post0 );

		// Test post 1.
		$rel_post1 = $this->rest_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[2], $posts[3] ), $rel_post1 );

		// Test post 2.
		$rel_post2 = $this->rest_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[3] ), $rel_post2 );

		// Test post 3.
		$rel_post3 = $this->rest_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[2] ), $rel_post3 );

		// Test with multiple taxonomies.
		$taxonomies = array( 'category', 'post_tag' );

		// Test post 0.
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[2], $posts[3] ), $rel_post0 );

		// Test post 1.
		$rel_post1 = $this->rest_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[3], $posts[2] ), $rel_post1 );

		// Test post 2.
		$rel_post2 = $this->rest_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[3] ), $rel_post2 );

		// Test post 3.
		$rel_post3 = $this->rest_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[0], $posts[2] ), $rel_post3 );

		// Test post 4.
		$rel_post4 = $this->rest_related_posts_by_taxonomy( $posts[4], $taxonomies, $args );
		$this->assertEmpty( $rel_post4 );
	}

	/**
	 * Test related posts for custom post type and custom taxonomy.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_custom_post_type_and_custom_taxonomy() {

		register_post_type( 'rel_cpt', array( 'taxonomies' => array( 'post_tag', 'rel_ctax' ) ) );
		register_taxonomy( 'rel_ctax', 'rel_cpt' );

		$this->assertFalse( is_taxonomy_hierarchical( 'rel_ctax' ) );

		$this->create_posts( 'rel_cpt', 'post_tag', 'rel_ctax' );
		$posts = $this->posts;

		$args = array( 'post_types' => array( 'rel_cpt', 'post' ), 'fields' => 'ids' );

		// Test with a single taxonomy.
		$taxonomies = array( 'rel_ctax' );

		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[1] ), $rel_post0 );

		// Test post 1.
		$rel_post1 = $this->rest_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[3] ), $rel_post1 );

		// Test post 2.
		$rel_post2 = $this->rest_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEmpty( $rel_post2 );

		// Test post 3.
		$rel_post3 = $this->rest_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1] ), $rel_post3 );

		// Test with multiple taxonomies.
		$taxonomies = array( 'rel_ctax', 'post_tag' );

		// Test post 0.
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[2], $posts[3] ), $rel_post0 );

		// Test post 1.
		$rel_post1 = $this->rest_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[3], $posts[2] ), $rel_post1 );

		// Test post 2.
		$rel_post2 = $this->rest_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[3] ), $rel_post2 );

		// Test post 3.
		$rel_post3 = $this->rest_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[0], $posts[2] ), $rel_post3 );

		// Test post 4.
		$rel_post4 = $this->rest_related_posts_by_taxonomy( $posts[4], $taxonomies, $args );
		$this->assertEmpty( $rel_post4 );
	}

	/**
	 * Test invalid function arguments.
	 *
	 *  @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 *  @requires function WP_REST_Controller::register_routes
	 */
	function test_invalid_arguments() {

		$this->create_posts();
		$posts = $this->posts;

		$args = array( 'fields' => 'ids' );

		// Test single taxonomy.
		$taxonomies = array( 'post_tag' );

		// Not a post ID.
		$fail = $this->rest_related_posts_by_taxonomy( 'not a post ID', $taxonomies, $args );
		$this->assertEquals( 'rest_no_route', $fail );

		// Non existant post ID.
		$fail2 = $this->rest_related_posts_by_taxonomy( 9999999999, $taxonomies, $args );
		$this->assertEquals( 'rest_post_invalid_id', $fail2 );

		// Non existant taxonomy.
		$fail3 = $this->rest_related_posts_by_taxonomy( $posts[0], 'not a taxonomy', $args );
		$this->assertEmpty( $fail3 );

		// Empty string should default to taxonomy 'category'.
		$fail4 = $this->rest_related_posts_by_taxonomy( $posts[0], '', $args );
		$this->assertEmpty( $fail4 );

		// No arguments should return an empty array.
		$fail5 = $this->rest_related_posts_by_taxonomy();
		$this->assertEquals( 'rest_post_invalid_id', $fail5 );
	}

	/**
	 * Test exclude_terms argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_exclude_terms() {
		$this->create_posts();
		$args       = array( 'exclude_terms' => $this->tax_1_terms[2], 'fields' => 'ids' );
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2] ), $rel_post0 );
	}

	/**
	 * Test include_terms argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_include_terms() {
		$this->create_posts();
		$args       = array( 'include_terms' => $this->tax_1_terms[0], 'fields' => 'ids' );
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[2] ), $rel_post0 );
	}

	/**
	 * Test include_terms argument when related === false.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_include_terms_unrelated() {
		$this->create_posts();
		$args = array(
			'include_terms' => array( $this->tax_2_terms[2], $this->tax_1_terms[3] ),
			'related'       => 'false',
			'fields'        => 'ids',
		);

		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[3], $this->posts[4] ), $rel_post0 );
	}


	/**
	 * Test related === false without include_terms.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_related() {
		$this->create_posts();
		$args = array(
			'related'       => 'false',
			'fields'        => 'ids',
		);
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test exclude_posts function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_exclude_posts() {
		$this->create_posts();
		$args       = array( 'exclude_posts' => $this->posts[2], 'fields' => 'ids' );
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test limit_posts function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_limit_posts() {
		$this->create_posts();
		$args      = array( 'limit_posts' => 2, 'fields' => 'ids' );
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2] ), $rel_post0 );
	}

	/**
	 * Test posts_per_page function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_posts_per_page() {
		$this->create_posts();
		$args      = array( 'posts_per_page' => 1, 'fields' => 'ids' );
		$rel_post3 = $this->rest_related_posts_by_taxonomy( $this->posts[3], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1] ), $rel_post3 );
	}

	/**
	 * Test fields function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_fields() {
		$this->create_posts();
		$_posts = get_posts( array( 'posts__in' => $this->posts, 'order' => 'post__in' ) );

		$slugs = wp_list_pluck( $_posts, 'post_name' );
		$args  = array( 'fields' => 'slugs' );

		$rel_post0 = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $slugs[1], $slugs[2], $slugs[3] ), $rel_post0 );

		$titles     = wp_list_pluck( $_posts, 'post_title' );
		$args['fields'] = 'names';

		$rel_post0 = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $titles[1], $titles[2], $titles[3] ), $rel_post0 );
	}

	/**
	 * Test post_thumbnail function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_post_thumbnail() {
		$this->create_posts();

		// Fake post thumbnails for post 1 and 3
		add_post_meta( $this->posts[1], '_thumbnail_id' , 22 ); // Fake attachment ID's.
		add_post_meta( $this->posts[3], '_thumbnail_id' , 33 );

		$args       = array( 'post_thumbnail' => true, 'fields' => 'ids' );
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test limit_month function argument.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_limit_month() {
		$this->create_posts();
		$_posts = get_posts( array( 'posts__in' => $this->posts, 'order' => 'post__in' ) );

		list( $date, $time ) = explode( ' ', $_posts[2]->post_date );
		$mypost = array(
			'ID'        => $this->posts[2],
			'post_date' => date( 'Y-m-d H:i:s', strtotime( $date . ' -6 month' ) ),
		);
		wp_update_post( $mypost );

		$args      = array( 'limit_month' => 2, 'fields' => 'ids' );
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test ascending order.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_order_asc() {
		$this->create_posts();
		$posts = $this->posts;

		$taxonomies = array( 'category', 'post_tag' );
		$args       = array( 'fields' => 'ids', 'order' => 'asc' );

		// Test post 0.
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[2], $posts[1], $posts[3] ), $rel_post0 );
	}

	/**
	 * Test unrelated ascending order.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_order_asc_non_related() {
		$this->create_posts();
		$posts = $this->posts;

		$taxonomies = array( 'category', 'post_tag' );
		// 'false' is validated as false
		$args       = array( 'fields' => 'ids', 'order' => 'asc', 'related' => 'false' );

		// Test post 0.
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[3], $posts[2], $posts[1] ), $rel_post0 );
	}

	/**
	 * Test random order of posts.
	 * Todo: Find out how to test random results and apply
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_order_rand() {
		$this->create_posts();
		$posts = $this->posts;

		$taxonomies = array( 'category', 'post_tag' );
		$args       = array( 'fields' => 'ids', 'order' => 'rand' );

		// Test post 0.
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );

		$this->assertContains( $posts[1], $rel_post0 );
		$this->assertContains( $posts[2], $rel_post0 );
		$this->assertContains( $posts[3], $rel_post0 );
		$this->assertEquals( count( $rel_post0 ), 3 );
	}

	/**
	 * Test order by post_modified.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_orderby_post_modified() {
		$this->create_posts();
		$posts = $this->posts;

		$mypost = array(
			'ID' =>  $this->posts[2],
			'post_content' => 'new content',
		);

		// Update post_modified.
		wp_update_post( $mypost );

		$taxonomies = array( 'category', 'post_tag' );
		$args       = array( 'fields' => 'ids', 'orderby' => 'post_modified' );
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );

		// Test post 0.
		$this->assertEquals( array( $posts[2], $posts[1],  $posts[3] ), $rel_post0 );
	}

}
