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
	 * Set up.
	 */
	function setUp() {
		parent::setUp();

		// Use the utils class to create posts with terms.
		$this->utils = new RPBT_Test_Utils( $this->factory );
	}

	/**
	 * Test if shortcode is registered.
	 */
	public function test_shortcode_is_registered() {
		global $shortcode_tags;
		$this->assertArrayHasKey( 'related_posts_by_tax', $shortcode_tags );
	}

	/**
	 * Test output from shortcode.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @depends KM_RPBT_Misc_Tests::test_skip_output_tests
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
Related Posts
<ul>
<li><a href="{$permalinks[1]}" title="{$_posts[1]->post_title}">{$_posts[1]->post_title}</a></li>
<li><a href="{$permalinks[2]}" title="{$_posts[2]->post_title}">{$_posts[2]->post_title}</a></li>
<li><a href="{$permalinks[3]}" title="{$_posts[3]->post_title}">{$_posts[3]->post_title}</a></li>
</ul>
EOF;
		ob_start();
		echo do_shortcode( '[related_posts_by_tax post_id="' . $posts[0] . '"]' );
		$shortcode = ob_get_clean();

		$this->assertEquals( strip_ws( $expected ), strip_ws( $shortcode ) );
	}


	/**
	 * Test output from gallery.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @depends KM_RPBT_Misc_Tests::test_skip_output_tests
	 */
	function test_shortcode_gallery() {

		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		ob_start();
		echo km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post )  );
		$gallery = ob_get_clean();

		$expected = <<<EOF
<div id='gallery-1' class='gallery related-gallery related-galleryid-0 gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item'>
<dt class='gallery-icon '>
<a href='{$permalink}' title='{$related_post->post_title}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption'>
{$related_post->post_title}
</dd></dl>
<br style='clear: both' />
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery )  );
	}

	/**
	 * Test output from gallery.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @depends KM_RPBT_Misc_Tests::test_skip_output_tests
	 */
	function test_shortcode_gallery_no_caption() {

		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );
		$args['caption'] = '';

		ob_start();
		echo km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post )  );
		$gallery = ob_get_clean();

		$expected = <<<EOF
<div id='gallery-2' class='gallery related-gallery related-galleryid-0 gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item'>
<dt class='gallery-icon '>
<a href='{$permalink}' title='{$related_post->post_title}'><img></a>
</dt></dl>
<br style='clear: both' />
</div>
EOF;
		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery )  );
	}

	function setup_gallery() {

		$create_posts = $this->utils->create_posts_with_terms();
		$posts        = $create_posts['posts'];
		$related_post = get_post( $posts[0] );
		$permalink    = get_permalink( $related_post->ID );

		// Adds a fake image <img>, otherwhise the function will return nothing.
		add_filter( 'related_posts_by_taxonomy_post_thumbnail', array( $this, 'add_image' ) );

		$args = array(
			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
		);

		return compact( 'related_post', 'permalink', 'args' );

	}


	/**
	 * Adds fake image.
	 */
	function add_image( $image ) {
		return '<img>';
	}

}
