<?php
/**
 * Tests for the shortcode in shortcode.php
 */
class KM_RPBT_Shortcode_Tests extends WP_UnitTestCase {

	/**
	 * Utils object to create posts with terms
	 *
	 * @var object
	 */
	private $utils;

	/**
	 * Returned args from filter
	 *
	 * @var array
	 */
	private $args;


	/**
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// Use the utils class to create posts with terms.
		$this->utils = new RPBT_Test_Utils( $this->factory );
	}

	function tearDown() {
		// use tearDown for WP < 4.0
		remove_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', array( $this->utils, 'return_bool' ) );
		remove_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', '__return_true' );
		remove_filter( 'related_posts_by_taxonomy', array( $this, 'return_args' ), 10, 4 );
		parent::tearDown();
	}


	/**
	 * Test if shortcode is registered.
	 */
	public function test_shortcode_is_registered() {
		global $shortcode_tags;
		$this->assertArrayHasKey( 'related_posts_by_tax', $shortcode_tags );
	}

	/**
	 * Test if atts are not changed due to debugging.
	 */
	function test_km_rpbt_get_shortcode_atts() {

		$expected =  array(
			'post_id' => '', 'taxonomies' => 'all',
			'before_shortcode' => '<div class="rpbt_shortcode">', 'after_shortcode' => '</div>',
			'before_title' => '<h3>', 'after_title' => '</h3>',
			'title' => __( 'Related Posts', 'related-posts-by-taxonomy' ),
			'format' => 'links',
			'image_size' => 'thumbnail', 'columns' => 3,
			'caption' => 'post_title', 'type' => 'shortcode',
		);


		// km_rpbt_get_default_args() is also tested in test-functions.php
		$expected = array_merge( km_rpbt_get_default_args(), $expected );
		$expected['post_types'] = '';

		$atts = km_rpbt_get_shortcode_atts();
		$this->assertEquals( $expected, $atts );
	}


	/**
	 * Test validation of atts.
	 *
	 * todo: Needs more testing
	 */
	function test_km_rpbt_validate_shortcode_atts() {

		$atts = km_rpbt_validate_shortcode_atts( array( 'post_types' => '' ) );
		$this->assertEquals( array( 'post' ), $atts['post_types'] );

		$atts = km_rpbt_validate_shortcode_atts( array( 'post_types' => 'post' ) );
		$this->assertEquals( 'post', $atts['post_types'] );
	}


	/**
	 * Test if the shortcode_hide_empty filter is set to true (by default).
	 */
	function test_shortcode_hide_empty_filter_bool() {
		// shortcode
		add_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', array( $this->utils, 'return_bool' ) );
		$id = $this->factory->post->create();
		do_shortcode( '[related_posts_by_tax post_id="' . $id . '"]' );
		$this->assertTrue( $this->utils->boolean );
		$this->utils->boolean = null;
	}

	/**
	 * Test if the shortcode_hide_empty filter works as intended.
	 */
	function test_shortcode_hide_empty_filter() {

		$create_posts = $this->utils->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		ob_start();
		echo do_shortcode( '[related_posts_by_tax post_id="' . $posts[4] . '"]' );
		$shortcode = ob_get_clean();

		$this->assertEmpty( $shortcode );

		add_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', '__return_false' );

		ob_start();
		echo do_shortcode( '[related_posts_by_tax post_id="' . $posts[4] . '"]' );
		$shortcode = ob_get_clean();
		$this->assertContains( '<p>No related posts found</p>', $shortcode );
	}


	/**
	 * Test output from shortcode.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_shortcode_output() {

		$create_posts = $this->utils->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// get post ids array and permalinks array
		$_posts     = get_posts( array( 'posts__in' => $posts, 'order' => 'post__in' ) );
		$ids        = wp_list_pluck( $_posts, 'ID' );
		$permalinks = array_map( 'get_permalink', $ids );

		// expected related posts are post 1,2,3
		$expected = <<<EOF
<div class="rpbt_shortcode">
<h3>Related Posts</h3>
<ul>
<li><a href="{$permalinks[1]}" title="{$_posts[1]->post_title}">{$_posts[1]->post_title}</a></li>
<li><a href="{$permalinks[2]}" title="{$_posts[2]->post_title}">{$_posts[2]->post_title}</a></li>
<li><a href="{$permalinks[3]}" title="{$_posts[3]->post_title}">{$_posts[3]->post_title}</a></li>
</ul>
</div>
EOF;

		ob_start();
		echo do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$shortcode = ob_get_clean();

		$this->assertEquals( strip_ws( $expected ), strip_ws( $shortcode ) );
	}

	/**
	 * Test booleans in shortcode arguments.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_shortcode_booleans() {

		$create_posts = $this->utils->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// use filter to get arguments used for the related posts
		add_filter( 'related_posts_by_taxonomy', array( $this, 'return_args' ), 10, 4 );

		do_shortcode( '[related_posts_by_tax related="" post_id="' . $posts[0] . '"]'  );
		$this->assertTrue( $this->args['related'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax related="true" post_id="' . $posts[0] . '"]'  );
		$this->assertTrue( $this->args['related'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax related="gobbledygook" post_id="' . $posts[0] . '"]'  );
		$this->assertFalse( $this->args['related'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax related="false" post_id="' . $posts[0] . '"]'  );
		$this->assertFalse( $this->args['related'] );
		$this->args = null;
	}


	function return_args( $results, $post_id, $taxonomies, $args ) {
		$this->args = $args;
		return $results;
	}

}