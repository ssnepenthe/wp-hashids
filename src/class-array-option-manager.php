<?php
/**
 * Array_Option_Manager class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

/**
 * Defines the array option manager class.
 */
class Array_Option_Manager {
	/**
	 * Option data.
	 *
	 * @var array
	 */
	protected $data = [];

	/**
	 * Option defaults.
	 *
	 * @var array
	 */
	protected $defaults = [];

	/**
	 * Whether the data values have changed over the course of this request.
	 *
	 * @var boolean
	 */
	protected $dirty = false;

	/**
	 * Whether option data has been loaded from the database.
	 *
	 * @var boolean
	 */
	protected $initialized = false;

	/**
	 * Option key.
	 *
	 * @var string
	 */
	protected $key;

	/**
	 * Map of allowed data keys to their types.
	 *
	 * @var array
	 */
	protected $schema = [];

	/**
	 * Options store instance.
	 *
	 * @var Options_Store
	 */
	protected $store;

	/**
	 * Class constructor.
	 *
	 * @param string        $key      Option key.
	 * @param array         $defaults Option defaults.
	 * @param array         $schema   Map of allowed data keys to their types.
	 * @param Options_Store $store    Options store instance.
	 */
	public function __construct(
		string $key,
		array $defaults,
		array $schema,
		Options_Store $store
	) {
		$this->key = $key;
		$this->schema = $schema;
		$this->store = $store;

		$this->set_defaults( $defaults );
	}

	/**
	 * Get an option value, fall back to the default if it exists.
	 *
	 * @param  string $key Option key.
	 *
	 * @return mixed
	 */
	public function get( string $key ) {
		if ( array_key_exists( $key, $this->data ) ) {
			return $this->data[ $key ];
		}

		if ( array_key_exists( $key, $this->defaults ) ) {
			if ( is_callable( $this->defaults[ $key ] ) ) {
				$value = call_user_func( $this->defaults[ $key ] );
			} else {
				$value = $this->defaults[ $key ];
			}

			$this->set( $key, $value );

			// Call ->get() to ensure we get sanitized value.
			return $this->get( $key );
		}

		return null;
	}

	/**
	 * Determine whether a given key is set in the option data or defaults.
	 *
	 * @param  string $key Option key.
	 *
	 * @return boolean
	 */
	public function has( string $key ) {
		return array_key_exists( $key, $this->data )
			|| array_key_exists( $key, $this->defaults );
	}

	/**
	 * Fetch the option data from the database.
	 *
	 * @return void
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}

		$data = $this->store->get( $this->key );

		if ( ! is_array( $data ) ) {
			$data = [];
		}

		foreach ( $data as $key => $value ) {
			$this->set( $key, $value, false );
		}

		$this->initialized = true;
	}

	/**
	 * Determine whether the option data has changed over the course of this request.
	 *
	 * @return boolean
	 */
	public function is_dirty() {
		return $this->dirty;
	}

	/**
	 * Set an option value.
	 *
	 * @param string  $key           Option key.
	 * @param mixed   $value         Option value.
	 * @param boolean $track_changes Whether or not to mark option data as dirty.
	 *
	 * @return void
	 */
	public function set( string $key, $value, bool $track_changes = true ) {
		if ( array_key_exists( $key, $this->schema ) ) {
			$original = $this->data[ $key ] ?? null;
			$this->data[ $key ] = $this->sanitize( $this->schema[ $key ], $value );

			if ( $track_changes && $original !== $this->data[ $key ] ) {
				$this->dirty = true;
			}
		}
	}

	/**
	 * Set a default option value.
	 *
	 * @param string $key   Option key.
	 * @param mixed  $value Option default value.
	 *
	 * @return void
	 */
	public function set_default( string $key, $value ) {
		if ( array_key_exists( $key, $this->schema ) ) {
			$this->defaults[ $key ] = $value;
		}
	}

	/**
	 * Set multiple option defaults.
	 *
	 * @param array $defaults Map of option keys to values.
	 *
	 * @return void
	 */
	public function set_defaults( array $defaults ) {
		foreach ( $defaults as $key => $value ) {
			$this->set_default( $key, $value );
		}
	}

	/**
	 * Perform sanitization based on the type set in $this->schema.
	 *
	 * @param  string $type  Data type.
	 * @param  mixed  $value Option value.
	 *
	 * @return boolean|integer|string
	 */
	protected function sanitize( string $type, $value ) {
		switch ( $type ) {
			case 'string':
				return (string) $value;
			case 'int':
				return absint( $value );
			case 'bool':
			default:
				return (bool) $value;
		}
	}

	/**
	 * Save the option array to the database.
	 *
	 * @return boolean
	 */
	public function save() {
		$success = $this->store->set( $this->key, $this->data );

		if ( $success ) {
			$this->dirty = false;
		}

		return $success;
	}

	/**
	 * Unset a value from the data array and optionally from the defaults array.
	 *
	 * @param  string  $key     Option key.
	 * @param  boolean $default Whether the default should also be unset.
	 *
	 * @return void
	 */
	public function unset( string $key, bool $default = false ) {
		unset( $this->data[ $key ] );

		if ( $default ) {
			unset( $this->defaults[ $key ] );
		}
	}
}
