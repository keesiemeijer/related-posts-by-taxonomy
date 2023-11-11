<?php
$_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

require_once 'vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../related-posts-by-taxonomy.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
require dirname( __FILE__ ) . '/../tests/testcase.php';
require dirname( __FILE__ ) . '/../tests/testcase-ajax.php';
