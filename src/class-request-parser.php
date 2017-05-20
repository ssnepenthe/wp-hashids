<?php
/**
 * Request_Parser class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use WP;
use WP_Post;
use WP_Post_Type;
use Hashids\HashidsInterface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the request parser class.
 */
class Request_Parser {
	/**
	 * Hashids instance.
	 *
	 * @var HashidsInterface
	 */
	protected $hashids;

	/**
	 * Class constructor.
	 *
	 * @param HashidsInterface $hashids Hashids instance.
	 */
	public function __construct( HashidsInterface $hashids ) {
		$this->hashids = $hashids;
	}

	/**
	 * Determine and set the appropriate query vars based on the provided hashid.
	 *
	 * @param  WP $wp WP instance.
	 *
	 * @return void
	 */
	public function parse( WP $wp ) {
		if ( ! isset( $wp->query_vars['hashid'] ) ) {
			return;
		}

		$decoded = $this->hashids->decode( $wp->query_vars['hashid'] );
		$id = reset( $decoded );

		if ( ! $id ) {
			return $this->change_to_page_vars( $wp );
		}

		// @todo If permalink structure includes more rewrite tags than just
		// %hashid%, WP very likely already has everything it needs... We
		// should be doing some more robust checks in here.
		$post = get_post( $id );
		$pto = get_post_type_object( $post->post_type );

		if ( $this->is_custom_post_type( $pto ) ) {
			return $this->set_custom_post_type_vars( $wp, $pto, $post );
		}

		return $this->set_built_in_post_vars( $wp, $post );
	}

	/**
	 * Set the appropriate query var in the instance that a request for a page was
	 * accidentally caught against one of the WP Hashids rewrite rules.
	 *
	 * @param  WP $wp WP instance.
	 *
	 * @return void
	 */
	protected function change_to_page_vars( WP $wp ) {
		// Hashid is invalid - We likely captured a request for a page with a
		// single word slug (i.e. matches [a-zA-Z0-9]+)...
		// @todo Better way to handle?
		$wp->set_query_var( 'pagename', $wp->query_vars['hashid'] );

		unset( $wp->query_vars['hashid'] );
	}

	/**
	 * Check if a post type object represents a custom post type.
	 *
	 * @param  WP_Post_Type $pto WP post type instance.
	 *
	 * @return boolean
	 */
	protected function is_custom_post_type( WP_Post_Type $pto ) {
		return ! $pto->_builtin;
	}

	/**
	 * Set the appropriate query var for built in posts.
	 *
	 * @param WP      $wp   WP instance.
	 * @param WP_Post $post WP post instance.
	 *
	 * @return void
	 */
	protected function set_built_in_post_vars( WP $wp, WP_Post $post ) {
		// @todo Verify with all built-in post types.
		$wp->set_query_var( 'p', $post->ID );
	}

	/**
	 * Set the appropriate query vars for custom post types.
	 *
	 * @param WP           $wp   WP instance.
	 * @param WP_Post_Type $pto  WP post type instance.
	 * @param WP_Post      $post WP post instance.
	 *
	 * @return void
	 */
	protected function set_custom_post_type_vars(
		WP $wp,
		WP_Post_Type $pto,
		WP_Post $post
	) {
		$wp->set_query_var( $pto->query_var, $post->post_name );
		$wp->set_query_var( 'post_type', $pto->name );
		$wp->set_query_var( 'name', $post->post_name );
	}
}
