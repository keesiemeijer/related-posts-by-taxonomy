<?php
/**
 * Tests for dependencies and various plugin functions
 *
 * @group Misc
 */
class KM_RPBT_Misc_Tests extends KM_RPBT_UnitTestCase {

	/**
	 * Test if posts are created with the factory class.
	 */
	function test_create_posts() {
		$posts = $this->create_posts();
		$this->assertNotEmpty( $posts );
		return $posts;
	}

	/**
	 * Test if terms are created with the factory class.
	 *
	 * @depends test_create_posts
	 */
	function test_assign_taxonomy_terms( array $posts ) {
		$this->assertNotEmpty( $posts );
		$terms = $this->assign_taxonomy_terms( $posts, 'category', 1 );
		$this->assertNotEmpty( $terms );
	}

	/**
	 * Test if posts and terms are created with the factory class.
	 *
	 * Other test methods that create posts depend on this function to succeed.
	 */
	function test_create_posts_with_terms() {
		$create_posts = $this->create_posts_with_terms();
		$this->assertNotEmpty( $create_posts );
		$this->assertCount( 5, $create_posts['posts'] );
		$this->assertCount( 5, $create_posts['tax1_terms'] );
		$this->assertCount( 5, $create_posts['tax2_terms'] );
	}

	/**
	 * Test if default WordPress taxonomies exist.
	 */
	function test_get_post_taxonomies() {
		$this->assertEquals( array( 'category', 'post_tag', 'post_format' ), get_object_taxonomies( 'post' ) );
	}

	/**
	 * Test output from WordPress function get_posts_by_author_sql().
	 *
	 * Used in the km_rpbt_query_related_posts() function to replace 'post_type = 'post' with 'post_type IN ( ... )
	 */
	function test_get_posts_by_author_sql() {
		$where  = get_posts_by_author_sql( 'post' );
		$this->assertTrue( (bool) preg_match( "/post_type = 'post'/", $where ) );
	}

	/**
	 * Tests for functions that should not output anything.
	 *
	 * @expectedDeprecated km_rpbt_related_posts_by_taxonomy
	 * @expectedDeprecated km_rpbt_related_posts_by_taxonomy_validate_ids
	 * @expectedDeprecated km_rpbt_related_posts_by_taxonomy_template
	 * @expectedDeprecated km_rpbt_shortcode_output
	 * @expectedDeprecated km_rpbt_shortcode_get_related_posts
	 * @expectedDeprecated km_rpbt_related_posts_by_taxonomy_widget
	 * @expectedDeprecated km_rpbt_get_related_post_title_link
	 * @expectedDeprecated km_rpbt_get_shortcode_atts
	 * @expectedDeprecated km_rpbt_get_default_args
	 */
	function test_empty_output() {
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];
		$_posts       = get_posts();
		$args         =  array( 'fields' => 'ids' );
		$taxonomies   = array( 'category', 'post_tag' );

		ob_start();

		// these functions should not output anything.
		$plugin              = km_rpbt_plugin();
		$plugin2             = related_posts_by_taxonomy_init();
		$capability          = km_rpbt_plugin_supports( 'widget' );
		$rel_posts           = km_rpbt_query_related_posts( $posts[0], $taxonomies, $args );
		$rel_posts3          = km_rpbt_get_related_posts( $_posts[0]->ID );
		$cache_posts         = km_rpbt_cache_related_posts( $posts[0], $taxonomies, $args );
		$cache_posts2        = km_rpbt_is_cache_loaded();
		$_args               = km_rpbt_get_query_vars();
		$_args['taxonomies'] = $taxonomies;
		$sanitize            = km_rpbt_sanitize_args( $_args );
		$html                = km_rpbt_get_related_posts_html( $rel_posts3, $_args );
		$html_ajax           = km_rpbt_get_related_posts_ajax_html( $_args );
		$gallery             = km_rpbt_related_posts_by_taxonomy_gallery( array( 'id' => $posts[0] ), array() );
		$widget              = km_rpbt_related_posts_by_taxonomy_widget();
		$shortcode           = km_rpbt_related_posts_by_taxonomy_shortcode( array( 'post_id' => $posts[0] ) );
		$settings            = km_rpbt_get_default_settings( 'shortcode' );
		$valid_settings      = km_rpbt_is_valid_settings_type( 'lala' );
		$get_type            = km_rpbt_get_settings_type($settings);
		$settings['post_id'] = $posts[0];
		$feature_html        = km_rpbt_get_feature_html( 'shortcode', $settings );
		$sc_validate         = km_rpbt_validate_shortcode_atts( $settings );
		$post_types          = km_rpbt_get_post_types( 'post,page' );
		$taxonomies          = km_rpbt_get_taxonomies( $taxonomies );
		$taxonomies2         = km_rpbt_get_public_taxonomies();
		$terms               = km_rpbt_get_terms( $_posts[0]->ID, $taxonomies );
		$value               = km_rpbt_get_comma_separated_values( 'hello,world' );
		$template            = km_rpbt_get_template( 'excerpts' );
		$ids                 = km_rpbt_validate_ids( '1,2,1,string' );
		$classes1            = km_rpbt_get_post_classes( $_posts[0], 'add-this-class' );
		$classes2            = km_rpbt_sanitize_classes( $classes1 );
		$classes3            = km_rpbt_add_post_classes( $_posts, array( 'post_class' => 'add-this-class' ) );
		$classes4            = km_rpbt_post_class();
		$link                = km_rpbt_get_post_link( $_posts[0], true );

		// Deprecated functions
		$rel_posts4 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$id2        = km_rpbt_related_posts_by_taxonomy_validate_ids( '1,2,1,string' );
		$template   = km_rpbt_related_posts_by_taxonomy_template( 'excerpts' );
		$rel_posts2 = km_rpbt_shortcode_get_related_posts( $posts[0], $taxonomies, $args );
		$sc_output  = km_rpbt_shortcode_output( $_posts, $sc_validate );
		$link       = km_rpbt_get_related_post_title_link( $_posts[0], true );
		$sc_args    = km_rpbt_get_shortcode_atts();
		$_args      = km_rpbt_get_default_args();

		$out = ob_get_clean();

		$this->assertEmpty( $out );
	}
}
