<?php
/**
 * The plugin uninstall script.
 *
 * @package wp-hashids
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

/**
 * The plugin uninstaller.
 *
 * @return void
 */
function _wph_uninstall() {
	$options = [
		'wp_hashids_alphabet',
		'wp_hashids_min_length',
		'wp_hashids_salt',
	];

	foreach ( $options as $option ) {
		delete_option( $option );
	}
}

_wph_uninstall();
