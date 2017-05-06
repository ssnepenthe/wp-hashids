<?php
/**
 * Admin_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use Metis\Container\Container;
use Metis\Container\Abstract_Bootable_Service_Provider;

/**
 * Defines the admin provider class.
 */
class Admin_Provider extends Abstract_Bootable_Service_Provider {
	/**
	 * Provider-specific boot logic.
	 *
	 * @return void
	 */
	public function boot() {
		if ( ! is_admin() || $this->all_config_constants_are_defined() ) {
			return;
		}

		$page = $this->get_container()->make( Options_Page::class );

		add_action( 'admin_init', [ $page, 'register_sections_and_fields' ] );
		add_action( 'admin_menu', [ $page, 'register_page' ] );
	}

	/**
	 * Provider-specific registration logic.
	 *
	 * @return void
	 */
	public function register() {
		$this->get_container()->singleton( // @todo
			Options_Page::class,
			function( Container $container ) {
				return new Options_Page(
					$container->make( Options_Manager::class ),
					$container->make( 'metis.view' )->plugin( plugin_dir_path( __DIR__ ) )
				);
			}
		);
	}

	/**
	 * Check if all three plugin settings have been configured via constants.
	 *
	 * @return boolean
	 */
	protected function all_config_constants_are_defined() : bool {
		return defined( 'WP_HASHIDS_ALPHABET' )
			&& defined( 'WP_HASHIDS_MIN_LENGTH' )
			&& defined( 'WP_HASHIDS_SALT' );
	}
}
