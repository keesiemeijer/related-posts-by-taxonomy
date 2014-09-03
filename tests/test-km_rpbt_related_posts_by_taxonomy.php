<?php
class KM_RPBT_Related_Posts_by_Taxonomy_Tests extends WP_UnitTestCase {

	private $utils;

	function setUp() {
		parent::setUp();
		$this->utils = new RPBT_Test_Utils( $this->factory );
	}


	/**
	 * test related posts for post type post
	 */
	function test_post_type_post() {

		$create_posts = $this->utils->create_posts_with_terms();
		$posts = $create_posts['posts'];

		$args =  array( 'fields' => 'ids' );

		// test single taxonomy
		$taxonomies = array( 'post_tag' );

		// test post 0
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[2], $posts[1], $posts[3] ), $rel_post0 );

		// test post 1
		$rel_post1 = km_rpbt_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[2], $posts[3] ), $rel_post1 );

		// test post 2
		$rel_post2 = km_rpbt_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[3] ), $rel_post2 );

		// test post 3
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[2] ), $rel_post3 );

		// test multiple taxonomies
		$taxonomies = array( 'category', 'post_tag' );

		// test post 0
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[2], $posts[3] ), $rel_post0 );

		// test post 1
		$rel_post1 = km_rpbt_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[3], $posts[2] ), $rel_post1 );

		// test post 2
		$rel_post2 = km_rpbt_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[3] ), $rel_post2 );

		// test post 3
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[0], $posts[2] ), $rel_post3 );

		// test post 4
		$rel_post4 = km_rpbt_related_posts_by_taxonomy( $posts[4], $taxonomies, $args );
		$this->assertEmpty( $rel_post4 );
	}


	/**
	 * test related posts for custom post type and custom taxonomy
	 */
	function test_custom_post_type_and_custom_taxonomy() {

		register_post_type( 'rel_cpt', array( 'taxonomies' => array( 'post_tag', 'rel_ctax' ) ) );
		register_taxonomy( 'rel_ctax', 'rel_cpt' );

		$this->assertFalse( is_taxonomy_hierarchical( 'rel_ctax' ) );

		//$this->utils->create_posts_with_terms();
		$create_posts = $this->utils->create_posts_with_terms( 'rel_cpt', 'post_tag', 'rel_ctax' );
		$posts = $create_posts['posts'];

		$args =  array( 'post_types' => array( 'rel_cpt', 'post' ), 'fields' => 'ids', );

		// test single taxonomy
		$taxonomies = array( 'rel_ctax' );

		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[1] ), $rel_post0 );

		// test post 1
		$rel_post1 = km_rpbt_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[3] ), $rel_post1 );

		// test post 2
		$rel_post2 = km_rpbt_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEmpty( $rel_post2 );

		// test post 3
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1] ), $rel_post3 );

		// test multiple taxonomies
		$taxonomies = array( 'rel_ctax', 'post_tag' );

		// test post 0
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[2], $posts[3] ), $rel_post0 );

		// test post 1
		$rel_post1 = km_rpbt_related_posts_by_taxonomy( $posts[1], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[3], $posts[2] ), $rel_post1 );

		// test post 2
		$rel_post2 = km_rpbt_related_posts_by_taxonomy( $posts[2], $taxonomies, $args );
		$this->assertEquals( array( $posts[0], $posts[1], $posts[3] ), $rel_post2 );

		// test post 3
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $posts[3], $taxonomies, $args );
		$this->assertEquals( array( $posts[1], $posts[0], $posts[2] ), $rel_post3 );

		// test post 4
		$rel_post4 = km_rpbt_related_posts_by_taxonomy( $posts[4], $taxonomies, $args );
		$this->assertEmpty( $rel_post4 );
	}

	/**
	 * using wrong function arguments
	 */
	function test_invalid_arguments() {

		$create_posts = $this->utils->create_posts_with_terms();
		$posts = $create_posts['posts'];

		$args =  array( 'fields' => 'ids' );

		// test single taxonomy
		$taxonomies = array( 'post_tag' );

		// not a post ID
		$fail = km_rpbt_related_posts_by_taxonomy( 'not a post ID', $taxonomies, $args );
		$this->assertEmpty( $fail );

		// non existant post ID
		$fail2 = km_rpbt_related_posts_by_taxonomy( 9999999999, $taxonomies, $args );
		$this->assertEmpty( $fail2 );

		// non existant taxonomy
		$fail3 = km_rpbt_related_posts_by_taxonomy( $posts[0], 'not_a_taxonomy', $args );
		$this->assertEmpty( $fail3 );

		// empty string defaults to taxonomy 'category'
		$fail4 = km_rpbt_related_posts_by_taxonomy( $posts[0], '', $args );
		$this->assertEquals( array( $posts[1] ), $fail4 );
	}

	/**
	 * test function arguments
	 */
	function test_arguments() {

		$create_posts = $this->utils->create_posts_with_terms();
		$posts = $create_posts['posts'];

		$_posts = get_posts( array( 'posts__in' => $posts, 'order' => 'post__in' ) );
		$terms = $create_posts['tax1_terms'];
		$terms2 = $create_posts['tax2_terms'];

		$args =  array( 'fields' => 'ids' );
		$taxonomies = array( 'category', 'post_tag' );

		// test exclude_terms
		$_args = array_merge( $args, array( 'exclude_terms' => array( $terms[2] ) ) );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $_args );
		$this->assertEquals( array( $posts[1], $posts[2] ), $rel_post0 );

		// test include_terms
		$_args = array_merge( $args, array( 'include_terms' => array( $terms[0] ) ) );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $_args );
		$this->assertEquals( array( $posts[2] ), $rel_post0 );

		// test include_terms without relation by taxonomy
		$_args = array_merge( $args, array(
				'related' => false,
				'include_terms' => array( $terms2[2], $terms[3] )
			) );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $_args );
		$this->assertEquals( array( $posts[3], $posts[4] ), $rel_post0 );

		// test exclude_posts
		$_args = array_merge( $args, array( 'exclude_posts' => array( $posts[2] ) ) );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $_args );
		$this->assertEquals( array( $posts[1], $posts[3] ), $rel_post0 );

		// test limit_posts
		$_args = array_merge( $args, array( 'limit_posts' => 2 ) );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $_args );
		$this->assertEquals( array( $posts[1], $posts[2] ), $rel_post0 );

		// test posts_per_page
		$_args = array_merge( $args, array( 'posts_per_page' => 1 ) );
		$rel_post3 = km_rpbt_related_posts_by_taxonomy( $posts[3], $taxonomies, $_args );
		$this->assertEquals( array( $posts[1] ), $rel_post3 );

		$slugs = wp_list_pluck( $_posts, 'post_name' );
		$titles =  wp_list_pluck( $_posts, 'post_title' );

		// test post_fields
		$_args = array_merge( $args, array( 'fields' => 'slugs' ) );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $_args );
		$this->assertEquals( array( $slugs[1], $slugs[2], $slugs[3] ), $rel_post0 );

		// test post_fields
		$_args = array_merge( $args, array( 'fields' => 'names' ) );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $_args );
		$this->assertEquals( array( $titles[1], $titles[2], $titles[3] ), $rel_post0 );

		// fake post thumbnails
		add_post_meta( $posts[1], '_thumbnail_id' , 22 ); // fake ID's
		add_post_meta( $posts[3], '_thumbnail_id' , 33 );

		// test post_thumbnail
		$_args = array_merge( $args, array( 'post_thumbnail' => true ) );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $_args );
		$this->assertEquals( array( $posts[1], $posts[3] ), $rel_post0 );

		// test limit_month
		list( $date, $time ) = explode( ' ', $_posts[2]->post_date );
		$mypost = array(
			'ID' =>  $posts[2],
			'post_date' => date( 'Y-m-d H:i:s', strtotime( $date .' -2 month' ) ),
		);
		wp_update_post( $mypost );

		$_args = array_merge( $args, array( 'limit_month' => 1 ) );
		$rel_post0 = km_rpbt_related_posts_by_taxonomy( $posts[0], $taxonomies, $_args );
		$this->assertEquals( array( $posts[1], $posts[3] ), $rel_post0 );
	}
}
