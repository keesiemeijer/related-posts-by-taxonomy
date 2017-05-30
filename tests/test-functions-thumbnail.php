<?php

/**
 * Tests for gallery in functions-thumbnail.php
 */
class KM_RPBT_Gallery_Tests extends KM_RPBT_UnitTestCase {

	function tearDown() {
		// use tearDown for WP < 4.0
		remove_filter( 'use_default_gallery_style', '__return_false', 99 );
		remove_filter( 'use_default_gallery_style', '__return_true', 99 );
		remove_filter( 'related_posts_by_taxonomy_post_thumbnail_link', array( $this, 'add_image' ), 99, 4 );
	}


	/**
	 * Test output from gallery.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
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
	 */
	function test_shortcode_gallery_no_caption() {

		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );
		$args['caption'] = '';

		//
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


	/**
	 * Test linked caption.
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts_with_terms
	 */
	function test_shortcode_with_linked_caption() {

		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		$args['link_caption'] = true;

		add_filter( 'use_default_gallery_style', '__return_false', 99 );
		ob_start();
		echo km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post )  );
		$gallery = ob_get_clean();

		$expected = <<<EOF
<div id='gallery-4' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item'>
<dt class='gallery-icon '>
<a href='{$permalink}' title='{$related_post->post_title}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='gallery-4-{$args['id']}'>
<a href='{$permalink}'>{$related_post->post_title}</a>
</dd></dl>
<br style='clear: both' />
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery )  );
	}


	/**
	 * Test the output of a regular WordPress gallery.
	 *
	 * If anything changes in the WordPress gallery, change it in the Related posts gallery.
	 */
	function test_wordpress_gallery() {

		$ids = array();
		foreach ( range( 1, 3 ) as $i ) {
			$attachment_id = $this->factory->attachment->create_object( "image$i.jpg", 0, array(
					'post_mime_type' => 'image/jpeg',
					'post_type' => 'attachment',
					'post_excerpt' => "excerpt $i",
				) );
			$metadata = array_merge( array( "file" => "image$i.jpg" ), array( 'width' => 100, 'height' => 100, 'sizes' => '' ) );
			wp_update_attachment_metadata( $attachment_id, $metadata );
			$ids[] = $attachment_id;

		}

		$ids_str = implode( ',', $ids );


		$blob =<<<BLOB
[gallery ids="$ids_str"]
BLOB;

		$content = do_shortcode( $blob );
		$content = preg_replace( '/<img .*?\/>/', '', $content );

		$expected = <<<EOF
<style type='text/css'>
	#gallery-1 {
		margin: auto;
	}
	#gallery-1 .gallery-item {
		float: left;
		margin-top: 10px;
		text-align: center;
		width: 33%;
	}
	#gallery-1 img {
		border: 2px solid #cfcfcf;
	}
	#gallery-1 .gallery-caption {
		margin-left: 0;
	}
	/* see gallery_shortcode() in wp-includes/media.php */
</style>
<div id='gallery-1' class='gallery galleryid-0 gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item'>
	<dt class='gallery-icon landscape'>
		<a href='http://example.org/?attachment_id=$ids[0]'><img width="100" height="100" src="http://example.org/wp-content/uploads/image1.jpg" class="attachment-thumbnail" alt="excerpt 1" aria-describedby="gallery-1-$ids[0]" /></a>
	</dt>
		<dd class='wp-caption-text gallery-caption' id='gallery-1-$ids[0]'>
		excerpt 1
		</dd></dl><dl class='gallery-item'>
	<dt class='gallery-icon landscape'>
		<a href='http://example.org/?attachment_id=$ids[1]'><img width="100" height="100" src="http://example.org/wp-content/uploads/image2.jpg" class="attachment-thumbnail" alt="excerpt 2" aria-describedby="gallery-1-$ids[1]" /></a>
	</dt>
		<dd class='wp-caption-text gallery-caption' id='gallery-1-$ids[1]'>
		excerpt 2
		</dd></dl><dl class='gallery-item'>
	<dt class='gallery-icon landscape'>
		<a href='http://example.org/?attachment_id=$ids[2]'><img width="100" height="100" src="http://example.org/wp-content/uploads/image3.jpg" class="attachment-thumbnail" alt="excerpt 3" aria-describedby="gallery-1-$ids[2]" /></a>
	</dt>
		<dd class='wp-caption-text gallery-caption' id='gallery-1-$ids[2]'>
		excerpt 3
		</dd></dl><br style="clear: both" />
</div>
EOF;
		$expected = preg_replace( '/<img .*?\/>/', '', $expected );

		$this->assertEquals( strip_ws( $expected ), strip_ws( $content )  );
	}


	/**
	 * Sets up posts for the gallery
	 *
	 * @depends KM_RPBT_Misc_Tests::test_create_posts
	 */
	function setup_gallery() {

		$posts        = $this->create_posts();
		$related_post = get_post( $posts[0] );
		$permalink    = get_permalink( $related_post->ID );

		// Adds a fake image <img>, otherwhise the function will return nothing.
		add_filter( 'related_posts_by_taxonomy_post_thumbnail_link', array( $this, 'add_image' ), 99, 4 );

		$args = array(
			'id'         => $related_post->ID,
			'itemtag'    => 'dl',
			'icontag'    => 'dt',
			'captiontag' => 'dd',
		);

		return compact( 'related_post', 'permalink', 'args' );
	}


	/**
	 * Adds a fake image for testing.
	 */
	function add_image( $image, $attr, $related, $args ) {
		return "<a href='{$attr['permalink']}' title='{$attr['title_attr']}'><img></a>";
	}

}