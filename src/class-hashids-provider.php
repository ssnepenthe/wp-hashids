<?php
/**
 * Hashids_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Hashids\Hashids;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the hashids provider class.
 */
class Hashids_Provider implements ServiceProviderInterface {
	/**
	 * Provider specific registration logic.
	 *
	 * @param  Container $container The plugin container instance.
	 *
	 * @return void
	 */
	public function register( Container $container ) {
		$container['hashids'] = function( Container $c ) {
			$options = $c['options_manager'];

			return new Hashids(
				$options->salt(),
				$options->min_length(),
				$options->alphabet()
			);
		};
	}
}
