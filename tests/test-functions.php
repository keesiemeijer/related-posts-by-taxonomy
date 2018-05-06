<?php
/**
 * Tests for the km_rpbt_query_related_posts() function in functions.php.
 */
class KM_RPBT_Functions_Tests extends KM_RPBT_UnitTestCase {

	function get_default_sanitized_args() {
		return array(
			'post_types'     => array( 'post' ),
			'posts_per_page' => 5,
			'order'          => 'DESC',
			'fields'         => '',
			'limit_posts'    => -1,
			'limit_year'     => 0,
			'limit_month'    => 0,
			'orderby'        => 'post_date',
			'exclude_terms'  => array(),
			'include_terms'  => array(),
			'exclude_posts'  => array(),
			'post_thumbnail' => false,
			'related'        => true,
			'public_only'    => false,
			'include_self'   => false,
			'terms'          => array(),
		);
	}

	/**
	 * Test if km_rpbt_plugin() returns an object.
	 *
	 * Used in the km_rpbt_query_related_posts() function to replace 'post_type = 'post' with 'post_type IN ( ... )
	 */
	function test_km_rpbt_plugin() {
		$plugin = km_rpbt_plugin();
		$this->assertTrue( $plugin instanceof Related_Posts_By_Taxonomy_Defaults );
	}

	/**
	 * Test if args are not changed due to debugging.
	 */
	function test_km_rpbt_get_query_vars() {
		$expected = array(
			'post_types'     => 'post',
			'posts_per_page' => 5,
			'order'          => 'DESC',
			'fields'         => '',
			'limit_posts'    => -1,
			'limit_year'     => '',
			'limit_month'    => '',
			'orderby'        => 'post_date',
			'exclude_terms'  => '',
			'include_terms'  => '',
			'exclude_posts'  => '',
			'post_thumbnail' => false,
			'related'        => true,
			'public_only'    => false,
			'include_self'   => false,
			'terms'          => '',
		);

		$args = km_rpbt_get_query_vars();

		$this->assertEquals( $expected, $args );
	}

	/**
	 * Test sanitizing arguments.
	 */
	function test_km_rpbt_sanitize_args_array() {
		$expected = $this->get_default_sanitized_args();
		$sanitized = array(
			'posts_per_page' => 0,
			'order'          => '',
			'orderby'        => '',
			'exclude_terms'  => array( 1, 2, 3 ),
			'related'        => false,
		);

		$expected = array_merge( $expected, $sanitized );

		$args = array(
			'post_types'     => false,
			'posts_per_page' => false,
			'order'          => false,
			'fields'         => false,
			'limit_posts'    => -1,
			'limit_year'     => 'false',
			'limit_month'    => false,
			'orderby'        => array( false ),
			'exclude_terms'  => array( 1, 2, 'string', false, 3, 2 ),
			'include_terms'  => false,
			'exclude_posts'  => null,
			'post_thumbnail' => 'false',
			'related'        => 'lalala',
			'public_only'    => array(),
			'include_self'   => 'no',
			'terms'          => 'term-a,term-b,',
		);

		$this->assertEquals( $expected, km_rpbt_sanitize_args( $args ) );
	}

	/**
	 * Test sanitizing arguments.
	 */
	function test_km_rpbt_sanitize_args_string() {
		$expected = $this->get_default_sanitized_args();
		$sanitized = array(
			'posts_per_page' => 3,
			'fields'         => 'ids',
			'public_only'    => true,
			'terms'          => array( 1, 2, 3 ),
		);
		$expected = array_merge( $expected, $sanitized );

		$args = 'posts_per_page=3&fields=ids&public_only=true&terms=1,2,2,false,3';
		$this->assertEquals( $expected, km_rpbt_sanitize_args( $args ) );
	}

	/**
	 * Test validating post types.
	 */
	function test_km_rpbt_get_post_types() {
		$post_types = 'post ,lol, post';
		$this->assertEquals( array( 'post' ), km_rpbt_get_post_types( $post_types ) );
	}

	/**
	 * Test validating custom post types.
	 */
	function test_km_rpbt_get_post_types_custom() {
		register_post_type( 'cpt' );
		$expected = array( 'cpt', 'post' );
		$post_types = km_rpbt_get_post_types( $expected );
		sort( $post_types );
		$this->assertEquals( $expected, km_rpbt_get_post_types( $post_types ) );
	}

	/**
	 * Test validating taxonomies.
	 */
	function test_km_rpbt_get_taxonomies() {
		$taxonomies = 'category ,lol, category';
		$this->assertEquals( array( 'category' ), km_rpbt_get_taxonomies( $taxonomies ) );
	}

	/**
	 * Test validating custom taxonomies.
	 */
	function test_km_rpbt_get_taxonomies_custom() {
		register_taxonomy( 'ctax', 'post' );
		$expected = array( 'category', 'ctax' );
		$taxonomies = km_rpbt_get_taxonomies( $expected );
		sort( $taxonomies );
		$this->assertEquals( $expected, $taxonomies );
	}

	/**
	 * Test values separated by.
	 */
	function test_km_rpbt_get_comma_separated_values() {
		$expected = array( 'lol', 'hihi' );
		$value = ' lol, hihi,lol';
		$this->assertEquals( $expected, km_rpbt_get_comma_separated_values( $value ) );

		$value = array( ' lol', 'hihi ', ' lol ' );
		$this->assertEquals( $expected, km_rpbt_get_comma_separated_values( $value ) );
	}

	/**
	 * Test if array with validated ids are returned.
	 */
	function test_km_rpbt_validate_ids() {

		$ids = array( 1, false, 'string', 2, 0, 1, 3 );

		$validated_ids = km_rpbt_validate_ids( $ids );
		$this->assertEquals( array( 1, 2, 3 ), $validated_ids );

		$ids = '1,string,2,0,###,2,3';
		$validated_ids = km_rpbt_validate_ids( $ids );
		$this->assertEquals( array( 1, 2, 3 ), $validated_ids );
	}
}
