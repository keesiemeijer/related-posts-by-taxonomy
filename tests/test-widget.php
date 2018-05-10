<?php
/**
 * Tests for the widget in /includes/widget.php
 */
class KM_RPBT_Widget_Tests extends KM_RPBT_UnitTestCase {

	function tearDown() {
		// use tearDown for WP < 4.0
		remove_filter( 'related_posts_by_taxonomy_widget_hide_empty', array( $this, 'return_first_argument' ) );
		remove_filter( 'related_posts_by_taxonomy_widget_args', array( $this, 'return_first_argument' ) );
		remove_filter( 'related_posts_by_taxonomy_widget_hide_empty', '__return_false' );
		remove_filter( 'related_posts_by_taxonomy_widget', '__return_false' );
		remove_filter( 'related_posts_by_taxonomy_pre_related_posts', array( $this, 'override_related_posts' ), 10, 2 );
		parent::tearDown();
	}

	/**
	 * Test if the widget exists.
	 */
	function test_rpbt_widget_exists() {
		global $wp_widget_factory;

		$widget_class = 'Related_Posts_By_Taxonomy';
		$this->assertArrayHasKey( $widget_class, $wp_widget_factory->widgets );
	}

	/**
	 * Test if the widget exists.
	 */
	function test_rpbt_widget_disabled() {
		global $wp_widget_factory;

		// Unregister widget for this test.
		unregister_widget( 'Related_Posts_By_Taxonomy' );

		// Removes support for the widget.
		add_filter( 'related_posts_by_taxonomy_widget', '__return_false' );

		// Registers the widget if supported.
		km_rpbt_related_posts_by_taxonomy_widget();

		$widget_class = 'Related_Posts_By_Taxonomy';
		$this->assertArrayNotHasKey( $widget_class, $wp_widget_factory->widgets );
	}

