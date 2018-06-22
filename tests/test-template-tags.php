<?php
/**
 * Tests for the km_rpbt_query_related_posts() function in functions.php.
 *
 */
class KM_RPBT_Template_Tags extends KM_RPBT_UnitTestCase {

	function tearDown() {
		remove_filter( 'use_default_gallery_style', '__return_false', 99 );
		remove_filter( 'related_posts_by_taxonomy_post_class', array( $this, 'post_class' ), 10, 4 );
	}

	/**
	 * Test output from shortcode.
	 */
	function test_shortcode_output_with_post_class() {

		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// get post ids array and permalinks array
		$_posts     = get_posts(
			array(
				'posts__in' => $posts,
				'order'     => 'post__in',
			)
		);
		$ids        = wp_list_pluck( $_posts, 'ID' );
		$permalinks = array_map( 'get_permalink', $ids );

		// expected related posts are post 1,2,3
		$expected = <<<EOF
<div class="rpbt_shortcode">
<h3>Related Posts</h3>
<ul>
<li class="someclass">
<a href="{$permalinks[1]}">{$_posts[1]->post_title}</a>
</li>
<li class="someclass">
<a href="{$permalinks[2]}">{$_posts[2]->post_title}</a>
</li>
<li class="someclass">
<a href="{$permalinks[3]}">{$_posts[3]->post_title}</a>
</li>
</ul>
</div>
EOF;

		ob_start();
		echo do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '" post_class="someclass"]' );
		$shortcode = ob_get_clean();
		$this->assertEquals( strip_ws( $expected ), strip_ws( $shortcode ) );
	}

	/**
	 * Test output from gallery with post class added by a filter.
	 */
	function test_shortcode_no_gallery_style_post_class() {
		add_filter( 'related_posts_by_taxonomy_post_class', array( $this, 'post_class' ), 10, 4 );
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'use_default_gallery_style', '__return_false', 99 );

		ob_start();
		echo km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );
		$gallery = ob_get_clean();

		$static   = $this->get_gallery_instance_id( $gallery );
		$expected = <<<EOF
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='someclass gallery-item'>
<dt class='gallery-icon '>
<a href='{$permalink}' title='{$related_post->post_title}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$args['id']}'>
{$related_post->post_title}
</dd></dl>
<br style='clear: both' />
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery ) );
	}

	function test_km_rpbt_duplicate_post_classes() {
		$classes = km_rpbt_get_post_classes( null, ' class class ' );
		$this->assertSame( 'class', $classes );
	}

	function test_km_rpbt_sanitize_classes() {
		$classes = km_rpbt_sanitize_classes( '' );
		$this->assertSame( '', $classes );

		// Should only return classes if it's a string
		$classes = km_rpbt_sanitize_classes( array( 'class' ) );
		$this->assertSame( '', $classes );

		// Sanitezed string with duplicates removed
		$classes = km_rpbt_sanitize_classes( ' class  otherclass   class   ' );
		$this->assertSame( 'class otherclass', $classes );
	}

	function post_class( $classes, $post, $args, $index ) {
		$classes[] = 'someclass';
		return $classes;
	}

	/**
	 * Test getting the link for a related post title
	 * used in the templates
	 */
	function test_rpbt_get_post_link_global_post() {
		$link2 = km_rpbt_get_post_link();
		if ( $GLOBALS['post'] ) {
			$this->assertNotEmpty( $link2 );
		} else {
			$this->assertEmpty( $link2 );
		}
	}

	/**
	 * Test getting the link for a related post title
	 * used in the templates
	 */
	function test_rpbt_get_post_link_invalid_argument() {
		// get_post() returns null if a post is not found
		$link = km_rpbt_get_post_link( 'lala' );
		$this->assertEmpty( $link );
	}

	/**
	 * Test getting the link for a related post title
	 * used in the templates
	 */
	function test_km_rpbt_get_post_link_output() {
		$posts = $this->create_posts();
		$posts = get_posts();

		$link      = km_rpbt_get_post_link( $posts[0] );
		$permalink = get_permalink( $posts[0] );
		$expected  = '<a href="' . $permalink . '">' . $posts[0]->post_title . '</a>';
		$this->assertSame( $expected, $link );

		$link     = km_rpbt_get_post_link( $posts[0], array('title_attr'  => true ) );
		$expected = '<a href="' . $permalink . '" title="' . $posts[0]->post_title . '">' . $posts[0]->post_title . '</a>';
		$this->assertSame( $expected, $link );
	}

	/**
	 * Test getting the link for a related post title
	 * used in the templates
	 */
	function test_km_rpbt_get_post_link_output_deprecated_parameter_title_attr() {
		$posts = $this->create_posts();
		$posts = get_posts();

		$permalink = get_permalink( $posts[0] );
		$link      = km_rpbt_get_post_link( $posts[0], true );
		$expected  = '<a href="' . $permalink . '" title="' . $posts[0]->post_title . '">' . $posts[0]->post_title . '</a>';
		$this->assertSame( $expected, $link );
	}
}
