<?php
/**
 * Tests for the km_rpbt_query_related_posts() function in functions.php.
 *
 * @group Settings
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
	 * Test sanitizing arguments in a string.
	 *
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

	/**
	 * Test if related_posts is used as type if non valid type was provided.
	 */
	function test_km_rpbt_get_default_settings_wrong_type() {
		$defaults = km_rpbt_get_default_settings( 'no_settings_type' );
		$this->assertTrue( array_key_exists( 'before_related_posts', $defaults ) );
		$this->assertSame( 'related_posts', $defaults['type'] );
	}

	/**
	 * Test if specific widget setting exist
	 */
	function test_km_rpbt_get_default_settings_widget() {
		$defaults = km_rpbt_get_default_settings( 'widget' );
		$this->assertTrue( array_key_exists( 'random', $defaults ) );
	}

	/**
	 * Test if post_types is empty for defaults
	 */
	function test_km_rpbt_get_default_settings_post_type() {
		$defaults = km_rpbt_get_default_settings( 'shortcode' );
		$this->assertEmpty( $defaults['post_types'] );
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

	/**
	 * Test if array with validated booleans are returned.
	 */
	function test_km_rpbt_validate_booleans() {
		$defaults = array(
			'a' => true,
			'b' => true,
			'c' => false,
			'd' => false,
			'e' => true,
			'f' => array(),
			'g' => 'string',
			'h' => null,
			'include_self' => false,
		);

		$expected = $defaults;
		$expected['include_self'] = 'regular_order';

		$args = array(
			'a'            => true,
			'b'            => 'true',
			'c'            => false,
			'd'            => 'false',
			'e'            => 'yes',
			'f'            => array(),
			'g'            => 'string',
			'h'            => null,
			'include_self' => 'regular_order',
		);

		$this->assertSame( $expected, km_rpbt_validate_booleans( $args, $defaults ) );
	}
}
