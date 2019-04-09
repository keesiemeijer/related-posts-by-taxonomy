<?php
/**
 * Tests for the WordPress REST API in wp-rest-api.php
 *
 * @group Rest_API
 */
class KM_RPBT_WP_REST_API extends KM_RPBT_UnitTestCase {

	private $posts;
	private $tax_1_terms;
	private $tax_2_terms;
	private $taxonomies = array( 'category', 'post_tag' );

	function setUp() {
		parent::setUp();
		add_filter( 'related_posts_by_taxonomy_wp_rest_api', '__return_true' );
	}

	function tearDown() {
		remove_filter( 'related_posts_by_taxonomy_wp_rest_api', '__return_true' );
		remove_filter( 'related_posts_by_taxonomy', array( $this, 'return_query_args' ), 10, 4 );
		remove_filter( 'related_posts_by_taxonomy_cache', array( $this, 'return_first_argument' ) );
		remove_filter( 'related_posts_by_taxonomy_wp_rest_api_args', array( $this, 'return_first_argument' ) );
		remove_filter( 'related_posts_by_taxonomy_posts_meta_query', array( $this, 'return_first_argument' ) );
		remove_filter( 'related_posts_by_taxonomy_posts_meta_query', array( $this, 'meta_query_callback' ), 10, 4 );
	}

