<?php

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

require_once $_tests_dir . '/includes/functions.php';

function _wph_test_manually_load_plugin() {
	require_once __DIR__ . '/../../wp-hashids.php';
}
tests_add_filter( 'muplugins_loaded', '_wph_test_manually_load_plugin' );

require_once $_tests_dir . '/includes/bootstrap.php';
require_once __DIR__ . '/class-rewrite-test-case.php';
