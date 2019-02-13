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
	function test_wp_get_object_taxonomies() {
		$this->assertEquals( array( 'category', 'post_tag', 'post_format' ), get_object_taxonomies( 'post' ) );
	}

	/**
	 * Test output from WordPress function get_posts_by_author_sql().
	 *
	 * Used in the km_rpbt_query_related_posts() function to replace 'post_type = 'post' with 'post_type IN ( ... )
	 */
	function test_wp_get_posts_by_author_sql() {
		$where  = get_posts_by_author_sql( 'post' );
		$this->assertTrue( (bool) preg_match( "/post_type = 'post'/", $where ) );
	}

	/**
	 * Test get_meta_sql()
	 *
	 * Used for the meta query in km_rpbt_query_related_posts()
	 */
	function test_wp_get_meta_sql_value_comma_separated_string_to_array() {
		$meta_query_obj = new WP_Meta_Query();
		$meta_query = array(
			array(
				'key'       => 'my_key',
				'value'     => '10,20', // string (e.g. shortcode value)
				'compare'   => 'BETWEEN',
				'meta_type' => 'NUMERIC'
			)
		);
		global $wpdb;
		$meta_sql = get_meta_sql( $meta_query, 'post', $wpdb->posts, 'ID' );
		$this->assertTrue( ( false !== strpos( $meta_sql['where'], "postmeta.meta_value BETWEEN '10' AND '20'" ) ) );
	}

	/**
	 * Test WP_Meta_Query::parse_query_vars()
	 *
	 * Used for the meta query in km_rpbt_query_related_posts()
	 */
	function test_wp_meta_query_parse_query_vars_empty_string() {
		$meta_query_obj = new WP_Meta_Query();
		$meta_query_obj->parse_query_vars( '' );

		// should return empty array
		$this->assertTrue( is_array( $meta_query_obj->queries ) && empty( $meta_query_obj->queries ) );
	}

	/**
	 * Test WP_Meta_Query::parse_query_vars()
	 *
	 * Used for the meta query in km_rpbt_query_related_posts()
	 */
	function test_wp_meta_query_parse_query_vars_default_settings() {
		$meta_query_obj = new WP_Meta_Query();
		$args = km_rpbt_get_default_settings( 'shortcode' );
		$this->assertTrue( array_key_exists( 'meta_key', $args ) );
		$meta_query_obj->parse_query_vars( $args );

		// should return empty array
		$this->assertTrue( is_array( $meta_query_obj->queries ) && empty( $meta_query_obj->queries ) );
	}

	/**
	 * Test WP_Meta_Query::parse_query_vars()
	 *
	 * Used for the meta query in km_rpbt_query_related_posts()
	 */
	function test_wp_meta_query_parse_query_vars_with_meta_key() {
		$meta_query_obj = new WP_Meta_Query();
		$args = array(
			'meta_key' => 'my_key',
		);
		$meta_query_obj->parse_query_vars( $args );
		$expected = array(
			array(
				'key' => 'my_key',
			),
			'relation' => 'OR',
		);

		$this->assertSame( $expected, $meta_query_obj->queries );
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
	 * @expectedDeprecated km_rpbt_add_post_classes
	 */
	function test_empty_output() {
		$create_posts = $this->create_posts_with_terms();

		$posts        = $create_posts['posts'];
		$terms        = $create_posts['tax2_terms'];
		$_posts       = get_posts();
		$args         = array( 'fields' => 'ids' );
		$taxonomies   = array( 'category', 'post_tag' );
		$attachment_id = $this->create_image();

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
		$html_ajax           = km_rpbt_get_lazy_loading_html( $_args );
		$widget              = km_rpbt_related_posts_by_taxonomy_widget();
		$shortcode           = km_rpbt_related_posts_by_taxonomy_shortcode( array( 'post_id' => $posts[0] ) );
		$settings            = km_rpbt_get_default_settings( 'shortcode' );
		$valid_settings      = km_rpbt_is_valid_settings_type( 'lala' );
		$get_type            = km_rpbt_get_settings_type( $settings );
		$settings['post_id'] = $posts[0];
		$feature_html        = km_rpbt_get_feature_html( 'shortcode', $settings );
		$sc_validate         = km_rpbt_validate_shortcode_atts( $settings );
		$post_types          = km_rpbt_get_post_types( 'post,page' );
		$taxonomies          = km_rpbt_get_taxonomies( $taxonomies );
		$taxonomies2         = km_rpbt_get_public_taxonomies();
		$parent_terms        = km_rpbt_get_parent_terms( $terms, 'category' );
		$child_terms         = km_rpbt_get_child_terms( $terms, 'category' );
		$terms               = km_rpbt_get_terms( $_posts[0]->ID, $taxonomies );
		$value               = km_rpbt_get_comma_separated_values( 'hello,world' );
		$template            = km_rpbt_get_template( 'excerpts' );
		$ids                 = km_rpbt_validate_ids( '1,2,1,string' );
		$classes1            = km_rpbt_get_post_classes( $_posts[0], 'add-this-class' );
		$classes2            = km_rpbt_sanitize_classes( $classes1 );
		$classes4            = km_rpbt_post_class();
		$link                = km_rpbt_get_post_link( $_posts[0], true );
		$link2               = km_rpbt_get_permalink( $_posts[0] );
		$post_thumb          = set_post_thumbnail ( $posts[2], $attachment_id );
		$rel_posts4          = km_rpbt_get_related_posts( $posts[0], array( 'post_thumbnail' => true, 'fields' => 'ids' ) );
		$gallery_args        = km_kpbt_get_default_gallery_args();
		$gallery             = km_rpbt_related_posts_by_taxonomy_gallery( array( 'id' => $posts[0] ), array( $rel_posts4[0] ) );
		$gallery_shortcode   = km_kpbt_get_gallery_shortcode_html( $rel_posts4, $gallery_args, 1 );
		$gallery_block       = km_rpbt_get_gallery_editor_block_html( $rel_posts4, $gallery_args );
		$gallery_validate    = km_rpbt_validate_gallery_args( $gallery_args );
		$gallery_class       = km_rpbt_get_gallery_post_class( $rel_posts4[0], $gallery_args, 'my-class' );
		$gallery_image       = km_rpbt_get_gallery_image_link( $attachment_id, $_posts[2], $gallery_args );
		$gallery_caption     = km_rpbt_get_gallery_image_caption( $attachment_id, $_posts[2] );

		// Deprecated functions
		$classes3   = km_rpbt_add_post_classes( $_posts, array( 'post_class' => 'add-this-class' ) );
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
