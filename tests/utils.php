<?php
class RPBT_Test_Utils {

	private $factory;

	function __construct( $factory = null ) {
		$this->factory = $factory;
	}

	function create_posts_with_terms( $post_type = 'post', $tax1 = 'post_tag', $tax2 = 'category' ) {

		$posts = $this->create_posts( $post_type, 5 );

		// create terms
		$tax1_terms = $this->factory->term->create_many( 5, array( 'taxonomy' => $tax1 ) );

		$post_terms =  array(
			array( $tax1_terms[0], $tax1_terms[1], $tax1_terms[2] ), // post 0
			array( $tax1_terms[2] ),                                 // post 1
			array( $tax1_terms[0], $tax1_terms[2] ),                 // post 2
			array( $tax1_terms[3], $tax1_terms[4], $tax1_terms[2] ), // post 3
			array(),                                                 // post 4
		);

		foreach ( $post_terms as $key => $terms ) {
			if ( !empty( $terms ) ) {
				wp_set_post_terms ( $posts[ $key ], $terms, $tax1 );
			}
		}

		$tax2_terms = $this->factory->term->create_many( 5, array( 'taxonomy' => $tax2 ) );

		$post_terms = array(
			array( $tax2_terms[4] ),                 // post 0
			array( $tax2_terms[4], $tax2_terms[3] ), // post 1
			array(),                                 // post 2
			array( $tax2_terms[3] ),                 // post 3
			array( $tax2_terms[2] ),                 // post 4
		);

		foreach ( $post_terms as $key => $terms ) {
			if ( !empty( $terms ) ) {
				wp_set_post_terms ( $posts[ $key ], $terms, $tax2 );
			}
		}

		return compact( 'posts', 'tax1_terms', 'tax2_terms' );
	}


	function create_posts( $post_type = 'post', $posts_per_page = 5 ) {

		// create posts and increment timestamp
		$posts = array();
		foreach ( range( 1, $posts_per_page ) as $i ) {
			$this->factory->post->create(
				array(
					'post_date' => date( 'Y-m-d H:i:s', time() + $i ),
					'post_type' => $post_type
				) );
		}

		// desc posts
		$posts = get_posts(
			array(
				'post_type' => $post_type,
				'fields'    => 'ids',
				'order'     => 'DESC',
				'orderby'   => 'date'
			) );

		return $posts;
	}
}
