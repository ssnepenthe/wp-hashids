<?php
/**
 * Rewrite_Manager class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the rewrite manager class.
 */
class Rewrite_Manager {
	/**
	 * Options manager instance.
	 *
	 * @var Options_Manager
	 */
	protected $options;

	/**
	 * Class constructor.
	 *
	 * @param Options_Manager $options Options manager instance.
	 */
	public function __construct( Options_Manager $options ) {
		$this->options = $options;
	}

	/**
	 * Register the hashids rewrite tag with WordPress.
	 *
	 * @return void
	 */
	public function register_rewrites() {
		add_rewrite_tag(
			$this->options->rewrite_tag(),
			"([{$this->options->regex()}]+)"
		);
	}
}
