<?php
/**
 * Tests for gallery in gallery.php
 *
 * @group Gallery
 */
class KM_RPBT_Gallery_Tests extends KM_RPBT_UnitTestCase {

	function tear_down() {
		// use tearDown for WP < 4.0
		remove_filter( 'use_default_gallery_style', '__return_false', 99 );
		remove_filter( 'use_default_gallery_style', '__return_true', 99 );
		remove_filter( 'related_posts_by_taxonomy_post_thumbnail_link', array( $this, 'add_image' ), 99, 4 );
		remove_filter( 'related_posts_by_taxonomy_gallery', array( $this, 'return_first_argument' ) );
		remove_filter( 'related_posts_by_taxonomy_post_thumbnail_link', array( $this, 'return_query_args' ), 10, 4 );
		remove_filter( 'related_posts_by_taxonomy_strip_disallowed_html', '__return_false' );
	}

	function test_gallery_class() {
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'related_posts_by_taxonomy_gallery', array( $this, 'return_first_argument' ) );
		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		$this->assertSame( 'gallery', $this->arg['gallery_class'] );
		$this->arg = null;

		$args['gallery_format'] = 'editor_block';

		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );
		$this->assertSame( 'wp-block-gallery', $this->arg['gallery_class'] );
		$this->arg = null;
	}

	function test_gallery_columns() {
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		$args['columns'] = 0;
		add_filter( 'related_posts_by_taxonomy_post_thumbnail_link', array( $this, 'return_query_args' ), 10, 4 );
		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		$this->assertSame( 0, $this->query_args['columns'] );
		$this->query_args = null;

		$args['gallery_format'] = 'editor_block';

		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		// Zero columns is not allowed
		$this->assertSame( 1, $this->query_args['columns'] );
		$this->query_args = null;
	}

	/**
	 * Test output from gallery.
	 */
	function test_shortcode_no_gallery_style() {
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'use_default_gallery_style', '__return_false', 99 );

		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		$static   = $this->get_gallery_instance_id( $gallery );
		$expected = <<<EOF
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='{$related_post->post_title}'>
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

	/**
	 * Test output from gallery.
	 */
	function test_shortcode_gallery_format_editor_block_with_custom_post_class() {
		$gallery_args                           = $this->setup_gallery();
		$gallery_args['args']['gallery_format'] = 'editor_block';
		$gallery_args['args']['post_class']     = 'my-class';
		extract( $gallery_args );

		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		$expected = <<<EOF
<figure class="wp-block-gallery rpbt-related-block-gallery columns-3 is-cropped" role="group" aria-label="Gallery images">
<ul class="blocks-gallery-grid">
<li class="blocks-gallery-item my-class">
<figure role='figure' aria-label='{$related_post->post_title}'>
<a href='{$permalink}'><img></a>
<figcaption class="blocks-gallery-item__caption">{$related_post->post_title}</figcaption>
</figure>
</li>
</ul>
</figure>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery ) );
	}

	/**
	 * Test output from gallery.
	 */
	function test_shortcode_no_gallery_style_fields_ids() {
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'use_default_gallery_style', '__return_false', 99 );
		// Use IDs for related posts.
		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( "{$related_post->ID}" ) );

		$static   = $this->get_gallery_instance_id( $gallery );
		$expected = <<<EOF
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='{$related_post->post_title}'>
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

	/**
	 * Test output from gallery.
	 */
	function test_shortcode_no_gallery_style_post_date() {
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );
		$args['show_date'] = true;

		add_filter( 'use_default_gallery_style', '__return_false', 99 );
		$gallery  = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );
		$date     = get_the_date( '', $related_post );
		$datetime = get_the_date( DATE_W3C, $related_post );

		$static   = $this->get_gallery_instance_id( $gallery );
		$expected = <<<EOF
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='{$related_post->post_title} {$date}'>
<dt class='gallery-icon'>
<a href='{$permalink}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$args['id']}'>
{$related_post->post_title} <time class="rpbt-post-date" datetime="{$datetime}">{$date}</time>
</dd></dl>
<br style='clear: both' />
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery ) );
	}

	/**
	 * Test output from gallery.
	 */
	function test_shortcode_no_gallery_class() {
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'use_default_gallery_style', '__return_false', 99 );
		$args['gallery_class'] = '';
		$gallery               = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		$static   = $this->get_gallery_instance_id( $gallery );
		$expected = <<<EOF
