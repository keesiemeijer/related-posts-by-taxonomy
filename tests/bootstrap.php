<?php
$_tests_dir = '/tmp/wordpress-tests-lib';

require_once $_tests_dir . '/includes/functions.php';

require_once 'vendor/yoast/phpunit-polyfills/phpunitpolyfills-autoload.php';

define( 'RPBT_TEST_THEMES_DIR', dirname( __DIR__ ) . '/tests' );

function _manually_load_plugin() {
	switch_theme( 'rpbt-test-theme' );
	require __DIR__ . '/../related-posts-by-taxonomy.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
require __DIR__ . '/../tests/testcase.php';
require __DIR__ . '/../tests/testcase-ajax.php';
