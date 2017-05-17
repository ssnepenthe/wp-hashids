<?php
/**
 * Plates_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Pimple\Container;
use League\Plates\Engine;
use Pimple\ServiceProviderInterface;

/**
 * Defines the plates provider class.
 */
class Plates_Provider implements ServiceProviderInterface {
	/**
	 * Provider specific registration logic.
	 *
	 * @param  Container $container The plugin container instance.
	 *
	 * @return void
	 */
	public function register( Container $container ) {
		$container['plates'] = function( Container $c ) {
			return new Engine( $c['dir'] . '/templates' );
		};
	}
}
