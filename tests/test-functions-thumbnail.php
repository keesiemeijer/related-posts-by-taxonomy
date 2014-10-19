<?php

/**
 * Tests for gallery in functions-thumbnail.php
 */
class KM_RPBT_Gallery_Tests extends WP_UnitTestCase {

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
	 * Test output from gallery.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @depends KM_RPBT_Misc_Tests::test_skip_output_tests
	 */
	function test_shortcode_no_gallery_style() {

		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'use_default_gallery_style', '__return_false', 99 );
		ob_start();
		echo km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post )  );
		$gallery = ob_get_clean();

		$expected = <<<EOF
<div id='gallery-1' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item'>
<dt class='gallery-icon '>
<a href='{$permalink}' title='{$related_post->post_title}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='gallery-1-{$args['id']}'>
{$related_post->post_title}
</dd></dl>
<br style='clear: both' />
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery )  );
	}

	/**
	 * Test output from gallery with gallery style.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @depends KM_RPBT_Misc_Tests::test_skip_output_tests
	 */
	function test_shortcode_with_gallery_style() {

		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'use_default_gallery_style', '__return_true', 99 );
		ob_start();
		echo km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post )  );
		$gallery = ob_get_clean();

		$expected = <<<EOF
<style type='text/css'>
#gallery-2 {
margin: auto;
}
#gallery-2 .gallery-item {
float: left;
margin-top: 10px;
text-align: center;
width: 33%;
}
#gallery-2 img {
border: 2px solid #cfcfcf;
}
#gallery-2 .gallery-caption {
margin-left: 0;
}
/* see gallery_shortcode() in wp-includes/media.php */
</style>
<div id='gallery-2' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item'>
<dt class='gallery-icon '>
<a href='{$permalink}' title='{$related_post->post_title}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='gallery-2-{$args['id']}'>
{$related_post->post_title}
</dd></dl>
<br style='clear: both' />
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery )  );
	}

	/**
	 * Test output gallery with no caption.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 * @depends KM_RPBT_Misc_Tests::test_skip_output_tests
	 */
	function test_shortcode_gallery_no_caption() {

		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );
		$args['caption'] = '';
		add_filter( 'use_default_gallery_style', '__return_false', 99 );
		ob_start();
		echo km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post )  );
		$gallery = ob_get_clean();

		$expected = <<<EOF
<div id='gallery-3' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item'>
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
			'id'         => $related_post->ID,
			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
		);

		return compact( 'related_post', 'permalink', 'args' );
	}


	/**
	 * Adds fake image for testing.
	 */
	function add_image( $image ) {
		return '<img>';
	}

}