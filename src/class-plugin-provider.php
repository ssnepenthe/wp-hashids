<?php
/**
 * Plugin_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use WP;
use WP_Post;
use Hashids\HashidsInterface;
use Metis\Container\Container;
use Metis\Container\Abstract_Bootable_Service_Provider;

/**
 * Defines the plugin provider class.
 */
class Plugin_Provider extends Abstract_Bootable_Service_Provider {
	/**
	 * Provider specific boot logic.
	 *
	 * @return void
	 */
	public function boot() {
		add_action( 'init', [
			$this->get_container()->make( Rewrite_Manager::class ),
			'register_rewrites',
		] );
		// Docs still recommend using the admin_init hook but then the options will
		// not be available from the REST API...
		add_action( 'init', [
			$this->get_container()->make( Options_Manager::class ),
			'register_settings',
		] );
		add_action( 'parse_request', [
			$this->get_container()->make( Request_Parser::class ),
			'parse',
		] );

		$injector = $this->get_container()->make( Hashid_Injector::class );

		add_filter( 'pre_post_link', [ $injector, 'inject' ], 10, 2 );
		add_filter( 'post_type_link', [ $injector, 'inject' ], 10, 2 );
	}

	/**
	 * Provider specific registration logic.
	 *
	 * @return void
	 */
	public function register() {
		$this->get_container()->singleton( Hashid_Injector::class );

		$this->get_container()->singleton(
			Key_Value_Store_Interface::class,
			Options_Store::class
		);

		$this->get_container()->singleton( Options_Manager::class );

		$this->get_container()->when( Options_Store::class )
			->needs( '$prefix' )
			->give( 'wp_hashids' );

		$this->get_container()->singleton( Request_Parser::class );

		$this->get_container()->singleton( Rewrite_Manager::class );
	}
}
