<?php

/**
 * Tests for the km_rpbt_query_related_posts() function in functions.php.
 *
 * @group Template
 */
class KM_RPBT_Template_Loader_Tests extends KM_RPBT_UnitTestCase {
	private $path;
	private $theme;
	private $filter_dir;

	public function get_theme_dir() {
		return trailingslashit( WP_CONTENT_DIR ) . 'themes/rpbt-test-theme';
	}

	public static function wpSetUpBeforeClass( WP_UnitTest_Factory $factory ) {
		$test_themes_dir = trailingslashit( RPBT_TEST_THEMES_DIR ) . 'themes/rpbt-test-theme';
		$sym_themes_dir  = trailingslashit( WP_CONTENT_DIR ) . 'themes/rpbt-test-theme';

		symlink( $test_themes_dir, $sym_themes_dir );
	}

	public static function wpTearDownAfterClass() {
		unlink( trailingslashit( WP_CONTENT_DIR ) . 'themes/rpbt-test-theme' );
	}

	/**
	 * Set up.
	 */
	public function set_up() {
		parent::set_up();
		$path             = pathinfo( __DIR__ );
		$this->path       = $path['dirname'];
		$this->filter_dir = '';

		$this->theme = trailingslashit( get_stylesheet_directory() );
	}

	/**
	 * Reset post type on teardown.
	 */
	public function tear_down() {
		parent::tear_down();
		$this->unlink_templates();
		remove_filter( 'related_posts_by_taxonomy_template', array( $this, 'custom_template_file' ), 10, 2 );
		remove_filter( 'related_posts_by_taxonomy_template_directory', '__return_empty_string', 10, 3 );
		remove_filter( 'related_posts_by_taxonomy_template_directory', array( $this, 'custom_directory' ), 10, 3 );
	}

	public function test_theme() {
		$my_theme = wp_get_theme();
		$this->assertSame( 'RPBT Dummy Test Theme', $my_theme->get( 'Name' ) );
	}