	/**
	 * Helper function to create 5 posts with 5 terms from two taxonomies.
	 */
	function setup_posts( $post_type = 'post', $tax1 = 'post_tag', $tax2 = 'category' ) {
		$posts = $this->create_posts_with_terms( $post_type, $tax1, $tax2 );
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
	function rest_related_posts_by_taxonomy( $post_id = 0, $taxonomies = '', $args = '' ) {

		$request = new WP_REST_Request( 'GET', '/related-posts-by-taxonomy/v1/posts/' . $post_id );
		if ( $taxonomies ) {
			$request->set_param( 'taxonomies', $taxonomies );
		}
		$args    = is_array( $args ) ? $args : array( $args );
		foreach ( $args as $key => $value ) {
			$request->set_param( $key, $value );
		}

		$response = rest_do_request( $request );
		$data     = $response->get_data();

		if ( isset( $data['code'] ) ) {
			return $data['code'];
		}

		return $data['posts'];
	}

	/**
	 * Tests if wp_rest_api filter is set to false (by default).
	 */
	function test_wp_rest_Api_filter() {
		// Added by setUp().
		remove_filter( 'related_posts_by_taxonomy_wp_rest_api', '__return_true' );

		$plugin = km_rpbt_plugin();
		$this->assertFalse( km_rpbt_plugin_supports( 'wp_rest_api' ) );
	}

	/**
	 * Tests if wp_rest_api filter is set to false (by default).
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_wp_rest_Api_not_registered_route() {
		// Added by setUp().
		remove_filter( 'related_posts_by_taxonomy_wp_rest_api', '__return_true' );

		$this->setup_posts();
		$posts = $this->posts;

		$request = new WP_REST_Request( 'GET', '/related-posts-by-taxonomy/v1/posts/' . $posts[0] );

		$response = rest_do_request( $request );
		$data     = $response->get_data();

		$this->assertTrue( array_key_exists( 'code', $data ) );
		$this->assertSame( "rest_no_route", $data['code'] );
	}

	/**
	 * Test if the Related_Posts_By_Taxonomy_Rest_API class is loaded
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_wp_rest_api_class_is_loaded() {
		$plugin = new Related_Posts_By_Taxonomy_Plugin();
		global $wp_rest_server;
		$wp_rest_server = new Spy_REST_Server;
		do_action( 'rest_api_init' );
		$plugin->rest_api_init();
		$this->assertTrue( class_exists( 'Related_Posts_By_Taxonomy_Rest_API' ) );
		$wp_rest_server = null;
	}

	/**
	 * Test the route is registered
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_wp_rest_api_route_is_registered() {
		// Setup plugin with cache activated.
		$plugin = new Related_Posts_By_Taxonomy_Plugin();
		global $wp_rest_server;
		$wp_rest_server = new Spy_REST_Server;
		do_action( 'rest_api_init' );
		$plugin->rest_api_init();
		$this->assertTrue( in_array( '/related-posts-by-taxonomy/v1', array_keys( $wp_rest_server->get_routes() ) ) );
		$wp_rest_server = null;
	}

	/**
	 * Test success response for rest request.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_wp_rest_api_success_response() {
		$this->setup_posts();
		$posts = $this->posts;

		$request = new WP_REST_Request( 'GET', '/related-posts-by-taxonomy/v1/posts/' . $posts[0] );
		$request->set_param( 'fields', 'ids' );

		$response = rest_do_request( $request );
		$data     = $response->get_data();
		$expected = array(
			'posts',
			'termcount',
			'post_id',
			'post_types',
			'taxonomies',
			'related_terms',
			'rendered',
		);

		$data = array_keys( $data );

		sort( $expected );
		sort( $data );

		$this->assertEquals( $expected, $data );
	}

	/**
	 * Test success response for rest request.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_wp_rest_api_success_response_rendered() {
		$this->setup_posts();
		$posts = $this->posts;

		// get post ids array and permalinks array
		$_posts     = get_posts(
			array(
				'posts__in' => $this->posts,
				'order' => 'post__in',
			)
		);

		$permalinks = array_map( 'get_permalink', $this->posts );

		$request  = new WP_REST_Request( 'GET', '/related-posts-by-taxonomy/v1/posts/' . $posts[0] );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		$expected = array( $this->posts[1], $this->posts[2], $this->posts[3] );
		$post_ids = wp_list_pluck( $data['posts'], 'ID' );
		$this->assertEquals( $expected, $post_ids );

		$expected = <<<EOF
<div class="rpbt_wp_rest_api">
<h3>Related Posts</h3>
<ul>
<li>
<a href="{$permalinks[1]}">{$_posts[1]->post_title}</a>
</li>
<li>
<a href="{$permalinks[2]}">{$_posts[2]->post_title}</a>
</li>
<li>
<a href="{$permalinks[3]}">{$_posts[3]->post_title}</a>
</li>
</ul>
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $data['rendered'] ) );
	}

	/**
	 * Test success response for rest request.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_wp_rest_api_success_response_rendered_field_ids() {
		$this->setup_posts();
		$posts = $this->posts;

		// get post ids array and permalinks array
		$_posts     = get_posts(
			array(
				'posts__in' => $this->posts,
				'order' => 'post__in',
			)
		);
		$permalinks = array_map( 'get_permalink', $this->posts );

		$request = new WP_REST_Request( 'GET', '/related-posts-by-taxonomy/v1/posts/' . $posts[0] );
		$request->set_param( 'fields', 'ids' );
		$response = rest_do_request( $request );
		$data       = $response->get_data();

		$this->assertEquals( array( $this->posts[1], $this->posts[2], $this->posts[3] ), $data['posts'] );

		$expected = <<<EOF
<div class="rpbt_wp_rest_api">
<h3>Related Posts</h3>
<ul>
<li>
<a href="{$permalinks[1]}">{$_posts[1]->post_title}</a>
</li>
<li>
<a href="{$permalinks[2]}">{$_posts[2]->post_title}</a>
</li>
<li>
<a href="{$permalinks[3]}">{$_posts[3]->post_title}</a>
</li>
</ul>
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $data['rendered'] ) );
	}

	/**
	 * Test success response for rest request.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_wp_rest_api_success_response_rendered_field_names() {
		$this->setup_posts();
		$posts = $this->posts;

		// get post ids array and permalinks array
		$_posts     = get_posts(
			array(
				'posts__in' => $this->posts,
				'order' => 'post__in',
			)
		);

		$post_names = wp_list_pluck( $_posts, 'post_title' );

		$request = new WP_REST_Request( 'GET', '/related-posts-by-taxonomy/v1/posts/' . $posts[0] );
		$request->set_param( 'fields', 'names' );
		$response = rest_do_request( $request );
		$data     = $response->get_data();

		$this->assertEquals( array( $post_names[1], $post_names[2], $post_names[3] ), $data['posts'] );

		// Not rendered if fields is names
		$this->assertSame( '', $data['rendered'] );
	}

	/**
	 * Test type of request.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_wp_rest_api_default_type_request() {

		$this->setup_posts();
		$posts = $this->posts;
		add_filter( 'related_posts_by_taxonomy_wp_rest_api_args', array( $this, 'return_first_argument' ) );
		$request = new WP_REST_Request( 'GET', '/related-posts-by-taxonomy/v1/posts/' . $posts[0] );
		$request->set_param( 'fields', 'ids' );
		$response = rest_do_request( $request );

		// Default response without a type.
		$this->assertEquals( 'wp_rest_api', $this->arg['type'] );
		$this->arg = null;

		// Invalid type.
		$request->set_param( 'type', 'lala' );
		$response = rest_do_request( $request );
		$this->assertEquals( 'wp_rest_api', $this->arg['type'] );
		$this->arg = null;
	}

	/**
	 * Test related posts for post type post.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_post_type_post() {
		$this->setup_posts();
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
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_custom_post_type_and_custom_taxonomy() {

		register_post_type( 'rel_cpt', array( 'taxonomies' => array( 'post_tag', 'rel_ctax' ) ) );
		register_taxonomy( 'rel_ctax', 'rel_cpt' );

		$this->assertFalse( is_taxonomy_hierarchical( 'rel_ctax' ) );

		$this->setup_posts( 'rel_cpt', 'post_tag', 'rel_ctax' );
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
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_with_no_taxonomies() {
		register_post_type( 'rel_cpt', array( 'taxonomies' => array( 'post_tag', 'rel_ctax' ) ) );
		register_taxonomy( 'rel_ctax', 'rel_cpt' );

		$this->assertFalse( is_taxonomy_hierarchical( 'rel_ctax' ) );

		$this->setup_posts( 'rel_cpt', 'post_tag', 'rel_ctax' );
		$posts = $this->posts;

		$args = array( 'post_types' => array( 'rel_cpt', 'post' ), 'fields' => 'ids' );
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], '', $args );

		// Should default to query in all taxonomies
		$this->assertEquals( array( $posts[2], $posts[1], $posts[3] ), $rel_post0 );
	}

	/**
	 * Test invalid function arguments.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_invalid_arguments() {

		$this->setup_posts();
		$posts = $this->posts;

		$args = array( 'fields' => 'ids' );

		// Test single taxonomy.
		$taxonomies = array( 'post_tag' );

		// Not a post ID.
		$fail = $this->rest_related_posts_by_taxonomy( 'not a post ID', $taxonomies, $args );
		$this->assertEquals( 'rest_no_route', $fail, 'Not a post ID' );

		// Nonexistent post ID.
		$fail2 = $this->rest_related_posts_by_taxonomy( 9999999999, $taxonomies, $args );
		$this->assertSame( 'rest_post_invalid_id', $fail2, 'Non existant post ID' );

		// PEmpty taxonomy should default to all taxonomies.
		$fail4 = $this->rest_related_posts_by_taxonomy( $posts[0], '', $args );
		$this->assertNotEmpty( $fail4, 'no taxonomies' );

		// Ivalid taxonomy.
		$fail3 = $this->rest_related_posts_by_taxonomy( $posts[0], 'not a taxonomy', $args );
		$this->assertEmpty( $fail3, 'Invalid taxonomy' );

		// Invalid post type.
		$args['post_types'] = 'not a post type';
		$fail5 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEmpty( $fail5, 'Invalid post type' );
	}

	/**
	 * Test with valid and invlid post types and taxonomies.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_mixed_arguments() {
		$this->setup_posts();
		$posts = $this->posts;

		// Test with a valid and invalid taxonomy.
		$taxonomies = array( 'post_tag', 'lala' );

		// Test with a valid and invalid post_type.
		$args = array( 'post_types' => array( 'post', 'lala' ) );

		$args['fields'] = 'ids';

		// Test post 0.
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[2], $posts[1], $posts[3] ), $rel_post0 );
	}

	/**
	 * Test exclude_terms argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_exclude_terms() {
		$this->setup_posts();
		$args       = array( 'exclude_terms' => $this->tax_1_terms[2], 'fields' => 'ids' );
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2] ), $rel_post0 );
	}

	/**
	 * Test include_terms argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_include_terms() {
		$this->setup_posts();
		$args       = array( 'include_terms' => $this->tax_1_terms[0], 'fields' => 'ids' );
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[2] ), $rel_post0 );
	}

	/**
	 * Test include_terms argument when related === false.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_include_terms_unrelated() {
		$this->setup_posts();
		$args = array(
			'include_terms' => array( $this->tax_2_terms[2], $this->tax_1_terms[3] ),
			'related'       => 'false',
			'fields'        => 'ids',
		);

		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[3], $this->posts[4] ), $rel_post0 );
	}

	/**
	 * Test terms argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_related_posts_by_terms() {
		$this->setup_posts();
		$args = array(
			'terms'      => array( $this->tax_2_terms[3] ),
			'fields'     => 'ids',
		);

		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test terms argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_related_posts_by_terms_invalid_term_id() {
		$this->setup_posts();
		$invalid_id = $this->get_highest_term_id() + 1;

		$args = array(
			'terms'      => array( $invalid_id ),
			'fields'     => 'ids',
			'related'    => false,
		);

		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEmpty(  $rel_post0 );
	}

	/**
	 * Test terms argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_related_posts_by_terms_empty_taxonomies() {
		$this->setup_posts();
		$args = array(
			'terms'      => array( $this->tax_2_terms[3] ),
			'fields'     => 'ids',
		);

		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], '', $args );

		// No taxonomies defaults to all taxonomies
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test terms argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_related_posts_by_terms_invalid_taxonomy() {
		$this->setup_posts();
		$args = array(
			'terms'      => array( $this->tax_2_terms[3] ),
			'fields'     => 'ids',
		);

		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], 'lulu', $args );

		// Valid taxonomies are needed for related terms.
		$this->assertEmpty( $rel_post0 );
	}

	/**
	 * Test terms argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_related_posts_by_terms_invalid_taxonomy_unrelated() {
		$this->setup_posts();
		$args = array(
			'terms'      => array( $this->tax_2_terms[3] ),
			'fields'     => 'ids',
			'related'    => false,
		);

		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], 'lulu', $args );

		// Invalid taxonomies are ignored when related is set to false.
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test terms argument with and without the correct taxonomy.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_related_posts_by_terms_with_taxonomy() {
		$this->setup_posts();

		register_taxonomy( 'ctax', 'post' );
		$terms = $this->factory->term->create_many( 3, array( 'taxonomy' => 'ctax' ) );
		$term_id1 = wp_set_post_terms ( $this->posts[2], (int) $terms[0], 'ctax', true );

		$taxonomies = array( 'category' );

		$args = array(
			'terms'      => array( $this->tax_2_terms[3], (int) $term_id1[0] ),
			'fields'     => 'ids',
			'related'    => true,
		);

		// Post 2 should not be related as the 'ctax' taxonomy is not used in the query.
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );

		$args['related'] = false;
		// Post 2 should now be related because we can get any term from any taxonomy.
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2], $this->posts[3] ), $rel_post0 );

		$args['related'] = true;
		$taxonomies[] = 'ctax';
		// Post 2 should now be related as the 'ctax' taxonomy is queried.
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2], $this->posts[3] ), $rel_post0 );

		$term_id2 = wp_set_post_terms ( $this->posts[2], (int) $terms[1], 'ctax', true );
		$args['terms'][] = $term_id2[0];

		// Post two has more terms in common now
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $taxonomies, $args );
		$this->assertEquals( array( $this->posts[2], $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test related === false without include_terms.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_related() {
		$this->setup_posts();
		$args = array(
			'related'       => 'false',
			'fields'        => 'ids',
		);
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test the include_parents argument.
	 */
	function test_include_parents() {
		$hierarchical = $this->create_posts_with_hierarchical_terms();
		$posts = $hierarchical['posts'];
		$terms = $hierarchical['terms'];

		$args = array(
			'fields' => 'ids',
			'terms'  => array( $terms[3] ),
		);

		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], '', $args );
		$this->assertEquals( array( $posts[3] ), $rel_post0 );