<div id='rpbt-related-gallery-$static' class='related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='{$related_post->post_title}'>
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

	/**
	 * Test output from gallery.
	 */
	function test_gallery_post_class() {
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'use_default_gallery_style', '__return_false', 99 );
		$args['post_class'] = 'my-class';
		$gallery            = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		$static   = $this->get_gallery_instance_id( $gallery );
		$expected = <<<EOF
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item my-class' role='figure' aria-label='{$related_post->post_title}'>
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

	/**
	 * Test output from gallery with gallery style.
	 */
	function test_gallery_style() {
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		add_filter( 'use_default_gallery_style', '__return_true', 99 );
		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		$static    = $this->get_gallery_instance_id( $gallery );
		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

		$expected = <<<EOF
<style{$type_attr}>
#rpbt-related-gallery-$static {
margin: auto;
}
#rpbt-related-gallery-$static .gallery-item {
float: left;
margin-top: 10px;
text-align: center;
width: 33%;
}
#rpbt-related-gallery-$static img {
border: 2px solid #cfcfcf;
}
#rpbt-related-gallery-$static .gallery-caption {
margin-left: 0;
}
/* see gallery_shortcode() in wp-includes/media.php */
</style>
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='{$related_post->post_title}'>
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

	/**
	 * Test output from gallery with gallery style.
	 */
	function test_shortcode_with_gallery_style() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];
		$permalinks   = array();
		$titles       = array();

		foreach ( $posts as $post_id ) {
			$attachment_id = $this->create_image();
			set_post_thumbnail( $post_id, $attachment_id );
			$permalinks[] = get_permalink( $post_id );
			$titles[]     = get_the_title( $post_id );
		}

		add_filter( 'related_posts_by_taxonomy_post_thumbnail_link', array( $this, 'add_image' ), 99, 4 );
		add_filter( 'use_default_gallery_style', '__return_true', 99 );

		ob_start();
		echo do_shortcode( '[related_posts_by_tax format="thumbnails" post_id="' . $posts[0] . '"]' );
		$gallery = ob_get_clean();

		$static    = $this->get_gallery_instance_id( $gallery );
		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

				$expected = <<<EOF
<div class="rpbt_shortcode">
<h3>Related Posts</h3>
<style{$type_attr}>
#rpbt-related-gallery-$static {
margin: auto;
}
#rpbt-related-gallery-$static .gallery-item {
float: left;
margin-top: 10px;
text-align: center;
width: 33%;
}
#rpbt-related-gallery-$static img {
border: 2px solid #cfcfcf;
}
#rpbt-related-gallery-$static .gallery-caption {
margin-left: 0;
}
/* see gallery_shortcode() in wp-includes/media.php */
</style>
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$posts[0]} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='{$titles[1]}'>
<dt class='gallery-icon'>
<a href='{$permalinks[1]}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$posts[1]}'>
{$titles[1]}
</dd></dl><dl class='gallery-item' role='figure' aria-label='{$titles[2]}'>
<dt class='gallery-icon'>
<a href='{$permalinks[2]}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$posts[2]}'>
{$titles[2]}
</dd></dl><dl class='gallery-item' role='figure' aria-label='{$titles[3]}'>
<dt class='gallery-icon'>
<a href='{$permalinks[3]}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$posts[3]}'>
{$titles[3]}
</dd></dl><br style="clear: both" />
</div>
</div>
EOF;
		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery ) );
	}
	/**
	 * Test output from gallery with gallery style.
	 */
	function test_shortcode_with_gallery_style_with_title_not_sanitatized() {
		// With this filter set to false malicious code could be added with the shortcode
		add_filter( 'related_posts_by_taxonomy_strip_disallowed_html', '__return_false' );

		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];
		$permalinks   = array();
		$titles       = array();

		foreach ( $posts as $post_id ) {
			$attachment_id = $this->create_image();
			set_post_thumbnail( $post_id, $attachment_id );
			$permalinks[] = get_permalink( $post_id );
			$titles[]     = get_the_title( $post_id );
		}

		add_filter( 'related_posts_by_taxonomy_post_thumbnail_link', array( $this, 'add_image' ), 99, 4 );

		add_filter( 'use_default_gallery_style', '__return_true', 99 );
		// $gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );
		// $gallery = do_shortcode( "[related_posts_by_tax format='thumbnails' post_id='$post_id" );

		ob_start();
		echo do_shortcode( '[related_posts_by_tax format="thumbnails" post_id="' . $posts[0] . '" title="Related Posts \x3c\x73\x63\x72\x69\x70\x74\x3e\x61\x6c\x65\x72\x74\x28\x64\x6f\x63\x75\x6d\x65\x6e\x74\x2e\x63\x6f\x6f\x6b\x69\x65\x29\x3c\x2f\x73\x63\x72\x69\x70\x74\x3e\x0d\x0a"]' );
		$gallery = ob_get_clean();

		$static    = $this->get_gallery_instance_id( $gallery );
		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

				$expected = <<<EOF
