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

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

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

/**
 * Plugin instance getter.
 *
 * @return WP_Hashids\Plugin
 */
function _wph_instance() {
	static $instance = null;

	if ( is_null( $instance ) ) {
		$instance = new WP_Hashids\Plugin( [
			'dir' => __DIR__,
			'file' => __FILE__,
			'name' => 'WP Hashids',
			'version' => '0.1.0',
		] );

		$instance->register( new WP_Hashids\Admin_Provider );
		$instance->register( new WP_Hashids\Hashids_Provider );
		$instance->register( new WP_Hashids\Plates_Provider );
		$instance->register( new WP_Hashids\Plugin_Provider );
	}

	return $instance;
}

/**
 * Plugin initialization function.
 *
 * @return void
 */
function _wph_init() {
	static $initialized = false;

	if ( $initialized ) {
		return;
	}

	$checker = new WP_Requirements\Plugin_Checker( 'WP Hashids', __FILE__ );

	// Constant arrays.
	$checker->php_at_least( '5.6' );

	// Uses register_setting() with args array.
	$checker->wp_at_least( '4.7' );

	// Hashids lib must be loaded.
	$checker->class_exists( 'Hashids\\Hashids' );

	// Plates lib must be loaded.
	$checker->class_exists( 'League\\Plates\\Engine' );

	// Pimple lib must be loaded.
	$checker->class_exists( 'Pimple\\Container' );

	// Hashids lib requires one of bcmath or gmp.
	$checker->add_check( function() {
		return function_exists( 'bcadd' ) || function_exists( 'gmp_add' );
	}, 'One of the BCMath or GMP extensions is required' );

	if ( ! $checker->requirements_met() ) {
		return $checker->deactivate_and_notify();
	}

	$instance = _wph_instance();

	register_deactivation_hook(
		$instance['file'],
		[ $instance, 'deactivate' ]
	);

	add_action( 'plugins_loaded', [ $instance, 'boot' ] );

	$initialized = true;
}

_wph_require_if_exists( __DIR__ . '/vendor/autoload.php' );
_wph_init();
