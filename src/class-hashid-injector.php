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
	protected $manager;

	/**
	 * Class constructor.
	 *
	 * @param Options_Manager  $manager Options manager instance.
	 * @param HashidsInterface $hashids Hashids instance.
	 */
	public function __construct(
		Options_Manager $manager,
		HashidsInterface $hashids
	) {
		$this->manager = $manager;
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
	public function inject( $link, WP_Post $post ) {
		return str_replace(
			$this->manager->rewrite_tag(),
			$this->hashids->encode( $post->ID ),
			$link
		);
	}
}