	/**
	 * Test if the widget_hide_empty filter is set to true (by default).
	 */
	function test_widget_hide_empty_filter_set_to_true() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		add_filter( 'related_posts_by_taxonomy_widget_hide_empty', array( $this, 'return_first_argument' ) );
		$widget = new Related_Posts_By_Taxonomy( 'related-posts-by-taxonomy', __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ) );

		// run the widget
		ob_start();
		$args = array(
			'before_widget' => '<section>',
			'after_widget'  => '</section>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		);
		$instance = array( 'post_id' => $posts[0] );
		$widget->_set( 2 );
		$widget->widget( $args, $instance );
		$output = ob_get_clean();

		$this->assertTrue( $this->arg  );
		$this->arg = null;
	}

	/**
	 * Test if the widget_hide_empty filter is set to true (by default).
	 */
	function test_widget_posts() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		//add_filter( 'related_posts_by_taxonomy_widget_hide_empty', array( $this, 'return_first_argument' ) );
		$widget = new Related_Posts_By_Taxonomy( 'related-posts-by-taxonomy', __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ) );
		$args   = array(
			'before_widget' => '<section>',
			'after_widget'  => '</section>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		);

		// run the widget
		ob_start();
		$instance = array( 'post_id' => $posts[0] );
		$widget->_set( 2 );
		$widget->widget( $args, $instance );
		$output = ob_get_clean();

		$links = array_map( 'get_permalink', $posts );

		$count = 0;
		foreach ( $links as $link ) {
			if ( false !== strpos( $output, $link ) ) {
				$count++;
			}
		}

		// Found 3 related posts.
		$this->assertTrue( ( 3 === $count ) );

		// Create custom post type posts.
		$this->factory->post->create_many( 5,
			array(
				'post_type' => 'cpt',
			)
		);

		$cpt_posts = get_posts( 'post_type=cpt&fields=ids' );
		$cpt_links = array_map( 'get_permalink', $cpt_posts );

		// Add a filter to override the related posts.
		add_filter( 'related_posts_by_taxonomy_pre_related_posts', array( $this, 'override_related_posts' ), 10, 2 );

		// Run the same widget with the same arguments as before.
		ob_start();
		$instance = array( 'post_id' => $posts[0] );
		$widget->_set( 2 );
		$widget->widget( $args, $instance );
		$cpt_output = ob_get_clean();

		$count = 0;
		foreach ( $cpt_links as $cpt_link ) {
			if ( false !== strpos( $cpt_output, $cpt_link ) ) {
				$count++;
			}
		}
		// Found 5 custom post type posts.
		$this->assertTrue( ( 5 === $count ) );
	}

	/**
	 * Test if the widget_hide_empty filter if set to false.
	 */
	function test_widget_hide_empty_filter_set_to_false() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		add_filter( 'related_posts_by_taxonomy_widget_hide_empty', '__return_false' );
		$widget = new Related_Posts_By_Taxonomy( 'related-posts-by-taxonomy', __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ) );

		// run the widget
		ob_start();
		$args = array(
			'before_widget' => '<section>',
			'after_widget'  => '</section>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		);
		$instance = array( 'post_id' => $posts[4] );
		$widget->_set( 2 );
		$widget->widget( $args, $instance );
		$output = ob_get_clean();

		$this->assertContains( '<p>No related posts found</p>', $output );
	}

	/**
	 * Test the arguments for the filter related_posts_by_taxonomy_widget_args.
	 * Should be te similar to the arguments as for the related_posts_by_taxonomy_shortcode_atts filter
	 */
	function test_widget_filter_settings() {
		$create_posts = $this->create_posts_with_terms();

		add_filter( 'related_posts_by_taxonomy_widget_args', array( $this, 'return_first_argument' ) );
		$widget = new Related_Posts_By_Taxonomy( 'related-posts-by-taxonomy', __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ) );

		// run the widget
		ob_start();
		$args = array(
			'before_widget' => '<section>',
			'after_widget'  => '</section>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		);

		$widget->widget( $args, array() );
		$output = ob_get_clean();

		$expected               = km_rpbt_get_default_settings( 'widget' );
		$expected['post_types'] = array( 'post' ); // set in the widget as default
		$expected['post_id']    = false; // not in the loop

		$this->assertEquals( $expected, $this->arg );
		$this->arg = null;
	}

	/**
	 * Test args validation.
	 */
	function test_widget_get_instance_settings() {
		$create_posts = $this->create_posts_with_terms();
		$widget       = new Related_Posts_By_Taxonomy( 'related-posts-by-taxonomy', __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ) );

		$settings = $widget->get_instance_settings( array() );
		$expected = km_rpbt_get_default_settings( 'widget' );
		$expected['post_types'] = array( 'post' => 'on' ); // set in the widget as default

		$this->assertEquals( $expected, $settings );
	}

	/**
	 * Test output from widget.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_rpbt_widget_output() {

		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		$widget = new Related_Posts_By_Taxonomy( 'related-posts-by-taxonomy', __( 'Related Posts By Taxonomy', 'related-posts-by-taxonomy' ) );

		ob_start();
		$args = array(
			'before_widget' => '<section>',
			'after_widget'  => '</section>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		);

		$instance = array( 'post_id' => $posts[0] );
		$widget->_set( 2 );
		$widget->widget( $args, $instance );
		$output = ob_get_clean();

		$this->assertContains( '<h2>Related Posts</h2>', $output );
		$this->assertContains( '<section>', $output );
		$this->assertContains( '</section>', $output );

		// get post ids array and permalinks array
		$_posts     = get_posts( array( 'posts__in' => $posts, 'order' => 'post__in' ) );
		$ids        = wp_list_pluck( $_posts, 'ID' );
		$permalinks = array_map( 'get_permalink', $ids );

		// expected related posts are post 1,2,3
		$expected = <<<EOF
<section><h2>Related Posts</h2>
<ul>
<li><a href="{$permalinks[1]}">{$_posts[1]->post_title}</a></li>
<li><a href="{$permalinks[2]}">{$_posts[2]->post_title}</a></li>
<li><a href="{$permalinks[3]}">{$_posts[3]->post_title}</a></li>
</ul>
</section>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $output ) );
	}

	function override_related_posts( $related_posts, $args ) {
		return get_posts( 'post_type=cpt' );
	}

}
