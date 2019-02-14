<?php
/**
 * Tests for gallery in gallery.php
 *
 * @group Taxonomy
 */
class KM_RPBT_Taxonomy_Tests extends KM_RPBT_UnitTestCase {

	function test_km_rpbt_get_term_objects() {
		$create_posts = $this->create_posts_with_hierarchical_terms();

		$terms = $create_posts['terms'];
		$term_obj = km_rpbt_get_term_objects( array( $terms[0] ), $taxonomies = 'category' );
		$this->assertTrue( isset( $term_obj[0]->term_id, $term_obj[0]->taxonomy ) );
	}

	function test_km_rpbt_get_term_objects_no_taxonomies() {
		$create_posts = $this->create_posts_with_hierarchical_terms();

		$terms = $create_posts['terms'];
		$term_obj = km_rpbt_get_term_objects( array( $terms[0] ) );
		$this->assertTrue( isset( $term_obj[0]->term_id, $term_obj[0]->taxonomy ) );
	}

	function test_km_rpbt_get_term_objects_term_not_in_taxonomy_post_tag() {
		$create_posts = $this->create_posts_with_hierarchical_terms();

		$terms = $create_posts['terms'];
		$term_obj = km_rpbt_get_term_objects( array( $terms[0] ), $taxonomies = 'post_tag' );
		$this->assertEmpty( $term_obj );
	}

	function test_km_rpbt_get_term_objects_return_array() {
		$term_obj = km_rpbt_get_term_objects( false, $taxonomies = 'category' );
		$this->assertTrue( is_array( $term_obj ) && empty( $term_obj ) );
	}

	function test_km_rpbt_get_hierarchy_terms_parents() {
		$create_posts = $this->create_posts_with_hierarchical_terms();
		$terms = $create_posts['terms'];

		$term_ids = km_rpbt_get_hierarchy_terms( 'parents', array( $terms[2] ) );
		$this->assertEquals( array( $terms[1], $terms[0] ), $term_ids );
	}

	function test_km_rpbt_get_hierarchy_terms_children() {
		$create_posts = $this->create_posts_with_hierarchical_terms();
		$terms = $create_posts['terms'];

		$term_ids = km_rpbt_get_hierarchy_terms( 'children', array( $terms[2] ) );
		$this->assertEquals( array( $terms[3] ), $term_ids );
	}
}
