<?php
/**
 * Tests for gallery in gallery.php
 *
 * @group Taxonomy
 */
class KM_RPBT_Taxonomy_Tests extends KM_RPBT_UnitTestCase {

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

	function test_km_rpbt_get_term_objects() {
		$create_posts = $this->create_posts_with_hierarchical_terms();

		$terms = $create_posts['terms'];
		$term_obj = km_rpbt_get_term_objects( array( $terms[0] ), $taxonomies = 'category' );

		// Objects are returned
		$this->assertTrue( isset( $term_obj[0]->term_id, $term_obj[0]->taxonomy ) );
	}

	function test_km_rpbt_get_term_objects_no_taxonomies() {
		$create_posts = $this->create_posts_with_hierarchical_terms();

		$terms = $create_posts['terms'];
		$term_obj = km_rpbt_get_term_objects( array( $terms[0] ) );

		// Objects are returned even with no taxonomies
		$this->assertTrue( isset( $term_obj[0]->term_id, $term_obj[0]->taxonomy ) );
	}

	function test_km_rpbt_get_term_objects_term_not_in_taxonomy_post_tag() {
		$create_posts = $this->create_posts_with_hierarchical_terms();

		$terms = $create_posts['terms'];
		$term_obj = km_rpbt_get_term_objects( array( $terms[0] ), $taxonomies = 'post_tag' );

		// Term 0 is a not a post_tag taxonomy term.
		$this->assertEmpty( $term_obj );
	}

	function test_km_rpbt_get_term_objects_return_array() {
		$term_obj = km_rpbt_get_term_objects( false, $taxonomies = 'category' );

		// returns array for invalid argument
		$this->assertTrue( is_array( $term_obj ) && empty( $term_obj ) );
	}

	function test_km_rpbt_get_hierarchy_terms_parents() {
		$create_posts = $this->create_posts_with_hierarchical_terms();
		$terms = $create_posts['terms'];

		$term_ids = km_rpbt_get_hierarchy_terms( 'parents', array( $terms[2] ) );

		// Parent terms
		$this->assertEquals( array( $terms[1], $terms[0] ), $term_ids );
	}

	function test_km_rpbt_get_hierarchy_terms_children() {
		$create_posts = $this->create_posts_with_hierarchical_terms();
		$terms = $create_posts['terms'];

		$term_ids = km_rpbt_get_hierarchy_terms( 'children', array( $terms[2] ) );

		// Child terms
		$this->assertEquals( array( $terms[3] ), $term_ids );
	}

	function test_km_rpbt_get_terms_related_true() {
		$create_posts = $this->create_posts_with_terms();
		$terms = $create_posts['tax1_terms'];
		$posts = $create_posts['posts'];

		// Term 0 is assigned to post 0
		$args = array( 'related' => true, 'terms' => $terms[0] );
		$post_terms = km_rpbt_get_terms( $posts[0], 'post_tag', $args );

		// Term 0 is a post_tag
		$this->assertEquals( array( $terms[0] ), $post_terms );
	}

	function test_km_rpbt_get_terms_related_true_term_not_in_taxonomy() {
		$create_posts = $this->create_posts_with_terms();
		$terms = $create_posts['tax1_terms'];
		$posts = $create_posts['posts'];

		// Term 0 is assigned to post 0
		$args = array( 'related' => true, 'terms' => $terms[0] );
		$post_terms = km_rpbt_get_terms( $posts[0], 'category', $args );
		// Term 0 is not a category term
		$this->assertEmpty( $post_terms );

		// Term 3 is not assigned to post 0
		$args = array( 'related' => true, 'include_terms' => $terms[3] );
		$post_terms = km_rpbt_get_terms( $posts[0], 'post_tag', $args );
		// Term 3 is not included because it is not assinged to post 0.
		$this->assertEmpty( $post_terms );
	}

	function test_km_rpbt_get_terms_related_false_with_invalid_taxonomy() {
		$create_posts = $this->create_posts_with_terms();
		$terms = $create_posts['tax1_terms'];
		$posts = $create_posts['posts'];
		// In these tests terms need to exist. The taxonomy does not matter.

		// Term 3 is not assigned to post 0 and invalid taxonomy
		$args = array( 'related' => false, 'terms' => $terms[3], 'include_terms' => $terms[4] );
		$post_terms = km_rpbt_get_terms( $posts[0], 'invalid_tax', $args );
		// Term 3 is included over term 4 from include_terms.
		$this->assertEquals( array( $terms[3] ), $post_terms );

		// Term 3 is not assigned to post 0 and invalid taxonomy
		$args = array( 'related' => false, 'include_terms' => $terms[3] );
		$post_terms = km_rpbt_get_terms( $posts[0], 'invalid_tax', $args );
		// Term 3 is also included with include_terms
		$this->assertEquals( array( $terms[3] ), $post_terms );
	}
}