		$args['include_parents'] = true;
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], '', $args );
		$this->assertEquals( array( $posts[1], $posts[2], $posts[3] ), $rel_post0 );
	}

	/**
	 * Test the include_children argument.
	 */
	function test_include_children() {
		$hierarchical = $this->create_posts_with_hierarchical_terms();
		$posts = $hierarchical['posts'];
		$terms = $hierarchical['terms'];

		$args = array(
			'fields' => 'ids',
			'terms'  => array( $terms[1] ),
		);

		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], '', $args );
		$this->assertEquals( array( $posts[1] ), $rel_post0 );

		$args['include_children'] = true;
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], '', $args );
		$this->assertEquals( array( $posts[1], $posts[2], $posts[3] ), $rel_post0 );
	}

	/**
	 * Test exclude_posts function argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_exclude_posts() {
		$this->setup_posts();
		$args       = array( 'exclude_posts' => $this->posts[2], 'fields' => 'ids' );
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test limit_posts function argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_limit_posts() {
		$this->setup_posts();
		$args      = array( 'limit_posts' => 2, 'fields' => 'ids' );
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[2] ), $rel_post0 );
	}

	/**
	 * Test posts_per_page function argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_posts_per_page() {
		$this->setup_posts();
		$args      = array( 'posts_per_page' => 1, 'fields' => 'ids' );
		$rel_post3 = $this->rest_related_posts_by_taxonomy( $this->posts[3], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1] ), $rel_post3 );
	}

	/**
	 * Test fields function argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_fields() {
		$this->setup_posts();
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
	 * Test meta query arguments.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_meta_query() {
		$this->setup_posts();

		// add meta value for meta query argument
		add_post_meta( $this->posts[3], 'meta_key' , 'meta_value' );

		$args = array(
			'fields'     => 'ids',
			'meta_key'   => 'meta_key',
			'meta_value' => 'meta_value',
		);

		add_filter( 'related_posts_by_taxonomy_posts_meta_query', array( $this, 'return_first_argument' ) );

		// Post 3 is related and is the only posts with post meta key `meta_key`
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies,  $args );
		$this->assertEquals( array( $this->posts[3] ), $rel_post0 );
		$this->assertSame( 'AND', $this->arg['relation'] );
		$this->arg = null;
	}

	/**
	 * Test meta query without assigning meta to posts.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_meta_query_with_no_meta_assigned() {
		$this->setup_posts();
		$posts = $this->posts;

		$args = array(
			'fields'         => 'ids',
			'meta_key'       => 'meta_key',
		);

		// test post 0
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $this->taxonomies, $args );
		$this->assertEmpty( $rel_post0 );
	}

	/**
	 * Test post_thumbnail function argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_post_thumbnail() {
		$this->setup_posts();

		// Fake post thumbnails for post 1 and 3
		add_post_meta( $this->posts[1], '_thumbnail_id' , 22 ); // Fake attachment ID's.
		add_post_meta( $this->posts[3], '_thumbnail_id' , 33 );

		$args       = array( 'post_thumbnail' => true, 'fields' => 'ids' );
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[1], $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test post_thumbnail with meta function argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_post_thumbnail_and_meta() {
		$this->setup_posts();

		// Fake post thumbnails for post 1 and 3
		add_post_meta( $this->posts[1], '_thumbnail_id' , 22 ); // fake attachment ID's
		add_post_meta( $this->posts[3], '_thumbnail_id' , 33 );
		add_post_meta( $this->posts[3], 'meta_key' , 'meta_value' );

		$args       = array(
			'post_thumbnail' => true,
			'fields'         => 'ids',
			'meta_key'       => 'meta_key',
			'meta_value'     => 'meta_value',
		);
		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test meta query filter.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_meta_query_filter() {
		$this->setup_posts();

		// Fake post thumbnails for post 1 and 3
		add_post_meta( $this->posts[1], '_thumbnail_id' , 22 ); // fake attachment ID's
		add_post_meta( $this->posts[3], '_thumbnail_id' , 33 );

		// add meta value for meta query filter to post 3
		add_post_meta( $this->posts[3], 'meta_key' , 'meta_value' );

		$args = array( 'post_thumbnail' => true, 'fields' => 'ids' );

		// Adds meta_query array( 'key' => 'meta_key', 'value' => 'meta_value');
		add_filter( 'related_posts_by_taxonomy_posts_meta_query', array( $this, 'meta_query_callback' ), 10, 4 );

		$rel_post0  = $this->rest_related_posts_by_taxonomy( $this->posts[0], $this->taxonomies, $args );
		$this->assertEquals( array( $this->posts[3] ), $rel_post0 );
	}

	/**
	 * Test limit_month function argument.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_limit_month() {
		$this->setup_posts();
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
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_order_asc() {
		$this->setup_posts();
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
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_order_asc_non_related() {
		$this->setup_posts();
		$posts = $this->posts;

		$taxonomies = array( 'category', 'post_tag' );
		// 'false' is validated as false
		$args       = array( 'fields' => 'ids', 'order' => 'asc', 'related' => 'false' );

		// Test post 0.
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[2], $posts[1], $posts[3] ), $rel_post0 );
	}

	/**
	 * Test random order of posts.
	 * Todo: Find out how to test random results and apply
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_order_rand() {
		$this->setup_posts();
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
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_orderby_post_modified() {
		$this->setup_posts();
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

	/**
	 *
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_post_status() {
		$this->setup_posts();
		$posts = $this->posts;

		$user_id = $this->factory->user->create(
			array(
				'role' => 'author',
			)
		);

		$private = array(
			'ID' => $this->posts[2],
			'post_status' => 'private',
		);

		wp_update_post( $private );
		$taxonomies = array( 'category', 'post_tag' );

		$args       = array(
			'fields' => 'ids',
		);

		// User is not set, private post is not included.
		$rel_post3 = $this->rest_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[0] ), $rel_post3 );

		$private = array(
			'ID' => $this->posts[2],
			'post_status' => 'private',
			'post_author' => $user_id,
		);

		wp_update_post( $private );

		// Set user to private post author, post is included.
		wp_set_current_user( $user_id );

		$rel_post3 = $this->rest_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[0], $posts[2] ), $rel_post3 );
	}

	/**
	 * test related posts for post type post
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_include_self() {
		$this->setup_posts();
		$posts = $this->posts;

		// Test with a single taxonomy.
		$taxonomies = array( 'post_tag' );
		$args       = array(
			'fields' => 'ids',
			'include_self' => true,
		);

		// test post 0
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[2], $posts[1], $posts[3] ), $rel_post0 );

		// test post with post date prior then inclusive post
		$rel_post1 = $this->rest_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[0], $posts[2], $posts[3] ), $rel_post1 );
	}

	/**
	 * test related posts for post type post
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_include_self_orderby_rand() {
		$this->setup_posts();
		$posts = $this->posts;

		add_filter( 'related_posts_by_taxonomy_posts_orderby', array( $this, 'return_first_argument' ), 10, 4 );

		// Test with a single taxonomy.
		$taxonomies = array( 'post_tag' );
		$args       = array(
			'fields' => 'ids',
			'include_self' => true,
			'order' => 'RAND',
		);

		// test post 0
		$rel_post0 = $this->rest_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );

		$this->assertCount( 4, $rel_post0 );
		$this->assertSame( (int) $posts[0], (int) $rel_post0[0] );

		//Check if the query contains 'RAND()'
		$this->assertContains( 'RAND()', $this->arg );
		$this->arg = null;
	}

	/**
	 * test include self argument regular order.
	 *
	 * @requires function WP_REST_Controller::register_routes
	 */
	function test_include_self_regular_order() {
		$this->setup_posts();
		$posts = $this->posts;

		// Test with a single taxonomy.
		$taxonomies = array( 'post_tag' );
		$args       = array(
			'fields' => 'ids',
			'include_self' => 'regular_order',
		);

		// test post with post date prior then inclusive post
		$rel_post1 = $this->rest_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[2], $posts[3] ), $rel_post1 );
	}

}
