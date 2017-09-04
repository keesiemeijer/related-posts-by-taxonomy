<?php
/**
 * Tests for the shortcode in shortcode.php
 */
class KM_RPBT_Shortcode_Tests extends KM_RPBT_UnitTestCase {

	/**
	 * Returned args from filter
	 *
	 * @var array
	 */
	private $args;

	function tearDown() {
		// use tearDown for WP < 4.0
		remove_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', array( $this, 'return_bool' ) );
		remove_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', '__return_true' );
		remove_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_args' ) );
		remove_filter( 'related_posts_by_taxonomy_shortcode', '__return_false' );

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
			'title' => 'Related Posts',
			'format' => 'links',
			'image_size' => 'thumbnail', 'columns' => 3,
			'caption' => 'post_title', 'link_caption' => false,
			'type' => 'shortcode',
		);


		// km_rpbt_get_default_args() is also tested in test-functions.php
		$expected = array_merge( km_rpbt_get_default_args(), $expected );
		$expected['post_types'] = '';

		$atts = km_rpbt_get_default_settings( 'shortcode' );
		$this->assertEquals( $expected, $atts );
	}


	/**
	 * Test validation of atts.
	 *
	 * todo: Needs more testing
	 */
	function test_km_rpbt_validate_shortcode_atts() {
		// should default to current post post type or default post type post
		$atts = km_rpbt_validate_shortcode_atts( array( 'post_types' => '' ) );
		$this->assertEquals( array( 'post' ), $atts['post_types'] );
	}


	/**
	 * Test if the shortcode_hide_empty filter is set to true (by default).
	 */
	function test_shortcode_hide_empty_filter_bool() {
		// shortcode
		add_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', array( $this, 'return_bool' ) );
		$id = $this->factory->post->create();
		do_shortcode( '[related_posts_by_tax post_id="' . $id . '"]' );
		$this->assertTrue( $this->boolean );
		$this->boolean = null;
	}


	/**
	 * Test if the shortcode_hide_empty filter works as intended.
	 */
	function test_shortcode_hide_empty_filter() {

		$create_posts = $this->create_posts_with_terms();
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
	 * Test if the shortcode uses the post type from the current post.
	 */
	function test_shortcode_post_type() {

		// register custom post type
		register_post_type( 'cpt', array(
				'public'      => true,
				'has_archive' => true,
				'taxonomies'  => array( 'post_tag', 'category' ),
				'labels'      => array(
					'name' => 'test_cpt',
				),
			) );

		// create posts for custom post type
		$create_posts = $this->create_posts_with_terms( 'cpt' );
		$posts        = $create_posts['posts'];

		// Add a shortcode to post content.
		wp_update_post( array(
				'ID'          => $posts[0],
				'post_content' => '[related_posts_by_tax]',
			)
		);

		// use filter to get arguments used for the related posts
		add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_args' ) );

		// Go to a single post page
		$this->go_to( '?post_type=cpt&p=' . $posts[0] );

		// Trigger loop.
		ob_start();
		the_post();
		the_content();
		$content = ob_get_clean();

		$this->assertEquals( array( 'cpt' ), $this->args['post_types']  );
		$this->args = null;
	}


	/**
	 * Test the arguments for the related_posts_by_taxonomy_shortcode_atts filter.
	 * Should be te similar to the arguments as for the related_posts_by_taxonomy_widget_args filter
	 */
	function test_related_posts_by_taxonomy_shortcode_atts() {

		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// use filter to get arguments used for the related posts
		add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_args' ) );

		do_shortcode( '[related_posts_by_tax]' );
		$expected =  km_rpbt_get_default_settings( 'shortcode' );

		// false if outside the loop
		$expected['post_id'] = get_the_ID();
		// array after validation
		$expected['post_types'] = array( 'post' );

		$this->assertEquals( $expected, $this->args  );
		$this->args = null;
	}


	/**
	 * Test output from shortcode.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_shortcode_output() {

		$create_posts = $this->create_posts_with_terms();
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

	function test_shortcode_disabled_output() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// Removes support for the widget.
		add_filter( 'related_posts_by_taxonomy_shortcode', '__return_false' );

		$shortcode = do_shortcode( '[related_posts_by_tax post_id="' . $posts[1] . '"]' );
		add_filter( 'related_posts_by_taxonomy_shortcode', '__return_true' );
		//remove_filter( 'related_posts_by_taxonomy_shortcode', '__return_false' );
		//echo '$shortcode=' .$shortcode;
		$this->assertEmpty( $shortcode );

	}



	/**
	 * Test booleans in shortcode arguments.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_shortcode_related_value() {

		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// use filter to get arguments used for the related posts
		add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_args' ) );

		// Default value is true if not used
		do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->args['related'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax related="" post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->args['related'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax related="true" post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->args['related'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax related="gobbledygook" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->args['related'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax related="false" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->args['related'] );
		$this->args = null;
	}


	/**
	 * Test booleans in shortcode arguments.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_shortcode_link_caption_value() {

		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// use filter to get arguments used for the related posts
		add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_args' ) );

		// Default value is false if not used.
		do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->args['link_caption'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax link_caption="" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->args['link_caption'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax link_caption="true" post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->args['link_caption'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax link_caption="gobbledygook" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->args['link_caption'] );
		$this->args = null;

		do_shortcode( '[related_posts_by_tax link_caption="false" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->args['link_caption'] );
		$this->args = null;
	}


	function return_args( $args ) {
		$this->args = $args;
		return $args;
	}

}
