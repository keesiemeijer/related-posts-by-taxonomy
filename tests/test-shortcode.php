<?php
/**
 * Tests for the shortcode in shortcode.php
 *
 * @group Shortcode
 */
class KM_RPBT_Shortcode_Tests extends KM_RPBT_UnitTestCase {

	function tearDown() {
		// use tearDown for WP < 4.0
		remove_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', array( $this, 'return_first_argument' ) );
		remove_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', '__return_true' );
		remove_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_first_argument' ) );
		remove_filter( 'related_posts_by_taxonomy_shortcode', '__return_false' );
		remove_filter( 'related_posts_by_taxonomy', array( $this, 'return_first_argument' ) );
		remove_filter( 'related_posts_by_taxonomy_pre_related_posts', array( $this, 'override_related_posts' ), 10, 2 );

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

		$expected = array(
			'post_id'          => '',
			'taxonomies'       => '',
			'before_shortcode' => '<div class="rpbt_shortcode">',
			'after_shortcode'  => '</div>',
			'before_title'     => '<h3>',
			'after_title'      => '</h3>',
			'title'            => 'Related Posts',
			'format'           => 'links',
			'image_size'       => 'thumbnail',
			'columns'          => 3,
			'caption'          => 'post_title',
			'post_class'       => '',
			'link_caption'     => false,
			'type'             => 'shortcode',
		);

		// km_rpbt_get_query_vars() is also tested in test-functions.php
		$expected = array_merge( km_rpbt_get_query_vars(), $expected );
		$expected['post_types'] = '';

		$atts = km_rpbt_get_default_settings( 'shortcode' );

		ksort( $expected );
		ksort( $atts );

		$this->assertEquals( $expected, $atts );
	}

	/**
	 * Test validation of atts.
	 *
	 * todo: Needs more testing
	 */
	function test_km_rpbt_validate_shortcode_atts() {
		// should default to current post post type or default post type post
		$atts = km_rpbt_validate_shortcode_atts(
			array(
				'post_types' => '',
			)
		);
		$this->assertEquals( array( 'post' ), $atts['post_types'] );
	}

	/**
	 * Test if the shortcode_hide_empty filter is set to true (by default).
	 */
	function test_shortcode_hide_empty_filter_bool() {
		// shortcode
		add_filter( 'related_posts_by_taxonomy_shortcode_hide_empty', array( $this, 'return_first_argument' ) );
		$id = $this->factory->post->create();
		do_shortcode( '[related_posts_by_tax post_id="' . $id . '"]' );
		$this->assertTrue( $this->arg );
		$this->arg = null;
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
	 * Test the related posts retrieved by the shortcode
	 *
	 *
	 */
	function test_shortcode_posts() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];
		$post_id      = ' post_id="' . $posts[0] . '"';
		$taxonomies   = ' taxonomies="category,post_tag"';

		add_filter( 'related_posts_by_taxonomy', array( $this, 'return_first_argument' ) );
		$shortcode = do_shortcode( "[related_posts_by_tax{$post_id}{$taxonomies}]" );

		$post_ids = wp_list_pluck( $this->arg, 'ID' );
		$this->assertEquals( array( $posts[1], $posts[2], $posts[3] ), $post_ids );
		$this->arg = null;


		// Create custom post type posts.
		$this->factory->post->create_many( 5,
			array(
				'post_type' => 'cpt',
			)
		);

		$cpt_posts = get_posts( 'post_type=cpt&fields=ids' );
		$cpt_links = array_map( 'get_permalink', $cpt_posts );

		// add a filter to override the related posts.
		add_filter( 'related_posts_by_taxonomy_pre_related_posts', array( $this, 'override_related_posts' ), 10, 2 );

		// Use the same shortcode as before (post_type 'post' by default).
		$shortcode = do_shortcode( "[related_posts_by_tax{$post_id}{$taxonomies}]" );

		$count = 0;
		foreach ( $cpt_links as $link ) {
			if ( false !== strpos( $shortcode, $link ) ) {
				$count++;
			}
		}

