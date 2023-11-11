<?php
/**
 * Tests for the km_rpbt_query_related_posts() function in functions.php.
 *
 * @group TemplateTags
 */
class KM_RPBT_Template_Tags extends KM_RPBT_UnitTestCase {

	function tear_down() {
		remove_filter( 'use_default_gallery_style', '__return_false', 99 );
		remove_filter( 'related_posts_by_taxonomy_post_class', array( $this, 'post_class' ), 10, 3 );
		remove_filter( 'related_posts_by_taxonomy_cache', '__return_true' );
		remove_filter( 'related_posts_by_taxonomy_the_permalink', array( $this, 'the_permalink' ) , 10, 3 );
	}

	function setup_cache() {
		// Activate cache
		add_filter( 'related_posts_by_taxonomy_cache', '__return_true' );

		// Setup plugin with cache activated.
		$cache = new Related_Posts_By_Taxonomy_Plugin();
		$cache->cache_init();

		$plugin = km_rpbt_plugin();
		if ( $plugin ) {
			$plugin->_setup();
		}
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
	 */
	function test_post_class_is_added_with_filter_for_cached_posts() {
		$this->setup_cache();

		$plugin = km_rpbt_plugin();

		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		$taxonomies = array( 'post_tag' );
		$related_posts = km_rpbt_cache_related_posts( $posts[1], $taxonomies );

		add_filter( 'related_posts_by_taxonomy_post_class', array( $this, 'post_class' ), 10, 3 );
		$args = array( 'taxonomies' => $taxonomies, 'post_id' => $posts[1] );
		$related = $plugin->cache->get_related_posts( $args );

		// Check if related posts are from the cache
		$log = sprintf( 'Post ID %d - cache exists', $posts[1] );
		$this->assertTrue( $this->cache_log_contains( $log ), 'posts not found in cache' );

		$this->assertTrue( isset( $related[0]->rpbt_post_class ), 'property not found' );
	}

	/**
	 * Test output from gallery with post class added by a filter.
	 */
	function test_shortcode_no_gallery_style_post_class() {
		add_filter( 'related_posts_by_taxonomy_post_class', array( $this, 'post_class' ), 10, 3 );
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'use_default_gallery_style', '__return_false', 99 );

		ob_start();
		echo km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );
		$gallery = ob_get_clean();

		$static   = $this->get_gallery_instance_id( $gallery );
		$expected = <<<EOF
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item someclass' role='figure' aria-label='{$related_post->post_title}'>
<dt class='gallery-icon'>
<a href='{$permalink}'><img></a>
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

	/**
	 * Test getting the link for a related post without global post
	 * used in the templates
	 */
	function test_rpbt_get_post_link_empty_global_post() {
		unset( $GLOBALS['post'] );
		$link2 = km_rpbt_get_post_link();
		$this->assertEmpty( $link2 );
	}

	/**
	 * Test getting the link for a related post from the global post
	 * used in the templates
	 */
	function test_rpbt_get_post_link_global_post() {
		$posts = $this->create_posts();
		$posts = get_posts();
		$GLOBALS['post'] = $posts[0];

		$link2 = km_rpbt_get_post_link();
		$this->assertNotEmpty( $link2 );
	}

	/**
	 * Test getting the link with invalid post id
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

		$link     = km_rpbt_get_post_link( $posts[0], array( 'title_attr'  => true ) );
		$expected = '<a href="' . $permalink . '" title="' . $posts[0]->post_title . '">' . $posts[0]->post_title . '</a>';
		$this->assertSame( $expected, $link );
	}

	/**
	 * Test getting the link with a post date
	 * used in the templates
	 */
	function test_km_rpbt_get_post_link_with_date_output() {
		$posts = $this->create_posts();
		$posts = get_posts();

		$link      = km_rpbt_get_post_link( $posts[0], array( 'show_date' => true ) );
		$permalink = get_permalink( $posts[0] );

		$time_string = '<time class="rpbt-post-date" datetime="%1$s">%2$s</time>';

		$time_string = sprintf(
			$time_string,
			get_the_date( DATE_W3C, $posts[0] ),
			get_the_date( '', $posts[0] )
		);

		$expected  = '<a href="' . $permalink . '">' . $posts[0]->post_title . '</a> ' . $time_string;

		$this->assertSame( $expected, $link );
	}

	/**
	 * Test filtering the link for a related post title
	 * used in the templates
	 */
	function test_km_rpbt_the_permalink_filter() {
		$posts = $this->create_posts();
		$posts = get_posts();

		add_filter( 'related_posts_by_taxonomy_the_permalink', array( $this, 'the_permalink' ) , 10, 3 );

		$link      = km_rpbt_get_post_link( $posts[0] );
		$permalink = get_permalink( $posts[0] );
		$expected  = '<a href="' . $permalink . '?filtered=true">' . $posts[0]->post_title . '</a>';
		$this->assertSame( $expected, $link );
	}

	/**
	 * Test getting the link with deprecated parameter
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

	/**
	 * callback for related_posts_by_taxonomy_post_class filter.
	 */
	function post_class( $classes, $post, $args ) {
		$classes[] = 'someclass';
		return $classes;
	}

	/**
	 * callback for related_posts_by_taxonomy_the_permalink filter.
	 */
	function the_permalink( $permalink, $post, $args ) {
		return $permalink . '?filtered=true';
	}
}
