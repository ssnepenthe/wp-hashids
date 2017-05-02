<?php
/**
 * Plugin_Provider class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use WP;
use WP_Post;
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
		$this->boot_links();
		$this->boot_options();
		$this->boot_request();
		$this->boot_rewrite();
	}

	/**
	 * Hook the option manager ->save() method to the shutdown action.
	 *
	 * @return void
	 */
	protected function boot_options() {
		// Shutdown is late so debug bar will be unable to inspect insert query.
		add_action( 'shutdown', function() {
			$options = $this->get_container()->make( 'wph.options.manager' );

			if ( $options->is_dirty() ) {
				$options->save();
			}
		} );
	}

	/**
	 * Map hashids to post ids at the parse_request hook.
	 *
	 * @return void
	 */
	protected function boot_request() {
		add_action( 'parse_request', function( WP $wp ) {
			if ( ! isset( $wp->query_vars['hashid'] ) ) {
				return;
			}

			$decoded = $this->get_container()->make( 'wph.hashids' )
				->decode( $wp->query_vars['hashid'] );
			$id = reset( $decoded );

			// If hashid is invalid, $decoded will be empty which makes $id false.
			if ( $id ) {
				// @todo If permalink structure includes more rewrite tags than just
				// %hashid%, WP very likely already has everything it needs... We
				// should be doing some more robust checks in here.
				$post = get_post( $id );
				$pto = get_post_type_object( $post->post_type );

				// @todo Verify with all built-in post types.
				if ( ! $pto->_builtin ) {
					$wp->set_query_var( $pto->query_var, $post->post_name );
					$wp->set_query_var( 'post_type', $pto->name );
					$wp->set_query_var( 'name', $post->post_name );
				} else {
					$wp->set_query_var( 'p', $id );
				}
			} else {
				// Hashid is invalid - We likely captured a request for a page with a
				// single word slug (i.e. matches [a-zA-Z0-9]+)...
				// @todo Better way to handle?
				$wp->set_query_var( 'pagename', $wp->query_vars['hashid'] );
				unset( $wp->query_vars['hashid'] );
			}

			// @todo Should we unset hashid qv for all requests?
		} );
	}

	/**
	 * Register the hashids rewrite tag on the init hook.
	 *
	 * @return void
	 */
	protected function boot_rewrite() {
		add_action( 'init', function() {
			$options = $this->get_container()->make( 'wph.options.manager' );
			$regex = '';

			// @todo Account for case where all alphabet settings are false.
			// @todo Account for case where only numerals is true (strlen($alphabet) < 16).
			if ( $options->get( 'lowercase' ) ) {
				$regex .= 'a-z';
			}

			if ( $options->get( 'uppercase' ) ) {
				$regex .= 'A-Z';
			}

			if ( $options->get( 'numerals' ) ) {
				$regex .= '0-9';
			}

			$regex = "([{$regex}]+)";

			add_rewrite_tag( $options->get( 'rewrite_tag' ), $regex );
		} );
	}

	/**
	 * Insert post specific hashids in permalink functions.
	 *
	 * @return void
	 */
	protected function boot_links() {
		foreach ( [ 'pre_post_link', 'post_type_link' ] as $filter ) {
			add_filter( $filter, function( string $link, WP_Post $post ) : string {
				return str_replace(
					$this->get_container()->make( 'wph.options.manager' )
						->get( 'rewrite_tag' ),
					$this->get_container()->make( 'wph.hashids' )
						->encode( $post->ID ),
					$link
				);
			}, 10, 2 );
		}
	}

	/**
	 * Provider specific registration logic.
	 *
	 * @return void
	 */
	public function register() {
		$this->get_container()->singleton(
			'wph.options.store',
			Options_Store::class
		);

		$this->get_container()->singleton(
			'wph.options.manager',
			function( Container $container ) {
				$manager = new Array_Option_Manager(
					'wph_options',
					[
						'lowercase' => true,
						'min_length' => 6,
						'numerals' => true,
						// @todo Should this be in here? Basically allows overrides.
						'rewrite_tag' => '%hashid%',
						'salt' => function() use ( $container ) {
							return $container->make( 'wph.salt_generator' )
								->generate();
						},
						'uppercase' => true,
					],
					[
						'lowercase' => 'bool',
						'min_length' => 'int',
						'numerals' => 'bool',
						'rewrite_tag' => 'string',
						'salt' => 'string',
						'uppercase' => 'bool',
					],
					$container->make( 'wph.options.store' )
				);
				// @todo Is it a good idea to automatically hit DB like this?
				$manager->init();

				return $manager;
			}
		);

		$this->get_container()->singleton(
			'wph.salt_generator',
			Salt_Generator::class
		);
	}
}
