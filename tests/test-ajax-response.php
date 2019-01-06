<?php
/**
 * Ajax tests
 *
 * @group Ajax
 */
class KM_RPBT_Ajax_Tests extends KM_RPBT_Ajax_UnitTestCase {

	function setup_ajax() {
		// Activate ajax_query
		add_filter( 'related_posts_by_taxonomy_lazy_loading', '__return_true' );

		// Setup plugin with ajax_query activated.
		$ajax = new Related_Posts_By_Taxonomy_Plugin();
		$ajax->lazy_loading_init();
	}

	function get_args( $post_id, $type = 'shortcode' ) {
		$args = array(
			'post_id'   => $post_id,
			'post_type' => 'post',
		);
		$defaults = km_rpbt_get_default_settings( $type );
		return array_merge( $defaults, $args );
	}

	/**
	 * Test response from ajax query
	 */
	public function test_ajax_query_responce() {
		$this->setup_ajax();
		$create_posts = $this->create_posts_with_terms();
		$posts        = $create_posts['posts'];

		// get post ids array and permalinks array
		$_posts     = get_posts(
			array(
				'posts__in' => $posts,
				'order' => 'post__in',
			)
		);
		$ids        = wp_list_pluck( $_posts, 'ID' );
		$permalinks = array_map( 'get_permalink', $ids );

		// expected related posts are post 1,2,3
		$expected = <<<EOF
<div class="rpbt_shortcode">
<h3>Related Posts</h3>
<ul>
<li>
<a href="{$permalinks[1]}">{$_posts[1]->post_title}</a>
</li>
<li>
<a href="{$permalinks[2]}">{$_posts[2]->post_title}</a>
</li>
<li>
<a href="{$permalinks[3]}">{$_posts[3]->post_title}</a>
</li>
</ul>
</div>
EOF;
		$_POST['nonce'] = wp_create_nonce( 'rpbt_lazy_loading_nonce' );
		$_POST['args']  = $this->get_args( $create_posts['posts'][0] );

		// Make the request
		try {
			$this->_handleAjax( 'rpbt_lazy_loading' );
		} catch ( WPAjaxDieContinueException $e ) {
		}

		$response = json_decode( $this->_last_response );

		$this->assertTrue( $response->success );
		$this->assertEquals( strip_ws( $expected ), strip_ws( $response->data ) );
	}
}
