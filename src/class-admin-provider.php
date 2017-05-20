<?php
/**
 * Admin_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the admin provider class.
 */
class Admin_Provider implements ServiceProviderInterface {
	/**
	 * Provider-specific boot logic.
	 *
	 * @param  Container $container The plugin container instance.
	 *
	 * @return void
	 */
	public function boot( Container $container ) {
		if ( ! is_admin() || $this->all_config_constants_are_defined() ) {
			return;
		}

		add_action(
			'admin_init',
			[ $container->proxy( 'options_page' ), 'register_sections_and_fields' ]
		);
		add_action(
			'admin_menu',
			[ $container->proxy( 'options_page' ), 'register_page' ]
		);
	}

	/**
	 * Provider-specific registration logic.
	 *
	 * @param  Container $container The plugin container instance.
	 *
	 * @return void
	 */
	public function register( Container $container ) {
		$container['options_page'] = function( Container $c ) {
			return new Options_Page( $c['options_manager'], $c['plates'] );
		};
	}

	/**
	 * Check if all three plugin settings have been configured via constants.
	 *
	 * @return boolean
	 */
	protected function all_config_constants_are_defined() {
		return defined( 'WP_HASHIDS_ALPHABET' )
			&& defined( 'WP_HASHIDS_MIN_LENGTH' )
			&& defined( 'WP_HASHIDS_SALT' );
	}
}
