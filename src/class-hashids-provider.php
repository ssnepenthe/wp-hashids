<?php
/**
 * Hashids_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Hashids\Hashids;
use Hashids\HashidsInterface;
use Metis\Container\Container;
use Metis\Container\Abstract_Service_Provider;

/**
 * Defines the hashids provider class.
 */
class Hashids_Provider extends Abstract_Service_Provider {
	/**
	 * Provider specific registration logic.
	 *
	 * @return void
	 */
	public function register() {
		$this->get_container()->singleton( HashidsInterface::class, Hashids::class );

		$this->get_container()->when( Hashids::class )
			->needs( '$alphabet' )
			->give( function( Container $container ) {
				return $container->make( Options_Manager::class )
					->alphabet();
			} );
		$this->get_container()->when( Hashids::class )
			->needs( '$minHashLength' )
			->give( function( Container $container ) {
				return $container->make( Options_Manager::class )
					->min_length();
			} );
		$this->get_container()->when( Hashids::class )
			->needs( '$salt' )
			->give( function( Container $container ) {
				return $container->make( Options_Manager::class )->salt();
			} );
	}
}
