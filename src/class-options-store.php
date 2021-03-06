<?php
/**
 * Options_Store class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defines the options store class.
 */
class Options_Store {
	/**
	 * Prefix to prepend to option keys.
	 *
	 * @var string
	 */
	protected $prefix;

	/**
	 * Class constructor.
	 *
	 * @param string $prefix Prefix to prepend to option keys.
	 */
	public function __construct( $prefix = '' ) {
		$this->prefix = (string) $prefix;
	}

	/**
	 * Add an option entry if it does not already exist.
	 *
	 * @param string $key   Option key.
	 * @param mixed  $value Option value.
	 *
	 * @return boolean
	 */
	public function add( $key, $value ) {
		return add_option( $this->option_key( $key ), $value );
	}

	/**
	 * Delete an option entry.
	 *
	 * @param  string $key Option key.
	 *
	 * @return boolean
	 */
	public function delete( $key ) {
		return delete_option( $this->option_key( $key ) );
	}

	/**
	 * Get an option entry.
	 *
	 * @param  string $key Option key.
	 *
	 * @return mixed       Option value or null if not set.
	 */
	public function get( $key ) {
		$value = get_option( $this->option_key( $key ) );

		if ( false === $value ) {
			return null;
		}

		return $value;
	}

	/**
	 * Set an option value.
	 *
	 * @param string $key   Option key.
	 * @param mixed  $value Option value.
	 *
	 * @return boolean
	 */
	public function set( $key, $value ) {
		return update_option( $this->option_key( $key ), $value );
	}

	/**
	 * Generate the option key if a prefix has been set.
	 *
	 * @param  string $key Option key.
	 *
	 * @return string
	 */
	protected function option_key( $key ) {
		$key = (string) $key;

		if ( $this->prefix ) {
			$key = "{$this->prefix}_{$key}";
		}

		return $key;
	}
}
