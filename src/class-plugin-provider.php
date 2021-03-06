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

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

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
		$options_manager = $container['options_manager'];

		// Docs still recommend using the admin_init hook but then the options will
		// not be available from the REST API...
		add_action( 'init', [ $options_manager, 'register_settings' ] );
		add_action( 'init', [ $container['rewrite_manager'], 'register_rewrites' ] );
		add_action(
			'parse_request',
			[ $container->proxy( 'request_parser' ), 'parse' ]
		);
		add_action(
			'update_option_wp_hashids_alphabet',
			[ $options_manager, 'flush_rewrites_on_save' ],
			10,
			2
		);

		add_filter(
			'post_type_link',
			[ $container->proxy( 'hashid_injector' ), 'inject' ],
			10,
			2
		);

		foreach ( [ 'alphabet', 'min_length', 'salt' ] as $option ) {
			add_filter(
				"pre_option_wp_hashids_{$option}",
				[ $options_manager, 'use_constants_when_defined' ],
				10,
				2
			);
		}

		add_filter(
			'pre_post_link',
			[ $container->proxy( 'hashid_injector' ), 'inject' ],
			10,
			2
		);
	}

	/**
	 * Remove the rewrite tag from site permalink structure on deactivation.
	 *
	 * @param  Container $container The plugin container instance.
	 *
	 * @return void
	 */
	public function deactivate( Container $container ) {
		$container['wp_rewrite']->set_permalink_structure( str_replace(
			$container['options_manager']->rewrite_tag(),
			'%post_id%',
			$container['wp_rewrite']->permalink_structure
		) );

		$container['wp_rewrite']->flush_rules();
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

		$container['options_manager'] = function( Container $c ) {
			return new Options_Manager( $c['options_store'] );
		};

		$container['options_prefix'] = 'wp_hashids';

		$container['options_store'] = function( Container $c ) {
			return new Options_Store( $c['options_prefix'] );
		};

		$container['request_parser'] = function( Container $c ) {
			return new Request_Parser( $c['hashids'] );
		};

		$container['rewrite_manager'] = function( Container $c ) {
			return new Rewrite_Manager( $c['options_manager'] );
		};
	}
}
