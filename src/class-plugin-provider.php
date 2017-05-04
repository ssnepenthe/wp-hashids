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
		add_action( 'init', [ $this, 'add_rewrite_tag' ] );
		add_action( 'parse_request', [ $this, 'parse_request' ] );

		add_filter( 'pre_post_link', [ $this, 'post_link' ], 10, 2 );
		add_filter( 'post_type_link', [ $this, 'post_link' ], 10, 2 );
	}

	/**
	 * Map hashids to post ids at the parse_request hook.
	 *
	 * @return void
	 */
	public function parse_request( WP $wp ) {
		if ( ! isset( $wp->query_vars['hashid'] ) ) {
			return;
		}

		$decoded = $this->get_container()->make( HashidsInterface::class )
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
	}

	/**
	 * Register the hashids rewrite tag on the init hook.
	 *
	 * @return void
	 */
	public function add_rewrite_tag() {
		$options = $this->get_container()->make( Options_Manager_Interface::class );

		add_rewrite_tag( $options->rewrite_tag(), "([{$options->regex()}]+)" );
	}

	/**
	 * Insert post specific hashids in permalink functions.
	 *
	 * @return void
	 */
	public function post_link( string $link, WP_Post $post ) : string {
		$options = $this->get_container()->make( Options_Manager_Interface::class );
		$hashids = $this->get_container()->make( HashidsInterface::class );

		return str_replace(
			$options->rewrite_tag(),
			$hashids->encode( $post->ID ),
			$link
		);
	}

	/**
	 * Provider specific registration logic.
	 *
	 * @return void
	 */
	public function register() {
		$this->get_container()->singleton(
			Key_Value_Store_Interface::class,
			Options_Store::class
		);

		$this->get_container()->singleton(
			Options_Manager_Interface::class,
			Options_Manager::class
		);

		$this->get_container()->when( Options_Store::class )
			->needs( '$prefix' )
			->give( 'wp_hashids' );
	}
}
