<?php
$_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

if ( class_exists( 'PHPUnit\Runner\Version' ) && version_compare( PHPUnit\Runner\Version::id(), '6.0', '>=' ) ) {
	class_alias( 'PHPUnit\Framework\TestCase', 'PHPUnit_Framework_TestCase' ); 
}

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../related-posts-by-taxonomy.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
require dirname( __FILE__ ) . '/../tests/utils.php';