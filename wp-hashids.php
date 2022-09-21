<?php
/**
 * Hashids implementation for WordPress.
 *
 * @package wp-hashids
 */

use Psr\Container\ContainerInterface;
use WP_Hashids\Plugin;

/**
 * Plugin Name: WP Hashids
 * Plugin URI: https://github.com/ssnepenthe/wp-hashids
 * Description: <a href="http://hashids.org/php/">Hashids</a> implementation for WordPress.
 * Version: 0.1.2
 * Author: Ryan McLaughlin
 * Author URI: https://github.com/ssnepenthe
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Plugin instance getter.
 *
 * @param  null|string $id ID of container entry.
 *
 * @return mixed
 */
function _wph_instance( $id = null ) {
	static $instance = null;

	if ( null === $instance ) {
        $instance = new Plugin();
	}

	if ( null !== $id ) {
        if ( ! $instance->getContainer() instanceof ContainerInterface ) {
            throw new RuntimeException( '@todo' );
        }

        return $instance->getContainer()->get( $id );
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

	// Daedalus requires 7.4+.
	$checker->php_at_least( '7.4' );

	// Uses register_setting() with args array.
	$checker->wp_at_least( '4.7' );

	// Hashids lib must be loaded.
	$checker->class_exists( 'Hashids\\Hashids' );

	// Plates lib must be loaded.
	$checker->class_exists( 'League\\Plates\\Engine' );

	// Daedalus-pimple lib must be loaded which also requires Pimple.
	$checker->class_exists( 'Daedalus\\Pimple\\PimpleProvider' );
	$checker->class_exists( 'Pimple\\Container' );

	// Hashids lib requires one of bcmath or gmp.
	$checker->add_check( function() {
		return function_exists( 'bcadd' ) || function_exists( 'gmp_add' );
	}, 'One of the BCMath or GMP extensions is required' );

	if ( ! $checker->requirements_met() ) {
		return $checker->deactivate_and_notify();
	}

	_wph_instance()->run();

	$initialized = true;
}

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
    require_once __DIR__ . '/vendor/autoload.php';
}

_wph_init();