		$this->assertTrue( ( 5 === $count ) );
	}

	/**
	 * Test if the shortcode uses the post type from the current post.
	 */
	function test_shortcode_post_type() {

		// register custom post type
		register_post_type(
			'cpt', array(
				'public'      => true,
				'has_archive' => true,
				'taxonomies'  => array( 'post_tag', 'category' ),
				'labels'      => array(
					'name' => 'test_cpt',
				),
			)
		);

		// create posts for custom post type
		$create_posts = $this->create_posts_with_terms( 'cpt' );
		$posts        = $create_posts['posts'];

		// Add a shortcode to post content.
		wp_update_post(
			array(
				'ID'          => $posts[0],
				'post_content' => '[related_posts_by_tax]',
			)
		);

		// use filter to get arguments used for the related posts
		add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_first_argument' ) );

		// Go to a single post page
		$this->go_to( '?post_type=cpt&p=' . $posts[0] );

		// Trigger loop.
		ob_start();
		the_post();
		the_content();
		$content = ob_get_clean();

		$this->assertEquals( array( 'cpt' ), $this->arg['post_types'] );
		$this->arg = null;
	}

	/**
	 * Test the arguments for the related_posts_by_taxonomy_shortcode_atts filter.
	 * Should be te similar to the arguments as for the related_posts_by_taxonomy_widget_args filter
	 */
	function test_related_posts_by_taxonomy_shortcode_atts() {

		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// use filter to get arguments used for the related posts
		add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_first_argument' ) );

		do_shortcode( '[related_posts_by_tax]' );
		$expected = km_rpbt_get_default_settings( 'shortcode' );

		// false if outside the loop
		$expected['post_id'] = get_the_ID();
		// array after validation
		$expected['post_types'] = array( 'post' );

		$this->assertEquals( $expected, $this->arg );
		$this->arg = null;
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
		$_posts     = get_posts(
			array(
				'posts__in' => $posts,
				'order' => 'post__in',
			)
		);
		$ids        = wp_list_pluck( $_posts, 'ID' );
		$permalinks = array_map( 'get_permalink', $ids );

		// expected related posts are post 1,2,3
		$expected = <<<EOF
<div class="rpbt_shortcode">
<h3>Related Posts</h3>
<ul>
<li><a href="{$permalinks[1]}">{$_posts[1]->post_title}</a></li>
<li><a href="{$permalinks[2]}">{$_posts[2]->post_title}</a></li>
<li><a href="{$permalinks[3]}">{$_posts[3]->post_title}</a></li>
</ul>
</div>
EOF;

		ob_start();
		echo do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$shortcode = ob_get_clean();

		$this->assertEquals( strip_ws( $expected ), strip_ws( $shortcode ) );
	}

	/**
	 * Test output if the shortcode is disabled.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_shortcode_disabled_output() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		$shortcode = do_shortcode( '[related_posts_by_tax post_id="' . $posts[1] . '"]' );
		$this->assertNotEmpty( $shortcode );

		// Removes support for the widget.
		add_filter( 'related_posts_by_taxonomy_shortcode', '__return_false' );

		$shortcode = do_shortcode( '[related_posts_by_tax post_id="' . $posts[1] . '"]' );
		$this->assertEmpty( $shortcode );
	}

	/**
	 * Test booleans in shortcode arguments.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_shortcode_boolean_values() {

		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// use filter to get arguments used for the related posts
		add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_first_argument' ) );

		// Default value is true if not used
		do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->arg['related'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax related="" post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->arg['related'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax related="true" post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->arg['related'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax related="gobbledygook" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['related'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax related="false" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['related'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax public_only="true" post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->arg['public_only'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax include_self="true" post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->arg['include_self'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax public_only="hohoho" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['public_only'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax include_self="hohoho" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['include_self'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['public_only'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['include_self'] );
		$this->arg = null;
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
		add_filter( 'related_posts_by_taxonomy_shortcode_atts', array( $this, 'return_first_argument' ) );

		// Default value is false if not used.
		do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['link_caption'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax link_caption="" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['link_caption'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax link_caption="true" post_id="' . $posts[0] . '"]' );
		$this->assertTrue( $this->arg['link_caption'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax link_caption="gobbledygook" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['link_caption'] );
		$this->arg = null;

		do_shortcode( '[related_posts_by_tax link_caption="false" post_id="' . $posts[0] . '"]' );
		$this->assertFalse( $this->arg['link_caption'] );
		$this->arg = null;
	}

	function override_related_posts( $related_posts, $args ) {
		return get_posts( 'post_type=cpt' );
	}

}