	/**
	 * Test dummy theme templates
	 *
	 * @depends KM_RPBT_Template_Loader_Tests::test_theme
	 */
	public function test_theme_templates() {
		$expected = array(
			'index.php',
			'style.css',
			'related-post-plugin',
			'custom-directory',
		);

		$actual = array_map( 'basename', glob( $this->theme . '*' ) );
		sort( $expected );
		sort( $actual );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_template_loader_default_template() {
		// Wrong templates should default to links template.
		// expected to load template from plugin
		$expected = $this->path . '/templates/related-posts-links.php';
		$actual   = km_rpbt_get_template( 'not-a-template' );

		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_template_loader_excerpt_template() {
		// expected to load template from plugin
		$expected = $this->path . '/templates/related-posts-excerpts.php';
		$actual   = km_rpbt_get_template( 'excerpts' );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_template_loader_post_template() {
		// expected to load template from plugin
		$expected = $this->path . '/templates/related-posts-posts.php';
		$actual   = km_rpbt_get_template( 'posts' );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_template_loader_thumbnail_template() {
		// expected to load template from plugin
		$expected = $this->path . '/templates/related-posts-thumbnails.php';
		$actual   = km_rpbt_get_template( 'thumbnails' );
		$this->assertEquals( $expected, $actual );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_template_from_theme() {
		// add template to the dummy theme
		$this->create_templates( array( 'related-post-plugin/related-posts-links' ) );

		// expected to load template from theme
		$expected = $this->get_theme_dir() . '/related-post-plugin/related-posts-links.php';
		$template = km_rpbt_get_template();
		$this->assertEquals( $expected, $template );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_custom_theme_template() {
		// filter to load custom theme template
		add_filter( 'related_posts_by_taxonomy_template', array( $this, 'custom_template_file' ), 10, 2 );

		// add template to the dummy theme
		$this->create_templates( array( 'related-post-plugin/custom-template' ) );

		// expected to load template from theme
		$expected = $this->get_theme_dir() . '/related-post-plugin/custom-template.php';
		$template = km_rpbt_get_template( false, 'shortcode' );

		$this->assertEquals( $expected, $template );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_custom_template_in_root_folder_theme() {

		// Filter to load custom theme directory
		add_filter( 'related_posts_by_taxonomy_template_directory', '__return_empty_string', 10, 3 );

		// filter to load custom theme template
		add_filter( 'related_posts_by_taxonomy_template', array( $this, 'custom_template_file' ), 10, 2 );

		// add custom template to the dummy theme
		$this->create_templates( array( 'custom-template' ) );

		// expected to load template from theme
		$expected = $this->get_theme_dir() . '/custom-template.php';
		$template = km_rpbt_get_template( false, 'shortcode' );

		$this->assertEquals( $expected, $template );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_custom_template_in_custom_directory() {
		$this->filter_dir = 'custom-directory/';

		// Filter to load custom theme directory
		add_filter( 'related_posts_by_taxonomy_template_directory', array( $this, 'custom_directory' ), 10, 3 );

		// filter to load custom theme template
		add_filter( 'related_posts_by_taxonomy_template', array( $this, 'custom_template_file' ), 10, 2 );

		// add custom template to the dummy theme
		$this->create_templates( array( 'custom-directory/custom-template' ) );

		// expected to load template from theme
		$expected = $this->get_theme_dir() . '/custom-directory/custom-template.php';
		$template = km_rpbt_get_template( false, 'shortcode' );

		// reset directory
		$this->filter_dir = '';

		$this->assertEquals( $expected, $template );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_custom_template_in_nested_custom_directory() {
		$this->filter_dir = 'custom-directory/custom-directory-nested';

		// Filter to load custom theme directory
		add_filter( 'related_posts_by_taxonomy_template_directory', array( $this, 'custom_directory' ), 10, 3 );

		// filter to load custom theme template
		add_filter( 'related_posts_by_taxonomy_template', array( $this, 'custom_template_file' ), 10, 2 );

		// add custom template to the dummy theme
		$this->create_templates( array( 'custom-directory/custom-directory-nested/custom-template' ) );

		// expected to load template from theme
		$expected = $this->get_theme_dir() . '/custom-directory/custom-directory-nested/custom-template.php';
		$template = km_rpbt_get_template( false, 'shortcode' );

		// reset directory
		$this->filter_dir = '';

		$this->assertEquals( $expected, $template );
	}

	/**
	 * Test if correct template was found.
	 */
	public function test_custom_template_in_directory_not_existing() {
		$this->filter_dir = 'non-existing-directory';

		// Filter to load custom theme directory
		add_filter( 'related_posts_by_taxonomy_template_directory', array( $this, 'custom_directory' ), 10, 3 );

		// filter to load custom theme template
		add_filter( 'related_posts_by_taxonomy_template', array( $this, 'custom_template_file' ), 10, 2 );

		// expected to load template from plugin
		$expected = $this->path . '/templates/related-posts-links.php';
		$template = km_rpbt_get_template( false, 'shortcode' );

		// reset directory
		$this->filter_dir = '';

		$this->assertEquals( $expected, $template );
	}



	public function create_templates( $templates ) {
		foreach ( $templates as $template ) {
			if ( ! file_exists( $this->theme . "{$template}.php" ) ) {
				$file = fopen( $this->theme . "{$template}.php", 'w' );
				fclose( $file );
			}
		}
	}

	public function unlink_templates() {
		// theme cleanup
		$templates = array(
			'related-post-plugin/related-posts-links',
			'related-post-plugin/custom-template',
			'custom-directory/custom-template',
			'custom-directory/custom-directory-nested/custom-template',
			'custom-template',
		);

		foreach ( $templates as $template ) {

			if ( file_exists( $this->theme . "{$template}.php" ) ) {

				unlink( $this->theme . "{$template}.php" );
			}
		}
	}

	public function custom_directory( $template, $type, $format ) {
		return $this->filter_dir;
	}

	public function custom_template_file( $template, $type ) {
		return 'custom-template.php';
	}
}
