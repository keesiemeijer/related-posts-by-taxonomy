<?php
/**
 * Tests for the km_rpbt_query_related_posts() function in functions.php.
 *
 * @group Template
 */
class KM_RPBT_Template_Loader_Tests extends KM_RPBT_UnitTestCase {
	private $path;

	/**
	 * Set up.
	 */
	function set_up() {
		parent::set_up();
		$path       = pathinfo( dirname(  __FILE__  ) );
		$this->path = $path['dirname'];
	}

	/**
	 * Test if correct template was found.
	 */
	function test_template_loader_default_template() {
		// Wrong templates should default to links template.
		$template = km_rpbt_get_template( 'not-a-template' );
		$path3    = $this->path . '/templates/related-posts-links.php';
		$this->assertEquals( $path3 , $template );
	}

	/**
	 * Test if correct template was found.
	 */
	function test_template_loader_excerpt_template() {
		// get the excerpts template
		$template = km_rpbt_get_template( 'excerpts' );
		$path1    = $this->path . '/templates/related-posts-excerpts.php';
		$this->assertEquals( $path1 , $template );
	}

	/**
	 * Test if correct template was found.
	 */
	function test_template_loader_post_template() {
		// get the posts template
		$template = km_rpbt_get_template( 'posts' );
		$path2    = $this->path . '/templates/related-posts-posts.php';
		$this->assertEquals( $path2 , $template );
	}

	/**
	 * Test if correct template was found.
	 */
	function test_template_loader_thumbnail_template() {
		// get the thumbnail template
		$template = km_rpbt_get_template( 'thumbnails' );
		$path2    = $this->path . '/templates/related-posts-thumbnails.php';
		$this->assertEquals( $path2 , $template );
	}
}
