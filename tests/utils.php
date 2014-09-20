<?php
class RPBT_Test_Utils {

	private $factory;

	function __construct( $factory = null ) {
		$this->factory = $factory;
	}

	/**
	 * Creates 5 posts and assigns terms from two taxonomies.
	 *
	 * @param string  $post_type Post type.
	 * @param string  $tax1      First taxonomy name.
	 * @param string  $tax2      Second taxonomy name
	 * @return array             Array with post ids and term ids from both taxonomies
	 */
	function create_posts_with_terms( $post_type = 'post', $tax1 = 'post_tag', $tax2 = 'category' ) {

		$posts = $this->create_posts( $post_type, 5 );

		// bail if no posts were created
		if ( count( $posts ) !== 5 ) {
			return array();
		}

		// create terms taxonomy 1
		$tax1_terms = $this->assign_taxonomy_terms( $posts, $tax1, 1 );

		// create terms taxonomy 2
		$tax2_terms = $this->assign_taxonomy_terms( $posts, $tax2, 2 );

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
