<?php
/**
 * Plugin_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use WP;
use WP_Post;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Defines the plugin provider class.
 */
class Plugin_Provider implements ServiceProviderInterface {
	/**
	 * Provider specific boot logic.
	 *
	 * @param  Container $container The plugin container instance.
	 *
	 * @return void
	 */
	public function boot( Container $container ) {
		// Docs still recommend using the admin_init hook but then the options will
		// not be available from the REST API...
		add_action( 'init', [ $container['options_manager'], 'register_settings' ] );
		add_action( 'init', [ $container['rewrite_manager'], 'register_rewrites' ] );

		add_action( 'parse_request', [ $container['request_parser'], 'parse' ] );
	}

	/**
	 * Provider-specific, deferred boot logic. The request parser and hashid injector
	 * need to be instantiated after the options manager "register_settings" method
	 * has been called during the init hook in order for defaults to be applied.
	 *
	 * @param  Container $container The plugin container instance.
	 *
	 * @return void
	 */
	public function deferred_boot( Container $container ) {
		add_action( 'parse_request', [ $container['request_parser'], 'parse' ] );

		$injector = $container['hashid_injector'];

		add_filter( 'pre_post_link', [ $injector, 'inject' ], 10, 2 );
		add_filter( 'post_type_link', [ $injector, 'inject' ], 10, 2 );
	}

	/**
	 * Provider specific registration logic.
	 *
	 * @param  Container $container The plugin container instance.
	 *
	 * @return void
	 */
	public function register( Container $container ) {
		$container['hashid_injector'] = function( Container $c ) {
			return new Hashid_Injector( $c['options_manager'], $c['hashids'] );
		};

		$container['options_prefix'] = 'wp_hashids';

		$container['options_store'] = function( Container $c ) {
			return new Options_Store( $c['options_prefix'] );
		};

		$container['options_manager'] = function( Container $c ) {
			return new Options_Manager( $c['options_store'] );
		};

		$container['request_parser'] = function( Container $c ) {
			return new Request_Parser( $c['hashids'] );
		};

		$container['rewrite_manager'] = function( Container $c ) {
			return new Rewrite_Manager( $c['options_manager'] );
		};
	}
}
