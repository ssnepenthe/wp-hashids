<?php
/**
 * Options_Store class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

/**
 * Defines the options store class.
 */
class Options_Store implements Key_Value_Store_Interface {
	protected $prefix;

	public function __construct( string $prefix = '' ) {
		$this->prefix = $prefix;
	}

	/**
	 * Add an option entry if it does not already exist.
	 *
	 * @param string $key   Option key.
	 * @param mixed  $value Option value.
	 *
	 * @return boolean
	 */
	public function add( string $key, $value ) : bool {
		return add_option( $this->option_key( $key ), $value );
	}

	/**
	 * Delete an option entry.
	 *
	 * @param  string $key Option key.
	 *
	 * @return boolean
	 */
	public function delete( string $key ) : bool {
		return delete_option( $this->option_key( $key ) );
	}

	/**
	 * Get an option entry.
	 *
	 * @param  string $key Option key.
	 *
	 * @return mixed       Option value or null if not set.
	 */
	public function get( string $key ) {
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
	public function set( string $key, $value ) : bool {
		return update_option( $this->option_key( $key ), $value );
	}

	protected function option_key( string $key ) {
		if ( $this->prefix ) {
			$key = "{$this->prefix}_{$key}";
		}

		return $key;
	}
}
