<?php
/**
 * Hashids_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Hashids\Hashids;
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
		$this->get_container()->singleton(
			'wph.hashids',
			function( Container $container ) {
				$options = $container->make( 'wph.options.manager' );

				return new Hashids(
					$options->get( 'salt' ),
					$options->get( 'min_length' ),
					$container->make( 'wph.hashids.alphabet' )
				);
			}
		);

		$this->get_container()->singleton(
			'wph.hashids.alphabet',
			function( Container $container ) {
				$alphabet = '';
				$options = $container->make( 'wph.options.manager' );

				// @todo Account for case where all alphabet settings are false.
				// @todo Account for case where only numerals is true (strlen(alphabet) < 16).
				if ( $options->get( 'lowercase' ) ) {
					$alphabet .= 'abcdefghijklmnopqrstuvwxyz';
				}

				if ( $options->get( 'uppercase' ) ) {
					$alphabet .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				}

				if ( $options->get( 'numerals' ) ) {
					$alphabet .= '1234567890';
				}

				return $alphabet;
			}
		);
	}
}
