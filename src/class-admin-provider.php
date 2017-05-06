<?php

namespace WP_Hashids;

use Metis\Container\Container;
use Metis\Container\Abstract_Bootable_Service_Provider;

class Admin_Provider extends Abstract_Bootable_Service_Provider {
	public function boot() {
		if ( ! is_admin() || $this->all_config_constants_are_defined() ) {
			return;
		}

		$page = $this->get_container()->make( Options_Page::class );

		add_action( 'admin_init', [ $page, 'register_sections_and_fields' ] );
		add_action( 'admin_menu', [ $page, 'register_page' ] );
	}

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

	protected function all_config_constants_are_defined() {
		return defined( 'WP_HASHIDS_ALPHABET' )
			&& defined( 'WP_HASHIDS_MIN_LENGTH' )
			&& defined( 'WP_HASHIDS_SALT' );
	}
}
