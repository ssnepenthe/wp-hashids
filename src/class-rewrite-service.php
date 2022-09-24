<?php

namespace WP_Hashids;

use Hashids\HashidsInterface;
use WP;
use WP_Post;
use WP_Rewrite;

class Rewrite_Service {
	private $options;
	private $hashids;

	public function __construct(Options_Manager $options, HashidsInterface $hashids) {
		$this->options = $options;
		$this->hashids = $hashids;
	}

	public function replace_hashid_rewrite_tag( string $link, WP_Post $post ) {
		return str_replace(
			$this->options->rewrite_tag(),
			$this->hashids->encode( $post->ID ),
			$link
		);
	}

	public function register_rewrite_tag() {
		add_rewrite_tag( $this->options->rewrite_tag(), "([{$this->options->regex()}]+)" );
	}

	public function remove_hashid_tag_from_permalink_structure( WP_Rewrite $wp_rewrite ) {
		$wp_rewrite->set_permalink_structure( str_replace(
			$this->options->rewrite_tag(),
			'%post_id%',
			$wp_rewrite->permalink_structure
		) );

		$wp_rewrite->flush_rules();
	}

	/**
	 * Determine and set the appropriate query vars based on the provided hashid.
	 *
	 * @param  WP $wp WP instance.
	 *
	 * @return void
	 */
	public function parse_request( WP $wp ) {
		if ( ! isset( $wp->query_vars['hashid'] ) ) {
			return;
		}

		$decoded = $this->hashids->decode( $wp->query_vars['hashid'] );
		$id = reset( $decoded );

		if ( ! $id ) {
			// Hashid is invalid - We likely captured a request for a page with a
			// single word slug (i.e. matches [a-zA-Z0-9]+)...
			// @todo Better way to handle?
			$wp->set_query_var( 'pagename', $wp->query_vars['hashid'] );

			unset( $wp->query_vars['hashid'] );

			return;
		}

		// @todo If permalink structure includes more rewrite tags than just
		// %hashid%, WP very likely already has everything it needs... We
		// should be doing some more robust checks in here.
		$post = get_post( $id );
		$pto = get_post_type_object( $post->post_type );

		if ( ! $pto->_builtin ) {
			// Custom post type.
			$wp->set_query_var( $pto->query_var, $post->post_name );
			$wp->set_query_var( 'post_type', $pto->name );
			$wp->set_query_var( 'name', $post->post_name );
		} else {
			// Standard posts.
			$wp->set_query_var( 'p', $post->ID );
		}
	}
}
