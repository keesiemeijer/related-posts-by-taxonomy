<?php
/**
 * Tests for the km_rpbt_query_related_posts() function in functions.php.
 */
class KM_RPBT_Settings_Tests extends KM_RPBT_UnitTestCase {

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
	 * Test if args are not changed (due to debugging).
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

		ksort( $expected );
		ksort( $args );

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

		$sanitized_args = km_rpbt_sanitize_args( $args );

		ksort( $expected );
		ksort( $sanitized_args );

		$this->assertEquals( $expected, $sanitized_args );
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
		$sanitized_args = km_rpbt_sanitize_args( $args );

		ksort( $expected );
		ksort( $sanitized_args );

		$this->assertEquals( $expected, $sanitized_args );
	}
}
