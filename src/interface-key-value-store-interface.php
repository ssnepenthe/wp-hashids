<?php
/**
 * Key_Value_Store_Interface.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

/**
 * Defines the key value store interface.
 */
interface Key_Value_Store_Interface {
	/**
	 * Add data to the store if it does not already exist.
	 *
	 * @param string $key   Data key.
	 * @param mixed  $value Data value.
	 *
	 * @return boolean
	 */
	public function add( string $key, $value ) : bool;

	/**
	 * Delete data from the store.
	 *
	 * @param  string $key Data key.
	 *
	 * @return boolean
	 */
	public function delete( string $key ) : bool;

	/**
	 * Get data from the store.
	 *
	 * @param  string $key Data key.
	 *
	 * @return mixed
	 */
	public function get( string $key );

	/**
	 * Set data to the store, overwriting existing data if it exists.
	 *
	 * @param string $key   Data key.
	 * @param mixed  $value Data value.
	 *
	 * @return boolean
	 */
	public function set( string $key, $value ) : bool;
}
