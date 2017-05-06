<?php
/**
 * Rewrite_Manager class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

/**
 * Defines the rewrite manager class.
 */
class Rewrite_Manager {
	/**
	 * Options manager instance.
	 *
	 * @var Options_Manager
	 */
	protected $manager;

	/**
	 * Class constructor.
	 *
	 * @param Options_Manager $manager Options manager instance.
	 */
	public function __construct( Options_Manager $manager ) {
		$this->manager = $manager;
	}

	/**
	 * Register the hashids rewrite tag with WordPress.
	 *
	 * @return void
	 */
	public function register_rewrites() {
		add_rewrite_tag(
			$this->manager->rewrite_tag(),
			"([{$this->manager->regex()}]+)"
		);
	}
}
