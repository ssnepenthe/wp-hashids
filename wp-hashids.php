<?php
/**
 * Hashids implementation for WordPress.
 *
 * @package wp-hashids
 */

/**
 * Plugin Name: WP Hashids
 * Plugin URI: https://github.com/ssnepenthe/wp-hashids
 * Description: <a href="http://hashids.org/php/">Hashids</a> implementation for WordPress.
 * Version: 0.1.0
 * Author: Ryan McLaughlin
 * Author URI: https://github.com/ssnepenthe
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

/**
 * Require a file (once) if it exists.
 *
 * @param  string $file Path to the file you wish to check.
 */
function _wph_require_if_exists( $file ) {
	if ( file_exists( $file ) ) {
		require_once $file;
	}
}

_wph_require_if_exists( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

$wph_checker = WP_Requirements\Plugin_Checker::make( 'WP Hashids', __FILE__ )
	// Uses scalar type hints, depends on ssnepenthe/metis.
	->php_at_least( '7.0' )
	// Uses register_setting() with args array.
	->wp_at_least( '4.7' )
	// Hashids lib must be loaded.
	->class_exists( 'Hashids\\Hashids' )
	// Hashids lib requires one of bcmath or gmp.
	->add_check( function() {
		return function_exists( 'bcadd' ) || function_exists( 'gmp_add' );
	}, 'One of the BCMath or GMP extensions is required' );

if ( $wph_checker->requirements_met() ) {
	$wph_plugin = new Metis\Package( [
		'Metis\\View\\View_Provider',
		'WP_Hashids\\Admin_Provider',
		'WP_Hashids\\Hashids_Provider',
		'WP_Hashids\\Plugin_Provider',
	] );
	$wph_plugin->init();
} else {
	$wph_checker->deactivate_and_notify();
}

unset( $wph_checker, $wph_plugin );