<div class="rpbt_shortcode">
<h3>Related Posts <script>alert(document.cookie)</script></h3>
<style{$type_attr}>
#rpbt-related-gallery-$static {
margin: auto;
}
#rpbt-related-gallery-$static .gallery-item {
float: left;
margin-top: 10px;
text-align: center;
width: 33%;
}
#rpbt-related-gallery-$static img {
border: 2px solid #cfcfcf;
}
#rpbt-related-gallery-$static .gallery-caption {
margin-left: 0;
}
/* see gallery_shortcode() in wp-includes/media.php */
</style>
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$posts[0]} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='{$titles[1]}'>
<dt class='gallery-icon'>
<a href='{$permalinks[1]}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$posts[1]}'>
{$titles[1]}
</dd></dl><dl class='gallery-item' role='figure' aria-label='{$titles[2]}'>
<dt class='gallery-icon'>
<a href='{$permalinks[2]}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$posts[2]}'>
{$titles[2]}
</dd></dl><dl class='gallery-item' role='figure' aria-label='{$titles[3]}'>
<dt class='gallery-icon'>
<a href='{$permalinks[3]}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$posts[3]}'>
{$titles[3]}
</dd></dl><br style="clear: both" />
</div>
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery ) );
	}

	/**
	 * Test output from gallery with gallery style.
	 */
	function test_shortcode_with_gallery_style_with_title_sanitatized() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];
		$permalinks   = array();
		$titles       = array();

		foreach ( $posts as $post_id ) {
			$attachment_id = $this->create_image();
			set_post_thumbnail( $post_id, $attachment_id );
			$permalinks[] = get_permalink( $post_id );
			$titles[]     = get_the_title( $post_id );
		}

		add_filter( 'related_posts_by_taxonomy_post_thumbnail_link', array( $this, 'add_image' ), 99, 4 );

		add_filter( 'use_default_gallery_style', '__return_true', 99 );
		// $gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );
		// $gallery = do_shortcode( "[related_posts_by_tax format='thumbnails' post_id='$post_id" );

		ob_start();
		echo do_shortcode( '[related_posts_by_tax format="thumbnails" post_id="' . $posts[0] . '" title="Related Posts \x3c\x73\x63\x72\x69\x70\x74\x3e\x61\x6c\x65\x72\x74\x28\x64\x6f\x63\x75\x6d\x65\x6e\x74\x2e\x63\x6f\x6f\x6b\x69\x65\x29\x3c\x2f\x73\x63\x72\x69\x70\x74\x3e\x0d\x0a"]' );
		$gallery = ob_get_clean();

		$static    = $this->get_gallery_instance_id( $gallery );
		$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';

				$expected = <<<EOF
