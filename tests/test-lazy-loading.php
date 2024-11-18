<?php
/**
 * Tests for the html for a ajax query
 *
 * @group LazyLoading
 */
class KM_RPBT_Query_Ajax_Tests extends KM_RPBT_UnitTestCase {

	function tear_down() {
		remove_filter( 'related_posts_by_taxonomy_ajax_query', '__return_true' );
	}

	/**
	 * Test km_rpbt_get_lazy_loading_html().
	 */
	function test_get_related_posts_lazy_loading_html() {
		$args           = km_rpbt_get_default_settings( 'shortcode' );
		$args['format'] = 'thumbnails';
		$ajax_html      = km_rpbt_get_lazy_loading_html( $args );
		$this->assertStringContainsString( "class='rpbt-related-posts-lazy-loading'", $ajax_html );
		$this->assertStringContainsString( ',&quot;type&quot;:&quot;shortcode&quot;', $ajax_html );
		$this->assertStringContainsString( '&quot;format&quot;:&quot;thumbnails&quot;', $ajax_html );
	}

	/**
	 * Test ajax output for the shortcode.
	 */
	function test_shortcode_lazy_loading_html() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		add_filter( 'related_posts_by_taxonomy_lazy_loading', '__return_true' );
		// expected related posts are post 1,2,3
		$expected = <<<EOF
<div class='rpbt-related-posts-lazy-loading' data-rpbt_args='{&quot;post_types&quot;:[&quot;post&quot;],&quot;post_id&quot;:&quot;{$posts[0]}&quot;,&quot;type&quot;:&quot;shortcode&quot;}'>
</div>
EOF;

		ob_start();
		echo do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$shortcode = ob_get_clean();

		$this->assertEquals( strip_ws( $expected ), strip_ws( $shortcode ) );
	}

	/**
	 * Test ajax html output for the widget.
	 */
	function test_rpbt_widget_lazy_loading_html() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		add_filter( 'related_posts_by_taxonomy_lazy_loading', '__return_true' );

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

		// expected related posts are post 1,2,3
		$expected = <<<EOF
<div class='rpbt-related-posts-lazy-loading' data-rpbt_args='{&quot;post_types&quot;:[&quot;post&quot;],&quot;post_id&quot;:{$posts[0]},&quot;before_widget&quot;:&quot;&lt;section&gt;&quot;,&quot;after_widget&quot;:&quot;&lt;\/section&gt;&quot;,&quot;before_title&quot;:&quot;&lt;h2&gt;&quot;,&quot;after_title&quot;:&quot;&lt;\/h2&gt;&quot;,&quot;type&quot;:&quot;widget&quot;}'>
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $output ) );
	}
}
