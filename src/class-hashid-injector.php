<?php
/**
 * Hashid_Injector class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

use WP_Post;
use Hashids\HashidsInterface;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the hashid inject class.
 */
class Hashid_Injector {
	/**
	 * Hashids instance.
	 *
	 * @var HashidsInterface
	 */
	protected $hashids;

	/**
	 * Options manager instance.
	 *
	 * @var Options_Manager
	 */
	protected $options;

	/**
	 * Class constructor.
	 *
	 * @param Options_Manager  $options Options manager instance.
	 * @param HashidsInterface $hashids Hashids instance.
	 */
	public function __construct(
		Options_Manager $options,
		HashidsInterface $hashids
	) {
		$this->options = $options;
		$this->hashids = $hashids;
	}

	/**
	 * Replace the hashids rewrite tag with actual post hashid.
	 *
	 * @param  string  $link Post link.
	 * @param  WP_Post $post Current WP post instance.
	 *
	 * @return string
	 */
	public function inject( $link, $post ) {
		if ( ! (
			is_string( $link )
			&& is_object( $post )
			&& property_exists( $post, 'ID' )
			&& ( is_int( $post->ID ) || is_string( $post->ID ) )
		) ) {
			return $link;
		}

		return str_replace(
			$this->options->rewrite_tag(),
			$this->hashids->encode( $post->ID ),
			$link
		);
	}
}
