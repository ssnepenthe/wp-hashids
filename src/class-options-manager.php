<?php
/**
 * Options_Manager class.
 *
 * @package wp-hashids
 */

namespace WP_Hashids;

/**
 * Defines the options manager class.
 */
class Options_Manager {
	const UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const LOWER = 'abcdefghijklmnopqrstuvwxyz';
	const NUMBERS = '0123456789';

	const ALPHABET_MAP = [
		'lower' => [
			'alphabet' => self::LOWER,
			'label' => 'Lowercase',
			'regex' => 'a-z',
		],
		'upper' => [
			'alphabet' => self::UPPER,
			'label' => 'Uppercase',
			'regex' => 'A-Z',
		],
		'lowerupper' => [
			'alphabet' => self::LOWER . self::UPPER,
			'label' => 'Lowercase and uppercase',
			'regex' => 'a-zA-Z',
		],
		'lowernumber' => [
			'alphabet' => self::LOWER . self::NUMBERS,
			'label' => 'Lowercase and numbers',
			'regex' => 'a-z0-9',
		],
		'uppernumber' => [
			'alphabet' => self::UPPER . self::NUMBERS,
			'label' => 'Uppercase and numbers',
			'regex' => 'A-Z0-9',
		],
		'all' => [
			'alphabet' => self::LOWER . self::UPPER . self::NUMBERS,
			'label' => 'Lowercase, uppercase and numbers',
			'regex' => 'a-zA-Z0-9',
		],
	];

	protected $store;

	public function __construct( Key_Value_Store_Interface $store ) {
		$this->store = $store;
	}

	public function alphabet() : string {
		$alphabet = defined( 'WP_HASHIDS_ALPHABET' )
			? WP_HASHIDS_ALPHABET
			// If not set, will be "all" as set in ->register_settings().
			: $this->store->get( 'alphabet' );

		$alphabet = $this->sanitize_alphabet( $alphabet );

		return self::ALPHABET_MAP[ $alphabet ]['alphabet'];
	}

	public function min_length() : int {
		$min_length = defined( 'WP_HASHIDS_MIN_LENGTH' )
			? WP_HASHIDS_MIN_LENGTH
			// If not set, will be 6 as set in ->register_settings().
			: $this->store->get( 'min_length' );

		return absint( $min_length );
	}

	public function regex() : string {
		$alphabet = defined( 'WP_HASHIDS_ALPHABET' )
			? WP_HASHIDS_ALPHABET
			// If not set, will be "all" as set in ->register_settings().
			: $this->store->get( 'alphabet' );

		$alphabet = $this->sanitize_alphabet( $alphabet );

		return self::ALPHABET_MAP[ $alphabet ]['regex'];
	}

	public function register_settings() {
		register_setting( 'wp_hashids_group', 'wp_hashids_alphabet', [
			'default' => 'all',
			'sanitize_callback' => [ $this, 'sanitize_alphabet' ],
			'show_in_rest' => true,
		] );

		register_setting( 'wp_hashids_group', 'wp_hashids_min_length', [
			'default' => 6,
			'sanitize_callback' => 'absint',
			'show_in_rest' => true,
			'type' => 'integer',
		] );

		register_setting( 'wp_hashids_group', 'wp_hashids_salt', [
			'sanitize_callback' => [ $this, 'sanitize_salt' ],
			'show_in_rest' => true,
		] );
	}

	public function rewrite_tag() : string {
		return '%hashid%';
	}

	public function salt() : string {
		$needs_save = false;
		$salt = defined( 'WP_HASHIDS_SALT' )
			? WP_HASHIDS_SALT
			// If not set, will be null - there is no default.
			: $this->store->get( 'salt' );

		// First run it will be null (non-existent in DB).
		if ( is_null( $salt ) ) {
			$needs_save = true;
		}

		$salt = $this->sanitize_salt( $salt );

		if ( $needs_save ) {
			$this->store->set( 'salt', $salt );
		}

		return $salt;
	}

	public function sanitize_alphabet( $alphabet ) : string {
		if ( ! $this->is_valid_alphabet( $alphabet ) ) {
			$alphabet = 'all';
		}

		return $alphabet;
	}

	public function sanitize_salt( $salt ) : string {
		if ( is_null( $salt ) ) {
			// @todo
			$salt = ( new Salt_Generator )->generate();
		}

		return (string) $salt;
	}

	protected function is_valid_alphabet( $alphabet ) : bool {
		return in_array( $alphabet, array_keys( self::ALPHABET_MAP ), true );
	}
}
