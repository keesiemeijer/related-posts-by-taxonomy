<?php
class KM_RPBT_Ajax_UnitTestCase extends WP_Ajax_UnitTestCase {
	public $testcase;
	function set_up() {
		parent::set_up();
		$this->testcase = new KM_RPBT_UnitTestCase();
	}

	function create_posts_with_terms( $post_type = 'post', $tax1 = 'post_tag', $tax2 = 'category' ) {
		$this->testcase->set_up();
		return $this->testcase->create_posts_with_terms( $post_type, $tax1, $tax2);
	}
}