<div class="rpbt_shortcode">
<h3>Related Posts alert(document.cookie)</h3>
<style{$type_attr}>
#rpbt-related-gallery-$static {
margin: auto;
}
#rpbt-related-gallery-$static .gallery-item {
float: left;
margin-top: 10px;
text-align: center;
width: 33%;
}
#rpbt-related-gallery-$static img {
border: 2px solid #cfcfcf;
}
#rpbt-related-gallery-$static .gallery-caption {
margin-left: 0;
}
/* see gallery_shortcode() in wp-includes/media.php */
</style>
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$posts[0]} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='{$titles[1]}'>
<dt class='gallery-icon'>
<a href='{$permalinks[1]}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$posts[1]}'>
{$titles[1]}
</dd></dl><dl class='gallery-item' role='figure' aria-label='{$titles[2]}'>
<dt class='gallery-icon'>
<a href='{$permalinks[2]}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$posts[2]}'>
{$titles[2]}
</dd></dl><dl class='gallery-item' role='figure' aria-label='{$titles[3]}'>
<dt class='gallery-icon'>
<a href='{$permalinks[3]}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$posts[3]}'>
{$titles[3]}
</dd></dl><br style="clear: both" />
</div>
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery ) );
	}

	/**
	 * Test output gallery with no caption.
	 */
	function test_shortcode_gallery_no_caption() {
		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );
		$args['caption'] = '';

		add_filter( 'use_default_gallery_style', '__return_false', 99 );
		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		$static   = $this->get_gallery_instance_id( $gallery );
		$expected = <<<EOF
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='Gallery image'>
<dt class='gallery-icon'>
<a href='{$permalink}'><img></a>
</dt></dl>
<br style='clear: both' />
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery ) );
	}

	/**
	 * Test linked caption.
	 */
	function test_shortcode_with_linked_caption() {

		$gallery_args = $this->setup_gallery();
		extract( $gallery_args );

		$args['link_caption'] = true;

		add_filter( 'use_default_gallery_style', '__return_false', 99 );
		$gallery = km_rpbt_related_posts_by_taxonomy_gallery( $args, array( $related_post ) );

		$static   = $this->get_gallery_instance_id( $gallery );
		$expected = <<<EOF
<div id='rpbt-related-gallery-$static' class='gallery related-gallery related-galleryid-{$args['id']} gallery-columns-3 gallery-size-thumbnail'><dl class='gallery-item' role='figure' aria-label='{$related_post->post_title}'>
<dt class='gallery-icon'>
<a href='{$permalink}'><img></a>
</dt>
<dd class='wp-caption-text gallery-caption' id='rpbt-related-gallery-$static-{$args['id']}'>
<a href="{$permalink}">{$related_post->post_title}</a>
</dd></dl>
<br style='clear: both' />
</div>
EOF;

		$this->assertEquals( strip_ws( $expected ), strip_ws( $gallery ) );
	}

	/**
	 * Test the output of a regular WordPress gallery.
	 *
	 * If anything changes in the WordPress gallery, change it in the Related posts gallery.
	 */
	function test_wordpress_gallery() {

		$ids = array();
		foreach ( range( 1, 3 ) as $i ) {
			$attachment_id = $this->factory->attachment->create_object(
				"image$i.jpg",
				0,
				array(
					'post_mime_type' => 'image/jpeg',
					'post_type'      => 'attachment',
					'post_excerpt'   => "excerpt $i",
				)
			);
			$metadata      = array_merge(
				array(
					'file' => "image$i.jpg",
				),
				array(
					'width'  => 100,
					'height' => 100,
					'sizes'  => '',
				)
			);
			wp_update_attachment_metadata( $attachment_id, $metadata );
			$ids[] = $attachment_id;

		}

		$ids_str = implode( ',', $ids );

		$blob = <<<BLOB
[gallery ids="$ids_str"]
BLOB;

		$content = do_shortcode( $blob );
		$content = preg_replace( '/<img .*?\/>/', '', $content );

		$version   = $GLOBALS['wp_version'];
		$type_attr = " type='text/css'";

		// Type attribute changed from single to double quotes or
		// was omitted in WP 5.3
		if ( version_compare( $version, '5.3', '>=' ) ) {
			$type_attr = current_theme_supports( 'html5', 'style' ) ? '' : ' type="text/css"';
		}

		$expected = <<<EOF
<style{$type_attr}>
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

		$this->assertEquals( strip_ws( $expected ), strip_ws( $content ) );
	}
}
